<?php
/**
 * Site footer — global chrome (dark premium, ESTAVILLO).
 *
 * Global component rendered on every page (never page content). All values are
 * editable globally through Portfolio Content, bridged to filters with theme
 * defaults so nothing here breaks when a field is blank. Layout (compact / two
 * / three columns), width and per-section visibility are global options; the
 * nav reuses the SAME dataset as the header (es_nav_links_display).
 *
 * Content ownership (§15): phone / email / social / location / layout are
 * language-neutral (one global value, shared EN/ES — never duplicated). The
 * footer note and copyright name are plain global text; for per-language
 * variants use Polylang string translation (documented in docs/BACKLOG.md).
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_wm       = es_wordmark();
$es_links    = es_nav_links_display();
$es_email    = es_contact_email();
$es_phone    = es_contact_phone();
$es_whatsapp = es_contact_whatsapp();
$es_social   = apply_filters(
	'es_social_links',
	array(
		'LinkedIn' => '',
		'Behance'  => '',
	)
);
$es_place    = apply_filters( 'es_footer_location', 'Montevideo, Uruguay' );
$es_note     = es_footer_note();
$es_name     = es_footer_copyright_name();
$es_layout   = es_footer_layout();
$es_width    = es_footer_width();
$es_year     = gmdate( 'Y' );

// Social name → visibility key (unknown names default to visible).
$es_social_key = array(
	'LinkedIn'  => 'linkedin',
	'Instagram' => 'instagram',
	'Behance'   => 'behance',
);

// Which pieces actually render (visibility toggle AND has its own data).
$es_show_nav      = es_footer_visible( 'nav' ) && ! empty( $es_links );
$es_show_email    = es_footer_visible( 'email' ) && ! empty( $es_email );
$es_show_phone    = es_footer_visible( 'phone' ) && ! empty( $es_phone );
$es_show_whatsapp = es_footer_visible( 'whatsapp' ) && ! empty( $es_whatsapp );
$es_show_location = es_footer_visible( 'location' ) && ! empty( $es_place );
$es_show_note     = es_footer_visible( 'note' ) && ! empty( $es_note );
$es_show_credit   = es_footer_visible( 'credit' ) && '' !== trim( (string) $es_name );

$es_social_visible = array();
foreach ( $es_social as $es_sname => $es_surl ) {
	$es_vkey = $es_social_key[ $es_sname ] ?? sanitize_key( $es_sname );
	if ( ! empty( $es_surl ) && es_footer_visible( $es_vkey ) ) {
		$es_social_visible[ $es_sname ] = $es_surl;
	}
}

$es_has_contact = $es_show_email || $es_show_phone || $es_show_whatsapp;
$es_has_social  = ! empty( $es_social_visible );
?>

<footer class="es-site-footer es-site-footer--<?php echo esc_attr( $es_layout ); ?><?php echo 'wide' === $es_width ? ' es-site-footer--wide' : ''; ?>" data-screen-label="Footer">
	<div class="es-container es-site-footer__inner">
		<div class="es-site-footer__grid">

			<div class="es-site-footer__col es-site-footer__col--identity">
				<span class="es-nav__brand es-site-footer__brand"><?php echo esc_html( $es_wm['text'] ); ?></span>
				<?php if ( $es_show_note ) : ?>
					<p class="es-site-footer__note"><?php echo esc_html( $es_note ); ?></p>
				<?php endif; ?>
				<?php if ( $es_show_credit ) : ?>
					<p class="es-site-footer__credit">
						<?php echo wp_kses( es__( 'footer_credit_lead' ), array( 'em' => array() ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- wp_kses'd, <em> only, same pattern as the Connect title field. ?>
						<?php echo esc_html( $es_name ); ?>.
					</p>
				<?php endif; ?>
				<?php if ( $es_has_social ) : ?>
					<div class="es-footer-social">
						<?php foreach ( $es_social_visible as $es_sname => $es_surl ) :
							$es_social_aria = $es_name
								? sprintf( es__( 'footer_social_named' ), $es_name, $es_sname )
								: sprintf( es__( 'footer_social_generic' ), $es_sname );
							?>
							<a class="es-footer-social__link" href="<?php echo esc_url( $es_surl ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $es_social_aria ); ?>">
								<?php echo es_footer_icon( $es_social_key[ $es_sname ] ?? sanitize_key( $es_sname ), 'es-footer-social__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput -- theme-authored SVG. ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $es_show_nav ) : ?>
				<nav class="es-site-footer__col es-site-footer__col--nav es-site-footer__nav" aria-label="<?php echo esc_attr( es__( 'nav_aria_footer' ) ); ?>">
					<?php foreach ( $es_links as $es_link ) : ?>
						<a href="<?php echo esc_url( $es_link['url'] ); ?>"<?php echo es_nav_item_is_active( $es_link ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $es_link['label'] ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>

			<?php if ( $es_has_contact ) : ?>
				<div class="es-site-footer__col es-site-footer__col--contact">
					<div class="es-footer-contact">
						<?php if ( $es_show_email ) : ?>
							<a class="es-footer-contact__item" href="mailto:<?php echo esc_attr( antispambot( $es_email ) ); ?>"><?php echo esc_html( antispambot( $es_email ) ); ?></a>
						<?php endif; ?>
						<?php if ( $es_show_phone ) : ?>
							<a class="es-footer-contact__item" href="tel:+<?php echo esc_attr( es_phone_digits( $es_phone ) ); ?>" aria-label="<?php echo esc_attr( $es_name ? sprintf( es__( 'footer_call_named' ), $es_name ) : es__( 'footer_call_generic' ) ); ?>">
								<?php echo es_footer_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput -- theme-authored SVG. ?>
								<span><?php echo esc_html( $es_phone ); ?></span>
							</a>
						<?php endif; ?>
						<?php if ( $es_show_whatsapp ) : ?>
							<a class="es-footer-contact__item" href="https://wa.me/<?php echo esc_attr( es_phone_digits( $es_whatsapp ) ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $es_name ? sprintf( es__( 'footer_wa_named' ), $es_name ) : es__( 'footer_wa_generic' ) ); ?>">
								<?php echo es_footer_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput -- theme-authored SVG. ?>
								<span><?php echo esc_html( es__( 'whatsapp_label' ) ); ?></span>
							</a>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

		</div>

		<div class="es-site-footer__base">
			<?php if ( $es_show_location ) : ?>
				<span class="es-site-footer__place"><?php echo esc_html( $es_place ); ?></span>
			<?php endif; ?>
			<span class="es-site-footer__copy">
				&copy; <?php echo esc_html( $es_year ); ?><?php echo $es_name ? ' ' . esc_html( $es_name ) : ''; ?>
			</span>
		</div>
	</div>
</footer>
