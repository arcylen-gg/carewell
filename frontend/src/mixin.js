import Vue from 'vue'
import router from './router'
import store from './store'

import { mapState } from 'vuex'

Vue.mixin({
    filters:{
        mixin_change_date_format(value){
            let format = new Date(value)
            //set month format
            let month = (1 + format.getMonth()).toString();
            month = month.length > 1 ? month : '0' + month;

            //set day format
            let day = format.getDate().toString();
            day = day.length > 1 ? day : '0' + day;

            let dateFormat = month + '/' + day + '/' +  format.getFullYear();
            let dateShow = dateFormat !== 'NaN/NaN/NaN' ? dateFormat : "";
            return dateShow;
       } ,
       mixin_change_currency_format(value){
            return parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,")
       }
    },
    methods: {
        go_to_url(url){
                this.$router.push(url);
        },
        number_format(num)
        {
            return Number(num).toLocaleString()
        },
        currency_format(sign, num)
        {
            return sign + " " + num.toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,")
        },
        change_date_format(dateValue)
        {
            let format = new Date(dateValue)
            
            //set month format
            let month = (1 + format.getMonth()).toString();
            month = month.length > 1 ? month : '0' + month;

            //set day format
            let day = format.getDate().toString();
            day = day.length > 1 ? day : '0' + day;

            let dateFormat = month + '/' + day + '/' +  format.getFullYear();
            return dateFormat;
        },
        isNumber: function(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46 && charCode !== 44) {
            evt.preventDefault();;
            } else {
            return true;
            }
        },
        toaster:(msg, t) => {
            Vue.toasted.show(msg, {
            singleton: true,
            type : t != 200 ? 'error' : 'success',
            icon : t != 200 ? 'error_outline' : 'check_circle_outline',
            position: 'bottom-center',
            duration: null,
            action : {
                text : 'Close',
                color : 'white',
                onClick : (e, toastObject) => {
                    toastObject.goAway(0);
                }
            },
            })
        },
        get_user () {
            axios
            .get('/api/user/info', {})
            .then(res => { 
                store.commit('store_user', res.data)
                this.get_pages()
            })
            .catch(err => { router.push('/login') })
        },
        get_count () {
            axios
            .get('/api/count', {})
            .then(res => { 
                store.commit('store_count', res.data)
            })
            .catch(err => { console.log(err) })
        },
        get_pages () {
            axios
            .get('/api/pages', {})
            .then(res => { store.commit('store_pages', res.data.data.data) })
            .catch(err => { console.log(err) })
        },
        get_benefits () {
            axios
            .get('/api/benefit_type', {})
            .then(res => { store.commit('store_benefits', res.data.data.data) })
            .catch(err => { console.log(err) })
        },
        get_procedure_types () {
            var params = {}
            params.is_archived = 0
            axios
            .get('/api/procedures_type' + this.generateFilterURL(params), {})
            .then(res => { store.commit('store_procedure_types', res.data.data.data) })
            .catch(err => { console.log(err) })
        },
        get_page_access (pages, route) {
            var d = null
            pages.forEach(data => {
                if(data.code == route)
                {
                    d = data
                }
            })

            return d
        },
        generateFilterURL(obj){
            var new_arr = []
            var columns = Object.keys(obj)
            var values  = Object.values(obj)
            
            new_arr = columns.map((data, key) => {
                var conjunction = values.length-1 > key ? "&" : ""
                var params      = data + "=" + values[key] + conjunction
                return params
            })

            return "?" + new_arr.join("")
        },
        logout () {
            store.commit('logout')
            router.push('/login')
        },
    },
    computed: {
        ...mapState({
            user:       state => state.user,
            pages:      state => state.pages,
            benefits:      state => state.benefits,
            procedure_types:      state => state.procedure_types,
            count:      state => state.count,
        }),
    },
    
})