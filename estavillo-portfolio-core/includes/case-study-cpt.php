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
 * Variantes de layout del hero del Case Study (Sprint "mega" — hero
 * options). 'split-right' es el default histórico (texto izquierda /
 * imagen derecha, Sprint 4D) y no requiere CSS de modificador — el resto
 * son opt-in vía este select. Whitelist única (acá) usada tanto para
 * sanitizar al guardar como para pintar el <select> del meta box.
 *
 * @return array<string,string> valor => label legible.
 */
function es_case_hero_layout_choices() {
	return array(
		'split-right' => __( 'Split — image right (default)', 'estavillo-portfolio-core' ),
		'split-left'  => __( 'Split — image left', 'estavillo-portfolio-core' ),
		'compact'     => __( 'Compact — horizontal image, shorter frame (legacy)', 'estavillo-portfolio-core' ),
		'stacked'     => __( 'Stacked — full-width image', 'estavillo-portfolio-core' ),
	);
}

/**
 * Anchos de texto del hero, independientes del layout de imagen. Un caso
 * guardado ANTES de que este campo existiera no tiene este meta — el
 * template (single-es_case_study.php) resuelve ese caso a un default que
 * depende del hero layout ya guardado, para no cambiarle la composición
 * visual a un caso publicado sin que nadie lo haya pedido (ver el
 * comentario junto a $es_hero_text_width en el template).
 *
 * @return array<string,string> valor => label legible.
 */
function es_case_hero_text_width_choices() {
	return array(
		'editorial' => __( 'Editorial — controlled reading measure (default)', 'estavillo-portfolio-core' ),
		'wide'      => __( 'Wide — title uses most of the container, summary ~65–75%', 'estavillo-portfolio-core' ),
		'full'      => __( 'Full container — eyebrow, title, summary and tags use the complete width', 'estavillo-portfolio-core' ),
	);
}

/**
 * Tono del badge de estado del caso. Campo OPCIONAL y totalmente
 * independiente de "Label / status": ese sigue siendo texto libre y no se
 * deduce ni se migra nada a partir de su contenido. Este select sólo elige
 * el color del punto del badge; un caso guardado antes de que el campo
 * existiera no tiene este meta y cae a 'neutral', que es exactamente el
 * gris que ya se venía viendo — cero cambio visual para casos publicados.
 *
 * @return array<string,string> valor => label legible.
 */
function es_case_status_tone_choices() {
	return array(
		'neutral' => __( 'Neutral — no colour (default)', 'estavillo-portfolio-core' ),
		'green'   => __( 'Green — validated / live', 'estavillo-portfolio-core' ),
		'amber'   => __( 'Amber — in progress / MVP', 'estavillo-portfolio-core' ),
		'blue'    => __( 'Blue — in production', 'estavillo-portfolio-core' ),
		'purple'  => __( 'Purple — academic / research', 'estavillo-portfolio-core' ),
	);
}

/**
 * Categorías del archivo (ticket "Refine Work archive hierarchy"). Primera
 * versión SIMPLE a propósito: un select de meta, no una taxonomía nueva.
 *
 * Por qué no una taxonomía: son 5 valores fijos y cerrados, sin jerarquía,
 * sin necesidad de pantalla de gestión de términos propia ni de asociarse a
 * otros post types — un `get_post_meta()` de un solo valor cubre exactamente
 * eso, con el mismo patrón ya usado acá mismo para
 * es_case_status_tone_choices()/es_case_hero_layout_choices(). Registrar una
 * taxonomía para 5 opciones fijas habría sido más máquina de la que el
 * problema pide.
 *
 * Campo OPCIONAL: a diferencia de status_tone/hero_layout (que siempre
 * tienen un default), acá "sin categoría" es un estado válido — no todos los
 * casos necesitan una (Featured/Selected ya comunican categoría/tipo con el
 * campo "Eyebrow / category" existente; esto es sobre todo para el archivo,
 * donde SAMIC/French Bakery/etc. eventualmente se van a migrar de contenido
 * pegado a Case Studies reales — ver docs/handoff/WORK-PAGE-STRUCTURE.md).
 *
 * @return array<string,string> valor => label legible.
 */
