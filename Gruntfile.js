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
    // concat: {
    //   options: {
    //     banner: '<%= banner %>',
    //     stripBanners: true
    //   },
    //   dist: {
    //     src: ['js/*.js'],
    //     dest: 'app/js/racktemp.js'
    //   }
    // },
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
        files: ['app/**/*.php','js/*.js','sass/*'],
        tasks: ['jshint:main','copy:js', 'phplint','sass:racktemp'],
        options: {
          livereload: true
        }
      }
    },
    copy: {
      d3: {
        src: '<%= pkg.bwr.d3 %>',
        dest:'<%= pkg.appjs %>d3.min.js'
      },
      c3_js: {
        src: '<%= pkg.bwr.c3.js %>',
        dest:'<%= pkg.appjs %>c3.min.js'
      },
      c3_css: {
        src: '<%= pkg.bwr.c3.css %>',
        dest:'<%= pkg.appcss %>c3.css'
      },
      jquery: {
        src: '<%= pkg.bwr.jquery %>',
        dest:'<%= pkg.appjs %>jquery.min.js'
      },
      bootstrap_js: {
        src: '<%= pkg.bwr.bootstrap.js %>',
        dest:'<%= pkg.appjs %>bootstrap.min.js'
      },
      bootstrap_fonts: {
        expand: true,
        flatten: true,
        src: '<%= pkg.bwr.bootstrap.fonts %>',
        dest:'<%= pkg.appcss %>'
      },
      js: {
        expand: true,
        flatten: true,
        src: 'js/*',
        dest: '<%= pkg.appjs %>'
      }
    },
    sass: {
      bootstrap: {
        options: {
          style: 'compressed'
        },
        files: {
          '<%= pkg.appcss %>bootstrap.min.css': 'sass/bootstrap.scss'
        }
      },
      racktemp: {
        options: {
          style: 'compressed'
        },
        files: {
          '<%= pkg.appcss %>racktemp.min.css': 'sass/racktemp.scss'
        }
      }
    },
    phplint: {
      racktemp: ['app/**/*.php']
    }
  });

  // load plugs
  // grunt.loadNpmTasks('grunt-contrib-concat');
  // grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks("grunt-phplint");
  grunt.loadNpmTasks("grunt-notify");


  // grunt.registerTask('default', ['jshint', 'qunit', 'concat', 'uglify']);
  grunt.registerTask('default', 'watch:racktemp');
  grunt.registerTask('build',['phplint','sass','jshint','copy',]);

};
