# Checklist de Correcciones para el Proyecto Terranova

## Métodos HTTP
- [ ] Estandarizar métodos HTTP según convenciones REST
  - [ ] Cambiar `GET /logout` a `POST /logout` para consistencia con la API
  - [ ] Revisar otras rutas que modifiquen estado y asegurar que usen POST, PUT o DELETE

## Autenticación
- [ ] Implementar autenticación basada en tokens para la API móvil
  - [ ] Añadir soporte para JWT o OAuth
  - [ ] Mantener compatibilidad con sesiones PHP existentes
- [ ] Unificar middlewares de autenticación
  - [ ] Crear un middleware base que pueda responder en diferentes formatos

## Validación de Entradas
- [ ] Reforzar validación de entradas en todas las rutas
  - [ ] Implementar validación consistente entre web y API
  - [ ] Añadir sanitización de datos
- [ ] Documentar requisitos de validación para cada ruta

## Nomenclatura y Estructura
- [ ] Estandarizar nomenclatura de rutas
  - [ ] Unificar nombres entre web y API (ej: `/soporte/crear` vs `/api/soporte/new`)
- [ ] Implementar versionado para la API
  - [ ] Mover rutas API a `/api/v1/`
  - [ ] Preparar estructura para futuras versiones

## Manejo de Errores
- [ ] Estandarizar formato de respuestas de error
  - [ ] Crear formato JSON consistente para errores de API
  - [ ] Implementar códigos de error específicos
- [ ] Mejorar mensajes de error para facilitar depuración

## Seguridad Adicional
- [ ] Implementar rate limiting para prevenir ataques de fuerza bruta
- [ ] Revisar y actualizar políticas de CORS
- [ ] Añadir encabezados de seguridad (Content-Security-Policy, X-XSS-Protection, etc.)

## Documentación
- [ ] Documentar todas las rutas API
  - [ ] Crear documentación OpenAPI/Swagger
  - [ ] Incluir ejemplos de solicitud y respuesta
- [ ] Actualizar documentación de rutas web