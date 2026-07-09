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
