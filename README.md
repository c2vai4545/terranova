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

## Módulos del sistema (rutas principales)

- Landing: `index.html` con acceso al login.
- Autenticación:
  - `login.php`: valida credenciales contra tabla `Usuario` y crea sesión.
  - `logout.php`: destruye sesión y vuelve a inicio.
- Paneles:
  - `administrador.php`: menú a Histórico, Monitoreo, Cuentas, Soporte, Mi Cuenta.
  - `trabajador.php`: menú a Monitoreo, Soporte, Mi Cuenta.
- Monitoreo en tiempo real:
  - `monitoreo.php`: muestra valores actuales de `Temporal` y se actualiza cada 5 s vía AJAX.
  - `monitoreo_ajax.php`: endpoint JSON que lee `Temporal` y retorna `{ temperatura, humedadAire, humedadSuelo }`.
- Ingesta de mediciones (desde microcontrolador):
  - `datosdb.php` (POST): parámetros `temp`, `humSue`, `humAir`.
    - `TRUNCATE Temporal` y `INSERT` de los últimos valores.
    - Si han pasado ≥ 2 horas desde el último registro en `Lectura`, inserta:
      - Temperatura con `idTipoLectura = 1`.
      - Humedad aire con `idTipoLectura = 2`.
      - Humedad suelo con `idTipoLectura = 3`.
    - Zona horaria: `America/Punta_Arenas`.
- Histórico de mediciones:
  - `historico.php`: selector de rango de fechas y tipos de lectura (desde tabla `TipoLectura`).
  - `graficos.php`: consulta `Lectura` y muestra:
    - Gráfica por tipo con Chart.js.
    - Tabla con `fechaLectura`, `horaLectura`, `lectura`.
- Soporte (tickets):
  - `soporte.php`: menú contextual por rol.
  - Crear ticket: `crearTicket.php` (POST → `TicketSoporte`).
  - Tickets abiertos (admin): `ticketSoporte.php` (lista, ver problema, cerrar):
    - Obtiene problema: `obtenerProblema.php?id`.
    - Cierre: `cerrarTicket.php` (POST: `id`, `respuesta`) → setea `respuesta`, `fechaRespuesta`, `solucionador`.
  - Mis tickets (trabajador): `misTickets.php` (lista, ver detalle):
    - Obtiene detalle: `obtenerRespuesta.php?id` (JSON: problema, respuesta, fecha, solucionador).
- Cuentas de usuario (solo administrador):
  - `cuentas.php`: navegación para crear/editar.
  - `crearCuenta.php`: inserta en `Usuario` con contraseña por defecto `Terranova.2023`.
  - `editarCuenta.php`: lista usuarios, permite editar campos básicos y perfil; incluye reseteo de contraseña.
  - Servicios auxiliares: `obtenerUsuario.php` (datos + perfiles en JSON), `guardarEdicion.php`, `resetearContrasena.php`.
- Mi cuenta (cambio de contraseña):
  - `micuenta.php`: valida requisitos y actualiza contraseña del usuario autenticado; cierra sesión al finalizar.

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
3) UI de Monitoreo consulta `GET /monitor/data` cada 5 s → actualiza DOM.
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

Para la versión original (sin front-controller), las credenciales están embebidas en los scripts (marcadores “QUITADO POR SEGURIDAD”). Debe editarse cada archivo que realiza conexión a BD y establecer `host`, `dbname`, `username`, `password`:

- `login.php`
- `monitoreo.php`
- `monitoreo_ajax.php`
- `datosdb.php`
- `historico.php`
- `graficos.php`
- `ticketSoporte.php`
- `misTickets.php`
- `crearTicket.php`
- `obtenerProblema.php`
- `cerrarTicket.php`
- `obtenerRespuesta.php`
- `cuentas.php` (navegación)
- `crearCuenta.php`
- `editarCuenta.php`
- `obtenerUsuario.php`
- `guardarEdicion.php`
- `resetearContrasena.php`
- `micuenta.php`

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

- Contraseñas en texto plano en `Usuario.contraseña` y durante login/cambio de clave. Recomendado: usar hashing seguro (password_hash/password_verify) y política de renovación.
- Varias consultas construidas con interpolación directa. Recomendado: parametrizar todas las consultas (PDO/mysqli prepared statements) para mitigar inyección SQL.
- Sesiones sin regeneración de ID tras login ni controles de CSRF. Recomendado: regenerar `session_id()` al autenticarse y proteger formularios con tokens CSRF.
- El endpoint `datosdb.php` acepta POST sin autenticación. Recomendado: token de dispositivo, IP allowlist o firma HMAC.
- `TRUNCATE Temporal` en cada ingesta implica pérdida del último valor ante fallos de inserción. Alternativa: `DELETE`/`UPDATE` o `REPLACE INTO`.

## Recursos visuales

- Íconos e imágenes en `imgs/`.
- Estilos en `estilos.css`.

## Acceso de prueba

- RUT: `00000001`
- Pass: `Terranova.2023`

## Sitio del proyecto

No disponible actualmente.

## Estructura de archivos (principal)

- `index.html` (inicio)
- `login.php`, `logout.php` (autenticación)
- `administrador.php`, `trabajador.php` (paneles)
- `monitoreo.php`, `monitoreo_ajax.php`, `datosdb.php` (monitoreo e ingesta)
- `historico.php`, `graficos.php` (histórico y gráficas)
- `soporte.php`, `crearTicket.php`, `ticketSoporte.php`, `misTickets.php` (soporte)
- `obtenerProblema.php`, `cerrarTicket.php`, `obtenerRespuesta.php` (APIs soporte)
- `cuentas.php`, `crearCuenta.php`, `editarCuenta.php`, `obtenerUsuario.php`, `guardarEdicion.php`, `resetearContrasena.php` (gestión de cuentas)
- `micuenta.php` (cambio de contraseña)
- `estilos.css`, `imgs/` (assets)
