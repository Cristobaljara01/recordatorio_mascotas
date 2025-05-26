<?php
include("conexion.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conexion->query("DELETE FROM vacunas WHERE mascota_id = $id");
    $conexion->query("DELETE FROM mascotas WHERE id = $id");
}

header("Location: index.php");
exit();