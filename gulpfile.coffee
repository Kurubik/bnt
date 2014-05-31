path       = require 'path'
gulp       = require 'gulp'
gutil      = require 'gulp-util'
jade       = require 'gulp-jade'
stylus     = require 'gulp-stylus'
CSSmin     = require 'gulp-minify-css'
browserify = require 'browserify'
watchify   = require 'watchify'
source     = require 'vinyl-source-stream'
streamify  = require 'gulp-streamify'
rename     = require 'gulp-rename'
uglify     = require 'gulp-uglify'
coffeeify  = require 'coffeeify'
ecstatic   = require 'ecstatic'
plumber    = require 'gulp-plumber'
concat     = require 'gulp-concat'
prefix     = require 'gulp-autoprefixer'

production = process.env.NODE_ENV is 'production'


paths =
  scripts:
    source: './src/coffee/main.coffee'
    destination: './public/js/'
    filename: 'project.js'
    all : './src/coffee/*.*'
    all_files: 'project-all.js'

  templates:
    source: './src/jade/*.jade'
    watch: './src/jade/*.jade'
    destination: './public/'

  styles:
    source: './src/styles/index.styl'
    watch: './src/styles/*.styl'
    destination: './public/css/'

  assets:
    source: './src/assets/**/*.*'
    watch: './src/assets/**/*.*'
    destination: './public/'



handleError = (err) ->
  gutil.log err
  gutil.beep()
  this.emit 'end'



gulp.task 'scripts', ->

  bundle = browserify
    entries: [paths.scripts.source]
    extensions: ['.coffee']

  build = bundle.bundle(debug: not production)
  .on 'error', handleError
  .pipe source paths.scripts.filename

  build.pipe(streamify(uglify())) if production

  build
  .pipe gulp.dest paths.scripts.destination


gulp.task 'concat', ->
  gulp
  .src(paths.scripts.destination + '/*.js')
  .pipe(streamify(uglify()))
  .pipe(concat(paths.scripts.all_files))
  .pipe(gulp.dest(paths.scripts.destination))


gulp.task 'templates', ->
  gulp
  .src paths.templates.source
  .pipe(jade(
      pretty: not production
    ))
  .on 'error', handleError
  .pipe gulp.dest paths.templates.destination



gulp.task 'styles', ->
  styles = gulp
  .src paths.styles.source
  .pipe(stylus({set: ['include css']}))
  .on 'error', handleError
  .pipe prefix 'last 2 versions', 'Chrome 34', 'Firefox 28', 'iOS 7'

  styles = styles.pipe(CSSmin()) if production
  styles.pipe gulp.dest paths.styles.destination


gulp.task 'assets', ->
  gulp
  .src paths.assets.source
  .pipe gulp.dest paths.assets.destination


gulp.task "watch", ->

  gulp.watch paths.styles.watch, ['styles']
  gulp.watch paths.assets.watch, ['assets']

  bundle = watchify
    entries: [paths.scripts.source]
    extensions: ['.coffee']

  bundle.on 'update', ->
    build = bundle.bundle(debug: not production)
    .on 'error', handleError

    .pipe source paths.scripts.filename

    build
    .pipe gulp.dest paths.scripts.destination

  .emit 'update'

gulp.task "build", ['scripts', 'styles', 'assets', 'concat']
gulp.task "default", ["build", "watch"]