# CHANGELOG — Mi Tienda Online API

## [2.0.0] - 2026-06-07
### Añadido
- Búsqueda full-text en productos (GET /api/v2/productos?q=)
- Endpoint GET /api/v2/productos con paginación de 20 items por defecto

### Cambiado
- Paginación por defecto cambia de 15 a 20 items en v2
- Búsqueda más potente con soporte para término `q` en v2

## [1.0.0] - 2026-05-29
### Añadido
- CRUD completo de productos con subida de imágenes (Laravel Storage)
- CRUD de categorías con relación hasMany/belongsTo
- Autenticación con Laravel Sanctum (register, login, logout, me)
- Filtros avanzados: búsqueda, categoría, rango de precio, ordenamiento
- Paginación server-side con 15 items por página
- Sistema de roles: admin, editor, cliente
- Gates y Policies para control de permisos
- Validaciones con Form Requests y mensajes en español
- Jobs y Queues para envío de emails de confirmación de pedidos
- Notificaciones en tiempo real con Laravel Reverb (WebSockets)
- Documentación OpenAPI 3.0 con Swagger UI
