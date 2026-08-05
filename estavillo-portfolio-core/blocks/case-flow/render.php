<?php
/**
 * Render dinámico de estavillo/case-flow.
 *
 * Un DIAGRAMA DE FLUJO de verdad, no una lista de pasos maquillada: el dato
 * es un GRAFO (nodos + aristas). Cada nodo declara a qué otros nodos sale
 * (`edges`), así que una decisión puede tener dos salidas reales, una rama
 * puede pasar por una pantalla propia y volver al camino principal, y una
 * rama puede volver hacia atrás (bucle). El layout — en qué fila/columna
 * cae cada nodo y qué arista lo une con el anterior — lo CALCULA este archivo
 * a partir del grafo. El JS mide después las formas visibles y dibuja la
 * geometría final de cada conector; no hay ni un nth-child ni coordenadas
 * atadas a este diagrama en particular, así que el mismo componente sirve
 * para Trazur, para el Presupuestador o para cualquier flujo futuro.
 *
 * MODELO DE DATOS
 *   nodes[]: { id, kind, accent, num, title, text, ai, detail[], edges[] }
 *   edges[]: { to: <id de otro nodo>, label: "Sí" | "No" | "" }
 *
 *   - La PRIMERA arista de cada nodo es el camino principal ("la línea").
 *   - Las aristas siguientes son ramas: si su destino no está en el camino
 *     principal, ese destino es una pantalla intermedia que se dibuja en su
 *     propio carril y vuelve a entrar al flujo.
 *   - Una arista hacia un nodo anterior es un bucle (return path).
 *   - Si NINGÚN nodo declara `edges`, se encadena todo en línea recta: los
 *     flujos lineales que ya existían siguen funcionando sin tocar el dato.
 *
 * LAYOUT (desktop, calculado)
 *   El camino principal se reparte en bandas de 4 columnas en serpentina
 *   (fila 1 →, fila 2 ←, fila 3 →). Cada banda que tenga ramas recibe ADEMÁS
 *   un carril propio encima de su fila, donde caen las pantallas intermedias,
 *   en la columna vecina a la del nodo con el que se conectan — así el
 *   conector principal baja recto por el centro de su columna y la rama
 *   entra y sale por el costado, sin cruzarse con nada.
 *
 * PROGRESSIVE ENHANCEMENT: el panel de detalle se emite SIEMPRE en el HTML,
 * visible por CSS por defecto. El JS del theme (assets/js/case-flow.js) marca
 * el contenedor con .is-enhanced y recién ahí los paneles pasan a ser
 * popovers/acordeones. Sin JS el flujo se lee completo, en orden, con todo el
 * contenido — nada esencial depende del script.
 *
 * Semántica: <ol> — un flujo ES una secuencia ordenada. El orden del DOM es
 * SIEMPRE el orden narrativo real (cada pantalla intermedia va inmediatamente
 * después de la decisión que la abre), aunque el desktop lo reacomode en
 * serpentina y carriles vía CSS Grid: lectores de pantalla y la Vista de lista
 * de Gutenberg ven la secuencia verdadera.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    Sin uso (bloque hoja).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Acentos permitidos. Mapean 1:1 a tokens que YA existen en el theme
 * (tokens.css): 'signal' → --es-signal (verde, el sistema/camino
 * resuelto), 'decision' → --es-decision (naranja, el punto de juicio
 * humano), 'muted' → tinta apagada. No hay colores nuevos.
 */
if ( ! function_exists( 'es_flow_accent_class' ) ) {
	function es_flow_accent_class( $accent ) {
		$accent = sanitize_key( (string) $accent );
		return in_array( $accent, array( 'signal', 'decision', 'muted' ), true ) ? ' is-accent-' . $accent : '';
	}
}

/**
 * Tipos de nodo permitidos. 'step' es el default seguro para cualquier
 * valor desconocido (un dato viejo nunca rompe el render).
 */
if ( ! function_exists( 'es_flow_node_kind' ) ) {
	function es_flow_node_kind( $kind ) {
		$kind = sanitize_key( (string) $kind );
		return in_array( $kind, array( 'start', 'step', 'decision', 'milestone', 'end' ), true ) ? $kind : 'step';
	}
}

/**
 * Fallback sin JS entre dos nodos consecutivos del camino principal. Emite UN
 * svg con dos paths — uno horizontal y uno vertical — y el CSS muestra el que
 * corresponde al layout activo. Cuando el JS logra medir las formas, reemplaza
 * estos rieles por el overlay SVG anclado a su geometría real.
 *
 * `vector-effect="non-scaling-stroke"` mantiene el grosor real en 1.25px
 * aunque el svg se estire con preserveAspectRatio="none".
 * Sin <marker>/<defs>: los IDs de marker colisionarían si hay más de un
 * flujo en la misma página — la punta de flecha es un polyline común.
 */
