<?php
/**
 * Documento genérico — el chrome ESTAVILLO para las vistas que el theme no
 * tenía template propio: búsqueda, archivos (categoría/etiqueta/fecha/autor),
 * el fallback index, y una Página guardada sin ninguno de los templates del
 * theme.
 *
 * POR QUÉ EXISTE: hasta la iteración de cierre esas vistas caían al template
 * del padre (Kadence), que nunca se migró — fondo claro, el logo viejo y el
 * menú "Home / About / My Work / Contact" que ya no existe en ninguna otra
 * pantalla, y el copy en inglés incluso llegando desde una URL /es/.
 * assets/css/theme-dark.css les daba el color correcto, pero un menú
 * desactualizado no se arregla pintándolo de oscuro: había que servir el
 * chrome de verdad.
 *
 * QUÉ NO HACE: no inventa diseño. Reusa el mismo <head>/<body> standalone que
 * los otros siete templates, el mismo site-header/site-footer, el mismo
 * page-head (eyebrow + H1 + lead) y las mismas voces tipográficas. Lo único
 * propio es la lista de resultados (.es-postlist en pages.css), que es una
 * fila con hairline, sin tarjeta ni tratamiento nuevo.
 *
 * $args:
 *   'body_class' string  Clase extra para <body>.
 *   'eyebrow'    string  Eyebrow del page-head.
 *   'title'      string  H1 (admite <em>).
 *   'lead'       string  Bajada opcional.
 *   'mode'       string  'list' → recorre el loop como listado de enlaces;
 *                        'content' → imprime the_content() de un único post.
 *   'empty'      string  Texto cuando el loop no trae nada (sólo en 'list').
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_gd_body  = isset( $args['body_class'] ) ? (string) $args['body_class'] : 'es-generic-page';
$es_gd_mode  = isset( $args['mode'] ) ? (string) $args['mode'] : 'list';
$es_gd_empty = isset( $args['empty'] ) ? (string) $args['empty'] : '';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'es-page ' . $es_gd_body ); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
?>

<div id="es-page" class="es-page">

	<?php get_template_part( 'template-parts/site-header' ); ?>

	<main id="top" class="es-main">
		<?php
		get_template_part(
			'template-parts/page-head',
			null,
			array(
				'eyebrow' => isset( $args['eyebrow'] ) ? $args['eyebrow'] : '',
				'title'   => isset( $args['title'] ) ? $args['title'] : '',
				'lead'    => isset( $args['lead'] ) ? $args['lead'] : '',
			)
		);
		?>

		<section class="es-section es-generic-body">
			<div class="es-container">
				<?php if ( 'content' === $es_gd_mode ) : ?>

					<?php
					while ( have_posts() ) :
						the_post();
						the_content();
					endwhile;
					?>

				<?php elseif ( have_posts() ) : ?>

					<ul class="es-postlist">
						<?php
						while ( have_posts() ) :
							the_post();
							?>
							<li class="es-postlist__item">
								<a class="es-postlist__link" href="<?php the_permalink(); ?>">
									<h2 class="es-h2 es-postlist__title"><?php the_title(); ?></h2>
									<?php
									$es_gd_excerpt = trim( wp_strip_all_tags( get_the_excerpt() ) );
									if ( '' !== $es_gd_excerpt ) :
										?>
										<p class="es-body es-postlist__excerpt"><?php echo esc_html( wp_trim_words( $es_gd_excerpt, 28 ) ); ?></p>
									<?php endif; ?>
								</a>
							</li>
						<?php endwhile; ?>
					</ul>

					<?php
					/*
					 * Paginación nativa de WP. Se le pasa el markup de flecha
					 * del sistema (→ / ←, los mismos de .es-btn__arrow) para
					 * no depender de los defaults en inglés del core.
					 */
					the_posts_pagination(
						array(
							'mid_size'           => 2,
							'class'              => 'es-pagination',
							'prev_text'          => '&larr;',
							'next_text'          => '&rarr;',
							'screen_reader_text' => es__( 'pagination_aria' ),
						)
					);
					?>

				<?php elseif ( '' !== $es_gd_empty ) : ?>

					<p class="es-lead es-generic-empty"><?php echo esc_html( $es_gd_empty ); ?></p>

				<?php endif; ?>
			</div>
		</section>
	</main>

	<?php get_template_part( 'template-parts/site-footer' ); ?>

</div>

<?php wp_footer(); ?>
</body>
</html>
