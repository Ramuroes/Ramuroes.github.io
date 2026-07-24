<?php
/**
 * About — contenido completo de templates/page-about.php.
 *
 * Reusa los mismos filtros que ya alimentan el teaser de About en Home
 * (es_home_about_text / es_home_about_portrait — Sprint 3) más los
 * filtros de esta página (es_about_cv_url, es_about_experience_selected,
 * es_about_experience_previous, es_about_education,
 * es_about_certifications_other, es_about_languages), todos con UI en
 * Home Content (wp-admin). Cada sección es opcional: si no hay filas
 * cargadas todavía, esa sección entera no se imprime — mismo principio
 * "vacío no rompe nada" que el resto del tema, acá aplicado sección por
 * sección en vez de campo por campo.
 *
 * Contenido de toda la historia profesional (Selected/Previous
 * Experience, Education, Other Certifications, Languages) viene de
 * docs/about-page-authoritative-source.md — no se reconstruyó de memoria
 * ni se sustituyó por copy genérico.
 *
 * Hobbies & interests es la excepción histórica: sale de
 * es_about_hobbies_visible() (functions.php), que YA aplica sus propios
 * defaults (9 intereses) — así que a diferencia del resto, esta sección
 * SIEMPRE tiene contenido real de entrada.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_about_text     = apply_filters( 'es_home_about_text', es_about_intro_default() );
$es_about_portrait   = apply_filters( 'es_home_about_portrait', '' );
$es_cv_url           = apply_filters( 'es_about_cv_url', '' );
$es_exp_selected     = apply_filters( 'es_about_experience_selected', es_about_experience_selected_defaults() );
$es_exp_previous     = apply_filters( 'es_about_experience_previous', es_about_experience_previous_defaults() );
$es_education        = apply_filters( 'es_about_education', es_about_education_defaults() );
$es_certs_other      = apply_filters( 'es_about_certifications_other', es_about_certifications_other_defaults() );
$es_languages        = apply_filters( 'es_about_languages', es_about_languages_defaults() );
$es_hobbies          = es_about_hobbies_visible();
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
					<h2 class="es-label"><?php echo esc_html( es__( 'about_intro_label' ) ); ?></h2>
				</div>
			</div>
			<?php foreach ( es_about_intro_paragraphs( $es_about_text ) as $es_about_para ) : ?>
				<p class="es-about-page__text"><?php echo esc_html( $es_about_para ); ?></p>
			<?php endforeach; ?>
			<?php if ( ! empty( $es_cv_url ) ) : ?>
				<a class="es-btn" href="<?php echo esc_url( $es_cv_url ); ?>" target="_blank" rel="noopener">
					<?php esc_html_e( 'Download CV', 'estavillo-child' ); ?>
					<span class="es-btn__arrow" aria-hidden="true">&darr;</span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php if ( ! empty( $es_exp_selected ) || ! empty( $es_exp_previous ) ) : ?>
<section class="es-section es-about-page__experience" id="experience">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num">02</span>
				<h2 class="es-label"><?php esc_html_e( 'Experience', 'estavillo-child' ); ?></h2>
			</div>
		</div>
		<?php if ( ! empty( $es_exp_selected ) ) : ?>
		<div class="es-exp-list">
			<?php foreach ( $es_exp_selected as $es_exp ) : ?>
				<?php es_about_render_experience_item( $es_exp ); ?>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
		<?php if ( ! empty( $es_exp_previous ) ) : ?>
		<?php // Polish ticket §1: earlier roles are a continuation of Experience (a disclosure), not a separate numbered section. ?>
		<details class="es-about-details es-about-details--group" data-es-reveal>
			<summary><?php esc_html_e( 'Earlier experience', 'estavillo-child' ); ?></summary>
			<div class="es-about-details__body">
				<div class="es-exp-list es-exp-list--earlier">
					<?php foreach ( $es_exp_previous as $es_exp ) : ?>
						<?php es_about_render_experience_item( $es_exp, true ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</details>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php if ( ! empty( $es_education ) || ! empty( $es_certs_other ) ) : ?>
<section class="es-section es-about-page__education" id="education">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num">03</span>
				<h2 class="es-label"><?php esc_html_e( 'Education & Certifications', 'estavillo-child' ); ?></h2>
			</div>
		</div>
		<?php if ( ! empty( $es_education ) ) : ?>
		<div class="es-grid es-grid--2">
			<?php foreach ( $es_education as $es_i => $es_entry ) : ?>
				<?php if ( empty( $es_entry['title'] ) ) { continue; } ?>
				<div class="es-cred" data-es-reveal style="--es-reveal-delay: <?php echo esc_attr( $es_i * 60 ); ?>ms">
					<div class="es-cred__title"><?php echo esc_html( $es_entry['title'] ); ?></div>
					<?php
					$es_edu_meta = array_filter( array( $es_entry['org'] ?? '', $es_entry['meta'] ?? '', $es_entry['year'] ?? '' ) );
					if ( ! empty( $es_edu_meta ) ) :
						?>
						<div class="es-cred__meta"><?php echo esc_html( implode( ' · ', $es_edu_meta ) ); ?></div>
					<?php endif; ?>
					<?php if ( ! empty( $es_entry['description'] ) ) : ?>
						<p class="es-cred__description"><?php echo esc_html( $es_entry['description'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $es_entry['final_project'] ) ) : ?>
						<p class="es-cred__final-project"><?php echo esc_html( $es_entry['final_project'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $es_entry['link'] ) ) : ?>
						<a class="es-cred__link" href="<?php echo esc_url( $es_entry['link'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View credential', 'estavillo-child' ); ?> &rarr;</a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $es_certs_other ) ) : ?>
		<?php // Polish ticket §2: other certifications stay inside Education (a disclosure), not a separate numbered section. ?>
		<details class="es-about-details es-about-details--group" data-es-reveal>
			<summary><?php esc_html_e( 'Other certifications', 'estavillo-child' ); ?></summary>
			<div class="es-about-details__body">
				<ul class="es-cert-list">
					<?php foreach ( $es_certs_other as $es_cert ) : ?>
						<?php if ( empty( $es_cert['name'] ) ) { continue; } ?>
						<li class="es-cert-item">
							<div class="es-cert-item__name"><?php echo esc_html( $es_cert['name'] ); ?></div>
							<?php
							$es_cert_meta = array_filter(
								array(
									$es_cert['issuer'] ?? '',
									$es_cert['date'] ?? '',
									! empty( $es_cert['credential_id'] ) ? sprintf( /* translators: %s: credential ID */ __( 'ID %s', 'estavillo-child' ), $es_cert['credential_id'] ) : '',
								)
							);
							if ( ! empty( $es_cert_meta ) ) :
								?>
								<div class="es-cert-item__meta"><?php echo esc_html( implode( ' · ', $es_cert_meta ) ); ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $es_cert['link'] ) ) : ?>
								<a class="es-cred__link" href="<?php echo esc_url( $es_cert['link'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View credential', 'estavillo-child' ); ?> &rarr;</a>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</details>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php if ( ! empty( $es_languages ) ) : ?>
<section class="es-section es-about-page__languages" id="languages">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num">04</span>
				<h2 class="es-label"><?php esc_html_e( 'Languages', 'estavillo-child' ); ?></h2>
			</div>
		</div>
		<ul class="es-lang-list" data-es-reveal>
			<?php foreach ( $es_languages as $es_lang ) : ?>
				<?php if ( empty( $es_lang['name'] ) ) { continue; } ?>
				<li class="es-lang-item">
					<?php echo esc_html( $es_lang['name'] ); ?>
					<?php if ( ! empty( $es_lang['level'] ) ) : ?>
						<span class="es-lang-item__level"><?php echo esc_html( $es_lang['level'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
<?php endif; ?>

<?php if ( ! empty( $es_hobbies ) ) : ?>
<section class="es-section es-about-page__hobbies" id="hobbies">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num">05</span>
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
