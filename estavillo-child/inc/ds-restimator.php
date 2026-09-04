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
 * Idioma efectivo de esta página del Design System.
 *
 * Devuelve el código de idioma para el que EXISTE un partial. El documento se
 * publica en español e inglés (ds/restimator/master-es.php y master-en.php,
 * los dos generados por el build desde la misma fuente); si algún día se
 * agrega un idioma sin su partial, cae a español en vez de romper.
 *
 * @return string 'es' | 'en'
 */
function es_ds_restimator_lang() {
	$es_lang = function_exists( 'pll_current_language' ) ? (string) pll_current_language() : '';
	if ( '' === $es_lang ) {
		$es_lang = (string) get_locale();
	}
	$es_lang = strtolower( substr( $es_lang, 0, 2 ) );

	if ( 'es' !== $es_lang && file_exists( ES_DS_RESTIMATOR_DIR . "master-{$es_lang}.php" ) ) {
		return $es_lang;
	}
	return 'es';
}

/**
 * Strings del chrome EXCLUSIVOS de esta página, resueltos por el idioma del
 * propio documento.
 *
 * Por qué no se usa es__() acá: el resto del tema guarda sus textos en inglés
 * en es_child_ui_strings() y delega la traducción a Polylang → String
 * translations, o sea que depende de que alguien cargue esa traducción a mano
 * en wp-admin. Si no está cargada, la página en español muestra el chrome en
 * inglés (verificado). Como estos textos son sólo de esta página y el idioma
 * del documento ya está resuelto por es_ds_restimator_lang(), acá se sirve el
 * texto correcto de entrada, sin pasos manuales. El sistema de traducciones
 * del resto del portfolio no cambia.
 *
 * @param string $key Clave del string.
 * @return string
 */
function es_ds_text( $key ) {
	$es_strings = array(
		'es' => array(
			'back_to_case'  => 'Volver al caso REstimator',
			'owner'         => 'Ramiro Estavillo',
			'nav_aria'      => 'Salir de la documentación',
			'missing'       => 'El documento del Design System no está disponible en esta instalación.',
			'viewer_close'  => 'Cerrar la pantalla',
			'viewer_in'     => 'Acercar',
			'viewer_out'    => 'Alejar',
			'viewer_scroll' => 'Pantalla completa — usá las flechas para recorrerla',
		),
		'en' => array(
			'back_to_case'  => 'Back to the REstimator case',
			'owner'         => 'Ramiro Estavillo',
			'nav_aria'      => 'Leave the documentation',
			'missing'       => 'The Design System document is not available in this installation.',
			'viewer_close'  => 'Close the screen',
			'viewer_in'     => 'Zoom in',
			'viewer_out'    => 'Zoom out',
			'viewer_scroll' => 'Full screen — use the arrow keys to scroll through it',
		),
	);

	$es_lang = es_ds_restimator_lang();
	if ( isset( $es_strings[ $es_lang ][ $key ] ) ) {
		return $es_strings[ $es_lang ][ $key ];
	}
	return isset( $es_strings['es'][ $key ] ) ? $es_strings['es'][ $key ] : $key;
}

/**
 * Imprime el documento del Design System.
 *
 * El partial usa $es_ds_screens para construir las URLs de las capturas y
 * es_ds_text() para las labels del visor, así que $es_ds_screens tiene que
 * existir en el scope antes del include.
 *
 * @return bool true si se imprimió algo.
 */
function es_ds_restimator_render_document() {
	$es_ds_screens = es_ds_restimator_screens_uri(); // phpcs:ignore VariableAnalysis -- lo usa el partial incluido abajo.
	$es_file       = ES_DS_RESTIMATOR_DIR . 'master-' . es_ds_restimator_lang() . '.php';

	if ( ! file_exists( $es_file ) ) {
		return false;
	}
	require $es_file;
	return true;
}

/**
 * URL del Case Study de REstimator, para el "volver" de la navegación mínima.
 *
 * Se resuelve por slug contra el CPT del plugin. El slug real del caso es
 * 'presupuestador' (documentado en
 * docs/content/presupuestador-case-study-fields.md, para ES y EN); los otros
 * dos quedan como defensa por si el caso se republica con otro nombre. Si el
 * plugin está inactivo, el CPT no existe o el caso todavía no se publicó,
 * devuelve '' y el llamador cae al listado de Work — nunca imprime un enlace
 * roto.
 *
 * @return string
 */
function es_ds_restimator_case_url() {
	/**
	 * Permite fijar la URL del caso a mano si el slug cambia.
	 *
	 * @param string $url URL del caso, o '' para autodetectar.
	 */
	$es_url = (string) apply_filters( 'es_ds_restimator_case_url', '' );
	if ( '' !== $es_url ) {
		return $es_url;
	}

	if ( ! post_type_exists( 'es_case_study' ) ) {
		return '';
	}

	foreach ( array( 'presupuestador', 'restimator', 'presupuestador-re' ) as $es_slug ) {
		$es_post = get_page_by_path( $es_slug, OBJECT, 'es_case_study' );
		if ( $es_post && 'publish' === $es_post->post_status ) {
			// Con Polylang, el visitante tiene que caer en la traducción de su
			// idioma, no siempre en el post que encontró la búsqueda por slug.
			if ( function_exists( 'pll_get_post' ) ) {
				$es_translated = pll_get_post( $es_post->ID );
				if ( $es_translated ) {
					$es_post = get_post( $es_translated );
				}
			}
			return get_permalink( $es_post );
		}
	}
	return '';
}

