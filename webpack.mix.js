/**
 * Laravel mix version 6
 * https://laravel-mix.com/docs
 * https://github.com/JeffreyWay/laravel-mix/blob/628f6062cceb77610b1813e3179abcbd043a4642/UPGRADE.md#update-your-npm-scripts
 * @type {mix.Api}
 */
let mix = require('laravel-mix');

/**
 * https://www.npmjs.com/package/eslint-webpack-plugin
 */
const ESLintPlugin = require('eslint-webpack-plugin');

/**
 *
 */
const StylelintPlugin = require('stylelint-webpack-plugin');

/**
 *
 */
const path = require('path');

/**
 *
 */
const glob = require('glob');

/**
 * https://laravel-mix.com/docs/6.0/api#optionsoptions
 */
mix.options({
  processCssUrls: false,
  legacyNodePolyfills: true
});

/**
 * https://laravel-mix.com/docs/6.0/os-notifications
 */
mix.disableNotifications();

/**
 * https://laravel-mix.com/docs/6.0/quick-webpack-configuration#basic-usage
 */
mix.webpackConfig({
  plugins: [
    new ESLintPlugin({
      overrideConfigFile: 'eslintrc.json',
      failOnError: false
    }),
    new StylelintPlugin({
      configFile: 'stylelintrc.json',
      files: [
        './assets/scss/**/*.scss',
        './blocks/**/*.scss',
        './patterns/**/*.scss',
      ]
    })
  ],
  stats: {
    children: true
  }
});

/**
 * https://snyk.io/advisor/npm-package/laravel-mix/functions/laravel-mix.inProduction
 */
if (!mix.inProduction()) {
  mix
    .sourceMaps()
    .webpackConfig({devtool: 'inline-source-map'});
}

const distPath = 'assets/dist';

mix
  // Blocks common scripts
  .js('blocks/index.js', distPath + '/js/editor.blocks.js')
  .js('blocks/frontend.js', distPath + '/js/blocks-frontend-common.script.js')

mix
  // Blocks common styles
  .sass('blocks/style.scss', distPath + '/css/blocks-common.style.css')
  .sass('blocks/editor.scss', distPath + '/css/editor-common.style.css')

/**
 * Read the blocks dir and look for files
 */
let dir = path.resolve();
const blocksPath = dir.replace(/\\/g, '/') + '/blocks/src/';

// Scan all blocks styles in blocks folder
const stylesArray = glob.sync(blocksPath + '**/src/style.scss');

// Scan all blocks editor styles in blocks folder
const editorStylesArray = glob.sync(blocksPath + '**/src/editor.scss');

// Scan all blocks view scripts in blocks folder
const viewScriptsArray = glob.sync(blocksPath + '**/src/view.js');

// Blocks styles processing
stylesArray.forEach(function (stylePath) {
  mix.sass(stylePath, stylePath.replace('/src/style.scss', '/build/style.css'));
});

// Blocks editor styles processing
editorStylesArray.forEach(function (editorPath) {
  mix.sass(editorPath, editorPath.replace('/src/editor.scss', '/build/editor.style.css'));

  return editorPath;
});

// Blocks frontend scripts processing
viewScriptsArray.forEach(function (scriptPath) {
  mix.js(scriptPath, scriptPath.replace('/src/view.js', '/build/view.script.js'));

  return scriptPath;
});

/**
 * Read the patterns dir and look for files
 */
const patternsPath = dir.replace(/\\/g, '/') + '/patterns/',
  patternsStylesArray = glob.sync(patternsPath + '**/src/*.scss'),
  patternsScriptsArray = glob.sync(patternsPath + '**/src/*.js');

// ToDo update regexps - read only [a-z0-9-].xxx files
patternsStylesArray.forEach(function (stylePath) {
  mix.sass(stylePath, stylePath.replace(/src\/(.*?)\.scss/g, "/build/$1.css"));
});
patternsScriptsArray.forEach(function (scriptPath) {
  mix.js(scriptPath, scriptPath.replace(/src\/(.*?)\.js/g, "/build/$1.js"));

  return scriptPath;
});
