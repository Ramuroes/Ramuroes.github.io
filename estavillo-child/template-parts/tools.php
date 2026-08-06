<?php
/**
 * Tools — sección de Home (rama LEGACY FALLBACK: Home sin contenido
 * Gutenberg).
 *
 * NO define ningún componente propio: arma los mismos atributos que
 * guardaría Gutenberg y llama a render_block() sobre el bloque reutilizable
 * estavillo/tools del plugin — exactamente el mismo patrón (y la misma
 * fuente de datos) que ya usa la sección Tools de About en
 * template-parts/about-content.php. Un único render.php, un único CSS, un
 * único bloque para About, Home y cualquier página futura. Este archivo no
 * toca esa implementación: About sigue exactamente igual.
 *
 * Layout `inline` a propósito: en Home la sección es un resumen de una
 * línea por categoría, no el detalle vertical del About — es la variante
 * que el propio bloque ya trae para "espacios más chicos" (ver el control
 * Layout en blocks/tools/edit.js). Ningún estilo nuevo en el bloque.
 *
 * Ubicación y jerarquía (revisión de posicionamiento): va DESPUÉS de
 * Featured/Selected Work y ANTES de About — ver es_home_sections() en
 * functions.php, "primero criterio y trabajo, después herramientas". Sin
 * numeración (a diferencia de las demás secciones de Home): no recibe
 * $args['num'] porque page-home-estavillo.php excluye la clave 'tools' del
 * contador, igual que 'hero'. El encabezado usa .es-home-tools__head, una
 * envoltura más chica que .es-section-head (sin el número en acento que
 * le daba protagonismo de "capítulo") — pages-home.css, scopeada a esta
 * sección: no toca .es-section-head en ningún otro lugar del sitio.
 *
 * Los datos salen del mismo filtro que About ('es_about_tools' sobre
 * es_about_tools_defaults()), así que editar Tools en wp-admin actualiza
 * las dos páginas a la vez. Un filtro propio ('es_home_tools') permite
 * desacoplarlas si algún día hiciera falta, sin tocar este archivo.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'es_about_tools_defaults' ) || ! function_exists( 'render_block' ) ) {
	return;
}

/*
 * Mismo dato que About por defecto. El segundo filtro es el punto de
 * desacople: devolver otro array acá cambia sólo Home.
 */
$es_tools = apply_filters( 'es_about_tools', es_about_tools_defaults() );
$es_tools = apply_filters( 'es_home_tools', $es_tools );

if ( empty( $es_tools ) ) {
	return;
}
?>

<section class="es-section es-home-tools" id="tools">
	<div class="es-container">
		<div class="es-home-tools__head" data-es-reveal>
			<h2 class="es-label"><?php echo esc_html( es__( 'case_meta_tools' ) ); ?></h2>
		</div>
		<div data-es-reveal>
			<?php
			echo render_block( // phpcs:ignore WordPress.Security.EscapeOutput -- render_block() devuelve HTML ya escapado por el propio bloque (blocks/tools/render.php).
				array(
					'blockName' => 'estavillo/tools',
					'attrs'     => array(
						'layout'    => 'inline',
						'showIcons' => true,
						'groups'    => $es_tools,
					),
				)
			);
			?>
		</div>
	</div>
</section>
