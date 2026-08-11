# Presupuestador / Workshop Quoting System — mapa de contenido

Auditoría de todos los archivos relacionados con este caso en el repo
(ticket "PASADA GENERAL DE CIERRE", ítem 7). Sólo lectura — **ningún
archivo de contenido del caso fue modificado** al producir este mapa.

Hallazgo clave: el caso tuvo una actualización de contenido en inglés
(2026-07-16, "Workshop Quoting System") que **cambió título, slug y
narrativa** (de "App Alpha en curso" a "V1 deployed") y dejó un rastro de
archivos duplicados/desactualizados en inglés. **La versión en español NO
fue tocada por esa actualización** — sigue en su draft original
("Presupuestador", narrativa "App Alpha en curso"). Esto explica por qué
hay tantos archivos con nombres parecidos: no son alternativas
intercambiables, son dos generaciones distintas del contenido EN más el
contenido ES original, todavía sin su propia actualización.

## Tabla completa

| Archivo | Propósito | Estado | Fuente de verdad | Usar para qué | Legacy |
|---|---|---|---|---|---|
| `docs/content/presupuestador-case-study.html` | Primer borrador del cuerpo del caso, un solo idioma (texto en inglés), anterior al split ES/EN | Reemplazado | No | Sólo referencia histórica — no editar ni publicar desde acá | Sí |
| `docs/content/presupuestador-case-study-es.html` | Cuerpo maestro del caso en **español** — 13 secciones, mismos anchors que el Case Index, narrativa "App Alpha, en curso" | **Vigente** | **Sí (ES)** | Editar el texto del caso en español | No |
| `docs/content/presupuestador-case-study-en.html` | Cuerpo del caso en inglés, narrativa vieja "App Alpha" | Reemplazado por `workshop-quoting-system-gutenberg-en.html` | No | Sólo referencia histórica | Sí |
| `docs/content/presupuestador-case-study-fields.md` | Campos meta/nativos — tiene DOS secciones: "Spanish post" y "English post" — más una guía de hero layout | **Mixto** — ver nota | Sí, sólo la sección **"Spanish post"** y **"Featured image and hero layout recommendation"** (idioma-neutral). La sección **"English post"** quedó reemplazada por `workshop-quoting-system-fields-en.md` (título/slug/status/case-index viejos: dice "Presupuestador" y todavía incluye el paso "App Alpha" en el Case Index en inglés) | Campos meta del post ES; guía de hero layout para ambos idiomas | Parcial — sólo la sección "English post" es legacy, el resto sigue vigente |
| `docs/content/presupuestador-wordpress-publish.md` | Pasos de publicación paso a paso, método "pegar en un bloque Custom HTML" | Vigente para ES; el método/orden para EN quedó reemplazado por las instrucciones propias de `workshop-quoting-system-gutenberg-en.html` (que usa el "Code editor" del editor de bloques, no un bloque Custom HTML) | Sí, para los pasos del lado **ES** | Orden de publicación del post en español | Parcial — la parte EN es legacy |
| `docs/content/presupuestador-assets-plan.md` | Inventario de assets visuales pendientes: qué imagen falta, dónde se usa, aspect ratio exacto, crop mobile/desktop, confidencialidad, caption sugerido | **Vigente para ambos idiomas** — confirmado explícitamente por el propio `workshop-quoting-system-fields-en.md` ("nothing in it is outdated by this content update") | **Sí (ES + EN)** | Plan de assets compartido — no hay que duplicarlo por idioma | No |
| `estavillo-portfolio-core/patterns/presupuestador-es.php` | Pattern de bloques Gutenberg — transcripción fiel de `presupuestador-case-study-es.html` a bloques `estavillo/case-*`; registrado como pattern `estavillo/presupuestador-case-es` (ver `includes/case-patterns.php`) | **Vigente** | **Sí (ES)** | Insertar desde el Pattern inserter del editor para poblar el cuerpo del post ES con bloques reales y editables (alternativa recomendada a pegar HTML crudo) | No |
| `estavillo-portfolio-core/patterns/presupuestador-en.php` | Pattern de bloques Gutenberg — transcripción de la vieja `presupuestador-case-study-en.html` (narrativa "App Alpha") | **Reemplazado** — el propio `workshop-quoting-system-fields-en.md` lo nombra explícitamente como el draft que esta actualización supera | No | No usar. No existe todavía un pattern equivalente para el contenido EN nuevo (ver "Pendiente" abajo) | Sí |
| `docs/content/workshop-quoting-system-fields-en.md` | Campos meta EN actuales (título "Workshop Quoting System", slug, excerpt, tags, status "V1 deployed · Calibration in progress", tools, período) + Case Index + recomendación de hero layout + checklist de imágenes + lista de afirmaciones que necesitan confirmación del dueño del proyecto antes de publicar + instrucciones de publicación completas | **Vigente** | **Sí (EN)** | Campos meta del post en inglés + guía de publicación EN completa | No |
| `docs/content/workshop-quoting-system-gutenberg-en.html` | Cuerpo maestro del caso en **inglés**, narrativa actual "V1 deployed on Vercel, calibración en curso" — pensado para pegarse vía el "Code editor" del editor de bloques (no un bloque Custom HTML) | **Vigente** | **Sí (EN)** | Editar el texto del caso en inglés | No |

