<script>
    import { mapGetters } from 'vuex';
    import Bus from '../../bus';
    import VipModal from "./VipModal";
    import VipBonusModal from "./VipBonusModal";
    import AuthModal from "./AuthModal";

    class Modal {
        constructor(vm) {
            this.vm = vm;
        }

        wheel() {
            const p = this.vm.currencies[this.vm.currency].price, rewards = [
                {
                    value: this.vm.rawBitcoin(this.vm.currency, this.vm.usdToToken(p, 0.30)),
                    color: '#f46e42'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, this.vm.usdToToken(p, 0.40)),
                    color: '#508bf0'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, this.vm.usdToToken(p, 0.50)),
                    color: '#df1347'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, this.vm.usdToToken(p, 0.80)),
                    color: '#d1d652'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, this.vm.usdToToken(p, 1.00)),
                    color: '#ffc645'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, this.vm.usdToToken(p, 2.00)),
                    color: '#f46e42'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, this.vm.usdToToken(p, 3.00)),
                    color: '#508bf0'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, this.vm.usdToToken(p, 5.00)),
                    color: '#df1347'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, this.vm.usdToToken(p, 7.00)),
                    color: '#d1d652'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, this.vm.usdToToken(p, 10.00)),
                    color: '#ffc645'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, this.vm.usdToToken(p, 50.00)),
                    color: '#f46e42'
                }
            ];

            let slides = [];
            _.forEach(rewards, (reward) => {
                slides.push({
                    text: `${reward.value} ${this.vm.currencies[this.vm.currency].name.substr(0, 3)}`,
                    value: slides.length,
                    border: {
                        radius: 3.25,
                        fill: reward.color
                    }
                });
            });

            $('.wheel').wheel({
                slices: slides,
                selector: 'value',
                width: 350,
                text: {
                    color: "white",
                    size: 11,
                    offset: 5,
                    arc: false
                },
                outer: {
                    width: 0,
                    color: 'transparent'
                },
                inner: {
                    width: 11,
                    color: '#222127'
                },
                line: {
                    width: 3,
                    color: '#222127'
                },
                slice: {
                    background: '#2a2a2f'
                }
            });

            $('.wheel').wheel('onStep', () => this.vm.playSound('/sounds/tick.mp3'));

            $('.wheel').wheel('onComplete', () => {
                this.vm.timeout();
                $('.wheelSpin').addClass('disabled');
                $('.wheelBlock').fadeIn('fast');
            });

            $('[data-bonus-modal-contents] .wheelSpin').on('click', () => {
                if($('[data-bonus-modal-contents] .wheelSpin').hasClass('disabled')) return;
                $('[data-bonus-modal-contents] .wheelSpin').toggleClass('disabled', true);

                axios.post('/api/promocode/bonus').then(({ data }) => {
                    $('.wheelBlock').fadeOut('fast');
                    window.next = data.next;
                    $('.wheel').wheel('start', data.slice);
                }).catch((error) => {
                    $('[data-bonus-modal-contents] .wheelSpin').toggleClass('disabled', false);
                    if(error.response.data.code === 2) this.vm.$toast.error(this.vm.$i18n.t('general.error.should_have_empty_balance'));
                });
            });
        }

        partner() {
            const v = this.vm.currencies[this.vm.currency].referralBonusWheel, rewards = [
                {
                    value: this.vm.rawBitcoin(this.vm.currency, v),
                    color: '#f46e42'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, v * 1.15),
                    color: '#508bf0'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, v * 1.3),
                    color: '#df1347'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, v * 1.15),
                    color: '#d1d652'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, v * 1.5),
                    color: '#ffc645'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, v),
                    color: '#f46e42'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, v * 2),
                    color: '#508bf0'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, v),
                    color: '#df1347'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, v * 1.15),
                    color: '#d1d652'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, v),
                    color: '#ffc645'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, v * 1.15),
                    color: '#f46e42'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, v * 1.5),
                    color: '#508bf0'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, v),
                    color: '#df1347'
                },
                {
                    value: this.vm.rawBitcoin(this.vm.currency, v * 2),
                    color: '#d1d652'
                }
            ];
            let slides = [];
            _.forEach(rewards, (reward) => {
                slides.push({
                    text: `${reward.value} ${this.vm.currencies[this.vm.currency].name.substr(0, 3)}`,
                    value: slides.length,
                    border: {
                        radius: 3.25,
                        fill: reward.color
                    }
                });
            });

            $('.wheel').wheel({
                slices: slides,
                selector: 'value',
                width: 350,
                text: {
                    color: "white",
                    size: 11,
                    offset: 5,
                    arc: false
                },
                outer: {
                    width: 0,
                    color: 'transparent'
                },
                inner: {
                    width: 11,
                    color: '#222127'
                },
                line: {
                    width: 3,
                    color: '#222127'
                },
                slice: {
                    background: '#2a2a2f'
                }
            });

            $('.wheel').wheel('onStep', () => this.vm.playSound('/sounds/tick.mp3'));

            $('.wheel').wheel('onComplete', () => {
                $('.wheelSpin').addClass('disabled');
                $('.wheelBlock').fadeIn('fast');
            });

            $('[data-bonus-modal-contents] .wheelSpin').on('click', () => {
                if($('[data-bonus-modal-contents] .wheelSpin').hasClass('disabled')) return;
                $('[data-bonus-modal-contents] .wheelSpin').toggleClass('disabled', true);

                axios.post('/api/promocode/partner_bonus').then(({ data }) => {
                    $('.wheel').wheel('start', data.slice);
                    $('.wheelBlock').fadeOut('fast');
                }).catch((error) => {
                    $('[data-bonus-modal-contents] .wheelSpin').toggleClass('disabled', false);
                    if(error.response.data.code === 1) {
                        Bus.$emit('modal:close');
                        this.vm.$router.push('/partner');
                    }
                });
            });
        }

        promocode() {
            $('#activate').on('click', () => {
                if($('#activate').hasClass('disabled')) return;
                $('#activate').addClass('disabled');

                axios.post('/api/promocode/activate', { code: $('#code').val() }).then(() => {
                    $('#activate').removeClass('disabled');
                    this.vm.$toast.success(this.vm.$i18n.t('bonus.promo.success'));
                }).catch((code) => {
                    if(code.response.data.code === 1) this.vm.$toast.error(this.vm.$i18n.t('bonus.promo.invalid'));
                    if(code.response.data.code === 2) this.vm.$toast.error(this.vm.$i18n.t('bonus.promo.expired_time'));
                    if(code.response.data.code === 3) this.vm.$toast.error(this.vm.$i18n.t('bonus.promo.expired_usages'));
                    if(code.response.data.code === 4) this.vm.$toast.error(this.vm.$i18n.t('bonus.promo.used'));
                    if(code.response.data.code === 5) this.vm.$toast.error(this.vm.$i18n.t('general.error.promo_limit'));
                    if(code.response.data.code === 7) this.vm.$toast.error(this.vm.$i18n.t('general.error.vip_only_promocode'));
                    if(code.response.data.code === 8) this.vm.$toast.error(this.vm.$i18n.t('general.error.promo_balance_limit'));

                    $('#activate').removeClass('disabled');
                });
            });
        }

        discord() {
            $('[data-check-subscription]').on('click', () => {
                if($('[data-check-subscription]').hasClass('disabled')) return;
                $('[data-check-subscription]').addClass('disabled');

                axios.post('/auth/discord_bonus').then(() => {
                    this.vm.$toast.success(this.vm.$i18n.t('bonus.discord.success'));
                    this.vm.$router.go();
                }).catch((error) => {
                    this.vm.$toast.error(this.vm.$i18n.t('bonus.discord.error.' + error));
                    $('[data-check-subscription]').removeClass('disabled');
                });
            });
        }
    }

    export default {
        methods: {
            open() {
                Bus.$emit('modal:new', {
                    name: 'faucet',
                    component: {
                        data() {
                            return {
                                tab: 'wheel',

                                interval: null
                            }
                        },
                        computed: {
                            ...mapGetters(['isGuest', 'user', 'currencies', 'currency', 'user'])
                        },
                        watch: {
                            tab() {
                                this.loadTab();
                            },
                            currency() {
                                this.loadTab();
                            }
                        },
                        methods: {
                            loadTab() {
                                let html = '';
                                switch (this.tab) {
                                    case 'discord':
                                        html = `
                                            <div class="bonus-side-menu-container text-center">
                                                <div class="header text-center">
                                                    <div class="title">${this.$i18n.t('bonus.discord.title')}</div>
                                                    <div class="description">${this.$i18n.t('bonus.discord.description')}</div>
                                                </div>
                                                <div class="bonusContent">
                                                    <div>${this.$i18n.t('bonus.discord.common_desc')}</div>
                                                    ${this.user.user.discord ? `
                                                        <button class="btn btn-primary btn-block mt-2" data-check-subscription>${this.$i18n.t('bonus.discord.check')}</button>
                                                    ` : `
                                                        ${this.$i18n.t('bonus.discord.link')}
                                                        <button class="btn btn-primary btn-block mt-2" onclick="window.location.href = '/profile/${this.user.user._id}#settings'">${this.$i18n.t('bonus.discord.redirect')}</button>
                                                    `}
                                                </div>
                                            </div>
                                        `;
                                        break;
                                    case 'rain':
                                        html = `<div class="bonus-side-menu-container text-center">
                                                    <div class="header text-center">
                                                        <div class="title">${this.$i18n.t('bonus.rain.title')}</div>
                                                        <div class="description">${this.$i18n.t('bonus.rain.description')}</div>
                                                    </div>
                                                    <div class="bonusContent" style="margin-top: 50px;">${this.$i18n.t('bonus.rain.modal_description')}</div>
                                                </div>`;
                                        break;
                                    case 'promo':
                                        html = `
                                            <div class="bonus-side-menu-container">
                                                <div class="header text-center">
                                                    <div class="title">${this.$i18n.t('bonus.promo.title')}</div>
                                                    <div class="description">${this.$i18n.t('bonus.promo.description')}</div>
                                                </div>
                                                <div class="bonusContent">
                                                    <div class="mt-2">
                                                        <input id="code" type="text" placeholder="${this.$i18n.t('bonus.promo.placeholder')}">
                                                    </div>
                                                    <button id="activate" class="btn btn-primary mt-2">${this.$i18n.t('bonus.promo.activate')}</button>
                                                </div>
                                            </div>
                                        `;
                                        break;
                                    case 'wheel':
                                        html = `
                                            <div class="bonus-side-menu-container">
                                                <div class="header">
                                                    <div class="title">${this.$i18n.t('bonus.lucky_spin_everyday')}</div>
                                                    <div class="description">${this.$i18n.t('bonus.have_a_try')}</div>
                                                </div>
                                                <div class="wheelContainer">
                                                    <div class="wheel"></div>
                                                    <div class="wheelSpin">${this.$i18n.t('general.spin')}</div>

                                                    <div class="wheelBlock">
                                                        <svg><use href="#red-diamond"></use></svg>
                                                        <div class="ribbon">
                                                            <div class="ribbon-content"><p><b id="reload"></b></p></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;

                                        window.next = this.user.user.bonus_claim ? +new Date(this.user.user.bonus_claim) / 1000 : 0;
                                        this.timeout();
                                        break;
                                    case 'partner':
                                        html = `
                                            <div class="bonus-side-menu-container">
                                                <div class="header">
                                                    <div class="title">${this.$i18n.t('bonus.partner.title')}</div>
                                                    <div class="description" style="padding-left: 50%">${this.$i18n.t('bonus.partner.description')}</div>
                                                </div>
                                                <div class="wheelContainer">
                                                    <div class="wheel"></div>
                                                    <div class="wheelSpin">${this.$i18n.t('general.spin')}</div>

                                                    <div class="wheelBlock">

                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                        break;
                                }

                                $('[data-bonus-modal-contents]').hide().html(html).fadeIn('fast');

                                const modal = new Modal(this);
                                switch (this.tab) {
                                    case 'discord':
                                        modal.discord();
                                        break;
                                    case 'promo':
                                        modal.promocode();
                                        break;
                                    case 'wheel':
                                        modal.wheel();
                                        this.timeout();
                                        break;
                                    case 'partner':
                                        modal.partner();
                                        break;
                                }
                            },
                            timeout() {
                                if(this.interval != null) {
                                    clearInterval(this.interval);
                                    this.interval = null;
                                }

                                if(window.next && +new Date() / 1000 < window.next) {
                                    const timer = () => {
                                        const diff = ((window.next - (Date.now() / 1000)) | 0);
                                        let hours = Math.floor((diff % (60 * 60 * 24)) / (60 * 60));
                                        let minutes = ((diff % 3600) / 60) | 0;
                                        let seconds = (diff % 60) | 0;

                                        if(hours === 0 && minutes === 0 && seconds < 1) {
                                            clearInterval(this.interval);
                                            $('[data-bonus-modal-contents] .wheelSpin').toggleClass('disabled', false);
                                            $('#reload').html(this.$i18n.t('bonus.spin_now'));
                                            return;
                                        }

                                        hours = hours < 10 ? "0" + hours : hours;
                                        minutes = minutes < 10 ? "0" + minutes : minutes;
                                        seconds = seconds < 10 ? "0" + seconds : seconds;

                                        $('#reload').html(this.$i18n.t('bonus.next_spin') + `${hours}:${minutes}:${seconds}`);
                                        $('.wheelSpin').toggleClass('disabled', true);
                                    };

                                    this.interval = setInterval(() => {
                                        if($('#reload').length === 0) {
                                            clearInterval(this.interval);
                                            return;
                                        }

                                        timer();
                                    }, 1000);
                                    timer();
                                } else {
                                    $('#reload').html(this.$i18n.t('bonus.spin_now'));
                                    $('.wheelSpin').toggleClass('disabled', false);
                                }
                            },
                            openVipModal() {
                                Bus.$emit('modal:close');
                                VipModal.methods.open();
                            },
                            openVipBonusModal() {
                                Bus.$emit('modal:close');
                                VipBonusModal.methods.open();
                            }
                        },
                        mounted() {
                            this.loadTab();

                            if('serviceWorker' in navigator) {
                                const urlBase64ToUint8Array = function(base64String) {
                                    const padding = '='.repeat((4 - base64String.length % 4) % 4);
                                    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
                                    const rawData = window.atob(base64);
                                    const outputArray = new Uint8Array(rawData.length);
                                    for (let i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
                                    return outputArray
                                };

                                const subscribe = () => {
                                    navigator.serviceWorker.ready.then(registration => {
                                        const options = { userVisibleOnly: true };
                                        const vapidPublicKey = window.Notifications.vapidPublicKey;

                                        if(vapidPublicKey) options.applicationServerKey = urlBase64ToUint8Array(vapidPublicKey);

                                        registration.pushManager.subscribe(options).then(subscription => {
                                            updateSubscription(subscription);
                                        }).catch(e => {
                                            if(Notification.permission === 'denied') {
                                                console.log('Permission for Notifications was denied');
                                                this.$toast.error(this.$i18n.t('general.error.disabled_notifications'));
                                            } else {
                                                console.error('Unable to subscribe to push', e);
                                            }
                                        });
                                    });
                                };

                                const updateSubscription = (subscription) => {
                                    const key = subscription.getKey('p256dh');
                                    const token = subscription.getKey('auth');
                                    const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];
                                    const data = {
                                        endpoint: subscription.endpoint,
                                        publicKey: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
                                        authToken: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
                                        contentEncoding
                                    };

                                    axios.post('/api/subscription/update', data).then(() => $('[data-notification-bonus]').stop().slideUp('fast', () => $('[data-notification-bonus]').remove()));
                                };

                                navigator.serviceWorker.register('/sw.js', { scope: '/' }).then(() => {
                                    if(!('showNotification' in ServiceWorkerRegistration.prototype)) {
                                        console.error('Notifications aren\'t supported');
                                        $('[data-notification-bonus]').stop().slideUp('fast', () => $('[data-notification-bonus]').remove())
                                        return;
                                    }

                                    if(!('PushManager' in window)) {
                                        console.error('Push messaging isn\'t supported');
                                        $('[data-notification-bonus]').stop().slideUp('fast', () => $('[data-notification-bonus]').remove())
                                        return;
                                    }

                                    navigator.serviceWorker.ready.then(registration => {
                                        registration.pushManager.getSubscription().then(subscription => {
                                            if(!subscription) return;

                                            updateSubscription(subscription);
                                        }).catch(e => {
                                            console.error('Error during getSubscription()', e);
                                        });
                                    });
                                });

                                $('[data-notification-bonus]').slideDown('fast');
                                $('[data-notification-bonus]').on('click', () => {
                                    if(this.isGuest) {
                                        AuthModal.methods.open('auth');
                                        return;
                                    }

                                    subscribe();
                                });
                            } else console.error('ServiceWorker isn\'t supported');
                        },
                        template: `
                                <div class="bonusContainer">
                                    <div data-bonus-modal-contents></div>

                                    <div class="bonusSidebar">
                                        <div :class="'sidebarEntry ' + (tab === 'wheel' ? 'active' : '')" @click="tab = 'wheel'">
                                            <div class="icon">
                                                <icon icon="wheel"></icon>
                                            </div>
                                        </div>
                                        <div :class="'sidebarEntry ' + (tab === 'partner' ? 'active' : '')" @click="tab = 'partner'">
                                            <div class="icon">
                                                <i class="fas fa-paper-plane"></i>
                                            </div>
                                        </div>
                                        <div :class="'sidebarEntry ' + (tab === 'promo' ? 'active' : '')" @click="tab = 'promo'">
                                            <div class="icon">
                                                <i class="fas fa-barcode-alt"></i>
                                            </div>
                                        </div>
                                        <div :class="'sidebarEntry ' + (tab === 'discord' ? 'active' : '')" @click="tab = 'discord'">
                                            <div class="icon">
                                                <i class="fab fa-discord"></i>
                                            </div>
                                        </div>
                                        <div :class="'sidebarEntry ' + (tab === 'rain' ? 'active' : '')" @click="tab = 'rain'">
                                            <div class="icon">
                                                <i class="fas fa-cloud-sun-rain"></i>
                                            </div>
                                        </div>
                                        <div class="sidebarEntry" @click="user.user.vipLevel === 0 ? openVipModal() : openVipBonusModal()">
                                        <div class="icon">
                                            <i class="fas fa-crown"></i>
                                        </div>
                                    </div>
                                    <div class="sidebarEntry" data-notification-bonus>
                                        <div class="icon">
                                            <i class="fas fa-bell"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>`
                    }
                });
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .xmodal.faucet {
        overflow-x: unset !important;

        .modal_template, .modal_content, .os-viewport, .os-padding, .os-host-overflow {
            overflow: unset !important;
        }

        ul {
            list-style: none;
        }

        .bonusContainer {
            @include themed() {
                height: 100%;
                background: t('sidebar');
                border-radius: 3px;
                border: 1px solid t('border');
                box-shadow: t('shadow');

                .bonus-side-menu-container {
                    .header {
                        background: t('body');
                        width: calc(100% + 40px);
                        text-align: right;
                        padding-right: 45px;
                        padding-top: 15px;
                        padding-bottom: 15px;
                        margin-left: -20px;
                        position: absolute;
                        top: 0;

                        .title {
                            font-size: 1.1em;
                            font-weight: 600;
                        }

                        &.text-center {
                            padding-right: 0;

                            .description {
                                padding-left: 10%;
                                padding-right: 10%;
                            }
                        }
                    }
                }

                .wheel {
                    margin-left: -35%;
                    margin-top: -50%;
                    position: absolute;
                }

                .bonusSidebar {
                    width: 100%;
                    display: flex;
                    position: absolute;
                    bottom: 25px;
                    left: 0;
                    height: 63px;

                    $inactive: t('input');
                    $active: t('secondary');

                    .sidebarEntry {
                        position: relative;
                        display: flex;
                        cursor: pointer;
                        margin-right: 85px;
                        width: 62px;
                        height: 62px;
                        margin-right: 20px;

                        &:first-child {
                            margin-left: auto;
                        }

                        &:last-child {
                            margin-right: auto;
                        }

                        .icon {
                            background: $inactive;
                            width: 62px;
                            height: 62px;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            transition: background 0.3s ease;

                            i, svg {
                                font-size: 1.1em;
                                color: rgba(t('text'), 0.5);
                                transition: color 0.3s ease;
                            }
                        }
                    }

                    .sidebarEntry.active {
                        .icon {
                            background: $active;

                            i, svg {
                                color: white;
                            }
                        }
                    }
                }
            }
        }

        .bonusContent {
            padding-left: 50px;
            padding-right: 80px;
            display: flex;
            flex-direction: column;
        }

        [data-bonus-modal-contents] {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
            min-height: 400px;

            @include themed() {
                .wheelContainer {
                    position: relative;
                    user-select: none;

                    .wheelSpin:not(.btn) {
                        position: absolute;
                        background: #d64b4b;
                        top: -155px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        width: 101px;
                        height: 102px;
                        left: -71px;
                        text-transform: uppercase;
                        font-size: 1.2em;
                        cursor: pointer;
                        color: t('text');
                        transition: background .3s ease, color .3s ease;

                        animation: pulse 1s infinite ease-in-out;

                        &.disabled {
                            background: t('sidebar');
                            color: transparent;
                            cursor: default;
                            animation: unset !important;
                        }
                    }

                    @keyframes pulse {
                        0% {
                            background: #d64b4b;
                        }

                        50% {
                            background: #ff5858;
                        }

                        100% {
                            background: #d64b4b;
                        }
                    }
                }

                .wheelBlock {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    position: absolute;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    margin-top: 45px;
                    margin-left: 60px;

                    #reload {
                        text-transform: uppercase;
                    }

                    svg {
                        width: 70px;
                        height: 90px;
                        margin-bottom: 170px;
                    }

                    .ribbon {
                        width: 250px;
                        position: absolute;
                        text-align: center;
                        font-size: 18px !important;
                        background: #D64B4B;
                    }

                    .ribbon p {
                        font-size: 18px !important;
                        margin: 0;
                        padding: 15px 10px;
                    }

                    .ribbon:before, .ribbon:after {
                        content: '';
                        position: absolute;
                        display: block;
                        bottom: -1em;
                        border: 1.5em solid #C23A3A;
                        z-index: -1;
                    }

                    .ribbon:before {
                        left: -2em;
                        border-right-width: 1.5em;
                        border-left-color: transparent;
                    }

                    .ribbon:after {
                        right: -2em;
                        border-left-width: 1.5em;
                        border-right-color: transparent;
                    }

                    .ribbon .ribbon-content:before, .ribbon .ribbon-content:after {
                        border-color: #871616 transparent transparent transparent;
                        position: absolute;
                        display: block;
                        border-style: solid;
                        bottom: -1em;
                        content: '';
                    }

                    .ribbon .ribbon-content:before {
                        left: 0;
                        border-width: 1em 0 0 1em;
                    }

                    .ribbon .ribbon-content:after {
                        right: 0;
                        border-width: 1em 1em 0 0;
                    }
                }
            }
        }
    }

    @include media-breakpoint-down(md) {
        .xmodal.faucet {
            .wheel {
                margin-left: 0 !important;
                margin-right: auto !important;
                transform: scale(.8) translate(-5%, -5%);
                margin-top: -270px !important;
            }

            .wheelBlock {
                margin-top: 40px !important;
                margin-left: 140px !important;
                transform: translate(-50%, -50%) scale(0.7) !important;
            }

            .wheelSpin {
                transform: scale(.8) translate(0%, 50%);
                left: 111px !important;
                top: -200px !important;
            }
        }
    }

    @include media-breakpoint-down(sm) {
        .xmodal.faucet {
            .wheel {
                transform: scale(0.65) translate(-25%, -5%);
                margin-top: -310px !important;
            }

            .header * {
                color: transparent !important;
            }

            .bonusSidebar {
                height: 35px !important;
            }

            .sidebarEntry {
                width: 42px !important;
                height: 42px !important;
                margin-right: 5px !important;

                &:last-child {
                    margin-right: auto !important;
                }

                .icon {
                    width: 42px !important;
                    height: 42px !important;

                    svg, i {
                        font-size: 0.6em !important;
                    }
                }
            }

            .wheelSpin {
                transform: scale(0.6) translate(0%, 50%);
                left: 68px !important;
                top: -227px !important;
            }

            .wheelBlock {
                margin-top: 90px !important;
                margin-left: 0 !important;
            }

            .sWheel-wrapper, .wheel {
                width: 350px !important;
                height: 350px !important;
                font-size: 87% !important;
            }
        }
    }
</style>
