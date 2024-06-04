<?php
session_start();

require_once('../php_librarys/bd.php');

if (isset($_POST['insertCantante'])) {
    insertCantante($_POST['id'], $_FILES['imagen'], $_POST['nombre'], $_POST['fecha_nacimiento'], $_POST['pais_id']);

    if (isset($_SESSION['error'])) {
        header('Location: ../forms/añadir_cantante.php');
        exit();
    } else {
        header('Location: ../index.php');
        exit();
    }
}
