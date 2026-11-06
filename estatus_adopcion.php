<?php
require_once 'conexion.php';
$resultado = null;
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = filter_var(trim($_POST['correo'] ?? ''), FILTER_SANITIZE_EMAIL);

    if (empty($correo)) {
        $error = "Por favor ingresa tu correo electrónico.";
    } else {
        $stmt = $conexion->prepare("SELECT * FROM solicitudes_adopcion WHERE correo = :correo");
        $stmt->execute([':correo' => $correo]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$resultado) {
            $error = "No se encontró ninguna solicitud de adopción registrada con este correo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patitas Recicladas - Estatus de Adopción</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <nav>
        <a href="inicio_usuario.html" class="logo">🐾 Patitas Recicladas</a>
        <div class="nav-links">
            <a href="inicio_usuario.html">Inicio</a>
            <a href="adoptar.html">Adoptar</a>
            <a href="reportes.html">Reportar Calle</a>
            <a href="proyecto.html">Sobre el Proyecto</a>
        </div>
    </nav>

    <header class="hero" style="padding: 40px 20px;">
        <h1>🔍 Consulta el Estatus de tu Adopción</h1>
        <p>Ingresa el correo con el que realizaste tu solicitud para conocer su avance.</p>
    </header>

    <div class="container" style="max-width: 500px;">
        <div class="card">
            <h2 style="color: #27ae60; margin-bottom: 20px; text-align: center;">Buscar Solicitud</h2>
            
            <form method="POST" novalidate>
                <div class="form-group">
                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" placeholder="ejemplo@correo.com" required>
                </div>

                <button type="submit" style="background-color: #27ae60;">Consultar Estatus</button>
            </form>

            <?php if (!empty($error)): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-top: 20px; text-align: center; font-weight: bold; font-size: 0.9rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($resultado): ?>
                <div style="margin-top: 30px; padding: 20px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0;">
                    <h3 style="margin-bottom: 15px; color: #1e293b;">Hola, <?php echo htmlspecialchars($resultado['nombre_adoptante']); ?></h3>
                    <p style="margin-bottom: 10px;"><strong>Mascota solicitada:</strong> <?php echo htmlspecialchars($resultado['nombre_mascota']); ?></p>
                    <p style="margin-bottom: 15px;"><strong>Estatus actual:</strong> 
                        <span style="font-weight: bold; color: <?php 
                            echo ($resultado['estado'] == 'Aprobado') ? '#27ae60' : (($resultado['estado'] == 'Rechazado') ? '#b71c1c' : '#d97706'); 
                        ?>;">
                            <?php echo $resultado['estado'] ?? 'Pendiente'; ?>
                        </span>
                    </p>

                    <?php if (($resultado['estado'] ?? 'Pendiente') == 'Aprobado'): ?>
                        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; font-size: 0.9rem;">
                            🎉 <strong>¡Muchas felicidades! Tu solicitud ha sido aprobada.</strong><br>
                            Puedes pasar por <strong><?php echo htmlspecialchars($resultado['nombre_mascota']); ?></strong> a nuestro refugio principal este próximo sábado en un horario de 10:00 AM a 2:00 PM. No olvides traer una identificación oficial y una correa o transportadora segura. ¡Te esperamos!
                        </div>
                    <?php elseif (($resultado['estado'] ?? 'Pendiente') == 'Rechazado'): ?>
                        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; font-size: 0.9rem;">
                            ❌ Lamentablemente tu solicitud no pudo ser aprobada en esta ocasión debido a que los datos proporcionados no cumplieron con los requisitos indispensables del refugio. Agradecemos mucho tu interés por ayudar.
                        </div>
                    <?php else: ?>
                        <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 6px; font-size: 0.9rem;">
                            ⏳ Tu solicitud se encuentra en revisión por parte del comité del refugio. Te pedimos estar atento a esta página para conocer la respuesta muy pronto.
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>