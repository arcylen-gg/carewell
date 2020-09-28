<template>
    <v-text-field v-model="form.name" @change="getData"></v-text-field>
</template>
<script>
import { settingData } from '../SettingStore.js';

export default {
    data : () => ({
        form : {}
    }),
    methods : {
        getData(){
            console.log(this.form)
            settingData.commit('otherSetting',this.form);
            // EventBus.$emit('getComponentData',this.form);
        }
    }
}
</script>
