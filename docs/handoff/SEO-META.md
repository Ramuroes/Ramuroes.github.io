# Títulos y meta descriptions — copy a cargar en Yoast

Esto es **documentación, no código**. El theme no escribe metadatos de SEO y
no va a hacerlo: Yoast ya es el dueño del `<title>`, la `meta description`,
el canonical y las Open Graph tags. Un theme que también los emitiera
produciría etiquetas duplicadas, que es peor que no tener ninguna.

Dónde se cargan: **al editar cada página → panel Yoast SEO (abajo del
editor) → "SEO title" y "Meta description"**. Con Polylang cada traducción
es un post distinto, así que **cada idioma se carga por separado**, en su
propia página.

## Por qué está esto acá

La auditoría encontró tres cosas en las pestañas y en los resultados de
búsqueda:

1. Páginas sin meta description, donde Google recorta un fragmento
   arbitrario del cuerpo.
2. Títulos con el naming viejo, incoherentes con la navegación actual.
3. El `<title>` en inglés en páginas servidas desde `/es/`.

Ninguna se arregla desde el theme. Las tres se arreglan pegando el copy de
abajo.

## Reglas del copy

- **SEO title**: hasta ~60 caracteres. Formato `Página · Ramiro Estavillo`,
  con el separador que ya usa el sistema visual.
- **Meta description**: 120–155 caracteres. Describe lo que la página
  realmente contiene. Nada de promesas, superlativos ni relleno.
- Sin rayas largas. Sin "apasionado", "impactante", "multidisciplinario".
- "Product Designer" queda en inglés también en español: es el nombre del
  puesto, no un descuido de traducción.

---

## Home

**EN** (`/`)

| Campo | Valor |
|---|---|
| SEO title | `Ramiro Estavillo · Product Designer` |
| Meta description | `Product designer working on systems and operations: quoting tools, internal processes and the decisions behind them. Case studies and process.` |

**ES** (`/es/`)

| Campo | Valor |
|---|---|
| SEO title | `Ramiro Estavillo · Product Designer` |
| Meta description | `Product designer enfocado en sistemas y operaciones: herramientas de presupuestado, procesos internos y las decisiones detrás. Casos y proceso.` |

## Work / Proyectos

**EN** (`/work/`)

| Campo | Valor |
|---|---|
| SEO title | `Work · Ramiro Estavillo` |
| Meta description | `A selection of product and systems design work, from live decision tools to earlier academic and legacy projects.` |

**ES** (`/es/proyectos/`)

| Campo | Valor |
|---|---|
| SEO title | `Proyectos · Ramiro Estavillo` |
| Meta description | `Una selección de trabajo de diseño de producto y sistemas, desde herramientas de decisión en producción hasta proyectos académicos anteriores.` |

## How I Work / Cómo trabajo

**EN** (`/how-i-work/`)

| Campo | Valor |
|---|---|
| SEO title | `How I Work · Ramiro Estavillo` |
| Meta description | `Six steps, with what each one is for and an example of where it changed the outcome. I don't start with interfaces. I start by understanding the system.` |

**ES** (`/es/como-trabajo/`)

| Campo | Valor |
|---|---|
| SEO title | `Cómo trabajo · Ramiro Estavillo` |
| Meta description | `Seis pasos, para qué sirve cada uno y un ejemplo de dónde cambió el resultado. No empiezo por las interfaces: empiezo por entender el sistema.` |

## About / Sobre mí

**EN** (`/about-me/`)

| Campo | Valor |
|---|---|
| SEO title | `About · Ramiro Estavillo` |
| Meta description | `Product designer in Montevideo, Uruguay. Experience across product design, UX research and project management, plus education, tools and languages.` |

**ES** (`/es/sobre-mi/`)

| Campo | Valor |
|---|---|
| SEO title | `Sobre mí · Ramiro Estavillo` |
| Meta description | `Product designer en Montevideo, Uruguay. Experiencia en diseño de producto, investigación UX y gestión de proyectos, además de formación e idiomas.` |

## Contact / Contacto

**EN** (`/contact/`)

| Campo | Valor |
|---|---|
| SEO title | `Contact · Ramiro Estavillo` |
| Meta description | `Open to Product Design, Design Systems and UX Research roles. Email, phone, WhatsApp and a short form, whichever is easiest.` |

**ES** (`/es/contacto/`)

| Campo | Valor |
|---|---|
| SEO title | `Contacto · Ramiro Estavillo` |
| Meta description | `Abierto a roles de Product Design, Design Systems e investigación UX. Email, teléfono, WhatsApp y un formulario corto, lo que resulte más cómodo.` |

## Case study — Trazur

**EN** (`/work/trazur/`)

| Campo | Valor |
|---|---|
| SEO title | `Trazur · Case study · Ramiro Estavillo` |
| Meta description | `Turning a quoting process spread across WhatsApp, spreadsheets and the workshop floor into one traceable system. Research, decisions and outcomes.` |

**ES** (`/es/proyectos/trazur/`)

| Campo | Valor |
|---|---|
| SEO title | `Trazur · Caso de estudio · Ramiro Estavillo` |
| Meta description | `Cómo un presupuestado repartido entre WhatsApp, planillas y el taller se convirtió en un sistema trazable. Investigación, decisiones y resultados.` |

---

## Lo que NO se toca

- **El `<title>` no sale de la caja "Page header"** del theme (esa controla
  el eyebrow, el H1 y la bajada que se ven en la página). Son dos cosas
  distintas: una es lo que lee la persona que ya llegó, la otra es lo que
  lee quien todavía está decidiendo si entra.
- **La imagen Open Graph** se define en Yoast → Social, por página. Si una
  página no la tiene, Yoast cae a la imagen social por defecto del sitio
  (Yoast → Settings → Site basics). Fuera del alcance de esta iteración:
  requiere elegir y subir las imágenes.
- **El canonical y el hreflang** los resuelven Yoast y Polylang juntos, sin
  intervención. No hay nada que cargar a mano, pero sí conviene verificar
  una vez con la vista de código fuente de una página `/es/` que el
  `hreflang` apunte a su par en inglés y viceversa.

## Cómo verificar

Después de cargar cada par, abrir la página en el front y mirar el código
fuente (`Ctrl+U`): tiene que haber **un solo** `<title>` y **una sola**
`<meta name="description">`, con el texto de la tabla. Si aparecen dos, hay
otro plugin de SEO activo además de Yoast y hay que desactivar uno.
