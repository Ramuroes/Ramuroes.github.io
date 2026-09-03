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

define( 'ES_CHILD_VERSION', '0.2.65' );
define( 'ES_CHILD_DIR', get_stylesheet_directory() );
define( 'ES_CHILD_URI', get_stylesheet_directory_uri() );

require ES_CHILD_DIR . '/inc/enqueue.php';
require ES_CHILD_DIR . '/inc/theme-options.php';
require ES_CHILD_DIR . '/inc/selected-work-fallback.php';
require ES_CHILD_DIR . '/inc/featured-case-fallback.php';
require ES_CHILD_DIR . '/inc/featured-media.php';
require ES_CHILD_DIR . '/inc/work-page-fallback.php';
require ES_CHILD_DIR . '/inc/block-styles.php';
require ES_CHILD_DIR . '/inc/how-i-work-illustrations.php';
require ES_CHILD_DIR . '/inc/header-footer.php';
require ES_CHILD_DIR . '/inc/theme-dark-mode.php';
require ES_CHILD_DIR . '/inc/page-hero-meta.php';

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
		'process_cta'         => 'See the full process',
		'work_label'          => 'Selected work',
		'work_view_all'       => 'All work',
		'work_view_case'      => 'View case study',
		// "01 — Featured Work" en la página Work (ticket "Refine Work
		// archive hierarchy"). Clave propia, no reusa 'featured_label'
		// ("Featured case"/"Caso destacado"): son la misma data (mismo
		// flag "Feature this case on Home") pero dos pantallas distintas
		// con su propio copy — tocar una nunca debe tocar la otra.
		'work_featured_label' => 'Featured work',
		'about_label'         => 'About',
		'about_intro_label'   => 'My approach',
		'about_cta'           => 'More about me',
		'cta_label'           => 'Connect',
		'cta_button'          => 'Write me',
		'lang_switch_label'   => 'Language switch',
		'case_meta_status'    => 'Status',
		'case_meta_role'      => 'Role',
		'case_meta_tools'     => 'Tools',
		'case_meta_period'    => 'Period',
		// Spanish-parity phase — global chrome + a11y strings that were
		// previously raw __()/hardcoded (no .mo pipeline exists, so they
		// never actually varied by language). Routed through Polylang
		// string translation like every other shared label above.
		'breadcrumb_home'     => 'Home',
		// 404 (404.php) y las vistas genéricas que antes servía Kadence
		// (search.php / archive.php / index.php / page.php, todas vía
		// template-parts/generic-document.php). Registradas acá para que
		// Polylang las traduzca como cualquier otra cadena de interfaz, en vez
		// de depender de un .mo que este theme no tiene.
		'error_404_title'     => 'Page not found.',
		'error_404_lead'      => "That page doesn't exist, or it moved.",
		'error_404_cta'       => 'Back home',
		'search_eyebrow'      => 'Search',
		// %s = el término buscado, ya escapado. Se deja el marcador para que
		// la traducción pueda moverlo de lugar en la frase.
		'search_title'        => 'Results for “%s”',
		'search_empty'        => 'Nothing matched that search.',
		'archive_eyebrow'     => 'Archive',
		'archive_empty'       => 'Nothing published here yet.',
		'pagination_aria'     => 'Results navigation',
		// Skip link del header (WCAG 2.4.1). Ver template-parts/site-header.php.
		'skip_to_content'     => 'Skip to content',
		'nav_aria_main'       => 'Main',
		'nav_aria_footer'     => 'Footer',
		'nav_aria_mobile'     => 'Mobile',
		'menu_label'          => 'Menu',
		'menu_open'           => 'Open menu',
		'menu_close'          => 'Close menu',
		'theme_toggle_title'  => 'Light / dark — coming soon',
		'scroll_label'        => 'Scroll',
		'scroll_aria'         => 'Scroll to How I work',
		'whatsapp_label'      => 'WhatsApp',
		'footer_call_generic' => 'Call by phone',
		'footer_call_named'   => 'Call %s',
		'footer_wa_generic'   => 'Contact on WhatsApp',
		'footer_wa_named'     => 'Contact %s on WhatsApp',
		'footer_credit_lead'    => 'Designed by <em>me</em>,',
		'footer_social_named'   => "View %1\$s's %2\$s profile",
		'footer_social_generic' => 'View %s profile',
		'case_sections_aria'  => 'Case sections',
		'case_nav_prev'       => 'Previous sections',
		'case_nav_next'       => 'More sections',
		'case_media_ph_aria'  => 'Placeholder for the case visual',
		'connect_cta_all'     => 'All ways to connect',
		// Page-head eyebrow/H1/lead — deliberately outside the migrated
		// Gutenberg body (shared page-head component), so these need the
		// same Polylang-string mechanism as nav labels, not raw __().
		'about_eyebrow'       => 'Product Designer · Systems & Operations',
		'about_title'         => 'About me.',
		'how_title'           => 'How I work.',
		'how_lead'            => "I don't start with interfaces. I start by understanding the system.",
		// Eyebrow del page-head de Work. Clave propia (no reusa 'work_label',
		// que ahora es SOLO el header de la sección "02 — Selected Work" más
		// abajo en la página) — antes una misma cadena hacía de doble función
		// (eyebrow arriba Y header de sección más abajo), acoplando dos roles
		// de UI que necesitaban poder cambiar por separado.
		'work_eyebrow'        => 'Work',
		'work_title'          => 'Work.',
		'work_lead'           => 'A selection of Product Design and systems work, alongside selected earlier work across digital, industrial and visual design.',
		// "03 — More Work / Archive": encabezado + bajada opcional que
		// envuelven TANTO los Case Studies marcados archivo (CPT) COMO el
		// contenido viejo pegado en la página (SAMIC, French Bakery, webs
		// anteriores, industrial/3D/visual/motion) — ver work-cases.php.
		'work_archive_label'  => 'More work',
		'work_archive_lead'   => 'Selected earlier work across digital, industrial, 3D and visual design.',
		'connect_eyebrow'     => 'Get in touch',
		'connect_title'       => 'Start a conversation.',
		'connect_lead'        => "I'm open to Product Design, Design Systems and UX Research roles — anywhere the goal is making a real system work better, not just look better.",
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
 * Teléfono de contacto (editable por filtro es_contact_phone) — mismo
 * criterio que es_contact_email(): una sola función/campo, reusable por
 * Connect hoy y por Footer más adelante si se decide mostrarlo ahí (ver
 * docs/BACKLOG.md).
 *
 * @return string
 */
function es_contact_phone() {
	return apply_filters( 'es_contact_phone', '+598 99 892 722' );
}

/**
 * Número de WhatsApp (editable por filtro es_contact_whatsapp) — campo
 * independiente del teléfono de arriba: mismo dato hoy, pero un número de
 * WhatsApp de negocio podría diferir del teléfono directo más adelante.
 *
 * @return string
 */
