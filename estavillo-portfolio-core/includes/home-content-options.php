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
 * espera about-content.php. 'tools' (final-polish ticket §2) es un
 * <input> de una línea, separado por comas — mismo criterio de "una
 * herramienta por vez, nunca inventar" que el resto del About: una fila
 * sin ninguna coma sigue guardando un array de un solo elemento.
 *
 * @param array  $post   $_POST completo.
 * @param string $prefix Prefijo de los campos (p.ej. 'es_exp_sel').
 * @return array<int,array{org:string,role:string,location:string,period:string,summary:string,contributions:string[],tools:string[],link_label:string,link_url:string}>
 */
function es_portfolio_sanitize_experience_rows( $post, $prefix ) {
	$orgs           = isset( $post[ "{$prefix}_org" ] ) ? wp_unslash( $post[ "{$prefix}_org" ] ) : array();
	$roles          = isset( $post[ "{$prefix}_role" ] ) ? wp_unslash( $post[ "{$prefix}_role" ] ) : array();
	$locations      = isset( $post[ "{$prefix}_location" ] ) ? wp_unslash( $post[ "{$prefix}_location" ] ) : array();
	$periods        = isset( $post[ "{$prefix}_period" ] ) ? wp_unslash( $post[ "{$prefix}_period" ] ) : array();
	$summaries      = isset( $post[ "{$prefix}_summary" ] ) ? wp_unslash( $post[ "{$prefix}_summary" ] ) : array();
	$contributions  = isset( $post[ "{$prefix}_contributions" ] ) ? wp_unslash( $post[ "{$prefix}_contributions" ] ) : array();
	$tools_raw      = isset( $post[ "{$prefix}_tools" ] ) ? wp_unslash( $post[ "{$prefix}_tools" ] ) : array();
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
		$es_tools = array();
		if ( isset( $tools_raw[ $i ] ) && '' !== trim( $tools_raw[ $i ] ) ) {
			foreach ( explode( ',', $tools_raw[ $i ] ) as $es_tool ) {
				$es_tool = sanitize_text_field( trim( $es_tool ) );
				if ( '' !== $es_tool ) {
					$es_tools[] = $es_tool;
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
			'tools'         => $es_tools,
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
define( 'ES_PORTFOLIO_ABOUT_DEFAULTS_VERSION', 2 );

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
		'about_tools'                => 'es_about_tools_defaults',
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

	// Tools (About page, ahora renderizado por el bloque estavillo/tools —
	// Design System ticket) — N grupos, cada uno título + ícono (misma
	// librería que Hobbies/How I Work) + <textarea> de items, uno por
	// línea. Mismo parseo uno-por-línea que 'contributions' arriba.
	// 'categoryDescription' no tiene campo propio todavía (atributo
	// preparado para el bloque, sin UI — ver block.json de estavillo/tools):
	// se guarda vacío, nunca se pisa un valor que no vino de este form.
	if ( isset( $_POST['es_about_tools_title'] ) && is_array( $_POST['es_about_tools_title'] ) ) {
		$es_tools_titles = wp_unslash( $_POST['es_about_tools_title'] );
		$es_tools_icons  = isset( $_POST['es_about_tools_icon'] ) && is_array( $_POST['es_about_tools_icon'] ) ? wp_unslash( $_POST['es_about_tools_icon'] ) : array();
		$es_tools_items  = isset( $_POST['es_about_tools_items'] ) && is_array( $_POST['es_about_tools_items'] ) ? wp_unslash( $_POST['es_about_tools_items'] ) : array();
		$es_valid_icons  = function_exists( 'es_process_icon_choices' ) ? array_keys( es_process_icon_choices() ) : array();
		$es_tools_groups = array();
		foreach ( $es_tools_titles as $i => $es_tools_title ) {
			$es_items = array();
			if ( isset( $es_tools_items[ $i ] ) && '' !== trim( $es_tools_items[ $i ] ) ) {
				foreach ( preg_split( '/\r\n|\r|\n/', $es_tools_items[ $i ] ) as $es_item ) {
					$es_item = sanitize_text_field( $es_item );
					if ( '' !== $es_item ) {
						$es_items[] = $es_item;
					}
				}
			}
			$es_icon_choice   = isset( $es_tools_icons[ $i ] ) ? sanitize_key( $es_tools_icons[ $i ] ) : '';
			$es_tools_groups[ $i ] = array(
				'title'               => sanitize_text_field( $es_tools_title ),
				'icon'                => in_array( $es_icon_choice, $es_valid_icons, true ) ? $es_icon_choice : '',
				'items'               => $es_items,
				'categoryDescription' => '',
			);
		}
		$data['about_tools'] = $es_tools_groups;
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

	// ---- Connect page (dedicated) — ticket Connect ----
	// Eyebrow/title/intro son EXCLUSIVOS de la página dedicada — nunca los
	// usa Home (que sigue con cta_title/cta_lead arriba, sin tocar).
	if ( isset( $_POST['es_connect_eyebrow'] ) ) {
		$data['connect_eyebrow'] = sanitize_text_field( wp_unslash( $_POST['es_connect_eyebrow'] ) );
	}
	if ( isset( $_POST['es_connect_title'] ) ) {
		$data['connect_title'] = wp_kses( wp_unslash( $_POST['es_connect_title'] ), array( 'em' => array() ) );
	}
	if ( isset( $_POST['es_connect_intro'] ) ) {
		$data['connect_intro'] = sanitize_textarea_field( wp_unslash( $_POST['es_connect_intro'] ) );
	}
	if ( isset( $_POST['es_contact_phone'] ) ) {
		$data['contact_phone'] = sanitize_text_field( wp_unslash( $_POST['es_contact_phone'] ) );
	}
	if ( isset( $_POST['es_contact_whatsapp'] ) ) {
		$data['contact_whatsapp'] = sanitize_text_field( wp_unslash( $_POST['es_contact_whatsapp'] ) );
	}
	if ( isset( $_POST['es_connect_country'] ) ) {
		$data['connect_country'] = sanitize_text_field( wp_unslash( $_POST['es_connect_country'] ) );
	}

	// ---- Header — site identity + behaviour ----
	if ( isset( $_POST['es_wordmark_text'] ) ) {
		$data['wordmark_text'] = sanitize_text_field( wp_unslash( $_POST['es_wordmark_text'] ) );
	}
	if ( isset( $_POST['es_wordmark_url'] ) ) {
		$data['wordmark_url'] = esc_url_raw( wp_unslash( $_POST['es_wordmark_url'] ) );
	}
	if ( isset( $_POST['es_wordmark_image'] ) ) {
		$data['wordmark_image'] = esc_url_raw( wp_unslash( $_POST['es_wordmark_image'] ) );
	}
	// Checkbox: only trust its presence when the Header section was on the
	// submitted form (a hidden marker) — an unchecked box posts nothing.
	if ( isset( $_POST['es_hf_header'] ) ) {
		$data['sticky_header'] = isset( $_POST['es_sticky_header'] ) ? '1' : '0';
	}

	// ---- Header (nav links) ----
	if ( isset( $_POST['es_nav_link_label'] ) && is_array( $_POST['es_nav_link_label'] ) ) {
		$labels = wp_unslash( $_POST['es_nav_link_label'] );
		$urls   = isset( $_POST['es_nav_link_url'] ) && is_array( $_POST['es_nav_link_url'] ) ? wp_unslash( $_POST['es_nav_link_url'] ) : array();
		$shows  = isset( $_POST['es_nav_link_show'] ) && is_array( $_POST['es_nav_link_show'] ) ? wp_unslash( $_POST['es_nav_link_show'] ) : array();
		$links  = array();
		foreach ( $labels as $i => $label ) {
			$links[ $i ] = array(
				'label' => sanitize_text_field( $label ),
				'url'   => isset( $urls[ $i ] ) ? esc_url_raw( $urls[ $i ] ) : '',
				'show'  => isset( $shows[ $i ] ),
			);
		}
		$data['nav_links'] = $links;
	}

	// ---- Footer — content, contact name, layout, visibility ----
	if ( isset( $_POST['es_footer_note'] ) ) {
		$data['footer_note'] = sanitize_text_field( wp_unslash( $_POST['es_footer_note'] ) );
	}
	if ( isset( $_POST['es_footer_copyright_name'] ) ) {
		$data['footer_copyright_name'] = sanitize_text_field( wp_unslash( $_POST['es_footer_copyright_name'] ) );
	}
	if ( isset( $_POST['es_footer_layout'] ) ) {
		$es_layout             = sanitize_key( wp_unslash( $_POST['es_footer_layout'] ) );
		$data['footer_layout'] = in_array( $es_layout, array( 'three', 'two', 'compact' ), true ) ? $es_layout : 'three';
	}
	if ( isset( $_POST['es_footer_width'] ) ) {
		$es_width             = sanitize_key( wp_unslash( $_POST['es_footer_width'] ) );
		$data['footer_width'] = in_array( $es_width, array( 'standard', 'wide' ), true ) ? $es_width : 'standard';
	}
	if ( isset( $_POST['es_hf_footer'] ) ) {
		$es_vis_keys = array( 'nav', 'email', 'phone', 'whatsapp', 'linkedin', 'instagram', 'behance', 'location', 'note' );
		$es_posted   = isset( $_POST['es_footer_show'] ) && is_array( $_POST['es_footer_show'] ) ? wp_unslash( $_POST['es_footer_show'] ) : array();
		$es_vis      = array();
		foreach ( $es_vis_keys as $es_vk ) {
			$es_vis[ $es_vk ] = isset( $es_posted[ $es_vk ] );
		}
		$data['footer_visibility'] = $es_vis;
	}

	// ---- Footer (social links + location) ----
	if ( isset( $_POST['es_social_linkedin'] ) ) {
		$data['social_linkedin'] = esc_url_raw( wp_unslash( $_POST['es_social_linkedin'] ) );
	}
	if ( isset( $_POST['es_social_behance'] ) ) {
		$data['social_behance'] = esc_url_raw( wp_unslash( $_POST['es_social_behance'] ) );
	}
	if ( isset( $_POST['es_social_instagram'] ) ) {
		$data['social_instagram'] = esc_url_raw( wp_unslash( $_POST['es_social_instagram'] ) );
	}
	if ( isset( $_POST['es_footer_location'] ) ) {
		$data['footer_location'] = sanitize_text_field( wp_unslash( $_POST['es_footer_location'] ) );
	}

	/*
	 * ---- Appearance — Portfolio dark mode ----
	 * Ya no se guarda nada acá. Dark dejó de ser una opción del sitio y pasó
	 * a ser el único sistema visual del portfolio (ver
	 * es_theme_dark_mode_enabled() en el theme), así que este bloque escribía
	 * un valor que nadie lee.
	 *
	 * La clave 'theme_dark_mode' que haya quedado guardada de antes se deja
	 * intacta a propósito: borrarla sería una migración destructiva sin
	 * ninguna ganancia. Simplemente ya no se escribe ni se lee.
	 */

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
							<input type="text" name="es_exp_sel_tools[<?php echo esc_attr( $i ); ?>]" class="large-text" value="<?php echo esc_attr( isset( $es_row['tools'] ) && is_array( $es_row['tools'] ) ? implode( ', ', $es_row['tools'] ) : '' ); ?>" placeholder="<?php esc_attr_e( 'Tools used, comma-separated, e.g. "Figma, Claude, VS Code" (optional — leave blank if unconfirmed)', 'estavillo-portfolio-core' ); ?>"><br>
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
							<input type="text" name="es_exp_prev_tools[<?php echo esc_attr( $i ); ?>]" class="large-text" value="<?php echo esc_attr( isset( $es_row['tools'] ) && is_array( $es_row['tools'] ) ? implode( ', ', $es_row['tools'] ) : '' ); ?>" placeholder="<?php esc_attr_e( 'Tools used, comma-separated (optional — leave blank if unconfirmed)', 'estavillo-portfolio-core' ); ?>"><br>
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

			<h3><?php esc_html_e( 'Tools (About page)', 'estavillo-portfolio-core' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Renders via the reusable "Tools" block (estavillo/tools) — the same component available on Home/Case Studies. Leave a row\'s title blank to keep it out of the list. Items: one tool per line.', 'estavillo-portfolio-core' ); ?></p>
			<table class="form-table" role="presentation">
				<?php
				$es_tools_groups = $data['about_tools'] ?? array();
				$es_tools_icon_choices = function_exists( 'es_process_icon_choices' ) ? es_process_icon_choices() : array();
				for ( $i = 0; $i < 6; $i++ ) :
					$es_row = $es_tools_groups[ $i ] ?? array();
					?>
					<tr>
						<th scope="row"><?php echo esc_html( sprintf( __( 'Group %d', 'estavillo-portfolio-core' ), $i + 1 ) ); ?></th>
						<td>
							<input type="text" name="es_about_tools_title[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_row['title'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Group title, e.g. "Design"', 'estavillo-portfolio-core' ); ?>">
							<?php if ( ! empty( $es_tools_icon_choices ) ) : ?>
								<select name="es_about_tools_icon[<?php echo esc_attr( $i ); ?>]">
									<option value=""><?php esc_html_e( '— No icon —', 'estavillo-portfolio-core' ); ?></option>
									<?php foreach ( $es_tools_icon_choices as $es_icon_key => $es_icon_label ) : ?>
										<option value="<?php echo esc_attr( $es_icon_key ); ?>" <?php selected( $es_row['icon'] ?? '', $es_icon_key ); ?>><?php echo esc_html( $es_icon_label ); ?></option>
									<?php endforeach; ?>
								</select>
							<?php endif; ?>
							<br>
							<textarea name="es_about_tools_items[<?php echo esc_attr( $i ); ?>]" rows="4" class="large-text" placeholder="<?php esc_attr_e( 'Tools — one per line', 'estavillo-portfolio-core' ); ?>"><?php echo esc_textarea( isset( $es_row['items'] ) && is_array( $es_row['items'] ) ? implode( "\n", $es_row['items'] ) : '' ); ?></textarea>
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
			<p class="description"><?php esc_html_e( 'These fields feed Home\'s own CTA section only. For the dedicated Connect/Contact page (eyebrow, title, intro, phone, WhatsApp, country), see "Connect page (dedicated)" below.', 'estavillo-portfolio-core' ); ?></p>
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
					<th scope="row"><label for="es_connect_status"><?php esc_html_e( 'Availability line (optional)', 'estavillo-portfolio-core' ); ?></label></th>
					<td>
						<input type="text" id="es_connect_status" name="es_connect_status" class="regular-text" value="<?php echo esc_attr( $data['connect_status'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'e.g. Based in Uruguay — open to remote and international roles', 'estavillo-portfolio-core' ); ?>">
						<p class="description"><?php esc_html_e( 'Connect page only — a restrained secondary line under "Let\'s talk.", not a status pill. Leave blank to hide it.', 'estavillo-portfolio-core' ); ?></p>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Connect page (dedicated)', 'estavillo-portfolio-core' ); ?></h2>
			<p class="description"><?php esc_html_e( 'The standalone Connect/Contact page only — Home is never affected by these. Contact email above and Availability above are shared with this page too.', 'estavillo-portfolio-core' ); ?></p>
			<p class="description"><strong><?php esc_html_e( 'Source-of-truth note (revision ticket):', 'estavillo-portfolio-core' ); ?></strong> <?php esc_html_e( 'Eyebrow/Title/Introduction below always feed the page-head (they render whether or not the WP Page has real Gutenberg content). Phone/WhatsApp/Country and Availability above only feed the legacy fallback body — once the Connect Page has real Gutenberg content (the normal state), its own static text and links are the source of truth for the two-column contact section and editing these fields will NOT change what visitors see. This is intentional, not a bug — see docs/content/connect-gutenberg-en.html.', 'estavillo-portfolio-core' ); ?></p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="es_connect_eyebrow"><?php esc_html_e( 'Eyebrow', 'estavillo-portfolio-core' ); ?></label></th>
					<td><input type="text" id="es_connect_eyebrow" name="es_connect_eyebrow" class="regular-text" value="<?php echo esc_attr( $data['connect_eyebrow'] ?? '' ); ?>" placeholder="Get in touch"></td>
				</tr>
				<tr>
					<th scope="row"><label for="es_connect_title"><?php esc_html_e( 'Title', 'estavillo-portfolio-core' ); ?></label></th>
					<td><input type="text" id="es_connect_title" name="es_connect_title" class="large-text" value="<?php echo esc_attr( $data['connect_title'] ?? '' ); ?>" placeholder="Start a conversation."></td>
				</tr>
				<tr>
					<th scope="row"><label for="es_connect_intro"><?php esc_html_e( 'Introduction', 'estavillo-portfolio-core' ); ?></label></th>
					<td><textarea id="es_connect_intro" name="es_connect_intro" rows="3" class="large-text"><?php echo esc_textarea( $data['connect_intro'] ?? '' ); ?></textarea></td>
				</tr>
				<tr>
					<th scope="row"><label for="es_contact_phone"><?php esc_html_e( 'Phone (not shown on Connect)', 'estavillo-portfolio-core' ); ?></label></th>
					<td>
						<input type="text" id="es_contact_phone" name="es_contact_phone" class="regular-text" value="<?php echo esc_attr( $data['contact_phone'] ?? '' ); ?>" placeholder="+598 99 892 722">
						<p class="description"><?php esc_html_e( 'Connect page now shows WhatsApp only (revision ticket — avoids showing the same number twice). Kept here for a future page that may need a separate phone row.', 'estavillo-portfolio-core' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="es_contact_whatsapp"><?php esc_html_e( 'WhatsApp', 'estavillo-portfolio-core' ); ?></label></th>
					<td>
						<input type="text" id="es_contact_whatsapp" name="es_contact_whatsapp" class="regular-text" value="<?php echo esc_attr( $data['contact_whatsapp'] ?? '' ); ?>" placeholder="+598 99 892 722">
						<p class="description"><?php esc_html_e( 'Any format is fine — only the digits are used to build the wa.me link.', 'estavillo-portfolio-core' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="es_connect_country"><?php esc_html_e( 'Country', 'estavillo-portfolio-core' ); ?></label></th>
					<td><input type="text" id="es_connect_country" name="es_connect_country" class="regular-text" value="<?php echo esc_attr( $data['connect_country'] ?? '' ); ?>" placeholder="Uruguay"></td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Header — site identity', 'estavillo-portfolio-core' ); ?></h2>
			<input type="hidden" name="es_hf_header" value="1">
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="es_wordmark_text"><?php esc_html_e( 'Wordmark text', 'estavillo-portfolio-core' ); ?></label></th>
					<td><input type="text" id="es_wordmark_text" name="es_wordmark_text" class="regular-text" value="<?php echo esc_attr( $data['wordmark_text'] ?? '' ); ?>" placeholder="ESTAVILLO"></td>
				</tr>
				<tr>
					<th scope="row"><label for="es_wordmark_url"><?php esc_html_e( 'Wordmark link URL', 'estavillo-portfolio-core' ); ?></label></th>
					<td><input type="url" id="es_wordmark_url" name="es_wordmark_url" class="regular-text" value="<?php echo esc_attr( $data['wordmark_url'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Leave blank to link to the Home page', 'estavillo-portfolio-core' ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="es_wordmark_image"><?php esc_html_e( 'Logo image URL (optional)', 'estavillo-portfolio-core' ); ?></label></th>
					<td>
						<input type="url" id="es_wordmark_image" name="es_wordmark_image" class="regular-text" value="<?php echo esc_attr( $data['wordmark_image'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Leave blank to show the text wordmark', 'estavillo-portfolio-core' ); ?>">
						<p class="description"><?php esc_html_e( 'When set, a logo image replaces the text wordmark in the header. The wordmark text above stays the accessible site name.', 'estavillo-portfolio-core' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Sticky header', 'estavillo-portfolio-core' ); ?></th>
					<td><label><input type="checkbox" name="es_sticky_header" value="1" <?php checked( '0' !== ( $data['sticky_header'] ?? '1' ) ); ?>> <?php esc_html_e( 'Keep the header pinned while scrolling (default). Uncheck for a static header.', 'estavillo-portfolio-core' ); ?></label></td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Header (navigation links)', 'estavillo-portfolio-core' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Used by the header nav, the mobile menu and the footer nav (one shared set). URL can be an in-page anchor (e.g. #work) or a real page URL. Anchors work on Home and, from other pages, jump to Home + that section automatically. Uncheck "Show" to hide a link everywhere. Rows are pre-filled with the current labels.', 'estavillo-portfolio-core' ); ?></p>
			<table class="form-table" role="presentation">
				<?php
				$es_nav_links_data = $data['nav_links'] ?? array();
				$es_nav_defaults   = function_exists( 'es_nav_links' ) ? es_nav_links() : array();
				for ( $i = 0; $i < 4; $i++ ) :
					$es_saved_row  = $es_nav_links_data[ $i ] ?? null;
					$es_link_label = $es_saved_row['label'] ?? ( $es_nav_defaults[ $i ]['label'] ?? '' );
					$es_link_url   = $es_saved_row['url'] ?? ( $es_nav_defaults[ $i ]['url'] ?? '' );
					$es_link_show  = null === $es_saved_row ? true : ( array_key_exists( 'show', $es_saved_row ) ? ! empty( $es_saved_row['show'] ) : true );
					?>
					<tr>
						<th scope="row"><?php echo esc_html( sprintf( __( 'Nav link %d', 'estavillo-portfolio-core' ), $i + 1 ) ); ?></th>
						<td>
							<input type="text" name="es_nav_link_label[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_link_label ); ?>" placeholder="<?php esc_attr_e( 'Label', 'estavillo-portfolio-core' ); ?>">
							<input type="text" name="es_nav_link_url[<?php echo esc_attr( $i ); ?>]" class="regular-text" value="<?php echo esc_attr( $es_link_url ); ?>" placeholder="<?php esc_attr_e( 'URL (e.g. #work or a real page URL)', 'estavillo-portfolio-core' ); ?>">
							<label style="margin-left:8px"><input type="checkbox" name="es_nav_link_show[<?php echo esc_attr( $i ); ?>]" value="1" <?php checked( $es_link_show ); ?>> <?php esc_html_e( 'Show', 'estavillo-portfolio-core' ); ?></label>
						</td>
					</tr>
				<?php endfor; ?>
			</table>

			<h2><?php esc_html_e( 'Footer', 'estavillo-portfolio-core' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Contact email is shared with Connect above; phone and WhatsApp are shared with the Connect section (edit them under "Connect page (dedicated)"). Everything below is footer-specific.', 'estavillo-portfolio-core' ); ?></p>
			<input type="hidden" name="es_hf_footer" value="1">
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="es_footer_note"><?php esc_html_e( 'Footer note (optional)', 'estavillo-portfolio-core' ); ?></label></th>
					<td><input type="text" id="es_footer_note" name="es_footer_note" class="large-text" value="<?php echo esc_attr( $data['footer_note'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Short line under the wordmark, e.g. Product Designer based in Montevideo. Leave blank to hide.', 'estavillo-portfolio-core' ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="es_footer_copyright_name"><?php esc_html_e( 'Copyright name', 'estavillo-portfolio-core' ); ?></label></th>
					<td><input type="text" id="es_footer_copyright_name" name="es_footer_copyright_name" class="regular-text" value="<?php echo esc_attr( $data['footer_copyright_name'] ?? '' ); ?>" placeholder="Ramiro Estavillo">
					<p class="description"><?php esc_html_e( 'Shown as “© [year] [name]” — the year updates automatically.', 'estavillo-portfolio-core' ); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><label for="es_footer_layout"><?php esc_html_e( 'Layout', 'estavillo-portfolio-core' ); ?></label></th>
					<td>
						<?php $es_fl = $data['footer_layout'] ?? 'three'; ?>
						<select id="es_footer_layout" name="es_footer_layout">
							<option value="three" <?php selected( $es_fl, 'three' ); ?>><?php esc_html_e( 'Three columns (default)', 'estavillo-portfolio-core' ); ?></option>
							<option value="two" <?php selected( $es_fl, 'two' ); ?>><?php esc_html_e( 'Two columns', 'estavillo-portfolio-core' ); ?></option>
							<option value="compact" <?php selected( $es_fl, 'compact' ); ?>><?php esc_html_e( 'Compact', 'estavillo-portfolio-core' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="es_footer_width"><?php esc_html_e( 'Width', 'estavillo-portfolio-core' ); ?></label></th>
					<td>
						<?php $es_fw = $data['footer_width'] ?? 'standard'; ?>
						<select id="es_footer_width" name="es_footer_width">
							<option value="standard" <?php selected( $es_fw, 'standard' ); ?>><?php esc_html_e( 'Standard site container (default)', 'estavillo-portfolio-core' ); ?></option>
							<option value="wide" <?php selected( $es_fw, 'wide' ); ?>><?php esc_html_e( 'Wide container', 'estavillo-portfolio-core' ); ?></option>
						</select>
					</td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Footer — social links & location', 'estavillo-portfolio-core' ); ?></h3>
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
					<th scope="row"><label for="es_social_instagram"><?php esc_html_e( 'Instagram URL', 'estavillo-portfolio-core' ); ?></label></th>
					<td>
						<input type="url" id="es_social_instagram" name="es_social_instagram" class="regular-text" value="<?php echo esc_attr( $data['social_instagram'] ?? '' ); ?>" placeholder="https://instagram.com/...">
						<p class="description"><?php esc_html_e( 'Also used by the dedicated Connect page.', 'estavillo-portfolio-core' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="es_footer_location"><?php esc_html_e( 'Location', 'estavillo-portfolio-core' ); ?></label></th>
					<td><input type="text" id="es_footer_location" name="es_footer_location" class="regular-text" value="<?php echo esc_attr( $data['footer_location'] ?? '' ); ?>" placeholder="Montevideo, Uruguay"></td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Footer — visibility', 'estavillo-portfolio-core' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Hide any footer section. Items with no data of their own (e.g. an empty social URL) hide themselves automatically regardless.', 'estavillo-portfolio-core' ); ?></p>
			<?php $es_vis = $data['footer_visibility'] ?? array(); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Show sections', 'estavillo-portfolio-core' ); ?></th>
					<td>
					<label style="display:inline-block;min-width:150px"><input type="checkbox" name="es_footer_show[nav]" value="1" <?php checked( ! isset( $es_vis['nav'] ) || ! empty( $es_vis['nav'] ) ); ?>> Navigation</label>
					<label style="display:inline-block;min-width:150px"><input type="checkbox" name="es_footer_show[email]" value="1" <?php checked( ! isset( $es_vis['email'] ) || ! empty( $es_vis['email'] ) ); ?>> Email</label>
					<label style="display:inline-block;min-width:150px"><input type="checkbox" name="es_footer_show[phone]" value="1" <?php checked( ! isset( $es_vis['phone'] ) || ! empty( $es_vis['phone'] ) ); ?>> Phone</label>
					<label style="display:inline-block;min-width:150px"><input type="checkbox" name="es_footer_show[whatsapp]" value="1" <?php checked( ! isset( $es_vis['whatsapp'] ) || ! empty( $es_vis['whatsapp'] ) ); ?>> WhatsApp</label>
					<label style="display:inline-block;min-width:150px"><input type="checkbox" name="es_footer_show[linkedin]" value="1" <?php checked( ! isset( $es_vis['linkedin'] ) || ! empty( $es_vis['linkedin'] ) ); ?>> LinkedIn</label>
					<label style="display:inline-block;min-width:150px"><input type="checkbox" name="es_footer_show[instagram]" value="1" <?php checked( ! isset( $es_vis['instagram'] ) || ! empty( $es_vis['instagram'] ) ); ?>> Instagram</label>
					<label style="display:inline-block;min-width:150px"><input type="checkbox" name="es_footer_show[behance]" value="1" <?php checked( ! isset( $es_vis['behance'] ) || ! empty( $es_vis['behance'] ) ); ?>> Behance</label>
					<label style="display:inline-block;min-width:150px"><input type="checkbox" name="es_footer_show[location]" value="1" <?php checked( ! isset( $es_vis['location'] ) || ! empty( $es_vis['location'] ) ); ?>> Location</label>
					<label style="display:inline-block;min-width:150px"><input type="checkbox" name="es_footer_show[note]" value="1" <?php checked( ! isset( $es_vis['note'] ) || ! empty( $es_vis['note'] ) ); ?>> Footer note</label>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Appearance', 'estavillo-portfolio-core' ); ?></h2>
			<?php
			/*
			 * Esto era un checkbox ("Portfolio dark mode: Enabled / Disabled")
			 * de la etapa en la que el sistema oscuro se estaba probando y
			 * convivía con el look claro de Kadence. Ya no lo es.
			 *
			 * Por qué se retiró y no simplemente se dejó marcado: "Disabled"
			 * nunca devolvió un sitio claro. Los tokens claros existen en
			 * tokens.css bajo [data-theme='light'], pero nada emite ese
			 * atributo y la capa que viste el chrome de Kadence (theme-dark.css)
			 * no tiene contraparte clara. Ahora que el fondo oscuro se aplica
			 * en html/body (assets/css/base.css), destildar la casilla dejaba
			 * el sitio a medio camino: fondo oscuro con el chrome de Kadence
			 * sin vestir. Un interruptor cuyo "off" es un bug no es una
			 * opción, es una trampa.
			 *
			 * El punto de extensión sigue existiendo en código: el theme
			 * resuelve el modo con apply_filters( 'es_theme_dark_mode', true ).
			 * El día que exista un modo claro de verdad, ese filtro vuelve a
			 * la pantalla como una elección entre dos sistemas completos.
			 */
			?>
			<p class="description"><?php esc_html_e( 'The portfolio uses a single dark visual system across every template — home, case studies, the fixed pages, and the Kadence-native views (generic pages, posts, archives, search, 404) — in both English and Spanish. There is nothing to configure here: it is the theme, not a setting.', 'estavillo-portfolio-core' ); ?></p>

			<?php submit_button( __( 'Save Portfolio Content', 'estavillo-portfolio-core' ), 'primary', 'es_portfolio_home_content_submit' ); ?>
		</form>
	</div>
	<?php
}

/**
 * Puente hacia los filtros que YA existían en el tema (Home v1) para
 * About. No son filtros nuevos — ver el comentario de cabecera del
 * archivo. Cada callback solo pisa el default si el campo tiene valor.
 *
 * Nota Hero: el copy del Hero de Home NO tiene campos acá a propósito
 * (revisión del ticket de Home) — es contenido Gutenberg real, editable
 * inline en la página vía el bloque estavillo/home-hero. Solo las
 * variantes del fondo animado siguen fuera de Gutenberg (Customizer,
 * inc/theme-options.php del theme).
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

function es_portfolio_filter_about_tools( $default ) {
	$data = es_portfolio_get_home_content();
	return es_portfolio_merge_keyed_rows( $default, $data['about_tools'] ?? array(), 'title' );
}
add_filter( 'es_about_tools', 'es_portfolio_filter_about_tools' );

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
 * Puentes para la página Connect dedicada (ticket Connect). Exclusivos de
 * esta página — Home sigue leyendo únicamente es_home_cta_title/_cta_lead
 * de arriba, sin tocar.
 */
function es_portfolio_filter_connect_eyebrow( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['connect_eyebrow'] ) ? $data['connect_eyebrow'] : $default;
}
add_filter( 'es_connect_eyebrow', 'es_portfolio_filter_connect_eyebrow' );

function es_portfolio_filter_connect_title( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['connect_title'] ) ? $data['connect_title'] : $default;
}
add_filter( 'es_connect_title', 'es_portfolio_filter_connect_title' );

function es_portfolio_filter_connect_intro( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['connect_intro'] ) ? $data['connect_intro'] : $default;
}
add_filter( 'es_connect_intro', 'es_portfolio_filter_connect_intro' );

function es_portfolio_filter_contact_phone( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['contact_phone'] ) ? $data['contact_phone'] : $default;
}
add_filter( 'es_contact_phone', 'es_portfolio_filter_contact_phone' );

function es_portfolio_filter_contact_whatsapp( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['contact_whatsapp'] ) ? $data['contact_whatsapp'] : $default;
}
add_filter( 'es_contact_whatsapp', 'es_portfolio_filter_contact_whatsapp' );

function es_portfolio_filter_connect_country( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['connect_country'] ) ? $data['connect_country'] : $default;
}
add_filter( 'es_connect_country', 'es_portfolio_filter_connect_country' );

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
			// Absent 'show' (older saved data, pre-Phase 5) = visible.
			'show'  => ! array_key_exists( 'show', $custom_link ) || ! empty( $custom_link['show'] ),
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
	if ( ! empty( $data['social_instagram'] ) ) {
		$default['Instagram'] = $data['social_instagram'];
	}
	return $default;
}
add_filter( 'es_social_links', 'es_portfolio_filter_social_links' );

function es_portfolio_filter_footer_location( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['footer_location'] ) ? $data['footer_location'] : $default;
}
add_filter( 'es_footer_location', 'es_portfolio_filter_footer_location' );

