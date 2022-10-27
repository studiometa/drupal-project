import { defineConfig } from '@studiometa/webpack-config';
import { tailwindcss, eslint, stylelint } from '@studiometa/webpack-config/presets';

/**
 * Meta builder config
 * @see https://github.com/studiometa/webpack-config#readme
 */
export default defineConfig({
  src: [
    './web/themes/custom/studiometa/src/js/app.js',
    './web/themes/custom/studiometa/src/css/**/[!_]*.scss',
  ],
  dist: './web/themes/custom/studiometa/dist/',
  public: '/themes/custom/studiometa/dist/',
  watch: [
    './web/themes/custom/studiometa/templates/**/*.twig',
    './web/themes/custom/studiometa/src/js/**/*.vue',
  ],
  presets: [tailwindcss(), eslint(), stylelint()],
});
