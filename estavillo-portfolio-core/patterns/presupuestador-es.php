<?php
/**
 * Pattern content — Presupuestador Case Structure (ES).
 *
 * Transcripción en bloques del maestro
 * docs/content/presupuestador-case-study-es.html: mismas 13 secciones,
 * mismos anchors, mismos textos y placeholders. Ver case-patterns.php
 * para las decisiones (cols → flujo secuencial, dos patterns por idioma).
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return <<<'CONTENT'
<!-- wp:estavillo/case-section {"anchor":"overview","label":"Fig. 00 — Resumen","heading":"Un presupuesto que dependía de una sola cabeza, convertido en un criterio que se puede escribir, discutir y mejorar.","lead":"Presupuestador es un sistema de apoyo a la decisión para un taller de fabricación metálica en Montevideo. El objetivo no fue construir una app de presupuestos genérica, sino hacer explícito el criterio de precios que hasta ahora vivía en la cabeza de una sola persona — y ponerlo a disposición del resto del equipo sin perder el juicio experto que lo sostiene."} -->
<!-- wp:estavillo/case-ladder {"steps":[{"label":"Diagnóstico operativo","state":"done"},{"label":"Criterios documentados","state":"done"},{"label":"MVP en Google Sheets","state":"done"},{"label":"Calibración contra el presupuestador","state":"done"},{"label":"App Alpha","state":"active"},{"label":"Adopción en todo el equipo","state":"future"}]} /-->

<!-- wp:paragraph {"className":"es-case-caption"} -->
<p class="es-case-caption">Dónde está hoy el proyecto: App Alpha está construida y responde presupuestos reales; el paso siguiente —que todo el equipo presupueste de forma independiente— es el objetivo, todavía no un resultado medido (más en Resultados y Limitaciones, más abajo).</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"context","label":"Fig. 01 — Contexto","heading":"Un taller que presupuesta bien — pero despacio, y desde la cabeza de una sola persona.","lead":"Guzmán Villalba es un taller de fabricación metálica en Montevideo. Cada trabajo — desde una baranda a medida hasta una tanda completa de piezas — arranca con un presupuesto. Durante años, ese presupuesto salió del criterio de un solo presupuestador: material, mano de obra, tiempo de máquina y margen, calculados desde la memoria y la experiencia, no desde un sistema escrito."} -->
<!-- wp:paragraph -->
<p>Eso funcionó mientras el volumen fue manejable. Pero los pedidos empezaron a hacer cola detrás de una sola persona. Alguien nuevo en el equipo no podía presupuestar sin acompañar el proceso durante meses. Y como el criterio vivía solo en su cabeza, no se podía cuestionar, versionar ni mejorar — solo repetir.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"problem","label":"Fig. 02 — El problema operativo","heading":"Un presupuesto lento puede perder el trabajo antes de cortar el primer caño.","lead":"Un cliente que llama pidiendo un número aproximado necesita una respuesta rápida y defendible. Si la única vía es un presupuesto formal completo — y ese presupuesto depende de que una persona puntual tenga tiempo libre esa semana — la respuesta llega tarde, o no llega."} -->
<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>El criterio de precios no estaba escrito en ningún lado: vivía en la experiencia de una persona.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>No había una forma rápida de dar un rango orientativo sin armar el presupuesto completo.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Sumar gente al equipo de presupuestos significaba, en la práctica, que esa persona los acompañara caso por caso.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>[DATO PENDIENTE DE VALIDACIÓN — si existe un tiempo de respuesta promedio real (p. ej. días de espera típicos para un presupuesto formal), reemplazar este párrafo con ese dato concreto. No se incluye acá un número porque no hay una fuente verificable en este repositorio.]</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"discovery","label":"Fig. 03 — Entender el flujo real","heading":"Mirar cómo se arma un presupuesto, no solo preguntar cómo se arma."} -->
<!-- wp:paragraph -->
<p>Las entrevistas solas no alcanzaron para entender cuánto criterio había en juego — el propio presupuestador tenía dificultad para poner en palabras sus propias reglas. Acompañar varios presupuestos reales de punta a punta mostró las variables reales en juego: tipo y espesor del material, complejidad del corte, terminaciones, disponibilidad de máquina esa semana, y un margen que se ajustaba según la relación con el cliente y la urgencia del pedido.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>El hallazgo más claro: el criterio no era arbitrario, era <em>consistente pero no estaba documentado</em> — lo que significaba que se podía capturar como un modelo explícito, en vez de reemplazarlo por una fórmula genérica que hubiera ignorado las condiciones reales del taller.</p>
<!-- /wp:paragraph -->

<!-- wp:estavillo/case-figure {"variant":"standard","tag":"01","caption":"[CAPTURA PENDIENTE — foto del acompañamiento de presupuestos o de las notas de investigación sintetizadas.]","placeholderLabel":"discovery-notes","alt":"Placeholder para foto o notas de investigación"} /-->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"system","label":"Fig. 04 — Hipótesis de diseño y arquitectura","heading":"Un solo modelo de precios, varias superficies.","lead":"La hipótesis de partida: si el criterio del presupuestador se podía volver explícito, no hacía falta reemplazar su juicio — hacía falta darle una forma que otra persona pudiera usar, cuestionar y mejorar. En vez de construir una aplicación única desde el principio, el sistema separa la <em>lógica de precios</em> (versionada, inspeccionable, propiedad del taller) de las <em>superficies</em> que la consultan — así el presupuestador podía seguir trabajando desde el primer día, y la interfaz podía evolucionar sin tocar la lógica de fondo."} -->
<!-- wp:estavillo/case-figure {"variant":"browser","browserLabel":"diagrama-de-sistema","placeholderLabel":"architecture-diagram","alt":"Placeholder para diagrama de arquitectura del sistema"} /-->

<!-- wp:paragraph -->
<p>Los datos de entrada (material, dimensiones, terminación, urgencia) alimentan un único modelo de precios. Ese modelo devuelve tanto un rango orientativo rápido como, una vez confirmado, un presupuesto detallado. El mismo modelo se consulta desde el MVP en Sheets y, más adelante, desde la app Alpha — cambiar de superficie nunca implica rehacer la lógica.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>[DIAGRAMA PENDIENTE — reemplazar el placeholder por el flujo real de entradas → modelo de precios → salidas una vez que exista como asset final.]</p>
<!-- /wp:paragraph -->

<!-- wp:estavillo/case-taxonomy {"root":"Modelo de precios — las entradas que lee","items":[{"title":"Material y espesor","meta":"mueve el costo"},{"title":"Complejidad del corte","meta":"mueve la mano de obra"},{"title":"Terminaciones","meta":"mueve la mano de obra"},{"title":"Disponibilidad de máquina esa semana","meta":"capacidad, no precio"}],"modsLabel":"Se ajusta<br>según…","mods":["Relación con el cliente","Urgencia"]} /-->

<!-- wp:paragraph {"className":"es-case-caption"} -->
<p class="es-case-caption">Las mismas variables que aparecieron en el descubrimiento — material y espesor, complejidad de corte, terminaciones y tiempo de máquina definen costo y mano de obra; la relación con el cliente y la urgencia ajustan desde ahí.</p>
<!-- /wp:paragraph -->

<!-- wp:estavillo/case-stats {"items":[{"num":"1","label":"modelo de precios único, versionado"},{"num":"2","label":"superficies que lo consultan (Sheets y App Alpha)"},{"num":"2","label":"tipos de salida por consulta: rango rápido y presupuesto detallado"}]} /-->

<!-- wp:paragraph {"className":"es-case-caption"} -->
<p class="es-case-caption">Estos tres números describen decisiones de diseño del sistema (cuántos modelos, cuántas superficies, cuántos tipos de salida) — no métricas de negocio del taller.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"mvp","label":"Fig. 05 — MVP en Google Sheets + Apps Script","heading":"Publicar la lógica antes que la interfaz.","lead":"La primera versión del modelo de precios vivió enteramente en Google Sheets con Apps Script — a propósito, para que el presupuestador pudiera corregir y extender el criterio él mismo, sin depender de un ciclo de desarrollo, y para poder validar el sistema contra presupuestos reales antes de construir cualquier interfaz."} -->
<!-- wp:estavillo/case-figure {"variant":"browser","browserLabel":"presupuestador.xlsx — modelo de precios v1","placeholderLabel":"mvp-sheets-screenshot","alt":"Placeholder para captura de la planilla del MVP"} /-->

<!-- wp:paragraph -->
<p>Cada presupuesto que pasaba por la planilla quedaba registrado. Esa base de presupuestos —no un número de negocio, sino el propio material de trabajo del sistema— es lo que después permitió comparar el modelo contra el criterio del presupuestador y ajustar la ponderación antes de pasar a una interfaz dedicada.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"testing","label":"Fig. 06 — Testing e iteración","heading":"El presupuestador como referencia, no como usuario de prueba.","lead":"La validación no fue una tanda de usability testing con usuarios externos — fue comparar, presupuesto por presupuesto, lo que proponía el modelo contra lo que decía el propio presupuestador. Cuando coincidían, eso confirmaba una regla. Cuando no coincidían, esa diferencia era la señal para revisar el modelo, no para descartar el desacuerdo."} -->
<!-- wp:estavillo/case-timeline {"items":[{"title":"Correr un presupuesto real por el modelo","text":"Mismo caso, dos criterios: el del modelo y el del presupuestador, uno al lado del otro."},{"title":"Marcar coincidencias y desvíos","text":"Los desvíos no se promediaban ni se ignoraban — cada uno se revisaba para entender qué variable faltaba o estaba mal ponderada."},{"title":"Ajustar el modelo, no el criterio","text":"El objetivo era que el modelo aprendiera a explicar el criterio del presupuestador — no al revés."}]} /-->

<!-- wp:paragraph -->
<p>[DATO PENDIENTE DE VALIDACIÓN — si existe una cifra real de cobertura o precisión del modelo frente al criterio del presupuestador (por ejemplo, en cuántos casos coincidieron dentro de un rango aceptable), incorporarla acá con su fuente. Mientras tanto, este proceso se describe de forma cualitativa porque no hay una cifra confirmada.]</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"app-alpha","label":"Fig. 07 — App Alpha","heading":"De una planilla que solo una persona sabía manejar, a una herramienta que puede usar el equipo."} -->
<!-- wp:estavillo/case-figure {"variant":"browser","browserLabel":"app.presupuestador — alpha","placeholderLabel":"app-alpha-screenshot","alt":"Placeholder para captura de App Alpha"} /-->

<!-- wp:estavillo/case-timeline {"items":[{"title":"Mismo modelo, puerta de entrada nueva","text":"La app Alpha consulta el mismo modelo de precios validado en Sheets — no se reescribió la lógica, solo se le dio una interfaz nueva."},{"title":"Entrada guiada en vez de una planilla en blanco","text":"Un formulario corto y estructurado reemplaza las celdas libres, para que cualquiera del equipo —no solo el presupuestador original— pueda armar un presupuesto consistente."},{"title":"Rango rápido primero, presupuesto preciso después","text":"La app responde un \"más o menos cuánto\" orientado al cliente en segundos, y después acompaña armar el presupuesto detallado."}]} /-->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"ai","label":"Fig. 08 — El rol de la IA y del criterio humano","heading":"La IA asiste el presupuesto. Nunca fija el precio en silencio.","lead":"La inteligencia artificial se usa en tres puntos acotados e inspeccionables del flujo — cada uno asistivo, y cada uno revisable por la persona que está armando el presupuesto."} -->
<!-- wp:estavillo/case-decisions {"taskLabel":"Tarea","guardrailLabel":"Resguardo","items":[{"num":"01","title":"Leer la entrada cruda","task":"Extraer medidas o notas de material desde una foto o una descripción en texto libre del cliente.","guardrail":"Siempre se muestra de vuelta al presupuestador como campos editables antes de correr el modelo."},{"num":"02","title":"Sugerir un rango","task":"Proponer un rango orientativo a partir de presupuestos anteriores con entradas similares.","guardrail":"Se etiqueta explícitamente como sugerencia — el número final lo define la lógica del modelo de precios, no la IA."},{"num":"03","title":"Marcar anomalías","task":"Señalar un presupuesto que se aleja mucho del patrón histórico, para que alguien lo revise antes de enviarlo.","guardrail":"Es solo una alerta — nunca bloquea el envío de un presupuesto, solo pide una segunda mirada."}]} /-->

<!-- wp:paragraph -->
<p>El sistema no reemplaza al presupuestador del taller: estructura y pone en operación su criterio. Los rangos sirven para responder rápido y hacer una primera evaluación de viabilidad — un caso fuera de lo común, con condiciones que el modelo todavía no cubre bien, sigue necesitando el juicio de una persona antes de confirmarse.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"results","label":"Fig. 09 — Estado actual","heading":"Dónde está el sistema hoy.","lead":"Presupuestador es un sistema en uso real, no un producto terminado. Esto es lo que se puede afirmar hoy con confianza, y lo que todavía necesita más evidencia antes de poder afirmarse como resultado."} -->
<!-- wp:estavillo/case-status {"columns":[{"heading":"Vigente hoy","style":"done","items":["El criterio de precios está escrito, versionado y ya no vive solo en la cabeza de una persona.","Existe un modelo único que responde tanto desde Sheets como desde la app Alpha.","Cada presupuesto que pasa por el sistema queda registrado, no se pierde en una conversación o un papel."]},{"heading":"Necesita más evidencia","style":"attention","items":["[DATO PENDIENTE DE VALIDACIÓN — impacto real en el tiempo de respuesta al cliente, una vez que haya datos de uso suficientes para medirlo con confianza.]","[DATO PENDIENTE DE VALIDACIÓN — cuántas personas del equipo, además del presupuestador original, ya presupuestan de forma independiente usando el sistema.]","Los casos atípicos todavía dependen del criterio directo del presupuestador — el sistema no tiene suficiente historial para cubrirlos con confianza."]}]} /-->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"limitations","label":"Fig. 10 — Limitaciones","heading":"Lo que el sistema todavía no hace."} -->
<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>Los trabajos atípicos o a medida siguen resolviéndose con criterio manual — el modelo cubre bien el patrón común, no el caso excepcional.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Todavía no existe un flujo pensado primero para uso desde el celular en el piso del taller.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>El rango sugerido por IA necesita más historial de presupuestos para mantenerse preciso a medida que cambian los costos de material.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>El sistema depende de que el presupuestador siga revisando y corrigiendo el modelo — no es un criterio que se mantenga solo.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"learnings","label":"Fig. 11 — Qué aprendí","heading":"Lo difícil nunca fue la pantalla."} -->
<!-- wp:paragraph -->
<p>El trabajo de diseño real fue volver explícito un criterio que una persona nunca había tenido que poner en palabras — hacerlo lo bastante claro como para escribirlo, discutirlo y corregirlo. La aplicación fue, en comparación, el paso más simple una vez que eso estaba resuelto.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>También aprendí a desconfiar de la tentación de reemplazar el criterio experto por una fórmula prolija: un modelo que ignora las condiciones reales del taller —la relación con el cliente, la urgencia, la disponibilidad de máquina esa semana— hubiera sido más simple de construir, y peor. El sistema tenía que aprender del presupuestador, no corregirlo desde afuera.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"next","label":"Fig. 12 — Próximos pasos","heading":"Hacia dónde va esto."} -->
<!-- wp:estavillo/case-details {"summary":"Extender el modelo a trabajos atípicos o a medida"} -->
<!-- wp:paragraph -->
<p>[DATO PENDIENTE DE VALIDACIÓN — describir el plan concreto para cubrir los trabajos no estándar que hoy siguen dependiendo del criterio manual.]</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-details -->

<!-- wp:estavillo/case-details {"summary":"Construir un flujo pensado para el piso del taller"} -->
<!-- wp:paragraph -->
<p>[DATO PENDIENTE DE VALIDACIÓN — describir el plan concreto para un flujo de presupuestado liviano, usable directamente desde el celular en el taller.]</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-details -->

<!-- wp:estavillo/case-details {"summary":"Formalizar el rango asistido por IA con más historial"} -->
<!-- wp:paragraph -->
<p>[DATO PENDIENTE DE VALIDACIÓN — describir cómo va a seguir creciendo la base histórica de presupuestos para mejorar la precisión del rango sugerido con el tiempo.]</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-details -->
<!-- /wp:estavillo/case-section -->
CONTENT;
