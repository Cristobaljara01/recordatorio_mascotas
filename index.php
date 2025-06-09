<?php
include("conexion.php");

// Procesar búsqueda
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$filtro_especie = isset($_GET['especie']) ? $_GET['especie'] : '';

// Construir consulta con filtros
$where_conditions = [];
$params = [];
$types = '';

if (!empty($busqueda)) {
    $where_conditions[] = "(mascotas.nombre LIKE ? OR mascotas.raza LIKE ?)";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
    $types .= 'ss';
}

if (!empty($filtro_especie)) {
    $where_conditions[] = "mascotas.especie = ?";
    $params[] = $filtro_especie;
    $types .= 's';
}

$where_sql = '';
if (!empty($where_conditions)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Preparar consulta
$sql = "SELECT * FROM mascotas $where_sql ORDER BY mascotas.id DESC";
$stmt = $conexion->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$mascotas = $stmt->get_result();

// Obtener especies únicas para el filtro
$especies_result = $conexion->query("SELECT DISTINCT especie FROM mascotas ORDER BY especie");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Mascotas</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
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
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .title {
            font-size: 2.5rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: #64748b;
            font-size: 1.1rem;
        }

        .search-section {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }

        .search-form {
            display: flex;
            gap: 1rem;
            align-items: end;
            flex-wrap: wrap;
        }

        .search-group {
            flex: 1;
            min-width: 200px;
        }

        .search-label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .search-input, .search-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: #fafafa;
        }

        .search-input:focus, .search-select:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-search, .btn-clear {
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
        }

        .btn-search {
            background: #3b82f6;
            color: white;
        }

        .btn-search:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-clear {
            background: #6b7280;
            color: white;
        }

        .btn-clear:hover {
            background: #4b5563;
        }

        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .btn-add {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .results-info {
            color: #64748b;
            font-size: 0.9rem;
        }

        .card {
            background: white;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .pet-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .pet-field {
            display: flex;
            flex-direction: column;
        }

        .pet-label {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .pet-value {
            font-size: 1.1rem;
            font-weight: 500;
            color: #1e293b;
            margin-top: 0.25rem;
        }

        .pet-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: #0f172a;
        }

        .card-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-edit {
            background: #f59e0b;
            color: white;
        }

        .btn-edit:hover {
            background: #d97706;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        .btn-vaccine {
            background: #8b5cf6;
            color: white;
        }

        .btn-vaccine:hover {
            background: #7c3aed;
        }

        .vaccines-section {
            padding: 1.5rem;
            background: #f8fafc;
        }

        .vaccines-title {
            font-weight: 600;
            color: #374151;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .vaccine-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            border-left: 4px solid #8b5cf6;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .vaccine-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .vaccine-details {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .vaccine-actions {
            margin-top: 0.5rem;
        }

        .btn-edit-vaccine {
            color: #3b82f6;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .btn-edit-vaccine:hover {
            text-decoration: underline;
        }

        .no-pets {
            text-align: center;
            padding: 4rem 2rem;
            color: #64748b;
        }

        .no-pets-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .no-vaccines {
            color: #9ca3af;
            font-style: italic;
            text-align: center;
            padding: 1rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .title {
                font-size: 2rem;
            }

            .search-form {
                flex-direction: column;
            }

            .search-group {
                min-width: auto;
            }

            .actions-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .pet-info {
                grid-template-columns: 1fr;
            }

            .card-actions {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1 class="title">🐾 Registro de Mascotas</h1>
            <p class="subtitle">Gestiona las mascotas y sus vacunas de forma simple</p>
        </header>

        <!-- Sección de búsqueda -->
        <div class="search-section">
            <form class="search-form" method="GET">
                <div class="search-group">
                    <label class="search-label">Buscar mascota</label>
                    <input type="text" 
                           name="buscar" 
                           class="search-input" 
                           placeholder="Nombre o raza..." 
                           value="<?= htmlspecialchars($busqueda) ?>">
                </div>
                
                <div class="search-group">
                    <label class="search-label">Filtrar por especie</label>
                    <select name="especie" class="search-select">
                        <option value="">Todas las especies</option>
                        <?php while ($especie = $especies_result->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($especie['especie']) ?>" 
                                    <?= $filtro_especie === $especie['especie'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($especie['especie']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn-search">
                    🔍 Buscar
                </button>
                
                <a href="index.php" class="btn-clear">
                    ✕ Limpiar
                </a>
            </form>
        </div>

        <!-- Barra de acciones -->
        <div class="actions-bar">
            <div class="results-info">
                <?php 
                $total = $mascotas->num_rows;
                if (!empty($busqueda) || !empty($filtro_especie)) {
                    echo "Mostrando $total resultado" . ($total !== 1 ? 's' : '');
                    if (!empty($busqueda)) echo " para \"" . htmlspecialchars($busqueda) . "\"";
                } else {
                    echo "Total: $total mascota" . ($total !== 1 ? 's' : '');
                }
                ?>
            </div>
            
            <a href="agregar_mascota.php" class="btn-add">
                ➕ Nueva Mascota
            </a>
        </div>

        <!-- Lista de mascotas -->
        <?php if ($mascotas->num_rows > 0): ?>
            <?php while ($mascota = $mascotas->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-header">
                        <div class="pet-info">
                            <div class="pet-field">
                                <span class="pet-label">Nombre</span>
                                <span class="pet-value pet-name"><?= htmlspecialchars($mascota['nombre']) ?></span>
                            </div>
                            <div class="pet-field">
                                <span class="pet-label">Especie</span>
                                <span class="pet-value"><?= htmlspecialchars($mascota['especie']) ?></span>
                            </div>
                            <div class="pet-field">
                                <span class="pet-label">Raza</span>
                                <span class="pet-value"><?= htmlspecialchars($mascota['raza']) ?></span>
                            </div>
                            <div class="pet-field">
                                <span class="pet-label">Fecha de nacimiento</span>
                                <span class="pet-value"><?= htmlspecialchars($mascota['fecha_nac']) ?></span>
                            </div>
                        </div>
                        
                        <div class="card-actions">
                            <a href="editar_mascota.php?id=<?= $mascota['id'] ?>" class="btn-action btn-edit">
                                ✏️ Editar
                            </a>
                            <a href="eliminar_mascota.php?id=<?= $mascota['id'] ?>" 
                               class="btn-action btn-delete"
                               onclick="return confirm('¿Estás seguro de eliminar esta mascota?')">
                                🗑️ Eliminar
                            </a>
                            <a href="agregar_vacuna.php?mascota_id=<?= $mascota['id'] ?>" class="btn-action btn-vaccine">
                                💉 Agregar Vacuna
                            </a>
                        </div>
                    </div>

                    <div class="vaccines-section">
                        <h3 class="vaccines-title">
                            💉 Historial de Vacunas
                        </h3>
                        
                        <?php
                        $mascota_id = intval($mascota['id']);
                        $sql= "SELECT * FROM agregar_vacuna WHERE id = $mascota_id ORDER BY fecha_aplicacion DESC";
      
                        $vacunas = $conexion->query($sql);
                        
                        if ($vacunas && $vacunas->num_rows > 0):
                            while ($v = $vacunas->fetch_assoc()):
                        ?>
                            <div class="vaccine-item">
                                <div class="vaccine-name"><?= htmlspecialchars($v['nombre_vacuna']) ?></div>
                                <div class="vaccine-details">
                                    📅 Aplicada: <?= date('d/m/Y', strtotime($v['fecha_aplicacion'])) ?>
                                    <?php if ($v['proxima_dosis']): ?>
                                        • 🗓️ Próxima: <?= date('d/m/Y', strtotime($v['proxima_dosis'])) ?>
                                    <?php endif; ?>
                                </div>
                                <?php if ($v['observaciones']): ?>
                                    <div class="vaccine-details">
                                        📝 <?= htmlspecialchars($v['observaciones']) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="vaccine-actions">
                                    <a href="editar_vacuna.php?id=<?= $v['id'] ?>" class="btn-edit-vaccine">
                                        ✏️ Editar vacuna
                                    </a>
                                </div>
                            </div>
                        <?php 
                            endwhile;
                        else: 
                        ?>
                            <div class="no-vaccines">
                                No hay vacunas registradas para esta mascota
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-pets">
                <div class="no-pets-icon">🐾</div>
                <h3>No se encontraron mascotas</h3>
                <?php if (!empty($busqueda) || !empty($filtro_especie)): ?>
                    <p>Intenta con otros términos de búsqueda o <a href="index.php">ver todas las mascotas</a></p>
                <?php else: ?>
                    <p>Comienza agregando una nueva mascota</p>
                    <br>
                    <a href="agregar_mascota.php" class="btn-add">➕ Agregar Primera Mascota</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto-submit del formulario cuando cambia el select de especies
        document.querySelector('select[name="especie"]').addEventListener('change', function() {
            this.form.submit();
        });

        // Focus en el campo de búsqueda con Ctrl+K
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                document.querySelector('input[name="buscar"]').focus();
            }
        });
    </script>
</body>
</html>