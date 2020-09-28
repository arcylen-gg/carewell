import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'

Vue.use(Vuex)

export default new Vuex.Store({
  plugins: [createPersistedState()],
  state: {
    user: null,
    pages: [],
    benefits: [],
    procedure_types: [],
    count: [],
    access_token: localStorage.getItem('access_token'),
		refresh_token: localStorage.getItem('refresh_token'),
		token_type: localStorage.getItem('token_type'),
		token_expire: localStorage.getItem('token_expire'),
		grant_type: "password",
		client_id: 1,
		client_secret: "ZWEWo9Es5jJpmW74pNAdVeoTKJWRKNLzwZovlmRv",
  },
  mutations: {
    login (state, data)
		{
			state.access_token 		= data.access_token;
			state.refresh_token 	= data.refresh_token;
			state.token_type 		  = data.token_type;
			state.token_expire 		= data.token_expire;

			localStorage.setItem('access_token', data.access_token);
			localStorage.setItem('refresh_token', data.refresh_token);
			localStorage.setItem('token_type', data.token_type);
			localStorage.setItem('token_expire', data.token_expire);
		},
		logout (state)
		{
			state.access_token 		= null;
			state.refresh_token 	= null;
			state.token_type 		= null;
			state.token_expire 		= null;
			state.user 				= null;

			localStorage.removeItem('access_token');
			localStorage.removeItem('refresh_token');
			localStorage.removeItem('token_type');
			localStorage.removeItem('token_expire');
			localStorage.removeItem('vuex');
    },
    store_user (state, user)
	{
		state.user = user;
	},
	store_pages (state, pages)
	{
		state.pages = pages;
	},
	store_benefits (state, benefits)
	{
		state.benefits = benefits;
	},
	store_procedure_types (state, procedure_types)
	{
		state.procedure_types = procedure_types;
	},
	store_count (state, count)
	{
		state.count = count;
	},
  },
  actions: {

  }
})
