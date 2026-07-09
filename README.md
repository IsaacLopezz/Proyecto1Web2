# Proyecto1Web2

LINK para la web
http://localhost/Proyecto1Web2/


Base de datos segun la aprovacion del profe en clase

cliente                 - LISTO
categoria               - 
producto                -
factura                 -
detalle_factura         -

producto.id_categoria → categoria.id
factura.cedula_cliente → cliente.cedula
detalle_factura.id_factura → factura.id
detalle_factura.codigo_producto → producto.codigo




Una corta explicacion sobre el tema de eliminaciones 

Cuando eliminas un cliente desde la pagina, no se borra fisicamente de la base de datos
Lo que hace es trabajar con esto de aqui   UPDATE cliente SET estado = 0 WHERE cedula = ?
por eso cuando vemos en la BD todavia sigue apareciendo aunq cuando lo vemos en la pagina ya no esta, principalmente sirve 
cuando ya hicimos facturas con alguien y si despues la queremos borrar que no se haga un desmadre cuando se borra la persona pq sino las facturas con esa persona se puede hacer un error con la base de datos 

