path       = require 'path'
gulp       = require 'gulp'
gutil      = require 'gulp-util'
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
    source: './app/src/coffee/index.coffee'
    destination: './public/js/'
    filename: 'project.js'
    all : './app/src/coffee/*.*'
    all_files: 'project.min.js'

  templates:
    source: './app/src/jade/*.jade'
    watch: './app/src/jade/*.jade'
    destination: './public/templates/'

  styles:
    source: './app/src/stylus/*.styl'
    watch: './app/src/stylus/**/*.styl'
    destination: './public/css/'
    min: './public/css/index.css'

  assets:
    source: './app/src/assets/**/*.*'
    watch: './app/src/assets/**/*.*'
    destination: './public/assets/'

  js:
    source: './app/src/js/**/*.*'
    watch: './app/src/js/**/*.*'
    destination: './public/js/'

  images:
    source: './src/assets/images/background/resize/*.jpg'
    destination: './public/images/resize/mobile/'
    width: 640
    height: 475

  imagesMin:
    source: './public/images/resize/*'
    destination: './public/images/resize'

  concat:
    scripts: ['']
    destination: './public/min/js/'


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



gulp.task 'js', ->
  gulp
  .src paths.js.source
  .pipe gulp.dest paths.js.destination


gulp.task "watch", ->

  gulp.watch paths.styles.watch, ['styles']
  gulp.watch paths.assets.watch, ['assets']
  gulp.watch paths.assets.watch, ['js']

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

gulp.task "build", ['scripts', 'styles', 'assets', 'js']
gulp.task "default", ["build", "watch"]