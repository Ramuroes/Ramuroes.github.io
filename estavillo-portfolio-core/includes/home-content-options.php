<?php
/**
 * Portfolio Content (nombre visible en wp-admin — antes "Home Content",
 * renombrado en el sprint de infra/polish porque esta página ya no es
 * solo de Home: alimenta también Work/About/How I Work/Contact. El slug
 * del submenú, el option key y todos los nombres de función/filtro se
 * mantienen igual a propósito — ver comentario en
 * es_portfolio_home_content_menu() más abajo) — página de opciones para
 * las secciones singulares compartidas (About, How I Work, Connect,
 * Header, Footer) que no son repetibles como Case Study.
 *
 * Reusa los filtros que YA existen en el tema desde Home v1
 * (es_home_about_text, es_home_process_steps, es_contact_email,
 * es_nav_links, etc. — documentados en estavillo-child/README.md desde el
 * principio como el mecanismo de edición por Code Snippets). Esta página
 * no inventa un mecanismo nuevo: le da una UI de wp-admin a extension
 * points que ya existían. Por eso ningún template del tema cambia para
 * estos tickets.
 *
 * Un solo option (es_portfolio_home_content, array asociativo) guarda
 * todos los campos. Si un campo queda vacío, su filtro no sobreescribe
 * nada y el tema sigue usando su propio default hardcodeado — mismo
 * principio de "Home nunca se rompe" que el Case Study CPT, aplicado por
 * campo individual en vez de por registro completo.
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lee el option completo (array asociativo; puede tener claves faltantes).
 *
 * @return array
 */
function es_portfolio_get_home_content() {
	return get_option( 'es_portfolio_home_content', array() );
}

/**
 * Registra la página de opciones como submenú de Case Studies.
 */
function es_portfolio_home_content_menu() {
	// Rename cosmético (sprint de infra/polish): el label visible pasa de
	// "Home Content" a "Portfolio Content" porque esta página ya no es
	// solo de Home — cubre Case Study hero/breadcrumbs indirectamente vía
	// nav_links, y alimenta directamente las 4 páginas fijas (Work/About/
	// How I Work/Contact). El slug ('es-portfolio-home-content') y el
	// option key (es_portfolio_home_content, ver
	// es_portfolio_get_home_content()) NO cambian — cambiarlos rompería la
	// URL del admin ya guardada como bookmark y perdería todo el contenido
	// ya cargado bajo el option key viejo. Ver README, sección "Where to
	// edit each part of the portfolio".
	add_submenu_page(
		'edit.php?post_type=' . ES_CASE_STUDY_CPT,
		__( 'Portfolio Content', 'estavillo-portfolio-core' ),
		__( 'Portfolio Content', 'estavillo-portfolio-core' ),
		'manage_options',
		'es-portfolio-home-content',
		'es_portfolio_home_content_page'
	);
}
add_action( 'admin_menu', 'es_portfolio_home_content_menu' );

/**
 * Guarda el formulario de Home Content (todas las secciones a la vez).
 */
