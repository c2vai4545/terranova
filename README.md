# Terranova GreenPUQ

Sistema web para gestión y monitoreo de un invernadero automatizado con mediciones en tiempo real y registro histórico. Proyecto implementado con PHP y MySQL, usando Bootstrap y Chart.js desde CDN.

## Descripción general

- Microcontrolador (en este proyecto: WeMos R1 WiFi) sensa temperatura, humedad del aire y humedad de suelo y controla actuadores (ventilación, riego, calefacción y deshumidificación).
- El microcontrolador envía los últimos valores al servidor mediante una petición HTTP a `datosdb.php`. El servidor:
  - Actualiza la tabla `Temporal` con la última lectura para consumo de la UI en tiempo real.
  - Cada 2 horas inserta lecturas en la tabla `Lectura` para el histórico, clasificadas por `idTipoLectura`.
- Los usuarios autenticados visualizan el monitoreo en vivo y, según rol, administran cuentas, consultan históricos y gestionan tickets de soporte.

## Arquitectura de alto nivel

- Frontend: páginas PHP con Bootstrap 4/5 (CDN) y JavaScript nativo.
- Backend: PHP (mysqli y PDO) con sesiones nativas.
- Base de datos: MySQL/MariaDB.
- Librerías externas: Chart.js (gráficas en histórico), Bootstrap (UI), CDN de jQuery/Popper/Bootstrap JS.

## Roles y autenticación

- Inicio de sesión en `login.php` por RUT (8 dígitos) y contraseña.
- Sesiones PHP con variables `$_SESSION['rut']` y `$_SESSION['idPerfil']`:
  - `idPerfil = 1`: Administrador.
  - `idPerfil = 2`: Trabajador.
- Redirecciones automáticas según rol (`administrador.php` o `trabajador.php`).

## Módulos del sistema (rutas web actuales)

- Landing: `/` (botón de login).
- Autenticación (web): `/login`, `/logout`.
- Panel administrador: `/admin` (Histórico, Monitoreo, Cuentas, Soporte, Mi cuenta).
- Panel trabajador: `/worker` (Monitoreo, Soporte, Mi cuenta).
- Monitoreo (web):
  - Vista: `/monitor`.
  - Datos usados por la vista: `GET /monitor/data` → `{ temperatura, humedadAire, humedadSuelo }`.
- Histórico (web):
  - Filtros: `/historico`.
  - Gráficos: `/historico/graficos`.
- Soporte (web): `/soporte`, `/soporte/crear`, `/soporte/admin`, `/soporte/mis`.
- Cuentas (web, admin): `/cuentas`, `/cuentas/crear`, `/cuentas/editar`.
- Mi cuenta (web): `/micuenta`.

## Estructura de base de datos (según el código)

- `Usuario(rut, nombre1, nombre2, apellido1, apellido2, idPerfil, contraseña)`
- `Perfil(idPerfil, nombrePerfil)`
- `Temporal(temperatura, humedadAire, humedadSuelo)`
- `Lectura(idTipoLectura, fechaLectura, horaLectura, lectura)`
- `TipoLectura(idTipoLectura, nombre)`
- `TicketSoporte(id, fechaCreacion, problema, respuesta, fechaRespuesta, creador, solucionador)`

## Flujo de datos de sensores

1) Microcontrolador → `POST /ingesta`.
   - Acepta `application/json`:
     - `{ "temp": number, "humAir": number, "humSue": number }`
   - Alternativamente `application/x-www-form-urlencoded` con campos `temp`, `humAir`, `humSue`.
2) El backend actualiza `Temporal` y, si corresponde (≥ 2 h), inserta en `Lectura` por tipo (1=temp, 2=hum aire, 3=hum suelo).
3) UI de Monitoreo (web) consulta `GET /monitor/data` cada 5 s → actualiza DOM.
4) Histórico consulta `Lectura` por rango y tipos seleccionados.

## Requisitos

- PHP 7.x o superior con extensiones mysqli y PDO.
- MySQL/MariaDB.
- Acceso a internet para los CDN de Bootstrap y Chart.js (o servirlos localmente si se desea).

## Configuración

Plantilla de configuración privada:
- Copie `app/Config/config.php.example` a `app/Config/config.local.php` y complete sus credenciales.
- `app/Config/config.local.php` está en `.gitignore` y no se versiona.
- Si no existe `config.local.php`, se usará `app/Config/config.php`.