if ( ! function_exists( 'es_flow_connector_svg' ) ) {
	function es_flow_connector_svg() {
		return '<svg class="es-flow__connector" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true" focusable="false">'
			. '<path class="es-flow__line es-flow__line--h" d="M0 50 H96" vector-effect="non-scaling-stroke" />'
			. '<polyline class="es-flow__head es-flow__head--h" points="91,44 98,50 91,56" vector-effect="non-scaling-stroke" />'
			. '<path class="es-flow__line es-flow__line--v" d="M50 0 V96" vector-effect="non-scaling-stroke" />'
			. '<polyline class="es-flow__head es-flow__head--v" points="44,91 50,98 56,91" vector-effect="non-scaling-stroke" />'
			. '</svg>';
	}
}

/**
 * Punta de flecha suelta, tamaño fijo, para el fallback CSS de las ramas.
 *
 * Las ramas se dibujan como una "L" hecha con dos bordes CSS (ver
 * .es-flow__branch-link en case-flow.css) en vez de un svg estirado: un
 * borde recto no se deforma nunca, sea cual sea el ancho de la columna,
 * mientras que un svg con preserveAspectRatio="none" sí deformaría el
 * triángulo de la punta. Por eso la punta va aparte, en su propio svg de
 * tamaño fijo, apoyada en el extremo de la L.
 *
 * @param string $dir up | down | left | right.
 */
if ( ! function_exists( 'es_flow_tip_svg' ) ) {
	function es_flow_tip_svg( $dir ) {
		$dir = in_array( $dir, array( 'up', 'down', 'left', 'right' ), true ) ? $dir : 'down';
		return '<svg class="es-flow__tip es-flow__tip--' . $dir . '" viewBox="0 0 12 12" aria-hidden="true" focusable="false">'
			. '<polyline points="2,5 6,9 10,5" vector-effect="non-scaling-stroke" />'
			. '</svg>';
	}
}

/**
 * Traduce el grafo (nodos + aristas) a una grilla concreta: a cada nodo le
 * asigna banda / columna / carril y a cada arista, el conector que le
 * corresponde. Es TODA la inteligencia del diagrama, y es genérica: no sabe
 * nada de Trazur ni de ningún flujo puntual.
 *
 * Devuelve array(
 *   'items' => nodos en orden narrativo, cada uno con su placement y sus
 *              conectores entrantes ya resueltos,
 *   'rows'  => cuántas filas de grilla ocupa el desktop,
 *   'cols'  => cuántas columnas por banda,
 * ).
 *
 * @param array $nodes Nodos ya filtrados (con title no vacío).
 * @param int   $cols  Columnas por banda en desktop.
 */
