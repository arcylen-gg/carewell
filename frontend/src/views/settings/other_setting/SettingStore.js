import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export const settingData = new Vuex.Store({
    state:{
        form : {
            accountingPeriod : {},
            otherSetting : {},
        }
       
    },
    getters:{

    },
    mutations:{
        accountingPeriod : (state , formData )=> {
            state.form.accountingPeriod = formData
        },
        otherSetting : (state , formData )=> {
            state.form.otherSetting = formData
        }
    },
    actions:{

    }
})