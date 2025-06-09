<?php
include("conexion.php");

// Procesar búsqueda y filtros
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$filtro_periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'proximas';
$filtro_especie = isset($_GET['especie']) ? $_GET['especie'] : '';

// Definir períodos de tiempo
$fecha_hoy = date('Y-m-d');
$fecha_una_semana = date('Y-m-d', strtotime('+1 week'));
$fecha_un_mes = date('Y-m-d', strtotime('+1 month'));
$fecha_tres_meses = date('Y-m-d', strtotime('+3 months'));

// Construir consulta según el período seleccionado
$where_conditions = ["v.proxima_dosis IS NOT NULL"];
$params = [];
$types = '';

switch ($filtro_periodo) {
    case 'vencidas':
        $where_conditions[] = "v.proxima_dosis < ?";
        $params[] = $fecha_hoy;
        $types .= 's';
        break;
    case 'esta_semana':
        $where_conditions[] = "v.proxima_dosis BETWEEN ? AND ?";
        $params[] = $fecha_hoy;
        $params[] = $fecha_una_semana;
        $types .= 'ss';
        break;
    case 'este_mes':
        $where_conditions[] = "v.proxima_dosis BETWEEN ? AND ?";
        $params[] = $fecha_hoy;
        $params[] = $fecha_un_mes;
        $types .= 'ss';
        break;
    case 'proximos_3_meses':
        $where_conditions[] = "v.proxima_dosis BETWEEN ? AND ?";
        $params[] = $fecha_hoy;
        $params[] = $fecha_tres_meses;
        $types .= 'ss';
        break;
    default: // 'proximas'
        $where_conditions[] = "v.proxima_dosis >= ?";
        $params[] = $fecha_hoy;
        $types .= 's';
        break;
}

// Filtro por búsqueda
if (!empty($busqueda)) {
    $where_conditions[] = "(m.nombre LIKE ? OR m.raza LIKE ? OR v.nombre_vacuna LIKE ?)";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
    $types .= 'sss';
}

// Filtro por especie
if (!empty($filtro_especie)) {
    $where_conditions[] = "m.especie = ?";
    $params[] = $filtro_especie;
    $types .= 's';
}

$where_sql = 'WHERE ' . implode(' AND ', $where_conditions);

// Consulta principal
$sql = "SELECT 
            v.id as vacuna_id,
            v.nombre_vacuna,
            v.fecha_aplicacion,
            v.proxima_dosis,
            v.observaciones,
            m.id as mascota_id,
            m.nombre as mascota_nombre,
            m.especie,
            m.raza,
            DATEDIFF(v.proxima_dosis, CURDATE()) as dias_restantes
        FROM vacunas v 
        JOIN mascotas m ON v.mascota_id = m.id 
        $where_sql 
        ORDER BY v.proxima_dosis ASC, m.nombre ASC";

$stmt = $conexion->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$vacunas = $stmt->get_result();

// Obtener especies para el filtro
$especies_result = $conexion->query("SELECT DISTINCT especie FROM mascotas ORDER BY especie");

