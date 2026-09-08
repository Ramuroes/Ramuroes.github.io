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

/** Directorio en disco de las capturas, para poder leerles el mtime. */
const ES_DS_RESTIMATOR_SCREENS_REL = 'assets/ds/restimator/screens/';

/**
 * URL de una captura, versionada por el propio archivo.
 *
 * El problema que resuelve, medido en producción: las capturas se sirven desde
 * una URL FIJA (`…/screens/light-dark.webp`). Cuando el build las regenera, el
 * archivo cambia pero la URL no, así que ni el navegador ni el CDN tienen forma
 * de saber que hay algo nuevo. En una ventana de incógnito se veía la captura
 * nueva y en una sesión existente seguía apareciendo la vieja, incluso después
 * de hard refresh: la respuesta venía del edge de Cloudflare, no del navegador.
 *
 * La versión sale de `filemtime()`, igual que `es_asset_ver()` para el CSS y el
 * JS del tema. Es la propiedad que se necesita: estable mientras el archivo no
 * cambie —así el caché sigue sirviendo— y distinta en cuanto el build lo
 * reescribe. No se usa ES_CHILD_VERSION porque obligaría a acordarse de subirla
 * a mano cada vez que se recapturan pantallas, que es exactamente el paso que
 * se olvida.
 *
 * Alcance: SÓLO estas capturas. El resto del portfolio no cambia.
 *
 * @param string $file Nombre del archivo dentro de screens/ (p. ej. 'light-dark.webp').
 * @return string URL con `?v=<mtime>`.
 */
