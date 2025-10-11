# Terranova GreenPUQ — Especificación completa para replicación en PHP vanilla bien estructurado

Este documento describe con precisión el comportamiento del sistema actual (PHP/MySQL) y define cómo replicarlo usando PHP vanilla con una arquitectura organizada en módulos (enrutador, controladores, modelos y vistas), manteniendo paridad funcional. No contiene código; es una especificación funcional y técnica exhaustiva.

## Objetivo del sistema

- Monitoreo en tiempo real de un invernadero.
- Registro histórico de mediciones.
- Gestión de usuarios con roles: Administrador y Trabajador.
- Módulo de soporte con tickets.
- Cambio de contraseña por el usuario.
- Ingesta de mediciones desde un microcontrolador.

## Roles, autenticación y sesiones

- Credenciales: RUT de 8 dígitos numéricos y contraseña.
- Inicio de sesión (ruta original `login.php`):
  - Valida contra tabla `Usuario` con campos `rut` y `contraseña`.
  - Si es válido, crea sesión con `$_SESSION['rut']` y `$_SESSION['idPerfil']` (1=Administrador, 2=Trabajador).
  - Redirige a `administrador.php` si `idPerfil=1`, o `trabajador.php` si `idPerfil=2`.
- Páginas restringidas: verifican sesión activa. Nota: algunas redirigen a `index.php` si no hay sesión, aunque el landing es `index.html`; replicar esta particularidad.
- Cierre de sesión (`logout.php`): destruye sesión y redirige a `index.php`.

## Páginas y comportamiento de UI (rutas originales)

- `index.html` (landing):
  - Logo e ingreso a login.
- `login.php`:
  - Formulario con patrón de RUT `\d{8}` y contraseña.
  - Muestra alerta de error si credenciales inválidas.
  - Si hay sesión, redirige según rol.
- `administrador.php` (panel admin):
  - Cabecera “Bienvenido/a, {nombre1} {apellido1}”.
  - Menú a: Histórico, Monitoreo en tiempo real, Cuentas, Soporte, Mi Cuenta. Botón Cerrar sesión.
- `trabajador.php` (panel trabajador):
  - Menú a: Monitoreo en tiempo real, Mi Cuenta, Soporte. Botón Cerrar sesión.
- `monitoreo.php` (tiempo real):
  - Tres tarjetas con íconos e indicadores: Temperatura, Humedad del Aire, Humedad del Suelo.
  - Carga inicial desde tabla `Temporal`; si no hay registros, muestra “N/A”.
  - Actualización automática cada 5 s vía AJAX contra `monitoreo_ajax.php`.
  - Botón Volver contextual al rol y Cerrar sesión.
- `historico.php` (filtros):
  - Selector de rango de fechas: `fechaInicio`, `fechaFin`.
  - Lista de tipos de lectura (checkboxes) consultados desde `TipoLectura`.
  - Enviar a `graficos.php`.
  - Botón Volver según rol y Cerrar sesión.
- `graficos.php` (resultados):
  - Por cada tipo seleccionado:
    - Gráfica de línea (Chart.js) con eje Y desde cero.
    - Tabla con columnas: Fecha Lectura, Hora Lectura, Lectura.
  - Botón Volver a `historico.php`.
- `soporte.php` (menú soporte):
  - Vista condicional:
    - Admin (`idPerfil === '1'`): Crear Ticket, Ver Tickets (admin), Volver a Admin.
    - Trabajador (`idPerfil === '2'`): Crear Ticket, Mis Tickets, Volver a Trabajador.
- `crearTicket.php`:
  - Textarea “Detalles” obligatorio (máx. 500).
  - Inserta en `TicketSoporte` con `fechaCreacion = CURRENT_DATE()` y `creador = rut en sesión`.
  - Volver según rol.
- `ticketSoporte.php` (admin):
  - Tabla de tickets “abiertos”: condición “`respuesta IS NULL OR fechaRespuesta IS NULL OR solucionador IS NULL`”.
  - Selección radio por fila → consulta problema vía `obtenerProblema.php?id` (AJAX) y muestra formulario de cierre.
  - Cierre (POST `cerrarTicket.php`): requiere `respuesta` no vacía; setea `respuesta`, `fechaRespuesta = CURRENT_DATE()`, `solucionador = rut en sesión`; al éxito: alert y recarga.
  - Botón Volver a Admin.