if ( ! function_exists( 'es_flow_layout' ) ) {
	function es_flow_layout( $nodes, $cols = 4 ) {
		$total = count( $nodes );

		// ---- 1. ids estables -------------------------------------------------
		// El dato puede no traer id (contenido viejo, o un flujo lineal donde no
		// hace falta nombrar nada): se sintetiza uno y el resto del algoritmo
		// trabaja siempre con ids, nunca con índices.
		$index_of = array();
		foreach ( $nodes as $i => $node ) {
			$id = trim( (string) ( $node['id'] ?? '' ) );
			if ( '' === $id || isset( $index_of[ $id ] ) ) {
				$id = 'n' . ( $i + 1 );
			}
			$nodes[ $i ]['_id']    = $id;
			$index_of[ $id ]       = $i;
		}

		// ---- 2. aristas ------------------------------------------------------
		// Si NADIE declara edges, el flujo es lineal: se encadena en el orden en
		// que vienen los nodos. Es exactamente el comportamiento anterior, así
		// que cualquier instancia ya publicada sigue renderizando igual.
		$declared = false;
		foreach ( $nodes as $node ) {
			if ( ! empty( $node['edges'] ) && is_array( $node['edges'] ) ) {
				$declared = true;
				break;
			}
		}

		foreach ( $nodes as $i => $node ) {
			$edges = array();
			if ( $declared ) {
				foreach ( (array) ( $node['edges'] ?? array() ) as $edge ) {
					if ( ! is_array( $edge ) ) {
						continue;
					}
					$to = trim( (string) ( $edge['to'] ?? '' ) );
					// Una arista a un id inexistente se descarta en silencio: un
					// nodo borrado en el editor no puede romper el render.
					if ( '' !== $to && isset( $index_of[ $to ] ) && $index_of[ $to ] !== $i ) {
						$edges[] = array(
							'to'    => $to,
							'label' => trim( (string) ( $edge['label'] ?? '' ) ),
						);
					}
				}
			} elseif ( $i + 1 < $total ) {
				$edges[] = array(
					'to'    => $nodes[ $i + 1 ]['_id'],
					'label' => '',
				);
			}
			$nodes[ $i ]['_edges'] = $edges;
		}

		// ---- 3. camino principal --------------------------------------------
		// "La línea": se sigue SIEMPRE la primera arista de cada nodo. Es una
		// convención simple y predecible para quien edita — la salida que
		// escribís primero es la que sigue derecho.
		$main    = array();
		$on_main = array();
		$cursor  = 0;
		while ( isset( $nodes[ $cursor ] ) && ! isset( $on_main[ $cursor ] ) ) {
			$on_main[ $cursor ] = count( $main );
			$main[]             = $cursor;
			$edges              = $nodes[ $cursor ]['_edges'];
			if ( empty( $edges ) ) {
				break;
			}
			$cursor = $index_of[ $edges[0]['to'] ];
		}

		// ---- 4. placement del camino principal (serpentina) ------------------
		foreach ( $main as $pos => $i ) {
			$band = intdiv( $pos, $cols );
			$idx  = $pos % $cols;
			$nodes[ $i ]['_band'] = $band;
			// Las bandas impares se leen al revés (derecha → izquierda): así el
			// final de una fila queda justo encima del principio de la siguiente
			// y el salto de banda es una caída vertical recta.
			$nodes[ $i ]['_col']  = ( 0 === $band % 2 ) ? $idx : ( $cols - 1 - $idx );
			$nodes[ $i ]['_lane'] = 'main';
		}

		// ---- 5. placement de las pantallas intermedias (ramas) ---------------
		// Un nodo fuera del camino principal es una pantalla por la que se pasa
		// SÓLO si la decisión sale por esa rama. Va en un carril propio, en la
		// columna VECINA a la del nodo con el que se conecta, para no pisar el
		// conector principal que baja por el centro de esa columna.
		$branch_bands = array();
		foreach ( $nodes as $i => $node ) {
			if ( isset( $on_main[ $i ] ) ) {
				continue;
			}

			// De quién sale esta pantalla (la decisión) y a dónde vuelve.
			$from = null;
			foreach ( $nodes as $j => $other ) {
				foreach ( $other['_edges'] as $edge ) {
					if ( $index_of[ $edge['to'] ] === $i && isset( $on_main[ $j ] ) ) {
						$from = $j;
						break 2;
					}
				}
			}
			$back = null;
			if ( ! empty( $node['_edges'] ) ) {
				$candidate = $index_of[ $node['_edges'][0]['to'] ];
				if ( isset( $on_main[ $candidate ] ) ) {
					$back = $candidate;
				}
			}

			if ( null === $from ) {
				// Nodo suelto (nadie lo apunta): se trata como un paso más del
				// camino principal para no perder contenido nunca.
				$pos                  = count( $main );
				$band                 = intdiv( $pos, $cols );
				$idx                  = $pos % $cols;
				$on_main[ $i ]        = $pos;
				$main[]               = $i;
				$nodes[ $i ]['_band'] = $band;
				$nodes[ $i ]['_col']  = ( 0 === $band % 2 ) ? $idx : ( $cols - 1 - $idx );
				$nodes[ $i ]['_lane'] = 'main';
				continue;
			}

			// El carril vive siempre entre las dos filas que conecta, o sea en
			// la banda de abajo de las dos. Si vuelve hacia atrás (bucle), la
			// de abajo es la de la decisión; si sigue hacia adelante, la del
			// nodo al que reingresa.
			$anchor = ( null !== $back && $on_main[ $back ] < $on_main[ $from ] ) ? $from : ( null !== $back ? $back : $from );
			$band   = $nodes[ $anchor ]['_band'];
			$col    = $nodes[ $anchor ]['_col'];

			$nodes[ $i ]['_band']   = $band;
			// Vecina hacia adentro de la banda: nunca se sale de la grilla.
			$nodes[ $i ]['_col']    = ( $col > 0 ) ? ( $col - 1 ) : ( $col + 1 );
			$nodes[ $i ]['_lane']   = 'branch';
			// De qué lado del carril queda la columna principal — define por
			// dónde entra y sale el conector de la rama.
			$nodes[ $i ]['_side']   = ( $nodes[ $i ]['_col'] < $col ) ? 'right' : 'left';
			$nodes[ $i ]['_from']   = $from;
			$nodes[ $i ]['_back']   = $back;
			$nodes[ $i ]['_isloop'] = ( null !== $back && $on_main[ $back ] < $on_main[ $from ] );

			$branch_bands[ $band ] = true;
		}

		// ---- 5b. rasgos de banda que el CSS no puede deducir solo ------------
		// Dos cosas dependen de la banda entera, no del nodo: (a) si la banda
		// se lee al revés — el conector tiene que entrar por el otro lado y la
		// flecha apuntar a la inversa —, y (b) si la banda contiene un rombo,
		// porque entonces TODA la banda comparte la franja alta para que
		// pastilla, rectángulo y rombo caigan en el mismo eje. Las dos salen
		// del grafo, así que las resuelve el PHP y el CSS sólo las consume.
		$band_has_decision = array();
		foreach ( $nodes as $node ) {
			if ( isset( $node['_band'] ) && 'main' === $node['_lane'] && 'decision' === es_flow_node_kind( $node['kind'] ?? 'step' ) ) {
				$band_has_decision[ $node['_band'] ] = true;
			}
		}
		foreach ( $nodes as $i => $node ) {
			if ( ! isset( $node['_band'] ) ) {
				continue;
			}
			$nodes[ $i ]['_rtl']  = ( 'main' === $node['_lane'] && 1 === $node['_band'] % 2 );
			$nodes[ $i ]['_tall'] = ( 'main' === $node['_lane'] && isset( $band_has_decision[ $node['_band'] ] ) );
		}

		// ---- 6. filas reales de la grilla ------------------------------------
		// Sólo las bandas que TIENEN ramas gastan una fila de carril: un flujo
		// lineal sigue ocupando exactamente las mismas filas que antes.
		$max_band   = 0;
		foreach ( $nodes as $node ) {
			if ( isset( $node['_band'] ) ) {
				$max_band = max( $max_band, (int) $node['_band'] );
			}
		}
		$row_of_band   = array();
		$branch_row_of = array();
		$row           = 1;
		for ( $b = 0; $b <= $max_band; $b++ ) {
			if ( isset( $branch_bands[ $b ] ) ) {
				$branch_row_of[ $b ] = $row++;
			}
			$row_of_band[ $b ] = $row++;
		}
		$rows = $row - 1;

		foreach ( $nodes as $i => $node ) {
			if ( ! isset( $node['_band'] ) ) {
				continue;
			}
			$nodes[ $i ]['_row'] = ( 'branch' === $node['_lane'] )
				? $branch_row_of[ $node['_band'] ]
				: $row_of_band[ $node['_band'] ];
		}

		// ---- 7. conectores ---------------------------------------------------
		// Cada arista recibe extremos y tipo de ruta estables. El descriptor se
		// hospeda en uno de los nodos para sostener el fallback CSS; el overlay JS
		// lee from/to/route y mide directamente las dos formas visibles.
		foreach ( $nodes as $i => $node ) {
			$nodes[ $i ]['_links'] = array();
		}
		$passes = array();

		foreach ( $nodes as $i => $node ) {
			foreach ( $node['_edges'] as $edge ) {
				$j       = $index_of[ $edge['to'] ];
				$a_main  = isset( $on_main[ $i ] );
				$b_main  = isset( $on_main[ $j ] );
				$label   = $edge['label'];

				if ( $a_main && $b_main ) {
					// Tramo del camino principal: recto. Horizontal si los dos
					// están en la misma banda, vertical si es un salto de banda.
					$same_band = ( $nodes[ $i ]['_band'] === $nodes[ $j ]['_band'] );
					// El rombo mide 190px fijos pero su columna de grilla es más
					// ancha (se centra con un margen a cada lado, ver .es-flow
					// __item--decision .es-flow__shape-band en el CSS): un riel
					// horizontal del ancho normal del gap se queda corto contra
					// ESE margen, entre a un rombo o salga de uno. 'decisionEdge'
					// avisa al CSS que este tramo toca un rombo en cualquiera de
					// sus dos puntas, para que el riel se estire lo que haga
					// falta — el mismo cálculo sirve para los dos casos porque el
					// riel siempre queda anclado contra el destino y crece hacia
					// atrás, sea cual sea el extremo angosto.
					$decision_edge = ( 'decision' === es_flow_node_kind( $node['kind'] ?? 'step' ) )
						|| ( 'decision' === es_flow_node_kind( $nodes[ $j ]['kind'] ?? 'step' ) );
					$nodes[ $j ]['_links'][] = array(
						'type'  => $same_band ? 'h' : 'v',
						'label' => $label,
						'wide'  => $same_band && $decision_edge,
						'from'  => $nodes[ $i ]['_id'],
						'to'    => $nodes[ $j ]['_id'],
						'route' => 'main',
					);
					// Si el salto de banda atraviesa un carril de ramas, hay que
					// prolongar la línea a través de esa fila (si no, el conector
					// llegaría sólo hasta el borde del carril y quedaría cortado).
					if ( ! $same_band && isset( $branch_row_of[ $nodes[ $j ]['_band'] ] ) ) {
						$passes[] = array(
							'row' => $branch_row_of[ $nodes[ $j ]['_band'] ],
							'col' => $nodes[ $j ]['_col'],
						);
					}
				} elseif ( ! $b_main ) {
					// Entrada a una pantalla intermedia. Se guarda la fila del
					// nodo del que sale: el carril puede quedar ENTRE las dos
					// filas que conecta (el caso habitual) o encima de las dos
					// (cuando decisión y reingreso caen en la misma banda), y
					// el conector tiene que salir por un lado distinto en cada
					// caso para no cruzar el texto del propio nodo.
					$nodes[ $j ]['_links'][] = array(
						'type'  => 'in',
						'label' => $label,
						'row'   => $nodes[ $i ]['_row'],
						'from'  => $nodes[ $i ]['_id'],
						'to'    => $nodes[ $j ]['_id'],
						'route' => 'branch',
					);
				} else {
					// Salida de una pantalla intermedia de vuelta al flujo.
					$nodes[ $i ]['_links'][] = array(
						'type'  => 'out',
						'label' => $label,
						'row'   => $nodes[ $j ]['_row'],
						'from'  => $nodes[ $i ]['_id'],
						'to'    => $nodes[ $j ]['_id'],
						'route' => ! empty( $nodes[ $i ]['_isloop'] ) ? 'loop' : 'rejoin',
					);
				}
			}
		}

		// ---- 8. orden narrativo del DOM --------------------------------------
		// Camino principal en orden, y cada pantalla intermedia inmediatamente
		// después de la decisión que la abre: es como se lee en voz alta, y es
		// el orden que ven lectores de pantalla, la Vista de lista y el mobile.
		$items    = array();
		$attached = array();
		foreach ( $main as $i ) {
			$items[] = $nodes[ $i ];
			foreach ( $nodes as $k => $other ) {
				if ( ! isset( $on_main[ $k ] ) && isset( $other['_from'] ) && $other['_from'] === $i && ! isset( $attached[ $k ] ) ) {
					$attached[ $k ] = true;
					$items[]        = $nodes[ $k ];
				}
			}
		}
		foreach ( $nodes as $k => $other ) {
			if ( ! isset( $on_main[ $k ] ) && ! isset( $attached[ $k ] ) ) {
				$items[] = $nodes[ $k ];
			}
		}

		return array(
			'items'  => $items,
			'rows'   => $rows,
			'cols'   => $cols,
			'passes' => $passes,
		);
	}
}

