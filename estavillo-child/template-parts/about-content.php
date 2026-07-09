<?php
/**
 * About — contenido completo de templates/page-about.php.
 *
 * Reusa los mismos filtros que ya alimentan el teaser de About en Home
 * (es_home_about_text / es_home_about_portrait — Sprint 3) más 4 filtros
 * nuevos de esta página (es_about_cv_url, es_about_hobbies,
 * es_about_timeline, es_about_education), todos con UI en Home Content
 * (wp-admin). Cada sección nueva es opcional: si no hay filas cargadas
 * todavía, esa sección entera no se imprime — mismo principio "vacío no
 * rompe nada" que el resto del tema, acá aplicado sección por sección en
 * vez de campo por campo.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_about_text     = apply_filters(
	'es_home_about_text',
	"I'm Ramiro Estavillo, Product Designer. I studied Industrial Design and have spent over a decade designing across physical products, communication systems, digital interfaces and operational workflows. What connects all of it is a preference for understanding how things actually work before proposing how they could work better."
);
$es_about_portrait = apply_filters( 'es_home_about_portrait', '' );
$es_cv_url         = apply_filters( 'es_about_cv_url', '' );
$es_hobbies_raw    = apply_filters( 'es_about_hobbies', '' );
$es_timeline       = apply_filters( 'es_about_timeline', array() );
$es_education      = apply_filters( 'es_about_education', array() );

$es_hobbies = array();
if ( ! empty( $es_hobbies_raw ) ) {
	foreach ( explode( ',', $es_hobbies_raw ) as $es_hobby ) {
		$es_hobby = trim( $es_hobby );
		if ( '' !== $es_hobby ) {
			$es_hobbies[] = $es_hobby;
		}
	}
}
?>

<section class="es-section es-about-page__intro" id="about">
	<div class="es-container es-about__grid" data-es-reveal>
		<div class="es-about__portrait">
			<?php if ( ! empty( $es_about_portrait ) ) : ?>
				<img src="<?php echo esc_url( $es_about_portrait ); ?>" alt="" loading="lazy" />
			<?php else : ?>
				<div class="es-placeholder es-about__placeholder" role="img" aria-label="<?php esc_attr_e( 'Placeholder for the editorial portrait', 'estavillo-child' ); ?>">
					<span class="es-placeholder__tag">{asset: retrato}</span>
				</div>
			<?php endif; ?>
		</div>

		<div class="es-about__body">
			<div class="es-section-head">
				<div class="es-section-head__title">
					<span class="es-section-head__num">01</span>
					<h2 class="es-label"><?php echo esc_html( es__( 'about_label' ) ); ?></h2>
				</div>
			</div>
			<p class="es-about-page__text"><?php echo esc_html( $es_about_text ); ?></p>
			<?php if ( ! empty( $es_cv_url ) ) : ?>
				<a class="es-btn" href="<?php echo esc_url( $es_cv_url ); ?>" target="_blank" rel="noopener">
					<?php esc_html_e( 'Download CV', 'estavillo-child' ); ?>
					<span class="es-btn__arrow" aria-hidden="true">&darr;</span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php if ( ! empty( $es_timeline ) ) : ?>
<section class="es-section es-about-page__timeline" id="experience">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num">02</span>
				<h2 class="es-label"><?php esc_html_e( 'Experience', 'estavillo-child' ); ?></h2>
			</div>
		</div>
		<ol class="es-timeline">
			<?php foreach ( $es_timeline as $es_i => $es_entry ) : ?>
				<li class="es-timeline__item" data-es-reveal style="--es-reveal-delay: <?php echo esc_attr( $es_i * 60 ); ?>ms">
					<?php if ( ! empty( $es_entry['year'] ) ) : ?>
						<span class="es-timeline__year"><?php echo esc_html( $es_entry['year'] ); ?></span>
					<?php endif; ?>
					<h3 class="es-timeline__title"><?php echo esc_html( $es_entry['title'] ); ?></h3>
					<?php if ( ! empty( $es_entry['text'] ) ) : ?>
						<p class="es-timeline__text"><?php echo esc_html( $es_entry['text'] ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
<?php endif; ?>

<?php if ( ! empty( $es_education ) ) : ?>
<section class="es-section es-about-page__education" id="education">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num">03</span>
				<h2 class="es-label"><?php esc_html_e( 'Education & certificates', 'estavillo-child' ); ?></h2>
			</div>
		</div>
		<div class="es-grid es-grid--2">
			<?php foreach ( $es_education as $es_i => $es_entry ) : ?>
				<div class="es-cred" data-es-reveal style="--es-reveal-delay: <?php echo esc_attr( $es_i * 60 ); ?>ms">
					<div class="es-cred__title"><?php echo esc_html( $es_entry['title'] ); ?></div>
					<?php if ( ! empty( $es_entry['org'] ) || ! empty( $es_entry['year'] ) ) : ?>
						<div class="es-cred__meta">
							<?php echo esc_html( trim( implode( ' · ', array_filter( array( $es_entry['org'] ?? '', $es_entry['year'] ?? '' ) ) ) ) ); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( ! empty( $es_hobbies ) ) : ?>
<section class="es-section es-about-page__hobbies" id="hobbies">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num">04</span>
				<h2 class="es-label"><?php esc_html_e( 'Hobbies & interests', 'estavillo-child' ); ?></h2>
			</div>
		</div>
		<div class="es-hobbies__list" data-es-reveal>
			<?php foreach ( $es_hobbies as $es_hobby ) : ?>
				<span class="es-pill"><?php echo esc_html( $es_hobby ); ?></span>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>
