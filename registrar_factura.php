<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $fecha = date('Y-m-d');
    $total = $_POST['total'];
    $comentario = $_POST['comentario'];
    $articulos = $_POST['articulos']; 

    $sql = "SELECT * FROM clientes WHERE id = '$cliente_id'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        echo json_encode(['success' => false, 'error' => 'Cliente no encontrado']);
        exit();
    }

    $conn->begin_transaction();

    try {
        $sql = "INSERT INTO facturas (fecha, cliente_id, total, comentario) VALUES ('$fecha', '$cliente_id', '$total', '$comentario')";
        if ($conn->query($sql) === TRUE) {
            $factura_id = $conn->insert_id;

    
            foreach ($articulos as $articulo) {
                $articulo_id = $articulo['id'];
                $cantidad = $articulo['cantidad'];
                $precio = $articulo['precio'];
                $total_articulo = $articulo['total'];

                $sql = "INSERT INTO factura_detalle (factura_id, articulo_id, cantidad, precio, total) VALUES ('$factura_id', '$articulo_id', '$cantidad', '$precio', '$total_articulo')";
                if (!$conn->query($sql)) {
                    throw new Exception("Error al insertar detalle de factura: " . $conn->error);
                }
            }

            $conn->commit();
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Error al registrar la factura: " . $conn->error);
        }
    } catch (Exception $e) {

        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método de solicitud no válido']);
}

$conn->close();
?>
