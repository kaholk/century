import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
	state: {
		userData: {
			login: "",
			email: ""
		}
	},
	mutations: {
		updateUserData(state,payload){
			for(let keyP in payload){
				for(let keyD in state.userData)
				{
					if(keyP == keyD){
						state.userData[keyD] = payload[keyP];
						break;
					}
				}
			}
		}
	},
	actions: {
		// updateUserData(context,payload){
		// 	context.commit(updateUserData,payload);
		// }
	}
})
