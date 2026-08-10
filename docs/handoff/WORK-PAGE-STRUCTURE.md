# Work / Proyectos — jerarquía Featured / Selected / Archive

Ticket: "Refine Work archive hierarchy and featured logic". Reestructura
`/my-work/` y `/es/trabajos/` en tres secciones numeradas dinámicamente,
Product Design primero. No toca Home, ni ningún otro template, ni el
sistema de navegación global (ese quedó resuelto en el ticket anterior,
"Work/Proyectos naming unification").

## 1. La jerarquía

```
01 — Featured Work    el caso marcado "Feature this case on Home"
02 — Selected Work     el resto de los casos "Show in Home"
03 — More Work         Case Studies de archivo (CPT) + el contenido viejo
                        pegado en la página
```

Un caso aparece en **una sola** sección — nunca dos. La numeración es
**dinámica**: si hoy no hay ningún caso featured, "Selected Work" pasa a
ser "01" (no queda un "02" huérfano sin "01" arriba). Verificado con los
cuatro escenarios reales: featured+selected+archive, sin featured, sin
ningún Case Study (sólo contenido legacy), y sin nada de nada (cae al
fallback de siempre, que también respeta esta numeración).

## 2. Featured — mismo flag que Home, comportamiento distinto

**Reutiliza el flag existente** "Feature this case on Home" — ningún
sistema paralelo, tal como pedía el ticket. La diferencia está en cómo se
usa cada dato:

- **En Home**, Featured Case y Selected Work son independientes: un caso
  puede aparecer en las dos secciones a la vez (documentado desde antes en
  el propio meta box: *"write the Excerpt to work for both sections if you
  also show this case in Selected Work"*). **Esto no cambia.**
- **En Work**, el featured se **excluye** de Selected/Archive — aparece una
  sola vez, arriba de todo, en su propia sección "01". Si por error hay más
  de un caso marcado featured, gana el primero por **Orden** (mismo
  desempate que ya usa Home) y el resto cae al split normal
  selected/archive — no se pierde, no se duplica.

Verificado con una tabla de 6 casos fake (incluye un "segundo featured por
error" y dos posts en draft/trash): Presupuestador sale featured y en
ningún otro lado; el segundo featured cae a archive por su propio flag;
drafts/trash no aparecen en ningún grupo.

## 3. Diseño

**Featured Work** reusa la card ancha (`.es-card--wide`) que ya usaba el
primer ítem de Selected Work — mismo componente, ya responsive (1 columna
en mobile, verificado). Muestra label/status, kicker, título, extracto,
tags y el CTA. **Selected Work** ahora es una grilla uniforme de 2
columnas (ya no separa un primer ítem ancho — ese rol pasó a ser
exclusivamente de Featured). **More Work** combina la grilla chica de
Case Studies de archivo (si hay) con el contenido viejo debajo, con un
separador visual (hairline + espacio) sólo cuando ambos coexisten.

## 4. El contenido viejo ("Algunos de mis trabajos")

**No se tocó ni se reescribió nada de lo que está pegado en la página.**
Antes se imprimía suelto al final, sin numerar ni relacionar visualmente
con el resto — el problema exacto que reportó el ticket. Ahora:

- Vive **dentro** de la sección "03 — More Work", con el mismo encabezado
  numerado que el resto del sitio.
- Tiene una **escala tipográfica reducida** y tinta más apagada
  (`--es-ink-2`/`--es-ink-3`, los mismos tokens ya verificados de contraste
  accesible en el commit anterior — nunca `opacity`, que bajaría el
  contraste texto/fondo) para que no compita al mismo nivel que Trazur o
  Presupuestador.
- Las imágenes tienen un tope de alto (340px) para que un logo o foto vieja
  no ocupe la pantalla completa al lado de cards de 250-300px.

**Esto es automático — no requiere ninguna acción en wp-admin.** Es un
cambio de template, no de contenido: al desplegar este commit, el mismo
contenido que ya está pegado en la página se reacomoda solo.

## 5. Categorías del archivo — primera versión

Cinco categorías fijas, agregadas como un **campo de meta** en el Case
Study (no una taxonomía nueva — ver el comentario de
`es_case_category_choices()` en el plugin para el razonamiento completo):
Product Design, Web & Digital, Industrial & 3D, Visual & Motion, Academic /
Experiments. Opcional — "sin categoría" es un estado válido.

**Dónde se ve:** sólo en las cards de archivo (CPT) de la sección "03",
como una pill pequeña bajo el título. **No** aparece en Featured/Selected
(esos ya comunican categoría/tipo con el campo "Eyebrow / category"
existente).

**Limitación honesta:** el contenido viejo pegado en la página (SAMIC,
French Bakery, webs anteriores, etc.) **no es un Case Study real** — es
HTML suelto en el body de la página, así que no tiene (ni puede tener
todavía) este campo. La categoría sólo sirve para Case Studies reales que
se marquen como archivo. Migrar ese contenido viejo a Case Studies propios
(con su categoría, su imagen destacada, su propio permalink) es el paso
natural siguiente, no parte de este ticket — así lo dejó abierto el propio
ticket ("documentar categorías como siguiente mejora" si hiciera falta;
acá se implementó la base de datos completa, sin bloquear la página).

**Explícitamente NO implementado:** filtros o tabs por categoría en el
frontend. Cero JS nuevo. Es la base de datos, lista para cuando haga falta
filtrar — el ticket lo permitía explícitamente si agregar la pieza
completa significaba más alcance del necesario.

## 6. Links

Cada card va al caso real (`_es_case_url` si está seteada, si no el
permalink del Case Study) — cero `href="#"` en ningún escenario probado.
El contenido legacy conserva los links que ya tenía (si tenía alguno) tal
cual estaban.

## 7. Acciones manuales en WordPress

### 7.1 — Confirmar el estado de Presupuestador y Trazur

Debería ya estar así, pero conviene confirmarlo una vez desplegado:

- **Presupuestador**: casilla "Feature this case on Home" ✅ tildada.
- **Trazur**: "Feature this case on Home" **destildada**, "Show this case
  in Home → Selected Work" tildada (o vacía, que es el default).

Si Presupuestador NO tiene la casilla de featured tildada hoy, va a
aparecer en "02 — Selected Work" en vez de "01 — Featured Work" — no es un
error, es exactamente la regla del punto 2 de arriba.

### 7.2 — Strings de Polylang (Idiomas → Traducción de cadenas)

Nuevos o actualizados desde el ticket anterior — tabla completa en
`docs/MULTILINGUAL-PARITY.md`:

| Clave | Español a cargar |
|---|---|
| `work_eyebrow` | Proyectos *(comparte traducción con `nav_work` — si ya cargaste esa, no hace falta cargar esta también)* |
| `work_featured_label` | Trabajo destacado |
| `work_lead` | Una selección de proyectos de Product Design y sistemas, junto con trabajos anteriores en diseño digital, industrial y visual. |
| `work_archive_label` | Más trabajos |
| `work_archive_lead` | Una selección de trabajos anteriores en diseño digital, industrial, 3D y visual. |

### 7.3 — Categorías (opcional, cuando haya casos de archivo reales)

En cada Case Study marcado como archivo, el nuevo select **"Archive
category"** dentro del meta box de siempre. Vacío por defecto — no hace
falta tocarlo si no se quiere categorizar todavía.

## 8. URLs a probar

```
/my-work/            /es/trabajos/
```

En cada una: sección "01" (Featured si hay caso marcado, si no arranca en
"Selected Work" numerada "01"), sin duplicados entre secciones, sección
"03" sólo si hay archivo CPT y/o contenido legacy, contenido legacy
visualmente más chico/apagado que las cards modernas, ningún `href="#"`,
sin overflow horizontal en mobile, Featured en 1 columna en mobile.

## 9. Qué se dejó explícitamente afuera (y por qué)

- **Filtros/tabs por categoría** — sin datos reales que filtrar todavía
  (ver §5), y el ticket permitía diferirlo.
- **Taxonomía en vez de meta field** — evaluado y descartado: 5 valores
  fijos sin jerarquía no justifican la maquinaria de una taxonomía nueva.
- **Migrar el contenido legacy a Case Studies reales** — el ticket pide
  conservarlo, no reconstruirlo. Reorganizar su jerarquía visual (hecho) es
  distinto de reescribirlo como datos estructurados (no pedido, no hecho).
- **Bug de hover del breadcrumb** — investigado con un test automatizado
  (hover + focus-visible, esperando a que termine la transición de color):
  el CSS ya cambia correctamente a `--es-accent` en ambos estados, con
  `!important` y especificidad suficiente. No se encontró ningún código que
  arreglar. Si el sitio en vivo todavía muestra el color apagado en hover,
  lo más probable es caché (navegador o de WordPress), no un defecto de
  este repositorio — vale la pena probarlo con un hard refresh antes de
  reabrir el ticket.
