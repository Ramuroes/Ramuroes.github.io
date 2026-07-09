<?php
/**
 * Case Study — CPT, taxonomía y meta box.
 *
 * Movido acá desde el child theme (Sprint 3 extraction): esta es la única
 * fuente de verdad para el contenido de Case Study. El post type y la
 * taxonomía NO cambian de slug (es_case_study / es_case_tag), así que los
 * Case Studies ya creados en wp-admin siguen funcionando igual sin
 * importar si el registro corre desde el tema o desde este plugin.
 *
 * El puente hacia el tema es el filtro 'es_portfolio_case_studies_for_home':
 * este plugin agrega su callback acá abajo; si el plugin está inactivo el
 * filtro simplemente no tiene callbacks y el tema usa su propio fallback —
 * cero llamadas directas a funciones de este archivo desde el tema, así
 * que no hay forma de que un fatal error por "función no definida" ocurra
 * si el plugin está desactivado.
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ES_CASE_STUDY_CPT', 'es_case_study' );
define( 'ES_CASE_TAG_TAX', 'es_case_tag' );

/**
 * Registra el CPT "Case Study" y su taxonomía de tags.
 */
function es_register_case_study_cpt() {
	register_post_type(
		ES_CASE_STUDY_CPT,
		array(
			'labels'       => array(
				'name'               => __( 'Case Studies', 'estavillo-portfolio-core' ),
				'singular_name'      => __( 'Case Study', 'estavillo-portfolio-core' ),
				'add_new'            => __( 'Add New', 'estavillo-portfolio-core' ),
				'add_new_item'       => __( 'Add New Case Study', 'estavillo-portfolio-core' ),
				'edit_item'          => __( 'Edit Case Study', 'estavillo-portfolio-core' ),
				'new_item'           => __( 'New Case Study', 'estavillo-portfolio-core' ),
				'view_item'          => __( 'View Case Study', 'estavillo-portfolio-core' ),
				'search_items'       => __( 'Search Case Studies', 'estavillo-portfolio-core' ),
				'not_found'          => __( 'No case studies found', 'estavillo-portfolio-core' ),
				'not_found_in_trash' => __( 'No case studies found in Trash', 'estavillo-portfolio-core' ),
				'all_items'          => __( 'All Case Studies', 'estavillo-portfolio-core' ),
				'menu_name'          => __( 'Case Studies', 'estavillo-portfolio-core' ),
			),
			'public'       => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-portfolio',
			'hierarchical' => false,
			'has_archive'  => false,
			'rewrite'      => array( 'slug' => 'case-studies' ),
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		)
	);

	register_taxonomy(
		ES_CASE_TAG_TAX,
		ES_CASE_STUDY_CPT,
		array(
			'labels'            => array(
				'name'          => __( 'Case Tags', 'estavillo-portfolio-core' ),
				'singular_name' => __( 'Case Tag', 'estavillo-portfolio-core' ),
			),
			'hierarchical'      => false,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
		)
	);
}
add_action( 'init', 'es_register_case_study_cpt' );

/**
 * Meta box con los campos propios del caso (los demás — título, extracto,
 * imagen destacada, tags, orden — usan las cajas nativas de WordPress).
 */
