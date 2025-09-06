const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  mode: 'production',
  entry: {
    blocks: './src/gutenberg/index.js',
    templates: './src/gutenberg/templates.js',
  },
  output: {
    path: path.resolve(__dirname, 'dist/gutenberg'),
    filename: '[name].js',
    clean: true,
  },
  module: {
    rules: [
      {
        test: /\.(js|jsx|ts|tsx)$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: [
              '@wordpress/babel-preset-default',
              '@babel/preset-typescript',
            ],
          },
        },
      },
      {
        test: /\.scss$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          'postcss-loader',
          'sass-loader',
        ],
      },
      {
        test: /\.css$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          'postcss-loader',
        ],
      },
    ],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: '[name].css',
    }),
  ],
  resolve: {
    extensions: ['.js', '.jsx', '.ts', '.tsx'],
  },
  externals: {
    '@wordpress/blocks': ['wp', 'blocks'],
    '@wordpress/i18n': ['wp', 'i18n'],
    '@wordpress/element': ['wp', 'element'],
    '@wordpress/components': ['wp', 'components'],
    '@wordpress/block-editor': ['wp', 'blockEditor'],
    '@wordpress/editor': ['wp', 'editor'],
    '@wordpress/edit-post': ['wp', 'editPost'],
    '@wordpress/plugins': ['wp', 'plugins'],
    '@wordpress/compose': ['wp', 'compose'],
    '@wordpress/data': ['wp', 'data'],
    '@wordpress/api-fetch': ['wp', 'apiFetch'],
    'react': 'React',
    'react-dom': 'ReactDOM',
    // Note: react-select will be bundled as it's not a WordPress external
  },
  optimization: {
    splitChunks: {
      cacheGroups: {
        editor: {
          name: 'editor',
          test: /editor\.s?css$/,
          chunks: 'all',
          enforce: true,
        },
        style: {
          name: 'style',
          test: /style\.s?css$/,
          chunks: 'all',
          enforce: true,
        },
      },
    },
  },
};
