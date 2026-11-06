document.addEventListener('DOMContentLoaded', () => {
    // --- 1. FUNCIONALIDAD DEL OJITO DE CONTRASEÑA ---
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('password');

    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.textContent = '🔒';
            } else {
                passwordInput.type = 'password';
                toggleBtn.textContent = '👁️';
            }
        });
    }

    // --- 2. FUNCIONALIDAD DEL LOGIN ---
    const loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const correo = document.getElementById('correo').value;
            const password = document.getElementById('password').value;

            try {
                const respuesta = await fetch('/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ correo, password })
                });

                if (respuesta.redirected) {
                    window.location.href = respuesta.url;
                } else {
                    const resultado = await respuesta.text();
                    document.open();
                    document.write(resultado);
                    document.close();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error en la respuesta del servidor.');
            }
        });
    }

    // --- 3. FUNCIONALIDAD DEL REGISTRO ---
    const registroForm = document.getElementById('registroForm');

    if (registroForm) {
        registroForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const nombre = document.getElementById('nombre').value;
            const correo = document.getElementById('correo').value;
            const password = document.getElementById('password').value;

            try {
                const respuesta = await fetch('/registro', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nombre, correo, password })
                });

                const resultado = await respuesta.json();
                alert(resultado.message);
                
                if (resultado.status === 'success') {
                    window.location.href = '/login.html';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error en la respuesta del servidor.');
            }
        });
    }

    // --- 4. FUNCIONALIDAD DEL FORMULARIO DE ADOPCIÓN ---
    const formAdopcion = document.getElementById('formAdopcion');

    if (formAdopcion) {
        formAdopcion.addEventListener('submit', async (e) => {
            e.preventDefault();

            const nombre = document.getElementById('nombre').value;
            const correo = document.getElementById('correo').value;
            const telefono = document.getElementById('telefono').value;
            const mascota = document.getElementById('mascota').value;
            const motivo = document.getElementById('motivo').value;

            try {
                const respuesta = await fetch('/adopcion', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nombre, correo, telefono, mascota, motivo })
                });

                const resultado = await respuesta.json();

                if (resultado.status === 'success') {
                    const cardContenedor = document.getElementById('cardContenedor');
                    cardContenedor.innerHTML = `
                        <div style="text-align: center; padding: 20px;">
                            <h2 style="color: #27ae60; margin-bottom: 15px;">🎉 ¡Solicitud Enviada con Éxito!</h2>
                            <p style="color: #475569; font-size: 1.05rem; margin-bottom: 25px;">
                                Hemos recibido tu solicitud para adoptar a <strong>${mascota}</strong>. Nuestro equipo la evaluará muy pronto.
                            </p>
                            <a href="estatus_adopcion.html" style="display: inline-block; background-color: #27ae60; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                🔍 Ver Estatus de mi Solicitud
                            </a>
                        </div>
                    `;
                } else {
                    alert(resultado.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error en la respuesta del servidor.');
            }
        });
    }

    // --- 5. FUNCIONALIDAD DEL FORMULARIO DE REPORTES ---
    const formReporte = document.getElementById('formReporte');

    if (formReporte) {
        formReporte.addEventListener('submit', async (e) => {
            e.preventDefault();

            const ubicacion = document.getElementById('ubicacion').value;
            const descripcion = document.getElementById('descripcion').value;
            const urgencia = document.getElementById('urgencia').value;
            const contacto = document.getElementById('contacto').value;

            try {
                const respuesta = await fetch('/reporte', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ubicacion, descripcion, urgencia, contacto })
                });

                const resultado = await respuesta.json();

                if (resultado.status === 'success') {
                    const cardContenedor = document.getElementById('cardContenedor');
                    cardContenedor.innerHTML = `
                        <div style="text-align: center; padding: 20px;">
                            <h2 style="color: #27ae60; margin-bottom: 15px;">🚨 ¡Reporte Registrado con Éxito!</h2>
                            <p style="color: #475569; font-size: 1.05rem; margin-bottom: 25px;">
                                Muchas gracias por tu ayuda. Nuestro equipo de rescate ha recibido la ubicación y evaluará el caso a la brevedad para auxiliar al animalito.
                            </p>
                            <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                                <a href="inicio_usuario.html" style="background-color: #27ae60; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 0.95rem;">
                                    🏠 Volver al Inicio
                                </a>
                                <a href="adoptar.html" style="background-color: #64748b; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 0.95rem;">
                                    🐾 Ver Catálogo
                                </a>
                            </div>
                        </div>
                    `;
                } else {
                    alert(resultado.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error en la respuesta del servidor.');
            }
        });
    }
});