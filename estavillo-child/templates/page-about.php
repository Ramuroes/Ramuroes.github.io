<?php
/**
 * Template Name: Estavillo — About
 * Description: Página About dedicada — intro más grande, retrato, texto
 * editable, experiencia, educación/certificados, hobbies y descarga de CV.
 * Reusa los mismos campos de "Home Content" (about_text/about_portrait) que
 * ya existían desde Sprint 3, más los nuevos de esta página (CV, timeline,
 * educación, hobbies) — todos editables desde el mismo lugar en wp-admin.
 *
 * Standalone igual que templates/page-home-estavillo.php: imprime su
 * propio wp_head()/wp_footer() y reusa el chrome ESTAVILLO.
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
<body <?php body_class( 'es-page es-about-page' ); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
?>

<div id="es-page" class="es-page">

	<?php get_template_part( 'template-parts/site-header' ); ?>
	<?php get_template_part( 'template-parts/breadcrumbs', null, array( 'trail' => es_breadcrumb_trail( 'nav_about' ) ) ); ?>

	<main id="top" class="es-main">
		<?php
		/*
		 * Polish ticket §5 — editorial hierarchy. The breadcrumb already says
		 * "About" and the H1 says "About me.", so echoing "About" a third time
		 * in the eyebrow was redundant. The eyebrow now carries a distinct
		 * role: the professional-identity kicker (same positioning line as the
		 * Home hero), so each level reads differently — breadcrumb = location,
		 * eyebrow = identity, H1 = page title. About-page only; the shared
		 * es__('about_label') string (Home About teaser) is left untouched.
		 */
		get_template_part(
			'template-parts/page-head',
			null,
			array(
				'eyebrow' => es__( 'about_eyebrow' ),
				'title'   => es__( 'about_title' ),
			)
		);

		/**
		 * Gutenberg migration (architecture ticket, in progress): the About
		 * page body is meant to become real, per-language editable Page
		 * content — the same the_content() pattern single-es_case_study.php
		 * already uses — but until the real English/Spanish pages are
		 * populated and approved, this stays an EXPLICIT, mutually
		 * exclusive branch: real content → the_content(); no real content →
		 * the existing template-parts/about-content.php fallback (same
		 * theme-default content it always rendered). Never both in the
		 * same request, so nothing double-renders once real content goes
		 * live. Do not remove the fallback branch until both languages are
		 * live and this has been explicitly approved — see docs/BACKLOG.md.
		 */
		$es_about_content = '';
		if ( have_posts() ) :
			the_post();
			$es_about_content = trim( get_the_content() );
		endif;

		if ( '' !== $es_about_content ) :
			the_content();
		else :
			get_template_part( 'template-parts/about-content' );
		endif;
		?>
	</main>

	<?php get_template_part( 'template-parts/site-footer' ); ?>

</div>

<?php wp_footer(); ?>
</body>
</html>
