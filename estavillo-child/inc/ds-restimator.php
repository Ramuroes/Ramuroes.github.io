<?php
/**
 * REstimator Design System — integración en el portfolio.
 *
 * El documento del Design System NO vive en post_content: son ~130 KB de
 * markup muy específico (rail de navegación, 11 secciones, tablas densas,
 * demos de componentes hechas con HTML+CSS) que Gutenberg no puede editar sin
 * romper, y que además tiene que poder REGENERARSE cuando el Design System se
 * actualice. Vive como partial del tema, generado por tools/build-ds.mjs a
 * partir de la fuente vendorizada en docs/ds-src/restimator/.
 *
 * La página en sí es una Página común de WordPress con el template
 * templates/page-restimator-ds.php — el mismo patrón "standalone" que ya usan
 * Home, Work, About, How I Work y Contact. Eso deja la ruta
 * (/lab/restimator-design-system/) y la traducción a Polylang en manos de
 * WordPress, sin rewrite rules ni endpoints propios.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Template de la página del Design System. Constante para que enqueue.php y
 * el propio template no repitan la ruta como string suelto.
 */
const ES_DS_RESTIMATOR_TEMPLATE = 'templates/page-restimator-ds.php';

/** Directorio de los partials del documento (generados por tools/build-ds.mjs). */
define( 'ES_DS_RESTIMATOR_DIR', ES_CHILD_DIR . '/ds/restimator/' );

/**
 * ¿Esta request es la página del REstimator Design System?
 *
 * @return bool
 */
function es_is_ds_restimator_page() {
	return is_page_template( ES_DS_RESTIMATOR_TEMPLATE );
}

/**
 * URI base de las capturas de pantalla del DS.
 *
 * Se construye con ES_CHILD_URI (no con rutas absolutas ni relativas al
 * documento): así funciona igual en cualquier dominio, subdirectorio o
 * entorno, que es justo lo que fallaba en el archivo original — sus enlaces
 * eran relativos a la carpeta del proyecto en disco.
 *
 * @return string URI con barra final.
 */
function es_ds_restimator_screens_uri() {
	return ES_CHILD_URI . '/assets/ds/restimator/screens/';
}

/**
 * Idioma efectivo del documento del DS.
 *
 * Devuelve el código de idioma para el que EXISTE un partial. Hoy sólo existe
 * el español: el contenido del Design System está escrito en español y no se
 * inventan traducciones. La arquitectura ya soporta el inglés — alcanza con
 * dejar ds/restimator/master-en.php y esta función lo toma sola.
 *
 * El chrome del portfolio (barra superior, labels del visor) SÍ se traduce:
 * son strings del tema, registrados en Polylang por es_child_ui_strings().
 *
 * @return string 'es' | 'en'
 */
function es_ds_restimator_lang() {
	$lang = function_exists( 'pll_current_language' ) ? (string) pll_current_language() : '';
	if ( '' === $lang ) {
		$lang = substr( (string) get_locale(), 0, 2 );
	}
	if ( 'es' !== $lang && file_exists( ES_DS_RESTIMATOR_DIR . "master-{$lang}.php" ) ) {
		return $lang;
	}
	return 'es';
}

/**
 * Imprime el documento del Design System.
 *
 * El partial usa $es_ds_screens para construir las URLs de las capturas y
 * es__() para las labels del visor, así que las dos cosas tienen que existir
 * en el scope antes del include.
 *
 * @return bool true si se imprimió algo.
 */
function es_ds_restimator_render_document() {
	$es_ds_screens = es_ds_restimator_screens_uri(); // phpcs:ignore VariableAnalysis -- lo usa el partial incluido abajo.
	$file          = ES_DS_RESTIMATOR_DIR . 'master-' . es_ds_restimator_lang() . '.php';

	if ( ! file_exists( $file ) ) {
		return false;
	}
	require $file;
	return true;
}

/**
 * URL del Case Study de REstimator, para el "volver" de la barra superior.
 *
 * Se resuelve por slug contra el CPT del plugin, probando los slugs reales que
 * usa el proyecto (el caso se publicó como "presupuestador"/"restimator" según
 * el idioma). Si el plugin está inactivo, el CPT no existe o el caso todavía
 * no se publicó, devuelve '' y la barra cae al listado de Work — nunca imprime
 * un enlace roto.
 *
 * @return string
 */
function es_ds_restimator_case_url() {
	/**
	 * Permite fijar la URL del caso a mano si el slug cambia.
	 *
	 * @param string $url URL del caso, o '' para autodetectar.
	 */
	$url = (string) apply_filters( 'es_ds_restimator_case_url', '' );
	if ( '' !== $url ) {
		return $url;
	}

	if ( ! post_type_exists( 'es_case_study' ) ) {
		return '';
	}

	foreach ( array( 'restimator', 'presupuestador', 'presupuestador-re' ) as $slug ) {
		$post = get_page_by_path( $slug, OBJECT, 'es_case_study' );
		if ( $post && 'publish' === $post->post_status ) {
			// Con Polylang, el visitante tiene que caer en la traducción de su
			// idioma, no siempre en el post que encontró la búsqueda por slug.
			if ( function_exists( 'pll_get_post' ) ) {
				$translated = pll_get_post( $post->ID );
				if ( $translated ) {
					$post = get_post( $translated );
				}
			}
			return get_permalink( $post );
		}
	}
	return '';
}
