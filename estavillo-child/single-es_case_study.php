<?php
/**
 * Single Case Study — template standalone que reusa el chrome ESTAVILLO.
 *
 * Sprint 4B. WordPress resuelve este archivo automáticamente por la
 * jerarquía de templates (single-{post_type}.php) para cualquier post del
 * CPT es_case_study — no requiere registro ni un filtro template_include.
 * Si el plugin "Estavillo Portfolio Core" está inactivo, el CPT no existe
 * y esta URL no se resuelve en absoluto (nada "cae" a este archivo sin el
 * CPT registrado), así que no hace falta ningún fallback acá.
 *
 * Standalone igual que templates/page-home-estavillo.php: imprime su
 * propio wp_head()/wp_footer() pero NO usa el header/footer de Kadence —
 * reusa el chrome propio de ESTAVILLO (site-header + site-footer).
 *
 * Deliberadamente simple (Sprint 4B): título, eyebrow/kicker, extracto,
 * tags, imagen destacada, status/role/tools/period (todos opcionales) y
 * el contenido del editor estándar de WordPress vía the_content() — nada
 * de campos custom para el cuerpo. No hay case builder acá.
 *
 * Sprint 4C suma: un índice sticky opcional (campo "Case index" del meta
 * box — manual, "Label|#anchor" por línea, ver docs) y la librería de
 * clases .es-case-* (case-study.css) para estructurar contenido dentro del
 * editor nativo — ver README para el listado completo con ejemplos.
 *
 * Sprint 4D reordena el hero como grilla editorial de 2 columnas en
 * desktop (texto a la izquierda, imagen/placeholder a la derecha) —
 * mismos campos de siempre, solo cambia el markup/CSS del hero. En mobile
 * sigue apilado (imagen debajo del texto). No toca .es-case-* del cuerpo.
 *
 * Mega sprint (hero options + breadcrumbs): el hero ahora admite 4 layouts
 * seleccionables por caso (campo "Hero layout" del meta box — ver
 * es_case_hero_layout_choices() en el plugin), vía una clase modificadora
 * sobre .es-case__hero; el default 'split-right' es exactamente el markup/
 * CSS de Sprint 4D, sin clase extra, cero riesgo para casos existentes.
 * Suma también una tira de breadcrumbs (Home / Work / título del caso)
 * arriba del índice sticky.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'es-page es-case-study' ); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
?>

<div id="es-page" class="es-page">

	<?php get_template_part( 'template-parts/site-header' ); ?>

	<main id="top" class="es-main">
		<?php
		while ( have_posts() ) :
			the_post();

			$es_case_id      = get_the_ID();
			$es_case_kicker  = get_post_meta( $es_case_id, '_es_case_kicker', true );
			$es_case_excerpt = get_the_excerpt();
			$es_case_tags    = get_the_terms( $es_case_id, 'es_case_tag' );
			if ( ! is_array( $es_case_tags ) ) {
				$es_case_tags = array();
			}

			// Status ya NO vive en esta grilla: se muestra como badge junto a
			// los tags del hero (ver más abajo). Lo que queda es la terna
			// Rol/Período/Herramientas — tres campos homogéneos, todos
			// "ficha técnica" del proyecto, que ahora sí forman una caja
			// coherente. Tools va último porque es sistemáticamente el más
			// largo (una lista), y en desktop ocupa la columna más ancha.
			$es_case_meta = array_filter(
				array(
					'role'   => get_post_meta( $es_case_id, '_es_case_role', true ),
					'period' => get_post_meta( $es_case_id, '_es_case_period', true ),
					'tools'  => get_post_meta( $es_case_id, '_es_case_tools', true ),
				)
			);

			/*
			 * Badge de estado. El texto sigue siendo el mismo meta de
			 * siempre (_es_case_label, texto libre — no se deduce ni se
			 * migra nada). El tono es un campo nuevo e independiente
			 * (_es_case_status_tone): si el caso no lo tiene guardado —o
			 * sea, todos los casos publicados hasta hoy— cae a 'neutral',
			 * que es el gris que ya se venía viendo. Whitelist estricta
			 * acá también, no sólo al guardar: el valor termina en un
			 * nombre de clase CSS.
			 */
			$es_case_status      = get_post_meta( $es_case_id, '_es_case_label', true );
			$es_status_tone      = get_post_meta( $es_case_id, '_es_case_status_tone', true );
			$es_status_tones     = array( 'neutral', 'green', 'amber', 'blue', 'purple' );
			if ( ! in_array( $es_status_tone, $es_status_tones, true ) ) {
				$es_status_tone = 'neutral';
			}

			// Layout del hero: 'split-right' (default histórico, Sprint 4D) no
			// suma clase modificadora — cero riesgo para casos ya publicados.
			$es_hero_layout       = get_post_meta( $es_case_id, '_es_case_hero_layout', true );
			$es_hero_layout_class = ( $es_hero_layout && 'split-right' !== $es_hero_layout ) ? ' es-case__hero--' . sanitize_html_class( $es_hero_layout ) : '';

			/*
			 * Ancho de texto del hero (campo nuevo, independiente del layout
			 * de imagen). Un caso guardado ANTES de que este campo existiera
			 * no tiene este meta — en vez de asumir 'editorial' para TODOS,
			 * el fallback depende del layout ya guardado:
			 *   - layout 'stacked': el CSS de --stacked ya le sacaba el
			 *     max-width:560px al bloque de texto (Sprint mega, hero
			 *     options) — su equivalente visual real es 'wide', no
			 *     'editorial'. Asumir 'editorial' ahí angostaría de golpe un
			 *     caso ya publicado sin que nadie lo haya pedido.
			 *   - cualquier otro layout: el texto SÍ estaba topeado a
			 *     560px — 'editorial' es su equivalente exacto.
			 * Casos nuevos siempre guardan un valor explícito (ver el
			 * <select> del meta box), así que este fallback sólo importa
			 * para posts pre-existentes al momento de este cambio.
			 */
			$es_hero_text_width_choices = array( 'editorial', 'wide', 'full' );
			$es_hero_text_width         = get_post_meta( $es_case_id, '_es_case_hero_text_width', true );
			if ( ! in_array( $es_hero_text_width, $es_hero_text_width_choices, true ) ) {
				$es_hero_text_width = ( 'stacked' === $es_hero_layout ) ? 'wide' : 'editorial';
			}
			$es_hero_text_width_class = ' es-case__hero--text-' . sanitize_html_class( $es_hero_text_width );

			// Breadcrumbs: Home / Work / título del caso (helper compartido,
			// ver functions.php → es_breadcrumb_trail() — también usado por
			// las 4 páginas fijas).
			$es_breadcrumb_trail = es_breadcrumb_trail( 'nav_work', get_the_title() );

			// Índice sticky: manual, "Label|#anchor" por línea (campo "Case
			// index" del meta box). Si está vacío, el índice no se imprime.
			$es_case_index_raw = get_post_meta( $es_case_id, '_es_case_index', true );
			$es_case_index     = array();
			if ( ! empty( $es_case_index_raw ) ) {
				foreach ( preg_split( '/\r\n|\r|\n/', $es_case_index_raw ) as $es_index_line ) {
					$es_index_line = trim( $es_index_line );
					if ( '' === $es_index_line || false === strpos( $es_index_line, '|' ) ) {
						continue;
					}
					list( $es_index_label, $es_index_href ) = array_map( 'trim', explode( '|', $es_index_line, 2 ) );
					if ( '' !== $es_index_label && '' !== $es_index_href ) {
						$es_case_index[] = array(
							'label' => $es_index_label,
							'href'  => $es_index_href,
						);
					}
				}
			}
			?>
			<?php
			/*
			 * ORDEN DE LA ZONA SUPERIOR (decidido comparando 3 arquitecturas):
			 *   header → índice sticky → breadcrumb → hero → contenido.
			 *
			 * El índice va ANTES del breadcrumb, pegado al header: es
			 * navegación real del documento y el único de los dos que queda
			 * sticky. El breadcrumb va después, como metadata editorial —
			 * más chico, más apagado, sin borde ni fondo — y scrollea
			 * normalmente con la página junto al hero.
			 *
			 * Índice sticky. El scroll horizontal ya lo hacía el CSS
			 * (overflow-x:auto sobre __inner); lo que suma este markup es:
			 *   - los dos botones de desplazamiento, que arrancan ocultos
			 *     (hidden) y sólo los muestra case-index.js si realmente
			 *     hay contenido tapado de ese lado. Sin JS nunca aparecen y
			 *     el menú se sigue scrolleando con gesto/trackpad/teclado:
			 *     son un atajo, nunca el único camino.
			 *   - el riel: una línea gris fina de ancho completo con un
			 *     segmento verde que el JS mueve a la sección activa. Vive
			 *     dentro de __inner (no de la <nav>) para poder medir en el
			 *     mismo sistema de coordenadas que los links, incluso con el
			 *     menú scrolleado. aria-hidden: es decoración pura; el
			 *     estado real lo lleva aria-current en el link activo.
			 */
			?>
			<?php if ( ! empty( $es_case_index ) ) : ?>
				<nav class="es-case-index" aria-label="<?php echo esc_attr( es__( 'case_sections_aria' ) ); ?>" data-es-case-index>
					<div class="es-case-index__viewport">
						<button type="button" class="es-case-index__arrow es-case-index__arrow--prev" data-es-index-prev aria-label="<?php echo esc_attr( es__( 'case_nav_prev' ) ); ?>" hidden>
							<svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M8.6 3 4.6 7l4 4"/></svg>
						</button>
						<div class="es-case-index__inner" data-es-index-scroller>
							<?php foreach ( $es_case_index as $es_index_item ) : ?>
								<a class="es-case-index__link" href="<?php echo esc_url( $es_index_item['href'] ); ?>"><?php echo esc_html( $es_index_item['label'] ); ?></a>
							<?php endforeach; ?>
							<span class="es-case-index__rail" aria-hidden="true"><span class="es-case-index__rail-active" data-es-index-rail></span></span>
						</div>
						<button type="button" class="es-case-index__arrow es-case-index__arrow--next" data-es-index-next aria-label="<?php echo esc_attr( es__( 'case_nav_next' ) ); ?>" hidden>
							<svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5.4 3l4 4-4 4"/></svg>
						</button>
					</div>
				</nav>
			<?php endif; ?>
			<?php get_template_part( 'template-parts/breadcrumbs', null, array( 'trail' => $es_breadcrumb_trail ) ); ?>
			<article class="es-section es-case">
				<div class="es-container es-case__hero<?php echo esc_attr( $es_hero_layout_class . $es_hero_text_width_class ); ?>" data-es-reveal>
					<div class="es-case__hero-content">
						<?php if ( ! empty( $es_case_kicker ) ) : ?>
							<p class="es-eyebrow es-case__kicker"><?php echo esc_html( $es_case_kicker ); ?></p>
						<?php endif; ?>

						<h1 class="es-h1 es-case__title"><?php the_title(); ?></h1>

						<?php if ( ! empty( $es_case_excerpt ) ) : ?>
							<p class="es-lead es-case__excerpt"><?php echo esc_html( $es_case_excerpt ); ?></p>
						<?php endif; ?>

						<?php
						/*
						 * Badge de estado + tags comparten la misma fila: el
						 * badge entra como primer ítem del flex que ya
						 * existía, no como un bloque nuevo con su propio
						 * margen. Así queda "cerca de las etiquetas" sin
						 * agregar una franja más al hero ni competir con el
						 * eyebrow/título. La fila se imprime si hay tags O
						 * si hay estado — un caso con estado y sin tags
						 * (o al revés) sigue funcionando igual.
						 */
						?>
						<?php if ( ! empty( $es_case_tags ) || ! empty( $es_case_status ) ) : ?>
							<div class="es-case__tags">
								<?php if ( ! empty( $es_case_status ) ) : ?>
									<span class="es-case__status es-case__status--<?php echo esc_attr( $es_status_tone ); ?>">
										<span class="es-case__status-dot" aria-hidden="true"></span>
										<span class="es-case__status-text"><?php echo esc_html( $es_case_status ); ?></span>
									</span>
								<?php endif; ?>
								<?php foreach ( $es_case_tags as $es_tag ) : ?>
									<span class="es-pill"><?php echo esc_html( $es_tag->name ); ?></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>

					<div class="es-case__hero-media">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="es-case__media">
								<?php the_post_thumbnail( 'large' ); ?>
							</div>
						<?php else : ?>
							<div class="es-placeholder es-case__media es-case__placeholder" role="img" aria-label="<?php echo esc_attr( es__( 'case_media_ph_aria' ) ); ?>">
								<span class="es-placeholder__tag">{asset: <?php echo esc_html( sanitize_title( get_the_title() ) ); ?>}</span>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<?php
				/*
				 * Metadata del proyecto: HERMANA del hero, no hija — antes vivía
				 * dentro de .es-case__hero-content (la columna de texto del
				 * grid), así que en mobile el orden real del DOM era
				 * eyebrow→título→resumen→tags→METADATA→imagen: la imagen
				 * quedaba después de todo el bloque técnico (Status/Role/
				 * Tools/Period), perdiendo el impulso visual de entrada al
				 * caso. Sacándola del grid del hero y poniéndola acá, el
				 * orden en TODOS los layouts (split o stacked, mobile o
				 * desktop) es siempre eyebrow→título→resumen→tags→imagen→
				 * metadata→cuerpo — estructural, no por un truco de 'order'
				 * en flex/grid. Mismo container que el hero (--es-max-w), no
				 * el container ancho del cuerpo — todavía se lee como parte
				 * de la introducción del proyecto, no como el cuerpo.
				 */
				?>
				<?php if ( ! empty( $es_case_meta ) ) : ?>
					<div class="es-container es-case__meta-wrap">
						<dl class="es-case__meta">
							<?php foreach ( $es_case_meta as $es_meta_key => $es_meta_value ) : ?>
								<div class="es-case__meta-item es-case__meta-item--<?php echo esc_attr( $es_meta_key ); ?>">
									<dt>
										<?php
										// Whitelist cerrada por clave interna (role/period/
										// tools), nunca contenido del admin — ver el
										// docblock de es_case_meta_icon() en functions.php.
										echo es_case_meta_icon( $es_meta_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										?>
										<span><?php echo esc_html( es__( 'case_meta_' . $es_meta_key ) ); ?></span>
									</dt>
									<dd><?php echo esc_html( $es_meta_value ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					</div>
				<?php endif; ?>

				<?php
				// es-container--case: el body del caso usa el container ancho
				// del sistema editorial (1320px, spec Grid System v1). El hero
				// de arriba y el chrome del sitio quedan en 1140 a propósito.
				?>
				<div class="es-container es-container--case">
					<?php
					// Sin data-es-reveal acá, a propósito: el body es UN solo
					// elemento de miles de px de alto, y un IntersectionObserver
					// con threshold > 0 puede no dispararse nunca para un
					// elemento más alto que el viewport (ratio máx = viewport /
					// alto total < threshold) — el contenido quedaba invisible
					// en producción. El contenido del caso nunca depende del
					// sistema de reveal; el hero (elemento chico) sí anima.
					?>
					<div class="es-case__body">
						<?php the_content(); ?>
					</div>
				</div>
			</article>
		<?php endwhile; ?>
	</main>

	<?php get_template_part( 'template-parts/site-footer' ); ?>

</div>

<?php wp_footer(); ?>
</body>
</html>
