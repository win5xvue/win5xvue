<template>
    <span>{{ computedValue }}</span>
</template>

<script>
    import bitcoin from 'bitcoin-units';
    import { mapGetters } from 'vuex';

    export default {
        computed: {
            computedValue() {
                if(this.to.startsWith('local_')) return (this.value ? this.value : 0).toFixed(2);

                return bitcoin(this.value ? this.value : 0, 'btc').to(this.unit).value().toFixed(this.unit === 'satoshi' ? 0 : 8);
            },
            ...mapGetters(['unit'])
        },
        props: ['value', 'to']
    }
</script>
