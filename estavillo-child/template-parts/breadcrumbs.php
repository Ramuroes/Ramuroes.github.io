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
?>
<nav class="es-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'estavillo-child' ); ?>">
	<div class="es-container">
		<ol class="es-breadcrumbs__list">
			<?php foreach ( $es_trail as $es_i => $es_crumb ) : ?>
				<li class="es-breadcrumbs__item">
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
