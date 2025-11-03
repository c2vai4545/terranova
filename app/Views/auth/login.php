<h1 class="text-center">Iniciar sesión</h1>
<?php if (!empty($error ?? '')): ?>
    <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <form id="login-form" method="post">
                    <div class="mb-3">
                        <label for="rut" class="form-label">RUT (8 dígitos)</label>
                        <input type="text" class="form-control" id="rut" name="rut" pattern="\d{8}" required>
                    </div>
                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" id="btnLogin" class="btn btn-dark">Iniciar sesión</button>
                    </div>
                </form>
                <script>
                    (function() {
                        const form = document.getElementById('login-form');
                        const btn = document.getElementById('btnLogin');
                        if (!form || !btn) return;
                        form.addEventListener('submit', async function(e) {
                            e.preventDefault();
                            const rut = document.getElementById('rut').value.trim();
                            const contrasena = document.getElementById('contrasena').value;
                            try {
                                const resp = await fetch('/api/login', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    credentials: 'include',
                                    body: JSON.stringify({
                                        rut,
                                        contrasena
                                    })
                                });
                                if (!resp.ok) {
                                    alert('Credenciales inválidas');
                                    return;
                                }
                                const data = await resp.json();
                                const sid = resp.headers.get('X-Session-Id') || (data && data.sid);
                                if (sid) {
                                    // Refuerza la cookie por si el navegador no persiste la de Set-Cookie
                                    document.cookie = 'PHPSESSID=' + sid + '; Path=/; SameSite=Lax';
                                }
                                // Verifica la sesión en el backend antes de redirigir
                                let perfil = data && data.idPerfil;
                                try {
                                    const meResp = await fetch('/api/me', {
                                        method: 'GET',
                                        headers: sid ? {
                                            'X-Session-Id': sid
                                        } : {},
                                        credentials: 'include'
                                    });
                                    if (meResp.ok) {
                                        const me = await meResp.json();
                                        if (me && typeof me.idPerfil !== 'undefined') {
                                            perfil = me.idPerfil;
                                        }
                                    }
                                } catch (_) {}
                                // Si tengo el perfil, redirijo directo; si no, delego al servidor
                                perfil = String(perfil || '');
                                if (perfil === '1') {
                                    window.location.replace('/admin');
                                    return;
                                }
                                if (perfil === '2') {
                                    window.location.replace('/worker');
                                    return;
                                }
                                window.location.href = '/after-login';
                            } catch (e) {
                                alert('Error de red');
                            }
                        });
                    })();
                </script>
            </div>
        </div>
    </div>