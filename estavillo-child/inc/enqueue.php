<?php
/**
 * Carga de assets del child theme.
 *
 * Estrategia:
 *  - Los tokens y la base se cargan en todo el sitio (son inertes: solo
 *    definen variables --es-* y estilos bajo .es-page, no pisan Kadence).
 *  - site.css (chrome: header + menú mobile + footer) y motion.js/nav.js
 *    se cargan en la Home Y en el single de Case Study (Sprint 4B):
 *    ambos reusan el mismo header/footer ESTAVILLO.
 *  - hero.css, pages-home.css y hero-system-map.js son SOLO de la Home
 *    (no hay hero en el single de Case Study).
 *  - case-study.css es SOLO del single de Case Study.
 *  - Ninguno de estos assets se carga en el resto del sitio (Kadence
 *    intacto), para no sumar peso donde no hace falta.
 *  - Google Fonts (Newsreader / Instrument Sans / Spline Sans Mono) se
 *    puede desactivar con el filtro 'es_child_load_google_fonts' si
 *    Kadence ya sirve estas fuentes localmente.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ¿Estamos en el template de la home nueva?
 *
 * @return bool
 */
function es_is_home_template() {
	return is_page_template( 'templates/page-home-estavillo.php' );
}

/**
 * ¿Estamos en la página single de un Case Study? No depende de que el
 * plugin "Estavillo Portfolio Core" esté activo: is_singular() con un
 * post_type no registrado simplemente devuelve false, nunca rompe.
 *
 * @return bool
 */
function es_is_case_study_single() {
	return is_singular( 'es_case_study' );
}

/**
 * ¿Estamos en alguna de las páginas fijas ESTAVILLO (Work / About / How I
 * Work / Contact)? Todas son templates "standalone" igual que Home y el
 * single de Case Study — reusan el mismo chrome (site-header/site-footer)
 * y varias secciones de Home (Selected Work, How I Work, About, Connect),
 * así que comparten la misma condición de assets que esas páginas.
 *
 * @return bool
 */
function es_is_estavillo_static_page() {
	$templates = array(
		'templates/page-work.php',
		'templates/page-about.php',
		'templates/page-how-i-work.php',
		'templates/page-contact.php',
	);
	foreach ( $templates as $template ) {
		if ( is_page_template( $template ) ) {
			return true;
		}
	}
	return false;
}

/**
 * ¿Hay al menos un estavillo/case-figure con el zoom activado en el post
 * actual? A diferencia de Case Flow, acá `has_block()` no alcanza: dice si
 * el BLOQUE existe, no si ESTA instancia tiene `enableZoom` — y el visor
 * (dialog + JS de pan/zoom) es exactamente el tipo de peso que no debería
 * pedirse en las páginas que no lo usan. parse_blocks() sobre el propio
 * post_content ya trae los atributos serializados del bloque, así que no
 * hace falta ningún acoplamiento con el plugin (ni una bandera global
 * seteada desde render.php): el theme lee el mismo dato crudo que
 * Gutenberg guardó.
 *
 * @return bool
 */
function es_post_has_case_figure_zoom() {
	return es_post_has_block_flag( 'estavillo/case-figure', 'enableZoom' );
}

/**
 * ¿Hay al menos un estavillo/case-stats con la animación de conteo activada?
 * Mismo criterio opt-in por instancia que el zoom de case-figure: el bloque
 * existe en casi todos los casos, pero el JS del contador sólo tiene sentido
 * donde alguien prendió el toggle — y es mejora progresiva pura, así que no
 * pedirlo no rompe nada (el número final ya está en el HTML).
 *
 * @return bool
 */
function es_post_has_case_stats_animation() {
	return es_post_has_block_flag( 'estavillo/case-stats', 'animate' );
}

/**
 * ¿El post actual tiene alguna instancia de $block_name con el atributo
 * booleano $attr en true? A diferencia de has_block(), mira los ATRIBUTOS
 * serializados, así que distingue "el bloque está" de "esta instancia pidió
 * la función" — que es lo que decide si vale la pena mandar un asset extra.
 *
 * @param string $block_name Nombre completo del bloque (namespace incluido).
 * @param string $attr       Atributo booleano a comprobar.
 * @return bool
 */
function es_post_has_block_flag( $block_name, $attr ) {
	if ( ! is_singular() ) {
		return false;
	}
	$post = get_post();
	if ( ! $post || false === strpos( (string) $post->post_content, $block_name ) ) {
		return false; // atajo: ni vale la pena parsear si el bloque ni aparece
	}
	return es_blocks_contain_flag( parse_blocks( $post->post_content ), $block_name, $attr );
}

