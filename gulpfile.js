import gulp from 'gulp';
import dartSass from 'sass';
import gulpSass from 'gulp-sass';
import autoprefixer from 'gulp-autoprefixer';
import cleanCSS from 'gulp-clean-css';
import sourcemaps from 'gulp-sourcemaps';
import concat from 'gulp-concat';
import uglify from 'gulp-uglify';
import plumber from 'gulp-plumber';

const sass = gulpSass(dartSass);

const paths = {
    scss: {
        src: './sass/main.scss',
        dest: './assets/css',
    },
    js: {
        src: './components/**/*.js', // Todos os arquivos JS dentro de /components e subpastas
        dest: './assets/js',        // Diretório de destino para o JS unificado
    },
};
// Tarefa para compilar SCSS em CSS
export function scss() {
    return gulp.src(paths.scss.src)
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(cleanCSS())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(paths.scss.dest));
}

// Tarefa para processar e unificar arquivos JS
export function js() {
    return gulp.src(paths.js.src) // Lê todos os arquivos JS em /components
        .pipe(plumber()) // Evita que erros interrompam o fluxo
        .pipe(sourcemaps.init()) // Inicia o sourcemap para debugging
        .pipe(concat('main.min.js')) // Une todos os arquivos em um único arquivo
        .pipe(uglify()) // Minifica o JS
        .pipe(sourcemaps.write('.')) // Gera o sourcemap
        .pipe(gulp.dest(paths.js.dest)); // Salva o resultado em /assets/js
}

// Tarefa para observar mudanças em SCSS e JS
export function watchFiles() {
    gulp.watch(['./sass/**/*.scss', './components/**/*.scss'], scss);
    gulp.watch(paths.js.src, js); // Observa mudanças nos arquivos JS
}

// Tarefa padrão
export default gulp.series(gulp.parallel(scss, js), watchFiles);
