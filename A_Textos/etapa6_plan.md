# Plan de Implementación: Etapa 6 - Sistema de Colecciones

Este documento detalla las acciones y componentes necesarios para incorporar el sistema de Colecciones en Artfolio. El objetivo es permitir a los artistas agrupar sus obras temáticamente (ej: "Modelos 3D", "Concept Art") y mostrar dichas agrupaciones como pestañas filtrables en su perfil público.

---

## 1. Base de Datos y Modelos (Backend Core)

Para que las obras puedan pertenecer a una o varias colecciones, usaremos una relación de "Muchos a Muchos".

- **Migración `create_collections_table`:**
  - `id`
  - `user_id` (Relación con el usuario creador)
  - `name` (Nombre de la colección)
  - `description` (Opcional, descripción corta)
  - `timestamps`

- **Migración Pivot `artwork_collection_table`:**
  - `artwork_id` (Clave foránea)
  - `collection_id` (Clave foránea)
  - Clave primaria compuesta o `id` simple.

- **Modelos Eloquent:**
  - Crear modelo `Collection.php` definiendo la relación `artworks()` (`belongsToMany`) y `user()` (`belongsTo`).
  - Actualizar modelo `Artwork.php` añadiendo la relación `collections()` (`belongsToMany`).

## 2. Lógica de Negocio y Rutas (Controladores)

Necesitamos permitir que el artista gestione sus colecciones desde el panel privado.

- **Rutas:** Añadir resource para colecciones protegido por Auth en `routes/web.php` (`Route::resource('collections', CollectionController::class)`).
- **Controlador `CollectionController`:**
  - `index`: Muestra una lista de todas las colecciones del usuario.
  - `create` / `store`: Formulario y validación para guardar una nueva colección.
  - `edit` / `update`: Formulario y validación para modificar una colección.
  - `destroy`: Eliminar la colección (borra en cascada la relación en la tabla pivot, pero **no** elimina las obras en sí).

## 3. Vistas de Gestión del Artista (Dashboard)

El usuario necesita una interfaz para administrar esto de forma cómoda.

- **Navegación:** Agregar un enlace "Mis Colecciones" en el menú desplegable superior del usuario (`navigation.blade.php`).
- **Vista Index (`resources/views/collections/index.blade.php`):** Una tabla o cuadrícula elegante listando las colecciones existentes con botones de editar/eliminar.
- **Vista Create/Edit (`resources/views/collections/form.blade.php`):** Un formulario de diseño premium, siguiendo el glassmorphism y diseño oscuro actual, para poner el título de la colección.
- **Asignación en Obras:**
  - Actualizar los formularios de **Crear** y **Editar Obra** (`artworks/create.blade.php` y `edit.blade.php`) para incluir una sección de checkboxes donde el artista pueda seleccionar rápidamente a qué colecciones pertenece la obra que está subiendo.
  - Modificar `ArtworkController@store` y `update` para usar el método `sync()` y guardar estas relaciones en la base de datos.

## 4. Perfil Público y Pestañas de Filtrado (Front-End)

Aquí es donde la funcionalidad brilla de cara al público visitante.

- **Lógica en `PublicProfileController@show`:**
  - Obtener las colecciones que pertenecen a ese artista.
  - Recibir opcionalmente un parámetro (ej: `?collection_id=5`) por URL.
  - Si no hay parámetro, cargar el paginador con todas las obras. Si hay parámetro, cargar solo las obras de esa colección.

- **UI en `profile/show.blade.php` (Vista Pública):**
  - Justo debajo de la biografía del artista, incrustaremos una **Barra de Pestañas (Tabs) Responsiva**.
  - La primera pestaña dirá "Todas las obras" (Activa por defecto).
  - Las siguientes pestañas se generarán dinámicamente con los nombres de las colecciones del artista (ej: "Animación", "Fantasía").
  - Al hacer clic en una pestaña, se recarga sutilmente la página con el filtro aplicado, mostrando la cuadrícula de obras específica para esa colección. Se usará un diseño atractivo que resalte la pestaña activa (fondo resaltado, tipografía bold).

---

### Criterios de Éxito de la Etapa 6
1. Un usuario autenticado puede crear/editar colecciones desde su perfil.
2. Al subir/editar una obra, el usuario puede marcar casillas para asignarla a sus colecciones.
3. El perfil público de ese usuario muestra unas elegantes pestañas con las colecciones.
4. Al hacer clic en una colección, la galería pública se filtra correctamente mostrando solo el arte respectivo.
