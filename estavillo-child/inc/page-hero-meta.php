<?php
/**
 * Cabecera editable por página (eyebrow / título / subtítulo / breadcrumb).
 *
 * Problema que resuelve: hasta acá, el encabezado de las páginas fijas
 * (Cómo trabajo, Sobre mí, Contacto, Work) salía de constantes del theme
 * vía es__() — cadenas globales, una sola por concepto, traducidas desde
 * Polylang → Strings translations. Eso tenía dos consecuencias molestas:
 * cambiar una frase obligaba a tocar PHP (o a buscar la cadena en una
 * pantalla aparte), y si la traducción ES no estaba cargada, la página en
 * español mostraba el texto en inglés (es exactamente lo que pasaba con el
 * subtítulo de Cómo trabajo).
 *
 * Con meta por página el problema desaparece por construcción: cada
 * traducción es su PROPIO post, así que su cabecera es su propio dato. No
 * hay nada que sincronizar entre idiomas y no hay una cadena global que
 * pueda quedar a medio traducir.
 *
 * Arquitectura: exactamente el mismo patrón que ya usa el CPT Case Study
 * (estavillo-portfolio-core/includes/case-study-cpt.php) — add_meta_box
 * clásico + nonce + save_post + post meta con prefijo. Sin ACF, sin
 * dependencia nueva, sin REST/Gutenberg sidebar: el editor ya conoce esta
 * caja de los casos.
 *
 * Fallbacks (ninguno inventa contenido):
 *   - título     → post_title (siempre hay uno);
 *   - eyebrow    → vacío, no se renderiza;
 *   - subtítulo  → vacío, no se renderiza;
 *   - breadcrumb → post_title.
 *
 * NO escribe nada en la base de datos por su cuenta: los valores sugeridos
 * para las páginas existentes están documentados en
 * docs/handoff/PAGE-HERO-FIELDS.md para cargarlos a mano una vez. Sembrar
 * meta automáticamente sobre páginas que el editor ya tocó sería pisarle
 * contenido sin pedirle permiso.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Definición de los campos: clave de meta => label del formulario.
 * Fuente única — la usan el render del meta box y el save.
 *
 * @return array<string,array{label:string,type:string,help:string}>
 */
function es_page_hero_fields() {
	return array(
		'_es_page_eyebrow'          => array(
			'label' => __( 'Eyebrow', 'estavillo-child' ),
			'type'  => 'text',
			'help'  => __( 'Small mono line above the title (e.g. "PROCESS · 6 STEPS"). Leave empty to hide it.', 'estavillo-child' ),
		),
		'_es_page_hero_title'       => array(
			'label' => __( 'Hero title', 'estavillo-child' ),
			'type'  => 'text',
			'help'  => __( 'Falls back to the page title. Accepts <em> for the accent word.', 'estavillo-child' ),
		),
		'_es_page_hero_subtitle'    => array(
			'label' => __( 'Hero subtitle', 'estavillo-child' ),
			'type'  => 'textarea',
			'help'  => __( 'One or two lines under the title. Leave empty to hide it.', 'estavillo-child' ),
		),
		'_es_page_breadcrumb_label' => array(
			'label' => __( 'Breadcrumb label', 'estavillo-child' ),
			'type'  => 'text',
			'help'  => __( 'Shorter label for the breadcrumb, if the page title is long. Falls back to the page title.', 'estavillo-child' ),
		),
	);
}

/**
 * Registra el meta box sólo en las páginas que usan un template del theme:
 * en una página común no aporta nada y sólo sería ruido en el editor.
 */
