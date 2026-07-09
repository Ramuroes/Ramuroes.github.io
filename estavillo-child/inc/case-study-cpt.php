<?php
/**
 * Case Study — CPT que hace editable la sección Selected Work.
 *
 * Primera sección convertida a contenido editable en wp-admin (ver
 * docs/EDITABILITY-PLAN.md, Fase 3). Cada caso es un post propio:
 * título, extracto y flag "featured image" nativos de WordPress, más un
 * único meta box propio (eyebrow/kicker, URL, label/estado, texto del
 * placeholder, y si aparece en Home) y una taxonomía simple para tags.
 * El orden en Home usa el campo nativo "Order" (page-attributes).
 *
 * Si no hay ningún Case Study publicado con "mostrar en Home" activo,
 * Selected Work sigue mostrando los 3 casos placeholder de siempre — la
 * Home nunca queda vacía. Ver es_home_selected_work_source() más abajo.
 *
 * @package estavillo-child
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
				'name'               => __( 'Case Studies', 'estavillo-child' ),
				'singular_name'      => __( 'Case Study', 'estavillo-child' ),
				'add_new'            => __( 'Add New', 'estavillo-child' ),
				'add_new_item'       => __( 'Add New Case Study', 'estavillo-child' ),
				'edit_item'          => __( 'Edit Case Study', 'estavillo-child' ),
				'new_item'           => __( 'New Case Study', 'estavillo-child' ),
				'view_item'          => __( 'View Case Study', 'estavillo-child' ),
				'search_items'       => __( 'Search Case Studies', 'estavillo-child' ),
				'not_found'          => __( 'No case studies found', 'estavillo-child' ),
				'not_found_in_trash' => __( 'No case studies found in Trash', 'estavillo-child' ),
				'all_items'          => __( 'All Case Studies', 'estavillo-child' ),
				'menu_name'          => __( 'Case Studies', 'estavillo-child' ),
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
				'name'          => __( 'Case Tags', 'estavillo-child' ),
				'singular_name' => __( 'Case Tag', 'estavillo-child' ),
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
		__( 'Case details (Selected Work)', 'estavillo-child' ),
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
		<label for="es_case_kicker"><strong><?php esc_html_e( 'Eyebrow / category', 'estavillo-child' ); ?></strong></label><br>
		<input type="text" id="es_case_kicker" name="es_case_kicker" class="widefat" value="<?php echo esc_attr( $kicker ); ?>" placeholder="<?php esc_attr_e( 'e.g. Thesis · Digital education', 'estavillo-child' ); ?>">
	</p>
	<p>
		<label for="es_case_url"><strong><?php esc_html_e( 'Case link (URL)', 'estavillo-child' ); ?></strong></label><br>
		<input type="url" id="es_case_url" name="es_case_url" class="widefat" value="<?php echo esc_attr( $url ); ?>" placeholder="<?php esc_attr_e( "Leave blank to use this post's own URL", 'estavillo-child' ); ?>">
	</p>
	<p>
		<label for="es_case_label"><strong><?php esc_html_e( 'Label / status (optional)', 'estavillo-child' ); ?></strong></label><br>
		<input type="text" id="es_case_label" name="es_case_label" class="widefat" value="<?php echo esc_attr( $label ); ?>" placeholder="<?php esc_attr_e( 'e.g. Case 02, In progress', 'estavillo-child' ); ?>">
	</p>
	<p>
		<label for="es_case_placeholder_label"><strong><?php esc_html_e( 'Placeholder tag text', 'estavillo-child' ); ?></strong></label><br>
		<input type="text" id="es_case_placeholder_label" name="es_case_placeholder_label" class="widefat" value="<?php echo esc_attr( $placeholder_label ); ?>" placeholder="<?php esc_attr_e( 'Used only if no Featured Image is set. Defaults to the case title.', 'estavillo-child' ); ?>">
	</p>
	<p>
		<label>
			<input type="checkbox" name="es_case_show_on_home" value="1" <?php checked( $show_on_home ); ?>>
			<?php esc_html_e( 'Show this case in Home → Selected Work', 'estavillo-child' ); ?>
		</label>
	</p>
	<p class="description">
		<?php esc_html_e( 'Title, Excerpt, Featured Image, Tags and Order use the standard WordPress fields above / in the sidebar.', 'estavillo-child' ); ?>
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
 * espera selected-work.php (mismas claves que el array hardcodeado
 * anterior, más 'placeholder_label').
 *
 * @return array<int,array> Vacío si no hay ninguno.
 */
function es_get_case_studies_for_home() {
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
		$es_url    = get_post_meta( $es_post->ID, '_es_case_url', true );
		$cases[]   = array(
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
 * Casos placeholder (los mismos 3 de siempre) usados cuando no hay ningún
 * Case Study publicado todavía — la Home nunca queda vacía.
 *
 * @return array<int,array>
 */
function es_home_selected_work_fallback_cases() {
	return array(
		array(
			'label'   => 'Case 02',
			'kicker'  => 'Thesis · Digital education',
			'title'   => 'Trazur',
			'excerpt' => 'Degree thesis turned case study: research-driven redesign of an e-learning product — UX research, service design and applied AI in digital education.',
			'tags'    => array( 'UX Research', 'Service Design', 'Applied AI', 'E-learning' ),
			'url'     => '#',
			'image'   => null,
		),
		array(
			'label'   => 'Case 03',
			'kicker'  => 'Academic UX Case · Google UX Design Certificate',
			'title'   => 'French Bakery',
			'excerpt' => 'End-to-end UX process — research, wireframes, usability testing — from the Google UX Design Professional Certificate.',
			'tags'    => array(),
			'url'     => '#',
			'image'   => null,
		),
		array(
			'label'   => 'Case 04',
			'kicker'  => 'Legacy · Industrial e-commerce',
			'title'   => 'Samic',
			'excerpt' => 'E-commerce and visual systems for an industrial parts distributor.',
			'tags'    => array(),
			'url'     => '#',
			'image'   => null,
		),
	);
}

/**
 * Fuente de datos para 'es_home_selected_work': Case Studies reales si
 * existen, si no los placeholders de siempre. Se pasa por apply_filters()
 * en el template, así el filtro documentado en el README sigue funcionando
 * igual (puede seguir sobreescribiendo el resultado final).
 *
 * @return array<int,array>
 */
function es_home_selected_work_source() {
	$cases = es_get_case_studies_for_home();
	return $cases ? $cases : es_home_selected_work_fallback_cases();
}
