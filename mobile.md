# Terranova GreenPUQ — Especificación para app móvil (React Native)

Objetivo: App móvil (iOS/Android) para ingresar manualmente lecturas del invernadero y consultar métricas actuales, con login (Recordarme) y navegación por rol (Administrador / Trabajador).

IMPORTANTE (estado de backend hoy):
- Implementado y protegido: `POST /api/login`, `POST /api/logout`, `GET /api/me`, `GET /api/monitor`, `GET /api/historico`, `POST /api/change-password` (todas protegidas por **JWT** vía cookie `jwt` o header `Authorization: Bearer <token>`).
- Ingesta unificada bajo mismo esquema **JWT**: `POST /api/ingesta` (equivalente a `/ingesta`).
- Pendiente: APIs de Soporte y Cuentas (CRUD completos) como servicios públicos.

## 1) Stack y librerías
- React Native 0.74+, TypeScript estricto.
- Navegación: @react-navigation/native (Stack + Bottom Tabs).
- Estado/requests: React Query (tanstack) con reintentos controlados.
- Storage: @react-native-async-storage/async-storage (flag Recordarme, rut, idPerfil).
- HTTP: fetch o axios; timeout 10s.
- Validación: zod o yup (RUT 8 dígitos, password política básica).
- Env: .env con API_BASE (ej. http://localhost:8000).
- Íconos/imágenes: empaquetar imgs/Temp.png, imgs/HumAire.png, imgs/HumSuelo.png, imgs/Terra.png dentro del proyecto móvil.

## 2) Rutas backend a usar
- POST ${API_BASE}/api/login — Body: { "rut": string(8), "contrasena": string } → 200: { rut, idPerfil } (crea sesión).
- POST ${API_BASE}/api/logout — 200: { ok: true } (requiere sesión).
- GET  ${API_BASE}/api/me — 200: { auth: true, rut, idPerfil } (requiere sesión).
- GET  ${API_BASE}/api/monitor — { temperatura|null, humedadAire|null, humedadSuelo|null } (requiere sesión).
- GET  ${API_BASE}/api/historico?start=YYYY-MM-DD&end=YYYY-MM-DD&tipos=1,2,3 (requiere sesión) — { start, end, series: [{ idTipoLectura, tipoNombre, data: [{ fechaLectura, horaLectura, lectura }] }] }.
- POST ${API_BASE}/api/ingesta — Body: { "temp": number, "humAir": number, "humSue": number } + **JWT** (cookie `jwt` o Header `Authorization: Bearer <token>`).

## 3) Autenticación (Login con “Recordarme”)
- Login real: llamar `POST /api/login` y guardar `{ rut, idPerfil }` si 200.
- **Autenticación**: al iniciar sesión el backend devuelve un JWT firmado dentro de una cookie httpOnly `jwt` y en el header `Authorization`. En React Native habilitar `withCredentials: true` (y, opcionalmente, copiar el token a AsyncStorage para enviarlo en headers en peticiones manuales).
- “Recordarme”: persistir `{ rut, idPerfil }` y, al abrir, validar con `GET /api/me`; si 401 → volver a Login.

## 4) Navegación y layout
- AppStack
  - AuthStack: LoginScreen.
  - WorkerStack (perfil 2): HomeWorker (Bottom Tabs) + hoja ProfileMenu.
  - AdminStack (perfil 1): HomeAdmin (Bottom Tabs) + hoja ProfileMenu.
- Navbar (ambos roles): título + ícono de perfil (derecha) → hoja con “Mi cuenta” y “Cerrar sesión”.
  - Mi cuenta: ChangePasswordScreen (UI/validación; llamada real pendiente).
  - Cerrar sesión: limpiar AsyncStorage y volver a Login.

## 5) Vistas por perfil
### 5.1 Trabajador (HomeWorker)
- Tab Monitoreo:
  - 3 tarjetas con ícono + valor: Temperatura (°C), Humedad Aire (%), Humedad Suelo (%).
  - Fuente: GET /api/monitor cada 5s (React Query; refetchInterval 5000). Estados: cargando, error (reintentar), N/A si null.
  - FAB “Ingresar lecturas” → abre formulario (ver 5.3).
- Tab Soporte: placeholder “Disponible próximamente”.

### 5.2 Administrador (HomeAdmin)
- Tab Histórico:
  - Filtros: rango de fechas (por defecto últimos 7 días) y checkboxes de tipos (Temperatura, Humedad Aire, Humedad Suelo).
  - Al aplicar filtros: llamar GET /api/historico y renderizar líneas/áreas (Victory/Charts a elección) mostrando etiquetas con `tipoNombre`.
- Tab Cuentas: placeholder “Disponible próximamente”.

### 5.3 Ingreso manual de lecturas (común)
- Formulario: Temperatura (−20 a 60; decimales OK), Humedad Aire (0–100), Humedad Suelo (0–100).
- Enviar: POST /api/ingesta (JSON, se envía automáticamente el JWT vía cookie o agregar header `Authorization: Bearer <token>` si se almacenó en AsyncStorage).
- Éxito: snackbar “Los datos se guardaron correctamente en la base de datos.” y cerrar sheet.
- Error: mensaje y opción reintentar.

### 5.4 Mi cuenta
- ChangePasswordScreen: UI con validación (min 8, mayúscula, minúscula, número). Enviar `POST /api/change-password` (requiere sesión).

## 6) Control de acceso por rol
- idPerfil=1 → Admin: tabs (Histórico, Cuentas).
- idPerfil=2 → Trabajador: tabs (Monitoreo, Soporte).
- FAB de ingreso visible en Monitoreo (ambos roles).

## 7) Estados/errores/timeouts
- Timeout global: 10s.
- Reintentos: GET /monitor/data hasta 2; POST /ingesta sin reintento automático.
- Offline: banner y reintento manual.

## 8) Estilos/Branding
- Paleta corporativa (coherente con web `estilos.css`):
  - Primario: `darkolivegreen` (#556B2F) para botones principales, headers y acentos.
  - Fondo: `#E6E6DD` para pantallas (body background).
  - Texto principal: `#212529` (negro/gris oscuro del sistema).
  - Texto secundario: `#6c757d` (coincide con `.footer-copy`).
  - Estados (sugerido): éxito `#198754`, advertencia `#FFC107`, error `#DC3545`.
- Tipografía: sistema (San Francisco en iOS, Roboto en Android). Usar peso medio para títulos y regular para cuerpo.
- Logos y recursos:
  - Usar `assets/imgs/Terra.png` como logo (navbar y splash). Ancho sugerido 40 px en nav.
  - Íconos de métricas: `Temp.png`, `HumAire.png`, `HumSuelo.png`.
- Componentes:
  - Botón primario: fondo `darkolivegreen`, texto blanco, borde 8 px, altura 48–56 px.
  - Tarjetas de métricas: fondo blanco, borde sutil `rgba(0,0,0,0.06)`, radio 12 px, padding 16.
  - Tabs inferiores: color activo `darkolivegreen`, inactivo gris medio.

## 9) Seguridad
- HTTPS cuando esté disponible.
- **JWT en cookie httpOnly protegida**; validar siempre con `/api/me`.
- **Todas las rutas protegidas requieren JWT** (cookie o header Authorization).
- No persistir contraseñas (solo { rut, idPerfil, remember }).

## 10) Estructura sugerida
```
src/
  api/
    monitor.ts   // GET /monitor/data
    ingesta.ts   // POST /ingesta
  screens/
    Auth/LoginScreen.tsx
    Worker/MonitorScreen.tsx
    Worker/SupportScreen.tsx
    Admin/HistoryScreen.tsx
    Admin/AccountsScreen.tsx
    Account/ChangePasswordScreen.tsx
  components/
    MetricCard.tsx
    ProfileMenu.tsx
    FloatingActionButton.tsx
  navigation/
    AppNavigator.tsx
  store/
    queryClient.ts
  assets/imgs/
    Temp.png HumAire.png HumSuelo.png Terra.png
```

## 11) Criterios de aceptación
- Login real contra `/api/login` con “Recordarme” y validación `/api/me`.
- Worker/Admin: Monitoreo usando `/api/monitor` con refresco 5s.
- Admin: Histórico consumiendo `/api/historico` con filtros.
- FAB realiza `POST /api/ingesta` con **JWT** (cookie `jwt` o Header `Authorization`).
- Navbar con perfil → Mi cuenta (`/api/change-password`) y Cerrar sesión (`/api/logout`).

## 12) Futuras integraciones
- Reemplazar mock de login por endpoint real (cookie/JWT) y proteger rutas.
- Exponer APIs públicas de Soporte, Cuentas, Histórico; cablear pantallas.
- Añadir refresh tokens y expiración de sesión.
