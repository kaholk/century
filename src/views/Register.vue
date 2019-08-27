<template>
	<div class="home">
		<h1>Century: Golem Edition</h1>
		<v-form ref="form" v-model="valid" lazy-validation>
			<v-alert
				v-model="error"
				prominent
				type="error"
				border="left"
				dismissible
			>Coś poszło nie tak, spróbuj ponownie poźniej</v-alert>
			<v-alert
				v-model="success"
				prominent
				type="success"
				border="left"
			>Gratulacje, zostałeś zarejestrowany. Teraz mozesz sie zalogowac</v-alert>
			<v-text-field
				v-model="login.value"
				:rules="login.rules"
				label="Login"
				required
				:disabled="success"
			></v-text-field>
			<v-text-field
				v-model="password.value"
				:rules="password.rules"
				label="Hasło"
				type="password"
				required
				:disabled="success"
			></v-text-field>
			<v-text-field
				v-model="passwordRepeat.value"
				:rules="passwordRepeat.rules"
				label="Powtorz Hasło"
				type="password"
				required
				:disabled="success"
			></v-text-field>
			<v-text-field
				v-model="email.value"
				:rules="email.rules"
				label="E-mail"
				required
				:disabled="success"
			></v-text-field>
			<v-btn
				:disabled="!valid || loading || success"
				color="success"
				class="mr-4"
				@click="validate"
				:loading="loading"
			>Zarejestruj</v-btn>
		</v-form>
	</div>
</template>

<script>

export default {
	data: () => ({
		valid: true,
		loading: false,
		error: false,
		success: false,
		login:{
			value: "",
			rules: [
				v => !!v || "Login jest wymagany",
				v =>(v && v.length >= 5) ||
					"Login powinien mieć co najmniej 5 znaków",
				v =>
					(v && v.length <= 10) ||
					"Login musi wynosic maksymalnie 10 znaków"
			]
		},
		password:{
			value: "",
			rules: [
				v => !!v || "Hasło jest wymagane",
				v =>
					(v && v.length >= 5) ||
					"Hasło powinno mieć co najmniej  5 znakow"
			]
		},
		passwordRepeat:{
			value: "",
			rules: [
				v => !!v || "Musisz powtórzyć haslo",
				v =>
					(v && v === this.password.value) || "Hasła nie są identyczne"
			]
		},
		email:{
			value: "",
			rules: [
				v => !!v || "E-mail jest wymagany",
				v =>
					/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(v) ||
					"Wprowadź poprawny adres E-mail"
			]
		},
  }),
  methods:{
	  	validate() {
			if (this.$refs.form.validate()) {
				this.loading = true;
				this.error = false;
				this.success = false;
				// api.post<api.Response, api.Register>("api/register.php", {
				// 	login: this.login.value,
				// 	password: this.password.value,
				// 	email: this.email.value
				// })
				// 	.then(res => {
				// 		this.success = true;
				// 	})
				// 	.catch(err => {
				// 		this.error = true;
				// 		console.log(err.message);
				// 	})
				// 	.finally(() => {
				// 		this.loading = false;
				// 	});
			}
		}
  }
};
</script>
