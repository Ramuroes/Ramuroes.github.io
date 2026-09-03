# All Work / Portfolio Archive — arquitectura y estado

**Resumen en una línea:** la página existe, funciona y es data-driven desde
wp-admin. Lo que faltaba no era la página: era el camino a ella desde la Home,
y eso quedó arreglado en código. Lo que resta es cargar contenido.

> **Actualización — ticket "Work/Proyectos naming unification":** las URLs
> canónicas de esta página ya existen en producción y NO son las que este
> documento asumía originalmente (`/work/`, `/es/proyectos/` eran slugs
> "previstos", nunca confirmados). Las reales son **`/my-work/`** (EN) y
> **`/es/trabajos/`** (ES). Todo lo de abajo sigue siendo cierto en
> arquitectura; sólo las URLs de ejemplo estaban desactualizadas y ya se
> corrigieron. Ver `docs/handoff/WORK-NAMING-UNIFICATION.md` para el detalle
> completo de esa iteración (navegación, breadcrumb, CTAs, strings).

## Qué existe hoy

La página **Work** (`templates/page-work.php` → `template-parts/work-cases.php`)
ya es la vista de "todo el trabajo", con dos grupos separados y numerados como
el resto del sistema:

| Sección | Ancla | Qué muestra | Layout |
|---|---|---|---|
| `01 Selected work` | `#work` | Casos NO marcados como archivo | card ancha + grilla de 2 |
| `02 Archive` | `#archive` | Casos marcados "no mostrar en Home" | grilla de 3, cards más chicas |

Cada grupo desaparece por completo si está vacío, así que no hay títulos de
sección huérfanos mientras el archivo se llena.

## Cómo se decide en qué grupo cae un caso

**No hay campo nuevo ni taxonomía nueva.** Se reusa el flag que ya existía
para la Home:

- Case Study → caja **"Show this case in Home → Selected Work"**
  - **destildado** (`_es_case_show_on_home` = `'0'`) → va al **Archive**
  - **tildado, vacío, o el caso es anterior al campo** → va a **Selected work**

El default es "selected", así que un caso nuevo aparece donde se lo espera sin
configurar nada, y mandarlo al archivo es una decisión explícita de una sola
casilla.

Orden: `menu_order`, y a igualdad, título. O sea el campo **Orden** de
Atributos de página en cada Case Study manda; sin tocarlo, alfabético.

Idioma: la consulta es una `WP_Query` normal, que Polylang filtra por defecto.
Cada idioma ve sus propios casos, sin nada que sincronizar.

Datos por card: label, kicker, título, extracto, tags (taxonomía Case Tags),
URL (la del meta `_es_case_url` si está, si no el permalink) e imagen
destacada. Sin imagen se dibuja el marco placeholder del design system.

## Lo que estaba roto y se arregló

En la Home, el link **"All work"** de la sección Selected Work apuntaba a
`href="#"`. El filtro `es_home_view_all_url` existía desde Home v1 pero
**nadie lo puenteaba nunca** — ni el theme ni el plugin — así que lo que se
servía era el default crudo: un link muerto en la sección más importante de la
Home.

Ahora se resuelve por **template**, con el mismo mecanismo que el menú
(`es_page_url_by_template( 'templates/page-work.php' )`): la página Work del
idioma de la request, sin rutas escritas a mano. Si esa página todavía no
existe en ese idioma, el link **no se imprime** — un "All work" que apunta a
la misma sección que ya estás mirando es peor que no tenerlo.

El filtro sigue disponible por si alguna vez conviene apuntarlo a otro lado.

Escenarios verificados con las funciones reales sobre stubs de WP/Polylang:
página ES → resuelve la página con el template asignado en español; página EN
→ la de inglés; sin página en ese idioma → sin link; con un filtro puesto →
gana el filtro. En los cuatro casos la consulta sale con
`suppress_filters => false`, que es lo que habilita el filtrado por idioma de
Polylang. El mecanismo nunca hardcodea `/my-work/` ni `/es/trabajos/` en
ningún lugar: resuelve por template, así que si esas páginas cambiaran de
slug algún día no habría nada que tocar en el código.

## Lo que requiere acción manual (no se puede hacer desde código)

1. **Confirmar que `/my-work/` y `/es/trabajos/` tienen el template
   *"Estavillo — Work"* asignado.** Son páginas canónicas preexistentes, no
   nuevas — este repo no las crea. Si por lo que sea no tuvieran ese template
   asignado, el resolvedor no las encuentra y el link de "All work"/"Ver
   todos los proyectos" en la Home, el ítem de menú y el breadcrumb caen a su
   fallback (anchor `#work` en la Home / sin link fuera de ella) en vez de a
   la página real.
2. **Marcar los casos de archivo.** Hoy todos los Case Studies caen en
   "Selected work" porque ninguno tiene la casilla destildada. Mientras eso no
   cambie, la sección `02 Archive` no se imprime — que es correcto, no un bug.
3. **Cargar los casos que faltan.** El archivo está pensado para el trabajo
   anterior (académico, heredado, proyectos más chicos). Ese contenido todavía
   no existe como Case Studies publicados, y este repo no lo inventa.
4. **Los posts de prueba ("prueba", "preuab") deben quedar en Draft o
   Trash.** Las tres consultas que alimentan Home/Work
   (`es_portfolio_get_case_studies_for_home`,
   `es_portfolio_get_featured_case_for_home`,
   `es_portfolio_get_case_studies_for_work_page`) ya filtran
   `post_status => 'publish'` — un draft o un trasheado nunca aparece en
   ninguna de las dos, así que si hoy no se ven es porque ya están en ese
   estado. Esto es una verificación, no una corrección de código.

## Qué NO se hizo, y por qué

- **No se creó un CPT archive separado ni una taxonomía nueva.** Un flag
  booleano ya resuelve el problema y agregar una taxonomía sería una decisión
  de arquitectura con costo de mantenimiento permanente para dos grupos fijos.
- **No se agregó paginación a Work.** La consulta es `posts_per_page => -1`.
  Con la cantidad de casos previsible (unidades, no decenas) paginar
  fragmentaría la lectura sin ganancia. Si algún día hay decenas, el lugar
  para cambiarlo es `es_portfolio_get_case_studies_for_work_page()`, y la
  paginación del sistema ya está estilada (`.es-pagination` en `pages.css`).
- **No se agregaron filtros por tag en la página Work.** Las tags ya se
  muestran en las cards, pero filtrar exige decidir qué pasa con el estado en
  la URL, el historial y los lectores de pantalla. Es un ticket propio, no un
  agregado al pasar.
- **No se tocó el orden ni el contenido de ningún caso existente.**
