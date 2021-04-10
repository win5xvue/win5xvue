<?php namespace App\Currency\Aggregator;

use App\Invoice;
use Illuminate\Http\Request;

abstract class Aggregator {

    abstract function invoice(Invoice $invoice): string;

    abstract function status(Request $request): string;

    abstract function validate(Request $request): bool;

    abstract function id(): string;

    abstract function name(): string;

    abstract function icon(): string;

    public static function list(): array {
        return [
            new FreeKassaAggregator()
        ];
    }

    public static function find(string $id): ?Aggregator {
        foreach (self::list() as $aggregator)
            if($aggregator->id() === $id) return $aggregator;
        return null;
    }

}