function es_portfolio_home_content_save() {
	if ( ! isset( $_POST['es_portfolio_home_content_nonce'] ) || ! wp_verify_nonce( $_POST['es_portfolio_home_content_nonce'], 'es_portfolio_home_content_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$data = es_portfolio_get_home_content();

	// ---- About ----
	if ( isset( $_POST['es_about_text'] ) ) {
		$data['about_text'] = sanitize_textarea_field( wp_unslash( $_POST['es_about_text'] ) );
	}
	if ( isset( $_POST['es_about_url'] ) ) {
		$data['about_url'] = esc_url_raw( wp_unslash( $_POST['es_about_url'] ) );
	}
	if ( isset( $_POST['es_about_portrait'] ) ) {
		$data['about_portrait'] = esc_url_raw( wp_unslash( $_POST['es_about_portrait'] ) );
	}
	if ( isset( $_POST['es_about_cv_url'] ) ) {
		$data['about_cv_url'] = esc_url_raw( wp_unslash( $_POST['es_about_cv_url'] ) );
	}
	// Hobbies / interests: 8 filas (label, ícono, texto corto opcional,
	// show/hide). Reemplaza el campo de texto simple de una sprint atrás —
	// mismo patrón de merge por-fila que Timeline/Educación abajo, más un
	// checkbox "Show" propio (independiente de dejar el label vacío).
	if ( isset( $_POST['es_hobby_label'] ) && is_array( $_POST['es_hobby_label'] ) ) {
		$es_hobby_labels = wp_unslash( $_POST['es_hobby_label'] );
		$es_hobby_icons  = isset( $_POST['es_hobby_icon'] ) && is_array( $_POST['es_hobby_icon'] ) ? wp_unslash( $_POST['es_hobby_icon'] ) : array();
		$es_hobby_texts  = isset( $_POST['es_hobby_text'] ) && is_array( $_POST['es_hobby_text'] ) ? wp_unslash( $_POST['es_hobby_text'] ) : array();
		$es_hobby_shows  = isset( $_POST['es_hobby_show'] ) && is_array( $_POST['es_hobby_show'] ) ? wp_unslash( $_POST['es_hobby_show'] ) : array();
		$es_valid_icons  = function_exists( 'es_hobby_icon_choices' ) ? array_keys( es_hobby_icon_choices() ) : array();
		$es_hobbies      = array();
		foreach ( $es_hobby_labels as $i => $es_hobby_label ) {
			$es_icon_choice   = isset( $es_hobby_icons[ $i ] ) ? sanitize_key( $es_hobby_icons[ $i ] ) : '';
			$es_hobbies[ $i ] = array(
				'label' => sanitize_text_field( $es_hobby_label ),
				'icon'  => in_array( $es_icon_choice, $es_valid_icons, true ) ? $es_icon_choice : '',
				'text'  => sanitize_text_field( $es_hobby_texts[ $i ] ?? '' ),
				'show'  => isset( $es_hobby_shows[ $i ] ),
			);
		}
		$data['about_hobbies_items'] = $es_hobbies;
	}

	// Timeline / education: mismo patrón de merge por-fila que How I Work y
	// Header abajo — una fila con título vacío no pisa nada.
	if ( isset( $_POST['es_about_timeline_title'] ) && is_array( $_POST['es_about_timeline_title'] ) ) {
		$es_tl_titles = wp_unslash( $_POST['es_about_timeline_title'] );
		$es_tl_years  = isset( $_POST['es_about_timeline_year'] ) && is_array( $_POST['es_about_timeline_year'] ) ? wp_unslash( $_POST['es_about_timeline_year'] ) : array();
		$es_tl_texts  = isset( $_POST['es_about_timeline_text'] ) && is_array( $_POST['es_about_timeline_text'] ) ? wp_unslash( $_POST['es_about_timeline_text'] ) : array();
		$es_timeline  = array();
		foreach ( $es_tl_titles as $i => $es_tl_title ) {
			$es_timeline[ $i ] = array(
				'year'  => sanitize_text_field( $es_tl_years[ $i ] ?? '' ),
				'title' => sanitize_text_field( $es_tl_title ),
				'text'  => sanitize_text_field( $es_tl_texts[ $i ] ?? '' ),
			);
		}
		$data['about_timeline'] = $es_timeline;
	}

	if ( isset( $_POST['es_about_edu_title'] ) && is_array( $_POST['es_about_edu_title'] ) ) {
		$es_edu_titles = wp_unslash( $_POST['es_about_edu_title'] );
		$es_edu_orgs   = isset( $_POST['es_about_edu_org'] ) && is_array( $_POST['es_about_edu_org'] ) ? wp_unslash( $_POST['es_about_edu_org'] ) : array();
		$es_edu_years  = isset( $_POST['es_about_edu_year'] ) && is_array( $_POST['es_about_edu_year'] ) ? wp_unslash( $_POST['es_about_edu_year'] ) : array();
		$es_education  = array();
		foreach ( $es_edu_titles as $i => $es_edu_title ) {
			$es_education[ $i ] = array(
				'title' => sanitize_text_field( $es_edu_title ),
				'org'   => sanitize_text_field( $es_edu_orgs[ $i ] ?? '' ),
				'year'  => sanitize_text_field( $es_edu_years[ $i ] ?? '' ),
			);
		}
		$data['about_education'] = $es_education;
	}

	// ---- How I Work ----
	// 'why' / 'example' / 'tools' son opcionales y solo se muestran en la
	// página dedicada How I Work (el teaser de Home se queda compacto,
	// solo título+texto+ícono — ver template-parts/how-i-work.php vs.
	// template-parts/how-i-work-detail.php).
	if ( isset( $_POST['es_process_step_title'] ) && is_array( $_POST['es_process_step_title'] ) ) {
		$titles      = wp_unslash( $_POST['es_process_step_title'] );
		$texts       = isset( $_POST['es_process_step_text'] ) && is_array( $_POST['es_process_step_text'] ) ? wp_unslash( $_POST['es_process_step_text'] ) : array();
		$icons_raw   = isset( $_POST['es_process_step_icon'] ) && is_array( $_POST['es_process_step_icon'] ) ? wp_unslash( $_POST['es_process_step_icon'] ) : array();
		$whys        = isset( $_POST['es_process_step_why'] ) && is_array( $_POST['es_process_step_why'] ) ? wp_unslash( $_POST['es_process_step_why'] ) : array();
		$examples    = isset( $_POST['es_process_step_example'] ) && is_array( $_POST['es_process_step_example'] ) ? wp_unslash( $_POST['es_process_step_example'] ) : array();
		$tools       = isset( $_POST['es_process_step_tools'] ) && is_array( $_POST['es_process_step_tools'] ) ? wp_unslash( $_POST['es_process_step_tools'] ) : array();
		$valid_icons = function_exists( 'es_process_icon_choices' ) ? array_keys( es_process_icon_choices() ) : array();
		$steps       = array();
		foreach ( $titles as $i => $title ) {
			$icon_choice = isset( $icons_raw[ $i ] ) ? sanitize_key( $icons_raw[ $i ] ) : '';
			$steps[ $i ] = array(
				'title'    => sanitize_text_field( $title ),
				'text'     => sanitize_text_field( $texts[ $i ] ?? '' ),
				'icon_key' => in_array( $icon_choice, $valid_icons, true ) ? $icon_choice : '',
				'why'      => sanitize_text_field( $whys[ $i ] ?? '' ),
				'example'  => sanitize_text_field( $examples[ $i ] ?? '' ),
				'tools'    => sanitize_text_field( $tools[ $i ] ?? '' ),
			);
		}
		$data['process_steps'] = $steps;
	}
	if ( isset( $_POST['es_process_url'] ) ) {
		$data['process_url'] = esc_url_raw( wp_unslash( $_POST['es_process_url'] ) );
	}

	// ---- Connect ----
	if ( isset( $_POST['es_cta_title'] ) ) {
		$data['cta_title'] = wp_kses( wp_unslash( $_POST['es_cta_title'] ), array( 'em' => array() ) );
	}
	if ( isset( $_POST['es_cta_lead'] ) ) {
		$data['cta_lead'] = sanitize_textarea_field( wp_unslash( $_POST['es_cta_lead'] ) );
	}
	if ( isset( $_POST['es_contact_email'] ) ) {
		$data['contact_email'] = sanitize_email( wp_unslash( $_POST['es_contact_email'] ) );
	}
	if ( isset( $_POST['es_connect_url'] ) ) {
		$data['connect_url'] = esc_url_raw( wp_unslash( $_POST['es_connect_url'] ) );
	}
	// Ambos opcionales, solo usados por la página dedicada Contact (ver
	// template-parts/contact-content.php) — Home nunca los muestra.
	if ( isset( $_POST['es_connect_note'] ) ) {
		$data['connect_note'] = sanitize_text_field( wp_unslash( $_POST['es_connect_note'] ) );
	}
	if ( isset( $_POST['es_connect_status'] ) ) {
		$data['connect_status'] = sanitize_text_field( wp_unslash( $_POST['es_connect_status'] ) );
	}

	// ---- Header (nav links) ----
	if ( isset( $_POST['es_nav_link_label'] ) && is_array( $_POST['es_nav_link_label'] ) ) {
		$labels = wp_unslash( $_POST['es_nav_link_label'] );
		$urls   = isset( $_POST['es_nav_link_url'] ) && is_array( $_POST['es_nav_link_url'] ) ? wp_unslash( $_POST['es_nav_link_url'] ) : array();
		$links  = array();
		foreach ( $labels as $i => $label ) {
			$links[ $i ] = array(
				'label' => sanitize_text_field( $label ),
				'url'   => isset( $urls[ $i ] ) ? esc_url_raw( $urls[ $i ] ) : '',
			);
		}
		$data['nav_links'] = $links;
	}

	// ---- Footer (social links + location) ----
	if ( isset( $_POST['es_social_linkedin'] ) ) {
		$data['social_linkedin'] = esc_url_raw( wp_unslash( $_POST['es_social_linkedin'] ) );
	}
	if ( isset( $_POST['es_social_behance'] ) ) {
		$data['social_behance'] = esc_url_raw( wp_unslash( $_POST['es_social_behance'] ) );
	}
	if ( isset( $_POST['es_footer_location'] ) ) {
		$data['footer_location'] = sanitize_text_field( wp_unslash( $_POST['es_footer_location'] ) );
	}

	update_option( 'es_portfolio_home_content', $data );
	add_action( 'admin_notices', 'es_portfolio_home_content_saved_notice' );
}
add_action(
	'admin_init',
	function () {
		if ( isset( $_POST['es_portfolio_home_content_submit'] ) ) {
			es_portfolio_home_content_save();
		}
	}
);

/**
 * Aviso de "guardado" tras el submit.
 */
function es_portfolio_home_content_saved_notice() {
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Home content saved.', 'estavillo-portfolio-core' ) . '</p></div>';
}

/**
 * Renderiza la página de opciones "Home Content".
 */
function es_portfolio_home_content_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$data = es_portfolio_get_home_content();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Portfolio Content', 'estavillo-portfolio-core' ); ?></h1>
		<p><?php esc_html_e( 'Edit the singular sections shared across the site: Home, the Work/About/How I Work/Contact pages, and the header/footer. Leave a field blank to keep the current placeholder for that field — nothing here ever breaks a page.', 'estavillo-portfolio-core' ); ?></p>
		<form method="post">
			<?php wp_nonce_field( 'es_portfolio_home_content_save', 'es_portfolio_home_content_nonce' ); ?>

			<h2><?php esc_html_e( 'About', 'estavillo-portfolio-core' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="es_about_text"><?php esc_html_e( 'About text', 'estavillo-portfolio-core' ); ?></label></th>
					<td><textarea id="es_about_text" name="es_about_text" rows="5" class="large-text"><?php echo esc_textarea( $data['about_text'] ?? '' ); ?></textarea></td>
				</tr>
				<tr>
					<th scope="row"><label for="es_about_url"><?php esc_html_e( 'About link (CTA URL)', 'estavillo-portfolio-core' ); ?></label></th>
					<td><input type="url" id="es_about_url" name="es_about_url" class="regular-text" value="<?php echo esc_attr( $data['about_url'] ?? '' ); ?>" placeholder="#about"></td>
				</tr>
				<tr>
					<th scope="row"><label for="es_about_portrait"><?php esc_html_e( 'Portrait image URL', 'estavillo-portfolio-core' ); ?></label></th>
					<td><input type="url" id="es_about_portrait" name="es_about_portrait" class="regular-text" value="<?php echo esc_attr( $data['about_portrait'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Leave blank to keep the placeholder frame', 'estavillo-portfolio-core' ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="es_about_cv_url"><?php esc_html_e( 'CV / résumé URL', 'estavillo-portfolio-core' ); ?></label></th>
					<td>
						<input type="url" id="es_about_cv_url" name="es_about_cv_url" class="regular-text" value="<?php echo esc_attr( $data['about_cv_url'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Link to a PDF (e.g. from Media Library) — leave blank to hide the download button on the About page', 'estavillo-portfolio-core' ); ?>">
					</td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Hobbies & interests (About page)', 'estavillo-portfolio-core' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Ships with 7 suggested interests already filled in — edit, reorder (by moving which row a label fills), hide with "Show", or replace with your own, up to 8 rows. Leave a row\'s label blank to remove it entirely. Short text is optional and only shown if filled in.', 'estavillo-portfolio-core' ); ?></p>
			<table class="form-table" role="presentation">
				<?php
				$es_hobbies_data  = $data['about_hobbies_items'] ?? array();
				$es_hobby_choices = function_exists( 'es_hobby_icon_choices' ) ? es_hobby_icon_choices() : array();
				$es_hobby_defaults = function_exists( 'es_home_about_hobbies_defaults' ) ? es_home_about_hobbies_defaults() : array();
				for ( $i = 0; $i < 8; $i++ ) :
					$es_hobby_saved = $es_hobbies_data[ $i ] ?? null;
					if ( null !== $es_hobby_saved ) {
						$es_hobby_label = $es_hobby_saved['label'] ?? '';
						$es_hobby_icon  = $es_hobby_saved['icon'] ?? '';
						$es_hobby_text  = $es_hobby_saved['text'] ?? '';
						$es_hobby_show  = ! empty( $es_hobby_saved['show'] );
					} else {
						// Sin guardar todavía: precarga los 7 sugeridos (show=true),
						// fila 8 vacía — así el admin VE el contenido real que ya
						// está publicado en la página, no un formulario en blanco.
						$es_hobby_label = $es_hobby_defaults[ $i ]['label'] ?? '';
						$es_hobby_icon  = $es_hobby_defaults[ $i ]['icon'] ?? '';
						$es_hobby_text  = $es_hobby_defaults[ $i ]['text'] ?? '';
						$es_hobby_show  = true;
					}
					// Claves legacy ('music', 'horse') → clave canónica del
					// artwork aprobado, para que el <select> muestre la opción
					// equivalente marcada (y el próximo guardado ya persista
					// la clave nueva) en vez de caer en "— None —".
					if ( '' !== $es_hobby_icon && function_exists( 'es_hobby_icon_resolve_key' ) ) {
						$es_hobby_icon = es_hobby_icon_resolve_key( $es_hobby_icon );
					}
					?>
					<tr>
						<th scope="row"><?php echo esc_html( sprintf( __( 'Interest %d', 'estavillo-portfolio-core' ), $i + 1 ) ); ?></th>
						<td>
							<input type="text" name="es_hobby_label[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_hobby_label ); ?>" placeholder="<?php esc_attr_e( 'Label', 'estavillo-portfolio-core' ); ?>">
							<?php if ( ! empty( $es_hobby_choices ) ) : ?>
								<select name="es_hobby_icon[<?php echo esc_attr( $i ); ?>]">
									<option value=""><?php esc_html_e( '— No icon —', 'estavillo-portfolio-core' ); ?></option>
									<?php foreach ( $es_hobby_choices as $es_hobby_icon_key => $es_hobby_icon_label ) : ?>
										<option value="<?php echo esc_attr( $es_hobby_icon_key ); ?>" <?php selected( $es_hobby_icon, $es_hobby_icon_key ); ?>><?php echo esc_html( $es_hobby_icon_label ); ?></option>
									<?php endforeach; ?>
								</select>
							<?php endif; ?>
							<label>
								<input type="checkbox" name="es_hobby_show[<?php echo esc_attr( $i ); ?>]" value="1" <?php checked( $es_hobby_show ); ?>>
								<?php esc_html_e( 'Show', 'estavillo-portfolio-core' ); ?>
							</label>
							<br>
							<input type="text" name="es_hobby_text[<?php echo esc_attr( $i ); ?>]" class="large-text" value="<?php echo esc_attr( $es_hobby_text ); ?>" placeholder="<?php esc_attr_e( 'Short text (optional)', 'estavillo-portfolio-core' ); ?>">
						</td>
					</tr>
				<?php endfor; ?>
			</table>

			<h3><?php esc_html_e( 'Career timeline (About page)', 'estavillo-portfolio-core' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Leave a row\'s title blank to keep it out of the timeline. Shown on the About page only, most recent first (order them as you want them displayed).', 'estavillo-portfolio-core' ); ?></p>
			<table class="form-table" role="presentation">
				<?php
				$es_timeline = $data['about_timeline'] ?? array();
				for ( $i = 0; $i < 4; $i++ ) :
					$es_tl_year  = $es_timeline[ $i ]['year'] ?? '';
					$es_tl_title = $es_timeline[ $i ]['title'] ?? '';
					$es_tl_text  = $es_timeline[ $i ]['text'] ?? '';
					?>
					<tr>
						<th scope="row"><?php echo esc_html( sprintf( __( 'Entry %d', 'estavillo-portfolio-core' ), $i + 1 ) ); ?></th>
						<td>
							<input type="text" name="es_about_timeline_year[<?php echo esc_attr( $i ); ?>]" class="small-text" value="<?php echo esc_attr( $es_tl_year ); ?>" placeholder="<?php esc_attr_e( 'Year(s)', 'estavillo-portfolio-core' ); ?>">
							<input type="text" name="es_about_timeline_title[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_tl_title ); ?>" placeholder="<?php esc_attr_e( 'Role / title', 'estavillo-portfolio-core' ); ?>"><br>
							<input type="text" name="es_about_timeline_text[<?php echo esc_attr( $i ); ?>]" class="large-text" value="<?php echo esc_attr( $es_tl_text ); ?>" placeholder="<?php esc_attr_e( 'Short description', 'estavillo-portfolio-core' ); ?>">
						</td>
					</tr>
				<?php endfor; ?>
			</table>

			<h3><?php esc_html_e( 'Education & certificates (About page)', 'estavillo-portfolio-core' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Leave a row\'s title blank to keep it out of the list.', 'estavillo-portfolio-core' ); ?></p>
			<table class="form-table" role="presentation">
				<?php
				$es_education = $data['about_education'] ?? array();
				for ( $i = 0; $i < 4; $i++ ) :
					$es_edu_title = $es_education[ $i ]['title'] ?? '';
					$es_edu_org   = $es_education[ $i ]['org'] ?? '';
					$es_edu_year  = $es_education[ $i ]['year'] ?? '';
					?>
					<tr>
						<th scope="row"><?php echo esc_html( sprintf( __( 'Entry %d', 'estavillo-portfolio-core' ), $i + 1 ) ); ?></th>
						<td>
							<input type="text" name="es_about_edu_title[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_edu_title ); ?>" placeholder="<?php esc_attr_e( 'Degree / certificate', 'estavillo-portfolio-core' ); ?>">
							<input type="text" name="es_about_edu_org[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_edu_org ); ?>" placeholder="<?php esc_attr_e( 'Institution', 'estavillo-portfolio-core' ); ?>">
							<input type="text" name="es_about_edu_year[<?php echo esc_attr( $i ); ?>]" class="small-text" value="<?php echo esc_attr( $es_edu_year ); ?>" placeholder="<?php esc_attr_e( 'Year', 'estavillo-portfolio-core' ); ?>">
						</td>
					</tr>
				<?php endfor; ?>
			</table>

			<h2><?php esc_html_e( 'How I Work', 'estavillo-portfolio-core' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Leave a step blank (both title and text) to keep its current placeholder — you can edit just one step without filling in all six. "Why it matters", "Example" and "Tools" are optional — the compact Home teaser never shows them (title + text + icon only); only the dedicated How I Work page does.', 'estavillo-portfolio-core' ); ?></p>
			<table class="form-table" role="presentation">
				<?php
				$es_steps        = $data['process_steps'] ?? array();
				$es_icon_choices = function_exists( 'es_process_icon_choices' ) ? es_process_icon_choices() : array();
				for ( $i = 0; $i < 6; $i++ ) :
					$es_step_title   = $es_steps[ $i ]['title'] ?? '';
					$es_step_text    = $es_steps[ $i ]['text'] ?? '';
					$es_step_icon    = $es_steps[ $i ]['icon_key'] ?? '';
					$es_step_why     = $es_steps[ $i ]['why'] ?? '';
					$es_step_example = $es_steps[ $i ]['example'] ?? '';
					$es_step_tools   = $es_steps[ $i ]['tools'] ?? '';
					?>
					<tr>
						<th scope="row"><?php echo esc_html( sprintf( __( 'Step %d', 'estavillo-portfolio-core' ), $i + 1 ) ); ?></th>
						<td>
							<input type="text" name="es_process_step_title[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_step_title ); ?>" placeholder="<?php esc_attr_e( 'Step title', 'estavillo-portfolio-core' ); ?>"><br>
							<input type="text" name="es_process_step_text[<?php echo esc_attr( $i ); ?>]" class="large-text" value="<?php echo esc_attr( $es_step_text ); ?>" placeholder="<?php esc_attr_e( 'Step description', 'estavillo-portfolio-core' ); ?>">
							<?php if ( ! empty( $es_icon_choices ) ) : ?>
								<br>
								<select name="es_process_step_icon[<?php echo esc_attr( $i ); ?>]">
									<option value=""><?php esc_html_e( '— No icon —', 'estavillo-portfolio-core' ); ?></option>
									<?php foreach ( $es_icon_choices as $es_icon_key => $es_icon_label ) : ?>
										<option value="<?php echo esc_attr( $es_icon_key ); ?>" <?php selected( $es_step_icon, $es_icon_key ); ?>><?php echo esc_html( $es_icon_label ); ?></option>
									<?php endforeach; ?>
								</select>
							<?php endif; ?>
							<br>
							<input type="text" name="es_process_step_why[<?php echo esc_attr( $i ); ?>]" class="large-text" value="<?php echo esc_attr( $es_step_why ); ?>" placeholder="<?php esc_attr_e( 'Why it matters (optional, dedicated page only)', 'estavillo-portfolio-core' ); ?>">
							<br>
							<input type="text" name="es_process_step_example[<?php echo esc_attr( $i ); ?>]" class="large-text" value="<?php echo esc_attr( $es_step_example ); ?>" placeholder="<?php esc_attr_e( 'Example / context (optional, dedicated page only)', 'estavillo-portfolio-core' ); ?>">
							<br>
							<input type="text" name="es_process_step_tools[<?php echo esc_attr( $i ); ?>]" class="large-text" value="<?php echo esc_attr( $es_step_tools ); ?>" placeholder="<?php esc_attr_e( 'Tools / methods, comma-separated (optional, dedicated page only)', 'estavillo-portfolio-core' ); ?>">
						</td>
					</tr>
				<?php endfor; ?>
				<tr>
					<th scope="row"><label for="es_process_url"><?php esc_html_e( 'How I Work link (CTA URL)', 'estavillo-portfolio-core' ); ?></label></th>
					<td><input type="url" id="es_process_url" name="es_process_url" class="regular-text" value="<?php echo esc_attr( $data['process_url'] ?? '' ); ?>" placeholder="#process"></td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Connect', 'estavillo-portfolio-core' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="es_cta_title"><?php esc_html_e( 'Connect title', 'estavillo-portfolio-core' ); ?></label></th>
					<td>
						<input type="text" id="es_cta_title" name="es_cta_title" class="large-text" value="<?php echo esc_attr( $data['cta_title'] ?? '' ); ?>" placeholder="Let's &lt;em&gt;talk.&lt;/em&gt;">
						<p class="description"><?php esc_html_e( 'The <em> tag is allowed here for emphasis, same as the current placeholder.', 'estavillo-portfolio-core' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="es_cta_lead"><?php esc_html_e( 'Connect lead text', 'estavillo-portfolio-core' ); ?></label></th>
					<td><textarea id="es_cta_lead" name="es_cta_lead" rows="3" class="large-text"><?php echo esc_textarea( $data['cta_lead'] ?? '' ); ?></textarea></td>
				</tr>
				<tr>
					<th scope="row"><label for="es_contact_email"><?php esc_html_e( 'Contact email', 'estavillo-portfolio-core' ); ?></label></th>
					<td><input type="email" id="es_contact_email" name="es_contact_email" class="regular-text" value="<?php echo esc_attr( $data['contact_email'] ?? '' ); ?>" placeholder="hello@ramiroestavillo.com"></td>
				</tr>
				<tr>
					<th scope="row"><label for="es_connect_url"><?php esc_html_e( 'Connect link (CTA URL)', 'estavillo-portfolio-core' ); ?></label></th>
					<td><input type="url" id="es_connect_url" name="es_connect_url" class="regular-text" value="<?php echo esc_attr( $data['connect_url'] ?? '' ); ?>" placeholder="#connect"></td>
				</tr>
				<tr>
					<th scope="row"><label for="es_connect_note"><?php esc_html_e( 'Secondary note (optional)', 'estavillo-portfolio-core' ); ?></label></th>
					<td>
						<input type="text" id="es_connect_note" name="es_connect_note" class="large-text" value="<?php echo esc_attr( $data['connect_note'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'e.g. Based in Montevideo, open to remote work — shown only on the Contact page', 'estavillo-portfolio-core' ); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="es_connect_status"><?php esc_html_e( 'Availability / status line (optional)', 'estavillo-portfolio-core' ); ?></label></th>
					<td>
						<input type="text" id="es_connect_status" name="es_connect_status" class="regular-text" value="<?php echo esc_attr( $data['connect_status'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'e.g. Currently available — leave blank to hide the status pill', 'estavillo-portfolio-core' ); ?>">
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Header (navigation links)', 'estavillo-portfolio-core' ); ?></h2>
			<p class="description"><?php esc_html_e( 'These 4 links are used in the header nav, the mobile menu, and the footer nav. Leave a row blank to keep its current label and URL.', 'estavillo-portfolio-core' ); ?></p>
			<table class="form-table" role="presentation">
				<?php
				$es_nav_links_data = $data['nav_links'] ?? array();
				for ( $i = 0; $i < 4; $i++ ) :
					$es_link_label = $es_nav_links_data[ $i ]['label'] ?? '';
					$es_link_url   = $es_nav_links_data[ $i ]['url'] ?? '';
					?>
					<tr>
						<th scope="row"><?php echo esc_html( sprintf( __( 'Nav link %d', 'estavillo-portfolio-core' ), $i + 1 ) ); ?></th>
						<td>
							<input type="text" name="es_nav_link_label[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_link_label ); ?>" placeholder="<?php esc_attr_e( 'Label', 'estavillo-portfolio-core' ); ?>">
							<input type="text" name="es_nav_link_url[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_link_url ); ?>" placeholder="<?php esc_attr_e( 'URL (e.g. #work or a real page URL)', 'estavillo-portfolio-core' ); ?>">
						</td>
					</tr>
				<?php endfor; ?>
			</table>

			<h2><?php esc_html_e( 'Footer', 'estavillo-portfolio-core' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Nav links and contact email are shared with Header and Connect above — only social links and location are footer-specific.', 'estavillo-portfolio-core' ); ?></p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="es_social_linkedin"><?php esc_html_e( 'LinkedIn URL', 'estavillo-portfolio-core' ); ?></label></th>
					<td><input type="url" id="es_social_linkedin" name="es_social_linkedin" class="regular-text" value="<?php echo esc_attr( $data['social_linkedin'] ?? '' ); ?>" placeholder="https://linkedin.com/in/..."></td>
				</tr>
				<tr>
					<th scope="row"><label for="es_social_behance"><?php esc_html_e( 'Behance URL', 'estavillo-portfolio-core' ); ?></label></th>
					<td><input type="url" id="es_social_behance" name="es_social_behance" class="regular-text" value="<?php echo esc_attr( $data['social_behance'] ?? '' ); ?>" placeholder="https://behance.net/..."></td>
				</tr>
				<tr>
					<th scope="row"><label for="es_footer_location"><?php esc_html_e( 'Location', 'estavillo-portfolio-core' ); ?></label></th>
					<td><input type="text" id="es_footer_location" name="es_footer_location" class="regular-text" value="<?php echo esc_attr( $data['footer_location'] ?? '' ); ?>" placeholder="Montevideo, Uruguay"></td>
				</tr>
			</table>

			<?php submit_button( __( 'Save Portfolio Content', 'estavillo-portfolio-core' ), 'primary', 'es_portfolio_home_content_submit' ); ?>
		</form>
	</div>
	<?php
}

/**
 * Puente hacia los filtros que YA existían en el tema (Home v1) para
 * About. No son filtros nuevos — ver el comentario de cabecera del
 * archivo. Cada callback solo pisa el default si el campo tiene valor.
 */
function es_portfolio_filter_about_text( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['about_text'] ) ? $data['about_text'] : $default;
}
add_filter( 'es_home_about_text', 'es_portfolio_filter_about_text' );

function es_portfolio_filter_about_url( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['about_url'] ) ? $data['about_url'] : $default;
}
add_filter( 'es_home_about_url', 'es_portfolio_filter_about_url' );

function es_portfolio_filter_about_portrait( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['about_portrait'] ) ? $data['about_portrait'] : $default;
}
add_filter( 'es_home_about_portrait', 'es_portfolio_filter_about_portrait' );

/**
 * Puentes para las secciones nuevas de la página About (CV, hobbies,
 * timeline, educación). Mismo principio "vacío no pisa nada" que el resto
 * de este archivo — si el campo nunca se llenó, la página About usa sus
 * propios defaults/placeholders (ver template-parts/about-content.php).
 */
function es_portfolio_filter_about_cv_url( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['about_cv_url'] ) ? $data['about_cv_url'] : $default;
}
add_filter( 'es_about_cv_url', 'es_portfolio_filter_about_cv_url' );

