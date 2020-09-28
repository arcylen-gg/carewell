import Vue from 'vue'
import Vuetify from 'vuetify/lib'
import 'vuetify/src/stylus/app.styl'

Vue.use(Vuetify, {
  iconfont: 'md',
  theme: {
    primary: '#222C3C',
    secondary: '#303E55',
    tertiary: '#DA463A',
    accent: '#1E3581',
    error: '#b71c1c'
  }
})
