# Work / Proyectos — unificación de navegación y naming

Ticket: usar las dos páginas canónicas existentes —
**`https://www.ramiroestavillo.com/my-work/`** (EN) y
**`https://www.ramiroestavillo.com/es/trabajos/`** (ES) — como destino único
de "Work" / "Proyectos" en todo el portfolio. No se crearon páginas nuevas.

## 1. Qué cambió en código

**`estavillo-child/functions.php` → `es_nav_links()`.** El ítem "Work" del
menú era la ÚNICA excepción deliberada del sistema: seguía apuntando al
anchor `#work` de la Home porque, según el comentario que lo explicaba
("Work es la excepción DELIBERADA: todavía no hay una página índice de
proyectos..."), esa página índice todavía no existía. Ahora sí existe, así
que el ítem pasa a resolverse EXACTAMENTE igual que Cómo trabajo / Sobre mí /
Contacto, con la misma función ya probada en un commit anterior:

```php
'url' => es_nav_page_or_anchor( 'templates/page-work.php', '#work' ),
```

Esto resuelve por **template**, no por slug: busca la página publicada con
*"Estavillo — Work"* asignado, en el idioma de la request, y devuelve su
permalink real — sea cual sea ese slug. `/my-work/` y `/es/trabajos/` no
están escritos en ningún lugar del código.

**No se tocó nada más en código para este ítem** — el resto del sistema ya
estaba preparado para esto desde commits anteriores y simplemente empezó a
funcionar solo:

- **Estado activo del menú** (`es_nav_item_is_active()`,
  `inc/header-footer.php`): compara el path de la página actual contra el
  path del ítem cuando el ítem es una página real. Ya lo hacía para
  how/about/connect; ahora también para Work, sin cambios.
- **Breadcrumb en Case Studies** (`es_breadcrumb_trail( 'nav_work', ... )`,
  usado por `single-es_case_study.php`): ya resolvía la URL del crumb con
  `es_nav_resolve_url()` en vez de copiar el ítem crudo (ese bug — el crumb
  "Work" heredando el `#work` literal dentro de un Case Study — se arregló en
  un commit anterior). Con el ítem ahora apuntando a una página real, el
  crumb "Proyectos"/"Work" en cada Case Study lleva a `/es/trabajos/` o
  `/my-work/` en vez de a un anchor muerto.
- **CTA "Ver todos los proyectos" / "View all work" en la Home** (PHP
  fallback, `template-parts/selected-work.php`): ya se había arreglado en el
  commit anterior (apuntaba a `href="#"` literal) usando el mismo resolvedor
  por template. No requirió cambios en este ticket.
- **CTA "Ver caso destacado" / "View featured case"** (hero de la Home):
  sigue apuntando a `#featured`, sin tocar — es una acción distinta ("ver el
  caso destacado"), no "ver todo el archivo".

## 2. Qué NO se tocó en código (a propósito)

- **Selected Work vs Archive** (`es_portfolio_get_case_studies_for_work_page()`,
  plugin): el reparto ya es mutuamente exclusivo por el flag *"Show this case
  in Home → Selected Work"* — un caso featured/selected nunca puede aparecer
  también en Archive, por diseño. No había nada que arreglar. Detalle
  completo en `docs/handoff/ALL-WORK.md`.
- **Orden** (`orderby: menu_order title`): ya usa el campo nativo **Orden**
  de Atributos de página de cada Case Study, no la fecha. Ya cumplía lo que
  pedía el ticket.
- **Exclusión de drafts/trash**: las tres consultas que alimentan Home/Work
  ya filtran `post_status => 'publish'`. Un post en Draft o Trash nunca
  aparece en ninguna lista pública.

Estos tres puntos ya estaban resueltos por trabajo anterior; se verificaron
de nuevo para este ticket y siguen correctos.

## 3. Acciones manuales en WordPress

### 3.1 — Verificar el template de las páginas canónicas (hacer esto primero)

Todo lo de arriba depende de que **`/my-work/`** y **`/es/trabajos/`** tengan
el template *"Estavillo — Work"* asignado (Páginas → editar la página →
panel lateral → Plantilla). Si no lo tienen — por ejemplo si son páginas más
viejas que esta arquitectura —, el resolvedor no las encuentra y el menú, el
breadcrumb y el CTA de la Home caen a su fallback (`#work` en la Home, sin
link fuera de ella) en vez de llevar a la página real. Asignar el template
no es crear una página nueva, es aplicar el que ya existe en el theme.

### 3.2 — Strings de Polylang (Idiomas → Traducción de cadenas)

Estos valores ya estaban documentados como "recomendados" en
`docs/MULTILINGUAL-PARITY.md` desde una fase anterior — sólo hace falta
confirmarlos o cargarlos en esa pantalla, con el grupo **"Estavillo Child"**:

| Clave | Inglés (default) | Español — cargar este valor |
|---|---|---|
| `nav_work` | Work | **Proyectos** |
| `work_view_all` | All work | **Ver todos los proyectos** |
| `work_title` | Work. | **Proyectos.** |
| `work_lead` | A selection of product and systems design work… | **Una selección de proyectos de Product Design y sistemas, más trabajo anterior en diseño digital, industrial y visual.** |
| `work_label` | Selected work | Proyectos seleccionados *(sin cambios — ya es correcto)* |
| `breadcrumb_home` | Home | Inicio *(sin cambios — ya es correcto)* |

`nav_work` es el más importante de la lista: alimenta el ítem de menú (todas
las pantallas), el breadcrumb en Case Studies, y el aria-label del ítem
activo — un solo valor, tres lugares.

**Antes de cargar `work_title`/`work_lead`, revisar la caja "Page header"**
de cada página Work (debajo del editor). Si alguna ya tiene "Hero title" o
"Hero subtitle" escrito a mano, ese valor gana sobre el string de Polylang
(mismo mecanismo que el resto de las páginas fijas — ver
`docs/handoff/PAGE-HERO-FIELDS.md`) y el string nunca se va a ver. En ese
caso, editar el campo directamente ahí en vez de (o adema de) la traducción.

### 3.3 — El link "Ver todos los proyectos" / "All work" en el contenido vivo de la Home

Si la Home en producción corre como contenido Gutenberg (no el fallback PHP),
esa línea es texto plano pegado, no algo que el string de Polylang controle.
Ubicarla en el editor de cada Home (sección "Proyectos seleccionados" /
"Selected Work", el párrafo con el link chico arriba a la derecha de esa
sección) y setear su URL:

- ES → `/es/trabajos/`
- EN → `/my-work/`

Las plantillas de referencia (`docs/content/home-gutenberg-es.html` y
`docs/content/home-gutenberg-en.html`) ya tienen esta URL actualizada — se
puede usar como comparación, pero no hace falta re-pegar el archivo entero
por un solo link: alcanza con editar la URL de ese bloque en el editor.

### 3.4 — Casos de archivo, orden y posts de prueba

Ver `docs/handoff/ALL-WORK.md`, sección "Lo que requiere acción manual" —
sin cambios respecto a lo ya documentado ahí: marcar qué casos van a
Archive, cargar el trabajo anterior que falta, y confirmar que "prueba" /
"preuab" están en Draft o Trash (ya deberían estarlo, dado que las queries
sólo muestran publicados).

Para el orden aproximado que pide el ticket (Presupuestador, luego Trazur,
luego el resto), usar el campo **Orden** en Atributos de página de cada Case
Study: `0` o el número más bajo para Presupuestador, el siguiente para
Trazur, etc. Sin tocarlo, el orden es alfabético por título.

## 4. URLs a probar

**ES**
```
/es/inicio/
/es/trabajos/
/es/como-trabajo/
/es/sobre-mi/
/es/contacto/
/es/proyectos/trazur/  (o el slug real del case study)
```

**EN**
```
/
/my-work/
/how-i-work/
/about-me/
/contact/
(el case study Trazur real)
```

En cada una, confirmar:

- El ítem de menú dice "Proyectos" (ES) / "Work" (EN) y lleva a
  `/es/trabajos/` / `/my-work/`.
- Estando DENTRO de `/es/trabajos/` o `/my-work/`, ese ítem se ve activo
  (subrayado/resaltado) y en ningún otro lado.
- Menú mobile: mismo comportamiento que el desktop.
- Breadcrumb en el Case Study de Trazur: segundo crumb dice "Proyectos" /
  "Work" y lleva a la página archivo, no a un anchor.
- CTA "Ver todos los proyectos" / "All work" en la Home lleva a la página
  archivo. CTA "Ver caso destacado" / "View featured case" sigue llevando al
  caso destacado, no al archivo.
- Language switcher: cambiar de idioma estando en `/my-work/` lleva a
  `/es/trabajos/` y viceversa (esto lo resuelve Polylang de forma nativa vía
  "+ Agregar traducción" entre esas dos páginas — verificar que estén
  efectivamente linkeadas como traducción una de la otra).
- Página Work: eyebrow, H1 y subtítulo con el copy correcto por idioma;
  sección "01 Selected Work" con Presupuestador y Trazur, sin duplicarse en
  "02 Archive"; sin los posts de prueba en ningún lado.
