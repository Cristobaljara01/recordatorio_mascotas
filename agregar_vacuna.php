<?php
include("conexion.php");

if (!isset($_GET['mascota_id'])) {
    die("ID de mascota no proporcionado.");
}

$mascota_id = intval($_GET['mascota_id']);

// Verificar que la mascota existe
$stmt_check = $conexion->prepare("SELECT id, nombre FROM mascotas WHERE id = ?");
$stmt_check->bind_param("i", $mascota_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 0) {
    die("La mascota especificada no existe.");
}

$mascota = $result_check->fetch_assoc();
$stmt_check->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_vacuna = trim($_POST['nombre_vacuna']);
    $fecha_aplicacion = $_POST['fecha_aplicacion'];
    $proxima_dosis = isset($_POST['proxima_dosis']) && $_POST['proxima_dosis'] !== '' ? $_POST['proxima_dosis'] : null;
    $observaciones = trim($_POST['observaciones']);

    if (empty($nombre_vacuna)) {
        $error = "El nombre de la vacuna es obligatorio.";
    } elseif (empty($fecha_aplicacion)) {
        $error = "La fecha de aplicación es obligatoria.";
    } else {
        $stmt = $conexion->prepare("INSERT INTO agregar_vacuna (mascota_id, nombre_vacuna, fecha_aplicacion, proxima_dosis, observaciones) VALUES (?, ?, ?, ?, ?)");

        if (!$stmt) {
            die("Error en prepare(): " . $conexion->error);
        }

        // Si $proxima_dosis es null, usaremos bind_param solo con tipos adecuados
        if ($proxima_dosis === null) {
            $stmt->bind_param("issss", $mascota_id, $nombre_vacuna, $fecha_aplicacion, $proxima_dosis , $observaciones);
        } else {
            $stmt->bind_param("issss", $mascota_id, $nombre_vacuna, $fecha_aplicacion, $proxima_dosis, $observaciones);
        }

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: index.php?success=vacuna_agregada");
            exit();
        } else {
            $error = "Error al guardar la vacuna: " . $stmt->error;
            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Vacuna</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
            padding: 2rem 1rem;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: #64748b;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-input, .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: #fafafa;
        }

        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .error {
            background: #fef2f2;
            color: #dc2626;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #fecaca;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 2rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">
            ← Volver al listado
        </a>

        <div class="card">
            <div class="header">
                <h1 class="title">💉 Agregar Vacuna</h1>
                <p class="subtitle">Para: <?= htmlspecialchars($mascota['nombre']) ?></p>
            </div>

            <?php if (isset($error)): ?>
                <div class="error">
                    ⚠️ <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Nombre de la vacuna *</label>
                    <input type="text" 
                           name="nombre_vacuna" 
                           class="form-input" 
                           value="<?= isset($_POST['nombre_vacuna']) ? htmlspecialchars($_POST['nombre_vacuna']) : '' ?>"
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label">Fecha de aplicación *</label>
                    <input type="date" 
                           name="fecha_aplicacion" 
                           class="form-input" 
                           value="<?= isset($_POST['fecha_aplicacion']) ? htmlspecialchars($_POST['fecha_aplicacion']) : '' ?>"
                           max="<?= date('Y-m-d') ?>"
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label">Próxima dosis (opcional)</label>
                    <input type="date" 
                           name="proxima_dosis" 
                           class="form-input" 
                           value="<?= isset($_POST['proxima_dosis']) ? htmlspecialchars($_POST['proxima_dosis']) : '' ?>"
                           min="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" 
                              class="form-textarea" 
                              placeholder="Información adicional sobre la vacuna..."><?= isset($_POST['observaciones']) ? htmlspecialchars($_POST['observaciones']) : '' ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        💾 Guardar Vacuna
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        ✕ Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>