<?php
/**
 * Home Content — página de opciones para las secciones singulares de Home
 * (About, How I Work, Connect, Header, Footer) que no son repetibles como
 * Case Study.
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
	add_submenu_page(
		'edit.php?post_type=' . ES_CASE_STUDY_CPT,
		__( 'Home Content', 'estavillo-portfolio-core' ),
		__( 'Home Content', 'estavillo-portfolio-core' ),
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

	// ---- How I Work ----
	if ( isset( $_POST['es_process_step_title'] ) && is_array( $_POST['es_process_step_title'] ) ) {
		$titles = wp_unslash( $_POST['es_process_step_title'] );
		$texts  = isset( $_POST['es_process_step_text'] ) && is_array( $_POST['es_process_step_text'] ) ? wp_unslash( $_POST['es_process_step_text'] ) : array();
		$steps  = array();
		foreach ( $titles as $i => $title ) {
			$steps[ $i ] = array(
				'title' => sanitize_text_field( $title ),
				'text'  => sanitize_text_field( $texts[ $i ] ?? '' ),
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
		<h1><?php esc_html_e( 'Home Content', 'estavillo-portfolio-core' ); ?></h1>
		<p><?php esc_html_e( 'Edit the singular sections of Home. Leave a field blank to keep the current placeholder for that field — Home never breaks.', 'estavillo-portfolio-core' ); ?></p>
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
			</table>

			<h2><?php esc_html_e( 'How I Work', 'estavillo-portfolio-core' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Leave a step blank (both title and text) to keep its current placeholder — you can edit just one step without filling in all six.', 'estavillo-portfolio-core' ); ?></p>
			<table class="form-table" role="presentation">
				<?php
				$es_steps = $data['process_steps'] ?? array();
				for ( $i = 0; $i < 6; $i++ ) :
					$es_step_title = $es_steps[ $i ]['title'] ?? '';
					$es_step_text  = $es_steps[ $i ]['text'] ?? '';
					?>
					<tr>
						<th scope="row"><?php echo esc_html( sprintf( __( 'Step %d', 'estavillo-portfolio-core' ), $i + 1 ) ); ?></th>
						<td>
							<input type="text" name="es_process_step_title[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_step_title ); ?>" placeholder="<?php esc_attr_e( 'Step title', 'estavillo-portfolio-core' ); ?>"><br>
							<input type="text" name="es_process_step_text[<?php echo esc_attr( $i ); ?>]" class="large-text" value="<?php echo esc_attr( $es_step_text ); ?>" placeholder="<?php esc_attr_e( 'Step description', 'estavillo-portfolio-core' ); ?>">
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

			<?php submit_button( __( 'Save Home Content', 'estavillo-portfolio-core' ), 'primary', 'es_portfolio_home_content_submit' ); ?>
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
 * Puente para How I Work. A diferencia de About (campos sueltos), acá se
 * mergea PASO A PASO contra el default: un paso sin título editado en
 * Home Content no pisa nada y sigue mostrando el placeholder de ESE paso
 * puntual — así el editor puede tocar un solo paso sin tener que llenar
 * los 6. 'icon' nunca se toca (slot reservado a futuro, fuera de alcance).
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