$es_nodes = isset( $attributes['nodes'] ) && is_array( $attributes['nodes'] ) ? $attributes['nodes'] : array();
$es_nodes = array_values(
	array_filter(
		$es_nodes,
		function ( $es_node ) {
			return is_array( $es_node ) && '' !== trim( (string) ( $es_node['title'] ?? '' ) );
		}
	)
);

if ( empty( $es_nodes ) ) {
	return;
}

$es_section_label = trim( (string) ( $attributes['sectionLabel'] ?? '' ) );
$es_start   = trim( (string) ( $attributes['startLabel'] ?? '' ) );
$es_end     = trim( (string) ( $attributes['endLabel'] ?? '' ) );
$es_detail  = trim( (string) ( $attributes['detailLabel'] ?? '' ) );
$es_close   = trim( (string) ( $attributes['closeLabel'] ?? '' ) );
$es_step_lb = trim( (string) ( $attributes['stepLabel'] ?? '' ) );
$es_ai_legend = trim( (string) ( $attributes['aiLegend'] ?? '' ) );
$es_density = sanitize_key( (string) ( $attributes['density'] ?? 'comfortable' ) );
$es_density = in_array( $es_density, array( 'comfortable', 'compact' ), true ) ? $es_density : 'comfortable';

$es_layout = es_flow_layout( $es_nodes );
$es_items  = $es_layout['items'];

