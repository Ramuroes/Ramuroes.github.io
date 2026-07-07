<?php
/**
 * Site header — nav sticky ESTAVILLO (dark premium).
 *
 * Se usa dentro del template standalone de la home. Los enlaces salen de
 * es_nav_links() (filtrable). El indicador EN/ES es informativo por ahora
 * (Polylang se conecta cuando se definan las traducciones de páginas).
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_links    = es_nav_links();
$es_home_url = apply_filters( 'es_home_url', home_url( '/' ) );
?>

<header class="es-site-header" data-screen-label="Nav">
	<nav class="es-container es-nav" aria-label="<?php esc_attr_e( 'Main', 'estavillo-child' ); ?>">
		<a class="es-nav__brand" href="<?php echo esc_url( $es_home_url ); ?>">ESTAVILLO</a>

		<div class="es-nav__links" data-es-navlinks>
			<?php foreach ( $es_links as $es_link ) : ?>
				<a class="es-nav__link" href="<?php echo esc_url( $es_link['url'] ); ?>">
					<?php echo esc_html( $es_link['label'] ); ?>
				</a>
			<?php endforeach; ?>
			<span class="es-nav__lang" aria-label="<?php esc_attr_e( 'Language: English active, Spanish coming later', 'estavillo-child' ); ?>">
				<span class="es-nav__lang-on">EN</span> / ES
			</span>
		</div>

		<button
			class="es-nav__burger"
			type="button"
			data-es-menu-open
			aria-label="<?php esc_attr_e( 'Open menu', 'estavillo-child' ); ?>"
			aria-expanded="false"
			aria-controls="es-mobile-menu"
		>
			<svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
				<line x1="2" y1="6.5" x2="18" y2="6.5" /><line x1="2" y1="13.5" x2="18" y2="13.5" />
			</svg>
		</button>
	</nav>
</header>

<!-- mobile menu (toggled by nav.js) -->
<div class="es-mobile-menu" id="es-mobile-menu" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'estavillo-child' ); ?>" hidden>
	<div class="es-container es-mobile-menu__bar">
		<span class="es-nav__brand">ESTAVILLO</span>
		<button class="es-nav__burger" type="button" data-es-menu-close aria-label="<?php esc_attr_e( 'Close menu', 'estavillo-child' ); ?>">
			<svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
				<line x1="3" y1="3" x2="15" y2="15" /><line x1="15" y1="3" x2="3" y2="15" />
			</svg>
		</button>
	</div>
	<nav class="es-container es-mobile-menu__links" aria-label="<?php esc_attr_e( 'Mobile', 'estavillo-child' ); ?>">
		<?php foreach ( $es_links as $es_i => $es_link ) : ?>
			<a class="es-mobile-menu__link" href="<?php echo esc_url( $es_link['url'] ); ?>" data-es-menu-link>
				<span class="es-mobile-menu__num"><?php echo esc_html( sprintf( '%02d', $es_i + 1 ) ); ?></span>
				<?php echo esc_html( $es_link['label'] ); ?>
			</a>
		<?php endforeach; ?>
	</nav>
	<div class="es-container es-mobile-menu__foot"><span class="es-nav__lang-on">EN</span> / ES</div>
</div>
