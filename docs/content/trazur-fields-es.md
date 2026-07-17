# Trazur Cursos (IA + UX Research) — ficha de campos en español

Complementa `trazur-gutenberg-es.html`. Ese archivo es el contenido del
cuerpo (pegar en el Code editor del editor de bloques). Este archivo es
cada campo nativo/meta del post de Case Study, más el orden de
publicación, el checklist de imágenes y — lo más importante — las
afirmaciones que todavía necesitan tu confirmación antes de publicar.

Este es un caso **nuevo** (a diferencia de Workshop Quoting System, no
reemplaza ningún borrador anterior — no existía HTML de Trazur en el
repositorio antes de este ticket). Migrado y editado desde el PDF del
Trabajo de Grado del dueño del proyecto ("IA + UX Research en Trazur
Cursos"), reorganizado según un outline aprobado, con dos rondas de
corrección editorial ya aplicadas. Es **solo la versión en español** — la
versión en inglés es una tarea separada y posterior, todavía no
iniciada.

---

## Campos nativos

| Campo | Valor |
|---|---|
| **Título del post** | Trazur Cursos *(recomendado — ver "Sobre el título" abajo)* |
| **Slug** | `trazur-cursos` |
| **Eyebrow / categoría** | UX Research · IA Aplicada |
| **Excerpt** | Rediseño de una plataforma de e-learning en trazabilidad ganadera para productores rurales uruguayos, combinando investigación UX tradicional con análisis asistido por IA bajo validación humana. |
| **Case Tags** | UX Research, Diseño de Producto, IA Aplicada |
| **Status / label** | Propuesta de rediseño · Validación pendiente |
| **Role** | UX Researcher & UX/UI Designer (enfoque en IA) |
| **Tools** | Balsamiq, Relume, WordPress, Google Analytics, Microsoft Clarity, ChatGPT, Gemini, Synthetic Users, LLaMA |
| **Period** | 2025 (6 meses) |
| **Source / context line** (solo si este caso se marca "Feature this case on Home") | Trabajo de Grado desarrollado junto a Renzo Morandi, con la tutoría de Alejandra Capocasale. |
| **Placeholder label** (se muestra solo si no hay imagen destacada) | trazur-cursos |
| **Case link URL** | Dejar vacío — no existe una URL en vivo confirmada para este proyecto. |
| **Show on Home → Selected Work** | Dejar sin marcar hasta que exista una imagen destacada real y se revisen las afirmaciones pendientes. |
| **Feature this case on Home** | Dejar sin marcar — mismo motivo, más fuerte aún al ser el lugar más visible del sitio. |
| **Order** (campo nativo "Order") | 0, o el valor disponible más bajo. |
| **Case index** | Ver el bloque exacto abajo. |

### Sobre el título

"Trazur Cursos" es una recomendación, no una decisión tomada en tu
nombre — el título no se discutió explícitamente en la ronda de
aprobación de contenido. El PDF fuente se titula "IA + UX Research en
Trazur Cursos"; si preferís ese título completo (o cualquier otro),
cambialo libremente — no afecta el contenido del cuerpo ni los bloques.

---

## Case Index — pegar en el campo "Case index"

```
Resumen|#overview
Contexto|#contexto
El problema|#problema
Objetivos y KPIs|#objetivos
Método|#metodo
IA en el análisis|#ia-analisis
Hallazgos clave|#hallazgos
Persona|#persona
Journey map|#journey
De los hallazgos a las decisiones|#decisiones
Prototipado y fidelidad|#prototipado
Propuesta de experiencia|#propuesta
Impacto esperado|#impacto-esperado
Limitaciones|#limitaciones
Próximos pasos|#proximos-pasos
Aprendizajes clave|#aprendizajes
Póster del proyecto|#poster
```

Las 17 líneas de arriba coinciden, una a una y en el mismo orden, con
los 17 atributos `anchor` de los bloques `estavillo/case-section` en
`trazur-gutenberg-es.html` — confirmado por validación automática (ver
el reporte de validación más abajo).

---

## Recomendación de hero layout

No existe todavía ninguna imagen real para este caso — los 9
placeholders de abajo están genuinamente pendientes.

- **Mejor candidata a imagen destacada una vez que exista:** el mockup
  de alta fidelidad (`{asset: trazur-mockup-alta-fidelidad-confirmacion}`,
  sección `#prototipado`) — es la pantalla final del producto propuesto y
  se lee bien como retrato/vertical en mobile.
- **Hero layout recomendado:** `split-right` (el default del theme).
- **Si la imagen destacada termina siendo horizontal** (por ejemplo, el
  diagrama de journey map o el diagrama de flujo, si preferís usar uno de
  esos): cambiar a `stacked`. Ver `estavillo-child/README.md` → "Hero
  layout options".

---

## Checklist de imágenes

Ninguna imagen existe todavía en el repositorio para este caso — las 9
entradas de contenido más el póster están genuinamente pendientes, no
"ya producidas pero sin enlazar".

| # | Placeholder (`placeholderLabel`) | Sección | Origen en el PDF | Nota de confidencialidad |
|---|---|---|---|---|
| 1 | `trazur-contexto-rural-ia` | `#contexto` | Ilustración conceptual generada con IA (gaucho + holograma de vaca) | Ninguna — arte generado, no hay dato real que redactar. |
| 2 | `trazur-estrategia-metodologica` | `#metodo` | Diagrama "Estrategia Metodológica" (3 columnas) | Ninguna. |
| 3 | `trazur-persona-juan-pablo` | `#persona` | Foto de la persona "Juan Pablo Techera" | Usar una foto genérica/stock que represente al arquetipo — no una foto de una persona real identificable sin su consentimiento. "Juan Pablo" es una persona arquetipo, no un productor real nombrado. |
| 4 | `trazur-journey-map` | `#journey` | Journey Map (swimlane de 6 etapas + curva de emoción) | Ninguna, salvo que el diagrama original incluya algún dato o nombre real — redactarlo en ese caso. |
| 5 | `trazur-wireframe-baja-fidelidad-inscripcion` | `#prototipado` | Wireframe Balsamiq, pantalla de inscripción | Ninguna — wireframe sin datos reales. |
| 6 | `trazur-wireframe-media-fidelidad-cursado` | `#prototipado` | Wireframe Relume, pantalla de cursado | Ninguna. |
| 7 | `trazur-mockup-alta-fidelidad-confirmacion` | `#prototipado` | Mockup hi-fi, confirmación de curso aprobado (misma imagen usada como hero — ver nota abajo) | Usar datos de ejemplo/semilla (nombre, % de progreso), no datos de un alumno real. |
| 8 | `trazur-flujo-ideal` | `#propuesta` | Diagrama de flujo ideal propuesto | Ninguna. |
| 9 | *(imagen destacada nativa, no está en el cuerpo)* | Hero | Composición mano + celular + vaca (mismo mockup hi-fi que #7) | Igual que #7. |
| — | Póster (`trazur-poster-tdg-2025.pdf`) | `#poster` (botón) | Póster oficial del Trabajo de Grado | Revisar que no incluya datos internos sensibles antes de subirlo a la Biblioteca de medios. |

**Nota sobre el ítem 9:** la imagen destacada (hero) y el placeholder #7
son, en el PDF original, la misma foto reutilizada dos veces. Podés
mantener esa reutilización o producir una imagen distinta para el hero —
está marcado como una afirmación a confirmar más abajo.

---

## Afirmaciones que todavía necesitan tu confirmación

Además de estar señaladas dentro del propio HTML, se listan acá
explícitamente:

1. **Las 5 metas del rediseño** (`#objetivos`, recordadas sin repetir el
   bloque de números en `#impacto-esperado`) — están etiquetadas como
   metas en todo el documento, nunca como resultados. Confirmá que el
   enfoque es el correcto, o ajustá los números.
2. **La síntesis honesta de límites de la IA en `#ia-analisis`**
   ("ChatGPT tendió a omitir barreras reales…") — es una síntesis
   editorial propia a partir de los datos de la tabla del PDF, no una
   cita textual. Confirmá que representa fielmente lo que encontraste.
3. **Campo nativo de equipo** — el CPT probablemente tiene campos Role/
   Tools/Period de un solo valor, pero no un campo "Equipo" de varias
   personas. El crédito a Renzo Morandi y Alejandra Capocasale está en
   el texto del cuerpo (`#overview`) en vez de en un campo nativo.
   Confirmá si alcanza así, o si querés pedir un campo nuevo (eso sería
   un ticket de infraestructura separado, no este).
4. **Case link URL y botón del póster** — ambos vacíos/placeholder
   (`href="#"`), por instrucción explícita de no inventar URLs.
   Confirmar antes de publicar.
5. **Imagen del hero duplicada con el mockup de alta fidelidad** (ítems
   7 y 9 del checklist) — confirmá si es intencional, o si preferís una
   imagen distinta para el hero.
6. **Título y slug recomendados** ("Trazur Cursos" / `trazur-cursos`) —
   no se discutieron explícitamente en la aprobación de contenido; son
   una recomendación, no una decisión tomada en tu nombre.

---

## Notas editoriales (resumen del proceso)

- Migrado desde el PDF del Trabajo de Grado, reorganizado en 17
  capítulos (el outline original de 20 se consolidó donde el PDF no
  tenía evidencia para un capítulo separado — detalle completo en el
  historial de esta conversación / commit).
- Las menciones a Synthetic Users se homogeneizaron en todo el
  documento: se presenta consistentemente como simulación exploratoria
  y generador de hipótesis, nunca como equivalente a analítica de
  comportamiento real o pruebas con usuarios reales.
- El bloque `case-stats` con las metas del rediseño aparece **una sola
  vez** (`#objetivos`) — `#impacto-esperado` no lo repite; en su lugar
  explica cómo se mediría el impacto real después de un despliegue
  (Analytics, Clarity, finalización de tareas, pruebas de usabilidad).
- Ningún hallazgo, herramienta, meta, dato de persona, etapa del
  journey o próximo paso del PDF fue eliminado — todo está representado
  en algún capítulo.
- Journey Map y Flujo ideal se mantienen como imagen (`case-figure`,
  variante `wide`) — no se reconstruyeron en HTML, según la política de
  imágenes aprobada.

---

## Publicación

1. En wp-admin: **Case Studies → Add New** (o abrir el borrador existente
   para este caso, si ya hay uno — no crear un duplicado).
2. Completar cada campo de la tabla "Campos nativos" arriba, en el meta
   box "Case details" y en la barra lateral (Excerpt, Case Tags).
3. Abrir el editor de bloques → **Opciones (⋮) → Code editor**. Pegar
   todo lo que está debajo del comentario instructivo en
   `trazur-gutenberg-es.html`. Volver a **Visual editor** y confirmar que
   las 17 secciones aparecen como bloques reales (Case Section, Columns,
   Case Figure, Case Stats, Case Decisions, Case Quote, Case Details,
   Case Timeline, Case Ladder, Table, Group, Buttons), sin ningún aviso
   de "contenido inesperado o inválido".
4. Pegar el bloque **Case Index** de arriba en el campo "Case index".
5. Dejar la imagen destacada vacía hasta tener una real (ver el
   checklist) — el marco placeholder del theme se ve bien sin nada
   configurado.
6. Dejar **Show on Home → Selected Work** y **Feature this case on
   Home** sin marcar, según la tabla de campos nativos.
7. Publicar (o actualizar), y revisar el frontend:
   - El índice sticky muestra las 17 entradas en orden, y cada link
     hace scroll a su sección correspondiente.
   - `#contexto` y `#persona` muestran una composición real de dos
     columnas en desktop y se apilan bien en mobile.
   - Las cards de Case Decisions (`#decisiones`), la grilla de Case
     Stats (`#objetivos`), el timeline (`#prototipado`) y los
     acordeones de Case Details (`#proximos-pasos`) renderizan
     correctamente; los acordeones abren y cierran.
   - La tabla de `#ia-analisis` y la de `#hallazgos` se pueden leer
     completas en mobile (scroll horizontal dentro de la tabla, sin que
     el texto se aplaste ni la página entera se desborde).
8. Una vez revisadas las "Afirmaciones que todavía necesitan tu
   confirmación" de arriba, volver a los campos de Home/Selected Work
   cuando exista una imagen destacada real.

Este ticket cubre solo la integración del contenido en español. La
versión en inglés queda pendiente, como tarea separada y posterior.
