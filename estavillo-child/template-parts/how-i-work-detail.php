<?php
/**
 * How I Work — versión editorial completa, solo para
 * templates/page-how-i-work.php (nunca en Home — el teaser compacto sigue
 * siendo template-parts/how-i-work.php).
 *
 * Mismos 6 pasos, mismo dato (es_home_process_steps() — fuente y default
 * compartidos con el teaser, ver functions.php), pero acá se muestran los
 * 3 campos opcionales que el teaser omite a propósito: "Why it matters",
 * "Example" y "Tools". Cualquiera de los 3 vacío simplemente no imprime
 * su bloque — ningún campo es obligatorio.
 *
 * Layout: secuencia vertical numerada con una columna de marcador fija
 * (número + ícono + línea conectora) y el cuerpo a la derecha — un índice
 * técnico/editorial, no el cliché de íconos-en-círculos-y-flechas de un
 * timeline de SaaS genérico.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_steps = es_home_process_steps();
?>

<section class="es-process-detail">
	<div class="es-container">
		<ol class="es-process-detail__list">
			<?php foreach ( $es_steps as $es_i => $es_step ) : ?>
				<?php
				$es_icon_markup = es_process_step_icon_markup( $es_step );
				$es_tools_raw   = ! empty( $es_step['tools'] ) ? $es_step['tools'] : '';
				$es_tools       = array();
				if ( '' !== $es_tools_raw ) {
					foreach ( explode( ',', $es_tools_raw ) as $es_tool ) {
						$es_tool = trim( $es_tool );
						if ( '' !== $es_tool ) {
							$es_tools[] = $es_tool;
						}
					}
				}
				?>
				<li class="es-process-detail__step" data-es-reveal style="--es-reveal-delay: <?php echo esc_attr( $es_i * 70 ); ?>ms">
					<div class="es-process-detail__marker">
						<span class="es-process-detail__num"><?php echo esc_html( sprintf( '%02d', $es_i + 1 ) ); ?></span>
						<span class="es-process-detail__icon<?php echo '' === $es_icon_markup ? ' es-process-detail__icon--empty' : ''; ?>" aria-hidden="true">
							<?php echo $es_icon_markup; // phpcs:ignore -- ya pasado por wp_kses en es_process_step_icon_markup(). ?>
						</span>
					</div>

					<div class="es-process-detail__body">
						<h3 class="es-process-detail__title"><?php echo esc_html( $es_step['title'] ); ?></h3>
						<p class="es-process-detail__text"><?php echo esc_html( $es_step['text'] ); ?></p>

						<?php if ( ! empty( $es_step['why'] ) ) : ?>
							<p class="es-process-detail__note es-process-detail__note--why">
								<span class="es-process-detail__note-label"><?php esc_html_e( 'Why it matters', 'estavillo-child' ); ?></span>
								<?php echo esc_html( $es_step['why'] ); ?>
							</p>
						<?php endif; ?>

						<?php if ( ! empty( $es_step['example'] ) ) : ?>
							<p class="es-process-detail__note es-process-detail__note--example">
								<span class="es-process-detail__note-label"><?php esc_html_e( 'Example', 'estavillo-child' ); ?></span>
								<?php echo esc_html( $es_step['example'] ); ?>
							</p>
						<?php endif; ?>

						<?php if ( ! empty( $es_tools ) ) : ?>
							<div class="es-process-detail__tools">
								<span class="es-process-detail__note-label"><?php esc_html_e( 'Tools', 'estavillo-child' ); ?></span>
								<div class="es-process-detail__tools-list">
									<?php foreach ( $es_tools as $es_tool ) : ?>
										<span class="es-pill"><?php echo esc_html( $es_tool ); ?></span>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
