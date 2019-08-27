import Vue from 'vue';
import Vuetify from 'vuetify/lib';
import pl from 'vuetify/es5/locale/pl';
import colors from 'vuetify/lib/util/colors'

Vue.use(Vuetify);

export default new Vuetify({
  theme: {
      options: {
        customProperties: true,
      },
    themes: {
      light: {
		primary: colors.deepPurple.base,
		secondary: colors.indigo.darken4,
		accent: colors.orange.base,
		error: colors.red.accent2,
		info: colors.blue.base,
		success: colors.green.base,
		warning: colors.amber.base,
      },
    },
  },
    lang: {
      locales: { pl },
      current: 'pl',
    },
  icons: {
    iconfont: 'mdi',
  },
});
