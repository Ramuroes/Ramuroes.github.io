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
 * Sanitiza N filas de un grupo "Experience" (Selected/Previous, mismo
 * esquema para las dos — ver es_about_experience_selected_defaults() /
 * es_about_experience_previous_defaults() en el tema). 'contributions' es
 * un <textarea>, una bullet por línea — se separa por salto de línea acá,
 * líneas vacías descartadas, para guardar el mismo array<string> que
 * espera about-content.php.
 *
 * @param array  $post   $_POST completo.
 * @param string $prefix Prefijo de los campos (p.ej. 'es_exp_sel').
 * @return array<int,array{org:string,role:string,location:string,period:string,summary:string,contributions:string[],link_label:string,link_url:string}>
 */
function es_portfolio_sanitize_experience_rows( $post, $prefix ) {
	$orgs           = isset( $post[ "{$prefix}_org" ] ) ? wp_unslash( $post[ "{$prefix}_org" ] ) : array();
	$roles          = isset( $post[ "{$prefix}_role" ] ) ? wp_unslash( $post[ "{$prefix}_role" ] ) : array();
	$locations      = isset( $post[ "{$prefix}_location" ] ) ? wp_unslash( $post[ "{$prefix}_location" ] ) : array();
	$periods        = isset( $post[ "{$prefix}_period" ] ) ? wp_unslash( $post[ "{$prefix}_period" ] ) : array();
	$summaries      = isset( $post[ "{$prefix}_summary" ] ) ? wp_unslash( $post[ "{$prefix}_summary" ] ) : array();
	$contributions  = isset( $post[ "{$prefix}_contributions" ] ) ? wp_unslash( $post[ "{$prefix}_contributions" ] ) : array();
	$link_labels    = isset( $post[ "{$prefix}_link_label" ] ) ? wp_unslash( $post[ "{$prefix}_link_label" ] ) : array();
	$link_urls      = isset( $post[ "{$prefix}_link_url" ] ) ? wp_unslash( $post[ "{$prefix}_link_url" ] ) : array();
	$rows           = array();
	foreach ( $orgs as $i => $org ) {
		$es_lines = array();
		if ( isset( $contributions[ $i ] ) && '' !== trim( $contributions[ $i ] ) ) {
			foreach ( preg_split( '/\r\n|\r|\n/', $contributions[ $i ] ) as $es_line ) {
				$es_line = sanitize_text_field( $es_line );
				if ( '' !== $es_line ) {
					$es_lines[] = $es_line;
				}
			}
		}
		$rows[ $i ] = array(
			'org'           => sanitize_text_field( $org ),
			'role'          => sanitize_text_field( $roles[ $i ] ?? '' ),
			'location'      => sanitize_text_field( $locations[ $i ] ?? '' ),
			'period'        => sanitize_text_field( $periods[ $i ] ?? '' ),
			'summary'       => sanitize_textarea_field( $summaries[ $i ] ?? '' ),
			'contributions' => $es_lines,
			'link_label'    => sanitize_text_field( $link_labels[ $i ] ?? '' ),
			'link_url'      => isset( $link_urls[ $i ] ) ? esc_url_raw( $link_urls[ $i ] ) : '',
		);
	}
	return $rows;
}

/**
 * Merge genérico "fila guardada pisa el default de esa misma posición,
 * solo si su campo llave no está vacío" — mismo principio que Nav Links/
 * How I Work, generalizado para no repetirlo 4 veces (Selected/Previous
 * Experience, Other Certifications, Languages). A diferencia de Timeline/
 * Educación (reemplazo completo si hay AL MENOS una fila con título), acá
 * cada fila se evalúa independiente: una fila guardada vacía no pisa el
 * default de esa posición, y una fila default sin equivalente guardado se
 * mantiene tal cual — así el admin puede editar una sola entrada sin
 * reescribir las demás.
 *
 * @param array  $default   Filas default del tema.
 * @param array  $saved     Filas guardadas en wp-admin (puede venir vacío).
 * @param string $key_field Campo que decide si una fila guardada es real.
 * @return array
 */
