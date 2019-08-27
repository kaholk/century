<template>
	<div>
		<h1>Century: Golem Edition</h1>
		<v-form ref="form" v-model="valid" lazy-validation>
						<v-alert
				v-model="error"
				prominent
				type="error"
				border="left"
				dismissible
			>Coś poszło nie tak, spróbuj ponownie poźniej</v-alert>
			<v-text-field v-model="login.value" :rules="login.rules" label="Login" required></v-text-field>
			<v-text-field v-model="password.value" :rules="password.rules" label="Hasło" required></v-text-field>
			<v-btn :disabled="!valid || loading" color="success" class="mr-4" :loading="loading" @click="validate">Zaloguj</v-btn>
			Nie masz konta ?
			<router-link :to="{ name:'register'}">Zarejestruj się</router-link>
		</v-form>
	</div>
</template>

<script>
import api from '@/api/api';

export default {
	data: () => ({
		valid:true,
		loading:false,
		error:false,
		login:{
			value: "",
			rules: [v => !!v || "Login jest wymagany"]
		},
		password:{
			value: "",
			rules: [v => !!v || "Hasło jest wymagane"]
		},
  }),
  methods:{
	  	validate() {
			if(this.$refs.form.validate()) {
				this.loading = true;
				this.error = false;
				api.post('api/login.php',{
					login: this.login.value,
					password: this.password.value
				}).then(res=>{
					// 	this.$store.dispatch("updateUserData",res.user);
				// 	this.$router.push({name:"rooms"})
				})
				.catch(err=>{
					this.error = true;
				})
				.finally(()=>{
					this.loading = false;
				})
			}
		}
  }
};
</script>