function es_page_hero_add_meta_box() {
	add_meta_box(
		'es_page_hero',
		__( 'Page header (Estavillo)', 'estavillo-child' ),
		'es_page_hero_render_meta_box',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'es_page_hero_add_meta_box' );

/**
 * Renderiza el meta box.
 *
 * @param WP_Post $post Post actual.
 */
function es_page_hero_render_meta_box( $post ) {
	wp_nonce_field( 'es_page_hero_save', 'es_page_hero_nonce' );
	?>
	<table class="form-table" role="presentation">
		<?php foreach ( es_page_hero_fields() as $es_key => $es_field ) : ?>
			<?php $es_value = get_post_meta( $post->ID, $es_key, true ); ?>
			<tr>
				<th scope="row">
					<label for="<?php echo esc_attr( $es_key ); ?>"><?php echo esc_html( $es_field['label'] ); ?></label>
				</th>
				<td>
					<?php if ( 'textarea' === $es_field['type'] ) : ?>
						<textarea id="<?php echo esc_attr( $es_key ); ?>" name="<?php echo esc_attr( $es_key ); ?>" rows="2" class="large-text"><?php echo esc_textarea( (string) $es_value ); ?></textarea>
					<?php else : ?>
						<input type="text" id="<?php echo esc_attr( $es_key ); ?>" name="<?php echo esc_attr( $es_key ); ?>" class="large-text" value="<?php echo esc_attr( (string) $es_value ); ?>">
					<?php endif; ?>
					<p class="description"><?php echo esc_html( $es_field['help'] ); ?></p>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<p class="description">
		<?php esc_html_e( 'These fields are per page, so each language edits its own header — nothing to keep in sync between translations. Empty fields fall back to the page title (for the title and the breadcrumb) or render nothing at all (eyebrow, subtitle).', 'estavillo-child' ); ?>
	</p>
	<?php
}

/**
 * Guarda los campos. Mismos guards que el meta box del Case Study: nonce,
 * autosave y capability sobre ESTE post.
 *
 * @param int $post_id ID del post.
 */
function es_page_hero_save_meta( $post_id ) {
	if ( ! isset( $_POST['es_page_hero_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['es_page_hero_nonce'] ) ), 'es_page_hero_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( es_page_hero_fields() as $es_key => $es_field ) {
		if ( ! isset( $_POST[ $es_key ] ) ) {
			continue;
		}
		$es_raw = wp_unslash( $_POST[ $es_key ] );
		if ( 'textarea' === $es_field['type'] ) {
			$es_clean = sanitize_textarea_field( $es_raw );
		} elseif ( '_es_page_hero_title' === $es_key ) {
			// El título admite <em> para la palabra en acento, igual que el
			// resto de los titulares del sistema (ver template-parts/page-head.php,
			// que lo imprime con wp_kses). wp_kses acá también, para no guardar
			// nunca markup fuera de esa whitelist.
			$es_clean = wp_kses( $es_raw, array( 'em' => array() ) );
		} else {
			$es_clean = sanitize_text_field( $es_raw );
		}
		update_post_meta( $post_id, $es_key, $es_clean );
	}
}
add_action( 'save_post_page', 'es_page_hero_save_meta' );

/**
 * Lee la cabecera de la página actual, con los fallbacks aplicados.
 *
 * Los $defaults que pasa cada template son los textos aprobados de siempre
 * (las constantes es__() que ya existían), así que una instalación sin
 * ningún campo cargado se ve EXACTAMENTE igual que antes de este cambio.
 * El meta, cuando existe, gana.
 *
 * @param array $defaults Opcional: 'eyebrow', 'title', 'lead'.
 * @return array{eyebrow:string,title:string,lead:string}
 */
function es_page_hero_args( $defaults = array() ) {
	$es_id = get_queried_object_id();

	$es_meta = function ( $key ) use ( $es_id ) {
		return $es_id ? trim( (string) get_post_meta( $es_id, $key, true ) ) : '';
	};

	$es_eyebrow = $es_meta( '_es_page_eyebrow' );
	$es_title   = $es_meta( '_es_page_hero_title' );
	$es_lead    = $es_meta( '_es_page_hero_subtitle' );

	if ( '' === $es_title ) {
		$es_title = isset( $defaults['title'] ) ? (string) $defaults['title'] : '';
	}
	if ( '' === $es_title && $es_id ) {
		$es_title = get_the_title( $es_id );
	}
	if ( '' === $es_eyebrow && isset( $defaults['eyebrow'] ) ) {
		$es_eyebrow = (string) $defaults['eyebrow'];
	}
	if ( '' === $es_lead && isset( $defaults['lead'] ) ) {
		$es_lead = (string) $defaults['lead'];
	}

	return array(
		'eyebrow' => $es_eyebrow,
		'title'   => $es_title,
		'lead'    => $es_lead,
	);
}

/**
 * Label de la página actual para el breadcrumb.
 *
 * @param string $default Texto de respaldo si no hay meta ni título.
 * @return string
 */
function es_page_breadcrumb_label( $default = '' ) {
	$es_id = get_queried_object_id();
	if ( $es_id ) {
		$es_label = trim( (string) get_post_meta( $es_id, '_es_page_breadcrumb_label', true ) );
		if ( '' !== $es_label ) {
			return $es_label;
		}
	}
	if ( '' !== $default ) {
		return $default;
	}
	return $es_id ? (string) get_the_title( $es_id ) : '';
}