/**
 * Phase 5 — Header/Footer global settings bridges. Same "empty never overrides
 * the theme default" principle as every filter above: a blank field leaves the
 * theme's own default in place. All language-neutral (one global value).
 */
function es_portfolio_filter_wordmark_text( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['wordmark_text'] ) ? $data['wordmark_text'] : $default;
}
add_filter( 'es_wordmark_text', 'es_portfolio_filter_wordmark_text' );

function es_portfolio_filter_wordmark_url( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['wordmark_url'] ) ? $data['wordmark_url'] : $default;
}
add_filter( 'es_wordmark_url', 'es_portfolio_filter_wordmark_url' );

function es_portfolio_filter_wordmark_image( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['wordmark_image'] ) ? $data['wordmark_image'] : $default;
}
add_filter( 'es_wordmark_image', 'es_portfolio_filter_wordmark_image' );

function es_portfolio_filter_sticky_header( $default ) {
	$data = es_portfolio_get_home_content();
	// Only override once the Header section was saved at least once; until then
	// keep the theme default (sticky enabled).
	if ( ! array_key_exists( 'sticky_header', $data ) ) {
		return $default;
	}
	return '0' !== $data['sticky_header'];
}
add_filter( 'es_sticky_header', 'es_portfolio_filter_sticky_header' );

