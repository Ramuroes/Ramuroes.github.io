<?php
/**
 * Breadcrumbs — tira simple y accesible (Home / Work / título del caso).
 *
 * Genérico por diseño (recibe un 'trail' de {label, url} vía $args): hoy lo
 * usan single-es_case_study.php y las 4 páginas fijas (Work/About/How I
 * Work/Contact), todas armando su trail con es_breadcrumb_trail() —
 * ninguna reimplementa el markup.
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
 * Todos los niveles se muestran siempre, en todos los viewports — nunca
 * se oculta un nivel intermedio (perdía navegación real, p. ej. el link a
 * "Work"). Lo único que trunca es el ÚLTIMO crumb (el actual, sin link),
 * vía ellipsis puro CSS: los ítems anteriores llevan flex-shrink:0 (nunca
 * se achican) y sólo el último es flexible + min-width:0, así el que se
 * corta es siempre el título largo, nunca "Home" o "Work" a la mitad.
 */
?>
<nav class="es-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'estavillo-child' ); ?>">
	<div class="es-container">
		<ol class="es-breadcrumbs__list">
			<?php foreach ( $es_trail as $es_i => $es_crumb ) : ?>
				<li class="es-breadcrumbs__item<?php echo ( $es_i === $es_last_index ) ? ' es-breadcrumbs__item--current' : ''; ?>">
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