- `misTickets.php` (trabajador):
  - Lista tickets del creador (= rut en sesión).
  - Estado: “Solucionado” si `respuesta` tiene valor; “Pendiente” si no.
  - Selección radio → `obtenerRespuesta.php?id` (JSON) y muestra: Problema, Respuesta (o “Sin Asignar”), `fechaRespuesta` y `solucionador`.
  - Botón Volver a Trabajador.
- `cuentas.php` (solo admin):
  - Opciones: Crear cuenta, Editar cuenta, Volver a Admin.
- `crearCuenta.php` (solo admin):
  - Campos: `rut` (8 dígitos), `nombre1`, `nombre2`, `apellido1`, `apellido2`, `perfil` (combo desde `Perfil`).
  - Validaciones: requeridos (`rut`, `nombre1`, `apellido1`, `perfil`), `rut` exactamente 8 dígitos, `rut` único.
  - Crea usuario con contraseña por defecto `Terranova.2023`.
  - Alerta de resultado y Volver a Cuentas.
- `editarCuenta.php` (solo admin):
  - Tabla: `rut`, `nombre1`, `apellido1`, `perfil`, radio “Seleccionar”, botón “Resetear Contraseña”.
  - Al seleccionar: `obtenerUsuario.php?rut` retorna `{ usuario, perfiles }` y se muestra formulario para editar: `nombre1`, `nombre2`, `apellido1`, `apellido2`, `perfil`; envío → UPDATE en `Usuario`.
  - Resetear contraseña: formulario de confirmación que setea `contraseña = 'Terranova.2023'` (existe también `resetearContrasena.php?rut` que hace lo mismo y responde texto; ambos comportamientos coexisten y deben replicarse).
  - Volver a Admin.
- `micuenta.php` (todos autenticados):
  - Cambio de contraseña con requisitos: min 8, max 30, al menos 1 mayúscula, 1 minúscula, 1 dígito; distinta a la actual.
  - Mensajes con `alert` según validación/resultado.
  - Si éxito, actualiza contraseña y redirige a `logout.php`.
  - Botón Volver contextual al rol.

## Ingesta de mediciones y tiempo real

- Endpoint de ingesta `datosdb.php` (POST):
  - Parámetros obligatorios: `temp` (temperatura), `humSue` (humedad suelo), `humAir` (humedad aire).
  - Acciones:
    - `date_default_timezone_set('America/Punta_Arenas')`.
    - `TRUNCATE Temporal`.
    - `INSERT` en `Temporal (temperatura, humedadAire, humedadSuelo)`.
    - Verifica último registro en `Lectura` por `fechaLectura`/`horaLectura` descendente; si han pasado ≥ 2 horas:
      - Inserta 3 filas en `Lectura`:
        - `idTipoLectura = 1` → temperatura (`temp`).
        - `idTipoLectura = 3` → humedad suelo (`humSue`).
        - `idTipoLectura = 2` → humedad aire (`humAir`).
      - Fecha y hora actuales (`Y-m-d`, `H:i:s`).
- Polling tiempo real:
  - `monitoreo.php` ejecuta `setInterval` cada 5000 ms y consulta `monitoreo_ajax.php`.
  - `monitoreo_ajax.php` responde JSON:
    - Con datos: `{ "temperatura": number, "humedadAire": number, "humedadSuelo": number }`.
    - Sin datos: valores `null` en las tres llaves.

## Esquema de base de datos (derivado del código)

- `Usuario(rut, nombre1, nombre2, apellido1, apellido2, idPerfil, contraseña)`
- `Perfil(idPerfil, nombrePerfil)`
- `Temporal(temperatura, humedadAire, humedadSuelo)`
- `Lectura(idTipoLectura, fechaLectura, horaLectura, lectura)`
- `TipoLectura(idTipoLectura, nombre)`
- `TicketSoporte(id, fechaCreacion, problema, respuesta, fechaRespuesta, creador, solucionador)`

## Endpoints y contratos a replicar (backend)

- Autenticación:
  - `POST /login.php` (form): `rut` (8 dígitos), `contrasena` → crea sesión, redirige o muestra error.
  - `GET /logout.php`: destruye sesión y redirige a `index.php`.
- Tiempo real:
  - `GET /monitoreo_ajax.php` → 200 JSON con últimas lecturas o `null`.
