<template>
    <div class="coming-soon">
        <div class="logo"></div>
        <div class="title">Мы скоро откроемся!</div>
        <div class="subtitle">Открытие состоится 9 января в 16:00 по московскому времени.</div>
        <div class="timer">{{ days }}:{{ hours }}:{{ minutes }}:{{ seconds }}</div>
    </div>
</template>

<script type="text/javascript">
    export default {
        data() {
            return {
                days: 0,
                hours: 0,
                minutes: 0,
                seconds: 0
            }
        },
        mounted() {
            let countDownDate = new Date("Jan 9, 2021 13:00:00 UTC").getTime();

            const timer = () => {
                let now = new Date().getTime();

                let distance = countDownDate - now;

                const pad = (n) => String("0" + n).slice(-2);

                this.days = pad(Math.floor(distance / (1000 * 60 * 60 * 24)));
                this.hours = pad(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)));
                this.minutes = pad(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)));
                this.seconds = pad(Math.floor((distance % (1000 * 60)) / 1000));

                if (distance < 0) {
                    clearInterval(interval);
                    window.allowed = true;
                    this.$router.push('/');
                }
            };

            let interval = setInterval(timer, 1000);

            timer();

            console.log(this.$route.fullPath)

            if(this.$route.fullPath === '/coming-soon-avoid') {
                this.setCookie('avoidComingSoon', 'true');
                this.$router.push('/');
            }

            window.allow = () => {
                window.allowed = true;
                this.$router.push('/');
            };
        }
    }
</script>

<style lang="scss" scoped>
    @import "resources/sass/variables";

    .coming-soon {
        z-index: 999999999;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        padding: 25vh 10%;

        @include themed() {
            background: t('body');

            .logo {
                background: url('/img/misc/logo.png') no-repeat center;
                background-size: cover;
                width: 150px;
                height: 90px;
                margin-bottom: 15px;
            }

            .title {
                font-size: 3em;
                font-weight: 600;
            }

            .subtitle {
                font-size: 1.5em;
                margin-bottom: 40px;
            }

            .timer {
                font-size: 5em;
                font-weight: 600;
            }
        }
    }

    @media(max-width: 991px) {
        .title {
            font-size: 1.7em !important;
        }

        .subtitle {
            font-size: 1.5em !important;
        }

        .timer {
            font-size: 3em !important;
        }
    }
</style>
