<?php

require_once('../php_librarys/bd.php');

if (isset($_POST['insertCancion'])) {
    insertCancion($_POST['nombre'], $_POST['cantante_id']);

    header('Location: ../index.php');
    exit();
}
