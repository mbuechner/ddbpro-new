module.exports = {
  presets: [
    ['@vue/app',
      {
        polyfills: [
          'es6.promise',
          'es7.object.entries'
        ]
      }]
  ]
}
