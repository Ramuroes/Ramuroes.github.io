<?php
/**
 * Render dinámico de estavillo/case-flow-v2.
 *
 * MISMO dato que estavillo/case-flow (un grafo: nodos + aristas) y MISMO
 * lenguaje visual (tokens, colores, formas, hover, popovers). Lo que cambia
 * es la GEOMETRÍA de los conectores del rombo, que acá se comporta como en un
 * diagrama de flujo clásico: los trazos nacen de los VÉRTICES, no del borde
 * de una caja invisible.
 *
 *   · vértice DERECHO  → la salida principal, recta, hacia el paso siguiente.
 *   · vértice SUPERIOR → la rama sube a un carril propio, entra a una pantalla
 *                        intermedia y esa pantalla BAJA al mismo destino que
 *                        la salida principal. Las dos vuelven a juntarse.
 *   · vértice INFERIOR → el bucle: baja, recorre un canal de retorno y vuelve
 *                        a un paso ANTERIOR. Sin tarjeta intermedia: el bucle
 *                        es el propio conector, como en un flowchart.
 *
 * Por qué un bloque nuevo y no una edición de case-flow: la v1 está publicada
 * y aprobada. Los conectores de rama de la v1 entran por el costado o el techo
 * de la tarjeta a propósito (debajo de cada forma vive su descripción), así que
 * anclar a los vértices exige mover esa descripción y cambiar el reparto de
 * filas. Son dos diagramas distintos, no una corrección: conviven, y cada
 * página elige cuál usa.
 *
 * MODELO DE DATOS — idéntico a la v1, así que el contenido es intercambiable:
 *   nodes[]: { id, kind, accent, num, title, text, ai, detail[], edges[] }
 *   edges[]: { to: <id de otro nodo>, label: "Sí" | "No" | "" }
 *
 *   - La PRIMERA arista de cada nodo es el camino principal ("la línea").
 *   - Una arista a un nodo que NO está en el camino principal abre una
 *     pantalla intermedia (rama con tarjeta).
 *   - Una arista a un nodo ANTERIOR del camino principal es un BUCLE y se
 *     dibuja como canal de retorno, sin tarjeta.
 *   - Si NINGÚN nodo declara `edges`, se encadena todo en línea recta.
 *
 * LAYOUT (desktop, calculado). Cada banda puede tener hasta tres filas:
 *     carril de ramas   (sólo si la banda abre alguna pantalla intermedia)
 *     banda             (el camino principal, en serpentina de 4 columnas)
 *     canal de retorno  (sólo si la banda tiene algún bucle)
 *
 * PROGRESSIVE ENHANCEMENT y semántica: igual que la v1 — el detalle se emite
 * siempre visible y el JS (assets/js/case-flow.js, compartido) lo convierte en
 * popover/acordeón; el orden del DOM es siempre el narrativo real.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    Sin uso (bloque hoja).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Acentos permitidos — mismos tokens que la v1, sin colores nuevos. */
if ( ! function_exists( 'es_flow2_accent_class' ) ) {
	function es_flow2_accent_class( $accent ) {
		$accent = sanitize_key( (string) $accent );
		return in_array( $accent, array( 'signal', 'decision', 'muted' ), true ) ? ' is-accent-' . $accent : '';
	}
}

/** Tipos de nodo permitidos. 'step' es el default seguro. */
if ( ! function_exists( 'es_flow2_node_kind' ) ) {
	function es_flow2_node_kind( $kind ) {
		$kind = sanitize_key( (string) $kind );
		return in_array( $kind, array( 'start', 'step', 'decision', 'milestone', 'end' ), true ) ? $kind : 'step';
	}
}