/**
 * Puente para Hobbies & interests: mismo merge por-fila que Timeline/
 * Educación (una fila con label vacío no pisa nada) más el flag 'show'
 * propio de cada fila (checkbox independiente de vaciar el label).
 */
function es_portfolio_filter_about_hobbies_items( $default ) {
	$data = es_portfolio_get_home_content();
	if ( empty( $data['about_hobbies_items'] ) || ! is_array( $data['about_hobbies_items'] ) ) {
		return $default;
	}
	$entries = array_filter(
		$data['about_hobbies_items'],
		function ( $entry ) {
			return ! empty( $entry['label'] );
		}
	);
	return $entries ? array_values( $entries ) : $default;
}
add_filter( 'es_about_hobbies_items', 'es_portfolio_filter_about_hobbies_items' );

function es_portfolio_filter_about_timeline( $default ) {
	$data = es_portfolio_get_home_content();
	if ( empty( $data['about_timeline'] ) || ! is_array( $data['about_timeline'] ) ) {
		return $default;
	}
	$entries = array_filter(
		$data['about_timeline'],
		function ( $entry ) {
			return ! empty( $entry['title'] );
		}
	);
	return $entries ? array_values( $entries ) : $default;
}
add_filter( 'es_about_timeline', 'es_portfolio_filter_about_timeline' );

