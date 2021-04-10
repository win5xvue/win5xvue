<template>
    <div :class="'floatingButtons ' + (chat ? '' : 'chatIsHidden')">
        <div class="floatingButton" @click="toggleChat">
            <svg><use href="#chat"></use></svg>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        computed: {
            ...mapGetters(['chat'])
        },
        methods: {
            toggleChat() {
                this.$store.dispatch('toggleChat');
            }
        }
    }
</script>
