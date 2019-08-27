import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

export default new Router({
	mode: 'history',
	base: process.env.BASE_URL,
	routes: [
		{
			path: '/',
			name: 'home',
			component: () => import('./views/Home.vue')
		},
		{
			path: '/register',
			name: 'register',
			component: () => import('./views/Register.vue')
		},
		{
			path: '/help',
			name: 'help',
			component: () => import('./views/Help.vue')
		},
		// {
		// 	path: '/rooms',
		// 	name: 'rooms',
		// 	component: () => import('./views/Rooms.vue')
		// }
	]
})
