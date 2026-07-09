<?php
/**
 * Site header — nav sticky ESTAVILLO (dark premium).
 *
 * Estructura: logo · [nav links · language switch · theme-toggle slot · menu btn].
 * El "theme-toggle slot" reserva el espacio para un futuro Light/Dark toggle
 * (sin funcionalidad todavía). En mobile la nav colapsa a un botón "Menu" +
 * hamburguesa que morfa a X (un solo control, queda por encima del overlay).
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_links    = es_nav_links();
$es_home_url = apply_filters( 'es_home_url', home_url( '/' ) );

/*
 * Switcher de idioma real vía Polylang (Sprint 4A). function_exists()
 * evita cualquier fatal si Polylang no está instalado — en ese caso
 * $es_pll_langs queda vacío y es_print_lang_switcher() cae al markup
 * estático "EN / ES" de siempre (mismas clases, mismo texto).
 */
$es_pll_langs = function_exists( 'pll_the_languages' ) ? pll_the_languages( array( 'raw' => 1 ) ) : array();

if ( ! function_exists( 'es_print_lang_switcher' ) ) {
	/**
	 * Imprime el contenido del switcher de idioma (sin wrapper — cada lugar
	 * que lo usa pone su propio <span>/<div>, igual que antes de este
	 * ticket, así que ningún CSS necesita cambiar).
	 *
	 * @param array $langs Resultado de pll_the_languages( array( 'raw' => 1 ) ).
	 */
	function es_print_lang_switcher( $langs ) {
		if ( empty( $langs ) ) {
			echo '<span class="es-nav__lang-on">EN</span> / ES';
			return;
		}
		$es_i = 0;
		foreach ( $langs as $es_lang ) {
			if ( $es_i > 0 ) {
				echo ' / ';
			}
			$es_lang_label = esc_html( strtoupper( $es_lang['slug'] ) );
			if ( ! empty( $es_lang['current_lang'] ) ) {
				echo '<span class="es-nav__lang-on">' . $es_lang_label . '</span>';
			} else {
				printf( '<a href="%s">%s</a>', esc_url( $es_lang['url'] ), $es_lang_label );
			}
			$es_i++;
		}
	}
}
?>

<header class="es-site-header" data-screen-label="Nav">
	<nav class="es-container es-nav" aria-label="<?php esc_attr_e( 'Main', 'estavillo-child' ); ?>">
		<a class="es-nav__brand" href="<?php echo esc_url( $es_home_url ); ?>">ESTAVILLO</a>

		<div class="es-nav__right">
			<div class="es-nav__links" data-es-navlinks>
				<?php foreach ( $es_links as $es_link ) : ?>
					<a class="es-nav__link" href="<?php echo esc_url( $es_link['url'] ); ?>">
						<?php echo esc_html( $es_link['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</div>

			<span class="es-nav__lang" aria-label="<?php echo esc_attr( es__( 'lang_switch_label' ) ); ?>">
				<?php es_print_lang_switcher( $es_pll_langs ); ?>
			</span>

			<?php /* Espacio reservado para un futuro toggle Light/Dark. Sin funcionalidad aún. */ ?>
			<span class="es-nav__toggle" aria-hidden="true" title="<?php esc_attr_e( 'Light / dark — coming soon', 'estavillo-child' ); ?>">
				<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.3" aria-hidden="true">
					<path d="M13 9.2A5 5 0 0 1 6.8 3 5 5 0 1 0 13 9.2Z" stroke-linejoin="round" />
				</svg>
			</span>

			<button
				class="es-nav__menu-btn"
				type="button"
				data-es-menu-toggle
				aria-expanded="false"
				aria-controls="es-mobile-menu"
				aria-label="<?php esc_attr_e( 'Open menu', 'estavillo-child' ); ?>"
				data-label-open="<?php esc_attr_e( 'Open menu', 'estavillo-child' ); ?>"
				data-label-close="<?php esc_attr_e( 'Close menu', 'estavillo-child' ); ?>"
			>
				<span class="es-nav__menu-label"><?php esc_html_e( 'Menu', 'estavillo-child' ); ?></span>
				<span class="es-burger" aria-hidden="true"><span></span><span></span></span>
			</button>
		</div>
	</nav>
</header>

<!-- mobile menu overlay (toggled by nav.js; el botón morfa a X y queda encima) -->
<div class="es-mobile-menu" id="es-mobile-menu" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'estavillo-child' ); ?>" hidden>
	<div class="es-mobile-menu__panel">
		<nav class="es-container es-mobile-menu__links" aria-label="<?php esc_attr_e( 'Mobile', 'estavillo-child' ); ?>">
			<?php foreach ( $es_links as $es_i => $es_link ) : ?>
				<a class="es-mobile-menu__link" href="<?php echo esc_url( $es_link['url'] ); ?>" data-es-menu-link>
					<span class="es-mobile-menu__num"><?php echo esc_html( sprintf( '%02d', $es_i + 1 ) ); ?></span>
					<span class="es-mobile-menu__text"><?php echo esc_html( $es_link['label'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</nav>
		<div class="es-container es-mobile-menu__foot">
			<?php es_print_lang_switcher( $es_pll_langs ); ?>
		</div>
	</div>
</div>
