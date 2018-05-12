const path = require('path');
const webpack = require('webpack');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const StyleLintPlugin = require('stylelint-webpack-plugin');

const tsConfigFilePath = path.resolve(__dirname, 'tsconfig.json');
const generatedDirectory = 'Resources/Public/Generated/';
// const stylelintOptions = require('./stylelint.config.js');
const stylelintOptions = {
  configFile: 'stylelint.config.js',
  failOnError: false,
  syntax: 'scss',
  // lintDirtyModulesOnly: true,
  files: './Resources/Public/Css/Sass/**/*.scss',
};

module.exports = {
  entry: [
    './Resources/Private/TypeScript/index.ts',
    './Resources/Public/Sass/main.scss',
  ],
  devtool: 'source-map',
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: ExtractTextPlugin.extract({
          use: [
            {
              loader: 'css-loader',
              options: {
                sourceMap: true,
                importLoaders: 1,
              }
            },
            {
              loader: 'postcss-loader',
              options: {
                sourceMap: true,
                plugins: [
                  require('postcss-sorting')({
                    'sort-order': 'default',
                    'empty-lines-between-children-rules': 0,
                    'empty-lines-between-media-rules': 0,
                    'preserve-empty-lines-between-children-rules': false
                  }),
                  require('autoprefixer')(),
                  require('cssnano')(),
                ],
              },
            },
            {
              loader: 'sass-loader',
              options: {
                sourceMap: true,
              },
            },
            // {
            //   loader: 'postcss-loader',
            //   options: {
            //     parser: 'postcss-scss',
            //     plugins: [
            //       require('stylelint')(stylelintOptions),
            //       require('postcss-reporter')({
            //         clearMessages: true,
            //         throwError: true
            //       }),
            //     ]
            //   },
            // },
          ],
        }),
      },
      {
        test: /\.tsx?$/,
        enforce: 'pre',
        loader: 'tslint-loader',
        options: {
          configFile: path.resolve(__dirname, 'tslint.json'),
          tsConfigFile: tsConfigFilePath,
          formatter: 'codeFrame',
        },
        include: [
          path.resolve(__dirname, "Resources/Private/TypeScript/"),
        ],
      },
      {
        test: /\.tsx?$/,
        loader: 'ts-loader',
        options: {
          configFile: tsConfigFilePath,
          onlyCompileBundledFiles: true,
        },
      },
    ],
  },
  resolve: {
    extensions: [".tsx", ".ts", ".js"],
  },
  output: {
    filename: 'bundle.min.js',
    chunkFilename: '[name].bundle.min.js?bust=[chunkhash]',
    path: path.resolve(__dirname, generatedDirectory),
    publicPath: generatedDirectory,
  },
  plugins: [
    new webpack.optimize.UglifyJsPlugin({
      sourceMap: true,
      minimize: true,
      output: {
        comments: false,
      },
      parallel: true,
    }),
    // new CopyWebpackPlugin([
    //   {
    //     from: 'node_modules/jquery/dist',
    //     to: 'vendor/jquery',
    //   },
    // ]),
    new ExtractTextPlugin({
      filename: "bundle.min.css?bust=[chunkhash]",
      disable: false,
      allChunks: true
    }),
    new StyleLintPlugin(stylelintOptions),
  ],
  // externals: {
  //   jquery: 'jQuery',
  // },
};
