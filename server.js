const express = require('express');
const app = express();
const path = require('path');

// Middlewares para procesar datos de formularios y JSON
app.use(express.urlencoded({ extended: true }));
app.use(express.json());

// Carpeta estática para servir tus archivos HTML, CSS e imágenes
app.use(express.static(path.join(__dirname)));

// Ruta principal (Página de bienvenida)
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index.html'));
});

// Ruta POST para procesar el Inicio de Sesión (Login)
app.post('/login', (req, res) => {
    const { correo } = req.body;
    let nombreUsuario = "Sofía Morales";
    if (correo && correo.toLowerCase().includes("dylan")) {
        nombreUsuario = "Dylan Pérez";
    }
    res.redirect(`/inicio_usuario.html?nombre=${encodeURIComponent(nombreUsuario)}`);
});

// Ruta POST para procesar el Registro de Usuario
app.post('/registro', (req, res) => {
    const { nombre_completo } = req.body;
    const nombreUsuario = nombre_completo || "Oliver";
    res.redirect(`/inicio_usuario.html?nombre=${encodeURIComponent(nombreUsuario)}`);
});

// Ruta POST para procesar la Solicitud de Adopción
app.post('/adoptar', (req, res) => {
    const { mascota } = req.body;
    res.redirect(`/solicitud_enviada.html?mascota=${encodeURIComponent(mascota || 'Canela (Canino)')}`);
});

// Ruta POST para procesar el Reporte Ciudadano
app.post('/enviar-reporte', (req, res) => {
    const { ubicacion } = req.body;
    res.redirect(`/reporte_enviado.html?ubicacion=${encodeURIComponent(ubicacion || 'Colonia Centro')}`);
});

// Puerto de ejecución del servidor
const PORT = 3000;
app.listen(PORT, () => {
    console.log(`Servidor Node.js corriendo en http://localhost:${PORT}`);
});