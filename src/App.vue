<template>
	<v-app>
		<v-navigation-drawer app v-model="navigation">
			<v-list dense nav>
				<v-list-item v-for="item in menu" :key="item.title" link :to="item.link">
					<v-list-item-icon>
						<v-icon>{{ item.icon }}</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>{{ item.title }}</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
			</v-list>
		</v-navigation-drawer>

		<v-app-bar app>
			<v-btn text icon @click="navigation = !navigation">
				<v-icon>mdi-menu</v-icon>
			</v-btn>
		</v-app-bar>

		<!-- Sizes your content based upon application components -->
		<v-content>
			<!-- Provides the application the proper gutter -->
			<v-container fluid class="hhh">
				<!-- If using vue-router -->
				<router-view></router-view>
			</v-container>
		</v-content>

		<v-footer app>
			<span>Made with:</span>
			<a href="https://vuejs.org/">
				<v-icon>mdi-vuejs</v-icon>Vue.js
			</a>
			<a href="https://vuetifyjs.com">
				<v-icon>mdi-vuetify</v-icon>Vuetify
			</a>
			<a href="https://www.typescriptlang.org/">
				<v-icon>mdi-language-typescript</v-icon>Typescript
			</a>
		</v-footer>
	</v-app>
</template>


<style lang="scss">
	// @import "@/styles/col"
	@import "@/styles/colors.scss";
	a {
		text-decoration: none;
		color: $primary !important;
		// &.router-link-exact-active {
		// 	color: $accent !important;
		// }
		&:hover {
			color: $accent !important;
		}
	}
	.hhh {
		height: 100%;
	}
</style>

<script>
	// import { mapState } from "vuex";

	export default {
		name: "App",
		data: () => ({
			navigation: true
		}),
		computed: {
			// ...mapState(["login"]),
			menu() {
				let items = [
					{ title: "Home", icon: "mdi-home", link: "/" },
					{ title: "Rejestracja", icon: "mdi-key", link: "/register" },
					{ title: "Jak grać?", icon: "mdi-dice-multiple", link: "/help" }
				];
				if (this.$store.state["userData"]['login'] !== "")
					items = [
						...items,
						{ title: "Pokoje", icon: "mdi-door", link: "/rooms" }
					];
					
				return items;
			}
		}
	};
</script>
