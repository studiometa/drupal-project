const defaultTheme = require('tailwindcss/defaultTheme');
const { config } = require('@studiometa/tailwind-config');

module.exports = {
  presets: [config],
  theme: {},
  corePlugins: {
    container: false,
  },
  content: [
    './web/themes/custom/studiometa/templates/**/*.twig',
    './web/themes/custom/studiometa/static/svg/**/*.svg',
    './web/themes/custom/studiometa/src/js/**/*.vue',
    './web/themes/custom/studiometa/src/js/**/*.js',
    './web/themes/custom/studiometa/src/js/**/*.vue',
    './vendor/studiometa/ui/packages/ui/**/*.twig',
    './vendor/studiometa/ui/packages/ui/**/*.js',
  ],
};
