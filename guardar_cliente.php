<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $matricula = $_POST['matricula'];
    $nombre = $_POST['nombre'];

    $sql = "INSERT INTO clientes (matricula, nombre) VALUES ('$matricula', '$nombre')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'cliente_id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
}

$conn->close();
?>
