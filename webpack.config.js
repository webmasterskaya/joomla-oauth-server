const path = require('path');
const babelConfig = require(`./${process.env.BABEL_CONFIG}`);

const production = process.env.NODE_ENV === 'production';
const ES5 = process.env.BABEL_CONFIG.includes('es5');

const TerserPlugin = require('terser-webpack-plugin');

const publicPath = path.join(__dirname, '/com_oauthserver/media');
const sourcePath = path.join(__dirname, '/build/src');

const entry = {
  "field/copy": {
    import: path.join(sourcePath, '/com_oauthserver/js/field/copy.es6'),
    filename: `js/field-copy${ES5 ? '-es5' : ''}${production ? '.min' : ''}.js`
  }
}

const webpackConfig = {
  mode: production ? 'production' : 'development',
  output: {
    path: publicPath,
  },
  entry: entry,
  devtool: false,
  module: {
    rules: [
      {
        test: /\.(es6|js)$/,
        use: {
          loader: 'babel-loader',
          options: babelConfig
        },
      }
    ]
  },
  plugins: [],
  optimization: {
    minimize: production,
    minimizer: production ? [
      new TerserPlugin({
        test: /\.js(\?.*)?$/i,
        parallel: true,
        terserOptions: {
          compress: {
            pure_getters: true,
            unsafe_comps: true,
            unsafe: true,
            passes: 2,
            keep_fargs: false,
            drop_console: true
          },
          output: {
            beautify: false,
            comments: false,
          },
        },
        extractComments: false,
      }),
    ] : []
  }
}

module.exports = webpackConfig;
