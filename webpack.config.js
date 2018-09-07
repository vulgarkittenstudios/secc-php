const webpack = require('webpack');
const modules = process.env.NODE_PATH;

module.exports = {
  
  mode: 'development',
  resolve: {
     modules: [modules],
     descriptionFiles: ['package.json'],
     extensions: ['.js', '.json', '.html'],
     alias: {
      fs: require.resolve('browserify-fs')
    }
  },
  resolveLoader: {
    modules: [modules],
    extensions: ['.js', '.json'],
    mainFields: ['loader', 'main']
  },
  context: __dirname,
  entry: __dirname+"/public/src/main.js",
  output: {
    path: __dirname+"/public/dist/js",
    filename: 'bundle.js'
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules|bower_components)/,
        use: {
         loader: 'babel-loader',
         options: {
          presets: ['@babel/preset-env']
         }
        }
       }
     ]
   }
};