function es_contact_whatsapp() {
	return apply_filters( 'es_contact_whatsapp', '+598 99 892 722' );
}

/**
 * Normaliza un número de teléfono/WhatsApp a solo dígitos, para construir
 * enlaces tel:/wa.me sin depender de que el campo se haya guardado con o
 * sin espacios/guiones/paréntesis.
 *
 * @param string $number Número tal como se guardó (formato libre).
 * @return string Solo dígitos.
 */
function es_phone_digits( $number ) {
	return preg_replace( '/[^0-9]/', '', (string) $number );
}

/**
 * Registro ordenado de secciones de la Home (fundación para editabilidad).
 *
 * Fuente única de la NARRATIVA de la home. Cada entrada mapea una clave de
 * sección → su template part. El orden del array es el orden de render:
 *
 *   Hero → How I Work → Featured Case → Selected Work → Tools → About → Connect
 *
 * Tools va DESPUÉS de los casos, no antes: primero criterio y trabajo
 * (cómo se trabaja, qué se hizo), recién después el inventario de con qué
 * — nunca antes de haber mostrado evidencia. Entra sin numeración (ver
 * template-parts/tools.php: la rama fallback la excluye del contador de
 * $es_section_num en templates/page-home-estavillo.php, igual que 'hero')
 * y con un encabezado deliberadamente más liviano que el resto de la
 * narrativa — es un cierre de referencia, no un capítulo con el mismo
 * peso que Featured/Selected Work/About. No es una sección nueva del
 * tema: es el bloque reutilizable estavillo/tools invocado vía
 * render_block(), el mismo que usa About.
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
			'tools'         => 'template-parts/tools',
			'about'         => 'template-parts/about-teaser',
			'connect'       => 'template-parts/footer-cta',
		)
	);
}

/**
 * Enlaces de navegación (header, menú mobile, footer y breadcrumb).
 *
 * Criterio de navegación (iteración de cierre): Cómo trabajo, Sobre mí y
 * Contacto son PÁGINAS reales, no secciones de la Home, así que el menú
 * apunta a la página en el idioma de la request — resuelto por template vía
 * es_nav_page_or_anchor() (inc/header-footer.php), nunca por una ruta
 * hardcodeada. Si la página todavía no existe en ese idioma, el ítem cae al
 * anchor de la Home de siempre y nada se rompe.
 *
 * Work YA NO es la excepción (iteración "unificar Work/Proyectos"): las
 * páginas índice canónicas existen — /my-work/ en inglés, /es/trabajos/ en
 * español — así que el ítem se resuelve exactamente igual que los otros
 * tres, por TEMPLATE, nunca por slug hardcodeado: es_page_url_by_template()
 * busca la página publicada con "Estavillo — Work" asignado en el idioma de
 * la request y devuelve su permalink real, sea cual sea su slug. El '#work'
 * queda solo como fallback si algún día esa página no existiera en un
 * idioma — mismo criterio que how/about/connect.
 *
 * @return array<int,array{label:string,url:string}>
 */
