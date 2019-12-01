<template>
    {{ __('no domain 1') }}
    {{gettext('no domain 2')}}

    {{dgettext('domain1', 'domain1 text')}}
    {{dngettext('domain1', 'domain1 text singular', 'domain1 plural', 123)}}

    {{dgettext('domain2', 'domain2 text')}}

    <label :v-text="__('no domain 3')"></label>
    <label :v-text="__('no domain 4 with diacritics čťľďáé')"></label>

</template>

<script>
    export default {
        computed: {
            buttonText() {
                var test = {
                    key: this.dn__('domain 3', 'domain 3 singular', 'domain 3 plural', this.randomMethod())
                };

                dgettext('domainUnknown', 'Unkown domain text')
            }
        },
    }
</script>
