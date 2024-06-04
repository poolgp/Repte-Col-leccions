<?php

function openBD()
{
    $servername = "localhost";
    $username = "root";
    $password = "mysql";

    $conn = new PDO("mysql:host=$servername;dbname=musica", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("set names utf8");

    return $conn;
}

function closeBD()
{
    return null;
}

function selectPaises()
{
    $conn = openBD();

    $sentenciaText = "select * from musica.paises order by id";
    $sentencia = $conn->prepare($sentenciaText);
    $sentencia->execute();

    $resultado = $sentencia->fetchAll();

    $conn = closeBD();
    return $resultado;
}

function selectCanciones()
{
    $conn = openBD();

    $sentenciaText = "select * from musica.canciones order by id";
    $sentencia = $conn->prepare($sentenciaText);
    $sentencia->execute();

    $resultado = $sentencia->fetchAll();

    $conn = closeBD();
    return $resultado;
}

// function selectCantantes()
// {
//     $conn = openBD();

//     $sentenciaText = "select * from musica.cantantes order by id";
//     $sentencia = $conn->prepare($sentenciaText);
//     $sentencia->execute();

//     $resultado = $sentencia->fetchAll();

//     $conn = closeBD();
//     return $resultado;
// }
function selectCantantes()
{
    $conn = openBD();

    $sentenciaText = "SELECT cantantes.id, cantantes.imagen, cantantes.nombre, cantantes.fecha_nacimiento, paises.nombre AS nombre_pais, GROUP_CONCAT(canciones.nombre SEPARATOR ', ') AS canciones
                      FROM cantantes
                      LEFT JOIN paises ON cantantes.pais_id = paises.id
                      LEFT JOIN cantantes_canciones ON cantantes.id = cantantes_canciones.cantante_id
                      LEFT JOIN canciones ON cantantes_canciones.cancion_id = canciones.id
                      GROUP BY cantantes.id";
    $sentencia = $conn->prepare($sentenciaText);
    $sentencia->execute();

    $resultado = $sentencia->fetchAll();

    $conn = closeBD();
    return $resultado;
}

function insertCantante($id, $imagen, $nombre, $fecha_nacimiento, $pais_id)
{
    $conn = openBD();

    $rutaImg = "/Repte-Col-leccions/img/";

    $fechaActual = date("Ymd_His");

    $nombreArchivo = $fechaActual . "-" . $_FILES['imagen']['name'];
    $imgSubida = $rutaImg . $nombreArchivo;

    move_uploaded_file($_FILES['imagen']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $imgSubida);

    $sentenciaText = "insert into musica.cantantes (id, imagen, nombre, fecha_nacimiento, pais_id) values (:id, :imagen, :nombre, :fecha_nacimiento, :pais_id)";
    $sentencia = $conn->prepare($sentenciaText);
    $sentencia->bindParam(':id', $id);
    $sentencia->bindParam(':imagen', $imagen);
    $sentencia->bindParam(':nombre', $nombre);
    $sentencia->bindParam(':fecha_nacimiento', $fecha_nacimiento);
    $sentencia->bindParam(':pais_id', $pais_id);
    $sentencia->execute();

    $conn = closeBD();
}

function jointPais()
{
    $conn = openBD();

    $sentenciaText = "SELECT cantantes.id, cantantes.nombre AS nombre_cantante, cantantes.fecha_nacimiento, paises.nombre AS nombre_pais
        FROM cantantes
        INNER JOIN paises ON cantantes.pais_id = paises.id";

    $sentencia = $conn->prepare($sentenciaText);
    $sentencia->execute();

    $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    $conn = closeBD();

    return $resultado;
}

function insertCancion($nombre, $cantanteIds)
{
    $conn = openBD();

    // $sentenciaText = "insert into musica.canciones (id, nombre, cantante_id) values (:id, :nombre, :cantante_id)";
    // $sentencia = $conn->prepare($sentenciaText);
    // $sentencia->bindParam(':id', $id);
    // $sentencia->bindParam(':nombre', $nombre);
    // $sentencia->bindParam(':cantante_id', $cantante_id);
    // $sentencia->execute();

    // Inicia una transacción
    $conn->beginTransaction();

    // Inserta la canción
    $sentenciaText = "INSERT INTO musica.canciones (nombre) VALUES (:nombre)";
    $sentencia = $conn->prepare($sentenciaText);
    $sentencia->bindParam(':nombre', $nombre);
    $sentencia->execute();

    // Obtén el ID de la canción insertada
    $cancionId = $conn->lastInsertId();

    // Inserta las relaciones en la tabla intermedia
    $sentenciaText = "INSERT INTO musica.cantantes_canciones (cantante_id, cancion_id) VALUES (:cantante_id, :cancion_id)";
    $sentencia = $conn->prepare($sentenciaText);

    foreach ($cantanteIds as $cantanteId) {
        $sentencia->bindParam(':cantante_id', $cantanteId);
        $sentencia->bindParam(':cancion_id', $cancionId);
        $sentencia->execute();
    }

    // Confirma la transacción
    $conn->commit();

    $conn = closeBD();
}