function es_case_category_choices() {
	return array(
		'product-design'       => __( 'Product Design', 'estavillo-portfolio-core' ),
		'web-digital'          => __( 'Web & Digital', 'estavillo-portfolio-core' ),
		'industrial-3d'        => __( 'Industrial & 3D', 'estavillo-portfolio-core' ),
		'visual-motion'        => __( 'Visual & Motion', 'estavillo-portfolio-core' ),
		'academic-experiments' => __( 'Academic / Experiments', 'estavillo-portfolio-core' ),
	);
}

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
	$source            = get_post_meta( $post->ID, '_es_case_source', true );
	$role              = get_post_meta( $post->ID, '_es_case_role', true );
	$tools             = get_post_meta( $post->ID, '_es_case_tools', true );
	$period            = get_post_meta( $post->ID, '_es_case_period', true );
	$index             = get_post_meta( $post->ID, '_es_case_index', true );
	$hero_layout       = get_post_meta( $post->ID, '_es_case_hero_layout', true );
	if ( ! $hero_layout ) {
		$hero_layout = 'split-right';
	}
	$hero_text_width = get_post_meta( $post->ID, '_es_case_hero_text_width', true );
	if ( ! array_key_exists( $hero_text_width, es_case_hero_text_width_choices() ) ) {
		// Mismo fallback que el template del frontend: un caso guardado
		// antes de que este campo existiera con layout 'stacked' ya
		// renderizaba el texto sin el tope de 560px (ver CSS) — 'wide' es
		// el equivalente visual real, no 'editorial'. Cualquier otro
		// layout SÍ tenía ese tope, así que 'editorial' es su equivalente.
		$hero_text_width = ( 'stacked' === $hero_layout ) ? 'wide' : 'editorial';
	}
	// Campo nuevo, opcional: sin meta guardado cae a 'neutral' (gris), que
	// es lo que ya se veía antes de que existiera — no se toca ningún caso.
	$status_tone = get_post_meta( $post->ID, '_es_case_status_tone', true );
	if ( ! array_key_exists( $status_tone, es_case_status_tone_choices() ) ) {
		$status_tone = 'neutral';
	}
	$show_on_home_raw  = get_post_meta( $post->ID, '_es_case_show_on_home', true );
	$show_on_home      = ( '' === $show_on_home_raw ) ? true : ( '1' === $show_on_home_raw );
	$featured          = '1' === get_post_meta( $post->ID, '_es_case_featured', true );
	$category          = get_post_meta( $post->ID, '_es_case_category', true );
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
		<label for="es_case_status_tone"><strong><?php esc_html_e( 'Status tone (optional)', 'estavillo-portfolio-core' ); ?></strong></label><br>
		<select id="es_case_status_tone" name="es_case_status_tone">
			<?php foreach ( es_case_status_tone_choices() as $es_tone_key => $es_tone_label ) : ?>
				<option value="<?php echo esc_attr( $es_tone_key ); ?>" <?php selected( $status_tone, $es_tone_key ); ?>><?php echo esc_html( $es_tone_label ); ?></option>
			<?php endforeach; ?>
		</select>
		<span class="description"><?php esc_html_e( 'Colour of the small dot next to the status badge on this case\'s single page. Purely visual — it never changes the "Label / status" text above, and leaving it Neutral keeps the badge exactly as it looked before this field existed.', 'estavillo-portfolio-core' ); ?></span>
	</p>
	<p>
		<label for="es_case_placeholder_label"><strong><?php esc_html_e( 'Placeholder tag text', 'estavillo-portfolio-core' ); ?></strong></label><br>
		<input type="text" id="es_case_placeholder_label" name="es_case_placeholder_label" class="widefat" value="<?php echo esc_attr( $placeholder_label ); ?>" placeholder="<?php esc_attr_e( 'Used only if no Featured Image is set. Defaults to the case title.', 'estavillo-portfolio-core' ); ?>">
	</p>
	<p>
		<label for="es_case_source"><strong><?php esc_html_e( 'Source / context line (optional)', 'estavillo-portfolio-core' ); ?></strong></label><br>
		<input type="text" id="es_case_source" name="es_case_source" class="widefat" value="<?php echo esc_attr( $source ); ?>" placeholder="<?php esc_attr_e( 'e.g. Developed and implemented at ... — used only by Featured Case', 'estavillo-portfolio-core' ); ?>">
	</p>
	<p>
		<label for="es_case_role"><strong><?php esc_html_e( 'Role (optional)', 'estavillo-portfolio-core' ); ?></strong></label><br>
		<input type="text" id="es_case_role" name="es_case_role" class="widefat" value="<?php echo esc_attr( $role ); ?>" placeholder="<?php esc_attr_e( 'e.g. Product Designer — used only by the single Case Study page', 'estavillo-portfolio-core' ); ?>">
	</p>
	<p>
		<label for="es_case_tools"><strong><?php esc_html_e( 'Tools (optional)', 'estavillo-portfolio-core' ); ?></strong></label><br>
		<input type="text" id="es_case_tools" name="es_case_tools" class="widefat" value="<?php echo esc_attr( $tools ); ?>" placeholder="<?php esc_attr_e( 'e.g. Figma, Notion, Airtable', 'estavillo-portfolio-core' ); ?>">
	</p>
	<p>
		<label for="es_case_period"><strong><?php esc_html_e( 'Period (optional)', 'estavillo-portfolio-core' ); ?></strong></label><br>
		<input type="text" id="es_case_period" name="es_case_period" class="widefat" value="<?php echo esc_attr( $period ); ?>" placeholder="<?php esc_attr_e( 'e.g. 2024–2025', 'estavillo-portfolio-core' ); ?>">
	</p>
	<p>
		<label for="es_case_index"><strong><?php esc_html_e( 'Case index (optional)', 'estavillo-portfolio-core' ); ?></strong></label><br>
		<textarea id="es_case_index" name="es_case_index" class="widefat" rows="5" placeholder="<?php esc_attr_e( "One entry per line: Label|#anchor-id — e.g. Context|#context. Requires matching id=\"context\" attributes inside the body content. Leave empty to hide the sticky index entirely.", 'estavillo-portfolio-core' ); ?>"><?php echo esc_textarea( $index ); ?></textarea>
		<span class="description"><?php esc_html_e( 'Powers the sticky in-page index on this case\'s single page. One line per entry, format Label|#anchor-id.', 'estavillo-portfolio-core' ); ?></span>
	</p>
	<p>
		<label for="es_case_hero_layout"><strong><?php esc_html_e( 'Hero layout', 'estavillo-portfolio-core' ); ?></strong></label><br>
		<select id="es_case_hero_layout" name="es_case_hero_layout">
			<?php foreach ( es_case_hero_layout_choices() as $es_layout_key => $es_layout_label ) : ?>
				<option value="<?php echo esc_attr( $es_layout_key ); ?>" <?php selected( $hero_layout, $es_layout_key ); ?>><?php echo esc_html( $es_layout_label ); ?></option>
			<?php endforeach; ?>
		</select>
		<span class="description"><?php esc_html_e( 'Controls how the title/excerpt block and the featured image are arranged on this case\'s single page. Safe to change any time — no other field is affected.', 'estavillo-portfolio-core' ); ?></span>
	</p>
	<p>
		<label for="es_case_hero_text_width"><strong><?php esc_html_e( 'Hero text width', 'estavillo-portfolio-core' ); ?></strong></label><br>
		<select id="es_case_hero_text_width" name="es_case_hero_text_width">
			<?php foreach ( es_case_hero_text_width_choices() as $es_width_key => $es_width_label ) : ?>
				<option value="<?php echo esc_attr( $es_width_key ); ?>" <?php selected( $hero_text_width, $es_width_key ); ?>><?php echo esc_html( $es_width_label ); ?></option>
			<?php endforeach; ?>
		</select>
		<span class="description"><?php esc_html_e( 'Independent from Hero layout above — this only controls how wide the eyebrow/title/summary/tags block reads, not where the image sits. "Wide" is the recommended choice together with the "Stacked — full-width image" hero layout.', 'estavillo-portfolio-core' ); ?></span>
	</p>
	<p>
		<label for="es_case_category"><strong><?php esc_html_e( 'Archive category (optional)', 'estavillo-portfolio-core' ); ?></strong></label><br>
		<select id="es_case_category" name="es_case_category">
			<option value="" <?php selected( $category, '' ); ?>><?php esc_html_e( '— No category —', 'estavillo-portfolio-core' ); ?></option>
			<?php foreach ( es_case_category_choices() as $es_cat_key => $es_cat_label ) : ?>
				<option value="<?php echo esc_attr( $es_cat_key ); ?>" <?php selected( $category, $es_cat_key ); ?>><?php echo esc_html( $es_cat_label ); ?></option>
			<?php endforeach; ?>
		</select>
		<span class="description"><?php esc_html_e( 'Only shown on the Work page\'s "More Work" archive grid, as a small tag. Leave unset for Product Design cases — this is for sorting older/secondary work by discipline once it exists as real Case Studies.', 'estavillo-portfolio-core' ); ?></span>
	</p>
	<p>
		<label>
			<input type="checkbox" name="es_case_show_on_home" value="1" <?php checked( $show_on_home ); ?>>
			<?php esc_html_e( 'Show this case in Home → Selected Work', 'estavillo-portfolio-core' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="es_case_featured" value="1" <?php checked( $featured ); ?>>
			<?php esc_html_e( 'Feature this case on Home (Featured Case section)', 'estavillo-portfolio-core' ); ?>
		</label>
	</p>
	<p class="description">
		<?php esc_html_e( 'Title, Excerpt, Featured Image, Tags and Order use the standard WordPress fields above / in the sidebar. If this case is featured, its Excerpt becomes the Featured Case body paragraph and Label/status becomes its status pill — write the Excerpt to work for both sections if you also show this case in Selected Work. Role, Tools and Period are only used on this case\'s own single page — the body content below (or in the block editor) is that page\'s main content.', 'estavillo-portfolio-core' ); ?>
	</p>
	<p class="description">
		<?php esc_html_e( 'Note on the Work page specifically (different from Home): a featured case appears ONCE, in its own "Featured Work" section, and is excluded from Selected Work and Archive there — no double listing. On Home, Featured Case and Selected Work stay independent as before.', 'estavillo-portfolio-core' ); ?>
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

	$text_fields = array( 'es_case_kicker', 'es_case_label', 'es_case_placeholder_label', 'es_case_source', 'es_case_role', 'es_case_tools', 'es_case_period' );
	foreach ( $text_fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, '_' . $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}

	if ( isset( $_POST['es_case_url'] ) ) {
		update_post_meta( $post_id, '_es_case_url', esc_url_raw( wp_unslash( $_POST['es_case_url'] ) ) );
	}

	// Textarea multilinea: sanitize_text_field() colapsaría los saltos de
	// línea (formato "Label|#anchor" por línea), así que usa
	// sanitize_textarea_field() (nativo de WP) en vez del loop de arriba.
	if ( isset( $_POST['es_case_index'] ) ) {
		update_post_meta( $post_id, '_es_case_index', sanitize_textarea_field( wp_unslash( $_POST['es_case_index'] ) ) );
	}

	// Select contra whitelist: cualquier valor fuera de
	// es_case_hero_layout_choices() cae al default 'split-right', nunca se
	// guarda texto arbitrario.
	if ( isset( $_POST['es_case_hero_layout'] ) ) {
		$es_hero_layout_choice = sanitize_key( wp_unslash( $_POST['es_case_hero_layout'] ) );
		if ( ! array_key_exists( $es_hero_layout_choice, es_case_hero_layout_choices() ) ) {
			$es_hero_layout_choice = 'split-right';
		}
		update_post_meta( $post_id, '_es_case_hero_layout', $es_hero_layout_choice );
	}

	// Mismo criterio whitelist-o-default que hero_layout arriba. El
	// fallback que depende del layout (para posts guardados ANTES de que
	// este campo existiera) vive en el template del frontend, no acá: acá
	// el <select> siempre manda un valor explícito al guardar.
	if ( isset( $_POST['es_case_hero_text_width'] ) ) {
		$es_hero_text_width_choice = sanitize_key( wp_unslash( $_POST['es_case_hero_text_width'] ) );
		if ( ! array_key_exists( $es_hero_text_width_choice, es_case_hero_text_width_choices() ) ) {
			$es_hero_text_width_choice = 'editorial';
		}
		update_post_meta( $post_id, '_es_case_hero_text_width', $es_hero_text_width_choice );
	}

	// Mismo criterio whitelist-o-default que los dos selects de arriba. El
	// default 'neutral' es el gris de siempre: si alguien manda basura, el
	// badge queda como estaba, nunca con un color inventado.
	if ( isset( $_POST['es_case_status_tone'] ) ) {
		$es_status_tone_choice = sanitize_key( wp_unslash( $_POST['es_case_status_tone'] ) );
		if ( ! array_key_exists( $es_status_tone_choice, es_case_status_tone_choices() ) ) {
			$es_status_tone_choice = 'neutral';
		}
		update_post_meta( $post_id, '_es_case_status_tone', $es_status_tone_choice );
	}

	// Whitelist-o-vacío, no whitelist-o-default: a diferencia de hero_layout/
	// hero_text_width/status_tone (que siempre tienen un valor por defecto
	// con sentido propio), "sin categoría" es un estado válido acá — un
	// valor fuera de la whitelist cae a '' (sin categoría), nunca a una
	// categoría inventada.
	if ( isset( $_POST['es_case_category'] ) ) {
		$es_category_choice = sanitize_key( wp_unslash( $_POST['es_case_category'] ) );
		if ( ! array_key_exists( $es_category_choice, es_case_category_choices() ) ) {
			$es_category_choice = '';
		}
		update_post_meta( $post_id, '_es_case_category', $es_category_choice );
	}

	update_post_meta( $post_id, '_es_case_show_on_home', isset( $_POST['es_case_show_on_home'] ) ? '1' : '0' );
	update_post_meta( $post_id, '_es_case_featured', isset( $_POST['es_case_featured'] ) ? '1' : '0' );
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
				'relation' => 'AND',
				array(
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
				// Auditoría "PASADA GENERAL DE CIERRE" (ítem 9): un caso
				// marcado "Feature this case on Home" ya se muestra en la
				// sección Featured (arriba) — sin este OR, si ese mismo caso
				// también tiene "Show on Home" tildado (o simplemente no
				// desmarcado, que es el default), aparecía DOS veces en
				// Home: una vez como Featured y otra vez en la grilla de
				// Selected Work. Se excluye acá, no cambia el editorial
				// (los campos "Feature" y "Show on Home" siguen siendo
				// independientes en el admin) — sólo evita el duplicado.
				array(
					'relation' => 'OR',
					array(
						'key'     => '_es_case_featured',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'     => '_es_case_featured',
						'value'   => '1',
						'compare' => '!=',
					),
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

/**
 * El Case Study publicado marcado "Feature this case on Home", en el shape
 * que espera featured-case.php en el tema (kicker, title, body, source,
 * status, url, image, placeholder_label). Si varios están marcados, gana
 * el de menor "Order" (mismo campo nativo que usa Selected Work).
 *
 * @return array Vacío si no hay ninguno marcado.
 */
function es_portfolio_get_featured_case_for_home() {
	$query = new WP_Query(
		array(
			'post_type'      => ES_CASE_STUDY_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
			'meta_key'       => '_es_case_featured',
			'meta_value'     => '1',
		)
	);

	if ( empty( $query->posts ) ) {
		return array();
	}

	$es_post = $query->posts[0];
	$es_url  = get_post_meta( $es_post->ID, '_es_case_url', true );

	return array(
		'kicker'            => get_post_meta( $es_post->ID, '_es_case_kicker', true ),
		'title'             => get_the_title( $es_post ),
		'body'              => get_the_excerpt( $es_post ),
		'source'            => get_post_meta( $es_post->ID, '_es_case_source', true ),
		'status'            => get_post_meta( $es_post->ID, '_es_case_label', true ),
		'url'               => $es_url ? $es_url : get_permalink( $es_post ),
		'image'             => get_the_post_thumbnail_url( $es_post, 'large' ),
		'placeholder_label' => get_post_meta( $es_post->ID, '_es_case_placeholder_label', true ),
	);
}

/**
 * Callback del filtro 'es_portfolio_featured_case_for_home' que expone el
 * tema: si hay un Case Study marcado como featured lo devuelve, si no deja
 * pasar el default recibido (el fallback del tema) sin tocarlo.
 *
 * @param array $default_case Lo que el tema pasó como default (su fallback).
 * @return array
 */
function es_portfolio_filter_featured_case_for_home( $default_case ) {
	$real_case = es_portfolio_get_featured_case_for_home();
	return $real_case ? $real_case : $default_case;
}

/**
 * Todos los Case Studies publicados, separados en TRES grupos para la
 * página Work (ticket "Refine Work archive hierarchy"):
 *
 *  - 'featured': el caso marcado "Feature this case on Home" — MISMO flag
 *    que usa Home, ningún sistema paralelo. A diferencia de Home (donde
 *    Featured Case y Selected Work son independientes y un caso puede
 *    aparecer en los dos a propósito), acá el featured se EXCLUYE de
 *    selected/archive: en Work aparece una sola vez. Si por error hay más
 *    de un caso marcado featured, gana el primero por 'Order' — MISMO
 *    criterio de desempate que es_portfolio_get_featured_case_for_home(),
 *    así que Home y Work nunca muestran un featured distinto — y el resto
 *    de los casos marcados featured por error no se pierden: caen al split
 *    normal selected/archive de abajo, ni duplicados ni descartados.
 *  - 'selected': el resto, marcados "Show this case in Home → Selected
 *    Work" (o el flag no existe todavía — mismo default que Home).
 *  - 'archive': el resto, marcados explícitamente "no mostrar en Home".
 *
 * Una sola query: no hace falta una segunda pasada para encontrar el
 * featured porque el orderby ya es el mismo que usa la query dedicada de
 * Home, así que "el primero que cumple la condición, recorriendo en este
 * orden" da exactamente el mismo resultado sin duplicar el WP_Query.
 *
 * @return array{featured:array,selected:array<int,array>,archive:array<int,array>}
 */
function es_portfolio_get_case_studies_for_work_page() {
	$query = new WP_Query(
		array(
			'post_type'      => ES_CASE_STUDY_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		)
	);

	if ( empty( $query->posts ) ) {
		return array(
			'featured' => array(),
			'selected' => array(),
			'archive'  => array(),
		);
	}

	$featured = array();
	$selected = array();
	$archive  = array();

	foreach ( $query->posts as $es_post ) {
		$es_url          = get_post_meta( $es_post->ID, '_es_case_url', true );
		$es_show_on_home = get_post_meta( $es_post->ID, '_es_case_show_on_home', true );
		$es_is_featured  = '1' === get_post_meta( $es_post->ID, '_es_case_featured', true );
		$es_category_key = get_post_meta( $es_post->ID, '_es_case_category', true );
		$es_choices      = es_case_category_choices();
		$es_case_data    = array(
			'label'             => get_post_meta( $es_post->ID, '_es_case_label', true ),
			'kicker'            => get_post_meta( $es_post->ID, '_es_case_kicker', true ),
			'title'             => get_the_title( $es_post ),
			'excerpt'           => get_the_excerpt( $es_post ),
			'tags'              => wp_list_pluck( wp_get_post_terms( $es_post->ID, ES_CASE_TAG_TAX ), 'name' ),
			'category'          => isset( $es_choices[ $es_category_key ] ) ? $es_choices[ $es_category_key ] : '',
			'url'               => $es_url ? $es_url : get_permalink( $es_post ),
			'image'             => get_the_post_thumbnail_url( $es_post, 'large' ),
			'placeholder_label' => get_post_meta( $es_post->ID, '_es_case_placeholder_label', true ),
		);

		if ( $es_is_featured && empty( $featured ) ) {
			$featured = $es_case_data;
			continue;
		}

		// '0' explícito = "no mostrar en Home" = trabajo de archivo en Work.
		// Vacío/'1' (o el flag no existe todavía) = selected, igual que Home.
		if ( '0' === $es_show_on_home ) {
			$archive[] = $es_case_data;
		} else {
			$selected[] = $es_case_data;
		}
	}

	return array(
		'featured' => $featured,
		'selected' => $selected,
		'archive'  => $archive,
	);
}

/**
 * Callback del filtro 'es_portfolio_case_studies_for_work' que expone el
 * tema: si hay algún Case Study publicado en cualquiera de los tres grupos
 * (featured/selected/archive) devuelve los datos reales, si no deja pasar
 * el default recibido (el fallback del tema) sin tocarlo.
 *
 * @param array $default_data Lo que el tema pasó como default (su fallback).
 * @return array
 */
function es_portfolio_filter_case_studies_for_work( $default_data ) {
	$real_data = es_portfolio_get_case_studies_for_work_page();
	if ( empty( $real_data['featured'] ) && empty( $real_data['selected'] ) && empty( $real_data['archive'] ) ) {
		return $default_data;
	}
	return $real_data;
}
add_filter( 'es_portfolio_case_studies_for_work', 'es_portfolio_filter_case_studies_for_work' );
add_filter( 'es_portfolio_featured_case_for_home', 'es_portfolio_filter_featured_case_for_home' );
