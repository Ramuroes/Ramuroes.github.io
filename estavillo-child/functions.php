<?php
/**
 * Estavillo Child — funciones del tema.
 *
 * Foundation técnica del nuevo portfolio ESTAVILLO sobre Kadence.
 * Todo lo específico vive en inc/ para mantener este archivo mínimo.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ES_CHILD_VERSION', '0.2.0' );
define( 'ES_CHILD_DIR', get_stylesheet_directory() );
define( 'ES_CHILD_URI', get_stylesheet_directory_uri() );

require ES_CHILD_DIR . '/inc/enqueue.php';
require ES_CHILD_DIR . '/inc/theme-options.php';
require ES_CHILD_DIR . '/inc/selected-work-fallback.php';
require ES_CHILD_DIR . '/inc/featured-case-fallback.php';
require ES_CHILD_DIR . '/inc/work-page-fallback.php';

/**
 * Textdomain del child theme.
 */
function es_child_setup() {
	load_child_theme_textdomain( 'estavillo-child', ES_CHILD_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'es_child_setup' );

/* -------------------------------------------------------------------------
 * Compatibilidad Polylang
 * -------------------------------------------------------------------------
 * Los strings de interfaz del tema (labels de secciones, CTAs placeholder)
 * se registran en Polylang cuando el plugin está activo, y es__() los
 * resuelve con pll__() si existe. Sin Polylang, cae en __() estándar.
 * El copy final NO se hardcodea acá: estas son cadenas provisionales
 * marcadas como editables en los template parts.
 * ---------------------------------------------------------------------- */

/**
 * Strings de interfaz registrables/traducibles del tema.
 *
 * @return string[] Mapa clave => texto por defecto (EN provisional).
 */
function es_child_ui_strings() {
	return array(
		'hero_eyebrow'        => 'Product Designer · Systems & Operations',
		'hero_cta_primary'    => 'View featured case',
		'hero_cta_secondary'  => 'See how I work',
		'nav_work'            => 'Work',
		'nav_how'             => 'How I Work',
		'nav_about'           => 'About',
		'nav_connect'         => 'Connect',
		'featured_label'      => 'Featured case',
		'featured_cta'        => 'Read the case study',
		'process_label'       => 'How I work',
		'process_cta'         => 'See my process',
		'work_label'          => 'Selected work',
		'work_view_all'       => 'All work',
		'work_view_case'      => 'View case study',
		'about_label'         => 'About',
		'about_cta'           => 'More about me',
		'cta_label'           => 'Connect',
		'cta_button'          => 'Write me',
		'lang_switch_label'   => 'Language switch',
		'case_meta_status'    => 'Status',
		'case_meta_role'      => 'Role',
		'case_meta_tools'     => 'Tools',
		'case_meta_period'    => 'Period',
	);
}

/**
 * Email de contacto por defecto (editable por filtro es_contact_email).
 *
 * @return string
 */
function es_contact_email() {
	return apply_filters( 'es_contact_email', 'hello@ramiroestavillo.com' );
}

/**
 * Registro ordenado de secciones de la Home (fundación para editabilidad).
 *
 * Fuente única de la NARRATIVA de la home. Cada entrada mapea una clave de
 * sección → su template part. El orden del array es el orden de render:
 *
 *   Hero → How I Work → Featured Case → Selected Work → About → Connect
 *
 * Reordenar / quitar / insertar secciones = filtrar 'es_home_sections' (p. ej.
 * desde Code Snippets), sin editar el template PHP:
 *
 *   add_filter( 'es_home_sections', function ( $s ) {
 *       // mover About antes de Selected Work, por ejemplo:
 *       $about = $s['about']; unset( $s['about'] );
 *       // ...reordenar el array y devolverlo
 *       return $s;
 *   } );
 *
 * MIGRACIÓN FUTURA: cada valor es hoy un template part; mañana cada clave puede
 * apuntar a un block/pattern reutilizable sin reescribir el template — la clave
 * de sección es el contrato estable. Ver README (Foundation for editability).
 *
 * @return array<string,string> clave de sección => slug del template part
 */
function es_home_sections() {
	return apply_filters(
		'es_home_sections',
		array(
			'hero'          => 'template-parts/hero-home',
			'how-i-work'    => 'template-parts/how-i-work',
			'featured'      => 'template-parts/featured-case',
			'selected-work' => 'template-parts/selected-work',
			'about'         => 'template-parts/about-teaser',
			'connect'       => 'template-parts/footer-cta',
		)
	);
}

/**
 * Enlaces de navegación (header y footer). Editable por filtro.
 * Por defecto anclan a las secciones de la home de una sola página; se
 * pueden apuntar a páginas reales (Work/About/…) vía el filtro cuando existan.
 *
 * @return array<int,array{label:string,url:string}>
 */
function es_nav_links() {
	return apply_filters(
		'es_nav_links',
		array(
			array(
				'label' => es__( 'nav_work' ),
				'url'   => '#work',
			),
			array(
				'label' => es__( 'nav_how' ),
				'url'   => '#process',
			),
			array(
				'label' => es__( 'nav_about' ),
				'url'   => '#about',
			),
			array(
				'label' => es__( 'nav_connect' ),
				'url'   => '#connect',
			),
		)
	);
}

/**
 * Arma un breadcrumb trail estándar: Home, un nivel intermedio opcional
 * (reusando es_nav_links() — el mismo array que ya alimenta el header, el
 * menú mobile y el footer), y un último crumb opcional para la página/caso
 * actual (siempre sin link, sea cual sea su posición — ver
 * template-parts/breadcrumbs.php).
 *
 * Reusar es_nav_links() en vez de hardcodear URLs acá tiene dos ventajas:
 * si el admin repunta un nav link a una página real, el breadcrumb la
 * sigue automáticamente; y con Polylang activo, esos links y home_url()
 * ya resuelven al idioma actual de forma nativa — cero código extra.
 *
 * @param string $nav_label_key Clave de es_child_ui_strings() a matchear
 *                               contra la label de es_nav_links() (p.ej.
 *                               'nav_work'). Cadena vacía = omitir ese nivel.
 * @param string $current_label Título de la página/caso actual. Cadena
 *                               vacía = omitir (p.ej. en la propia página
 *                               Work, donde el nav link YA es el último
 *                               crumb).
 * @return array<int,array{label:string,url?:string}>
 */
function es_breadcrumb_trail( $nav_label_key = '', $current_label = '' ) {
	$trail = array(
		array(
			'label' => __( 'Home', 'estavillo-child' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( $nav_label_key ) {
		foreach ( es_nav_links() as $es_bc_link ) {
			if ( es__( $nav_label_key ) === $es_bc_link['label'] ) {
				$trail[] = $es_bc_link;
				break;
			}
		}
	}

	if ( $current_label ) {
		$trail[] = array( 'label' => $current_label );
	}

	return $trail;
}

/**
 * Librería curada de íconos para los pasos de "How I Work" (línea fina,
 * 16x16, currentColor — mismo lenguaje visual que el ícono del toggle
 * Light/Dark en el header). Whitelist deliberada: el plugin solo guarda la
 * CLAVE elegida en un <select> (nunca HTML libre), así que no hay superficie
 * de XSS acá — ver es_portfolio_home_content_page() en el plugin, sección
 * "How I Work".
 *
 * @return array<string,string> clave => markup SVG completo.
 */
function es_process_icon_library() {
	$stroke = 'fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"';
	return array(
		'compass'  => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><circle cx="8" cy="8" r="6.2"/><path d="M10.2 5.8 8.9 8.9 5.8 10.2 7.1 7.1z"/></svg>',
		'flow'     => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><circle cx="3" cy="8" r="1.4"/><circle cx="13" cy="8" r="1.4"/><path d="M4.4 8h7.2M9 5.2 11.8 8 9 10.8"/></svg>',
		'map'      => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><path d="M2 4.3 6 3l4 1.3 4-1.3v9.4L10 13.7 6 12.4l-4 1.3z"/><path d="M6 3v9.4M10 4.3v9.4"/></svg>',
		'target'   => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><circle cx="8" cy="8" r="6"/><circle cx="8" cy="8" r="3"/><circle cx="8" cy="8" r="0.6" fill="currentColor"/></svg>',
		'layers'   => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><path d="M8 2 14 5.2 8 8.4 2 5.2Z"/><path d="M2 9.4 8 12.6 14 9.4"/></svg>',
		'tool'     => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><path d="M9.7 3.3a2.6 2.6 0 0 0-3.4 3.4L2.9 10l1.6 1.6 3.3-3.4a2.6 2.6 0 0 0 3.4-3.4l-1.5 1.5-1.2-1.2Z"/></svg>',
		'check'    => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><circle cx="8" cy="8" r="6.2"/><path d="M5.2 8.2 7.2 10.2 10.8 6"/></svg>',
		'document' => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><rect x="3.2" y="2" width="9.6" height="12" rx="1"/><path d="M5.6 5.8h4.8M5.6 8.4h4.8M5.6 11h3"/></svg>',
		'bulb'     => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><path d="M8 2.2a4 4 0 0 1 2.2 7.3c-.4.3-.7.9-.7 1.4v.3H6.5v-.3c0-.5-.3-1.1-.7-1.4A4 4 0 0 1 8 2.2Z"/><path d="M6.6 13.4h2.8M7 14.8h2"/></svg>',
		'rocket'   => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><path d="M8 2c1.8 1.4 2.8 3.6 2.8 6.2 0 1-.2 2-.6 2.9H5.8c-.4-.9-.6-1.9-.6-2.9C5.2 5.6 6.2 3.4 8 2Z"/><circle cx="8" cy="7" r="1"/><path d="M5.8 11.1 4.4 13M10.2 11.1l1.4 1.9M6.6 13.6v1.2M9.4 13.6v1.2"/></svg>',
	);
}

/**
 * Choices para el <select> de ícono por paso en Home Content (wp-admin).
 * Llamado desde el plugin con function_exists() como guarda (ver
 * home-content-options.php) — el plugin nunca asume que el tema activo
 * define esto.
 *
 * @return array<string,string> clave => label legible.
 */
function es_process_icon_choices() {
	$labels = array(
		'compass'  => __( 'Compass (understand)', 'estavillo-child' ),
		'flow'     => __( 'Flow (connect)', 'estavillo-child' ),
		'map'      => __( 'Map (plan)', 'estavillo-child' ),
		'target'   => __( 'Target (bottleneck)', 'estavillo-child' ),
		'layers'   => __( 'Layers (design)', 'estavillo-child' ),
		'tool'     => __( 'Tool (prototype)', 'estavillo-child' ),
		'check'    => __( 'Check (validate)', 'estavillo-child' ),
		'document' => __( 'Document (archive)', 'estavillo-child' ),
		'bulb'     => __( 'Bulb (idea)', 'estavillo-child' ),
		'rocket'   => __( 'Rocket (scale)', 'estavillo-child' ),
	);
	return $labels;
}

/**
 * Markup SVG de un ícono por clave, o cadena vacía si la clave no existe en
 * la librería (nunca imprime HTML fuera de esta whitelist).
 *
 * @param string $key Clave dentro de es_process_icon_library().
 * @return string
 */
function es_process_icon_svg( $key ) {
	$library = es_process_icon_library();
	return isset( $library[ $key ] ) ? $library[ $key ] : '';
}

/**
 * Defaults de los 6 pasos de "How I Work" — única fuente de verdad,
 * usada tanto por el teaser de Home (template-parts/how-i-work.php) como
 * por la página dedicada (template-parts/how-i-work-detail.php), así que
 * los dos siempre ven el mismo contenido base si Home Content está vacío.
 * 'why' / 'example' / 'tools' quedan sin default (opcionales, solo se
 * completan si el admin los carga en Home Content).
 *
 * @return array<int,array>
 */
function es_home_process_steps_defaults() {
	return array(
		array(
			'title' => 'Understand the system',
			'text'  => 'Map the operational context before touching any screen.',
			'icon'  => '',
		),
		array(
			'title' => 'Map the flows',
			'text'  => 'Make the invisible process visible and shared.',
			'icon'  => '',
		),
		array(
			'title' => 'Identify bottlenecks',
			'text'  => 'Find where decisions, information or knowledge break down.',
			'icon'  => '',
		),
		array(
			'title' => 'Design interventions',
			'text'  => 'Prototype the logic before the interface.',
			'icon'  => '',
		),
		array(
			'title' => 'Validate in the real world',
			'text'  => 'Real users, real data, real pressure.',
			'icon'  => '',
		),
		array(
			'title' => 'Document and scale',
			'text'  => 'Good design must survive the designer.',
			'icon'  => '',
		),
	);
}

/**
 * Los 6 pasos de "How I Work" ya pasados por el filtro 'es_home_process_steps'
 * (con los defaults de arriba) — llamado por el teaser Y la página dedicada,
 * así que las dos leen exactamente el mismo dato editado desde Home Content.
 *
 * @return array<int,array>
 */
function es_home_process_steps() {
	return apply_filters( 'es_home_process_steps', es_home_process_steps_defaults() );
}

/**
 * Whitelist de tags/atributos SVG permitidos al imprimir un ícono de la
 * librería curada (compartida por process-icons y hobby-icons — mismo
 * shape de SVG simple, línea fina, sin scripts/estilos/eventos).
 *
 * @return array
 */
function es_icon_svg_kses_rules() {
	return array(
		'svg'    => array(
			'width'           => true,
			'height'          => true,
			'viewbox'         => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
		),
		'g'      => array(
			'class' => true,
		),
		'circle' => array(
			'cx'   => true,
			'cy'   => true,
			'r'    => true,
			'fill' => true,
		),
		'path'   => array(
			'd'     => true,
			'fill'  => true,
			'class' => true,
		),
		'rect'   => array(
			'x'      => true,
			'y'      => true,
			'width'  => true,
			'height' => true,
			'rx'     => true,
		),
	);
}

/**
 * Markup seguro (ya pasado por wp_kses) del ícono de un paso de "How I
 * Work": primero la librería curada (icon_key), si no la clave legacy
 * 'icon' (HTML libre, wp_kses_post) por compatibilidad hacia atrás.
 * Cadena vacía si el paso no tiene ninguno — el layout no cambia.
 * Compartido por el teaser de Home y la página dedicada.
 *
 * @param array $step Un elemento de es_home_process_steps().
 * @return string
 */
function es_process_step_icon_markup( $step ) {
	$icon_key = ! empty( $step['icon_key'] ) ? $step['icon_key'] : '';
	$icon_svg = $icon_key ? es_process_icon_svg( $icon_key ) : '';

	if ( '' !== $icon_svg ) {
		return wp_kses( $icon_svg, es_icon_svg_kses_rules() );
	}
	if ( ! empty( $step['icon'] ) ) {
		return wp_kses_post( $step['icon'] );
	}
	return '';
}

/**
 * Librería curada de íconos para "Hobbies & interests" (About). Registro
 * separado de es_process_icon_library() — dominio distinto (intereses
 * personales, no pasos de proceso) — pero mismo principio de whitelist:
 * el admin solo guarda la CLAVE en un <select>, nunca HTML libre.
 *
 * Cada ícono termina en un <g> o <path> "animado": ver
 * assets/css/pages.css → ".es-hobby-item[data-icon=...]" para la
 * micro-interacción de hover/focus de CADA uno (siempre un único
 * transform/opacity en respuesta a :hover/:focus-visible — nunca
 * autoplay/loop, y ya cubierto por la regla global de
 * prefers-reduced-motion en base.css).
 *
 * @return array<string,string> clave => markup SVG completo.
 */
function es_hobby_icon_library() {
	$stroke = 'fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"';
	return array(
		'taekwondo' => '<svg width="20" height="20" viewBox="0 0 20 20" ' . $stroke . '><circle cx="10" cy="4.2" r="1.6"/><path d="M10 5.8v5.4"/><path d="M10 7.6 7.3 9.4"/><path d="M10 11.2 7.6 16.2"/><g><path d="M10 11.2 14.4 13"/></g></svg>',
		'music'     => '<svg width="20" height="20" viewBox="0 0 20 20" ' . $stroke . '><circle cx="6" cy="15" r="1.6" fill="currentColor" stroke="none"/><path d="M7.6 15V6.2L14 4.6"/><g><circle cx="14" cy="8.4" r="1.6" fill="currentColor" stroke="none"/><path d="M15.6 8.4V4.6"/></g></svg>',
		'coffee'    => '<svg width="20" height="20" viewBox="0 0 20 20" ' . $stroke . '><path d="M4.5 8h9v4.2a3 3 0 0 1-3 3h-3a3 3 0 0 1-3-3z"/><path d="M13.5 9h1.2a1.8 1.8 0 0 1 0 3.6h-1.2"/><path d="M4.5 15.2h9"/><g><path d="M7.4 6.4c0-1 .8-1 .8-2s-.8-1-.8-2"/><path d="M10.6 6.4c0-1 .8-1 .8-2s-.8-1-.8-2"/></g></svg>',
		'horse'     => '<svg width="20" height="20" viewBox="0 0 20 20" ' . $stroke . '><path d="M5.5 13V9a4.5 4.5 0 0 1 9 0v4"/><g><path d="M5.5 13v1.8M14.5 13v1.8"/></g></svg>',
		'drawing'   => '<svg width="20" height="20" viewBox="0 0 20 20" ' . $stroke . '><path d="M5 15 12.3 7.7"/><path d="M12.3 7.7 14.3 5.7 15.3 6.7 13.3 8.7Z"/><path d="M4.6 15.4 5 14"/><g><path d="M4 16.6c1.2-.35 2.3-.15 3.3.4"/></g></svg>',
		'travel'    => '<svg width="20" height="20" viewBox="0 0 20 20" ' . $stroke . '><path d="M17 3 3 9.4l5.6 1.6M17 3 11.4 17l-2.8-6M17 3 8.6 11"/><g><path d="M8.6 11v3.4"/></g></svg>',
		'cinema'    => '<svg width="20" height="20" viewBox="0 0 20 20" ' . $stroke . '><rect x="3.5" y="7.5" width="13" height="9" rx="1"/><path d="M3.5 10.5h13"/><g><path d="M4.2 7.5 6.4 4M8.2 7.5 10.4 4M12.2 7.5 14.4 4"/></g></svg>',
	);
}

/**
 * Choices para el <select> de ícono por hobby en Home Content (wp-admin).
 *
 * @return array<string,string> clave => label legible.
 */
function es_hobby_icon_choices() {
	return array(
		'taekwondo' => __( 'Taekwondo / martial arts', 'estavillo-child' ),
		'music'     => __( 'Music', 'estavillo-child' ),
		'coffee'    => __( 'Coffee', 'estavillo-child' ),
		'horse'     => __( 'Horse riding', 'estavillo-child' ),
		'drawing'   => __( 'Drawing', 'estavillo-child' ),
		'travel'    => __( 'Travel', 'estavillo-child' ),
		'cinema'    => __( 'Cinema / film', 'estavillo-child' ),
	);
}

/**
 * Markup SVG de un hobby-icon por clave, o cadena vacía si no existe en la
 * librería.
 *
 * @param string $key Clave dentro de es_hobby_icon_library().
 * @return string
 */
function es_hobby_icon_svg( $key ) {
	$library = es_hobby_icon_library();
	return isset( $library[ $key ] ) ? $library[ $key ] : '';
}

/**
 * Defaults de "Hobbies & interests" (About) — los 7 sugeridos por el
 * ticket original, mostrados de entrada como cualquier otro contenido
 * placeholder del sitio (mismo principio que About text / Process steps:
 * contenido real de arranque, no una sección vacía). El admin los edita,
 * reordena (moviendo qué fila llena — mismo criterio "posición = orden"
 * que Nav Links/Process Steps/Timeline/Educación), oculta (checkbox
 * "Show") o reemplaza desde Home Content, hasta 8 filas.
 *
 * @return array<int,array{label:string,icon:string,text:string,show:bool}>
 */
function es_home_about_hobbies_defaults() {
	return array(
		array(
			'label' => 'Taekwondo',
			'icon'  => 'taekwondo',
			'text'  => '',
			'show'  => true,
		),
		array(
			'label' => 'Music',
			'icon'  => 'music',
			'text'  => '',
			'show'  => true,
		),
		array(
			'label' => 'Coffee',
			'icon'  => 'coffee',
			'text'  => '',
			'show'  => true,
		),
		array(
			'label' => 'Horse riding',
			'icon'  => 'horse',
			'text'  => '',
			'show'  => true,
		),
		array(
			'label' => 'Drawing',
			'icon'  => 'drawing',
			'text'  => '',
			'show'  => true,
		),
		array(
			'label' => 'Travel',
			'icon'  => 'travel',
			'text'  => '',
			'show'  => true,
		),
		array(
			'label' => 'Cinema',
			'icon'  => 'cinema',
			'text'  => '',
			'show'  => true,
		),
	);
}

/**
 * Los items de "Hobbies & interests" ya pasados por el filtro
 * 'es_about_hobbies_items' (con los defaults de arriba) Y ya filtrados a
 * solo los visibles (show !== false) — la plantilla nunca necesita volver
 * a chequear el flag "show", solo recorrer el array.
 *
 * @return array<int,array{label:string,icon:string,text:string}>
 */
function es_about_hobbies_visible() {
	$items = apply_filters( 'es_about_hobbies_items', es_home_about_hobbies_defaults() );
	if ( ! is_array( $items ) ) {
		return array();
	}
	return array_values(
		array_filter(
			$items,
			function ( $item ) {
				return ! empty( $item['label'] ) && ( ! isset( $item['show'] ) || false !== $item['show'] );
			}
		)
	);
}

/**
 * Registra los strings del tema en Polylang (si está activo).
 */
function es_child_register_pll_strings() {
	if ( ! function_exists( 'pll_register_string' ) ) {
		return;
	}
	foreach ( es_child_ui_strings() as $key => $text ) {
		pll_register_string( 'es_' . $key, $text, 'Estavillo Child' );
	}
}
add_action( 'init', 'es_child_register_pll_strings' );

/**
 * Devuelve un string de interfaz, traducido por Polylang si está disponible.
 *
 * @param string $key Clave dentro de es_child_ui_strings().
 * @return string
 */
function es__( $key ) {
	$strings = es_child_ui_strings();
	$text    = isset( $strings[ $key ] ) ? $strings[ $key ] : $key;
	if ( function_exists( 'pll__' ) ) {
		return pll__( $text );
	}
	return $text;
}
