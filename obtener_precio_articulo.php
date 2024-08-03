<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $articulo_id = $_POST['articulo_id'];

    $sql = "SELECT * FROM articulos WHERE id = '$articulo_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $articulo = $result->fetch_assoc();
        echo json_encode(['nombre' => $articulo['nombre'], 'precio' => $articulo['precio']]);
    } else {
        echo json_encode(['error' => 'Artículo no encontrado']);
    }
}

$conn->close();
?>