/**
 * URL de salida para la navegación mínima: el caso si existe, si no el listado
 * de Work, si no la Home. Nunca vacía.
 *
 * @return string
 */
function es_ds_restimator_back_url() {
	$es_url = es_ds_restimator_case_url();
	if ( '' === $es_url && function_exists( 'es_page_url_by_template' ) ) {
		$es_url = es_page_url_by_template( 'templates/page-work.php' );
	}
	return '' !== $es_url ? $es_url : home_url( '/' );
}

/* -------------------------------------------------------------------------
 * Chrome institucional por página (header / footer)
 * ---------------------------------------------------------------------- */

/**
 * Meta de visibilidad del chrome, con default ACTIVO.
 *
 * El default no se siembra en la base de datos: un checkbox HTML no envía nada
 * cuando está desmarcado, así que el guardado escribe '1' o '0' explícito y la
 * AUSENCIA de meta (una página que nunca pasó por el meta box — por ejemplo la
 * que ya está publicada) se lee como activo. Mismo criterio de "no escribir
 * nada por su cuenta" que inc/page-hero-meta.php: cero migración.
 *
 * @param string $key '_es_ds_show_header' | '_es_ds_show_footer'.
 * @return bool
 */
function es_ds_chrome_enabled( $key ) {
	$es_id = get_queried_object_id();
	if ( ! $es_id ) {
		return true;
	}
	$es_value = get_post_meta( $es_id, $key, true );
	return '' === $es_value ? true : ( '1' === $es_value );
}

/**
 * ¿Se imprime el header institucional del portfolio en esta página?
 *
 * @return bool
 */
function es_ds_show_header() {
	return es_ds_chrome_enabled( '_es_ds_show_header' );
}

/**
 * ¿Se imprime el footer institucional del portfolio en esta página?
 *
 * @return bool
 */
function es_ds_show_footer() {
	return es_ds_chrome_enabled( '_es_ds_show_footer' );
}

/**
 * ¿Esta página necesita el chrome del portfolio (site.css + nav.js + motion.js)?
 * Lo consulta inc/enqueue.php: sin header ni footer no hace falta pedir nada de
 * eso, igual que hoy.
 *
 * @return bool
 */
function es_ds_needs_chrome_assets() {
	return es_is_ds_restimator_page() && ( es_ds_show_header() || es_ds_show_footer() );
}

/**
 * Meta box con los dos toggles.
 */
function es_ds_add_meta_box() {
	add_meta_box(
		'es_ds_restimator',
		__( 'REstimator Design System', 'estavillo-child' ),
		'es_ds_render_meta_box',
		'page',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'es_ds_add_meta_box' );

/**
 * Renderiza el meta box. En una Página que no usa este template sólo explica
 * para qué sirve, en vez de ofrecer controles que no harían nada.
 *
 * @param WP_Post $post Post actual.
 */
function es_ds_render_meta_box( $post ) {
	if ( ES_DS_RESTIMATOR_TEMPLATE !== get_page_template_slug( $post->ID ) ) {
		echo '<p class="description">' . esc_html__( 'Only applies to pages using the “Estavillo — REstimator Design System” template.', 'estavillo-child' ) . '</p>';
		return;
	}

	wp_nonce_field( 'es_ds_chrome_save', 'es_ds_chrome_nonce' );

	// Sin meta guardada = activo (ver es_ds_chrome_enabled()).
	$es_header = get_post_meta( $post->ID, '_es_ds_show_header', true );
	$es_footer = get_post_meta( $post->ID, '_es_ds_show_footer', true );
	?>
	<p>
		<label>
			<input type="checkbox" name="es_ds_show_header" value="1" <?php checked( '' === $es_header || '1' === $es_header ); ?>>
			<?php esc_html_e( 'Show institutional header', 'estavillo-child' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="es_ds_show_footer" value="1" <?php checked( '' === $es_footer || '1' === $es_footer ); ?>>
			<?php esc_html_e( 'Show institutional footer', 'estavillo-child' ); ?>
		</label>
	</p>
	<p class="description">
		<?php esc_html_e( 'Both on by default. With the header off, the page falls back to a minimal “Back to the REstimator case” bar, so there is always a way out.', 'estavillo-child' ); ?>
	</p>
	<?php
}

/**
 * Guarda los toggles. Mismos guards que el resto de los meta boxes del tema:
 * nonce, autosave y capability sobre ESTE post.
 *
 * Escribe '1' u '0' explícito y nunca borra la meta: así "desmarcado a
 * propósito" queda distinguible de "nunca configurado", que es lo que hace que
 * el default activo funcione sin sembrar nada.
 *
 * @param int $post_id ID del post.
 */
function es_ds_save_meta( $post_id ) {
	if ( ! isset( $_POST['es_ds_chrome_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['es_ds_chrome_nonce'] ) ), 'es_ds_chrome_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_es_ds_show_header', isset( $_POST['es_ds_show_header'] ) ? '1' : '0' );
	update_post_meta( $post_id, '_es_ds_show_footer', isset( $_POST['es_ds_show_footer'] ) ? '1' : '0' );
}
add_action( 'save_post_page', 'es_ds_save_meta' );
