<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $especie = $_POST['especie'];
    $raza = $_POST['raza'];
    $fecha_nac = $_POST['fecha_nac'];

    $stmt = $conexion->prepare("INSERT INTO mascotas (nombre, especie, raza, fecha_nac) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $especie, $raza, $fecha_nac);
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
    <title>Agregar Mascota</title>
</head>
<body>
    <h1>Agregar Nueva Mascota</h1>
    <form method="POST" action="">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br>
        <label>Especie:</label><br>
        <input type="text" name="especie" required><br>
        <label>Raza:</label><br>
        <input type="text" name="raza" required><br>
        <label>Fecha de nacimiento:</label><br>
        <input type="date" name="fecha_nac" required><br><br>
        <input type="submit" value="Guardar">
    </form>
    <a href="index.php">← Volver al listado</a>
</body>
</html>