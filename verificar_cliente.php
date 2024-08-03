<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $matricula = $_POST['matricula'];

    $sql = "SELECT * FROM clientes WHERE matricula = '$matricula'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $cliente = $result->fetch_assoc();
        echo json_encode(['existe' => true, 'cliente_id' => $cliente['id']]);
    } else {
        echo json_encode(['existe' => false]);
    }
}

$conn->close();
?>
