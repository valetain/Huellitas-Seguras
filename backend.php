<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'conexion.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $accion = $_POST['accion'] ?? '';

        if ($accion === 'registro') {
            $nombre = trim($_POST['nombre'] ?? '');
            $correo = filter_var(trim($_POST['correo'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($nombre) || empty($correo) || empty($password)) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("El correo electrónico no es válido.");
            }

            if (strlen($password) < 6) {
                throw new Exception("La contraseña debe tener al menos 6 caracteres.");
            }

            // Verificar si el correo ya está registrado
            $stmtCheck = $conexion->prepare("SELECT id FROM usuarios WHERE correo = :correo");
            $stmtCheck->execute([':correo' => $correo]);
            if ($stmtCheck->rowCount() > 0) {
                throw new Exception("Este correo ya está registrado.");
            }

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $sql = "INSERT INTO usuarios (nombre, correo, password) VALUES (:nombre, :correo, :password)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':nombre' => htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'),
                ':correo' => $correo,
                ':password' => $passwordHash
            ]);

            echo json_encode(["status" => "success", "message" => "¡Cuenta creada con éxito! Redirigiendo..."]);
            exit;

        } elseif ($accion === 'login') {
            $correo = filter_var(trim($_POST['correo'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($correo) || empty($password)) {
                throw new Exception("Por favor ingresa tu correo y contraseña.");
            }

            $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = :correo");
            $stmt->execute([':correo' => $correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($password, $usuario['password'])) {
                // GUARDAMOS LOS DATOS EN LA SESIÓN DE PHP
                session_start();
                $_SESSION['usuario_correo'] = $usuario['correo'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];

                echo json_encode(["status" => "success", "message" => "¡Bienvenido, " . $usuario['nombre'] . "!"]);
            } else {
                throw new Exception("Correo o contraseña incorrectos.");
            }
            exit;

        } elseif ($accion === 'adopcion') {
            $nombre = trim($_POST['nombre'] ?? '');
            $correo = filter_var(trim($_POST['correo'] ?? ''), FILTER_SANITIZE_EMAIL);
            $telefono = trim($_POST['telefono'] ?? '');
            $mascota = trim($_POST['mascota'] ?? '');
            $motivo = trim($_POST['motivo'] ?? '');

            if (empty($nombre) || empty($correo) || empty($telefono) || empty($mascota) || empty($motivo)) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            // Validar si este correo ya tiene una solicitud registrada
            $stmtCheck = $conexion->prepare("SELECT id FROM solicitudes_adopcion WHERE correo = :correo");
            $stmtCheck->execute([':correo' => $correo]);
            if ($stmtCheck->rowCount() > 0) {
                echo json_encode(["status" => "error", "message" => "Ya cuentas con una solicitud registrada con esta cuenta."]);
                exit;
            }

            $sql = "INSERT INTO solicitudes_adopcion (nombre_adoptante, correo, telefono, nombre_mascota, motivo) VALUES (:nombre, :correo, :telefono, :mascota, :motivo)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':nombre' => htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'),
                ':correo' => $correo,
                ':telefono' => htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8'),
                ':mascota' => htmlspecialchars($mascota, ENT_QUOTES, 'UTF-8'),
                ':motivo' => htmlspecialchars($motivo, ENT_QUOTES, 'UTF-8')
            ]);

            echo json_encode(["status" => "success", "message" => "¡Solicitud registrada con éxito!"]);
            exit;

        } elseif ($accion === 'reporte') {
            $ubicacion = trim($_POST['ubicacion'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $urgencia = trim($_POST['urgencia'] ?? '');
            $contacto = trim($_POST['contacto'] ?? '');

            if (empty($ubicacion) || empty($descripcion) || empty($urgencia) || empty($contacto)) {
                throw new Exception("Todos los campos del reporte son obligatorios.");
            }

            $sql = "INSERT INTO reportes_calle (ubicacion, descripcion, urgencia, contacto) VALUES (:ubicacion, :descripcion, :urgencia, :contacto)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':ubicacion' => htmlspecialchars($ubicacion, ENT_QUOTES, 'UTF-8'),
                ':descripcion' => htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8'),
                ':urgencia' => htmlspecialchars($urgencia, ENT_QUOTES, 'UTF-8'),
                ':contacto' => htmlspecialchars($contacto, ENT_QUOTES, 'UTF-8')
            ]);

            echo json_encode(["status" => "success", "message" => "¡Alerta ciudadana registrada con éxito!"]);
            exit;
        }
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    exit;
}
?>