function es_portfolio_filter_about_education( $default ) {
	$data = es_portfolio_get_home_content();
	if ( empty( $data['about_education'] ) || ! is_array( $data['about_education'] ) ) {
		return $default;
	}
	$entries = array_filter(
		$data['about_education'],
		function ( $entry ) {
			return ! empty( $entry['title'] );
		}
	);
	return $entries ? array_values( $entries ) : $default;
}
add_filter( 'es_about_education', 'es_portfolio_filter_about_education' );

/**
 * Puente para How I Work. A diferencia de About (campos sueltos), acá se
 * mergea PASO A PASO contra el default: un paso sin título editado en
 * Home Content no pisa nada y sigue mostrando el placeholder de ESE paso
 * puntual — así el editor puede tocar un solo paso sin tener que llenar
 * los 6. 'icon_key' selecciona de la librería curada del tema
 * (es_process_icon_choices() / es_process_icon_svg()) — nunca HTML libre.
 * 'why' / 'example' / 'tools' son opcionales: solo los lee la página
 * dedicada How I Work (template-parts/how-i-work-detail.php) — el teaser
 * de Home (template-parts/how-i-work.php) los ignora a propósito.
 */
function es_portfolio_filter_process_steps( $default ) {
	$data = es_portfolio_get_home_content();
	if ( empty( $data['process_steps'] ) || ! is_array( $data['process_steps'] ) ) {
		return $default;
	}

	$merged = $default;
	foreach ( $data['process_steps'] as $i => $custom_step ) {
		if ( empty( $custom_step['title'] ) ) {
			continue;
		}
		if ( ! isset( $merged[ $i ] ) ) {
			$merged[ $i ] = array( 'icon' => '' );
		}
		$merged[ $i ]['title'] = $custom_step['title'];
		$merged[ $i ]['text']  = $custom_step['text'] ?? '';
		if ( ! empty( $custom_step['icon_key'] ) ) {
			$merged[ $i ]['icon_key'] = $custom_step['icon_key'];
		}
		foreach ( array( 'why', 'example', 'tools' ) as $es_optional_field ) {
			if ( ! empty( $custom_step[ $es_optional_field ] ) ) {
				$merged[ $i ][ $es_optional_field ] = $custom_step[ $es_optional_field ];
			}
		}
	}
	return $merged;
}
add_filter( 'es_home_process_steps', 'es_portfolio_filter_process_steps' );

