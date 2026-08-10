# Pendiente manual — imagen del Journey Map en Trazur EN

**Estado:** no verificado. No lo toqué.

## Qué observé

En el contenido de Trazur EN, la figura del Journey Map y la de User Persona
apuntan a archivos que, por su nombre, parecen cruzados respecto de ES:

| Figura | ES (mediaId / archivo) | EN (mediaId / archivo) |
|---|---|---|
| `trazur-persona-juan-pablo` | 3121 / `Brainstorming-User-Persona-Experiencia-De-Usuario-Moderno-Azul-Amarillo.png` | 3198 / `user-persona.png` |
| `trazur-journey-map` | 2867 / `Journey-Map-Juan-Pablo.jpg` | 3199 / `Brainstorming-User-Persona-Experiencia-De-Usuario-Moderno-Azul-Amarillo.jpg` |

El archivo que EN usa como **Journey Map** (`Brainstorming-User-Persona-…`)
es, por nombre, el mismo que ES usa como **User Persona**.

## Por qué no lo corregí

No tengo salida de red hacia ramiroestavillo.com desde este entorno, así que
no pude abrir la página EN publicada ni descargar las imágenes para
compararlas. Un nombre de archivo no es evidencia suficiente: `mediaId 3199`
podría ser un recorte distinto, o el journey map real subido con un nombre
heredado de otra plantilla de Canva.

Corregirlo por inferencia sería reemplazar una imagen aprobada por otra sin
haberla visto.

## Qué hacer (2 minutos)

1. Abrir `/case-studies/trazur-cursos/` en **inglés**.
2. Ir a la sección **Journey Map**.
3. Mirar la imagen:
   - Si muestra un **mapa de recorrido** (seis etapas con acciones,
     pensamientos, puntos de dolor y oportunidades) → **está correcta, no
     tocar nada.**
   - Si muestra la **ficha de la persona** (retrato de Juan Pablo, datos
     demográficos) → está cruzada. En el editor, seleccionar el bloque Case
     Figure del Journey Map, usar **Replace** y elegir el mismo archivo que
     usa la versión ES: `Journey-Map-Juan-Pablo.jpg` (mediaId 2867).
4. De paso, verificar que la sección **Persona** de EN muestre el retrato y
   no el journey map.

## Nota

Las dos versiones comparten `placeholderLabel` a propósito (misma imagen
conceptual en los dos idiomas), así que usar el mismo mediaId en ES y EN es
consistente con la arquitectura del caso: no duplica assets.
