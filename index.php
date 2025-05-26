<?php
include("conexion.php");


$mascotas = $conexion->query("SELECT * FROM mascotas ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Mascotas y Vacunas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        a { margin-right: 10px; text-decoration: none; color: #007BFF; }
        a:hover { text-decoration: underline; }
        .acciones { white-space: nowrap; }
        .vacunas { margin-left: 20px; font-size: 0.9em; background-color: #fcfcfc; padding: 10px; border: 1px dashed #ccc; }
        .btn-agregar { background: #28a745; color: #fff; padding: 10px 15px; text-decoration: none; border-radius: 4px; }
        .btn-agregar:hover { background: #218838; }
    </style>
</head>
<body>
    <h1>🐶 Registro de Mascotas y Vacunas</h1>
    <a class="btn-agregar" href="agregar_mascota.php">➕ Agregar Nueva Mascota</a>
    <br><br>
    <table>
        <tr>
            <th>Nombre</th>
            <th>Especie</th>
            <th>Raza</th>
            <th>Fecha Nac.</th>
            <th>Acciones</th>
        </tr>
        <?php while ($mascota = $mascotas->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($mascota['nombre']) ?></td>
                <td><?= htmlspecialchars($mascota['especie']) ?></td>
                <td><?= htmlspecialchars($mascota['raza']) ?></td>
                <td><?= htmlspecialchars($mascota['fecha_nac']) ?></td>
                <td class="acciones">
                    <a href="editar_mascota.php?id=<?= $mascota['id'] ?>">✏️ Editar</a>
                    <a href="eliminar_mascota.php?id=<?= $mascota['id'] ?>" onclick="return confirm('¿Eliminar esta mascota?')">🗑️ Eliminar</a>
                    <a href="agregar_vacuna.php?mascota_id=<?= $mascota['id'] ?>">💉 Agregar Vacuna</a>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <div class="vacunas">
                        <strong>Vacunas:</strong><br>
                        <?php
                        $mascota_id = intval($mascota['id']);
                        $vacunas = $conexion->query("SELECT * FROM vacunas WHERE mascota_id = $mascota_id");
                        if ($vacunas && $vacunas->num_rows > 0):
                            while ($v = $vacunas->fetch_assoc()):
                        ?>
                            • <strong><?= htmlspecialchars($v['nombre_vacuna']) ?></strong> aplicada el <?= htmlspecialchars($v['fecha_aplicacion']) ?>
                            <?php if ($v['proxima_dosis']): ?> – próxima: <?= htmlspecialchars($v['proxima_dosis']) ?><?php endif; ?>
                            <?php if ($v['observaciones']): ?> (<?= htmlspecialchars($v['observaciones']) ?>)<?php endif; ?>
                            [<a href="editar_vacuna.php?id=<?= $v['id'] ?>">Editar</a>]
                            <br>
                        <?php endwhile;
                        else: ?>
                            <em>No hay vacunas registradas.</em>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>