const path = require('path');

module.exports = {
  mode: 'production', // or 'development'
  entry: './spa/public/front.jsx', // make sure this path is correct
  output: {
    filename: 'main.js',
    path: path.resolve(__dirname, 'build'),
  },
  module: {
    rules: [
      {
        test: /\.jsx?$/, // handle .js and .jsx
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: [
              '@babel/preset-env',
              '@babel/preset-react'
            ]
          }
        }
      }
    ]
  },
  resolve: {
    extensions: ['.js', '.jsx'] // so imports work without extensions
  }
};
