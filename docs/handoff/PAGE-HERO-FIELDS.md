# Cabecera editable por página — valores a cargar

A partir de esta iteración, el encabezado de las páginas fijas (eyebrow,
título, subtítulo y label del breadcrumb) se edita **desde cada página**, en
la caja **"Page header (Estavillo)"** que aparece debajo del editor.

No hay que tocar PHP nunca más para cambiar estas frases.

## Por qué esto arregla la localización

Cada traducción es su propio post, así que su cabecera es su propio dato. Ya
no hay una cadena global compartida entre idiomas que pueda quedar a medio
traducir — que es exactamente lo que pasaba con el subtítulo de Cómo trabajo
(mostraba el texto en inglés dentro de la página en español).

## El código no escribe nada en la base de datos

Los campos arrancan vacíos y el theme cae a los textos aprobados de siempre.
Sembrarlos automáticamente sobre páginas que ya editaste sería pisarte
contenido. Hay que cargarlos una vez, a mano.

## Valores a cargar

### ES · Cómo trabajo (`/es/como-trabajo/`)
| Campo | Valor |
|---|---|
| Eyebrow | `PROCESO · 6 PASOS` |
| Hero title | `Cómo trabajo` |
| Hero subtitle | `No empiezo por las interfaces. Empiezo por entender el sistema.` |
| Breadcrumb label | *(vacío)* |

> Corrige de una vez: el H1 sin tilde ("Como trabajo"), el subtítulo en
> inglés, y la repetición breadcrumb + eyebrow + H1 con el mismo texto.

### EN · How I Work (`/how-i-work/`)
| Campo | Valor |
|---|---|
| Eyebrow | `PROCESS · 6 STEPS` |
| Hero title | `How I Work` |
| Hero subtitle | `I don't start with interfaces. I start by understanding the system.` |
| Breadcrumb label | *(vacío)* |

### ES · Sobre mí (`/es/sobre-mi/`)
| Campo | Valor |
|---|---|
| Eyebrow | `Product Designer · Sistemas y Operaciones` |
| Hero title | `Sobre mí.` |
| Hero subtitle | *(vacío)* |
| Breadcrumb label | *(vacío)* |

> "Product Designer" queda en inglés a propósito: es un título profesional,
> no un error de localización. Lo que sí estaba mal era "About me." y
> "Systems & Operations".

### EN · About (`/about-me/`)
| Campo | Valor |
|---|---|
| Eyebrow | `Product Designer · Systems & Operations` |
| Hero title | `About me.` |
| Hero subtitle | *(vacío)* |
| Breadcrumb label | *(vacío)* |

### ES · Contacto (`/es/contacto/`)
| Campo | Valor |
|---|---|
| Eyebrow | `CONTACTO` |
| Hero title | `Hablemos.` |
| Hero subtitle | *(vacío)* |
| Breadcrumb label | *(vacío)* |

> Reemplaza "Start a conversation." (que estaba en inglés dentro de ES) y
> elimina la duplicación del eyebrow "HABLEMOS" con el H2.

### EN · Contact (`/contact/`)
| Campo | Valor |
|---|---|
| Eyebrow | `CONTACT` |
| Hero title | `Let's talk.` |
| Hero subtitle | *(vacío)* |
| Breadcrumb label | `Contact` |

> Unifica el naming: la nav y el breadcrumb decían "Connect" mientras la URL
> y el `<title>` decían "Contact". El breadcrumb label lo fuerza a `Contact`.
> Para que la **nav** también diga "Contact", hay que cambiar la cadena
> `nav_connect` en **Polylang → Translations → Strings** (grupo "Estavillo
> Child"), no acá.

## Notas

- El campo **Hero title** acepta `<em>` para la palabra en acento, igual que
  el resto de los titulares del sistema.
- **Hero subtitle** vacío = no se renderiza el párrafo (no queda un hueco).
- **Breadcrumb label** vacío = el crumb usa el texto de la navegación, como
  hasta ahora.
- El `<title>` y el `og:title` de la pestaña/Google **no** salen de acá:
  los controla Yoast. Ver `docs/handoff/SEO-META.md`.
