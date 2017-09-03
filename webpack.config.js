var Encore = require('@symfony/webpack-encore');

Encore
  .setOutputPath('web/build')
  .setPublicPath('/build')
  .cleanupOutputBeforeBuild()
  .addEntry(
    'app',
    './web/assets/js/main.js'
  )
  .addEntry(
    'app_text_edit',
    './web/assets/js/text/edit.js'
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