// Obtener estadísticas
$stats_sql = "SELECT 
                COUNT(CASE WHEN v.proxima_dosis < CURDATE() THEN 1 END) as vencidas,
                COUNT(CASE WHEN v.proxima_dosis BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as esta_semana,
                COUNT(CASE WHEN v.proxima_dosis BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 1 END) as este_mes,
                COUNT(CASE WHEN v.proxima_dosis >= CURDATE() THEN 1 END) as proximas
              FROM vacunas v 
              JOIN mascotas m ON v.mascota_id = m.id 
              WHERE v.proxima_dosis IS NOT NULL";

$stats = $conexion->query($stats_sql)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Próximas Dosis de Vacunas</title>
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            text-align: center;
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .stat-vencidas { color: #dc2626; }
        .stat-semana { color: #f59e0b; }
        .stat-mes { color: #3b82f6; }
        .stat-proximas { color: #10b981; }

        .filters-section {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }

        .filters-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .filter-input, .filter-select {
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: #fafafa;
        }

        .filter-input:focus, .filter-select:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
            justify-content: center;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .vaccine-card {
            background: white;
            border-radius: 12px;
            margin-bottom: 1rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .vaccine-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }

        .vaccine-card.vencida {
            border-left: 4px solid #dc2626;
            background: #fef2f2;
        }

        .vaccine-card.esta-semana {
            border-left: 4px solid #f59e0b;
            background: #fffbeb;
        }

        .vaccine-card.este-mes {
            border-left: 4px solid #3b82f6;
            background: #eff6ff;
        }

        .vaccine-card.proxima {
            border-left: 4px solid #10b981;
        }

        .vaccine-header {
            padding: 1.5rem;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 1rem;
            align-items: start;
        }

        .vaccine-info h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .vaccine-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .detail-value {
            font-weight: 500;
            color: #1e293b;
            margin-top: 0.25rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
        }

        .badge-vencida {
            background: #fee2e2;
            color: #dc2626;
        }

        .badge-urgente {
            background: #fef3c7;
            color: #d97706;
        }

        .badge-proximo {
            background: #dbeafe;
            color: #2563eb;
        }

        .badge-futuro {
            background: #d1fae5;
            color: #059669;
        }

        .vaccine-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
            border-radius: 6px;
        }

        .btn-edit {
            background: #f59e0b;
            color: white;
        }

        .btn-edit:hover {
            background: #d97706;
        }

        .btn-done {
            background: #10b981;
            color: white;
        }

        .btn-done:hover {
            background: #059669;
        }

        .no-results {
            text-align: center;
            padding: 4rem 2rem;
            color: #64748b;
        }

        .no-results-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .title {
                font-size: 2rem;
            }

            .filters-form {
                grid-template-columns: 1fr;
            }

            .vaccine-header {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .vaccine-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">
            ← Volver al listado principal
        </a>

        <header class="header">
            <h1 class="title">🗓️ Próximas Dosis</h1>
            <p class="subtitle">Control de vacunas pendientes y vencidas</p>
        </header>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number stat-vencidas"><?= $stats['vencidas'] ?></div>
                <div class="stat-label">Vencidas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number stat-semana"><?= $stats['esta_semana'] ?></div>
                <div class="stat-label">Esta semana</div>
            </div>
            <div class="stat-card">
                <div class="stat-number stat-mes"><?= $stats['este_mes'] ?></div>
                <div class="stat-label">Este mes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number stat-proximas"><?= $stats['proximas'] ?></div>
                <div class="stat-label">Próximas</div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters-section">
            <form class="filters-form" method="GET">
                <div class="filter-group">
                    <label class="filter-label">Buscar</label>
                    <input type="text" 
                           name="buscar" 
                           class="filter-input" 
                           placeholder="Mascota, raza o vacuna..." 
                           value="<?= htmlspecialchars($busqueda) ?>">
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Período</label>
                    <select name="periodo" class="filter-select">
                        <option value="vencidas" <?= $filtro_periodo === 'vencidas' ? 'selected' : '' ?>>🔴 Vencidas</option>
                        <option value="esta_semana" <?= $filtro_periodo === 'esta_semana' ? 'selected' : '' ?>>⚠️ Esta semana</option>
                        <option value="este_mes" <?= $filtro_periodo === 'este_mes' ? 'selected' : '' ?>>📅 Este mes</option>
                        <option value="proximos_3_meses" <?= $filtro_periodo === 'proximos_3_meses' ? 'selected' : '' ?>>📋 Próximos 3 meses</option>
                        <option value="proximas" <?= $filtro_periodo === 'proximas' ? 'selected' : '' ?>>✅ Todas las próximas</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Especie</label>
                    <select name="especie" class="filter-select">
                        <option value="">Todas las especies</option>
                        <?php while ($especie = $especies_result->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($especie['especie']) ?>" 
                                    <?= $filtro_especie === $especie['especie'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($especie['especie']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    🔍 Filtrar
                </button>
                
                <a href="proximas_dosis.php" class="btn btn-secondary">
                    ✕ Limpiar
                </a>
            </form>
        </div>

        <!-- Lista de vacunas -->
        <?php if ($vacunas->num_rows > 0): ?>
            <?php while ($v = $vacunas->fetch_assoc()): 
                $dias = $v['dias_restantes'];
                $clase_urgencia = '';
                $badge_class = '';
                $status_text = '';
                
                if ($dias < 0) {
                    $clase_urgencia = 'vencida';
                    $badge_class = 'badge-vencida';
                    $status_text = 'Vencida (' . abs($dias) . ' días)';
                } elseif ($dias <= 7) {
                    $clase_urgencia = 'esta-semana';
                    $badge_class = 'badge-urgente';
                    $status_text = $dias == 0 ? 'Hoy' : ($dias == 1 ? 'Mañana' : "En $dias días");
                } elseif ($dias <= 30) {
                    $clase_urgencia = 'este-mes';
                    $badge_class = 'badge-proximo';
                    $status_text = "En $dias días";
                } else {
                    $clase_urgencia = 'proxima';
                    $badge_class = 'badge-futuro';
                    $status_text = "En $dias días";
                }
            ?>
                <div class="vaccine-card <?= $clase_urgencia ?>">
                    <div class="vaccine-header">
                        <div class="vaccine-info">
                            <h3><?= htmlspecialchars($v['nombre_vacuna']) ?></h3>
                            <div class="vaccine-details">
                                <div class="detail-item">
                                    <span class="detail-label">Mascota</span>
                                    <span class="detail-value"><?= htmlspecialchars($v['mascota_nombre']) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Especie/Raza</span>
                                    <span class="detail-value"><?= htmlspecialchars($v['especie']) ?> - <?= htmlspecialchars($v['raza']) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Última aplicación</span>
                                    <span class="detail-value"><?= date('d/m/Y', strtotime($v['fecha_aplicacion'])) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Próxima dosis</span>
                                    <span class="detail-value"><?= date('d/m/Y', strtotime($v['proxima_dosis'])) ?></span>
                                </div>
                            </div>
                            
                            <?php if ($v['observaciones']): ?>
                                <div class="detail-item" style="margin-top: 1rem;">
                                    <span class="detail-label">Observaciones</span>
                                    <span class="detail-value"><?= htmlspecialchars($v['observaciones']) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="vaccine-actions">
                                <a href="editar_vacuna.php?id=<?= $v['vacuna_id'] ?>" class="btn btn-small btn-edit">
                                    ✏️ Editar
                                </a>
                                <a href="agregar_vacuna.php?mascota_id=<?= $v['mascota_id'] ?>" class="btn btn-small btn-done">
                                    💉 Aplicar nueva dosis
                                </a>
                            </div>
                        </div>
                        
                        <div class="status-badge <?= $badge_class ?>">
                            <?= $status_text ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-results">
                <div class="no-results-icon">📅</div>
                <h3>No hay vacunas programadas</h3>
                <p>No se encontraron vacunas que coincidan con los filtros seleccionados</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto-submit cuando cambian los selects
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });

        // Actualizar la página cada 5 minutos para mantener datos frescos
        setTimeout(function() {
            location.reload();
        }, 300000); // 5 minutos
    </script>
</body>
</html>