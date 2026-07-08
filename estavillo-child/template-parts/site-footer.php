<?php
/**
 * Site footer — ESTAVILLO (dark premium).
 *
 * Email real vía filtro es_contact_email; enlaces sociales vía es_social_links.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_links   = es_nav_links();
$es_email   = es_contact_email();
$es_social  = apply_filters(
	'es_social_links',
	array(
		'LinkedIn' => '',
		'Behance'  => '',
	)
);
$es_year    = gmdate( 'Y' );
$es_place   = apply_filters( 'es_footer_location', 'Montevideo, Uruguay' );
?>

<footer class="es-site-footer" data-screen-label="Footer">
	<div class="es-container es-site-footer__inner">
		<div class="es-site-footer__row">
			<span class="es-nav__brand es-site-footer__brand">ESTAVILLO</span>
			<nav class="es-site-footer__nav" aria-label="<?php esc_attr_e( 'Footer', 'estavillo-child' ); ?>">
				<?php foreach ( $es_links as $es_link ) : ?>
					<a href="<?php echo esc_url( $es_link['url'] ); ?>"><?php echo esc_html( $es_link['label'] ); ?></a>
				<?php endforeach; ?>
			</nav>
		</div>

		<div class="es-site-footer__row es-site-footer__row--meta">
			<div class="es-site-footer__meta">
				<?php if ( ! empty( $es_email ) ) : ?>
					<a href="mailto:<?php echo esc_attr( antispambot( $es_email ) ); ?>"><?php echo esc_html( antispambot( $es_email ) ); ?></a>
				<?php else : ?>
					<span class="es-placeholder__tag">{pending: email}</span>
				<?php endif; ?>
				<?php foreach ( $es_social as $es_name => $es_url ) : ?>
					<?php if ( ! empty( $es_url ) ) : ?>
						<a href="<?php echo esc_url( $es_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $es_name ); ?></a>
					<?php else : ?>
						<span title="<?php esc_attr_e( 'URL pending', 'estavillo-child' ); ?>"><?php echo esc_html( $es_name ); ?></span>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<div class="es-site-footer__meta">
				<span><?php echo esc_html( $es_place ); ?></span>
				<span>&copy; <?php echo esc_html( $es_year ); ?></span>
			</div>
		</div>
	</div>
</footer>