Nota: la versión actual usa front‑controller y configuración centralizada (`app/Config/config.local.php`).

Sugerencia: centralizar credenciales en un archivo de configuración común e incluirlo para evitar duplicación y facilitar despliegue.

## Despliegue y ejecución local

Opción A (estructura original PHP):
1) Crear base de datos y tablas según el esquema deducido arriba.
2) Configurar credenciales en los archivos PHP listados.
3) Servir el proyecto mediante Apache/Nginx apuntando a la raíz actual.

Opción B (front-controller en `public/`):
1) Crear base de datos y tablas según el esquema deducido arriba.
2) Configurar credenciales en `app/Config/config.php`.
3) Levantar PHP embebido usando el router (para servir `/estilos.css` e imágenes):
   - `php -S localhost:8000 public/index.php`
4) Acceder a `http://localhost:8000/`.

## Seguridad y consideraciones

- Contraseñas con hashing bcrypt en `Usuario.contraseña` (password_hash/password_verify).
- Varias consultas construidas con interpolación directa. Recomendado: parametrizar todas las consultas (PDO/mysqli prepared statements) para mitigar inyección SQL.
- Sesiones sin regeneración de ID tras login ni controles de CSRF. Recomendado: regenerar `session_id()` al autenticarse y proteger formularios con tokens CSRF.
- El endpoint `datosdb.php` acepta POST sin autenticación. Recomendado: token de dispositivo, IP allowlist o firma HMAC.
- `TRUNCATE Temporal` en cada ingesta implica pérdida del último valor ante fallos de inserción. Alternativa: `DELETE`/`UPDATE` o `REPLACE INTO`.

## Recursos visuales

- Íconos e imágenes en `imgs/`.
- Estilos en `estilos.css`.

## Acceso de prueba

- RUT: `22222222`
- Pass: `Terranova.2023`

## Sitio del proyecto

No disponible actualmente.

## Estructura de archivos (principal)

- `public/index.php` (front‑controller/router)
- `app/Config/` (config y rutas)
- `app/Controllers/` (controladores web)
- `app/Models/` (modelos/DAO)
- `app/Services/` (servicios)
- `app/Views/` (vistas)
- `estilos.css`, `imgs/` (assets)

## Estado de API pública

Rutas disponibles para la app móvil (basadas en sesión PHP):

- `POST /api/login` → Inicia sesión.
  - Body JSON: `{ "rut": string(8), "contrasena": string }`
  - 200: `{ "rut": string, "idPerfil": 1|2 }`
  - 400/401 en error.
- `POST /api/logout` → Cierra sesión (requiere sesión). Respuesta: `{ "ok": true }`.
- `GET /api/me` → Estado de sesión (requiere sesión). Respuesta: `{ "auth": true, "rut", "idPerfil" }`.
- `GET /api/monitor` → Métricas actuales para UI (requiere sesión).
  - Igual a `GET /monitor/data` → `{ temperatura, humedadAire, humedadSuelo }`.
- `POST /api/ingesta` → Ingreso de lecturas (equivalente a `POST /ingesta`).
  - JSON: `{ "temp": number, "humAir": number, "humSue": number }`.
  - Seguridad: Header `Authorization: Bearer <API_KEY>` o `X-Api-Key: <API_KEY>`.
- `POST /api/change-password` → Cambiar contraseña (requiere sesión).
  - Body JSON: `{ "nuevaContrasena": string }`
  - 200: `{ "ok": true }`.
 - `GET /api/historico` → Series históricas (requiere sesión).
   - Query: `start=YYYY-MM-DD&end=YYYY-MM-DD&tipos=1,2,3` (tipos opcional; por defecto todos).
   - 200: `{ start, end, series: [{ idTipoLectura, tipoNombre, data: [{ fechaLectura, horaLectura, lectura }] }] }`.

-Notas:
- Estas rutas usan sesión/cookies; si no hay sesión devuelven 401 JSON.
- Endpoints de Soporte, Cuentas e Histórico como API externa siguen pendientes.

### Seguridad de ingesta
- Configurar la clave en `app/Config/config.local.php` bajo `api.ingesta_key`.
- Producción: rotar periódicamente y no exponerla en cliente si el dispositivo es comprometible.