function es_case_study_add_meta_box() {
	add_meta_box(
		'es_case_study_details',
		__( 'Case details (Selected Work)', 'estavillo-portfolio-core' ),
		'es_case_study_render_meta_box',
		ES_CASE_STUDY_CPT,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'es_case_study_add_meta_box' );

/**
 * Renderiza el meta box.
 *
 * @param WP_Post $post Post actual.
 */
function es_case_study_render_meta_box( $post ) {
	wp_nonce_field( 'es_case_study_save', 'es_case_study_nonce' );

	$kicker            = get_post_meta( $post->ID, '_es_case_kicker', true );
	$url               = get_post_meta( $post->ID, '_es_case_url', true );
	$label             = get_post_meta( $post->ID, '_es_case_label', true );
	$placeholder_label = get_post_meta( $post->ID, '_es_case_placeholder_label', true );
	$show_on_home_raw  = get_post_meta( $post->ID, '_es_case_show_on_home', true );
	$show_on_home      = ( '' === $show_on_home_raw ) ? true : ( '1' === $show_on_home_raw );
	?>
	<p>
		<label for="es_case_kicker"><strong><?php esc_html_e( 'Eyebrow / category', 'estavillo-portfolio-core' ); ?></strong></label><br>
		<input type="text" id="es_case_kicker" name="es_case_kicker" class="widefat" value="<?php echo esc_attr( $kicker ); ?>" placeholder="<?php esc_attr_e( 'e.g. Thesis · Digital education', 'estavillo-portfolio-core' ); ?>">
	</p>
	<p>
		<label for="es_case_url"><strong><?php esc_html_e( 'Case link (URL)', 'estavillo-portfolio-core' ); ?></strong></label><br>
		<input type="url" id="es_case_url" name="es_case_url" class="widefat" value="<?php echo esc_attr( $url ); ?>" placeholder="<?php esc_attr_e( "Leave blank to use this post's own URL", 'estavillo-portfolio-core' ); ?>">
	</p>
	<p>
		<label for="es_case_label"><strong><?php esc_html_e( 'Label / status (optional)', 'estavillo-portfolio-core' ); ?></strong></label><br>
		<input type="text" id="es_case_label" name="es_case_label" class="widefat" value="<?php echo esc_attr( $label ); ?>" placeholder="<?php esc_attr_e( 'e.g. Case 02, In progress', 'estavillo-portfolio-core' ); ?>">
	</p>
	<p>
		<label for="es_case_placeholder_label"><strong><?php esc_html_e( 'Placeholder tag text', 'estavillo-portfolio-core' ); ?></strong></label><br>
		<input type="text" id="es_case_placeholder_label" name="es_case_placeholder_label" class="widefat" value="<?php echo esc_attr( $placeholder_label ); ?>" placeholder="<?php esc_attr_e( 'Used only if no Featured Image is set. Defaults to the case title.', 'estavillo-portfolio-core' ); ?>">
	</p>
	<p>
		<label>
			<input type="checkbox" name="es_case_show_on_home" value="1" <?php checked( $show_on_home ); ?>>
			<?php esc_html_e( 'Show this case in Home → Selected Work', 'estavillo-portfolio-core' ); ?>
		</label>
	</p>
	<p class="description">
		<?php esc_html_e( 'Title, Excerpt, Featured Image, Tags and Order use the standard WordPress fields above / in the sidebar.', 'estavillo-portfolio-core' ); ?>
	</p>
	<?php
}

/**
 * Guarda los campos del meta box.
 *
 * @param int $post_id ID del post.
 */
function es_save_case_study_meta( $post_id ) {
	if ( ! isset( $_POST['es_case_study_nonce'] ) || ! wp_verify_nonce( $_POST['es_case_study_nonce'], 'es_case_study_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$text_fields = array( 'es_case_kicker', 'es_case_label', 'es_case_placeholder_label' );
	foreach ( $text_fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, '_' . $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}

	if ( isset( $_POST['es_case_url'] ) ) {
		update_post_meta( $post_id, '_es_case_url', esc_url_raw( wp_unslash( $_POST['es_case_url'] ) ) );
	}

	update_post_meta( $post_id, '_es_case_show_on_home', isset( $_POST['es_case_show_on_home'] ) ? '1' : '0' );
}
add_action( 'save_post_' . ES_CASE_STUDY_CPT, 'es_save_case_study_meta' );

/**
 * Case Studies publicados y marcados "mostrar en Home", en el shape que
 * espera selected-work.php en el tema (label, kicker, title, excerpt,
 * tags, url, image, placeholder_label).
 *
 * @return array<int,array> Vacío si no hay ninguno.
 */
function es_portfolio_get_case_studies_for_home() {
	$query = new WP_Query(
		array(
			'post_type'      => ES_CASE_STUDY_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => '_es_case_show_on_home',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'   => '_es_case_show_on_home',
					'value' => '1',
				),
			),
		)
	);

	if ( empty( $query->posts ) ) {
		return array();
	}

	$cases = array();
	foreach ( $query->posts as $es_post ) {
		$es_url  = get_post_meta( $es_post->ID, '_es_case_url', true );
		$cases[] = array(
			'label'             => get_post_meta( $es_post->ID, '_es_case_label', true ),
			'kicker'            => get_post_meta( $es_post->ID, '_es_case_kicker', true ),
			'title'             => get_the_title( $es_post ),
			'excerpt'           => get_the_excerpt( $es_post ),
			'tags'              => wp_list_pluck( wp_get_post_terms( $es_post->ID, ES_CASE_TAG_TAX ), 'name' ),
			'url'               => $es_url ? $es_url : get_permalink( $es_post ),
			'image'             => get_the_post_thumbnail_url( $es_post, 'large' ),
			'placeholder_label' => get_post_meta( $es_post->ID, '_es_case_placeholder_label', true ),
		);
	}

	return $cases;
}

/**
 * Callback del filtro 'es_portfolio_case_studies_for_home' que expone el
 * tema: si hay Case Studies reales los devuelve, si no deja pasar el
 * default recibido (el fallback del tema) sin tocarlo.
 *
 * @param array $default_cases Lo que el tema pasó como default (su fallback).
 * @return array<int,array>
 */
function es_portfolio_filter_case_studies_for_home( $default_cases ) {
	$real_cases = es_portfolio_get_case_studies_for_home();
	return $real_cases ? $real_cases : $default_cases;
}
add_filter( 'es_portfolio_case_studies_for_home', 'es_portfolio_filter_case_studies_for_home' );