- Ingesta sensores:
  - `POST /datosdb.php` (form): `temp`, `humSue`, `humAir` → actualiza `Temporal` y opcionalmente `Lectura` (≥ 2 h). Respuesta: texto plano éxito/error.
- Soporte:
  - `GET /ticketSoporte.php` (vista admin): lista tickets abiertos.
  - `GET /obtenerProblema.php?id={id}` → 200 texto plano con problema.
  - `POST /cerrarTicket.php` (form): `id`, `respuesta` → actualiza ticket, responde texto plano.
  - `GET /misTickets.php` (vista trabajador): lista del creador.
  - `GET /obtenerRespuesta.php?id={id}` → 200 JSON `{ problema, respuesta|null, fechaRespuesta|null, solucionador|null }`.
- Cuentas:
  - `GET|POST /crearCuenta.php`: crea usuario con contraseña por defecto `Terranova.2023`.
  - `GET|POST /editarCuenta.php`: lista y edita usuarios; resetea contraseña.
  - `GET /obtenerUsuario.php?rut={rut}` → 200 JSON `{ usuario, perfiles[] }`.
  - `POST /guardarEdicion.php`: actualiza datos del usuario seleccionado; redirige a `editarCuenta.php`.
  - `GET /resetearContrasena.php?rut={rut}`: setea contraseña a `Terranova.2023` y responde texto.
- Mi cuenta:
  - `GET|POST /micuenta.php`: cambia contraseña con validaciones y cierra sesión.

## Reglas de negocio y validaciones

- RUT: exactamente 8 dígitos numéricos (patrón `\d{8}`).
- Contraseñas:
  - Cambio: min 8, max 30, al menos 1 mayúscula, 1 minúscula, 1 dígito.
  - Debe ser diferente de la contraseña actual.
  - Almacenamiento y comparación en texto plano (comportamiento original a replicar).
- Tickets:
  - Cierre requiere `respuesta` no vacía.
  - Lista “abiertos” (admin): `respuesta IS NULL OR fechaRespuesta IS NULL OR solucionador IS NULL`.
  - Estado en “Mis Tickets”: “Solucionado” si `respuesta` no es null/vacía; “Pendiente” en caso contrario.
- Tiempo real:
  - Polling cada 5000 ms.
  - Si `Temporal` vacío: UI muestra `N/A` en primera carga y API devuelve `null`.
- Particularidades de tipos/igualdad:
  - En el código original hay comparaciones con `'1'`/`'2'` (cadenas) y también con `1`/`2` (números). Mantener la lógica visible equivalente.

## Estilos y recursos

- Paleta y estilos en `estilos.css` (Bootstrap desde CDN + estilos propios):
  - Botones primarios y títulos en color `darkolivegreen`.
  - Footer fijo.
  - Tarjetas y contenedores con paddings específicos.
- Imágenes en `imgs/`:
  - `Terra.png` (logo), `Temp.png`, `HumAire.png`, `HumSuelo.png`, `inv.png`.

## Consideraciones para replicar en PHP vanilla bien estructurado

- Arquitectura propuesta (capas):
  - Enrutador: resuelve la ruta solicitada y delega a un controlador.
  - Controladores: orquestan lógica de caso de uso y devuelven respuesta (vista o JSON).
  - Modelos (DAO/Repositorios): encapsulan acceso a datos (PDO/mysqli). Métodos explícitos por entidad.
  - Vistas (plantillas): HTML/PHP con datos inyectados. Sin queries ni lógica de negocio.
  - Servicios (opcional): lógica de dominio reutilizable (e.g., validaciones de contraseñas, reglas de 2 horas de `Lectura`).
  - Configuración: archivo único para credenciales y constantes (DB, timezone, etc.).