/** Conector recto del camino principal (idéntico a la v1). */
if ( ! function_exists( 'es_flow2_connector_svg' ) ) {
	function es_flow2_connector_svg() {
		return '<svg class="es-flow__connector" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true" focusable="false">'
			. '<path class="es-flow__line es-flow__line--h" d="M0 50 H96" vector-effect="non-scaling-stroke" />'
			. '<polyline class="es-flow__head es-flow__head--h" points="91,44 98,50 91,56" vector-effect="non-scaling-stroke" />'
			. '<path class="es-flow__line es-flow__line--v" d="M50 0 V96" vector-effect="non-scaling-stroke" />'
			. '<polyline class="es-flow__head es-flow__head--v" points="44,91 50,98 56,91" vector-effect="non-scaling-stroke" />'
			. '</svg>';
	}
}

/**
 * Punta de flecha suelta, tamaño fijo. Los canales de retorno y las ramas se
 * dibujan con bordes CSS (un borde recto no se deforma nunca, sea cual sea el
 * ancho de columna); la punta va aparte para que el triángulo no se estire.
 *
 * @param string $dir up | down | left | right.
 */
if ( ! function_exists( 'es_flow2_tip_svg' ) ) {
	function es_flow2_tip_svg( $dir ) {
		$dir = in_array( $dir, array( 'up', 'down', 'left', 'right' ), true ) ? $dir : 'down';
		return '<svg class="es-flow__tip es-flow__tip--' . $dir . '" viewBox="0 0 12 12" aria-hidden="true" focusable="false">'
			. '<polyline points="2,5 6,9 10,5" vector-effect="non-scaling-stroke" />'
			. '</svg>';
	}
}

/**
 * Traduce el grafo a una grilla concreta. Genérico: no sabe nada de Trazur ni
 * de ningún flujo puntual.
 *
 * Devuelve array(
 *   'items'   => nodos en orden narrativo con su placement y sus conectores,
 *   'rows'    => filas de grilla que ocupa el desktop,
 *   'cols'    => columnas por banda,
 *   'passes'  => prolongaciones de la línea principal a través de un carril,
 *   'routes'  => trazos de rama y de bucle, posicionados por grilla,
 *   'spacers' => filas de canal de retorno (alto fijo, sin contenido),
 * ).
 *
 * @param array $nodes Nodos ya filtrados (con title no vacío).
 * @param int   $cols  Columnas por banda en desktop.
 */
