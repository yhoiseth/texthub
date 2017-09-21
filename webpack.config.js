var Encore = require('@symfony/webpack-encore');

Encore
  .setOutputPath('web/build')
  .setPublicPath('/build')
  .cleanupOutputBeforeBuild()
  .addEntry(
    'app',
    './web/assets/js/app.js'
  )
  .addStyleEntry(
    'global',
    './web/assets/css/global.scss'
  )
  .enableSassLoader()
  .autoProvidejQuery()
  .enableSourceMaps(!Encore.isProduction())
;

module.exports = Encore.getWebpackConfig();
