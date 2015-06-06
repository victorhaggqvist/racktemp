/*global module:false*/
module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    // Metadata.
    pkg: grunt.file.readJSON('package.json'),
    banner: '/*! <%= pkg.title || pkg.name %> - v<%= pkg.version %> - ' +
      '<%= grunt.template.today("yyyy-mm-dd") %>\n' +
      '<%= pkg.homepage ? "* " + pkg.homepage + "\\n" : "" %>' +
      '* Copyright (c) <%= grunt.template.today("yyyy") %> <%= pkg.author.name %>;' +
      ' Licensed <%= _.pluck(pkg.licenses, "type").join(", ") %> */\n',
    // Task configuration.
    concat: {
      options: {
        banner: '<%= banner %>',
        stripBanners: true
      },
      css: {
        src: 'build/*.css',
        dest: 'app/Resources/public/css/racktemp.min.css'
      }
    },
    uglify: {
      js_dev: {
        options: {
          beautify: true,
          mangle: false
        },
        src: 'js/RackTemp.js',
        dest: 'app/Resources/public/js/racktemp.min.js'
      },
      js: {
        options: {
          stripBanners: true
        },
        src: 'js/RackTemp.js',
        dest: 'app/Resources/public/js/racktemp.min.js'
      },
      bootstrap: {
        options: {
          stripBanners: true
        },
        src: 'bower_components/bootstrap-sass-twbs/assets/javascripts/bootstrap.js',
        dest: 'app/Resources/public/js/bootstrap.min.js'
      },
      jquery: {
        options: {
          stripBanners: true
        },
        src: 'bower_components/jquery/dist/jquery.js',
        dest: 'app/Resources/public/js/jquery.min.js'
      }
    },
    jshint: {
      options: {
        eqeqeq: true,
        immed: true,
        latedef: true,
        newcap: true,
        noarg: true,
        sub: true,
        undef: true,
        unused: true,
        boss: true,
        eqnull: true,
        browser: true,
        indent: 2,
        globals: {
          jQuery: true,
          $: true
        }
      },
      gruntfile: {
        src: 'Gruntfile.js'
      },
      main: {
        src: ['js/**/*.js']
      }
    },
    watch: {
      gruntfile: {
        files: '<%= jshint.gruntfile.src %>',
        tasks: ['jshint:gruntfile']
      },
      racktemp: {
        files: ['**/*.php','js/*.js','sass/*'],
        tasks: ['jshint:main','uglify:js_dev', 'phplint','sass:racktemp', 'concat:css'],
        options: {
          livereload: true
        }
      }
    },
    copy: {
      d3: {
        src: 'bower_components/d3/d3.min.js',
        dest:'app/Resources/public/js/d3.min.js'
      },
      c3: {
        src: 'bower_components/c3/c3.min.js',
        dest:'app/Resources/public/js/c3.min.js'
      },
      bootstrap_fonts: {
        expand: true,
        flatten: true,
        src: 'bower_components/bootstrap-sass-twbs/assets/fonts/bootstrap/*',
        dest:'app/Resources/public/css/'
      }
    },
    sass: {
      bootstrap: {
        options: {
          style: 'compressed',
          loadPath: 'bower_components/bootstrap-sass-twbs/assets/stylesheets'
        },
        files: {
          'build/bootstrap.min.css': 'sass/bootstrap.scss'
        }
      },
      racktemp: {
        options: {
          style: 'compressed'
        },
        files: {
          'build/racktemp.min.css': 'sass/racktemp.scss'
        }
      }
    },
    phplint: {
      main: ['php/**/*.php', 'app/**/*.php']
    },
    cssmin: {
      c3: {
        files: {
          'build/c3.min.css': 'bower_components/c3/c3.css'
        }
      }
    },
    clean: ["build", "app/Resources/public/js", "app/Resources/public/css"]
  });

  // load plugs
  grunt.loadNpmTasks("grunt-notify");
  grunt.loadNpmTasks("grunt-phplint");
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');


  grunt.registerTask('jsdist', ['uglify:js', 'uglify:bootstrap', 'uglify:jquery']);
  grunt.registerTask('default', ['sass', 'cssmin', 'jshint', 'copy', 'jsdist', 'concat', 'phplint']);
  grunt.registerTask('build',['default']);
  grunt.registerTask('cleanbuild',['clean', 'default']);

};
