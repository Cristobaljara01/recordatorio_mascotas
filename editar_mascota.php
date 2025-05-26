<?php
include("conexion.php");

if (!isset($_GET['id'])) {
    die("ID de mascota no especificado.");
}

$id = intval($_GET['id']);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $especie = $_POST['especie'];
    $raza = $_POST['raza'];
    $fecha_nac = $_POST['fecha_nac'];

    $stmt = $conexion->prepare("UPDATE mascotas SET nombre=?, especie=?, raza=?, fecha_nac=? WHERE id=?");
    $stmt->bind_param("ssssi", $nombre, $especie, $raza, $fecha_nac, $id);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Mascota actualizada correctamente.</p>";
    } else {
        echo "<p style='color: red;'>Error al actualizar: " . $stmt->error . "</p>";
    }
    $stmt->close();
}


$result = $conexion->query("SELECT * FROM mascotas WHERE id = $id");
if ($result->num_rows == 0) {
    die("Mascota no encontrada.");
}
$mascota = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Editar Mascota</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        margin: 20px;
      }
      form {
        max-width: 400px;
        background: #fff;
        padding: 15px;
        border-radius: 6px;
        box-shadow: 0 0 5px rgba(0,0,0,0.1);
      }
      label {
        font-weight: bold;
      }
      input[type="text"], input[type="date"] {
        width: 100%;
        padding: 7px;
        margin-top: 5px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
      }
      input[type="submit"] {
        background-color: #2980b9;
        color: white;
        padding: 10px 18px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
      }
      input[type="submit"]:hover {
        background-color: #3498db;
      }
      a {
        display: inline-block;
        margin-top: 15px;
        color: #2980b9;
      }
    </style>
</head>
<body>
    <h1>Editar Mascota</h1>
    <form method="POST" action="">
        <label>Nombre:</label><br />
        <input type="text" name="nombre" value="<?= htmlspecialchars($mascota['nombre']) ?>" required /><br />

        <label>Especie:</label><br />
        <input type="text" name="especie" value="<?= htmlspecialchars($mascota['especie']) ?>" required /><br />

        <label>Raza:</label><br />
        <input type="text" name="raza" value="<?= htmlspecialchars($mascota['raza']) ?>" required /><br />

        <label>Fecha de Nacimiento:</label><br />
        <input type="date" name="fecha_nac" value="<?= $mascota['fecha_nac'] ?>" required /><br />

        <input type="submit" value="Actualizar Mascota" />
    </form>
    <a href="index.php">← Volver al listado</a>
</body>
</html>