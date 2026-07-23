<?php
/**
 * Estavillo Child — funciones del tema.
 *
 * Foundation técnica del nuevo portfolio ESTAVILLO sobre Kadence.
 * Todo lo específico vive en inc/ para mantener este archivo mínimo.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ES_CHILD_VERSION', '0.2.12' );
define( 'ES_CHILD_DIR', get_stylesheet_directory() );
define( 'ES_CHILD_URI', get_stylesheet_directory_uri() );

require ES_CHILD_DIR . '/inc/enqueue.php';
require ES_CHILD_DIR . '/inc/theme-options.php';
require ES_CHILD_DIR . '/inc/selected-work-fallback.php';
require ES_CHILD_DIR . '/inc/featured-case-fallback.php';
require ES_CHILD_DIR . '/inc/work-page-fallback.php';
require ES_CHILD_DIR . '/inc/block-styles.php';
require ES_CHILD_DIR . '/inc/how-i-work-illustrations.php';

/**
 * Textdomain del child theme.
 */
function es_child_setup() {
	load_child_theme_textdomain( 'estavillo-child', ES_CHILD_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'es_child_setup' );

/* -------------------------------------------------------------------------
 * Compatibilidad Polylang
 * -------------------------------------------------------------------------
 * Los strings de interfaz del tema (labels de secciones, CTAs placeholder)
 * se registran en Polylang cuando el plugin está activo, y es__() los
 * resuelve con pll__() si existe. Sin Polylang, cae en __() estándar.
 * El copy final NO se hardcodea acá: estas son cadenas provisionales
 * marcadas como editables en los template parts.
 * ---------------------------------------------------------------------- */

/**
 * Strings de interfaz registrables/traducibles del tema.
 *
 * @return string[] Mapa clave => texto por defecto (EN provisional).
 */
function es_child_ui_strings() {
	return array(
		'hero_eyebrow'        => 'Product Designer · Systems & Operations',
		'hero_cta_primary'    => 'View featured case',
		'hero_cta_secondary'  => 'See how I work',
		'nav_work'            => 'Work',
		'nav_how'             => 'How I Work',
		'nav_about'           => 'About',
		'nav_connect'         => 'Connect',
		'featured_label'      => 'Featured case',
		'featured_cta'        => 'Read the case study',
		'process_label'       => 'How I work',
		'process_cta'         => 'See the full process',
		'work_label'          => 'Selected work',
		'work_view_all'       => 'All work',
		'work_view_case'      => 'View case study',
		'about_label'         => 'About',
		'about_intro_label'   => 'My approach',
		'about_cta'           => 'More about me',
		'cta_label'           => 'Connect',
		'cta_button'          => 'Write me',
		'lang_switch_label'   => 'Language switch',
		'case_meta_status'    => 'Status',
		'case_meta_role'      => 'Role',
		'case_meta_tools'     => 'Tools',
		'case_meta_period'    => 'Period',
	);
}

/**
 * Email de contacto por defecto (editable por filtro es_contact_email).
 *
 * @return string
 */
function es_contact_email() {
	return apply_filters( 'es_contact_email', 'hello@ramiroestavillo.com' );
}

/**
 * Registro ordenado de secciones de la Home (fundación para editabilidad).
 *
 * Fuente única de la NARRATIVA de la home. Cada entrada mapea una clave de
 * sección → su template part. El orden del array es el orden de render:
 *
 *   Hero → How I Work → Featured Case → Selected Work → About → Connect
 *
 * Reordenar / quitar / insertar secciones = filtrar 'es_home_sections' (p. ej.
 * desde Code Snippets), sin editar el template PHP:
 *
 *   add_filter( 'es_home_sections', function ( $s ) {
 *       // mover About antes de Selected Work, por ejemplo:
 *       $about = $s['about']; unset( $s['about'] );
 *       // ...reordenar el array y devolverlo
 *       return $s;
 *   } );
 *
 * MIGRACIÓN FUTURA: cada valor es hoy un template part; mañana cada clave puede
 * apuntar a un block/pattern reutilizable sin reescribir el template — la clave
 * de sección es el contrato estable. Ver README (Foundation for editability).
 *
 * @return array<string,string> clave de sección => slug del template part
 */
function es_home_sections() {
	return apply_filters(
		'es_home_sections',
		array(
			'hero'          => 'template-parts/hero-home',
			'how-i-work'    => 'template-parts/how-i-work',
			'featured'      => 'template-parts/featured-case',
			'selected-work' => 'template-parts/selected-work',
			'about'         => 'template-parts/about-teaser',
			'connect'       => 'template-parts/footer-cta',
		)
	);
}

/**
 * Enlaces de navegación (header y footer). Editable por filtro.
 * Por defecto anclan a las secciones de la home de una sola página; se
 * pueden apuntar a páginas reales (Work/About/…) vía el filtro cuando existan.
 *
 * @return array<int,array{label:string,url:string}>
 */
function es_nav_links() {
	return apply_filters(
		'es_nav_links',
		array(
			array(
				'label' => es__( 'nav_work' ),
				'url'   => '#work',
			),
			array(
				'label' => es__( 'nav_how' ),
				'url'   => '#process',
			),
			array(
				'label' => es__( 'nav_about' ),
				'url'   => '#about',
			),
			array(
				'label' => es__( 'nav_connect' ),
				'url'   => '#connect',
			),
		)
	);
}

/**
 * Arma un breadcrumb trail estándar: Home, un nivel intermedio opcional
 * (reusando es_nav_links() — el mismo array que ya alimenta el header, el
 * menú mobile y el footer), y un último crumb opcional para la página/caso
 * actual (siempre sin link, sea cual sea su posición — ver
 * template-parts/breadcrumbs.php).
 *
 * Reusar es_nav_links() en vez de hardcodear URLs acá tiene dos ventajas:
 * si el admin repunta un nav link a una página real, el breadcrumb la
 * sigue automáticamente; y con Polylang activo, esos links y home_url()
 * ya resuelven al idioma actual de forma nativa — cero código extra.
 *
 * @param string $nav_label_key Clave de es_child_ui_strings() a matchear
 *                               contra la label de es_nav_links() (p.ej.
 *                               'nav_work'). Cadena vacía = omitir ese nivel.
 * @param string $current_label Título de la página/caso actual. Cadena
 *                               vacía = omitir (p.ej. en la propia página
 *                               Work, donde el nav link YA es el último
 *                               crumb).
 * @return array<int,array{label:string,url?:string}>
 */
function es_breadcrumb_trail( $nav_label_key = '', $current_label = '' ) {
	$trail = array(
		array(
			'label' => __( 'Home', 'estavillo-child' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( $nav_label_key ) {
		foreach ( es_nav_links() as $es_bc_link ) {
			if ( es__( $nav_label_key ) === $es_bc_link['label'] ) {
				$trail[] = $es_bc_link;
				break;
			}
		}
	}

	if ( $current_label ) {
		$trail[] = array( 'label' => $current_label );
	}

	return $trail;
}

/**
 * Librería curada de íconos para los pasos de "How I Work" (línea fina,
 * 16x16, currentColor — mismo lenguaje visual que el ícono del toggle
 * Light/Dark en el header). Whitelist deliberada: el plugin solo guarda la
 * CLAVE elegida en un <select> (nunca HTML libre), así que no hay superficie
 * de XSS acá — ver es_portfolio_home_content_page() en el plugin, sección
 * "How I Work".
 *
 * @return array<string,string> clave => markup SVG completo.
 */
function es_process_icon_library() {
	$stroke = 'fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"';
	return array(
		'compass'  => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><circle cx="8" cy="8" r="6.2"/><path d="M10.2 5.8 8.9 8.9 5.8 10.2 7.1 7.1z"/></svg>',
		'flow'     => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><circle cx="3" cy="8" r="1.4"/><circle cx="13" cy="8" r="1.4"/><path d="M4.4 8h7.2M9 5.2 11.8 8 9 10.8"/></svg>',
		'map'      => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><path d="M2 4.3 6 3l4 1.3 4-1.3v9.4L10 13.7 6 12.4l-4 1.3z"/><path d="M6 3v9.4M10 4.3v9.4"/></svg>',
		'target'   => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><circle cx="8" cy="8" r="6"/><circle cx="8" cy="8" r="3"/><circle cx="8" cy="8" r="0.6" fill="currentColor"/></svg>',
		'layers'   => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><path d="M8 2 14 5.2 8 8.4 2 5.2Z"/><path d="M2 9.4 8 12.6 14 9.4"/></svg>',
		'tool'     => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><path d="M9.7 3.3a2.6 2.6 0 0 0-3.4 3.4L2.9 10l1.6 1.6 3.3-3.4a2.6 2.6 0 0 0 3.4-3.4l-1.5 1.5-1.2-1.2Z"/></svg>',
		'check'    => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><circle cx="8" cy="8" r="6.2"/><path d="M5.2 8.2 7.2 10.2 10.8 6"/></svg>',
		'document' => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><rect x="3.2" y="2" width="9.6" height="12" rx="1"/><path d="M5.6 5.8h4.8M5.6 8.4h4.8M5.6 11h3"/></svg>',
		'bulb'     => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><path d="M8 2.2a4 4 0 0 1 2.2 7.3c-.4.3-.7.9-.7 1.4v.3H6.5v-.3c0-.5-.3-1.1-.7-1.4A4 4 0 0 1 8 2.2Z"/><path d="M6.6 13.4h2.8M7 14.8h2"/></svg>',
		'rocket'   => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><path d="M8 2c1.8 1.4 2.8 3.6 2.8 6.2 0 1-.2 2-.6 2.9H5.8c-.4-.9-.6-1.9-.6-2.9C5.2 5.6 6.2 3.4 8 2Z"/><circle cx="8" cy="7" r="1"/><path d="M5.8 11.1 4.4 13M10.2 11.1l1.4 1.9M6.6 13.6v1.2M9.4 13.6v1.2"/></svg>',
	);
}

/**
 * Choices para el <select> de ícono por paso en Home Content (wp-admin).
 * Llamado desde el plugin con function_exists() como guarda (ver
 * home-content-options.php) — el plugin nunca asume que el tema activo
 * define esto.
 *
 * @return array<string,string> clave => label legible.
 */
function es_process_icon_choices() {
	$labels = array(
		'compass'  => __( 'Compass (understand)', 'estavillo-child' ),
		'flow'     => __( 'Flow (connect)', 'estavillo-child' ),
		'map'      => __( 'Map (plan)', 'estavillo-child' ),
		'target'   => __( 'Target (bottleneck)', 'estavillo-child' ),
		'layers'   => __( 'Layers (design)', 'estavillo-child' ),
		'tool'     => __( 'Tool (prototype)', 'estavillo-child' ),
		'check'    => __( 'Check (validate)', 'estavillo-child' ),
		'document' => __( 'Document (archive)', 'estavillo-child' ),
		'bulb'     => __( 'Bulb (idea)', 'estavillo-child' ),
		'rocket'   => __( 'Rocket (scale)', 'estavillo-child' ),
	);
	return $labels;
}

/**
 * Markup SVG de un ícono por clave, o cadena vacía si la clave no existe en
 * la librería (nunca imprime HTML fuera de esta whitelist).
 *
 * @param string $key Clave dentro de es_process_icon_library().
 * @return string
 */
function es_process_icon_svg( $key ) {
	$library = es_process_icon_library();
	return isset( $library[ $key ] ) ? $library[ $key ] : '';
}

/**
 * Defaults de los 6 pasos de "How I Work" — única fuente de verdad,
 * usada tanto por el teaser de Home (template-parts/how-i-work.php) como
 * por la página dedicada (template-parts/how-i-work-detail.php), así que
 * los dos siempre ven el mismo contenido base si Home Content está vacío.
 * 'why' / 'example' / 'tools' quedan sin default (opcionales, solo se
 * completan si el admin los carga en Home Content).
 *
 * @return array<int,array>
 */
function es_home_process_steps_defaults() {
	return array(
		array(
			'title'    => 'Understand the system',
			'text'     => 'Before I open any design tool, I map how the system actually operates — not how it\'s supposed to.',
			'icon_key' => 'compass',
			'why'      => 'A screen is the visible 1% of a system. Redesigning it without understanding the other 99% just makes the same problem look better.',
			'example'  => 'On the Workshop Quoting System, that meant sitting on the shop floor to see how a quote actually got built — paper, memory, a spreadsheet nobody trusted — long before assuming the fix was a better form.',
			'tools'    => 'Contextual inquiry, stakeholder mapping, process observation',
		),
		array(
			'title'    => 'Find the real bottleneck',
			'text'     => 'Every system has one point where things actually break. I look for that specific point, not a list of general issues.',
			'icon_key' => 'target',
			'why'      => 'Teams often ask for a redesign when the real problem sits one step earlier or later. Naming the actual bottleneck precisely is what keeps the rest of the work from becoming decoration.',
			'example'  => 'In Trazur, the interface had real usability issues, but the deeper bottleneck was trust — people didn\'t believe the platform understood their situation, so they disengaged before the screen ever became the problem.',
			'tools'    => 'Root-cause analysis, journey mapping, structured evaluation',
		),
		array(
			'title'    => 'Gather evidence',
			'text'     => 'I build a case before I build a solution — evidence a team can react to, not an opinion they have to accept.',
			'icon_key' => 'document',
			'why'      => 'A strong opinion in the room isn\'t evidence, no matter how senior it comes from. Evidence turns a debate about taste into a decision about the system.',
			'example'  => 'For Trazur, that meant pairing traditional research with AI-assisted analysis to process a heavier volume of evidence faster — every synthesized finding still reviewed by a person before it counted as one.',
			'tools'    => 'Interviews, field observation, AI-assisted synthesis',
		),
		array(
			'title'    => 'Challenge assumptions',
			'text'     => 'Before anything gets built, I try to break the plan myself — find where it depends on something nobody\'s actually confirmed.',
			'icon_key' => 'check',
			'why'      => 'Every proposal quietly assumes something. Finding that assumption and testing the one that would be expensive to get wrong is cheaper than discovering it after launch.',
			'example'  => 'On the Workshop Quoting System, that meant checking with the person who actually prices jobs that a proposed shortcut wasn\'t quietly removing a judgment call they relied on.',
			'tools'    => 'Assumption mapping, walkthroughs with real users, comparative testing',
		),
		array(
			'title'    => 'Design practical solutions',
			'text'     => 'Not every problem needs a new interface — sometimes the strongest move is fixing what\'s underneath it. When an interface is the right call, I design it for the conditions that actually exist: limited connectivity, time pressure, mixed skill levels, not an idealized user in an ideal setting.',
			'icon_key' => 'layers',
			'why'      => 'A solution that only works under perfect conditions doesn\'t survive a busy shop floor or a rural connection that drops mid-session. Practical means it still works on a bad day.',
			'example'  => 'Trazur\'s proposed solution was built around low-connectivity, low-fidelity conditions from the start, instead of assuming a fast connection and a confident, tech-comfortable user.',
			'tools'    => 'Service blueprints, wireframing, systems-level design decisions',
		),
		array(
			'title'    => 'Iterate with purpose',
			'text'     => 'I treat a first version as a hypothesis, not a finish line — and decide in advance what would prove it wrong.',
			'icon_key' => 'rocket',
			'why'      => 'Iteration without a target just produces motion. Knowing what "wrong" looks like before you ship is what makes the next version actually better, not just different.',
			'example'  => 'Across projects — from an operational tool built for Guzmán Villalba to institutional work at Ceibal — the versions that held up were the ones designed to be revisited on purpose, not the ones treated as finished at delivery.',
			'tools'    => 'Usage review, structured feedback loops, versioned documentation',
		),
	);
}

/**
 * Los 6 pasos de "How I Work" ya pasados por el filtro 'es_home_process_steps'
 * (con los defaults de arriba) — llamado por el teaser Y la página dedicada,
 * así que las dos leen exactamente el mismo dato editado desde Home Content.
 *
 * @return array<int,array>
 */
function es_home_process_steps() {
	return apply_filters( 'es_home_process_steps', es_home_process_steps_defaults() );
}

/**
 * Defaults del teaser de "How I Work" en Home (docs/HOW-I-WORK-CONTENT-
 * SPEC.md §1A) — deliberadamente NO derivados de es_home_process_steps():
 * el teaser agrupa los 6 pasos en 3 ideas con copy propio, escrito para
 * ser más corto/genérico que cualquier paso individual, no una selección
 * de frases de los pasos. Mismo criterio de "Home nunca se rompe": si
 * algún día se agrega un campo editable para esto, cae acá como default.
 *
 * @return array{headline:string,lead:string,groups:array<int,array{title:string,text:string}>}
 */
function es_home_process_teaser_defaults() {
	return array(
		'headline' => "I don't start with interfaces. I start by understanding the system.",
		'lead'     => 'The result should make sense for the people using it, and for the system that has to carry it.',
		'groups'   => array(
			array(
				'title' => 'Understand',
				'text'  => 'See how people, information and goals actually connect.',
			),
			array(
				'title' => 'Explore',
				'text'  => 'Test ideas and challenge assumptions before committing to one.',
			),
			array(
				'title' => 'Improve',
				'text'  => 'Build something that works — and keeps working.',
			),
		),
	);
}

/**
 * El teaser ya pasado por el filtro 'es_home_process_teaser' (con los
 * defaults de arriba) — mismo patrón que es_home_process_steps().
 *
 * @return array
 */
function es_home_process_teaser() {
	return apply_filters( 'es_home_process_teaser', es_home_process_teaser_defaults() );
}

/**
 * Whitelist de tags/atributos SVG permitidos al imprimir un ícono de la
 * librería curada (compartida por process-icons y hobby-icons — mismo
 * shape de SVG simple, línea fina, sin scripts/estilos/eventos).
 *
 * @return array
 */
function es_icon_svg_kses_rules() {
	return array(
		'svg'    => array(
			'xmlns'           => true,
			'width'           => true,
			'height'          => true,
			'viewbox'         => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'aria-hidden'     => true,
			'focusable'       => true,
		),
		'g'      => array(
			'class' => true,
		),
		'circle' => array(
			'cx'   => true,
			'cy'   => true,
			'r'    => true,
			'fill' => true,
		),
		'path'   => array(
			'd'         => true,
			'fill'      => true,
			'fill-rule' => true,
			'clip-rule' => true,
			'class'     => true,
		),
		'rect'   => array(
			'x'      => true,
			'y'      => true,
			'width'  => true,
			'height' => true,
			'rx'     => true,
		),
	);
}

/**
 * Markup seguro (ya pasado por wp_kses) del ícono de un paso de "How I
 * Work": primero la librería curada (icon_key), si no la clave legacy
 * 'icon' (HTML libre, wp_kses_post) por compatibilidad hacia atrás.
 * Cadena vacía si el paso no tiene ninguno — el layout no cambia.
 * Compartido por el teaser de Home y la página dedicada.
 *
 * @param array $step Un elemento de es_home_process_steps().
 * @return string
 */
function es_process_step_icon_markup( $step ) {
	$icon_key = ! empty( $step['icon_key'] ) ? $step['icon_key'] : '';
	$icon_svg = $icon_key ? es_process_icon_svg( $icon_key ) : '';

	if ( '' !== $icon_svg ) {
		return wp_kses( $icon_svg, es_icon_svg_kses_rules() );
	}
	if ( ! empty( $step['icon'] ) ) {
		return wp_kses_post( $step['icon'] );
	}
	return '';
}

/**
 * Imprime una entrada de Experience (Selected o Previous — mismo esquema,
 * $compact solo cambia la clase modificadora para el tratamiento visual
 * más chico de Previous Experience). Vive acá (no en el template-part)
 * para no redeclarar una función cada vez que about-content.php se
 * incluye — mismo criterio que el resto de los helpers de render de este
 * archivo (es_process_step_icon_markup(), etc.).
 *
 * Cada bullet de 'contributions' vive en un <details> nativo propio ("Key
 * contributions") — colapsado por default, mismo componente que agrupa
 * Previous Experience/Other Certifications enteras (.es-about-details,
 * ver assets/css/pages.css).
 *
 * @param array $es_exp  Una fila de es_about_experience_{selected,previous}_defaults().
 * @param bool  $compact True para Previous Experience (tratamiento más chico).
 */
function es_about_render_experience_item( $es_exp, $compact = false ) {
	if ( empty( $es_exp['org'] ) ) {
		return;
	}
	$es_meta = array_filter( array( $es_exp['location'] ?? '', $es_exp['period'] ?? '' ) );
	?>
	<article class="es-exp-item<?php echo $compact ? ' es-exp-item--compact' : ''; ?>" data-es-reveal>
		<div class="es-exp-item__head">
			<?php if ( ! empty( $es_exp['role'] ) ) : ?>
				<h3 class="es-exp-item__role"><?php echo esc_html( $es_exp['role'] ); ?></h3>
			<?php endif; ?>
			<span class="es-exp-item__org"><?php echo esc_html( $es_exp['org'] ); ?></span>
		</div>
		<?php if ( ! empty( $es_meta ) ) : ?>
			<div class="es-exp-item__meta"><?php echo esc_html( implode( ' · ', $es_meta ) ); ?></div>
		<?php endif; ?>
		<?php if ( ! empty( $es_exp['summary'] ) ) : ?>
			<p class="es-exp-item__summary"><?php echo esc_html( $es_exp['summary'] ); ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $es_exp['contributions'] ) && is_array( $es_exp['contributions'] ) ) : ?>
			<details class="es-about-details es-about-details--nested">
				<summary><?php esc_html_e( 'Key contributions', 'estavillo-child' ); ?></summary>
				<div class="es-about-details__body">
					<ul class="es-about-contributions">
						<?php foreach ( $es_exp['contributions'] as $es_line ) : ?>
							<li><?php echo esc_html( $es_line ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</details>
		<?php endif; ?>
		<?php if ( ! empty( $es_exp['link_url'] ) ) : ?>
			<a class="es-exp-item__link" href="<?php echo esc_url( $es_exp['link_url'] ); ?>">
				<?php echo esc_html( ! empty( $es_exp['link_label'] ) ? $es_exp['link_label'] : __( 'View case study', 'estavillo-child' ) ); ?>
				<span aria-hidden="true">&rarr;</span>
			</a>
		<?php endif; ?>
	</article>
	<?php
}

/**
 * Claves canónicas de la librería de hobby-icons: el artwork final
 * APROBADO (estavillo-hobby-icons.zip), instalado como archivos en
 * assets/icons/{clave}.svg. Los archivos son la fuente de verdad —
 * dibujo a mano en paths RELLENOS (fill-rule evenodd), no la línea
 * 1.4px de los process icons — y NO deben redibujarse ni pasarse por
 * optimizadores: en la integración solo se normalizó metadata (viewBox
 * cuadrado centrado, fill="black" → currentColor, aria-hidden) sin
 * tocar un solo punto de la geometría.
 *
 * @return string[] claves en el orden canónico del set.
 */
function es_hobby_icon_keys() {
	return array( 'taekwondo', 'guitar', 'coffee', 'horse-head', 'horse-run', 'drawing', 'travel', 'cinema' );
}

/**
 * Alias de claves legacy (la librería inline 20×20 de la sprint de
 * infra/polish) → clave del artwork aprobado. Un hobby guardado en la
 * base con la clave vieja sigue renderizando sin reconfigurar nada;
 * el <select> de wp-admin también resuelve el alias antes de marcar
 * la opción seleccionada.
 *
 * @param string $key Clave guardada (vieja o nueva).
 * @return string Clave canónica dentro de es_hobby_icon_keys().
 */
function es_hobby_icon_resolve_key( $key ) {
	$aliases = array(
		'music' => 'guitar',
		'horse' => 'horse-head',
	);
	return isset( $aliases[ $key ] ) ? $aliases[ $key ] : $key;
}

/**
 * Librería de hobby-icons: clave => markup SVG completo, leído de
 * assets/icons/{clave}.svg con cache estática por request (8 lecturas
 * chicas como máximo, una sola vez). Si un archivo falta, la clave
 * simplemente no aparece — la plantilla ya maneja el ícono vacío.
 *
 * La micro-interacción de hover/focus vive en assets/css/pages.css
 * (".es-hobby-icon", V1: translateY + acento — nunca autoplay/loop, y
 * cubierta por la regla global de prefers-reduced-motion en base.css).
 *
 * @return array<string,string> clave => markup SVG completo.
 */
function es_hobby_icon_library() {
	static $library = null;
	if ( null !== $library ) {
		return $library;
	}
	$library = array();
	$dir     = get_stylesheet_directory() . '/assets/icons/';
	foreach ( es_hobby_icon_keys() as $key ) {
		$file = $dir . $key . '.svg';
		if ( is_readable( $file ) ) {
			$svg = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- archivo local del propio tema, no remoto.
			if ( is_string( $svg ) && '' !== $svg ) {
				$library[ $key ] = trim( $svg );
			}
		}
	}
	return $library;
}

/**
 * Choices para el <select> de ícono por hobby en Home Content (wp-admin).
 * Solo las claves canónicas del artwork aprobado — los alias legacy no
 * se ofrecen como opción nueva, solo se resuelven al leer.
 *
 * @return array<string,string> clave => label legible.
 */
function es_hobby_icon_choices() {
	return array(
		'taekwondo'  => __( 'Taekwondo', 'estavillo-child' ),
		'guitar'     => __( 'Guitar / Rock Music', 'estavillo-child' ),
		'coffee'     => __( 'Coffee', 'estavillo-child' ),
		'horse-head' => __( 'Horse Head', 'estavillo-child' ),
		'horse-run'  => __( 'Horse Running', 'estavillo-child' ),
		'drawing'    => __( 'Drawing', 'estavillo-child' ),
		'travel'     => __( 'Travel', 'estavillo-child' ),
		'cinema'     => __( 'Cinema', 'estavillo-child' ),
	);
}

/**
 * Markup SVG de un hobby-icon por clave (resolviendo alias legacy), o
 * cadena vacía si no existe en la librería.
 *
 * @param string $key Clave dentro de es_hobby_icon_keys() o un alias.
 * @return string
 */
function es_hobby_icon_svg( $key ) {
	$key     = es_hobby_icon_resolve_key( $key );
	$library = es_hobby_icon_library();
	return isset( $library[ $key ] ) ? $library[ $key ] : '';
}

/**
 * Defaults de "Hobbies & interests" (About) — 9 items confirmados en
 * docs/about-page-authoritative-source.md §8 (reemplaza los 7 anteriores;
 * 3 quedan renombrados — "Horse riding"→"Horses", "Travel"→"Travelling",
 * "Cinema"→"Movies & Series" — mismo hobby real, wording del documento
 * fuente). "Gaming" y "Photography" son nuevos y no tienen ícono en la
 * librería curada (es_hobby_icon_choices() — 8 claves, ninguna de
 * gaming/fotografía); el documento fuente pide "keep the current approved
 * icon system", así que no se dibuja arte nuevo acá — quedan con
 * icon:'', que ya cae en el estado --empty existente del template (mismo
 * mecanismo que cualquier hobby sin ícono asignado desde wp-admin). El
 * texto corto se preserva para los 7 hobbies que ya lo tenían (adaptado a
 * la nueva label donde cambió); Gaming/Photography quedan sin texto corto
 * — no había nada aprobado que reusar para esos dos.
 *
 * @return array<int,array{label:string,icon:string,text:string,show:bool}>
 */
function es_home_about_hobbies_defaults() {
	return array(
		array(
			'label' => 'Horses',
			'icon'  => 'horse-head',
			'text'  => 'One of the few things that gets me fully off-screen.',
			'show'  => true,
		),
		array(
			'label' => 'Gaming',
			'icon'  => '',
			'text'  => '',
			'show'  => true,
		),
		array(
			'label' => 'Movies & Series',
			'icon'  => 'cinema',
			'text'  => 'A steady source of pacing and structure, outside of any brief.',
			'show'  => true,
		),
		array(
			'label' => 'Travelling',
			'icon'  => 'travel',
			'text'  => 'New places, mostly for the systems and details other people don\'t notice.',
			'show'  => true,
		),
		array(
			'label' => 'Music',
			'icon'  => 'guitar',
			'text'  => 'Guitar, mostly rock — another way of building something out of structure and repetition.',
			'show'  => true,
		),
		array(
			'label' => 'Drawing',
			'icon'  => 'drawing',
			'text'  => 'Where Industrial Design started for me — still just for the habit of it.',
			'show'  => true,
		),
		array(
			'label' => 'Taekwondo',
			'icon'  => 'taekwondo',
			'text'  => 'Years of practice; the same discipline and repetition show up in how I work.',
			'show'  => true,
		),
		array(
			'label' => 'Photography',
			'icon'  => '',
			'text'  => '',
			'show'  => true,
		),
		array(
			'label' => 'Coffee',
			'icon'  => 'coffee',
			'text'  => 'A daily ritual, not a productivity hack.',
			'show'  => true,
		),
	);
}

/**
 * Los items de "Hobbies & interests" ya pasados por el filtro
 * 'es_about_hobbies_items' (con los defaults de arriba) Y ya filtrados a
 * solo los visibles (show !== false) — la plantilla nunca necesita volver
 * a chequear el flag "show", solo recorrer el array.
 *
 * @return array<int,array{label:string,icon:string,text:string}>
 */
function es_about_hobbies_visible() {
	$items = apply_filters( 'es_about_hobbies_items', es_home_about_hobbies_defaults() );
	if ( ! is_array( $items ) ) {
		return array();
	}
	return array_values(
		array_filter(
			$items,
			function ( $item ) {
				return ! empty( $item['label'] ) && ( ! isset( $item['show'] ) || false !== $item['show'] );
			}
		)
	);
}

/**
 * Copy de intro por defecto de la página About — texto directo, personal,
 * no defensivo (reemplaza el copy anterior "not a past title... the
 * foundation this practice still runs on", reportado como demasiado
 * conceptual/defensivo en la ticket de corrección). 4 párrafos separados
 * por línea en blanco — ver es_about_render_intro_paragraphs() más abajo
 * para cómo esos saltos se convierten en <p> independientes en vez de
 * colapsar en un solo bloque de texto.
 *
 * @return string
 */
function es_about_intro_default() {
	return "Hi, I'm Ramiro Estavillo, a Product Designer and Industrial Designer based in Montevideo.\n\nI hold a Bachelor's Degree in Industrial Design, with a Product Design orientation, and I apply that foundation to digital products, services and operational systems.\n\nMy work usually begins by understanding how people, information and decisions move through a system — not only what happens on the screen. I combine research, systems thinking and practical implementation to turn complex processes into clearer, more usable solutions.\n\nI also use AI to accelerate research synthesis, documentation and exploration, while keeping design decisions grounded in human judgment and real-world context.";
}

/**
 * Divide el texto de intro de About en párrafos (separados por línea en
 * blanco en el valor guardado/default) — el <textarea> del admin guarda
 * saltos de línea reales (sanitize_textarea_field() los preserva), pero
 * imprimirlos dentro de un solo <p> los colapsa visualmente a un bloque
 * (whitespace collapsing de HTML). Esta función es la única responsable
 * de esa división, para no repetir la regex en el template-part.
 *
 * @param string $text Texto crudo (default o guardado vía es_home_about_text).
 * @return string[] Párrafos ya recortados, sin líneas vacías.
 */
function es_about_intro_paragraphs( $text ) {
	if ( '' === trim( (string) $text ) ) {
		return array();
	}
	$paragraphs = preg_split( '/\r\n\r\n|\n\n+|\r\r+/', trim( $text ) );
	return array_values( array_filter( array_map( 'trim', $paragraphs ) ) );
}

/**
 * Defaults de "Selected Experience" y "Previous Experience" (About page).
 *
 * Fuente: docs/about-page-authoritative-source.md — documento agregado
 * por el project owner como fuente de verdad explícita para toda la
 * historia profesional del About page, tras dos tickets previos donde
 * quedó claro que reconstruir esto desde el resto del repo era
 * insuficiente. Cada campo de cada entrada de abajo viene literal de ese
 * documento — no se resumió de memoria ni se sustituyó por copy genérico.
 *
 * 'contributions' es un array de líneas (una bullet por línea en el
 * admin — textarea, split por salto de línea) para la lista "Key
 * contributions" que va dentro de un <details> propio por entrada — ver
 * about-content.php. 'link_url'/'link_label' quedan vacíos en los 3 casos
 * de Selected Experience: el documento fuente dice "use el link existente
 * si ya existe" pero ningún Case Study está publicado todavía (sin acceso
 * a wp-admin en este entorno), así que no hay URL real que enlazar
 * todavía — no se inventa una.
 *
 * @return array<int,array{org:string,role:string,location:string,period:string,summary:string,contributions:string[],link_label:string,link_url:string}>
 */
function es_about_experience_selected_defaults() {
	return array(
		array(
			'org'           => 'Guzmán Villalba',
			'role'          => 'Lead Product Designer',
			'location'      => 'Uruguay',
			'period'        => '2025–Present',
			'summary'       => 'Designing and evolving an internal quoting and operational system for a custom metalwork workshop. The work focuses on turning tacit knowledge, fragmented information and owner-dependent decisions into a more consistent, traceable and usable process.',
			'contributions' => array(
				'Mapped the existing quoting workflow across WhatsApp, email, spreadsheets, Trello, physical documents and workshop conversations.',
				'Identified bottlenecks caused by undocumented criteria, scattered information and constant validation dependencies.',
				'Designed and iterated an internal quoting system using Google Sheets, Apps Script and AI-assisted workflows.',
				'Structured categories, price ranges, labor units, placement criteria and reusable estimating rules.',
				'Connected Product Design, Service Design and operational thinking in a physical-business environment.',
				'Improved the documentation of suppliers, materials, decisions and quoting criteria.',
				'Defined a progressive roadmap from a quick-estimate MVP toward a broader project and approval system.',
				'Documented the work as an evolving Product Design case study.',
			),
			'link_label'    => '',
			'link_url'      => '',
		),
		array(
			'org'           => 'Trazur',
			'role'          => 'Product Designer & UX Researcher',
			'location'      => 'Montevideo, Uruguay',
			'period'        => '2010–Present',
			'summary'       => 'Long-term work across the digital experience, online-course platform and communication ecosystem of an agricultural traceability and training business. The role evolved from web and content execution toward Product Design, UX Research and service improvement.',
			'contributions' => array(
				'Designed and maintained the company website in WordPress.',
				'Developed user flows and wireframes for the digital experience.',
				'Conducted usability testing and user interviews.',
				'Studied the needs and barriers of rural users in relation to technology.',
				'Supported the implementation and operation of online courses.',
				'Helped manage enrollment, student support and technical issues related to the courses.',
				'Created and maintained digital content across the website and social channels.',
				'Analyzed competitors and opportunities for improving the service.',
				'Worked on the redesign of the e-learning experience through UX Research.',
				'Used AI-assisted synthesis as part of the research process, with human review and validation.',
				'Explored how connectivity, trust and digital confidence affect adoption among rural users.',
				'Contributed to the final degree project focused on the redesign of an e-learning platform for livestock traceability (not sole authorship — a collaborative thesis project).',
			),
			'link_label'    => '',
			'link_url'      => '',
		),
		array(
			'org'           => 'Ceibal',
			'role'          => 'Project Manager',
			'location'      => 'Montevideo, Uruguay',
			'period'        => '2023–2024',
			'summary'       => "Led and coordinated digital projects for Ceibal's institutional portal and related initiatives, working with cross-functional teams, internal stakeholders and external vendors.",
			'contributions' => array(
				'Managed multiple digital projects, including Portal Ceibal, transparency initiatives and mailing systems.',
				'Developed and maintained project plans, timelines and roadmaps.',
				'Coordinated cross-functional teams in agile environments using Scrum and Kanban.',
				'Worked with internal stakeholders and external vendors.',
				'Prioritized high-volume tasks and competing project demands.',
				'Supported iterative development and continuous improvement.',
				'Conducted risk assessment and followed corrective actions.',
				'Communicated project progress with stakeholders.',
				'Helped improve workflows and operational coordination.',
			),
			'link_label'    => '',
			'link_url'      => '',
		),
	);
}

/**
 * Defaults de "Previous Experience" (About page) — mismo esquema que
 * Selected Experience, mismo origen (docs/about-page-authoritative-source.md).
 * "Master" y "FUMS" (que aparecían en una ticket anterior, superada) NO
 * están acá — el documento fuente dice explícitamente que no forman parte
 * de la historia real del About page.
 *
 * Samic SA incluye su "role progression" (3 fases fechadas) como las
 * primeras 3 líneas de 'contributions', en vez de un sub-esquema anidado
 * nuevo — mismo campo, mismo tratamiento visual, sin inventar UI nueva
 * para un caso único.
 *
 * @return array<int,array{org:string,role:string,location:string,period:string,summary:string,contributions:string[],link_label:string,link_url:string}>
 */
function es_about_experience_previous_defaults() {
	return array(
		array(
			'org'           => 'Verona Office & Home',
			'role'          => 'Sales Supervisor and Administrative Organizer',
			'location'      => 'Montevideo, Uruguay',
			'period'        => '2021–2022',
			'summary'       => 'Supervised the sales team and helped organize internal processes, identifying problems across sales, administration, marketing, logistics and warehouse operations.',
			'contributions' => array(
				'Identified pain points in sales and internal business processes.',
				'Conducted questionnaires to understand public awareness of the company and identify marketing gaps.',
				'Interviewed employees to understand internal problems and operational friction.',
				'Facilitated brainstorming sessions with the sales team.',
				'Proposed, prioritized and implemented process improvements.',
				'Led the sales team.',
				'Created guides, protocols and working methodologies.',
				'Planned marketing actions across social media, showroom and website.',
				'Studied competitors and market positioning.',
				'Implemented, taught and monitored a CRM for the sales team.',
				'Implemented, taught and monitored the administrative software.',
				'Identified and addressed logistics and warehouse problems.',
			),
			'link_label'    => '',
			'link_url'      => '',
		),
		array(
			'org'           => 'Samic SA',
			'role'          => 'Web & Graphic Designer / Store Manager',
			'location'      => 'Montevideo, Uruguay',
			'period'        => '2015–2021',
			'summary'       => 'A multidisciplinary role spanning graphic design, web design, e-commerce, content, communication and business operations. This experience became an important foundation for later Product Design work.',
			'contributions' => array(
				'2015–2016: Flooring estimator and graphic designer; identified the need for a dedicated design and communication function.',
				'2016–2021: Web and graphic designer.',
				'2018–2021: General manager, applying planning methods influenced by Design Thinking.',
				'Identified business needs related to sales, communication and customer acquisition.',
				'Recognized that many customers were far from the physical store and helped create new digital sales channels.',
				'Designed user flows, navigation architecture and e-commerce wireframes.',
				'Prototyped, implemented and maintained the e-commerce experience in WordPress and WooCommerce.',
				'Worked with HTML and CSS where necessary.',
				'Conducted usability testing with internal and external users.',
				'Iterated the experience across web, marketplace and social platforms.',
				'Managed product publishing and catalog content.',
				'Created graphic and digital content for platforms and social channels.',
				'Collaborated with the creative director on communication materials.',
				'Produced visual, video, GIF and motion content where required.',
				'Planned processes, guidelines and internal improvements across administration, sales and logistics.',
				'Used Mailchimp for newsletter campaigns.',
				'Contributed to a new digital sales channel, improved shopping experience and more organized operations.',
			),
			'link_label'    => '',
			'link_url'      => '',
		),
		array(
			'org'           => 'Fupsi.org',
			'role'          => 'Webmaster / Freelancer',
			'location'      => 'Montevideo, Uruguay',
			'period'        => '2012–2018',
			'summary'       => "Redesigned and maintained the organization's visual identity, WordPress website and newsletter communication.",
			'contributions' => array(
				'Redesigned the corporate identity.',
				'Redesigned and maintained the WordPress website.',
				'Sent newsletter campaigns with Mailchimp.',
				'Conducted usability tests and satisfaction surveys.',
				'Developed user flows and wireframes.',
				'Implemented and maintained the website.',
				'Improved the user experience across digital touchpoints over time.',
			),
			'link_label'    => '',
			'link_url'      => '',
		),
	);
}

/**
 * Defaults de "Education & certificates" (About page) — esquema ampliado
 * respecto de la versión anterior (title/org/year) para soportar todo lo
 * que pide docs/about-page-authoritative-source.md §5: orientación,
 * facultad/escuela, período de estudio distinto del año de emisión
 * oficial, descripción y proyecto final de grado. 'year' es el año de
 * EMISIÓN oficial del título (2026 — el documento fuente es explícito:
 * "Do not say the degree is pending issuance. The degree is completed and
 * officially issued."), no el año de inicio/fin de cursada (eso vive en
 * 'period', 2008–2025, dentro de 'description'). 'link' queda vacío para
 * las dos entradas: ningún link de credencial verificado existe en el
 * repo para ninguna de las dos.
 *
 * @return array<int,array{title:string,org:string,meta:string,year:string,description:string,final_project:string,link:string}>
 */
function es_about_education_defaults() {
	return array(
		array(
			'title'         => "Bachelor's Degree in Industrial Design — Product Design orientation",
			'org'           => 'Universidad de la República (UdelaR)',
			'meta'          => 'Facultad de Arquitectura, Diseño y Urbanismo (FADU) · Escuela Universitaria Centro de Diseño (EUCD), Uruguay',
			'year'          => '2026',
			'description'   => 'Product-oriented education focused on design methodology, systems thinking, manufacturing, ergonomics, research and user-centered design. Studied 2008–2025; degree officially completed and issued in 2026.',
			'final_project' => 'Final degree project: "Analysis of an E-learning Platform for Livestock Traceability through UX Research and Artificial Intelligence."',
			'link'          => '',
		),
		array(
			'title'         => 'Google UX Design Professional Certificate',
			'org'           => 'Google · Coursera',
			'meta'          => '',
			'year'          => '2026',
			'description'   => '',
			'final_project' => '',
			'link'          => '',
		),
	);
}

/**
 * Defaults de "Other Certifications" (About page) — grupo secundario
 * colapsado (ver about-content.php). Los 8 items vienen literales de
 * docs/about-page-authoritative-source.md §6. Ningún link de credencial
 * existe en el repo para ninguno — 'link' queda vacío en los 8, nunca
 * inventado. El ítem 6 (Figma for UX Design) no trae Credential ID en el
 * documento fuente — queda vacío, no se inventa uno.
 *
 * @return array<int,array{name:string,issuer:string,date:string,credential_id:string,link:string}>
 */
function es_about_certifications_other_defaults() {
	return array(
		array(
			'name'          => "User Experience: The Beginner's Guide",
			'issuer'        => 'Interaction Design Foundation',
			'date'          => 'August 2022',
			'credential_id' => '133455',
			'link'          => '',
		),
		array(
			'name'          => 'Design Thinking: The Ultimate Guide',
			'issuer'        => 'Interaction Design Foundation',
			'date'          => 'September 2022',
			'credential_id' => '133455',
			'link'          => '',
		),
		array(
			'name'          => 'Foundations of User Experience (UX) Design',
			'issuer'        => 'Google · Coursera',
			'date'          => 'June 2022',
			'credential_id' => 'UVWFHTTRTX',
			'link'          => '',
		),
		array(
			'name'          => 'Agile with Atlassian Jira',
			'issuer'        => 'Atlassian University · Coursera',
			'date'          => 'July 2022',
			'credential_id' => 'NFV95GJGQZ5E',
			'link'          => '',
		),
		array(
			'name'          => 'Introduction to UI Design',
			'issuer'        => 'University of Minnesota · Coursera',
			'date'          => 'August 2022',
			'credential_id' => '2T7RU7RZBXEH',
			'link'          => '',
		),
		array(
			'name'          => 'Figma for UX Design',
			'issuer'        => 'LinkedIn Learning',
			'date'          => 'June 2022',
			'credential_id' => '',
			'link'          => '',
		),
		array(
			'name'          => 'Introduction to C# Programming and Unity',
			'issuer'        => 'University of Colorado System · Coursera',
			'date'          => 'August 2022',
			'credential_id' => 'FP7749Y8DSBJ',
			'link'          => '',
		),
		array(
			'name'          => 'Introduction to Video Game Development with Unity',
			'issuer'        => 'Universitat Politècnica de València · edX',
			'date'          => 'July 2022',
			'credential_id' => '86c1923e49464142ac08e9fdbce62ce2',
			'link'          => '',
		),
	);
}

/**
 * Defaults de "Languages" (About page) — confirmados por el project owner
 * vía docs/about-page-authoritative-source.md §7 ("Advanced (C1)" es más
 * específico que el "Advanced" plano de la ticket anterior — el documento
 * fuente gana).
 *
 * @return array<int,array{name:string,level:string}>
 */
function es_about_languages_defaults() {
	return array(
		array(
			'name'  => 'Spanish',
			'level' => 'Native',
		),
		array(
			'name'  => 'English',
			'level' => 'Advanced (C1)',
		),
		array(
			'name'  => 'Portuguese',
			'level' => 'Basic',
		),
	);
}

/**
 * Registra los strings del tema en Polylang (si está activo).
 */
function es_child_register_pll_strings() {
	if ( ! function_exists( 'pll_register_string' ) ) {
		return;
	}
	foreach ( es_child_ui_strings() as $key => $text ) {
		pll_register_string( 'es_' . $key, $text, 'Estavillo Child' );
	}
}
add_action( 'init', 'es_child_register_pll_strings' );

/**
 * Devuelve un string de interfaz, traducido por Polylang si está disponible.
 *
 * @param string $key Clave dentro de es_child_ui_strings().
 * @return string
 */
function es__( $key ) {
	$strings = es_child_ui_strings();
	$text    = isset( $strings[ $key ] ) ? $strings[ $key ] : $key;
	if ( function_exists( 'pll__' ) ) {
		return pll__( $text );
	}
	return $text;
}