function es_portfolio_merge_keyed_rows( $default, $saved, $key_field ) {
	if ( empty( $saved ) || ! is_array( $saved ) ) {
		return $default;
	}
	$merged = $default;
	foreach ( $saved as $i => $row ) {
		if ( empty( $row[ $key_field ] ) ) {
			continue;
		}
		$merged[ $i ] = $row;
	}
	return array_values( $merged );
}

/**
 * Versión del set de grupos "About" que la inicialización de abajo sabe
 * sembrar. Subir este número (y agregar la clave nueva al array de
 * es_portfolio_about_default_seeds()) es la forma de sumar un grupo nuevo
 * en el futuro sin tocar los que ya se sembraron — cada grupo se evalúa
 * de forma independiente, así que ni siquiera hace falta subir la versión
 * para que un grupo nuevo se siembre solo (ver el loop de abajo: corre
 * igual aunque el option ya esté "inicializado", el guard de versión es
 * solo para no pagar el costo de este chequeo en cada admin_init una vez
 * que TODOS los grupos conocidos ya están sembrados).
 */
define( 'ES_PORTFOLIO_ABOUT_DEFAULTS_VERSION', 1 );

/**
 * Mapa "clave del option => función de tema que devuelve su default".
 * Fuente única para es_portfolio_maybe_seed_about_defaults() — separado
 * en su propia función para poder testearlo/reusarlo sin ejecutar el
 * side-effect de update_option().
 *
 * @return array<string,string>
 */
function es_portfolio_about_default_seeds() {
	return array(
		'about_text'                 => 'es_about_intro_default',
		'about_experience_selected'  => 'es_about_experience_selected_defaults',
		'about_experience_previous'  => 'es_about_experience_previous_defaults',
		'about_education'            => 'es_about_education_defaults',
		'about_certifications_other' => 'es_about_certifications_other_defaults',
		'about_languages'            => 'es_about_languages_defaults',
		'about_hobbies_items'        => 'es_home_about_hobbies_defaults',
	);
}

/**
 * Siembra en el option real (no solo en pantalla) el default del tema
 * para cada grupo del About page que todavía nunca se guardó — root cause
 * de la ticket "Content editability": el admin de Portfolio Content leía
 * $data[clave] directo (sin fallback a los defaults del tema), así que
 * mientras el option nunca tuvo esas claves, el formulario se veía vacío
 * aunque el frontend mostrara contenido completo (ese viene de
 * apply_filters($hook, defaults_del_tema()) en about-content.php — el
 * option nunca tuvo un valor real ahí, solo el fallback PHP).
 *
 * Precedencia resultante después de esto:
 *   valor guardado en wp-admin (el editor lo tocó)
 *     → valor sembrado acá (recién ahora es un valor REAL del option,
 *       igual de editable, indistinguible para el resto del código de
 *       "el admin lo tipeó a mano")
 *     → default hardcodeado del tema (solo se usa si ni siquiera esta
 *       siembra llegó a correr — p.ej. entre instalar el plugin y la
 *       primera carga de /wp-admin/).
 *
 * Garantías de seguridad (todas ellas, no una interpretación laxa):
 *   - Por grupo, no por option completo: cada una de las 7 claves de
 *     es_portfolio_about_default_seeds() se evalúa independiente. Editar
 *     "Languages" en wp-admin nunca afecta si "Education" se siembra o no.
 *   - "Genuinely absent" = array_key_exists() en false, no empty(). Si el
 *     admin guardó el formulario alguna vez y esa guardada dejó la clave
 *     como array vacío (p.ej. borró las 3 filas de Languages a propósito),
 *     la clave YA EXISTE en el option → nunca se vuelve a tocar. Vaciar un
 *     campo a propósito se respeta para siempre, no se re-rellena.
 *   - No destructivo: la única escritura es agregar claves que no
 *     existían; ninguna clave existente se lee siquiera para comparar.
 *   - Versionado: ES_PORTFOLIO_ABOUT_DEFAULTS_VERSION + el flag
 *     '_about_defaults_seeded_version' en el propio option. Una vez que
 *     ese flag alcanza la versión actual, la función retorna en la
 *     primera línea — no vuelve a correr el loop en cada admin_init.
 *     Subir la constante en el futuro (para sumar un grupo nuevo) hace
 *     que el loop corra una vez más, pero el guard "ya existe la clave"
 *     de cada grupo sigue protegiendo a los 7 grupos ya sembrados.
 */
