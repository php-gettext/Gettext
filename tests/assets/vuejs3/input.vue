<template>
    {{ __('Let’s test ā ūtf8 štriņģ ❤️') }}
</template>

<script>
    export default {
        computed: {
            buttonText() {
                return __('We’re happy to have you here, 愛');
            }
        },
    }
</script>
