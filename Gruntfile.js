/*
 * Parameters
 *
 * dev (optional): If true, the result is not minified or gziped (much faster)
 * Example: Enable dev mode and render my css
 * grunt watch --dev=true --render=css
 *
 * render (optional): Render js, css or both (js,css)
 * Example: Render German JavaScript for development
 * grunt watch --render=js
 * grunt watch --render=css
 *
 * You can minify images with grunt imagemin
 *
 */
var loadGruntTasks = require('load-grunt-tasks');
var sorting = require('postcss-sorting');
var autoprefixer = require('autoprefixer');
var cssnano = require('cssnano');

module.exports = function (grunt) {
  var jsFilesToIgnore = [];

  var jsFilesToUse = [];

  var jsFilesToIgnoreNegated = [];
  for (var i = 0; i < jsFilesToIgnore.length; i++) {
    jsFilesToIgnoreNegated.push('!' + jsFilesToIgnore[i]);
  }

  // render
  var renderParts = {};
  var tmpRenderParts = ['js', 'css'];
  if (typeof grunt.option('render') === 'string') {
    tmpRenderParts = grunt.option('render').split(',');
  }

  for (var j = 0; j < tmpRenderParts.length; j++) {
    renderParts[tmpRenderParts[j]] = 1;
  }

  // dev mode (boolean value)
  var devMode = (
    grunt.option('dev') === true ||
    (
      typeof grunt.option('dev') === 'string' &&
      (grunt.option('dev') === 'true' || grunt.option('dev') === '1')
    )
  );

  // define main grunt config
  var gruntConfig = {
    pkg: grunt.file.readJSON('package.json'),

    // https://www.npmjs.org/package/grunt-contrib-compress
    compress: {},

    // https://github.com/gruntjs/grunt-contrib-watch
    watch: {}
  };

  var compressOptions = {
    mode: 'gzip',
    level: devMode ? 1 : 9
  };

  // Render JavaScript
  if (renderParts.js === 1) {
    // https://github.com/gruntjs/grunt-eslint
    gruntConfig.eslint = {
      options: {
        format: './node_modules/eslint-friendly-formatter'
      },
      target: jsFilesToUse.concat(jsFilesToIgnoreNegated).concat('Gruntfile.js')
    };
    if (grunt.option('verbose')) {
      gruntConfig.eslint.options.format = './node_modules/eslint-onelineperfile/onelineperfile.js';
    }

    // https://github.com/gruntjs/grunt-contrib-uglify
    gruntConfig.uglify = {
      options: {
        sourceMap: true,
        preserveComments: /(?:^!|@(?:license|preserve|cc_on))/,
        mangle: true,
        compress: {
          warnings: false
        }
      },
      aus_project: {
        src: jsFilesToUse,
        dest: 'typo3conf/ext/aus_project/Resources/Public/Generated/main.min.js'
      }
    };

    // https://www.npmjs.org/package/grunt-contrib-compress
    gruntConfig.compress.javascript = {
      options: compressOptions,
      files: {
        'typo3conf/ext/aus_project/Resources/Public/Generated/main.min.js.gz': 'typo3conf/ext/aus_project/Resources/Public/Generated/main.min.js'
      }
    };

    // https://github.com/gruntjs/grunt-contrib-watch
    gruntConfig.watch.uglify = {
      files: jsFilesToUse.concat([
        'typo3conf/ext/**/Resources/Public/JavaScript/*.js',
        'typo3conf/ext/**/Resources/Public/JavaScript/**/*.js'
      ]),
      tasks: ['eslint', 'uglify', 'compress']
    };

    if (devMode) {
      gruntConfig.uglify.options.preserveComments = 'all';
      gruntConfig.uglify.options.mangle = false;
      gruntConfig.uglify.options.compress = false;
      gruntConfig.uglify.options.beautify = true;
    }
  }

  // Render CSS
  if (renderParts.css === 1) {
    // https://github.com/wikimedia/grunt-stylelint
    // use grunt stylelint -v for better overview of stylelint errors.
    gruntConfig.stylelint = {
      options: {
        configFile: 'stylelint.config.js'
      },
      src: 'Resources/Public/Sass/**/*.scss'
    };

    // https://github.com/sindresorhus/grunt-sass
    gruntConfig.sass = {
      options: {
        sourceMap: false,
        outputStyle: 'nested'
      },
      dist: {
        files: {
          'Resources/Public/Generated/main.css': 'Resources/Public/Sass/Main.scss'
        }
      }
    };

    // https://github.com/nDmitry/grunt-postcss
    gruntConfig.postcss = {
      options: {
        map: {
          inline: false
        },

        processors: [

          // https://github.com/hudochenkov/postcss-sorting
          sorting({
            'sort-order': 'default',
            'empty-lines-between-children-rules': 0,
            'empty-lines-between-media-rules': 0,
            'preserve-empty-lines-between-children-rules': false
          }),

          // https://github.com/postcss/autoprefixer
          autoprefixer({
            browsers: 'last 2 versions'
          }),

          // https://www.npmjs.com/package/cssnano
          cssnano({
            sourcemap: true
          })
        ]
      },
      dist: {
        files: {
          'Resources/Public/Generated/main.min.css': 'Resources/Public/Generated/main.css'
        }
      }
    };

    // https://www.npmjs.org/package/grunt-contrib-compress
    gruntConfig.compress.css = {
      options: compressOptions,
      files: {
        'Resources/Public/Generated/main.min.css.gz': 'Resources/Public/Generated/main.min.css'
      }
    };

    // https://github.com/gruntjs/grunt-contrib-watch
    gruntConfig.watch.sass = {
      files: [
        'Resources/Public/Sass/*.scss',
        'Resources/Public/Sass/**/*.scss'
      ],
      tasks: ['stylelint', 'sass', 'postcss', 'compress'],
      options: {
        livereload: true
      }
    };

    if (devMode) {
      // gruntConfig.sass.dist.options.style = 'nested';
      gruntConfig.sass.dist.noCache = false;
      gruntConfig.sass.dist.update = true;
    }
  }

  gruntConfig.imagemin = {
    aus_project: {
      files: [{
        expand: true,
        cwd: 'Resources/Public/',
        src: [
          'Icons/**/*.{png,jpg,gif}',
          'Images/**/*.{png,jpg,gif}'
        ],
        dest: 'Resources/Public/'
      }]
    }
    /*     , fileadmin: {
     files: [{
     expand: true,
     cwd: 'fileadmin',
     src: [
     'swg.com/media/!**!/!*.{png,jpg,gif}'
     ],
     dest: 'fileadmin/'
     }]
     } */
  };

  // NO CHANGES FROM HERE!
  grunt.initConfig(gruntConfig);

  // https://github.com/sindresorhus/load-grunt-tasks
  loadGruntTasks(grunt);

  var subTasks = [];

  if (renderParts.js === 1) {
    subTasks.push('eslint');
    subTasks.push('uglify');
  }

  if (renderParts.css === 1) {
    subTasks.push('stylelint');
    subTasks.push('sass');
    subTasks.push('postcss');
  }

  if ({}.hasOwnProperty.call(gruntConfig.compress, 'javascript') || {}.hasOwnProperty.call(gruntConfig.compress, 'css')) {
    subTasks.push('compress');
  }

  grunt.registerTask('default', subTasks);
};