function es_portfolio_filter_process_url( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['process_url'] ) ? $data['process_url'] : $default;
}
add_filter( 'es_home_process_url', 'es_portfolio_filter_process_url' );

/**
 * Puente para Connect. es_contact_email es compartido con el footer (ver
 * es_portfolio_filter_contact_email más abajo): un solo campo alimenta el
 * mailto de las dos secciones — es el mismo dato, no dos independientes.
 */
function es_portfolio_filter_cta_title( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['cta_title'] ) ? $data['cta_title'] : $default;
}
add_filter( 'es_home_cta_title', 'es_portfolio_filter_cta_title' );

function es_portfolio_filter_cta_lead( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['cta_lead'] ) ? $data['cta_lead'] : $default;
}
add_filter( 'es_home_cta_lead', 'es_portfolio_filter_cta_lead' );

function es_portfolio_filter_contact_email( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['contact_email'] ) ? $data['contact_email'] : $default;
}
add_filter( 'es_contact_email', 'es_portfolio_filter_contact_email' );

function es_portfolio_filter_connect_url( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['connect_url'] ) ? $data['connect_url'] : $default;
}
add_filter( 'es_home_connect_url', 'es_portfolio_filter_connect_url' );

/**
 * Puentes para los 2 campos opcionales del Contact page (nota secundaria
 * y línea de disponibilidad/estado) — solo los lee
 * template-parts/contact-content.php, Home nunca los muestra.
 */
