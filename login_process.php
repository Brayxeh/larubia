<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $username = $conn->real_escape_string($username);

    $sql = "SELECT * FROM usuarios WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            header('Location: index.php');
            exit();
        } else {
            echo "<script>alert('Contraseña incorrecta'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Usuario no encontrado'); window.location.href='login.php';</script>";
    }
}
?>