/**
 * Recorre un árbol de bloques (parse_blocks()) buscando $block_name con
 * $attr=true, en cualquier nivel de anidamiento (columnas, grupos…).
 *
 * @param array  $blocks     Árbol de bloques.
 * @param string $block_name Nombre completo del bloque.
 * @param string $attr       Atributo booleano a comprobar.
 * @return bool
 */
function es_blocks_contain_flag( $blocks, $block_name, $attr ) {
	foreach ( $blocks as $block ) {
		if ( $block_name === ( $block['blockName'] ?? '' ) && ! empty( $block['attrs'][ $attr ] ) ) {
			return true;
		}
		if ( ! empty( $block['innerBlocks'] ) && es_blocks_contain_flag( $block['innerBlocks'], $block_name, $attr ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Versión de cache-bust por archivo: usa filemtime() para que CADA cambio de
 * un asset genere un ?ver= nuevo y el navegador/CDN vuelva a bajarlo.
 *
 * Motivo: con una versión estática (p. ej. ES_CHILD_VERSION fija) al actualizar
 * el tema el HTML del servidor cambia pero el JS/CSS cacheado NO — el hero
 * cliente-side quedaba viejo (sin el motor nuevo) aunque el Customizer emitiera
 * la variante correcta. filemtime evita eso de raíz.
 *
 * @param string $rel Ruta relativa al tema (p. ej. 'assets/js/hero-system-map.js').
 * @return string Versión (mtime como entero, o ES_CHILD_VERSION de fallback).
 */
function es_asset_ver( $rel ) {
	$path = ES_CHILD_DIR . '/' . ltrim( $rel, '/' );
	$mtime = file_exists( $path ) ? filemtime( $path ) : false;
	return $mtime ? (string) $mtime : ES_CHILD_VERSION;
}

/**
 * Registra y encola estilos y scripts.
 */
function es_child_enqueue_assets() {
	$v = ES_CHILD_VERSION;

	// Hoja del parent (Kadence encola la suya propia; esto cubre el caso estándar de child theme).
	wp_enqueue_style( 'kadence-parent', get_template_directory_uri() . '/style.css', array(), $v );
	wp_enqueue_style( 'estavillo-child', get_stylesheet_uri(), array( 'kadence-parent' ), es_asset_ver( 'style.css' ) );

	// Tipografías: solo se cargan las Google Fonts si el preset es
	// 'design_system'. Con 'classic_mockup' se usa el stack de sistema y no
	// se pide ninguna web font (más liviano). Igual queda el filtro para
	// forzar el comportamiento si hiciera falta.
	$load_fonts = 'design_system' === es_get_option( 'es_font_preset' );
	if ( apply_filters( 'es_child_load_google_fonts', $load_fonts ) ) {
		wp_enqueue_style(
			'es-fonts',
			'https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,300..700;1,6..72,300..700&family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Spline+Sans+Mono:wght@400;500;600&display=swap',
			array(),
			null
		);
	}

	// Capa global del design system (tokens + base + layout + componentes).
	wp_enqueue_style( 'es-tokens', ES_CHILD_URI . '/assets/css/tokens.css', array( 'estavillo-child' ), es_asset_ver( 'assets/css/tokens.css' ) );
	wp_enqueue_style( 'es-base', ES_CHILD_URI . '/assets/css/base.css', array( 'es-tokens' ), es_asset_ver( 'assets/css/base.css' ) );
	wp_enqueue_style( 'es-layout', ES_CHILD_URI . '/assets/css/layout.css', array( 'es-base' ), es_asset_ver( 'assets/css/layout.css' ) );
	wp_enqueue_style( 'es-components', ES_CHILD_URI . '/assets/css/components.css', array( 'es-layout' ), es_asset_ver( 'assets/css/components.css' ) );

	// Global dark-mode layer (site-wide, every template — Kadence-native
	// contexts included). Inert unless body.es-theme-dark is present (see
	// inc/theme-dark-mode.php): same "always loaded, no-op until its class/
	// scope is active" principle as es-tokens/es-base above. Enqueued last
	// among the theme's own stylesheets so its source order can win ties
	// against Kadence's own CSS for the Kadence-native selectors it targets.
	wp_enqueue_style( 'es-theme-dark', ES_CHILD_URI . '/assets/css/theme-dark.css', array( 'es-components' ), es_asset_ver( 'assets/css/theme-dark.css' ) );

	// Chrome ESTAVILLO (header + menú mobile + footer): lo comparten la
	// Home, el single de Case Study y las 4 páginas fijas nuevas (Work /
	// About / How I Work / Contact) — todas usan template-parts/site-header
	// y site-footer con el mismo markup/JS de menú.
	$es_needs_chrome = es_is_home_template() || es_is_case_study_single() || es_is_estavillo_static_page();

	if ( $es_needs_chrome ) {
		wp_enqueue_style( 'es-site', ES_CHILD_URI . '/assets/css/site.css', array( 'es-components' ), es_asset_ver( 'assets/css/site.css' ) );

		wp_enqueue_script(
			'es-motion',
			ES_CHILD_URI . '/assets/js/motion.js',
			array(),
			es_asset_ver( 'assets/js/motion.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
		wp_enqueue_script(
			'es-nav',
			ES_CHILD_URI . '/assets/js/nav.js',
			array(),
			es_asset_ver( 'assets/js/nav.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}

	// Secciones de Home (Featured / How I Work / Selected Work / About /
	// Connect): la Home Y las 4 páginas fijas nuevas las necesitan, porque
	// Work/About/How I Work/Contact reusan esos mismos template-parts o su
	// mismo lenguaje visual (cards, timeline, process grid, footer CTA) en
	// vez de reinventar estilos nuevos — ver README, sección "Páginas fijas".
	if ( es_is_home_template() || es_is_estavillo_static_page() ) {
		wp_enqueue_style( 'es-pages-home', ES_CHILD_URI . '/assets/css/pages-home.css', array( 'es-site' ), es_asset_ver( 'assets/css/pages-home.css' ) );
	}

	// Capa propia de las 4 páginas fijas nuevas (page-head, separación
	// selected/archive de Work, timeline/educación/hobbies/CV de About,
	// layout de Contact) — nunca se carga en Home ni en Case Study.
	if ( es_is_estavillo_static_page() ) {
		wp_enqueue_style( 'es-pages', ES_CHILD_URI . '/assets/css/pages.css', array( 'es-pages-home' ), es_asset_ver( 'assets/css/pages.css' ) );
	}

	// Capa específica de la home (hero animado) — no se carga en el single
	// de Case Study ni en las páginas fijas nuevas, que no tienen hero.
	if ( es_is_home_template() ) {
		wp_enqueue_style( 'es-hero', ES_CHILD_URI . '/assets/css/hero.css', array( 'es-pages-home' ), es_asset_ver( 'assets/css/hero.css' ) );

		wp_enqueue_script(
			'es-hero-system-map',
			ES_CHILD_URI . '/assets/js/hero-system-map.js',
			array(),
			es_asset_ver( 'assets/js/hero-system-map.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		// Transición de scroll del hero (fade del contenido + linger del
		// fondo + click del indicador). Reduced-motion-safe adentro del
		// propio script. Sin dependencias (lee/escribe una CSS var).
		wp_enqueue_script(
			'es-hero-scroll',
			ES_CHILD_URI . '/assets/js/hero-scroll.js',
			array(),
			es_asset_ver( 'assets/js/hero-scroll.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		// Config del hero expuesta a JS (fuente confiable además del
		// data-attribute del template): la variante seleccionada llega al
		// motor aunque un plugin de caché/optimización reescriba el HTML.
		wp_localize_script(
			'es-hero-system-map',
			'EstavilloHeroConfig',
			array(
				'desktop'    => es_get_option( 'es_hero_variant_desktop' ),
				'mobile'     => es_get_option( 'es_hero_variant_mobile' ),
				'fontPreset' => es_get_option( 'es_font_preset' ),
				'version'    => ES_CHILD_VERSION,
			)
		);
	}

	// Capa específica del single de Case Study (prose body + meta row).
	if ( es_is_case_study_single() ) {
		wp_enqueue_style( 'es-case-study', ES_CHILD_URI . '/assets/css/case-study.css', array( 'es-site' ), es_asset_ver( 'assets/css/case-study.css' ) );

		/*
		 * Índice sticky: scrollspy + riel + botones de desplazamiento. Sólo
		 * si el caso tiene el campo "Case index" cargado — sin ese campo el
		 * <nav> ni siquiera se imprime, así que pedir el script sería peso
		 * muerto. Es 100% mejora progresiva (ver el docblock de
		 * case-index.js): sin él el menú se sigue scrolleando solo.
		 */
		if ( get_post_meta( get_the_ID(), '_es_case_index', true ) ) {
			wp_enqueue_script(
				'es-case-index',
				ES_CHILD_URI . '/assets/js/case-index.js',
				array(),
				es_asset_ver( 'assets/js/case-index.js' ),
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);
		}
	}

	/*
	 * Case Flow (.es-flow): componente del design system, no exclusivo del
	 * Case Study — puede vivir en cualquier página con contenido Gutenberg
	 * (roadmap: User Journey, Service Blueprint, System Diagram...). Por eso
	 * la condición no es "¿es un case study?" sino has_block(): se carga
	 * exactamente donde el bloque está y en ningún otro lado, sin importar
	 * la plantilla. El JS es 100% opcional (progressive enhancement — ver el
	 * docblock de case-flow.js): si no cargara, el flujo se sigue leyendo.
	 */
	$es_has_flow_v1 = is_singular() && has_block( 'estavillo/case-flow' );
	$es_has_flow_v2 = is_singular() && has_block( 'estavillo/case-flow-v2' );

	if ( $es_has_flow_v1 || $es_has_flow_v2 ) {
		wp_enqueue_style( 'es-case-flow', ES_CHILD_URI . '/assets/css/case-flow.css', array( 'es-components' ), es_asset_ver( 'assets/css/case-flow.css' ) );
		wp_enqueue_script(
			'es-case-flow',
			ES_CHILD_URI . '/assets/js/case-flow.js',
			array(),
			es_asset_ver( 'assets/js/case-flow.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}

	/*
	 * La v2 son SÓLO los deltas de geometría de los conectores: se declara
	 * dependiente de 'es-case-flow' para que el orden de carga quede
	 * garantizado por WordPress y no por el orden de estas líneas. El JS es
	 * el mismo de la v1 (mismos data-attributes), así que no hay un segundo
	 * script que mantener.
	 */
	if ( $es_has_flow_v2 ) {
		wp_enqueue_style( 'es-case-flow-v2', ES_CHILD_URI . '/assets/css/case-flow-v2.css', array( 'es-case-flow' ), es_asset_ver( 'assets/css/case-flow-v2.css' ) );
	}

	/*
	 * Case Figure lightbox: visor compartido por página (un solo <dialog>),
	 * opt-in por instancia vía el toggle "Permitir ampliar imagen" del
	 * bloque. Sin ninguna figura con zoom activo, ni el CSS ni el JS se
	 * piden — ver es_post_has_case_figure_zoom() arriba.
	 */
	if ( es_post_has_case_figure_zoom() ) {
		wp_enqueue_style(
			'es-case-figure-lightbox',
			ES_CHILD_URI . '/assets/css/case-figure-lightbox.css',
			array( 'es-components' ),
			es_asset_ver( 'assets/css/case-figure-lightbox.css' )
		);
		wp_enqueue_script(
			'es-case-figure-lightbox',
			ES_CHILD_URI . '/assets/js/case-figure-lightbox.js',
			array(),
			es_asset_ver( 'assets/js/case-figure-lightbox.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}

	/*
	 * Tools (.es-tools): mismo criterio que Case Flow arriba — componente
	 * del Design System, no exclusivo de ninguna página (Home/About/Case
	 * Studies/landings futuras), así que se carga por has_block() y en
	 * ningún otro lado. Único agregado sobre el patrón de Case Flow: el
	 * template de About también dispara la carga aunque has_block() no
	 * encuentre el bloque, porque About tiene una rama de fallback PHP
	 * (template-parts/about-content.php) que renderiza el bloque vía
	 * render_block() directo — sin pasar por post_content real, así que
	 * has_block() nunca lo vería ahí. El is_page_template() de abajo sólo
	 * agrega ESE caso puntual; el bloque en sí sigue sin acoplarse a
	 * ninguna plantilla.
	 */
	$es_needs_tools = is_singular() && ( has_block( 'estavillo/tools' ) || is_page_template( 'templates/page-about.php' ) );

	if ( $es_needs_tools ) {
		wp_enqueue_style( 'es-tools', ES_CHILD_URI . '/assets/css/tools.css', array( 'es-components' ), es_asset_ver( 'assets/css/tools.css' ) );
	}

	/*
	 * Contador de Case Stats: mismo criterio opt-in por instancia que el
	 * lightbox de case-figure arriba — sin ningún bloque con "Animate
	 * numbers" activo, el script no se pide. No hay CSS asociado: el estilo
	 * del bloque (ícono incluido) ya vive en case-study.css, y el contador no
	 * agrega ninguna regla propia. Mejora progresiva: sin este script el
	 * número final ya está impreso en el HTML.
	 */
	if ( es_post_has_case_stats_animation() ) {
		wp_enqueue_script(
			'es-case-stats',
			ES_CHILD_URI . '/assets/js/case-stats.js',
			array(),
			es_asset_ver( 'assets/js/case-stats.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'es_child_enqueue_assets' );
