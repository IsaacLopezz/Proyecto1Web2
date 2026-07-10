# Proyecto1Web2

LINK para la web
http://localhost/Proyecto1Web2/

Base de datos: proyecto1web2 (aprobada por el profe en clase)

---

## Estado de los modulos

cliente                 - LISTO
categoria               - LISTO
producto                - LISTO
factura                 - LISTO
detalle_factura         - LISTO

---

## Relaciones entre tablas

producto.cod_categoria → categoria.cod_categoria
factura.cedula_cliente → cliente.cedula
detalle_factura.num_factura → factura.num_factura
detalle_factura.cod_producto → producto.cod_producto

---

## Lo que tiene cada modulo

### Cliente
- CRUD completo con DataTable en español
- Formulario inline (sin modal)
- Busca por cedula, edita, elimina de forma logica

### Categoria
- CRUD completo con DataTable en español
- Formulario inline igual que clientes
- Archivos: modelo/Categoria.php, controlador/categoria_ajax.php, vista/categorias.php, assets/js/categorias.js

### Producto
- CRUD completo con DataTable en español
- Tiene relacion con categoria: se puede escribir el codigo de la categoria y se autocompleta el nombre (evento blur + AJAX)
- Boton lupa que abre un modal con la tabla de categorias para seleccionar una con clic
- Valida que precio sea numero mayor o igual a cero
- Valida que stock sea entero mayor o igual a cero
- Archivos: modelo/Producto.php, controlador/producto_ajax.php, vista/productos.php, assets/js/productos.js

### Factura
- Encabezado: buscar cliente por cedula con autocompletado (blur + AJAX) y modal de busqueda
- Detalle: seleccion de productos por codigo con autocompletado y modal de busqueda
- Tabla de detalle dinamica en el DOM con botones para editar cantidad y eliminar linea
- Calculo automatico de subtotales y total en tiempo real
- Guardar factura con transaccion mysqli (begin_transaction / commit / rollback)
- Si algo falla al guardar, se hace rollback y no queda nada a medias en la BD
- Listado de facturas guardadas con DataTable
- Ver detalle de cualquier factura en un modal
- Anular factura: cambia estado a 0 y devuelve el stock de cada producto
- Archivos: modelo/Factura.php, controlador/factura_ajax.php, vista/factura.php, assets/js/factura.js

---

## Sobre las eliminaciones

Cuando eliminas un cliente, categoria o producto desde la pagina, no se borra fisicamente de la base de datos.
Lo que hace es: UPDATE [tabla] SET estado = 0 WHERE [pk] = ?

Esto sirve principalmente para no hacer un desmadre con las facturas que ya existen.
Si borraras fisicamente un cliente que ya tiene facturas, la base de datos tiraria error por las foreign keys.
Con estado = 0 simplemente desaparece de la vista pero el registro sigue ahi guardado.

Si agregas de nuevo un cliente o categoria con el mismo dato que ya estaba eliminado, el sistema lo reactiva en vez de crear un duplicado.

---

## Estructura de carpetas

config/        -> global.php (credenciales) y Conexion.php (clase de conexion)
modelo/        -> una clase PHP por entidad
controlador/   -> endpoints AJAX que reciben peticiones y devuelven JSON
vista/         -> paginas HTML con Bootstrap + DataTables
assets/js/     -> un archivo JS por modulo
