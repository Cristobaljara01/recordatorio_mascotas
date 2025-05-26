<?php
include("conexion.php");

if (!isset($_GET['mascota_id'])) {
    die("ID de mascota no proporcionado.");
}

$mascota_id = intval($_GET['mascota_id']);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_vacuna = $_POST['nombre_vacuna'];
    $fecha_aplicacion = $_POST['fecha_aplicacion'];
    $proxima_dosis = $_POST['proxima_dosis'];
    $observaciones = $_POST['observaciones'];

    $stmt = $conexion->prepare("INSERT INTO vacunas (mascota_id, nombre_vacuna, fecha_aplicacion, proxima_dosis, observaciones) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $mascota_id, $nombre_vacuna, $fecha_aplicacion, $proxima_dosis, $observaciones);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Agregar Vacuna</title>
</head>
<body>
    <h1>Agregar Vacuna para la Mascota #<?= $mascota_id ?></h1>
    <form method="POST">
        <label>Nombre de la vacuna:</label><br>
        <input type="text" name="nombre_vacuna" required><br>
        <label>Fecha de aplicación:</label><br>
        <input type="date" name="fecha_aplicacion" required><br>
        <label>Próxima dosis (opcional):</label><br>
        <input type="date" name="proxima_dosis"><br>
        <label>Observaciones:</label><br>
        <textarea name="observaciones"></textarea><br><br>
        <input type="submit" value="Guardar Vacuna">
    </form>
    <a href="index.php">← Volver al listado</a>
</body>
</html>