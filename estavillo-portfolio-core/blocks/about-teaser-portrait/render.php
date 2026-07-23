<?php
/**
 * Render dinámico de estavillo/about-teaser-portrait.
 *
 * Sin atributos configurables — la imagen vive en wp-admin → Portfolio
 * Content → About → "Portrait image URL" (filtro es_home_about_portrait,
 * puenteado por este mismo plugin), el MISMO campo que usa la página
 * About: una sola fuente, cero copias. Con el campo vacío se renderiza el
 * marco reservado del design system SIN texto de marcador — el contenido
 * Gutenberg final no lleva marcadores de desarrollo tipo {asset: …}
 * (corrección del ticket de revisión de Home); el marco desaparece solo
 * al cargar la URL real.
 *
 * @package estavillo-portfolio-core
 * @var array $attributes Atributos del bloque (ninguno usado).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_portrait = trim( (string) apply_filters( 'es_home_about_portrait', '' ) );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'es-about__portrait' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<?php if ( '' !== $es_portrait ) : ?>
		<img src="<?php echo esc_url( $es_portrait ); ?>" alt="" loading="lazy" />
	<?php else : ?>
		<div class="es-placeholder es-about__placeholder" role="img" aria-label="<?php esc_attr_e( 'Reserved space for the editorial portrait', 'estavillo-portfolio-core' ); ?>"></div>
	<?php endif; ?>
</div>