## Conclusiones (fuente de verdad final)

- **Para editar el caso en español, usar:**
  `docs/content/presupuestador-case-study-es.html` (texto del cuerpo) +
  `docs/content/presupuestador-case-study-fields.md` → sección **"Spanish
  post"** (campos nativos/meta). El pattern
  `estavillo-portfolio-core/patterns/presupuestador-es.php` es la
  transcripción en bloques de ese mismo HTML — mantenerlos en sync si se
  edita el texto.

- **Para editar el caso en inglés, usar:**
  `docs/content/workshop-quoting-system-gutenberg-en.html` (texto del
  cuerpo) + `docs/content/workshop-quoting-system-fields-en.md` (campos
  nativos/meta + Case Index + hero layout + checklist de imágenes +
  reclamos pendientes de confirmación).
  **NO usar** `presupuestador-case-study-en.html` ni
  `estavillo-portfolio-core/patterns/presupuestador-en.php` — ambos
  describen la narrativa vieja ("App Alpha", título "Presupuestador",
  status "In progress") que esta actualización reemplazó explícitamente.

- **Para publicarlo, usar:**
  - **ES:** seguir `docs/content/presupuestador-wordpress-publish.md`
    para el orden general de pasos; para el cuerpo del post, insertar el
    pattern registrado `estavillo/presupuestador-case-es` desde el Pattern
    inserter del editor de bloques (recomendado — produce bloques reales
    y editables) en vez de pegar el HTML crudo en un bloque Custom HTML.
  - **EN:** seguir la sección **"Publishing instructions"** de
    `docs/content/workshop-quoting-system-fields-en.md`, junto con el
    comentario de instrucciones que ya está dentro de
    `workshop-quoting-system-gutenberg-en.html` (pegar el contenido en el
    **"Code editor"** del editor de bloques, no en un bloque Custom HTML).
  - **Assets (ambos idiomas):** `docs/content/presupuestador-assets-plan.md`
    sigue siendo la referencia vigente para aspect ratios, crops y
    confidencialidad de cada imagen pendiente.

## Pendiente (fuera de alcance de este ticket)

- No existe todavía un pattern de bloques Gutenberg (`.php`) para el
  contenido EN nuevo (`workshop-quoting-system-gutenberg-en.html`) — sólo
  existe como HTML para pegar vía "Code editor". Si más adelante se
  quiere paridad ES/EN (insertar ambos desde el Pattern inserter en vez de
  pegar HTML), habría que transcribirlo a un nuevo
  `patterns/presupuestador-en.php` (o renombrarlo/registrarlo como
  `workshop-quoting-en.php`) y registrar un tercer pattern en
  `includes/case-patterns.php`. Esto sería contenido nuevo del caso — no
  se hizo acá porque el ticket pide explícitamente no modificar el
  contenido del caso Presupuestador todavía.
- El ES sigue sin la actualización de contenido que tuvo el EN
  (narrativa "V1 deployed" vs. "App Alpha en curso", título "Workshop
  Quoting System" vs. "Presupuestador"). Si se decide llevar el ES al
  mismo estado narrativo, es un trabajo de contenido nuevo, no un bugfix
  — no se tocó acá.
- `presupuestador-case-study-fields.md` conserva su sección "English
  post" desactualizada en el mismo archivo que su sección "Spanish post"
  (vigente). No se separó/limpió ese archivo en este ticket porque
  editar contenido del caso está fuera de alcance — queda documentado acá
  para quien lo edite después.