if ( ! function_exists( 'es_flow2_layout' ) ) {
	function es_flow2_layout( $nodes, $cols = 4 ) {
		$total = count( $nodes );

		// ---- 1. ids estables -------------------------------------------------
		$index_of = array();
		foreach ( $nodes as $i => $node ) {
			$id = trim( (string) ( $node['id'] ?? '' ) );
			if ( '' === $id || isset( $index_of[ $id ] ) ) {
				$id = 'n' . ( $i + 1 );
			}
			$nodes[ $i ]['_id'] = $id;
			$index_of[ $id ]    = $i;
		}

		// ---- 2. aristas ------------------------------------------------------
		// Sin edges declaradas → cadena lineal (compatibilidad con dato viejo).
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
					// Arista a un id inexistente: se descarta en silencio.
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
			$nodes[ $i ]['_col']  = ( 0 === $band % 2 ) ? $idx : ( $cols - 1 - $idx );
			$nodes[ $i ]['_lane'] = 'main';
		}

		// ---- 5. pantallas intermedias (ramas con tarjeta) --------------------
		// Diferencia central con la v1: una pantalla intermedia que REINGRESA
		// al flujo se coloca en la MISMA COLUMNA que el nodo al que vuelve, en
		// el carril de arriba. Así su salida es una caída vertical recta al
		// techo de ese nodo — que es exactamente lo que hace que la rama "se
		// vuelva a juntar" con la salida principal en vez de quedar colgada al
		// costado.
		$lane_bands = array();
		foreach ( $nodes as $i => $node ) {
			if ( isset( $on_main[ $i ] ) ) {
				continue;
			}

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
				// Nodo suelto: se trata como un paso más, nunca se pierde contenido.
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

			$rejoins = ( null !== $back && $on_main[ $back ] > $on_main[ $from ] );

			if ( $rejoins ) {
				// Reingreso: misma columna que el destino, carril de esa banda.
				$band = $nodes[ $back ]['_band'];
				$col  = $nodes[ $back ]['_col'];
			} else {
				// Rama sin reingreso hacia adelante (o bucle con tarjeta): al
				// lado de la decisión, para no taparle la columna.
				$band = $nodes[ $from ]['_band'];
				$anchor_col = $nodes[ $from ]['_col'];
				$col = ( $anchor_col > 0 ) ? ( $anchor_col - 1 ) : ( $anchor_col + 1 );
			}

			$nodes[ $i ]['_band']    = $band;
			$nodes[ $i ]['_col']     = $col;
			$nodes[ $i ]['_lane']    = 'branch';
			$nodes[ $i ]['_from']    = $from;
			$nodes[ $i ]['_back']    = $back;
			$nodes[ $i ]['_rejoins'] = $rejoins;

			$lane_bands[ $band ] = true;
		}

		// ---- 5b. bucles sin tarjeta (arista a un nodo anterior) --------------
		// Se detectan ANTES de repartir filas porque cada banda con bucle
		// necesita su propio canal de retorno debajo.
		$back_edges    = array();
		$return_bands  = array();
		foreach ( $nodes as $i => $node ) {
			if ( ! isset( $on_main[ $i ] ) ) {
				continue;
			}
			foreach ( $node['_edges'] as $edge ) {
				$j = $index_of[ $edge['to'] ];
				if ( isset( $on_main[ $j ] ) && $on_main[ $j ] < $on_main[ $i ] ) {
					$band                  = max( $nodes[ $i ]['_band'], $nodes[ $j ]['_band'] );
					$return_bands[ $band ] = true;
					$back_edges[]          = array(
						'from'  => $i,
						'to'    => $j,
						'band'  => $band,
						'label' => $edge['label'],
					);
				}
			}
		}

		// ---- 5c. rasgos de banda que el CSS no puede deducir solo ------------
		$band_has_decision = array();
		foreach ( $nodes as $node ) {
			if ( isset( $node['_band'] ) && 'main' === $node['_lane'] && 'decision' === es_flow2_node_kind( $node['kind'] ?? 'step' ) ) {
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
		$max_band = 0;
		foreach ( $nodes as $node ) {
			if ( isset( $node['_band'] ) ) {
				$max_band = max( $max_band, (int) $node['_band'] );
			}
		}
		$lane_row_of   = array();
		$row_of_band   = array();
		$return_row_of = array();
		$row           = 1;
		for ( $b = 0; $b <= $max_band; $b++ ) {
			if ( isset( $lane_bands[ $b ] ) ) {
				$lane_row_of[ $b ] = $row++;
			}
			$row_of_band[ $b ] = $row++;
			if ( isset( $return_bands[ $b ] ) ) {
				$return_row_of[ $b ] = $row++;
			}
		}
		$rows = $row - 1;

		foreach ( $nodes as $i => $node ) {
			if ( ! isset( $node['_band'] ) ) {
				continue;
			}
			$nodes[ $i ]['_row'] = ( 'branch' === $node['_lane'] )
				? $lane_row_of[ $node['_band'] ]
				: $row_of_band[ $node['_band'] ];
		}

		// ---- 7. conectores ---------------------------------------------------
		foreach ( $nodes as $i => $node ) {
			$nodes[ $i ]['_links'] = array();
		}
		$passes  = array();
		$routes  = array();
		$spacers = array();

		foreach ( $return_row_of as $b => $r ) {
			$spacers[] = $r;
		}

		foreach ( $nodes as $i => $node ) {
			foreach ( $node['_edges'] as $edge ) {
				$j      = $index_of[ $edge['to'] ];
				$a_main = isset( $on_main[ $i ] );
				$b_main = isset( $on_main[ $j ] );
				$label  = $edge['label'];

				if ( $a_main && $b_main ) {
					if ( $on_main[ $j ] < $on_main[ $i ] ) {
						// BUCLE sin tarjeta: sale del vértice inferior del rombo,
						// baja al canal de retorno, lo recorre y sube al costado
						// del paso anterior. El canal va por DEBAJO de la banda,
						// así que no cruza el texto de ninguna tarjeta.
						$band = max( $nodes[ $i ]['_band'], $nodes[ $j ]['_band'] );
						$from_col = (int) $nodes[ $i ]['_col'];
						$to_col   = (int) $nodes[ $j ]['_col'];
						$routes[] = array(
							'kind'  => 'back',
							'row'   => $row_of_band[ $band ],
							'col'   => min( $from_col, $to_col ),
							'cspan' => abs( $from_col - $to_col ) + 1,
							'dir'   => ( $to_col < $from_col ) ? 'left' : 'right',
							'label' => $label,
							// El trazo nace en el vértice INFERIOR del rombo, o sea
							// en el borde de abajo de la franja de forma. Ese alto
							// lo fija la banda (una banda con rombo es más alta),
							// y el trazo no es un nodo, así que no lo hereda solo:
							// hay que decírselo.
							'tall'  => isset( $band_has_decision[ $band ] ),
						);
						continue;
					}

					// Tramo recto del camino principal.
					$same_band = ( $nodes[ $i ]['_band'] === $nodes[ $j ]['_band'] );
					$nodes[ $j ]['_links'][] = array(
						'type'  => $same_band ? 'h' : 'v',
						'label' => $label,
					);
					if ( ! $same_band && isset( $lane_row_of[ $nodes[ $j ]['_band'] ] ) ) {
						$passes[] = array(
							'row' => $lane_row_of[ $nodes[ $j ]['_band'] ],
							'col' => $nodes[ $j ]['_col'],
						);
					}
				} elseif ( ! $b_main ) {
					// ENTRADA a una pantalla intermedia: sale del vértice SUPERIOR
					// del rombo, sube al carril y entra por el costado de la
					// pantalla. Es un trazo de grilla, no un hijo de la tarjeta:
					// tiene que tocar dos filas distintas con precisión.
					$from_col = (int) $nodes[ $i ]['_col'];
					$to_col   = (int) $nodes[ $j ]['_col'];
					$routes[] = array(
						'kind'  => 'fork',
						'row'   => (int) $nodes[ $j ]['_row'],
						'col'   => min( $from_col, $to_col ),
						'cspan' => abs( $from_col - $to_col ) + 1,
						'dir'   => ( $to_col < $from_col ) ? 'left' : 'right',
						'label' => $label,
						'same'  => ( $from_col === $to_col ),
					);
				} else {
					// SALIDA de una pantalla intermedia de vuelta al flujo. Si
					// quedó en la misma columna que su destino (el caso normal:
					// así se coloca arriba), la salida es la caída vertical recta
					// que ya sabe dibujar el riel del camino principal.
					if ( (int) $nodes[ $i ]['_col'] === (int) $nodes[ $j ]['_col'] ) {
						$nodes[ $j ]['_links'][] = array(
							'type'  => 'v',
							'label' => $label,
						);
					} else {
						$from_col = (int) $nodes[ $i ]['_col'];
						$to_col   = (int) $nodes[ $j ]['_col'];
						$routes[] = array(
							'kind'  => 'rejoin',
							'row'   => (int) $nodes[ $i ]['_row'],
							'col'   => min( $from_col, $to_col ),
							'cspan' => abs( $from_col - $to_col ) + 1,
							'dir'   => ( $to_col < $from_col ) ? 'left' : 'right',
							'label' => $label,
							'tall'  => false,
						);
					}
				}
			}
		}

		// ---- 8. orden narrativo del DOM --------------------------------------
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

		// ---- 9. resumen textual de cada bifurcación --------------------------
		// Lo que un lector de pantalla necesita saber de una decisión es a dónde
		// lleva cada salida. Los rótulos "Sí"/"No" del dibujo son decorativos
		// (van sobre trazos absolutos, fuera del orden de lectura), así que la
		// bifurcación se cuenta en texto dentro del propio nodo.
		$title_of = array();
		foreach ( $nodes as $node ) {
			$title_of[ $node['_id'] ] = trim( (string) ( $node['title'] ?? '' ) );
		}
		foreach ( $items as $k => $item ) {
			$branches = array();
			if ( count( $item['_edges'] ) > 1 ) {
				foreach ( $item['_edges'] as $edge ) {
					$branches[] = array(
						'label' => $edge['label'],
						'title' => $title_of[ $edge['to'] ] ?? '',
					);
				}
			}
			$items[ $k ]['_branches'] = $branches;
		}

		return array(
			'items'   => $items,
			'rows'    => $rows,
			'cols'    => $cols,
			'passes'  => $passes,
			'routes'  => $routes,
			'spacers' => $spacers,
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
$es_start         = trim( (string) ( $attributes['startLabel'] ?? '' ) );
$es_end           = trim( (string) ( $attributes['endLabel'] ?? '' ) );
$es_detail        = trim( (string) ( $attributes['detailLabel'] ?? '' ) );
$es_close         = trim( (string) ( $attributes['closeLabel'] ?? '' ) );
$es_step_lb       = trim( (string) ( $attributes['stepLabel'] ?? '' ) );
$es_ai_legend     = trim( (string) ( $attributes['aiLegend'] ?? '' ) );
$es_density       = sanitize_key( (string) ( $attributes['density'] ?? 'comfortable' ) );
$es_density       = in_array( $es_density, array( 'comfortable', 'compact' ), true ) ? $es_density : 'comfortable';

/*
 * Columnas por banda. Es DATO y no una constante porque de esto depende qué
 * nodos comparten fila, y por lo tanto si una decisión y su reingreso caen en
 * la misma banda (la rama sube a un carril y vuelve a bajar) o en bandas
 * distintas. Un flujo largo con una bifurcación temprana necesita una banda
 * más ancha para que la bifurcación se lea; uno corto, no.
 */
$es_cols = (int) ( $attributes['columns'] ?? 4 );
$es_cols = max( 2, min( 6, $es_cols ) );

$es_layout = es_flow2_layout( $es_nodes, $es_cols );
$es_items  = $es_layout['items'];

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

$es_uid   = wp_unique_id( 'es-flow2-' );
$es_total = count( $es_items );

$es_classes = 'es-flow es-flow--v2 es-flow--' . $es_density;
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $es_classes ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?> data-es-flow style="--es-flow-rows: <?php echo esc_attr( (string) $es_layout['rows'] ); ?>; --es-flow-cols: <?php echo esc_attr( (string) $es_layout['cols'] ); ?>">

	<?php if ( '' !== $es_section_label ) : ?>
		<p class="es-eyebrow es-flow__eyebrow"><?php echo esc_html( $es_section_label ); ?></p>
	<?php endif; ?>

	<p class="es-flow__progress" data-es-flow-progress aria-hidden="true">
		<span class="es-flow__progress-current" data-es-flow-current>01</span><span class="es-flow__progress-sep">/</span><span class="es-flow__progress-total"><?php echo esc_html( sprintf( '%02d', $es_total ) ); ?></span>
	</p>

	<ol class="es-flow__track">
		<?php foreach ( $es_layout['passes'] as $es_pass ) : ?>
			<li class="es-flow__pass" aria-hidden="true" style="--r: <?php echo esc_attr( (string) $es_pass['row'] ); ?>; --c: <?php echo esc_attr( (string) ( $es_pass['col'] + 1 ) ); ?>"></li>
		<?php endforeach; ?>

		<?php
		// Filas de canal de retorno: alto fijo y sin contenido. Existen para que
		// el bucle tenga por dónde pasar por debajo de la banda sin cruzar
		// ninguna descripción.
		foreach ( $es_layout['spacers'] as $es_spacer ) :
			?>
			<li class="es-flow__spacer" aria-hidden="true" style="--r: <?php echo esc_attr( (string) $es_spacer ); ?>"></li>
		<?php endforeach; ?>

		<?php
		/*
		 * Trazos de rama y de bucle. Van como items de grilla propios, no como
		 * hijos de una tarjeta: tienen que nacer en el vértice de un rombo de
		 * UNA fila y morir en el costado de una tarjeta de OTRA, así que la
		 * única referencia que sirve para los dos extremos es la grilla misma.
		 * El rótulo ("Sí"/"No") es decoración acá — el texto equivalente se
		 * emite dentro del nodo de decisión, en el orden de lectura real.
		 */
		foreach ( $es_layout['routes'] as $es_route ) :
			/*
			 * La punta apunta SIEMPRE en el sentido del viaje, hacia el nodo
			 * al que se entra: la rama entra de costado a la pantalla
			 * intermedia (del lado que indica 'dir'), el bucle entra POR
			 * ABAJO al paso anterior, y el reingreso suelto baja.
			 */
			if ( 'fork' === $es_route['kind'] ) {
				$es_route_tip = ( 'left' === $es_route['dir'] ) ? 'left' : 'right';
			} elseif ( 'back' === $es_route['kind'] ) {
				$es_route_tip = 'up';
			} else {
				$es_route_tip = 'down';
			}
			?>
			<li class="es-flow__route es-flow__route--<?php echo esc_attr( $es_route['kind'] ); ?> is-dir-<?php echo esc_attr( $es_route['dir'] ); ?><?php echo ! empty( $es_route['same'] ) ? ' is-same-col' : ''; ?><?php echo ! empty( $es_route['tall'] ) ? ' is-tall' : ''; ?>" aria-hidden="true" style="--r: <?php echo esc_attr( (string) $es_route['row'] ); ?>; --c: <?php echo esc_attr( (string) ( $es_route['col'] + 1 ) ); ?>; --cspan: <?php echo esc_attr( (string) $es_route['cspan'] ); ?>">
				<span class="es-flow__route-path"></span>
				<span class="es-flow__route-end">
					<?php echo es_flow2_tip_svg( $es_route_tip ); // phpcs:ignore WordPress.Security.EscapeOutput -- SVG del propio plugin. ?>
				</span>
				<?php if ( '' !== $es_route['label'] ) : ?>
					<span class="es-flow__route-label"><?php echo esc_html( $es_route['label'] ); ?></span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>

		<?php foreach ( $es_items as $es_i => $es_node ) : ?>
			<?php
			$es_kind     = es_flow2_node_kind( $es_node['kind'] ?? 'step' );
			$es_accent   = es_flow2_accent_class( $es_node['accent'] ?? '' );
			$es_num      = trim( (string) ( $es_node['num'] ?? '' ) );
			$es_title    = trim( (string) ( $es_node['title'] ?? '' ) );
			$es_text     = trim( (string) ( $es_node['text'] ?? '' ) );
			$es_rows     = isset( $es_node['detail'] ) && is_array( $es_node['detail'] ) ? $es_node['detail'] : array();
			$es_lane     = $es_node['_lane'] ?? 'main';
			$es_links    = $es_node['_links'] ?? array();
			$es_branches = $es_node['_branches'] ?? array();

			$es_rows = array_values(
				array_filter(
					$es_rows,
					function ( $es_row ) {
						return is_array( $es_row ) && '' !== trim( (string) ( $es_row['text'] ?? '' ) );
					}
				)
			);

			/*
			 * DIFERENCIA CON LA V1: debajo de un rombo no va nada. El vértice
			 * inferior es por donde sale el bucle, así que la descripción de una
			 * decisión se muestra en el panel (donde ya vive el resto de su
			 * razonamiento) en vez de ocupar el eje vertical. En los demás nodos
			 * la descripción sigue debajo de la forma, igual que en la v1.
			 */
			$es_inline_text = ( 'decision' === $es_kind ) ? '' : $es_text;
			if ( 'decision' === $es_kind && '' !== $es_text ) {
				array_unshift(
					$es_rows,
					array(
						'label' => '',
						'text'  => $es_text,
					)
				);
			}

			$es_panel_id  = $es_uid . '-panel-' . $es_i;
			$es_has_panel = ! empty( $es_rows );

			$es_item_class  = 'es-flow__item es-flow__item--' . $es_kind . $es_accent;
			$es_item_class .= ( 'branch' === $es_lane ) ? ' es-flow__item--branch' : '';
			$es_item_class .= ! empty( $es_node['_rtl'] ) ? ' is-rtl' : '';
			$es_item_class .= ! empty( $es_node['_tall'] ) ? ' is-tall' : '';
			?>
			<li class="<?php echo esc_attr( $es_item_class ); ?>" data-es-flow-item data-es-flow-index="<?php echo esc_attr( (string) ( $es_i + 1 ) ); ?>" style="--r: <?php echo esc_attr( (string) ( $es_node['_row'] ?? 1 ) ); ?>; --c: <?php echo esc_attr( (string) ( ( $es_node['_col'] ?? 0 ) + 1 ) ); ?>">

				<?php if ( '' !== $es_start && $es_i === $es_first_main ) : ?>
					<p class="es-flow__marker es-flow__marker--start"><span><?php echo esc_html( $es_start ); ?></span></p>
				<?php endif; ?>

				<div class="es-flow__node">
					<?php
					$es_tag   = $es_has_panel ? 'button' : 'div';
					$es_attrs = $es_has_panel
						? ' type="button" class="es-flow__trigger" aria-expanded="false" aria-controls="' . esc_attr( $es_panel_id ) . '" data-es-flow-trigger'
						: ' class="es-flow__trigger es-flow__trigger--static"';
					?>
					<<?php echo $es_tag . $es_attrs; // phpcs:ignore WordPress.Security.EscapeOutput -- tag de whitelist + atributos ya escapados. ?>>
						<span class="es-flow__shape-band">
							<?php
							foreach ( $es_links as $es_link ) :
								$es_edge = trim( (string) ( $es_node['edgeLabel'] ?? '' ) );
								?>
								<span class="es-flow__rail es-flow__rail--<?php echo esc_attr( $es_link['type'] ); ?>" aria-hidden="true">
									<?php echo es_flow2_connector_svg(); // phpcs:ignore WordPress.Security.EscapeOutput -- SVG del propio plugin. ?>
								</span>
								<?php if ( '' !== $es_link['label'] ) : ?>
									<span class="es-flow__edge-label"><?php echo esc_html( $es_link['label'] ); ?></span>
								<?php endif; ?>
								<?php if ( '' !== $es_edge ) : ?>
									<span class="es-flow__edge"><?php echo esc_html( $es_edge ); ?></span>
								<?php endif; ?>
								<?php
							endforeach;
							?>
							<span class="es-flow__shape">
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
									<span class="es-flow__ai" title="<?php echo esc_attr( $es_ai_legend ); ?>"><span class="es-visually-hidden"><?php echo esc_html( $es_ai_legend ); ?></span><span aria-hidden="true">IA</span></span>
								<?php endif; ?>
							</span>
						</span>

						<span class="es-flow__body">
							<?php if ( ! empty( $es_branches ) ) : ?>
								<?php
								// La bifurcación, en texto y en el orden de lectura real.
								// El dibujo la muestra con trazos y rótulos absolutos, que
								// un lector de pantalla no puede reconstruir.
								?>
								<span class="es-visually-hidden">
									<?php foreach ( $es_branches as $es_branch ) : ?>
										<?php if ( '' !== $es_branch['label'] ) : ?>
											<?php echo esc_html( $es_branch['label'] . ': ' . $es_branch['title'] . '. ' ); ?>
										<?php endif; ?>
									<?php endforeach; ?>
								</span>
							<?php endif; ?>

							<?php if ( '' !== $es_inline_text ) : ?>
								<span class="es-flow__text"><?php echo wp_kses_post( $es_inline_text ); ?></span>
							<?php endif; ?>

							<?php if ( $es_has_panel && '' !== $es_detail ) : ?>
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

	<?php
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