function es_nav_links() {
	return apply_filters(
		'es_nav_links',
		array(
			array(
				'label' => es__( 'nav_work' ),
				'url'   => es_nav_page_or_anchor( 'templates/page-work.php', '#work' ),
			),
			array(
				'label' => es__( 'nav_how' ),
				'url'   => es_nav_page_or_anchor( 'templates/page-how-i-work.php', '#process' ),
			),
			array(
				'label' => es__( 'nav_about' ),
				'url'   => es_nav_page_or_anchor( 'templates/page-about.php', '#about' ),
			),
			array(
				'label' => es__( 'nav_connect' ),
				'url'   => es_nav_page_or_anchor( 'templates/page-contact.php', '#connect' ),
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
			'label' => es__( 'breadcrumb_home' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( $nav_label_key ) {
		foreach ( es_nav_links() as $es_bc_link ) {
			if ( es__( $nav_label_key ) === $es_bc_link['label'] ) {
				/*
				 * La URL se RESUELVE, igual que en el header. Antes se copiaba
				 * el ítem crudo de es_nav_links(), así que el crumb "Work"
				 * heredaba el '#work' literal: dentro de un Case Study eso es
				 * un anchor a una sección que no existe en esa página, y el
				 * click no hacía nada. es_nav_resolve_url() es la MISMA regla
				 * que ya usa es_nav_links_display() para el menú (por eso el
				 * menú funcionaba y el breadcrumb no), y devuelve
				 * "Home del idioma + #work" fuera de la Home.
				 */
				$es_bc_link['url'] = es_nav_resolve_url( isset( $es_bc_link['url'] ) ? $es_bc_link['url'] : '' );
				$trail[]           = $es_bc_link;
				break;
			}
		}
	}

	if ( $current_label ) {
		$trail[] = array( 'label' => $current_label );
	}

	/*
	 * Override opcional del último crumb desde la caja "Page header" de la
	 * propia página (_es_page_breadcrumb_label). Útil cuando el título es
	 * largo y el crumb queda mejor corto. Sólo aplica en una página real y
	 * sólo si el campo tiene contenido: si está vacío, el crumb sigue
	 * saliendo del nav/título de siempre.
	 */
	if ( is_page() ) {
		// Se lee el meta CRUDO a propósito, no es_page_breadcrumb_label():
		// esa función cae al título de la página cuando el campo está vacío,
		// y acá un campo vacío tiene que dejar el crumb como estaba.
		$es_bc_id       = get_queried_object_id();
		$es_bc_override = $es_bc_id ? trim( (string) get_post_meta( $es_bc_id, '_es_page_breadcrumb_label', true ) ) : '';
		$es_bc_last     = count( $trail ) - 1;
		if ( '' !== $es_bc_override && $es_bc_last >= 0 ) {
			$trail[ $es_bc_last ]['label'] = $es_bc_override;
		}
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
		// cubo isométrico (wireframe, 3 caras) — sumado para el bloque Tools
		// (categoría "3D"): 'map' ya está tomado por How I Work ("plan") y no
		// comunica 3D, así que en vez de reusarlo se agrega una figura nueva
		// a la misma librería compartida, con el mismo trazo 1.3.
		'cube'     => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><path d="M8 2 13.5 5.2v5.6L8 14 2.5 10.8V5.2Z"/><path d="M8 8V2M8 8 2.5 5.2M8 8 13.5 5.2"/></svg>',
		/*
		 * Bloque siguiente: figuras sumadas para el ícono por stat de
		 * estavillo/case-stats (un stat suele nombrar una medición, una
		 * persona, un tiempo o una tendencia — vocabulario que la librería
		 * original de How I Work no cubría). Mismo trazo 1.3 / 16x16 /
		 * currentColor: no es una librería nueva, es la misma extendida, y
		 * quedan disponibles para cualquier otro bloque que las necesite.
		 *
		 * 'chart' cubre tanto "chart" como "analytics" a propósito: dos
		 * figuras de barras casi idénticas sólo fragmentarían el <select>
		 * sin agregar significado.
		 */
		// Eje en L + dos barras: probado contra una variante de tres barras
		// sobre línea de base — a 16px, que es el tamaño real de uso, tres
		// barras se apelmazan y el dibujo se lee como ruido; con el eje y dos
		// barras la figura se identifica como gráfico de un vistazo.
		'chart'    => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><path d="M3 2.8V13h10.2"/><path d="M6.4 13V9.4M10.6 13V5.8"/></svg>',
		'trend'    => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><path d="M2.4 11.4 6.2 7.6l2.4 2.4 4.8-4.8"/><path d="M9.8 5.2h3.6v3.6"/></svg>',
		'clock'    => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><circle cx="8" cy="8" r="6.2"/><path d="M8 4.6V8l2.4 1.6"/></svg>',
		'user'     => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><circle cx="8" cy="5.4" r="2.6"/><path d="M3.2 13.4a4.8 4.8 0 0 1 9.6 0"/></svg>',
		'search'   => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><circle cx="7.2" cy="7.2" r="4.4"/><path d="M10.5 10.5 13.6 13.6"/></svg>',
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
		'cube'     => __( 'Cube (3D)', 'estavillo-child' ),
		'chart'    => __( 'Chart (analytics)', 'estavillo-child' ),
		'trend'    => __( 'Trend (growth)', 'estavillo-child' ),
		'clock'    => __( 'Clock (time)', 'estavillo-child' ),
		'user'     => __( 'User (people)', 'estavillo-child' ),
		'search'   => __( 'Search (find)', 'estavillo-child' ),
	);
	return $labels;
}

/**
 * Íconos de la caja de metadata del Case Study (Rol / Período /
 * Herramientas). Mismo trazo fino de 16x16 y currentColor que
 * es_process_icon_library() — no es una librería nueva, es el mismo
 * lenguaje con las 3 figuras que esta caja necesita. Siempre
 * aria-hidden en el markup: el <dt> de al lado ya nombra el campo, así
 * que el ícono no aporta nada a un lector de pantalla.
 *
 * Whitelist cerrada por clave interna del template ('role'/'period'/
 * 'tools'), nunca contenido del admin — cero superficie de XSS.
 *
 * @param string $key Clave del campo de metadata.
 * @return string Markup SVG, o '' si la clave no está en la whitelist.
 */
function es_case_meta_icon( $key ) {
	$stroke = 'fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"';
	$icons  = array(
		// persona: rol en el proyecto
		'role'   => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . ' aria-hidden="true" focusable="false"><circle cx="8" cy="5.4" r="2.6"/><path d="M3.2 13.4a4.8 4.8 0 0 1 9.6 0"/></svg>',
		// calendario: período
		'period' => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . ' aria-hidden="true" focusable="false"><rect x="2.4" y="3.4" width="11.2" height="10.2" rx="1.2"/><path d="M2.4 6.6h11.2M5.6 2.2v2.4M10.4 2.2v2.4"/></svg>',
		// llave: herramientas (mismo dibujo que 'tool' de How I Work)
		'tools'  => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . ' aria-hidden="true" focusable="false"><path d="M9.7 3.3a2.6 2.6 0 0 0-3.4 3.4L2.9 10l1.6 1.6 3.3-3.4a2.6 2.6 0 0 0 3.4-3.4l-1.5 1.5-1.2-1.2Z"/></svg>',
	);
	return isset( $icons[ $key ] ) ? $icons[ $key ] : '';
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
			'title'    => 'Understand the system',
			'text'     => 'Before I open any design tool, I map how the system actually operates: the people, the information, the constraints and the product goals involved.',
			'icon_key' => 'compass',
			'why'      => 'A screen is the visible 1% of a system. Redesigning it without understanding the other 99% just makes the same problem look better.',
			'example'  => 'On Presupuestador, the internal quoting system, that meant sitting on the shop floor to see how a quote actually got built, with paper, memory and a spreadsheet nobody trusted, long before assuming the fix was a better form.',
			'tools'    => 'Contextual inquiry, stakeholder mapping, process observation',
		),
		array(
			'title'    => 'Find the real problem',
			'text'     => 'Every system has one point where things actually break. I look for that specific point, not a list of general issues.',
			'icon_key' => 'target',
			'why'      => 'Teams often ask for a redesign when the real problem sits one step earlier or later. Naming the actual bottleneck precisely is what keeps the rest of the work from becoming decoration.',
			'example'  => 'In Trazur, the interface had real usability issues, but the deeper problem was trust: people didn\'t believe the platform understood their situation, so they disengaged before the screen ever became the problem.',
			'tools'    => 'Root-cause analysis, journey mapping, structured evaluation',
		),
		array(
			'title'    => 'Gather evidence',
			'text'     => 'I build a case before I build a solution: user research a team can react to, not an opinion they have to accept.',
			'icon_key' => 'document',
			'why'      => 'A strong opinion in the room isn\'t evidence, no matter how senior it comes from. Evidence turns a debate about taste into a decision about the system.',
			'example'  => 'For Trazur, that meant pairing traditional research with AI-assisted analysis to process a heavier volume of evidence faster, with every synthesized finding still reviewed by a person before it counted as one.',
			'tools'    => 'Interviews, field observation, AI-assisted synthesis',
		),
		array(
			'title'    => 'Explore and define',
			'text'     => 'I turn what I learn into structure: information architecture, user flows and alternatives I can compare before committing to one.',
			'icon_key' => 'check',
			'why'      => 'Every proposal quietly assumes something. Finding that assumption and testing the one that would be expensive to get wrong is cheaper than discovering it after launch.',
			'example'  => 'On Presupuestador, that meant checking with the person who actually prices jobs that a proposed shortcut wasn\'t quietly removing a judgment call they relied on.',
			'tools'    => 'Assumption mapping, information architecture, user flows, comparative testing',
		),
		array(
			'title'    => 'Design and prototype',
			'text'     => 'Not every problem needs a new interface; sometimes the strongest move is fixing what sits underneath it. When it is the right call, I move from structure to interaction and UI: wireframes, prototypes and reusable components when the project justifies them.',
			'icon_key' => 'layers',
			'why'      => 'A solution that only works under perfect conditions doesn\'t survive a busy shop floor or a rural connection that drops mid-session. Practical means it still works on a bad day.',
			'example'  => 'Trazur\'s proposed solution was built around low-connectivity, low-fidelity conditions from the start, instead of assuming a fast connection and a confident, tech-comfortable user.',
			'tools'    => 'Service blueprints, wireframes, prototypes, design systems',
		),
		array(
			'title'    => 'Test, learn and iterate',
			'text'     => 'I treat a first version as a hypothesis, test it with users, and decide in advance what would prove it wrong.',
			'icon_key' => 'rocket',
			'why'      => 'Iteration without a target just produces motion. Knowing what "wrong" looks like before you ship is what makes the next version actually better, not just different.',
			'example'  => 'Across projects, from Presupuestador to institutional work at Ceibal, the versions that held up were the ones designed to be revisited on purpose, not the ones treated as finished at delivery.',
			'tools'    => 'Usage review, structured feedback loops, versioned documentation',
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
 * Defaults del teaser de "How I Work" en Home (docs/HOW-I-WORK-CONTENT-
 * SPEC.md §1A) — deliberadamente NO derivados de es_home_process_steps():
 * el teaser agrupa los 6 pasos en 3 ideas con copy propio, escrito para
 * ser más corto/genérico que cualquier paso individual, no una selección
 * de frases de los pasos. Mismo criterio de "Home nunca se rompe": si
 * algún día se agrega un campo editable para esto, cae acá como default.
 *
 * @return array{headline:string,lead:string,groups:array<int,array{title:string,text:string}>}
 */
function es_home_process_teaser_defaults() {
	return array(
		'headline' => "I don't start with interfaces. I start by understanding the system.",
		'lead'     => 'The result should make sense for the people using it, and for the system that has to carry it.',
		'groups'   => array(
			array(
				'title' => 'Understand',
				'text'  => 'See how people, information and product goals actually connect.',
			),
			array(
				'title' => 'Explore',
				'text'  => 'Shape the structure, weigh alternatives and challenge assumptions before committing to one.',
			),
			array(
				'title' => 'Iterate',
				'text'  => 'Prototype, validate with users and refine until the solution works and holds up in real use.',
			),
		),
	);
}

/**
 * El teaser ya pasado por el filtro 'es_home_process_teaser' (con los
 * defaults de arriba) — mismo patrón que es_home_process_steps().
 *
 * @return array
 */
function es_home_process_teaser() {
	return apply_filters( 'es_home_process_teaser', es_home_process_teaser_defaults() );
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
			'xmlns'           => true,
			'width'           => true,
			'height'          => true,
			'viewbox'         => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'aria-hidden'     => true,
			'focusable'       => true,
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
			'd'         => true,
			'fill'      => true,
			'fill-rule' => true,
			'clip-rule' => true,
			'class'     => true,
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
 * Imprime una entrada de Experience (Selected o Previous — mismo esquema,
 * $compact solo cambia la clase modificadora para el tratamiento visual
 * más chico de Previous Experience). Vive acá (no en el template-part)
 * para no redeclarar una función cada vez que about-content.php se
 * incluye — mismo criterio que el resto de los helpers de render de este
 * archivo (es_process_step_icon_markup(), etc.).
 *
 * Cada bullet de 'contributions' vive en un <details> nativo propio ("Key
 * contributions") — colapsado por default, mismo componente que agrupa
 * Previous Experience/Other Certifications enteras (.es-about-details,
 * ver assets/css/pages.css).
 *
 * @param array $es_exp  Una fila de es_about_experience_{selected,previous}_defaults().
 * @param bool  $compact True para Previous Experience (tratamiento más chico).
 */
function es_about_render_experience_item( $es_exp, $compact = false ) {
	if ( empty( $es_exp['org'] ) ) {
		return;
	}
	$es_meta = array_filter( array( $es_exp['location'] ?? '', $es_exp['period'] ?? '' ) );
	?>
	<article class="es-exp-item<?php echo $compact ? ' es-exp-item--compact' : ''; ?>" data-es-reveal>
		<div class="es-exp-item__head">
			<?php if ( ! empty( $es_exp['role'] ) ) : ?>
				<h3 class="es-exp-item__role"><?php echo esc_html( $es_exp['role'] ); ?></h3>
			<?php endif; ?>
			<span class="es-exp-item__org"><?php echo esc_html( $es_exp['org'] ); ?></span>
		</div>
		<?php if ( ! empty( $es_meta ) ) : ?>
			<div class="es-exp-item__meta"><?php echo esc_html( implode( ' · ', $es_meta ) ); ?></div>
		<?php endif; ?>
		<?php if ( ! empty( $es_exp['summary'] ) ) : ?>
			<p class="es-exp-item__summary"><?php echo esc_html( $es_exp['summary'] ); ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $es_exp['contributions'] ) && is_array( $es_exp['contributions'] ) ) : ?>
			<details class="es-about-details es-about-details--nested">
				<summary><?php esc_html_e( 'Key contributions', 'estavillo-child' ); ?></summary>
				<div class="es-about-details__body">
					<ul class="es-about-contributions">
						<?php foreach ( $es_exp['contributions'] as $es_line ) : ?>
							<li><?php echo esc_html( $es_line ); ?></li>
						<?php endforeach; ?>
					</ul>
					<?php if ( ! empty( $es_exp['tools'] ) && is_array( $es_exp['tools'] ) ) : ?>
						<p class="es-about-tools">
							<span class="es-about-tools__label"><?php echo esc_html( es__( 'case_meta_tools' ) ); ?></span>
							<span class="es-about-tools__list"><?php echo esc_html( implode( ' · ', $es_exp['tools'] ) ); ?></span>
						</p>
					<?php endif; ?>
				</div>
			</details>
		<?php endif; ?>
		<?php if ( ! empty( $es_exp['link_url'] ) ) : ?>
			<a class="es-exp-item__link" href="<?php echo esc_url( $es_exp['link_url'] ); ?>">
				<?php echo esc_html( ! empty( $es_exp['link_label'] ) ? $es_exp['link_label'] : __( 'View case study', 'estavillo-child' ) ); ?>
				<span aria-hidden="true">&rarr;</span>
			</a>
		<?php endif; ?>
	</article>
	<?php
}

/**
 * Claves canónicas de la librería de hobby-icons: el artwork final
 * APROBADO (estavillo-hobby-icons.zip), instalado como archivos en
 * assets/icons/{clave}.svg. Los archivos son la fuente de verdad —
 * dibujo a mano en paths RELLENOS (fill-rule evenodd), no la línea
 * 1.4px de los process icons — y NO deben redibujarse ni pasarse por
 * optimizadores: en la integración solo se normalizó metadata (viewBox
 * cuadrado centrado, fill="black" → currentColor, aria-hidden) sin
 * tocar un solo punto de la geometría.
 *
 * @return string[] claves en el orden canónico del set.
 */
function es_hobby_icon_keys() {
	return array( 'taekwondo', 'guitar', 'coffee', 'horse-head', 'horse-run', 'drawing', 'travel', 'cinema' );
}

/**
 * Alias de claves legacy (la librería inline 20×20 de la sprint de
 * infra/polish) → clave del artwork aprobado. Un hobby guardado en la
 * base con la clave vieja sigue renderizando sin reconfigurar nada;
 * el <select> de wp-admin también resuelve el alias antes de marcar
 * la opción seleccionada.
 *
 * @param string $key Clave guardada (vieja o nueva).
 * @return string Clave canónica dentro de es_hobby_icon_keys().
 */
function es_hobby_icon_resolve_key( $key ) {
	$aliases = array(
		'music' => 'guitar',
		'horse' => 'horse-head',
	);
	return isset( $aliases[ $key ] ) ? $aliases[ $key ] : $key;
}

/**
 * Librería de hobby-icons: clave => markup SVG completo, leído de
 * assets/icons/{clave}.svg con cache estática por request (8 lecturas
 * chicas como máximo, una sola vez). Si un archivo falta, la clave
 * simplemente no aparece — la plantilla ya maneja el ícono vacío.
 *
 * La micro-interacción de hover/focus vive en assets/css/pages.css
 * (".es-hobby-icon", V1: translateY + acento — nunca autoplay/loop, y
 * cubierta por la regla global de prefers-reduced-motion en base.css).
 *
 * @return array<string,string> clave => markup SVG completo.
 */
function es_hobby_icon_library() {
	static $library = null;
	if ( null !== $library ) {
		return $library;
	}
	$library = array();
	$dir     = get_stylesheet_directory() . '/assets/icons/';
	foreach ( es_hobby_icon_keys() as $key ) {
		$file = $dir . $key . '.svg';
		if ( is_readable( $file ) ) {
			$svg = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- archivo local del propio tema, no remoto.
			if ( is_string( $svg ) && '' !== $svg ) {
				$library[ $key ] = trim( $svg );
			}
		}
	}
	return $library;
}

/**
 * Choices para el <select> de ícono por hobby en Home Content (wp-admin).
 * Solo las claves canónicas del artwork aprobado — los alias legacy no
 * se ofrecen como opción nueva, solo se resuelven al leer.
 *
 * @return array<string,string> clave => label legible.
 */
function es_hobby_icon_choices() {
	return array(
		'taekwondo'  => __( 'Taekwondo', 'estavillo-child' ),
		'guitar'     => __( 'Guitar / Rock Music', 'estavillo-child' ),
		'coffee'     => __( 'Coffee', 'estavillo-child' ),
		'horse-head' => __( 'Horse Head', 'estavillo-child' ),
		'horse-run'  => __( 'Horse Running', 'estavillo-child' ),
		'drawing'    => __( 'Drawing', 'estavillo-child' ),
		'travel'     => __( 'Travel', 'estavillo-child' ),
		'cinema'     => __( 'Cinema', 'estavillo-child' ),
	);
}

/**
 * Markup SVG de un hobby-icon por clave (resolviendo alias legacy), o
 * cadena vacía si no existe en la librería.
 *
 * @param string $key Clave dentro de es_hobby_icon_keys() o un alias.
 * @return string
 */
function es_hobby_icon_svg( $key ) {
	$key     = es_hobby_icon_resolve_key( $key );
	$library = es_hobby_icon_library();
	return isset( $library[ $key ] ) ? $library[ $key ] : '';
}

/**
 * Íconos de método de contacto (Connect) — misma mecánica de archivo +
 * cache estática que es_hobby_icon_library(), pero deliberadamente NO es
 * la misma librería: acá la clave→ícono es fija (email siempre usa
 * email.svg, nunca elegible desde wp-admin), así que no hace falta
 * choices()/resolve_key() para alias — un select de icono por fila no
 * tendría sentido cuando cada fila ya sabe cuál es su propio ícono.
 *
 * @return string[] clave canónica del método de contacto.
 */
function es_connect_icon_keys() {
	return array( 'email', 'phone', 'whatsapp', 'linkedin', 'instagram', 'location' );
}

/**
 * @return array<string,string> clave => markup SVG completo, leído de
 *                               assets/icons/{clave}.svg.
 */
function es_connect_icon_library() {
	static $library = null;
	if ( null !== $library ) {
		return $library;
	}
	$library = array();
	$dir     = get_stylesheet_directory() . '/assets/icons/';
	foreach ( es_connect_icon_keys() as $key ) {
		$file = $dir . $key . '.svg';
		if ( is_readable( $file ) ) {
			$svg = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- archivo local del propio tema, no remoto.
			if ( is_string( $svg ) && '' !== $svg ) {
				$library[ $key ] = trim( $svg );
			}
		}
	}
	return $library;
}

/**
 * Markup SVG de un ícono de contacto por clave, o cadena vacía si no
 * existe.
 *
 * @param string $key Una de es_connect_icon_keys().
 * @return string
 */
function es_connect_icon_svg( $key ) {
	$library = es_connect_icon_library();
	return isset( $library[ $key ] ) ? $library[ $key ] : '';
}

/**
 * Defaults de "Hobbies & interests" (About) — 9 items confirmados en
 * docs/about-page-authoritative-source.md §8 (reemplaza los 7 anteriores;
 * 3 quedan renombrados — "Horse riding"→"Horses", "Travel"→"Travelling",
 * "Cinema"→"Movies & Series" — mismo hobby real, wording del documento
 * fuente). "Gaming" y "Photography" son nuevos y no tienen ícono en la
 * librería curada (es_hobby_icon_choices() — 8 claves, ninguna de
 * gaming/fotografía); el documento fuente pide "keep the current approved
 * icon system", así que no se dibuja arte nuevo acá — quedan con
 * icon:'', que ya cae en el estado --empty existente del template (mismo
 * mecanismo que cualquier hobby sin ícono asignado desde wp-admin). El
 * texto corto se preserva para los 7 hobbies que ya lo tenían (adaptado a
 * la nueva label donde cambió); Gaming/Photography quedan sin texto corto
 * — no había nada aprobado que reusar para esos dos.
 *
 * @return array<int,array{label:string,icon:string,text:string,show:bool}>
 */
function es_home_about_hobbies_defaults() {
	return array(
		array(
			'label' => 'Horses',
			'icon'  => 'horse-head',
			'text'  => 'One of the few things that gets me fully off-screen.',
			'show'  => true,
		),
		array(
			'label' => 'Gaming',
			'icon'  => '',
			'text'  => '',
			'show'  => true,
		),
		array(
			'label' => 'Movies & Series',
			'icon'  => 'cinema',
			'text'  => 'A steady source of pacing and structure, outside of any brief.',
			'show'  => true,
		),
		array(
			'label' => 'Travelling',
			'icon'  => 'travel',
			'text'  => 'New places, mostly for the systems and details other people don\'t notice.',
			'show'  => true,
		),
		array(
			'label' => 'Music',
			'icon'  => 'guitar',
			'text'  => 'Guitar, mostly rock — another way of building something out of structure and repetition.',
			'show'  => true,
		),
		array(
			'label' => 'Drawing',
			'icon'  => 'drawing',
			'text'  => 'Where Industrial Design started for me — still just for the habit of it.',
			'show'  => true,
		),
		array(
			'label' => 'Taekwondo',
			'icon'  => 'taekwondo',
			'text'  => 'Years of practice; the same discipline and repetition show up in how I work.',
			'show'  => true,
		),
		array(
			'label' => 'Photography',
			'icon'  => '',
			'text'  => '',
			'show'  => true,
		),
		array(
			'label' => 'Coffee',
			'icon'  => 'coffee',
			'text'  => 'A daily ritual, not a productivity hack.',
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
 * Copy de intro por defecto de la página About — texto directo, personal,
 * no defensivo (reemplaza el copy anterior "not a past title... the
 * foundation this practice still runs on", reportado como demasiado
 * conceptual/defensivo en la ticket de corrección). 4 párrafos separados
 * por línea en blanco — ver es_about_render_intro_paragraphs() más abajo
 * para cómo esos saltos se convierten en <p> independientes en vez de
 * colapsar en un solo bloque de texto.
 *
 * @return string
 */
function es_about_intro_default() {
	return "Hi, I'm Ramiro Estavillo, a Product Designer and Industrial Designer based in Montevideo.\n\nI hold a Bachelor's Degree in Industrial Design, with a Product Design orientation, and I apply that foundation to digital products, services and operational systems.\n\nMy work usually begins by understanding how people, information and decisions move through a system — not only what happens on the screen. I combine research, systems thinking and practical implementation to turn complex processes into clearer, more usable solutions.\n\nI also use AI to accelerate research synthesis, documentation and exploration, while keeping design decisions grounded in human judgment and real-world context.";
}

/**
 * Divide el texto de intro de About en párrafos (separados por línea en
 * blanco en el valor guardado/default) — el <textarea> del admin guarda
 * saltos de línea reales (sanitize_textarea_field() los preserva), pero
 * imprimirlos dentro de un solo <p> los colapsa visualmente a un bloque
 * (whitespace collapsing de HTML). Esta función es la única responsable
 * de esa división, para no repetir la regex en el template-part.
 *
 * @param string $text Texto crudo (default o guardado vía es_home_about_text).
 * @return string[] Párrafos ya recortados, sin líneas vacías.
 */
function es_about_intro_paragraphs( $text ) {
	if ( '' === trim( (string) $text ) ) {
		return array();
	}
	$paragraphs = preg_split( '/\r\n\r\n|\n\n+|\r\r+/', trim( $text ) );
	return array_values( array_filter( array_map( 'trim', $paragraphs ) ) );
}

/**
 * Defaults de "Selected Experience" y "Previous Experience" (About page).
 *
 * Fuente: docs/about-page-authoritative-source.md — documento agregado
 * por el project owner como fuente de verdad explícita para toda la
 * historia profesional del About page, tras dos tickets previos donde
 * quedó claro que reconstruir esto desde el resto del repo era
 * insuficiente. Cada campo de cada entrada de abajo viene literal de ese
 * documento — no se resumió de memoria ni se sustituyó por copy genérico.
 *
 * 'contributions' es un array de líneas (una bullet por línea en el
 * admin — textarea, split por salto de línea) para la lista "Key
 * contributions" que va dentro de un <details> propio por entrada — ver
 * about-content.php. 'link_url'/'link_label' quedan vacíos en los 3 casos
 * de Selected Experience: el documento fuente dice "use el link existente
 * si ya existe" pero ningún Case Study está publicado todavía (sin acceso
 * a wp-admin en este entorno), así que no hay URL real que enlazar
 * todavía — no se inventa una.
 *
 * @return array<int,array{org:string,role:string,location:string,period:string,summary:string,contributions:string[],tools:string[],link_label:string,link_url:string}>
 */
function es_about_experience_selected_defaults() {
	return array(
		array(
			'org'           => 'Guzmán Villalba',
			'role'          => 'Lead Product Designer',
			'location'      => 'Uruguay',
			'period'        => '2025–Present',
			'summary'       => 'Designing and evolving an internal quoting and operational system for a custom metalwork workshop. The work focuses on turning tacit knowledge, fragmented information and owner-dependent decisions into a more consistent, traceable and usable process.',
			'contributions' => array(
				'Mapped the existing quoting workflow across WhatsApp, email, spreadsheets, Trello, physical documents and workshop conversations.',
				'Identified bottlenecks caused by undocumented criteria, scattered information and constant validation dependencies.',
				'Designed and iterated an internal quoting system using Google Sheets, Apps Script and AI-assisted workflows.',
				'Structured categories, price ranges, labor units, placement criteria and reusable estimating rules.',
				'Connected Product Design, Service Design and operational thinking in a physical-business environment.',
				'Improved the documentation of suppliers, materials, decisions and quoting criteria.',
				'Defined a progressive roadmap from a quick-estimate MVP toward a broader project and approval system.',
				'Documented the work as an evolving Product Design case study.',
			),
			'tools'         => array( 'Figma', 'Claude', 'ChatGPT', 'Codex', 'VS Code' ),
			'link_label'    => '',
			'link_url'      => '',
		),
		array(
			'org'           => 'Trazur',
			'role'          => 'Product Designer & UX Researcher',
			'location'      => 'Montevideo, Uruguay',
			'period'        => '2010–Present',
			'summary'       => 'Long-term work across the digital experience, online-course platform and communication ecosystem of an agricultural traceability and training business. The role evolved from web and content execution toward Product Design, UX Research and service improvement.',
			'contributions' => array(
				'Designed and maintained the company website in WordPress.',
				'Developed user flows and wireframes for the digital experience.',
				'Conducted usability testing and user interviews.',
				'Studied the needs and barriers of rural users in relation to technology.',
				'Supported the implementation and operation of online courses.',
				'Helped manage enrollment, student support and technical issues related to the courses.',
				'Created and maintained digital content across the website and social channels.',
				'Analyzed competitors and opportunities for improving the service.',
				'Worked on the redesign of the e-learning experience through UX Research.',
				'Used AI-assisted synthesis as part of the research process, with human review and validation.',
				'Explored how connectivity, trust and digital confidence affect adoption among rural users.',
				'Contributed to the final degree project focused on the redesign of an e-learning platform for livestock traceability (not sole authorship — a collaborative thesis project).',
			),
			'tools'         => array( 'WordPress', 'Figma', 'ChatGPT' ),
			'link_label'    => '',
			'link_url'      => '',
		),
		array(
			'org'           => 'Ceibal',
			'role'          => 'Project Manager',
			'location'      => 'Montevideo, Uruguay',
			'period'        => '2023–2024',
			'summary'       => "Led and coordinated digital projects for Ceibal's institutional portal and related initiatives, working with cross-functional teams, internal stakeholders and external vendors.",
			'contributions' => array(
				'Managed multiple digital projects, including Portal Ceibal, transparency initiatives and mailing systems.',
				'Developed and maintained project plans, timelines and roadmaps.',
				'Coordinated cross-functional teams in agile environments using Scrum and Kanban.',
				'Worked with internal stakeholders and external vendors.',
				'Prioritized high-volume tasks and competing project demands.',
				'Supported iterative development and continuous improvement.',
				'Conducted risk assessment and followed corrective actions.',
				'Communicated project progress with stakeholders.',
				'Helped improve workflows and operational coordination.',
			),
			'tools'         => array( 'Redmine', 'Trello', 'Miro' ),
			'link_label'    => '',
			'link_url'      => '',
		),
	);
}

/**
 * Defaults de "Previous Experience" (About page) — mismo esquema que
 * Selected Experience, mismo origen (docs/about-page-authoritative-source.md).
 * "Master" y "FUMS" (que aparecían en una ticket anterior, superada) NO
 * están acá — el documento fuente dice explícitamente que no forman parte
 * de la historia real del About page.
 *
 * Samic SA incluye su "role progression" (3 fases fechadas) como las
 * primeras 3 líneas de 'contributions', en vez de un sub-esquema anidado
 * nuevo — mismo campo, mismo tratamiento visual, sin inventar UI nueva
 * para un caso único.
 *
 * @return array<int,array{org:string,role:string,location:string,period:string,summary:string,contributions:string[],tools:string[],link_label:string,link_url:string}>
 */
function es_about_experience_previous_defaults() {
	return array(
		array(
			'org'           => 'Verona Office & Home',
			'role'          => 'Sales Supervisor and Administrative Organizer',
			'location'      => 'Montevideo, Uruguay',
			'period'        => '2021–2022',
			'summary'       => 'Supervised the sales team and helped organize internal processes, identifying problems across sales, administration, marketing, logistics and warehouse operations.',
			'contributions' => array(
				'Identified pain points in sales and internal business processes.',
				'Conducted questionnaires to understand public awareness of the company and identify marketing gaps.',
				'Interviewed employees to understand internal problems and operational friction.',
				'Facilitated brainstorming sessions with the sales team.',
				'Proposed, prioritized and implemented process improvements.',
				'Led the sales team.',
				'Created guides, protocols and working methodologies.',
				'Planned marketing actions across social media, showroom and website.',
				'Studied competitors and market positioning.',
				'Implemented, taught and monitored a CRM for the sales team.',
				'Implemented, taught and monitored the administrative software.',
				'Identified and addressed logistics and warehouse problems.',
			),
			// Sin 'tools': el documento fuente no nombra ningún software
			// específico para este rol ("a CRM", "administrative software" —
			// ambos genéricos, sin marca) — no se inventa un nombre de
			// producto que el documento no confirma.
			'tools'         => array(),
			'link_label'    => '',
			'link_url'      => '',
		),
		array(
			'org'           => 'Samic SA',
			'role'          => 'Web & Graphic Designer / Store Manager',
			'location'      => 'Montevideo, Uruguay',
			'period'        => '2015–2021',
			'summary'       => 'A multidisciplinary role spanning graphic design, web design, e-commerce, content, communication and business operations. This experience became an important foundation for later Product Design work.',
			'contributions' => array(
				'2015–2016: Flooring estimator and graphic designer; identified the need for a dedicated design and communication function.',
				'2016–2021: Web and graphic designer.',
				'2018–2021: General manager, applying planning methods influenced by Design Thinking.',
				'Identified business needs related to sales, communication and customer acquisition.',
				'Recognized that many customers were far from the physical store and helped create new digital sales channels.',
				'Designed user flows, navigation architecture and e-commerce wireframes.',
				'Prototyped, implemented and maintained the e-commerce experience in WordPress and WooCommerce.',
				'Worked with HTML and CSS where necessary.',
				'Conducted usability testing with internal and external users.',
				'Iterated the experience across web, marketplace and social platforms.',
				'Managed product publishing and catalog content.',
				'Created graphic and digital content for platforms and social channels.',
				'Collaborated with the creative director on communication materials.',
				'Produced visual, video, GIF and motion content where required.',
				'Planned processes, guidelines and internal improvements across administration, sales and logistics.',
				'Used Mailchimp for newsletter campaigns.',
				'Contributed to a new digital sales channel, improved shopping experience and more organized operations.',
			),
			'tools'         => array( 'Photoshop', 'Illustrator', 'Figma', 'WordPress', 'Mailchimp' ),
			'link_label'    => '',
			'link_url'      => '',
		),
		array(
			'org'           => 'Fupsi.org',
			'role'          => 'Webmaster / Freelancer',
			'location'      => 'Montevideo, Uruguay',
			'period'        => '2012–2018',
			'summary'       => "Redesigned and maintained the organization's visual identity, WordPress website and newsletter communication.",
			'contributions' => array(
				'Redesigned the corporate identity.',
				'Redesigned and maintained the WordPress website.',
				'Sent newsletter campaigns with Mailchimp.',
				'Conducted usability tests and satisfaction surveys.',
				'Developed user flows and wireframes.',
				'Implemented and maintained the website.',
				'Improved the user experience across digital touchpoints over time.',
			),
			'tools'         => array( 'WordPress', 'Mailchimp' ),
			'link_label'    => '',
			'link_url'      => '',
		),
	);
}

/**
 * Defaults de "Education & certificates" (About page) — esquema ampliado
 * respecto de la versión anterior (title/org/year) para soportar todo lo
 * que pide docs/about-page-authoritative-source.md §5: orientación,
 * facultad/escuela, período de estudio distinto del año de emisión
 * oficial, descripción y proyecto final de grado. 'year' es el año de
 * EMISIÓN oficial del título (2026 — el documento fuente es explícito:
 * "Do not say the degree is pending issuance. The degree is completed and
 * officially issued."), no el año de inicio/fin de cursada (eso vive en
 * 'period', 2008–2025, dentro de 'description'). 'link' queda vacío para
 * las dos entradas: ningún link de credencial verificado existe en el
 * repo para ninguna de las dos.
 *
 * @return array<int,array{title:string,org:string,meta:string,year:string,description:string,final_project:string,link:string}>
 */
function es_about_education_defaults() {
	return array(
		array(
			'title'         => "Bachelor's Degree in Industrial Design — Product Design orientation",
			'org'           => 'Universidad de la República (UdelaR)',
			'meta'          => 'Facultad de Arquitectura, Diseño y Urbanismo (FADU) · Escuela Universitaria Centro de Diseño (EUCD), Uruguay',
			'year'          => '2026',
			'description'   => 'Product-oriented education focused on design methodology, systems thinking, manufacturing, ergonomics, research and user-centered design. Studied 2008–2025; degree officially completed and issued in 2026.',
			'final_project' => 'Final degree project: "Analysis of an E-learning Platform for Livestock Traceability through UX Research and Artificial Intelligence."',
			'link'          => '',
		),
		array(
			'title'         => 'Google UX Design Professional Certificate',
			'org'           => 'Google · Coursera',
			'meta'          => '',
			'year'          => '2026',
			'description'   => '',
			'final_project' => '',
			'link'          => '',
		),
	);
}

/**
 * Defaults de "Other Certifications" (About page) — grupo secundario
 * colapsado (ver about-content.php). Los 8 items vienen literales de
 * docs/about-page-authoritative-source.md §6. Ningún link de credencial
 * existe en el repo para ninguno — 'link' queda vacío en los 8, nunca
 * inventado. El ítem 6 (Figma for UX Design) no trae Credential ID en el
 * documento fuente — queda vacío, no se inventa uno.
 *
 * @return array<int,array{name:string,issuer:string,date:string,credential_id:string,link:string}>
 */
function es_about_certifications_other_defaults() {
	return array(
		array(
			'name'          => "User Experience: The Beginner's Guide",
			'issuer'        => 'Interaction Design Foundation',
			'date'          => 'August 2022',
			'credential_id' => '133455',
			'link'          => '',
		),
		array(
			'name'          => 'Design Thinking: The Ultimate Guide',
			'issuer'        => 'Interaction Design Foundation',
			'date'          => 'September 2022',
			'credential_id' => '133455',
			'link'          => '',
		),
		array(
			'name'          => 'Foundations of User Experience (UX) Design',
			'issuer'        => 'Google · Coursera',
			'date'          => 'June 2022',
			'credential_id' => 'UVWFHTTRTX',
			'link'          => '',
		),
		array(
			'name'          => 'Agile with Atlassian Jira',
			'issuer'        => 'Atlassian University · Coursera',
			'date'          => 'July 2022',
			'credential_id' => 'NFV95GJGQZ5E',
			'link'          => '',
		),
		array(
			'name'          => 'Introduction to UI Design',
			'issuer'        => 'University of Minnesota · Coursera',
			'date'          => 'August 2022',
			'credential_id' => '2T7RU7RZBXEH',
			'link'          => '',
		),
		array(
			'name'          => 'Figma for UX Design',
			'issuer'        => 'LinkedIn Learning',
			'date'          => 'June 2022',
			'credential_id' => '',
			'link'          => '',
		),
		array(
			'name'          => 'Introduction to C# Programming and Unity',
			'issuer'        => 'University of Colorado System · Coursera',
			'date'          => 'August 2022',
			'credential_id' => 'FP7749Y8DSBJ',
			'link'          => '',
		),
		array(
			'name'          => 'Introduction to Video Game Development with Unity',
			'issuer'        => 'Universitat Politècnica de València · edX',
			'date'          => 'July 2022',
			'credential_id' => '86c1923e49464142ac08e9fdbce62ce2',
			'link'          => '',
		),
	);
}

/**
 * Defaults de "Languages" (About page) — confirmados por el project owner
 * vía docs/about-page-authoritative-source.md §7 ("Advanced (C1)" es más
 * específico que el "Advanced" plano de la ticket anterior — el documento
 * fuente gana).
 *
 * @return array<int,array{name:string,level:string}>
 */
function es_about_languages_defaults() {
	return array(
		array(
			'name'  => 'Spanish',
			'level' => 'Native',
		),
		array(
			'name'  => 'English',
			'level' => 'Advanced (C1)',
		),
		array(
			'name'  => 'Portuguese',
			'level' => 'Basic',
		),
	);
}

/**
 * Defaults de "Tools" (About page) — sección de cierre que ahora renderiza
 * el bloque reutilizable estavillo/tools (Design System ticket): esta
 * función sólo sigue siendo la FUENTE DE DATOS (misma responsabilidad que
 * es_about_languages_defaults() etc.), no dibuja nada — about-content.php
 * pasa este array directo como 'groups' a render_block(), el mismo camino
 * que usa el contenido Gutenberg real. Una sola implementación del bloque,
 * dos orígenes de datos (este default PHP y los atributos guardados en
 * Gutenberg), igual que el resto de las secciones editables del About.
 *
 * No es un inventario de todo lo que se usó alguna vez (eso ya lo cuenta
 * cada 'tools' de Experience) — es lo que se maneja HOY. 'icon' es una
 * clave de es_process_icon_library(); 'categoryDescription' queda vacía a
 * propósito (atributo preparado para una evolución futura del bloque, no
 * usado todavía — ver estavillo-portfolio-core/blocks/tools/block.json).
 *
 * @return array<int,array{title:string,icon:string,items:string[],categoryDescription:string}>
 */
function es_about_tools_defaults() {
	return array(
		array(
			'title'               => 'Research',
			'icon'                => 'compass',
			'items'               => array( 'Google Analytics', 'Microsoft Clarity', 'Synthetic Users' ),
			'categoryDescription' => '',
		),
		array(
			'title'               => 'Design',
			'icon'                => 'layers',
			// Figma/FigJam/Relume = producto; Photoshop/Illustrator/InDesign/
			// Premiere Pro suman el lado editorial/gráfico — mismo criterio
			// "herramientas reales que uso", no exhaustividad de currículum.
			'items'               => array( 'Figma', 'FigJam', 'Relume', 'Photoshop', 'Illustrator', 'InDesign', 'Premiere Pro' ),
			'categoryDescription' => '',
		),
		array(
			'title'               => 'AI',
			'icon'                => 'bulb',
			'items'               => array( 'Claude', 'Claude Code', 'Claude Cowork', 'ChatGPT', 'Codex' ),
			'categoryDescription' => '',
		),
		/*
		 * Sexta categoría (composición 3×2 ticket): definición de producto,
		 * documentación, organización de procesos y seguimiento de trabajo —
		 * deliberadamente NO "Skills"/soft skills, sigue siendo una lista de
		 * herramientas reales, mismo criterio que el resto de las 5
		 * categorías. Ninguno de estos 4 nombres se repite en otra
		 * categoría (verificado contra las 5 anteriores antes de sumarlos).
		 * Ícono 'flow' (dos nodos conectados): distinto de 'layers'/'tool'/
		 * 'cube' ya usados acá, y ya comunica "proceso/sistema" en el resto
		 * de la librería (case-flow).
		 */
		array(
			'title'               => 'Product & Systems',
			'icon'                => 'flow',
			'items'               => array( 'Notion', 'Trello', 'Google Sheets', 'Apps Script' ),
			'categoryDescription' => '',
		),
		array(
			'title'               => 'Development',
			'icon'                => 'tool',
			'items'               => array( 'VS Code', 'WordPress', 'HTML', 'CSS', 'Git', 'GitHub' ),
			'categoryDescription' => '',
		),
		array(
			'title'               => '3D',
			'icon'                => 'cube',
			// Rhino/AutoCAD: ya estaban acá antes de este ticket. El pedido
			// de sumar "AutoCAD, Rhinoceros" se resuelve NO duplicándolos —
			// ya son estos dos ítems (Rhino = Rhinoceros).
			'items'               => array( 'Rhino', 'AutoCAD' ),
			'categoryDescription' => '',
		),
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
