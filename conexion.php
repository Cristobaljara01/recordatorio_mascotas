<?php
$host = "localhost";
$usuario = "root";
$contrasena = ""; 
$bd = "mascotas_db"; 

$conexion = new mysqli($host, $usuario, $contrasena, $bd);

if ($conexion->connect_error) {
    die("Error en la conexión: " . $conexion->connect_error);
}
?>