function es_portfolio_filter_connect_note( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['connect_note'] ) ? $data['connect_note'] : $default;
}
add_filter( 'es_connect_note', 'es_portfolio_filter_connect_note' );

function es_portfolio_filter_connect_status( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['connect_status'] ) ? $data['connect_status'] : $default;
}
add_filter( 'es_connect_status', 'es_portfolio_filter_connect_status' );

/**
 * Puente para Header: mismo merge por-item que How I Work. Un link sin
 * label editado no pisa nada y sigue mostrando ESE link puntual como
 * estaba (label + url originales) — así se puede editar uno solo sin
 * tocar los otros 3. Si se edita el label pero se deja la URL vacía, la
 * URL cae al default de ese mismo link (nunca queda un href vacío).
 * es_nav_links() se usa en el header, el menú mobile y el footer — un
 * solo array alimenta las tres, como ya era antes de este ticket.
 */
function es_portfolio_filter_nav_links( $default ) {
	$data = es_portfolio_get_home_content();
	if ( empty( $data['nav_links'] ) || ! is_array( $data['nav_links'] ) ) {
		return $default;
	}

	$merged = $default;
	foreach ( $data['nav_links'] as $i => $custom_link ) {
		if ( empty( $custom_link['label'] ) ) {
			continue;
		}
		$default_url = $merged[ $i ]['url'] ?? '#';
		$merged[ $i ] = array(
			'label' => $custom_link['label'],
			'url'   => ! empty( $custom_link['url'] ) ? $custom_link['url'] : $default_url,
		);
	}
	return $merged;
}
add_filter( 'es_nav_links', 'es_portfolio_filter_nav_links' );

/**
 * Puente para Footer. Nav links y email ya están cubiertos por Header y
 * Connect (mismos filtros, mismos datos) — acá solo faltan redes sociales
 * y ubicación, lo único realmente exclusivo del footer.
 */
function es_portfolio_filter_social_links( $default ) {
	$data = es_portfolio_get_home_content();
	if ( ! empty( $data['social_linkedin'] ) ) {
		$default['LinkedIn'] = $data['social_linkedin'];
	}
	if ( ! empty( $data['social_behance'] ) ) {
		$default['Behance'] = $data['social_behance'];
	}
	return $default;
}
add_filter( 'es_social_links', 'es_portfolio_filter_social_links' );

function es_portfolio_filter_footer_location( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['footer_location'] ) ? $data['footer_location'] : $default;
}
add_filter( 'es_footer_location', 'es_portfolio_filter_footer_location' );