function es_portfolio_maybe_seed_about_defaults() {
	$data = es_portfolio_get_home_content();

	$seeded_version = isset( $data['_about_defaults_seeded_version'] ) ? (int) $data['_about_defaults_seeded_version'] : 0;
	if ( $seeded_version >= ES_PORTFOLIO_ABOUT_DEFAULTS_VERSION ) {
		return;
	}

	foreach ( es_portfolio_about_default_seeds() as $es_key => $es_default_fn ) {
		if ( array_key_exists( $es_key, $data ) ) {
			continue; // Ya existe (guardado real o siembra previa) — nunca se pisa.
		}
		if ( ! function_exists( $es_default_fn ) ) {
			continue; // Tema no cargado todavía / función no existe — no rompe nada, se reintenta el próximo admin_init.
		}
		$data[ $es_key ] = call_user_func( $es_default_fn );
	}

	$data['_about_defaults_seeded_version'] = ES_PORTFOLIO_ABOUT_DEFAULTS_VERSION;
	update_option( 'es_portfolio_home_content', $data );
}
add_action( 'admin_init', 'es_portfolio_maybe_seed_about_defaults', 5 );

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

	// Selected Experience / Previous Experience: reemplaza el "Career
	// timeline" plano de una ticket anterior — ver
	// es_portfolio_sanitize_experience_rows() arriba para el esquema
	// compartido (org/role/location/period/summary/contributions/link).
	if ( isset( $_POST['es_exp_sel_org'] ) && is_array( $_POST['es_exp_sel_org'] ) ) {
		$data['about_experience_selected'] = es_portfolio_sanitize_experience_rows( $_POST, 'es_exp_sel' );
	}
	if ( isset( $_POST['es_exp_prev_org'] ) && is_array( $_POST['es_exp_prev_org'] ) ) {
		$data['about_experience_previous'] = es_portfolio_sanitize_experience_rows( $_POST, 'es_exp_prev' );
	}

	// Education & certificates: esquema ampliado (meta/description/
	// final_project/link) respecto de la versión anterior (title/org/year).
	if ( isset( $_POST['es_about_edu_title'] ) && is_array( $_POST['es_about_edu_title'] ) ) {
		$es_edu_titles = wp_unslash( $_POST['es_about_edu_title'] );
		$es_edu_orgs   = isset( $_POST['es_about_edu_org'] ) && is_array( $_POST['es_about_edu_org'] ) ? wp_unslash( $_POST['es_about_edu_org'] ) : array();
		$es_edu_metas  = isset( $_POST['es_about_edu_meta'] ) && is_array( $_POST['es_about_edu_meta'] ) ? wp_unslash( $_POST['es_about_edu_meta'] ) : array();
		$es_edu_years  = isset( $_POST['es_about_edu_year'] ) && is_array( $_POST['es_about_edu_year'] ) ? wp_unslash( $_POST['es_about_edu_year'] ) : array();
		$es_edu_descs  = isset( $_POST['es_about_edu_description'] ) && is_array( $_POST['es_about_edu_description'] ) ? wp_unslash( $_POST['es_about_edu_description'] ) : array();
		$es_edu_finals = isset( $_POST['es_about_edu_final_project'] ) && is_array( $_POST['es_about_edu_final_project'] ) ? wp_unslash( $_POST['es_about_edu_final_project'] ) : array();
		$es_edu_links  = isset( $_POST['es_about_edu_link'] ) && is_array( $_POST['es_about_edu_link'] ) ? wp_unslash( $_POST['es_about_edu_link'] ) : array();
		$es_education  = array();
		foreach ( $es_edu_titles as $i => $es_edu_title ) {
			$es_education[ $i ] = array(
				'title'         => sanitize_text_field( $es_edu_title ),
				'org'           => sanitize_text_field( $es_edu_orgs[ $i ] ?? '' ),
				'meta'          => sanitize_text_field( $es_edu_metas[ $i ] ?? '' ),
				'year'          => sanitize_text_field( $es_edu_years[ $i ] ?? '' ),
				'description'   => sanitize_textarea_field( $es_edu_descs[ $i ] ?? '' ),
				'final_project' => sanitize_textarea_field( $es_edu_finals[ $i ] ?? '' ),
				'link'          => isset( $es_edu_links[ $i ] ) ? esc_url_raw( $es_edu_links[ $i ] ) : '',
			);
		}
		$data['about_education'] = $es_education;
	}

	// Other Certifications (secondary, collapsed group on the About page).
	if ( isset( $_POST['es_cert_name'] ) && is_array( $_POST['es_cert_name'] ) ) {
		$es_cert_names   = wp_unslash( $_POST['es_cert_name'] );
		$es_cert_issuers = isset( $_POST['es_cert_issuer'] ) && is_array( $_POST['es_cert_issuer'] ) ? wp_unslash( $_POST['es_cert_issuer'] ) : array();
		$es_cert_dates   = isset( $_POST['es_cert_date'] ) && is_array( $_POST['es_cert_date'] ) ? wp_unslash( $_POST['es_cert_date'] ) : array();
		$es_cert_ids     = isset( $_POST['es_cert_credential_id'] ) && is_array( $_POST['es_cert_credential_id'] ) ? wp_unslash( $_POST['es_cert_credential_id'] ) : array();
		$es_cert_links   = isset( $_POST['es_cert_link'] ) && is_array( $_POST['es_cert_link'] ) ? wp_unslash( $_POST['es_cert_link'] ) : array();
		$es_certs        = array();
		foreach ( $es_cert_names as $i => $es_cert_name ) {
			$es_certs[ $i ] = array(
				'name'          => sanitize_text_field( $es_cert_name ),
				'issuer'        => sanitize_text_field( $es_cert_issuers[ $i ] ?? '' ),
				'date'          => sanitize_text_field( $es_cert_dates[ $i ] ?? '' ),
				'credential_id' => sanitize_text_field( $es_cert_ids[ $i ] ?? '' ),
				'link'          => isset( $es_cert_links[ $i ] ) ? esc_url_raw( $es_cert_links[ $i ] ) : '',
			);
		}
		$data['about_certifications_other'] = $es_certs;
	}

	// Languages.
	if ( isset( $_POST['es_lang_name'] ) && is_array( $_POST['es_lang_name'] ) ) {
		$es_lang_names  = wp_unslash( $_POST['es_lang_name'] );
		$es_lang_levels = isset( $_POST['es_lang_level'] ) && is_array( $_POST['es_lang_level'] ) ? wp_unslash( $_POST['es_lang_level'] ) : array();
		$es_languages   = array();
		foreach ( $es_lang_names as $i => $es_lang_name ) {
			$es_languages[ $i ] = array(
				'name'  => sanitize_text_field( $es_lang_name ),
				'level' => sanitize_text_field( $es_lang_levels[ $i ] ?? '' ),
			);
		}
		$data['about_languages'] = $es_languages;
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
					<td>
						<textarea id="es_about_text" name="es_about_text" rows="10" class="large-text"><?php echo esc_textarea( $data['about_text'] ?? '' ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Separate paragraphs with a blank line — each one renders as its own paragraph on the About page.', 'estavillo-portfolio-core' ); ?></p>
					</td>
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
			<p class="description"><?php esc_html_e( 'Ships with 9 suggested interests already filled in — edit, reorder (by moving which row a label fills), hide with "Show", or replace with your own, up to 9 rows. Leave a row\'s label blank to remove it entirely. Short text is optional and only shown if filled in. Two interests (Gaming, Photography) ship without an icon — the curated icon library doesn\'t have artwork for them yet, so they render label-only until real SVGs are added.', 'estavillo-portfolio-core' ); ?></p>
			<table class="form-table" role="presentation">
				<?php
				$es_hobbies_data  = $data['about_hobbies_items'] ?? array();
				$es_hobby_choices = function_exists( 'es_hobby_icon_choices' ) ? es_hobby_icon_choices() : array();
				$es_hobby_defaults = function_exists( 'es_home_about_hobbies_defaults' ) ? es_home_about_hobbies_defaults() : array();
				for ( $i = 0; $i < 9; $i++ ) :
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

			<h3><?php esc_html_e( 'Experience (About page)', 'estavillo-portfolio-core' ); ?></h3>
			<p class="description"><?php esc_html_e( 'The roles shown prioritized and always visible on the About page. Leave a row\'s Organization blank to keep it out of the list. "Key contributions" is a bullet list — one line per bullet, shown inside a collapsed disclosure.', 'estavillo-portfolio-core' ); ?></p>
			<table class="form-table" role="presentation">
				<?php
				$es_exp_sel = $data['about_experience_selected'] ?? array();
				for ( $i = 0; $i < 4; $i++ ) :
					$es_row = $es_exp_sel[ $i ] ?? array();
					?>
					<tr>
						<th scope="row"><?php echo esc_html( sprintf( __( 'Entry %d', 'estavillo-portfolio-core' ), $i + 1 ) ); ?></th>
						<td>
							<input type="text" name="es_exp_sel_org[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['org'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Organization', 'estavillo-portfolio-core' ); ?>">
							<input type="text" name="es_exp_sel_role[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['role'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Role', 'estavillo-portfolio-core' ); ?>"><br>
							<input type="text" name="es_exp_sel_location[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['location'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Location (optional)', 'estavillo-portfolio-core' ); ?>">
							<input type="text" name="es_exp_sel_period[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['period'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Period, e.g. 2025–Present', 'estavillo-portfolio-core' ); ?>"><br>
							<textarea name="es_exp_sel_summary[<?php echo esc_attr( $i ); ?>]" rows="2" class="large-text" placeholder="<?php esc_attr_e( 'Visible summary', 'estavillo-portfolio-core' ); ?>"><?php echo esc_textarea( $es_row['summary'] ?? '' ); ?></textarea>
							<textarea name="es_exp_sel_contributions[<?php echo esc_attr( $i ); ?>]" rows="4" class="large-text" placeholder="<?php esc_attr_e( 'Key contributions — one bullet per line', 'estavillo-portfolio-core' ); ?>"><?php echo esc_textarea( isset( $es_row['contributions'] ) && is_array( $es_row['contributions'] ) ? implode( "\n", $es_row['contributions'] ) : '' ); ?></textarea><br>
							<input type="text" name="es_exp_sel_link_label[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['link_label'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Link label, e.g. "View case study" (optional)', 'estavillo-portfolio-core' ); ?>">
							<input type="url" name="es_exp_sel_link_url[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['link_url'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Link URL (optional)', 'estavillo-portfolio-core' ); ?>">
						</td>
					</tr>
				<?php endfor; ?>
			</table>

			<h3><?php esc_html_e( 'Earlier Experience (About page)', 'estavillo-portfolio-core' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Shown as a secondary, collapsed group below Experience. Same fields as above. Leave a row\'s Organization blank to keep it out of the list.', 'estavillo-portfolio-core' ); ?></p>
			<table class="form-table" role="presentation">
				<?php
				$es_exp_prev = $data['about_experience_previous'] ?? array();
				for ( $i = 0; $i < 5; $i++ ) :
					$es_row = $es_exp_prev[ $i ] ?? array();
					?>
					<tr>
						<th scope="row"><?php echo esc_html( sprintf( __( 'Entry %d', 'estavillo-portfolio-core' ), $i + 1 ) ); ?></th>
						<td>
							<input type="text" name="es_exp_prev_org[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['org'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Organization', 'estavillo-portfolio-core' ); ?>">
							<input type="text" name="es_exp_prev_role[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['role'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Role', 'estavillo-portfolio-core' ); ?>"><br>
							<input type="text" name="es_exp_prev_location[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['location'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Location (optional)', 'estavillo-portfolio-core' ); ?>">
							<input type="text" name="es_exp_prev_period[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['period'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Period (optional)', 'estavillo-portfolio-core' ); ?>"><br>
							<textarea name="es_exp_prev_summary[<?php echo esc_attr( $i ); ?>]" rows="2" class="large-text" placeholder="<?php esc_attr_e( 'Visible summary', 'estavillo-portfolio-core' ); ?>"><?php echo esc_textarea( $es_row['summary'] ?? '' ); ?></textarea>
							<textarea name="es_exp_prev_contributions[<?php echo esc_attr( $i ); ?>]" rows="4" class="large-text" placeholder="<?php esc_attr_e( 'Key contributions — one bullet per line', 'estavillo-portfolio-core' ); ?>"><?php echo esc_textarea( isset( $es_row['contributions'] ) && is_array( $es_row['contributions'] ) ? implode( "\n", $es_row['contributions'] ) : '' ); ?></textarea><br>
							<input type="text" name="es_exp_prev_link_label[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['link_label'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Link label (optional)', 'estavillo-portfolio-core' ); ?>">
							<input type="url" name="es_exp_prev_link_url[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['link_url'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Link URL (optional)', 'estavillo-portfolio-core' ); ?>">
						</td>
					</tr>
				<?php endfor; ?>
			</table>

			<h3><?php esc_html_e( 'Education & certificates (About page)', 'estavillo-portfolio-core' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Leave a row\'s title blank to keep it out of the list. "Meta" is a secondary line (faculty/school/location). "Year" is the official completion/issue year.', 'estavillo-portfolio-core' ); ?></p>
			<table class="form-table" role="presentation">
				<?php
				$es_education = $data['about_education'] ?? array();
				for ( $i = 0; $i < 4; $i++ ) :
					$es_row = $es_education[ $i ] ?? array();
					?>
					<tr>
						<th scope="row"><?php echo esc_html( sprintf( __( 'Entry %d', 'estavillo-portfolio-core' ), $i + 1 ) ); ?></th>
						<td>
							<input type="text" name="es_about_edu_title[<?php echo esc_attr( $i ); ?>]" class="large-text" value="<?php echo esc_attr( $es_row['title'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Degree / certificate', 'estavillo-portfolio-core' ); ?>"><br>
							<input type="text" name="es_about_edu_org[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['org'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Institution / issuer', 'estavillo-portfolio-core' ); ?>">
							<input type="text" name="es_about_edu_year[<?php echo esc_attr( $i ); ?>]" class="small-text" value="<?php echo esc_attr( $es_row['year'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Year', 'estavillo-portfolio-core' ); ?>"><br>
							<input type="text" name="es_about_edu_meta[<?php echo esc_attr( $i ); ?>]" class="large-text" value="<?php echo esc_attr( $es_row['meta'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Meta line — faculty/school/location (optional)', 'estavillo-portfolio-core' ); ?>">
							<textarea name="es_about_edu_description[<?php echo esc_attr( $i ); ?>]" rows="2" class="large-text" placeholder="<?php esc_attr_e( 'Description (optional)', 'estavillo-portfolio-core' ); ?>"><?php echo esc_textarea( $es_row['description'] ?? '' ); ?></textarea>
							<textarea name="es_about_edu_final_project[<?php echo esc_attr( $i ); ?>]" rows="2" class="large-text" placeholder="<?php esc_attr_e( 'Final project (optional)', 'estavillo-portfolio-core' ); ?>"><?php echo esc_textarea( $es_row['final_project'] ?? '' ); ?></textarea>
							<input type="url" name="es_about_edu_link[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['link'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Credential link (optional)', 'estavillo-portfolio-core' ); ?>">
						</td>
					</tr>
				<?php endfor; ?>
			</table>

			<h3><?php esc_html_e( 'Other Certifications (About page)', 'estavillo-portfolio-core' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Shown as a secondary, collapsed group. Leave a row\'s Name blank to keep it out of the list.', 'estavillo-portfolio-core' ); ?></p>
			<table class="form-table" role="presentation">
				<?php
				$es_certs = $data['about_certifications_other'] ?? array();
				for ( $i = 0; $i < 10; $i++ ) :
					$es_row = $es_certs[ $i ] ?? array();
					?>
					<tr>
						<th scope="row"><?php echo esc_html( sprintf( __( 'Entry %d', 'estavillo-portfolio-core' ), $i + 1 ) ); ?></th>
						<td>
							<input type="text" name="es_cert_name[<?php echo esc_attr( $i ); ?>]" class="large-text" value="<?php echo esc_attr( $es_row['name'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Certificate name', 'estavillo-portfolio-core' ); ?>"><br>
							<input type="text" name="es_cert_issuer[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['issuer'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Issuer', 'estavillo-portfolio-core' ); ?>">
							<input type="text" name="es_cert_date[<?php echo esc_attr( $i ); ?>]" class="small-text" value="<?php echo esc_attr( $es_row['date'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Date', 'estavillo-portfolio-core' ); ?>">
							<input type="text" name="es_cert_credential_id[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['credential_id'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Credential ID (optional)', 'estavillo-portfolio-core' ); ?>">
							<input type="url" name="es_cert_link[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['link'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Credential link (optional)', 'estavillo-portfolio-core' ); ?>">
						</td>
					</tr>
				<?php endfor; ?>
			</table>

			<h3><?php esc_html_e( 'Languages (About page)', 'estavillo-portfolio-core' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Leave a row\'s Language blank to keep it out of the list.', 'estavillo-portfolio-core' ); ?></p>
			<table class="form-table" role="presentation">
				<?php
				$es_languages = $data['about_languages'] ?? array();
				for ( $i = 0; $i < 5; $i++ ) :
					$es_row = $es_languages[ $i ] ?? array();
					?>
					<tr>
						<th scope="row"><?php echo esc_html( sprintf( __( 'Entry %d', 'estavillo-portfolio-core' ), $i + 1 ) ); ?></th>
						<td>
							<input type="text" name="es_lang_name[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['name'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Language', 'estavillo-portfolio-core' ); ?>">
							<input type="text" name="es_lang_level[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['level'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Level, e.g. Native / Advanced (C1) / Basic', 'estavillo-portfolio-core' ); ?>">
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

/**
 * Puentes para Selected Experience / Previous Experience: merge por
 * posición (ver es_portfolio_merge_keyed_rows() arriba) en vez del
 * reemplazo completo que usaba el "Career timeline" anterior — acá cada
 * entrada es una organización específica y fija (Guzmán Villalba/Trazur/
 * Ceibal; Verona/Samic/Fupsi), así que editar una sola fila en wp-admin
 * NUNCA debe borrar las otras dos que el admin no tocó.
 */
function es_portfolio_filter_about_experience_selected( $default ) {
	$data = es_portfolio_get_home_content();
	return es_portfolio_merge_keyed_rows( $default, $data['about_experience_selected'] ?? array(), 'org' );
}
add_filter( 'es_about_experience_selected', 'es_portfolio_filter_about_experience_selected' );

function es_portfolio_filter_about_experience_previous( $default ) {
	$data = es_portfolio_get_home_content();
	return es_portfolio_merge_keyed_rows( $default, $data['about_experience_previous'] ?? array(), 'org' );
}
add_filter( 'es_about_experience_previous', 'es_portfolio_filter_about_experience_previous' );

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
 * Puentes para Other Certifications / Languages — mismo merge por
 * posición que Selected/Previous Experience arriba.
 */
function es_portfolio_filter_about_certifications_other( $default ) {
	$data = es_portfolio_get_home_content();
	return es_portfolio_merge_keyed_rows( $default, $data['about_certifications_other'] ?? array(), 'name' );
}
add_filter( 'es_about_certifications_other', 'es_portfolio_filter_about_certifications_other' );

function es_portfolio_filter_about_languages( $default ) {
	$data = es_portfolio_get_home_content();
	return es_portfolio_merge_keyed_rows( $default, $data['about_languages'] ?? array(), 'name' );
}
add_filter( 'es_about_languages', 'es_portfolio_filter_about_languages' );

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
