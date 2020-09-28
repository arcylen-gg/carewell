import '@babel/polyfill'
import Vue from 'vue'
import './plugins/axios'
import './plugins/bootstrap-vue'
import './plugins/vuetify'
import './mixin';
import App from './App.vue'
import router from './router'
import store from './store'
import Components from './components';
import './assets/styles.scss'
import Toasted from 'vue-toasted';

window.EventBus = new Vue();
Vue.config.productionTip = false


Object.keys(Components).forEach(name => {
  Vue.component(name, Components[name]);
});

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')

Vue.use(Toasted);
