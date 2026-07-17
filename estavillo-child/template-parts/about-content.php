<?php
/**
 * About — contenido completo de templates/page-about.php.
 *
 * Reusa los mismos filtros que ya alimentan el teaser de About en Home
 * (es_home_about_text / es_home_about_portrait — Sprint 3) más los
 * filtros nuevos de esta página (es_about_cv_url, es_about_timeline,
 * es_about_education), todos con UI en Home Content (wp-admin). Cada
 * sección nueva es opcional: si no hay filas cargadas todavía, esa
 * sección entera no se imprime — mismo principio "vacío no rompe nada"
 * que el resto del tema, acá aplicado sección por sección en vez de
 * campo por campo.
 *
 * Hobbies & interests (sprint de infra/polish) es la excepción: sale de
 * es_about_hobbies_visible() (functions.php), que YA aplica sus propios
 * defaults (7 intereses sugeridos) — así que a diferencia de
 * timeline/educación, esta sección SÍ tiene contenido real de entrada.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_about_text     = apply_filters(
	'es_home_about_text',
	'I hold a Bachelor\'s Degree in Industrial Design (Product Design) — not a past title, but the foundation this practice still runs on. It\'s why interfaces are never my starting point: the system behind them is, with products, services and operations treated as one connected structure, understood before any part of it changes. Research stays part of the decision itself, not a report attached to it afterward, and the goal is always the same — turn a complex, often invisible process into something practical enough to use. AI increasingly helps accelerate that research, synthesis and documentation, but the decisions themselves stay grounded in human judgment.'
);
$es_about_portrait = apply_filters( 'es_home_about_portrait', '' );
$es_cv_url         = apply_filters( 'es_about_cv_url', '' );
$es_timeline       = apply_filters( 'es_about_timeline', es_about_timeline_defaults() );
$es_education      = apply_filters( 'es_about_education', es_about_education_defaults() );
$es_hobbies        = es_about_hobbies_visible();
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
		<ul class="es-hobbies__list" data-es-reveal>
			<?php foreach ( $es_hobbies as $es_i => $es_hobby ) : ?>
				<?php
				// La clave guardada puede ser legacy ('music', 'horse'):
				// se resuelve al alias canónico del artwork aprobado para
				// data-icon y para la clase semántica .es-hobby-icon--*,
				// así el CSS solo necesita conocer las claves nuevas.
				$es_hobby_icon_key = ! empty( $es_hobby['icon'] ) ? $es_hobby['icon'] : '';
				if ( $es_hobby_icon_key && function_exists( 'es_hobby_icon_resolve_key' ) ) {
					$es_hobby_icon_key = es_hobby_icon_resolve_key( $es_hobby_icon_key );
				}
				$es_hobby_icon_svg   = $es_hobby_icon_key && function_exists( 'es_hobby_icon_svg' ) ? es_hobby_icon_svg( $es_hobby_icon_key ) : '';
				$es_hobby_icon_class = 'es-hobby-item__icon es-hobby-icon';
				if ( '' === $es_hobby_icon_svg ) {
					$es_hobby_icon_class .= ' es-hobby-item__icon--empty';
				} else {
					$es_hobby_icon_class .= ' es-hobby-icon--' . $es_hobby_icon_key;
				}
				?>
				<li
					class="es-hobby-item"
					tabindex="0"
					data-icon="<?php echo esc_attr( $es_hobby_icon_key ? $es_hobby_icon_key : 'none' ); ?>"
					data-es-reveal
					style="--es-reveal-delay: <?php echo esc_attr( $es_i * 50 ); ?>ms"
				>
					<span class="<?php echo esc_attr( $es_hobby_icon_class ); ?>" aria-hidden="true">
						<?php
						if ( '' !== $es_hobby_icon_svg ) {
							echo wp_kses( $es_hobby_icon_svg, es_icon_svg_kses_rules() ); // phpcs:ignore -- whitelisted SVG, wp_kses aplicado acá mismo.
						}
						?>
					</span>
					<span class="es-hobby-item__label"><?php echo esc_html( $es_hobby['label'] ); ?></span>
					<?php if ( ! empty( $es_hobby['text'] ) ) : ?>
						<span class="es-hobby-item__text"><?php echo esc_html( $es_hobby['text'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
<?php endif; ?>