function es_portfolio_filter_footer_note( $default ) {
	$data = es_portfolio_get_home_content();
	return isset( $data['footer_note'] ) && '' !== $data['footer_note'] ? $data['footer_note'] : $default;
}
add_filter( 'es_footer_note', 'es_portfolio_filter_footer_note' );

function es_portfolio_filter_footer_copyright_name( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['footer_copyright_name'] ) ? $data['footer_copyright_name'] : $default;
}
add_filter( 'es_footer_copyright_name', 'es_portfolio_filter_footer_copyright_name' );

function es_portfolio_filter_footer_layout( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['footer_layout'] ) ? $data['footer_layout'] : $default;
}
add_filter( 'es_footer_layout', 'es_portfolio_filter_footer_layout' );

function es_portfolio_filter_footer_width( $default ) {
	$data = es_portfolio_get_home_content();
	return ! empty( $data['footer_width'] ) ? $data['footer_width'] : $default;
}
add_filter( 'es_footer_width', 'es_portfolio_filter_footer_width' );

function es_portfolio_filter_footer_visibility( $default ) {
	$data = es_portfolio_get_home_content();
	if ( isset( $data['footer_visibility'] ) && is_array( $data['footer_visibility'] ) ) {
		return array_merge( (array) $default, $data['footer_visibility'] );
	}
	return $default;
}
add_filter( 'es_footer_visibility', 'es_portfolio_filter_footer_visibility' );

/*
 * Portfolio dark mode — el puente se retiró a propósito.
 *
 * Este filtro leía la clave 'theme_dark_mode' de la opción y, con el guard
 * array_key_exists(), cualquier guardado previo de la sección Appearance
 * dejaba escrito un '0' que ganaba SIEMPRE contra el default del theme. Con
 * el modo oscuro ya como sistema único (es_theme_dark_mode_enabled() devuelve
 * true), ese '0' guardado en su momento habría apagado el theme entero en
 * producción sin que nadie tocara nada.
 *
 * No se reemplaza por otro puente: no hay nada que elegir mientras exista un
 * solo sistema visual. El theme sigue resolviendo el modo con
 * apply_filters( 'es_theme_dark_mode', true ), así que el punto de extensión
 * queda disponible para cuando exista un modo claro real.
 */
