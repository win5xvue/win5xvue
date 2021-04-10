<template>
    <div class="bullContainer">
        <div class="gameTimer" v-if="gameEndsAt">
            <countdown :time="gameEndsAt >= 0 ? gameEndsAt : 0">
                <template slot-scope="props">
                    <div class="title">{{ $t('general.bullvsbear.' + (props.totalSeconds > 30 ? 'bet_phase' : 'waiting_phase')) }}</div>
                    <div class="time">{{ props.minutes === 0 && props.seconds === 0 ? $t('general.bullvsbear.processing') : (`0${props.minutes}`.slice(-2) + ':' + `0${props.seconds}`.slice(-2)) }}</div>
                    <div class="timerProgress" :set="progress = 100 - ((props.totalSeconds / 90) * 100)">
                        <div class="line" :style="{ width: progress + '%' }"></div>
                        <div :class="'circle ' + (progress >= 66.6 ? 'active' : '')"></div>
                        <div :class="'circle ' + (progress >= 100 ? 'active' : '')"></div>
                        <div class="circle active"></div>
                    </div>
                    <div class="decay" v-if="progress < 66.6">
                        <div class="title">{{ $t('general.bullvsbear.decay_value') }}</div>
                        <div class="decayValue" :set="mul = (4 - 4 * (1 - ((props.totalSeconds - 30 + 15) / 60)))">x{{ (mul < 1.01 ? 1.01 : mul).toFixed(2) }}</div>
                    </div>
                </template>
            </countdown>
        </div>
        <div class="strikePrice" v-if="tickers">
            <div class="title">{{ $t('general.strike_price') }}</div>
            <div class="price">{{ strikePrice.toFixed(2) }}$</div>
        </div>
        <div class="gauge" v-if="tickers">
            <div class="rangeStrike strike" :style="{ top: '50%', height: (height()).toFixed(1) + '%' }">
                <div class="price">{{ strikePrice.toFixed(2) }}$</div>
            </div>
            <div class="rangeStrike current" :style="{ top: strikeTopPercent() + '%', height: (height()).toFixed(1) + '%' }">
                <div class="price">{{ parseFloat(tickers[this.ticker].price).toFixed(2) }}$</div>
            </div>
            <div v-for="n in rows() + 1" class="gaugeRange" :style="{ top: (((n - 1) / rows()) * 100).toFixed(1) + '%', height: height().toFixed(1) + '%' }"></div>
        </div>
        <loader class="m-auto" v-if="!tickers"></loader>
    </div>
</template>

