<?php
/**
 * Site header — global nav (dark premium, ESTAVILLO).
 *
 * Global chrome: rendered on every page, never inserted as page content.
 * All editable values come from filters the plugin (Portfolio Content)
 * bridges — wordmark (es_wordmark), nav links + show + routing
 * (es_nav_links_display), sticky option (es_sticky_header). The desktop nav,
 * the mobile menu and the footer nav all read the SAME nav dataset (§3).
 *
 * Structure: wordmark · [nav links · language switch · theme-toggle slot ·
 * menu btn]. The theme-toggle slot reserves space for a future Light/Dark
 * control (still non-functional — full light mode is a separate backlog
 * item). Mobile collapses the nav to one accessible Menu button (nav.js).
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_wm       = es_wordmark();
$es_links    = es_nav_links_display();
$es_sticky   = es_sticky_header_enabled();
$es_active   = es_nav_active_key();

/*
 * Real language switcher via Polylang (Sprint 4A). function_exists() avoids a
 * fatal without Polylang — then $es_pll_langs is empty and es_print_lang_switcher()
 * falls back to the static "EN / ES" markup (same classes/text as always).
 */
$es_pll_langs = function_exists( 'pll_the_languages' ) ? pll_the_languages( array( 'raw' => 1 ) ) : array();

if ( ! function_exists( 'es_print_lang_switcher' ) ) {
	/**
	 * Prints the language-switcher content (no wrapper — each caller supplies
	 * its own span/div, so no CSS changes).
	 *
	 * @param array $langs Result of pll_the_languages( array( 'raw' => 1 ) ).
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
				// Polylang gives the real linked translation URL (or the site
				// home when there's no counterpart) — never a guessed URL.
				printf( '<a href="%s">%s</a>', esc_url( $es_lang['url'] ), $es_lang_label );
			}
			$es_i++;
		}
	}
}

/**
 * Prints one nav <a>, marking it active (green label + dot, aria-current) when
 * it maps to the current page context. Shared by the desktop and mobile nav so
 * the active logic lives in one place.
 *
 * @param array  $link  A resolved nav item from es_nav_links_display().
 * @param string $class Base link class.
 * @param bool   $numbered Whether to render the mobile 2-digit index + label spans.
 * @param int    $index    Zero-based index (for the mobile number).
 */
if ( ! function_exists( 'es_render_nav_link' ) ) {
	function es_render_nav_link( $link, $class, $numbered = false, $index = 0 ) {
		$is_active = es_nav_item_is_active( $link );
		$classes   = $class . ( $is_active ? ' is-active' : '' );
		printf(
			'<a class="%s" href="%s"%s%s>',
			esc_attr( $classes ),
			esc_url( $link['url'] ),
			$is_active ? ' aria-current="page"' : '',
			$numbered ? ' data-es-menu-link' : ''
		);
		if ( $numbered ) {
			printf( '<span class="es-mobile-menu__num">%s</span>', esc_html( sprintf( '%02d', $index + 1 ) ) );
			printf( '<span class="es-mobile-menu__text">%s</span>', esc_html( $link['label'] ) );
		} else {
			echo esc_html( $link['label'] );
		}
		if ( $is_active ) {
			echo '<span class="es-nav__dot" aria-hidden="true"></span>';
		}
		echo '</a>';
	}
}
?>

<header class="es-site-header<?php echo $es_sticky ? '' : ' es-site-header--static'; ?>" data-screen-label="Nav">
	<nav class="es-container es-nav" aria-label="<?php echo esc_attr( es__( 'nav_aria_main' ) ); ?>">
		<?php if ( ! empty( $es_wm['image'] ) ) : ?>
			<a class="es-nav__brand es-nav__brand--logo" href="<?php echo esc_url( $es_wm['url'] ); ?>" aria-label="<?php echo esc_attr( $es_wm['text'] ); ?>">
				<img src="<?php echo esc_url( $es_wm['image'] ); ?>" alt="<?php echo esc_attr( $es_wm['text'] ); ?>" class="es-nav__logo">
			</a>
		<?php else : ?>
			<a class="es-nav__brand" href="<?php echo esc_url( $es_wm['url'] ); ?>"><?php echo esc_html( $es_wm['text'] ); ?></a>
		<?php endif; ?>

		<div class="es-nav__right">
			<div class="es-nav__links" data-es-navlinks>
				<?php foreach ( $es_links as $es_link ) : ?>
					<?php es_render_nav_link( $es_link, 'es-nav__link' ); ?>
				<?php endforeach; ?>
			</div>

			<span class="es-nav__lang" aria-label="<?php echo esc_attr( es__( 'lang_switch_label' ) ); ?>">
				<?php es_print_lang_switcher( $es_pll_langs ); ?>
			</span>

			<?php /* Reserved slot for a future Light/Dark toggle. Still non-functional — full light mode is a separate backlog phase. */ ?>
			<span class="es-nav__toggle" aria-hidden="true" title="<?php echo esc_attr( es__( 'theme_toggle_title' ) ); ?>">
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
				aria-label="<?php echo esc_attr( es__( 'menu_open' ) ); ?>"
				data-label-open="<?php echo esc_attr( es__( 'menu_open' ) ); ?>"
				data-label-close="<?php echo esc_attr( es__( 'menu_close' ) ); ?>"
			>
				<span class="es-nav__menu-label"><?php echo esc_html( es__( 'menu_label' ) ); ?></span>
				<span class="es-burger" aria-hidden="true"><span></span><span></span></span>
			</button>
		</div>
	</nav>
</header>

<!-- mobile menu overlay (toggled by nav.js; the button morphs to X and stays above) -->
<div class="es-mobile-menu" id="es-mobile-menu" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( es__( 'menu_label' ) ); ?>" hidden>
	<div class="es-mobile-menu__panel">
		<nav class="es-container es-mobile-menu__links" aria-label="<?php echo esc_attr( es__( 'nav_aria_mobile' ) ); ?>">
			<?php foreach ( $es_links as $es_i => $es_link ) : ?>
				<?php es_render_nav_link( $es_link, 'es-mobile-menu__link', true, $es_i ); ?>
			<?php endforeach; ?>
		</nav>
		<div class="es-container es-mobile-menu__foot">
			<?php es_print_lang_switcher( $es_pll_langs ); ?>
		</div>
	</div>
</div>