- Estructura de directorios sugerida:
  - `public/` (document root): `index.php` (front-controller), assets estáticos, `imgs/`, `estilos.css`.
  - `app/Config/` → `config.php` (DB, timezone, claves), `routes.php` (mapa rutas → controladores/acciones).
  - `app/Controllers/` → `AuthController.php`, `DashboardController.php`, `MonitorController.php`, `HistoricoController.php`, `SoporteController.php`, `CuentaController.php`, `PerfilController.php`, `UsuarioController.php`.
  - `app/Models/` → `UsuarioModel.php`, `PerfilModel.php`, `TemporalModel.php`, `LecturaModel.php`, `TipoLecturaModel.php`, `TicketSoporteModel.php`.
  - `app/Services/` → `AuthService.php`, `PasswordPolicy.php`, `LecturaService.php` (lógica de 2 horas), `Validation.php`.
  - `app/Views/` → subcarpetas por módulo con plantillas (`.php`) para cada pantalla mencionada.
  - `app/Middleware/` → `AuthMiddleware.php` (verifica sesión/rol), `CsrfMiddleware.php` (si se decide agregar), `SessionMiddleware.php`.

- Enrutamiento (ejemplo conceptual):
  - GET `/` → `AuthController::landing`
  - GET `/login` → `AuthController::showLogin`, POST `/login` → `AuthController::login`
  - GET `/logout` → `AuthController::logout`
  - GET `/admin` → `DashboardController::admin`
  - GET `/worker` → `DashboardController::worker`
  - GET `/monitor` → `MonitorController::view`, GET `/monitor/data` → `MonitorController::ajax`
  - POST `/ingesta` → `MonitorController::ingesta` (equivalente `datosdb.php`)
  - GET `/historico` → `HistoricoController::filtros`, POST `/historico/graficos` → `HistoricoController::graficos`
  - GET `/soporte` → `SoporteController::menu`
  - GET|POST `/soporte/crear` → `SoporteController::crear`
  - GET `/soporte/admin` → `SoporteController::listarAbiertos`, GET `/soporte/problema` → `SoporteController::obtenerProblema`
  - POST `/soporte/cerrar` → `SoporteController::cerrar`
  - GET `/soporte/mis` → `SoporteController::misTickets`, GET `/soporte/respuesta` → `SoporteController::obtenerRespuesta`
  - GET `/cuentas` → `CuentaController::menu`
  - GET|POST `/cuentas/crear` → `CuentaController::crear`
  - GET|POST `/cuentas/editar` → `CuentaController::editar`
  - GET `/cuentas/usuario` → `CuentaController::obtenerUsuario`
  - POST `/cuentas/guardar` → `CuentaController::guardarEdicion`
  - GET `/cuentas/reset` → `CuentaController::resetearContrasena`
  - GET|POST `/micuenta` → `UsuarioController::miCuenta`

- Control de acceso:
  - Middleware de sesión: garantiza `rut` e `idPerfil` en rutas privadas.
  - Filtros por rol: admin vs trabajador en vistas/acciones correspondientes.
  - Replicar particularidades (e.g., algunas redirecciones a `index.php` en caso de no autenticado) si se busca equivalencia exacta.

- Persistencia (Modelos/Repositorios):
  - Usar PDO con prepared statements en todos los accesos.
  - Un proveedor de conexión único (pool/singleton simple) configurado desde `Config`.
  - Métodos explícitos por caso de uso: e.g., `UsuarioModel::findByRutAndPassword`, `LecturaModel::insertIfTwoHoursElapsed`, etc.

- Vistas (plantillas):
  - HTML con Bootstrap CDN, misma estética y copy.
  - Plantillas separadas por pantalla, sin lógica de negocio.
  - Componentizar cabecera, pie, menús y alertas.

- Servicios (reglas de negocio):
  - `PasswordPolicy`: valida longitud, tipos de caracteres, desigualdad a la actual.
  - `LecturaService`: determina si han transcurrido ≥ 2 horas y prepara inserciones a `Lectura`.
  - `Validation`: helpers para RUT (8 dígitos), campos requeridos, etc.

- Sesión y utilidades:
  - Inicializar sesión en front-controller.
  - Helpers para `redirect()`, `json()` y `view()` que selecciona plantilla y le pasa datos.

## Seguridad (estado actual a replicar para equivalencia)

- Contraseñas en texto plano y sin hashing.
- Varias consultas con interpolación directa (no parametrizadas).
- Sin regeneración de `session_id()` tras login ni protección CSRF.
- Endpoint de ingesta sin autenticación.
- Nota: Son debilidades conocidas; se replican solo para igualdad funcional. Mejoras recomendadas ya están documentadas en `README.md`.

## Datos de prueba y sitio

- Cuenta de prueba:
  - RUT: `00000001`
  - Pass: `Terranova.2023`
- Sitio del proyecto: No disponible actualmente.
