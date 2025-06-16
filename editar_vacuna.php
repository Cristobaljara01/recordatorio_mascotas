<?php
include("conexion.php");

if (!isset($_GET['id'])) {
    die("ID de vacuna no proporcionado.");
}

$id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_vacuna = $_POST['nombre_vacuna'];
    $fecha_aplicacion = $_POST['fecha_aplicacion'];
    $proxima_dosis = $_POST['proxima_dosis'];
    $observaciones = $_POST['observaciones'];

    $stmt = $conexion->prepare("UPDATE agregar_vacuna SET nombre_vacuna=?, fecha_aplicacion=?, proxima_dosis=?, observaciones=? WHERE id=?");
    $stmt->bind_param("ssssi", $nombre_vacuna, $fecha_aplicacion, $proxima_dosis, $observaciones, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit();
}

$vacuna = $conexion->query("SELECT * FROM agregar_vacuna WHERE id = $id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Vacuna - Sistema de Vacunas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .header i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .form-container {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4facfe;
            background: white;
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
            transform: translateY(-2px);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(79, 172, 254, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            color: #8b4513;
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(252, 182, 159, 0.4);
        }

        .card-info {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            border-left: 5px solid #4facfe;
        }

        .card-info h3 {
            color: #333;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-info p {
            color: #666;
            line-height: 1.6;
        }

        /* Animaciones */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            animation: slideInUp 0.6s ease-out;
        }

        .form-group {
            animation: slideInUp 0.6s ease-out;
            animation-fill-mode: both;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .form-container {
                padding: 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .btn-container {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                justify-content: center;
            }
        }

        /* Efectos adicionales */
        .form-group input[type="date"]::-webkit-calendar-picker-indicator {
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="%234facfe" d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>') no-repeat;
            cursor: pointer;
        }

        .success-message {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            color: #155724;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 5px solid #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-syringe"></i>
            <h1>Editar Vacuna</h1>
        </div>
        
        <div class="form-container">
            <div class="card-info">
                <h3><i class="fas fa-info-circle"></i> Información</h3>
                <p>Modifica los datos de la vacuna. Los campos marcados con asterisco (*) son obligatorios.</p>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label for="nombre_vacuna">
                        <i class="fas fa-prescription-bottle-alt"></i> Nombre de la vacuna *
                    </label>
                    <input type="text" 
                           id="nombre_vacuna" 
                           name="nombre_vacuna" 
                           value="<?= htmlspecialchars($vacuna['nombre_vacuna']) ?>" 
                           required
                           placeholder="Ej: COVID-19, Influenza, Hepatitis B">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_aplicacion">
                            <i class="fas fa-calendar-check"></i> Fecha de aplicación *
                        </label>
                        <input type="date" 
                               id="fecha_aplicacion" 
                               name="fecha_aplicacion" 
                               value="<?= $vacuna['fecha_aplicacion'] ?>" 
                               required>
                    </div>

                    <div class="form-group">
                        <label for="proxima_dosis">
                            <i class="fas fa-calendar-plus"></i> Próxima dosis
                        </label>
                        <input type="date" 
                               id="proxima_dosis" 
                               name="proxima_dosis" 
                               value="<?= $vacuna['proxima_dosis'] ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="observaciones">
                        <i class="fas fa-notes-medical"></i> Observaciones
                    </label>
                    <textarea id="observaciones" 
                              name="observaciones" 
                              placeholder="Ingresa observaciones adicionales sobre la vacuna..."><?= htmlspecialchars($vacuna['observaciones']) ?></textarea>
                </div>

                <div class="btn-container">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Guardar Cambios
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Volver al Listado
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validación adicional del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const nombreVacuna = document.getElementById('nombre_vacuna').value.trim();
            const fechaAplicacion = document.getElementById('fecha_aplicacion').value;
            
            if (!nombreVacuna) {
                alert('Por favor, ingresa el nombre de la vacuna.');
                e.preventDefault();
                return;
            }
            
            if (!fechaAplicacion) {
                alert('Por favor, selecciona la fecha de aplicación.');
                e.preventDefault();
                return;
            }
            
            // Validar que la fecha de aplicación no sea futura
            const hoy = new Date();
            const fechaApp = new Date(fechaAplicacion);
            
            if (fechaApp > hoy) {
                const confirmar = confirm('La fecha de aplicación es futura. ¿Estás seguro de que es correcta?');
                if (!confirmar) {
                    e.preventDefault();
                    return;
                }
            }
        });

        // Mejorar la experiencia del usuario con efectos
        document.querySelectorAll('input, textarea').forEach(field => {
            field.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            field.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Confirmación antes de salir si hay cambios sin guardar
        let formChanged = false;
        const originalData = {
            nombre_vacuna: document.getElementById('nombre_vacuna').value,
            fecha_aplicacion: document.getElementById('fecha_aplicacion').value,
            proxima_dosis: document.getElementById('proxima_dosis').value,
            observaciones: document.getElementById('observaciones').value
        };

        document.querySelectorAll('input, textarea').forEach(field => {
            field.addEventListener('input', function() {
                formChanged = true;
            });
        });

        document.querySelector('.btn-secondary').addEventListener('click', function(e) {
            if (formChanged) {
                const confirmar = confirm('Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?');
                if (!confirmar) {
                    e.preventDefault();
                }
            }
        });
    </script>
</body>
</html>