<script>
    import Bus from "../../../bus";
    import HelpModal from "../../modals/HelpModal";

    export default {
        data() {
            return {
                strikePrice: null,
                tickers: null,
                ticker: 'BTC-USD',
                gameEndsAt: null,
                target: 'higher',

                /** The speed which determines how big are the jumps. */
                speed: {
                    'BTC-USD': 4,
                    'ETH-USD': 25
                }
            }
        },
        methods: {
            height() {
                const height = 100 / (this.rows() * 4);
                return height >= 1 ? 1 : height;
            },
            rows() {
                let rows = Math.abs(Math.floor((parseFloat(this.tickers[this.ticker].price) - this.strikePrice) * this.speed[this.ticker]));
                return rows < 5 ? 5 : (rows > 50 ? 50 : rows);
            },
            strikeTopPercent() {
                let percent = 50 - ((parseFloat(this.tickers[this.ticker].price) - this.strikePrice) * this.speed[this.ticker]);
                return percent < 10 ? 10 : (percent > 90 ? 90 : percent);
            },
            openHelpModal() {
                HelpModal.methods.open('bullvsbear');
            },
            getClientData() {
                return {
                    coin: this.ticker,
                    target: this.target
                }
            },
            getSidebarComponents() {
                return [
                    { name: 'label', data: { label: this.$i18n.t('general.wager') } },
                    { name: 'wager-classic' },
                    { name: 'label', data: { label: this.$i18n.t('general.select_currency') } },
                    { name: 'select', data: { options: [{ text: 'Bitcoin', value: 'BTC-USD' }, { text: 'Ethereum', value: 'ETH-USD' }], onSelect: (value) => {
                        this.ticker = value;
                        this.strikePrice = parseFloat(this.tickers[this.ticker + '-STRIKE']);
                    } } },
                    { name: 'label', data: { label: this.$i18n.t('general.bullvsbear.target') } },
                    { name: 'buttons', data: { buttons: [
                        { label: this.$i18n.t('general.bullvsbear.up'), callback: () => this.target = 'higher' },
                        { label: this.$i18n.t('general.bullvsbear.down'), callback: () => this.target = 'lower' },
                    ] } },
                    { name: 'multiplayer-table' },
                    { name: 'play' },
                    { name: 'footer', data: { buttons: ['help', 'sound', 'stats'] } },
                ];
            },
            gameDataRetrieved(data) {
                this.tickers = data.data;
                this.strikePrice = parseFloat(data.data[this.ticker + '-STRIKE']);
                this.gameEndsAt = (data.timestamp - +new Date() / 1000) * 1000;

                _.forEach(data.players, (player) => {
                    Bus.$emit('sidebar:multiplayer:add', { user: player.user, game: player.game });
                });

                $('.play-button').toggleClass('disabled', !data.can_bet);
            },
            multiplayerEvent(event, data) {
                switch (event) {
                    case "MultiplayerDataUpdate":
                        this.tickers = data.data;
                        this.strikePrice = parseFloat(data.data[this.ticker + '-STRIKE']);
                        break;
                    case "MultiplayerBettingStateChange":
                        $('.play-button').toggleClass('disabled', !data.state);
                        break;
                    case "MultiplayerTimerStart":
                        this.gameEndsAt = 90051; // Reset vue component
                        setTimeout(() => this.gameEndsAt = 90000, 50);
                        Bus.$emit('sidebar:multiplayer:clear');
                        break;
                    case "MultiplayerGameBet":
                        Bus.$emit('sidebar:multiplayer:add', { user: data.user, game: data.game, additional: `<i class="fal fa-${data.data.target === 'higher' ? 'chevron-up' : 'chevron-down'} ml-2" style="color: ${data.data.target === 'higher' ? '#32b746' : '#e9545d'}"></i>` });
                        break;
                }
            },
            callback(response) {
                $('.play-button').toggleClass('disabled', true);
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .game-bullvsbear {
        @include themed() {
            .bullContainer {
                height: 100%;
                display: flex;
                flex-direction: column;
                position: relative;
            }

            .strikePrice {
                position: absolute;
                right: 30px;
                top: 25px;
                text-align: right;

                .title {
                    color: #bfc3c7;
                    font-size: 1.35em;
                }

                .price {
                    color: #e9545d;
                    font-size: 2em;
                }
            }

            .gameTimer {
                position: absolute;
                left: 30px;
                top: 25px;
                min-width: 170px;

                .title {
                    color: #bfc3c7;
                    font-size: 1.35em;

                    .info {
                        opacity: 0.8;
                        transition: opacity 0.3s ease;
                        cursor: pointer;
                        margin-left: 3px;

                        &:hover {
                            opacity: 1;
                        }
                    }
                }

                .time {
                    font-size: 2em;
                }

                .timerProgress {
                    width: 100%;
                    height: 5px;
                    margin-top: 15px;
                    background: #bfc3c7;
                    position: relative;

                    .line {
                        height: 100%;
                        background: #e9545d;
                        width: 0;
                        transition: width 0.3s ease;
                    }

                    .circle {
                        border-radius: 50%;
                        transform: translateY(-50%) translateX(-50%);
                        background: #bfc3c7;
                        position: absolute;
                        top: 2px;
                        width: 12px;
                        height: 12px;
                        transition: background 0.3s ease;

                        &.active {
                            background: #e9545d;
                        }

                        &:first-child {
                            left: 0;
                        }

                        &:nth-child(2) {
                            left: 66.6%;
                        }

                        &:nth-child(3) {
                            left: 100%;
                        }
                    }
                }

                .decay {
                    margin-top: 25px;

                    .decayValue {
                        margin-top: 5px;
                        font-size: 1.25em;
                    }
                }
            }

            .gauge {
                width: 70px;
                height: 300px;
                position: relative;
                margin: auto;

                .gaugeRange {
                    width: 100%;
                    background: rgba(t('text'), .5);
                    height: 1px;
                    position: absolute;
                }

                .rangeStrike {
                    top: 50%;
                    position: absolute;
                    transform: translateY(-50%);
                    height: 1%;
                    width: 150%;
                    z-index: 2;
                    transition: top 0.1s ease;

                    .price {
                        position: absolute;
                        top: 0;
                        font-size: 1.3em;
                        font-weight: 600;
                    }
                }

                .strike {
                    background: #e9545d;
                    color: #e9545d;

                    .price {
                        right: -10px;
                        transform: translateX(100%) translateY(-50%);
                    }
                }

                .current {
                    background: #32b746;
                    color: #32b746;
                    margin-left: -50%;

                    .price {
                        left: -10px;
                        transform: translateX(-100%) translateY(-50%);
                    }
                }
            }
        }
    }

    @media(max-width: 991px) {
        .game-content-bullvsbear {
            @include themed() {
                height: 200px !important;

                .bullContainer {
                    transform: scale(0.9);
                }

                .strikePrice {
                    right: -25px;
                    top: -5px;

                    display: none;

                    .title {
                        font-size: 0.9em;
                    }

                    .price {
                        font-size: 1.2em;
                    }
                }

                .gameTimer {
                    left: -15px;
                    top: -5px;
                    min-width: 100px;
                    z-index: 5;

                    .title {
                        font-size: 0.9em;
                    }

                    .time {
                        font-size: 1.2em;
                    }

                    .timerProgress {
                        height: 3px;
                        margin-top: 5px;

                        .circle {
                            width: 8px;
                            height: 8px;
                        }
                    }

                    .decay {
                        margin-top: unset;
                        position: absolute;
                        bottom: -130px;

                        .decayValue {
                            font-size: 1.2em;
                        }
                    }
                }
            }
        }
    }
</style>
