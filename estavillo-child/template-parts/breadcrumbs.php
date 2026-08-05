<?php
/**
 * Breadcrumbs — tira simple y accesible (Home / Work / título del caso).
 *
 * Usado por single-es_case_study.php. Genérico por diseño (recibe un
 * 'trail' de {label, url} vía $args) para poder reusarse en otras páginas
 * fijas más adelante sin cambiar este archivo — pero por ahora solo se
 * engancha en el Case Study, que es lo único que pidió este ticket.
 *
 * El link "Work" reusa es_nav_links() (el mismo array que ya alimenta el
 * header, el menú mobile y el footer) en vez de un campo nuevo: si el
 * admin repunta el nav link "Work" a una página real, el breadcrumb apunta
 * ahí automáticamente. Con Polylang activo, home_url() y los permalinks ya
 * resuelven al idioma actual sin código extra acá — es comportamiento
 * nativo de WordPress/Polylang, no algo que este archivo tenga que hacer.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_trail = isset( $args['trail'] ) && is_array( $args['trail'] ) ? $args['trail'] : array();
if ( empty( $es_trail ) ) {
	return;
}

$es_last_index = count( $es_trail ) - 1;

/*
 * Colapso en mobile (sólo CSS, sin JS y sin cambiar el desktop): con más
 * de 2 crumbs, los del MEDIO llevan --collapsible y el CSS los oculta por
 * debajo de 600px, dejando "Home › … › actual" en una sola línea. El "…"
 * se imprime siempre pero sólo es visible en ese mismo breakpoint.
 *
 * Accesibilidad: el "…" es aria-hidden (decorativo puro) y los crumbs del
 * medio se ocultan con display:none, no con visibility/opacity — o sea que
 * un lector de pantalla en un viewport chico escucha "Home, Trazur", una
 * ubicación comprensible, en vez de leer un "…" sin significado. Los links
 * intermedios siguen siendo alcanzables en desktop, que es donde se ven.
 * Con 2 crumbs o menos no hay nada que colapsar y el markup queda idéntico
 * al de siempre — por eso las otras 4 plantillas que usan este archivo
 * (Work/About/How I Work/Contact, todas de 2 crumbs) no cambian en nada.
 */
$es_has_collapse = $es_last_index >= 2;
?>
<nav class="es-breadcrumbs<?php echo $es_has_collapse ? ' es-breadcrumbs--collapsible' : ''; ?>" aria-label="<?php esc_attr_e( 'Breadcrumb', 'estavillo-child' ); ?>">
	<div class="es-container">
		<ol class="es-breadcrumbs__list">
			<?php foreach ( $es_trail as $es_i => $es_crumb ) : ?>
				<?php
				$es_is_middle = $es_has_collapse && $es_i > 0 && $es_i < $es_last_index;
				?>
				<?php if ( $es_is_middle && 1 === $es_i ) : ?>
					<li class="es-breadcrumbs__item es-breadcrumbs__item--ellipsis" aria-hidden="true">
						<span class="es-breadcrumbs__ellipsis">&hellip;</span>
					</li>
				<?php endif; ?>
				<li class="es-breadcrumbs__item<?php echo $es_is_middle ? ' es-breadcrumbs__item--collapsible' : ''; ?>">
					<?php if ( $es_i < $es_last_index && ! empty( $es_crumb['url'] ) ) : ?>
						<a class="es-breadcrumbs__link" href="<?php echo esc_url( $es_crumb['url'] ); ?>"><?php echo esc_html( $es_crumb['label'] ); ?></a>
					<?php else : ?>
						<span class="es-breadcrumbs__current" aria-current="page"><?php echo esc_html( $es_crumb['label'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</nav>
