<?php namespace App\Games;

use App\Currency\Native\Bitcoin;
use App\Currency\Native\Ethereum;
use App\Events\MultiplayerTimerStart;
use App\Game;
use App\Games\Kernel\GameCategory;
use App\Games\Kernel\Metadata;
use App\Games\Kernel\Module\General\HouseEdgeModule;
use App\Games\Kernel\Multiplayer\MultiplayerGame;
use App\Games\Kernel\ProvablyFair;
use App\Games\Kernel\ProvablyFairResult;
use App\Jobs\MultiplayerDisableBetAccepting;
use App\Jobs\MultiplayerFinishAndSetupNextGame;
use App\Jobs\MultiplayerUpdateTimestamp;
use App\Utils\Exception\UnsupportedOperationException;

class BullBear extends MultiplayerGame {

    private array $tickers;

    public function __construct() {
        $this->tickers = [
            new Bitcoin(),
            new Ethereum()
        ];
    }

    function metadata(): Metadata {
        return new class extends Metadata {
            function id(): string {
                return "bullvsbear";
            }

            function name(): string {
                return "Bull vs. Bear";
            }

            function icon(): string {
                return "bullvsbear";
            }

            public function isPlaceholder(): bool {
                return false;
            }

            public function category(): array {
                return [GameCategory::$originals];
            }
        };
    }

    public function nextGame() {
        foreach($this->tickers as $ticker) {
            $this->state()->pushData([
                $ticker->name() . '-USD-STRIKE' => $this->state()->data()[$ticker->name() . '-USD']['price']
            ]);
        }

        $this->state()->betting(true);
        event(new MultiplayerTimerStart($this));

        dispatch((new MultiplayerDisableBetAccepting($this))->delay(now()->addMinute()));

        dispatch((new MultiplayerUpdateTimestamp($this, now()->addSeconds(90 * 2)->timestamp))->delay(now()->addSeconds(90)));
        dispatch((new MultiplayerFinishAndSetupNextGame($this, [], now()->addMinute()))->delay(now()->addSeconds(90)));
    }

    protected function getPlayerData(Game $game): array {
        $decay = (($this->state()->timestamp() - now()->timestamp - 30 + 15) / 60) * 100;
        $decay = $decay < 1 ? 1 : ($decay > 100 ? 100 : $decay);

        $this->pushData($game, ['decay' => $decay]);

        return [
            'coin' => $this->userData($game)['data']['coin'],
            'target' => $this->userData($game)['data']['target'],
            'decay' => $decay
        ];
    }

    public function onDispatchedFinish() {
        foreach($this->getActiveGames() as $game) {
            $strikePrice = floatval($this->state()->data()[$this->userData($game)['data']['coin'] . '-STRIKE']);
            $currentPrice = floatval($this->state()->data()[$this->userData($game)['data']['coin']]['price']);

            $isWin = $this->userData($game)['data']['target'] === 'higher' ? $currentPrice > $strikePrice : $currentPrice < $strikePrice;
            $multiplier = 0;

            if($isWin) {
                $multiplier = HouseEdgeModule::apply($this, (4 - 4 * (1 - ($this->gameData($game)['decay'] / 100))));
                if($multiplier < 1.01) $multiplier = 1.01;
            }

            $this->win($game, $multiplier, 0);
        }

        $this->state()->resetPlayers();
    }

    public function startChain() {
        $this->state()->serverSeed(ProvablyFair::generateServerSeed()); // used as id for @getActiveGames()
        $this->state()->clientSeed('This game is not verifiable using "Provably Fair" tools, please verify market prices during bet time instead.');
        $this->state()->nonce(-1);
        $this->state()->betting(false);

        dispatch(new MultiplayerFinishAndSetupNextGame($this, [], now()));
    }

    function result(ProvablyFairResult $result): array {
        throw new UnsupportedOperationException();
    }

}
