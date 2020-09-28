export default {
    methods : {
        AVAILMENT_AUTOSAVE_MIXINS_AUTOSAVE(form ,source){
            if(!this.$route.query.id) {
                EventBus.$emit('autoSaveForm', {form, source});
            }
        }
    }
}