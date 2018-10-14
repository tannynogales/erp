SELECT 
CONTAR, producto_id, empresa_id, codigo, nombre, precio, costo, validado, eliminado, precio_mayorista, precio_web
FROM
(
	SELECT 
	COUNT(producto_id) AS 'CONTAR' , producto_id
	FROM
	( 
		SELECT producto_id
		FROM tanny_roble_erp.venta_has_producto
        UNION ALL
        SELECT producto_id 
        FROM tanny_roble_erp.dte_has_producto

	) AS A
	GROUP BY producto_id
) AS B
INNER JOIN producto
ON B.producto_id = producto.id
-- WHERE B.producto_id = 1120
ORDER BY B.CONTAR DESC 
;