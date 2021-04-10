<script>
    import Bus from '../../bus';
    import VueRecaptcha from 'vue-recaptcha';

    import recaptchaKey from '../../../../storage/recaptcha.key';
    import ForgotPasswordModal from "./ForgotPasswordModal";

    export default {
        methods: {
            open(type) {
                Bus.$emit('modal:new', {
                    name: 'register',
                    title: 'general.auth.' + (type === 'auth' ? 'login' : 'register'),
                    component: {
                        components: { VueRecaptcha },
                        data() {
                            return {
                                type: type,
                                email: '',
                                login: '',
                                password: '',
                                acceptTerms: false,

                                recaptchaKey: '6LcxQqQaAAAAAMgjDKICXA2FRQPMT2FhiQwnmZpu',
                                captchaPayload: null
                            }
                        },
                        created() {
                            Bus.$on('login:fail', () => this.$toast.error(this.$i18n.t('general.auth.wrong_credentials')), true);
                            Bus.$on('login:success', () => Bus.$emit('modal:close'), true);
                        },
                        methods: {
                            openForgotPasswordModal() {
                                Bus.$emit('modal:close');
                                ForgotPasswordModal.methods.open();
                            },
                            onVerify(response) {
                                this.captchaPayload = response;
                            },
                            onExpired() {
                                this.captchaPayload = null;
                            },
                            done() {
                                const login = () => {
                                    this.$store.dispatch('login', {
                                        login: this.login,
                                        password: this.password,
                                        captcha: this.captchaPayload
                                    }).catch(() => this.$toast.error(this.$i18n.t('general.auth.wrong_credentials')));
                                };

                                if(this.type === 'register') {
                                    axios.post('/auth/register', {
                                        email: this.email,
                                        name: this.login,
                                        password: this.password,
                                        captcha: this.captchaPayload
                                    }, {
                                        withCredentials: true
                                    }).then(({ data }) => {
                                        this.$store.dispatch('setUserData', data);
                                        this.$store.dispatch('updateData');
                                        Bus.$emit('login:success');
                                    }).catch(() => this.$toast.error(this.$i18n.t('general.auth.wrong_credentials')));
                                } else login();
                            }
                        },
                        template: `
                            <div>
                                <!--
                                <div class="divider">
                                    <div class="line"></div>
                                    {{ $t('general.auth.through_social') }}
                                    <div class="line"></div>
                                </div>
                                <div class="mt-2">
                                    <div class="auth-button-group">
                                        <button class="btn btn-vk"><i class="fab fa-vk"></i></button>
                                        <button class="btn btn-facebook"><i class="fab fa-facebook"></i></button>
                                        <button class="btn btn-google"><i class="fab fa-google"></i></button>
                                        <button class="btn btn-discord"><i class="fab fa-discord"></i></button>
                                        <button class="btn btn-steam"><i class="fab fa-steam"></i></button>
                                    </div>
                                </div>
                                -->
                                <div class="divider">
                                    <div class="line"></div>
                                    {{ $t('general.auth.through_login') }}
                                    <div class="line"></div>
                                </div>
                                <div class="mt-2 mb-2" v-if="type === 'register'">
                                    <input v-model="email" type="email" :placeholder="$t('general.auth.credentials.email')">
                                </div>
                                <div class="mt-2 mb-2">
                                    <input v-model="login" type="text" :placeholder="$t('general.auth.credentials.login')">
                                </div>
                                <div class="mt-2 mb-2">
                                    <input v-model="password" type="password" :placeholder="$t('general.auth.credentials.password')">
                                </div>
                                <vue-recaptcha class="recaptcha" theme="dark" :sitekey="recaptchaKey" :loadRecaptchaScript="true"
                                  @verify="onVerify" @expired="onExpired"></vue-recaptcha>

                                <div class="custom-control custom-checkbox mb-2" v-if="type === 'register'">
                                    <label>
                                        <input type="checkbox" class="custom-control-input" @change="acceptTerms = !acceptTerms">
                                        <div class="custom-control-label" v-html="$t('general.auth.notice')"></div>
                                    </label>
                                </div>

                                <button class="btn btn-primary btn-block p-3" :disabled="(type === 'register' && !acceptTerms) || !captchaPayload" @click="done">{{ $t('general.auth.'+(type === 'auth' ? 'login' : 'register')) }}</button>

                                <div class="divider">
                                    <div class="line"></div>
                                    {{ $t('general.or') }}
                                    <div class="line"></div>
                                </div>

                                <div class="auth-footer">
                                    <span @click="openForgotPasswordModal">{{ $t('general.auth.forgot_password') }}</span>
                                </div>

                                <div class="auth-footer" v-if="type === 'register'">
                                    <span @click="type = 'auth'">{{ $t('general.auth.login') }}</span>
                                </div>
                                <div class="auth-footer" v-else>
                                    <span @click="type = 'register'">{{ $t('general.auth.create_account') }}</span>
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

    .xmodal.register {
        width: 350px !important;

        .recaptcha {
            margin-top: 10px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-button-group {
            display: flex;
            button {
                position: relative;
                display: inline-flex;
                width: 33.333%;
                margin: 0 5px;
                padding: 15px;
                align-items: center;
                justify-content: center;

                i {
                    position: absolute;
                }
            }
        }

        .auth-footer {
            text-align: center;
            margin-top: 15px;

            div:first-child {
                font-size: 11px;
                margin-bottom: 10px;
            }

            span:first-of-type, span:last-of-type {
                @include themed() {
                    color: t('link');
                    transition: color 0.3s ease;
                    cursor: pointer;
                    &:hover {
                        color: t('link-hover');
                    }
                }
            }

            .or {
                display: inline-flex;
                margin: 7px 5px 0;
                cursor: default;
                user-select: none;
                @include themed() {
                    background: t('link');
                }
                width: 1px;
                height: 9px;
            }
        }
    }
</style>