function es_ds_restimator_screen_url( $file ) {
	$es_file = ltrim( (string) $file, '/' );
	$es_url  = es_ds_restimator_screens_uri() . $es_file;
	$es_path = ES_CHILD_DIR . '/' . ES_DS_RESTIMATOR_SCREENS_REL . $es_file;

	// Sin archivo en disco (theme instalado sin assets/ds/) cae a la versión del
	// tema: la URL sigue siendo válida y no se emite un `?v=` vacío.
	$es_mtime = file_exists( $es_path ) ? filemtime( $es_path ) : false;

	return add_query_arg( 'v', $es_mtime ? (string) $es_mtime : ES_CHILD_VERSION, $es_url );
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
			'crumb_work'    => 'Proyectos',
			'crumb_case'    => 'REstimator',
			'crumb_current' => 'Design System',
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
			'crumb_work'    => 'Work',
			'crumb_case'    => 'REstimator',
			'crumb_current' => 'Design System',
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
 * El partial llama a es_ds_restimator_screen_url() para cada captura, que le
 * agrega la versión del archivo a la URL, y a es_ds_text() para las labels del
 * visor. Las dos son funciones de este archivo: el include no necesita que haya
 * ninguna variable preparada en el scope.
 *
 * @return bool true si se imprimió algo.
 */
function es_ds_restimator_render_document() {
	$es_file = ES_DS_RESTIMATOR_DIR . 'master-' . es_ds_restimator_lang() . '.php';

	if ( ! file_exists( $es_file ) ) {
		return false;
	}
	require $es_file;
	return true;
}

/**
 * ID configurable del Case Study de REstimator.
 *
 * Da igual cuál de sus dos traducciones se cargue acá: es_ds_restimator_case_url()
 * resuelve el idioma correcto con pll_get_post(), así que un solo ID (el de
 * cualquiera de las dos) alcanza para las dos direcciones.
 *
 * Se lee de un theme_mod — Apariencia → Personalizar → REstimator Design
 * System → "REstimator Case Study — Post ID" (ver es_ds_customize_register())
 * — y queda filtrable por si se prefiere fijarlo por código (p. ej. desde el
 * plugin, con el mismo patrón de es_portfolio_featured_case_for_home) en vez
 * de por Customizer.
 *
 * 0 = sin configurar todavía. Es el estado esperado hasta que se cargue: el
 * breadcrumb y la barra mínima ya degradan sin URL de caso (ver más abajo),
 * nunca rompen.
 *
 * @return int
 */
function es_ds_restimator_case_id() {
	return (int) apply_filters( 'es_ds_restimator_case_id', (int) get_theme_mod( 'es_ds_restimator_case_id', 0 ) );
}

/**
 * URL del Case Study de REstimator, para el breadcrumb y para el "volver" de
 * la navegación mínima.
 *
 * Se resuelve por ID (es_ds_restimator_case_id()), no por slug: un slug es
 * exactamente lo que un editor puede cambiar sin avisar, y un ID de post no
 * cambia nunca. (Versión anterior: buscaba por un array de slugs candidatos —
 * 'presupuestador', 'restimator', 'presupuestador-re' — que ya no coincidían
 * con los slugs reales del caso en producción, así que esta relación no
 * resolvía nada. Ver docs/DS-RESTIMATOR.md §17.)
 *
 * Si el plugin está inactivo, el ID todavía no se cargó, el post no existe o
 * no está publicado, devuelve '' y el llamador degrada (breadcrumb omite el
 * nivel, la barra mínima cae al listado de Work) — nunca un enlace roto.
 *
 * @return string
 */
function es_ds_restimator_case_url() {
	/**
	 * Permite fijar la URL del caso a mano, sin pasar por el ID configurado.
	 *
	 * @param string $url URL del caso, o '' para autodetectar por ID.
	 */
	$es_url = (string) apply_filters( 'es_ds_restimator_case_url', '' );
	if ( '' !== $es_url ) {
		return $es_url;
	}

	$es_id = es_ds_restimator_case_id();
	if ( $es_id <= 0 ) {
		return '';
	}

	$es_post = get_post( $es_id );
	if ( ! $es_post || 'publish' !== $es_post->post_status || 'es_case_study' !== $es_post->post_type ) {
		return '';
	}

	// Con Polylang, el visitante tiene que caer en la traducción de su idioma;
	// si todavía no existe (p. ej. mientras se están cargando las dos), se
	// queda con el post configurado — nunca un link roto.
	if ( function_exists( 'pll_get_post' ) ) {
		$es_translated = pll_get_post( $es_post->ID );
		if ( $es_translated ) {
			$es_post = get_post( $es_translated );
		}
	}

	return get_permalink( $es_post );
}

/**
 * URL del REstimator Design System en el idioma de la request actual.
 *
 * Pensada para el CTA "Ver Design System completo" del Case Study de
 * REstimator (ver el shortcode más abajo), sin acoplar ese contenido a un
 * slug: reusa es_page_url_by_template() —el mismo helper que ya resuelve
 * Work— sobre EL MISMO template que usan las dos versiones del DS (una sola
 * plantilla técnica, dos páginas traducidas por Polylang). No hace falta
 * distinguir ES/EN a mano: la query de es_page_url_by_template() ya filtra
 * por el idioma activo.
 *
 * @return string URL, o '' si la página del DS no existe todavía en este idioma.
 */
function es_ds_restimator_url() {
	if ( ! function_exists( 'es_page_url_by_template' ) ) {
		return '';
	}
	return (string) es_page_url_by_template( ES_DS_RESTIMATOR_TEMPLATE );
}

/**
 * Shortcode [es_ds_restimator_url]: para pegar en el href de un botón del
 * Case Study (bloque Buttons o Custom HTML) sin hardcodear la URL del DS.
 *
 * Uso: <a href="[es_ds_restimator_url]">Ver Design System completo →</a> — o
 * el equivalente en un bloque Buttons de Gutenberg, en el campo URL. Con la
 * traducción todavía sin crear devuelve '#': nunca un href vacío ni un fatal.
 *
 * @return string
 */
function es_ds_restimator_url_shortcode() {
	$es_url = es_ds_restimator_url();
	return '' !== $es_url ? esc_url( $es_url ) : '#';
}
add_shortcode( 'es_ds_restimator_url', 'es_ds_restimator_url_shortcode' );

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

/**
 * URL del listado de Work en el idioma actual.
 *
 * es_page_url_by_template() ya resuelve por idioma: la query lleva
 * `suppress_filters => false`, que es lo que habilita el filtro de Polylang, y
 * cachea por template+idioma. Es la misma función que usa la barra mínima para
 * su fallback, así que las dos salidas de la página apuntan al mismo lugar.
 *
 * @return string URL, o '' si no hay página de Work publicada.
 */
function es_ds_restimator_work_url() {
	if ( ! function_exists( 'es_page_url_by_template' ) ) {
		return '';
	}
	return (string) es_page_url_by_template( 'templates/page-work.php' );
}

/**
 * Breadcrumb contextual: Work / REstimator / Design System.
 *
 * SÓLO se imprime con el header institucional activo. Con el header apagado la
 * salida ya la da la barra mínima ("← Volver al caso REstimator"), y sumarle un
 * breadcrumb sería una segunda navegación para lo mismo.
 *
 * Reusa template-parts/breadcrumbs.php: el MISMO partial y las mismas clases
 * que el Case Study y las páginas fijas, así que el estilo lo pone site.css
 * —que en esta página ya se carga por el header— y acá no se inventa ningún
 * componente. Lo único propio es la alineación con la columna del documento
 * (ver doc-overrides.css).
 *
 * Los textos salen de es_ds_text() y no de es__(): el resto del tema delega en
 * Polylang → String translations, que depende de que alguien cargue la
 * traducción a mano en wp-admin. Mismo criterio que la barra mínima.
 *
 * El crumb del caso se omite si el Case Study no existe todavía (plugin
 * inactivo, o caso sin publicar): antes que un link muerto, un nivel menos.
 */
function es_ds_restimator_breadcrumb() {
	if ( ! es_ds_show_header() ) {
		return;
	}

	$es_trail = array();

	$es_work = es_ds_restimator_work_url();
	if ( '' !== $es_work ) {
		$es_trail[] = array(
			'label' => es_ds_text( 'crumb_work' ),
			'url'   => $es_work,
		);
	}

	$es_case = es_ds_restimator_case_url();
	if ( '' !== $es_case ) {
		$es_trail[] = array(
			'label' => es_ds_text( 'crumb_case' ),
			'url'   => $es_case,
		);
	}

	// El último nivel es el estado actual y nunca lleva link (lo resuelve el
	// partial: el último ítem del trail se imprime como <span aria-current>).
	$es_trail[] = array( 'label' => es_ds_text( 'crumb_current' ) );

	// Con un solo nivel no hay nada que navegar: sería un rótulo, no un
	// breadcrumb.
	if ( count( $es_trail ) < 2 ) {
		return;
	}

	get_template_part( 'template-parts/breadcrumbs', null, array( 'trail' => $es_trail ) );
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

/* -------------------------------------------------------------------------
 * Relación estable con el Case Study (Apariencia → Personalizar)
 * ---------------------------------------------------------------------- */

/**
 * Registra la sección "REstimator Design System" en el Customizer, con el
 * único control que necesita: el ID del Case Study al que pertenece el DS.
 *
 * Es un theme_mod y no una opción del CPT porque la relación es del lado del
 * Design System, no del caso — el caso no tiene por qué saber que existe una
 * documentación técnica separada. Un campo de texto con un ID numérico, no un
 * selector de posts: mantiene esto en una sola sección chica, sin agregar un
 * componente de UI nuevo al Customizer sólo para esto. El ID se ve en la barra
 * de direcciones al editar el caso (wp-admin/post.php?post=123&action=edit).
 *
 * No comparte sección con "Estavillo" (theme-options.php): esas opciones son
 * de todo el sitio (acento, hero, tipografía); esta es específica del DS y
 * vive junto al resto de su código, igual que su meta box.
 */
function es_ds_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'es_ds_restimator_options',
		array(
			'title'       => __( 'REstimator Design System', 'estavillo-child' ),
			'description' => __( 'Relación estable con el Case Study de REstimator, para el breadcrumb y el "volver" del Design System. Sobrevive a un cambio de slug: se resuelve por ID de post.', 'estavillo-child' ),
			'priority'    => 31,
		)
	);

	$wp_customize->add_setting(
		'es_ds_restimator_case_id',
		array(
			'default'           => 0,
			'type'              => 'theme_mod',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'es_ds_restimator_case_id',
		array(
			'label'       => __( 'REstimator Case Study — Post ID', 'estavillo-child' ),
			'description' => __( 'El ID de CUALQUIERA de las dos traducciones (ES o EN): el idioma correcto se resuelve solo vía Polylang. 0 = sin configurar — el breadcrumb omite ese nivel y la barra mínima cae al listado de Work.', 'estavillo-child' ),
			'section'     => 'es_ds_restimator_options',
			'type'        => 'text',
		)
	);
}
add_action( 'customize_register', 'es_ds_customize_register' );