// Primer y último nodo del camino principal: son los que hospedan los
// marcadores de contexto de inicio y de fin.
$es_first_main = null;
$es_last_main  = null;
foreach ( $es_items as $es_k => $es_it ) {
	if ( 'main' === ( $es_it['_lane'] ?? 'main' ) ) {
		if ( null === $es_first_main ) {
			$es_first_main = $es_k;
		}
		$es_last_main = $es_k;
	}
}

// ID base único por instancia: permite varios flujos en una misma página
// sin colisión de aria-controls.
$es_uid   = wp_unique_id( 'es-flow-' );
$es_total = count( $es_items );

$es_classes = 'es-flow es-flow--' . $es_density;
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $es_classes ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?> data-es-flow style="--es-flow-rows: <?php echo esc_attr( (string) $es_layout['rows'] ); ?>; --es-flow-cols: <?php echo esc_attr( (string) $es_layout['cols'] ); ?>">

	<?php // Título de sección opcional ("FLUJO DE USUARIO IDEAL"…) — mismo lenguaje visual que el resto de eyebrows del portfolio (.es-eyebrow: mono, uppercase, acento). Vacío = no se imprime nada, ni siquiera el <p>. ?>
	<?php if ( '' !== $es_section_label ) : ?>
		<p class="es-eyebrow es-flow__eyebrow"><?php echo esc_html( $es_section_label ); ?></p>
	<?php endif; ?>

	<?php // Indicador de progreso: sólo se muestra en mobile (CSS). Lo llena el JS; sin JS queda el total, que ya es información útil. ?>
	<p class="es-flow__progress" data-es-flow-progress aria-hidden="true">
		<span class="es-flow__progress-current" data-es-flow-current>01</span><span class="es-flow__progress-sep">/</span><span class="es-flow__progress-total"><?php echo esc_html( sprintf( '%02d', $es_total ) ); ?></span>
	</p>

	<div class="es-flow__diagram" data-es-flow-diagram>
		<svg class="es-flow__connections" data-es-flow-connectors aria-hidden="true" focusable="false"></svg>
		<div class="es-flow__connector-labels" data-es-flow-connector-labels aria-hidden="true"></div>

		<ol class="es-flow__track">
		<?php
		// Prolongaciones del conector principal a través de un carril de ramas.
		// Son items de grilla vacíos (puro trazo), no nodos: por eso van fuera
		// del <li> y marcados aria-hidden.
		foreach ( $es_layout['passes'] as $es_pass ) :
			?>
			<li class="es-flow__pass" aria-hidden="true" style="--r: <?php echo esc_attr( (string) $es_pass['row'] ); ?>; --c: <?php echo esc_attr( (string) ( $es_pass['col'] + 1 ) ); ?>"></li>
		<?php endforeach; ?>

		<?php foreach ( $es_items as $es_i => $es_node ) : ?>
			<?php
			$es_kind     = es_flow_node_kind( $es_node['kind'] ?? 'step' );
			$es_accent   = es_flow_accent_class( $es_node['accent'] ?? '' );
			$es_num      = trim( (string) ( $es_node['num'] ?? '' ) );
			$es_title    = trim( (string) ( $es_node['title'] ?? '' ) );
			$es_text     = trim( (string) ( $es_node['text'] ?? '' ) );
			$es_rows     = isset( $es_node['detail'] ) && is_array( $es_node['detail'] ) ? $es_node['detail'] : array();
			$es_lane     = $es_node['_lane'] ?? 'main';
			$es_side     = $es_node['_side'] ?? 'right';
			$es_links    = $es_node['_links'] ?? array();

			$es_rows = array_values(
				array_filter(
					$es_rows,
					function ( $es_row ) {
						return is_array( $es_row ) && '' !== trim( (string) ( $es_row['text'] ?? '' ) );
					}
				)
			);

			$es_panel_id  = $es_uid . '-panel-' . $es_i;
			$es_has_panel = ! empty( $es_rows );

			$es_item_class  = 'es-flow__item es-flow__item--' . $es_kind . $es_accent;
			$es_item_class .= ( 'branch' === $es_lane ) ? ' es-flow__item--branch is-side-' . $es_side : '';
			$es_item_class .= ! empty( $es_node['_isloop'] ) ? ' is-loop' : '';
			$es_item_class .= ! empty( $es_node['_rtl'] ) ? ' is-rtl' : '';
			$es_item_class .= ! empty( $es_node['_tall'] ) ? ' is-tall' : '';
			?>
			<li class="<?php echo esc_attr( $es_item_class ); ?>" data-es-flow-item data-es-flow-node-id="<?php echo esc_attr( (string) $es_node['_id'] ); ?>" data-es-flow-node-kind="<?php echo esc_attr( $es_kind ); ?>" data-es-flow-index="<?php echo esc_attr( (string) ( $es_i + 1 ) ); ?>" style="--r: <?php echo esc_attr( (string) ( $es_node['_row'] ?? 1 ) ); ?>; --c: <?php echo esc_attr( (string) ( ( $es_node['_col'] ?? 0 ) + 1 ) ); ?>">

				<?php
				/*
				 * Los marcadores de contexto ("Catálogo de cursos" / "Certificado
				 * descargado") viven DENTRO del nodo que describen, no sueltos
				 * arriba y abajo del diagrama. Así su posición sale siempre de la
				 * pastilla real: en la narrativa vertical caen justo antes/después
				 * en el flujo normal, y en el grid se anclan a la propia forma con
				 * variables (ver case-flow.css), sin un desplazamiento a ojo que
				 * haya que reajustar cada vez que cambia el alto de una fila.
				 */
				?>
				<?php if ( '' !== $es_start && $es_i === $es_first_main ) : ?>
					<p class="es-flow__marker es-flow__marker--start"><span><?php echo esc_html( $es_start ); ?></span></p>
				<?php endif; ?>

				<div class="es-flow__node">
					<?php
					// El trigger es un <button> real sólo si hay detalle que
					// revelar. Si un nodo no tiene filas de detalle, el mismo
					// contenido se emite en un <div> — no se ofrece un control
					// interactivo que no haga nada (regla de a11y, no adorno).
					$es_tag   = $es_has_panel ? 'button' : 'div';
					$es_attrs = $es_has_panel
						? ' type="button" class="es-flow__trigger" aria-expanded="false" aria-controls="' . esc_attr( $es_panel_id ) . '" data-es-flow-trigger'
						: ' class="es-flow__trigger es-flow__trigger--static"';
					?>
					<<?php echo $es_tag . $es_attrs; // phpcs:ignore WordPress.Security.EscapeOutput -- tag de whitelist + atributos ya escapados. ?>>
						<?php
						// LA FORMA ES LA CAJA. Igual que en un diagrama de flujo
						// clásico: pastilla (inicio/fin), rectángulo (paso) o
						// rombo (decisión) con el título ADENTRO; la descripción
						// va afuera, debajo, en tinta apagada.
						?>
						<span class="es-flow__shape-band">
							<?php
							foreach ( $es_links as $es_link ) :
								$es_edge = trim( (string) ( $es_node['edgeLabel'] ?? '' ) );
								$es_link_from  = (string) ( $es_link['from'] ?? '' );
								$es_link_to    = (string) ( $es_link['to'] ?? '' );
								$es_link_route = (string) ( $es_link['route'] ?? 'main' );
								$es_link_label = (string) ( $es_link['label'] ?? '' );
								if ( 'h' === $es_link['type'] || 'v' === $es_link['type'] ) :
									/*
									 * Fallback del camino principal. El riel cuelga de
									 * .es-flow__shape-band, la franja de altura compartida
									 * por fila: así top:50% del riel apunta siempre al
									 * centro REAL de la fila — el mismo eje en el que la
									 * forma (pastilla, rectángulo o rombo) ya está
									 * centrada — en vez de adivinar el centro de CADA
									 * forma, que es lo que rompía la alineación cuando el
									 * rombo compartía fila con una pastilla mucho más baja.
									 */
									?>
									<span class="es-flow__rail es-flow__rail--<?php echo esc_attr( $es_link['type'] ); ?><?php echo ! empty( $es_link['wide'] ) ? ' is-decision-edge' : ''; ?>" data-es-flow-edge data-es-flow-from="<?php echo esc_attr( $es_link_from ); ?>" data-es-flow-to="<?php echo esc_attr( $es_link_to ); ?>" data-es-flow-route="<?php echo esc_attr( $es_link_route ); ?>" data-es-flow-label="<?php echo esc_attr( $es_link_label ); ?>" aria-hidden="true">
										<?php echo es_flow_connector_svg(); // phpcs:ignore WordPress.Security.EscapeOutput -- SVG del propio plugin, sin dato de usuario. ?>
									</span>
									<?php if ( '' !== $es_link['label'] ) : ?>
										<span class="es-flow__edge-label"><?php echo esc_html( $es_link['label'] ); ?></span>
									<?php endif; ?>
									<?php if ( '' !== $es_edge ) : ?>
										<span class="es-flow__edge"><?php echo esc_html( $es_edge ); ?></span>
									<?php endif; ?>
									<?php
								else :
									/*
									 * Conector de rama: una "L" de dos bordes CSS (nunca un
									 * svg estirado, que deformaría la punta) más una punta
									 * de flecha suelta de tamaño fijo. 'in' entra a la
									 * pantalla intermedia desde la decisión; 'out' la
									 * devuelve al flujo.
									 */
									/*
									 * La geometría sale de la RELACIÓN DE FILAS, no de si
									 * el nodo es un bucle: lo único que importa es si el
									 * otro extremo está arriba o abajo de esta tarjeta.
									 *   viene de arriba → entra por el TECHO
									 *   viene de abajo  → entra por el COSTADO
									 *   va hacia abajo  → sale por el COSTADO
									 *   va hacia arriba → sale por el TECHO
									 * Debajo de la forma vive su propia descripción, así
									 * que ningún conector puede pasar por ahí — de ahí que
									 * "desde abajo" nunca entre por la base.
									 */
									$es_vert = ( (int) $es_link['row'] < (int) $es_node['_row'] ) ? 'above' : 'below';
									$es_where = ( 'in' === $es_link['type'] ? 'is-from-' : 'is-to-' ) . $es_vert;
									$es_tip = ( 'in' === $es_link['type'] )
										? ( 'above' === $es_vert ? 'down' : 'right' )
										: ( 'above' === $es_vert ? 'up' : 'down' );
									?>
									<?php
									// Sin aria-hidden en el contenedor: el trazo es
									// decorativo (la punta ya se marca sola), pero el
									// rótulo "Sí"/"No" que va adentro es contenido real —
									// es lo que explica por qué existe esta pantalla.
									?>
									<span class="es-flow__branch-link es-flow__branch-link--<?php echo esc_attr( $es_link['type'] ); ?> <?php echo esc_attr( $es_where ); ?>" data-es-flow-edge data-es-flow-from="<?php echo esc_attr( $es_link_from ); ?>" data-es-flow-to="<?php echo esc_attr( $es_link_to ); ?>" data-es-flow-route="<?php echo esc_attr( $es_link_route ); ?>" data-es-flow-label="<?php echo esc_attr( $es_link_label ); ?>">
										<?php echo es_flow_tip_svg( $es_tip ); // phpcs:ignore WordPress.Security.EscapeOutput -- SVG del propio plugin, sin dato de usuario. ?>
										<?php if ( '' !== $es_link['label'] ) : ?>
											<?php
											// El rótulo va ADENTRO del conector, no al lado: así
											// se posiciona contra la "L" misma y cae siempre en
											// el extremo que nace del rombo, cualquiera sea el
											// ancho de la columna.
											?>
											<span class="es-flow__edge-label es-flow__edge-label--<?php echo esc_attr( $es_link['type'] ); ?>"><?php echo esc_html( $es_link['label'] ); ?></span>
										<?php endif; ?>
									</span>
									<?php
								endif;
							endforeach;
							?>
							<span class="es-flow__shape" data-es-flow-shape>
								<span class="es-flow__shape-inner">
									<?php if ( '' !== $es_num ) : ?>
										<span class="es-flow__num">
											<?php if ( '' !== $es_step_lb ) : ?>
												<span class="es-visually-hidden"><?php echo esc_html( $es_step_lb ); ?> </span>
											<?php endif; ?>
											<?php echo esc_html( $es_num ); ?>
										</span>
									<?php endif; ?>
									<span class="es-flow__title"><?php echo wp_kses_post( $es_title ); ?></span>
								</span>
								<?php if ( ! empty( $es_node['ai'] ) ) : ?>
									<?php // Marcador (IA): punto donde interviene el asistente. La leyenda al pie lo explica una sola vez. ?>
									<span class="es-flow__ai" title="<?php echo esc_attr( $es_ai_legend ); ?>"><span class="es-visually-hidden"><?php echo esc_html( $es_ai_legend ); ?></span><span aria-hidden="true">IA</span></span>
								<?php endif; ?>
							</span>
						</span>

						<span class="es-flow__body">
							<?php if ( '' !== $es_text ) : ?>
								<span class="es-flow__text"><?php echo wp_kses_post( $es_text ); ?></span>
							<?php endif; ?>

							<?php if ( $es_has_panel && '' !== $es_detail ) : ?>
								<?php
								/*
								 * En mobile no hay hover: esto es el ÚNICO indicador de que
								 * el nodo se puede abrir, así que lleva un ícono de flecha
								 * (no sólo texto) y gira al abrirse — misma lógica que un
								 * desplegable. El <button> ya cubre toda la tarjeta como
								 * área táctil; esto es la señal visual, no un segundo target.
								 */
								?>
								<span class="es-flow__more">
									<span class="es-flow__more-label"><?php echo esc_html( $es_detail ); ?></span>
									<svg class="es-flow__more-icon" viewBox="0 0 12 12" aria-hidden="true" focusable="false">
										<polyline points="2.5,4.5 6,8 9.5,4.5" />
									</svg>
								</span>
							<?php endif; ?>
						</span>
					</<?php echo esc_html( $es_tag ); ?>>

					<?php if ( $es_has_panel ) : ?>
						<div class="es-flow__panel" id="<?php echo esc_attr( $es_panel_id ); ?>" data-es-flow-panel>
							<dl class="es-flow__detail">
								<?php foreach ( $es_rows as $es_row ) : ?>
									<?php $es_row_label = trim( (string) ( $es_row['label'] ?? '' ) ); ?>
									<?php if ( '' !== $es_row_label ) : ?>
										<dt><?php echo esc_html( $es_row_label ); ?></dt>
									<?php endif; ?>
									<dd><?php echo wp_kses_post( trim( (string) $es_row['text'] ) ); ?></dd>
								<?php endforeach; ?>
							</dl>
							<?php if ( '' !== $es_close ) : ?>
								<button type="button" class="es-flow__close" data-es-flow-close><?php echo esc_html( $es_close ); ?></button>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( '' !== $es_end && $es_i === $es_last_main ) : ?>
					<p class="es-flow__marker es-flow__marker--end"><span><?php echo esc_html( $es_end ); ?></span></p>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
		</ol>
	</div>

	<?php
	// Leyenda del marcador (IA), una sola vez al pie. Sólo se imprime si
	// algún nodo realmente lo usa.
	$es_uses_ai = false;
	foreach ( $es_items as $es_n ) {
		if ( ! empty( $es_n['ai'] ) ) {
			$es_uses_ai = true;
			break;
		}
	}
	?>
	<?php if ( $es_uses_ai && '' !== $es_ai_legend ) : ?>
		<p class="es-flow__legend"><span class="es-flow__ai es-flow__ai--legend" aria-hidden="true">IA</span> <?php echo esc_html( $es_ai_legend ); ?></p>
	<?php endif; ?>
</div>
