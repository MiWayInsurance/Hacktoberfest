var Encore = require('@symfony/webpack-encore');
var DashboardPlugin = require('webpack-dashboard/plugin');

Encore
    .setOutputPath('public/assets/')
    .setPublicPath('/assets')
    .cleanupOutputBeforeBuild()
    .addEntry('app', './assets/js/main.js')
    .addStyleEntry('global', './assets/css/global.less')
    .enableLessLoader()
    .enableVueLoader()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabel(function(babelConfig) {
        babelConfig.presets.push('env');
    })
;

let config = Encore.getWebpackConfig();

config.plugins.push(new DashboardPlugin());

module.exports = config;
