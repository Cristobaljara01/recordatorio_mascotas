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

    $stmt = $conexion->prepare("UPDATE vacunas SET nombre_vacuna=?, fecha_aplicacion=?, proxima_dosis=?, observaciones=? WHERE id=?");
    $stmt->bind_param("ssssi", $nombre_vacuna, $fecha_aplicacion, $proxima_dosis, $observaciones, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit();
}


$vacuna = $conexion->query("SELECT * FROM vacunas WHERE id = $id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Editar Vacuna</title>
</head>
<body>
    <h1>Editar Vacuna</h1>
    <form method="POST">
        <label>Nombre de la vacuna:</label><br>
        <input type="text" name="nombre_vacuna" value="<?= $vacuna['nombre_vacuna'] ?>" required><br>
        <label>Fecha de aplicación:</label><br>
        <input type="date" name="fecha_aplicacion" value="<?= $vacuna['fecha_aplicacion'] ?>" required><br>
        <label>Próxima dosis (opcional):</label><br>
        <input type="date" name="proxima_dosis" value="<?= $vacuna['proxima_dosis'] ?>"><br>
        <label>Observaciones:</label><br>
        <textarea name="observaciones"><?= $vacuna['observaciones'] ?></textarea><br><br>
        <input type="submit" value="Guardar Cambios">
    </form>
    <a href="index.php">← Volver al listado</a>
</body>
</html>