<?php
session_start();

function errorMessage($ex)
{
    if (!empty($ex->errorInfo[1])) {
        switch ($ex->errorInfo[1]) {
            case 1062:
                $mensaje = 'Registro ducplicado';
                break;
            case 1451:
                $mensaje = 'Registro con elementos relacionados';
                break;
            default:
                $mensaje = $ex->errorInfo[1] . ' - ' . $ex->errorInfo[2];
                break;
        }
    } else {
        switch ($ex->getCode()) {
            case 1044:
                $mensaje = "Usuario y/o password incorrectos";
                break;
            case 1049:
                $mensaje = "Base de datos deconocida";
                break;
            case 2002:
                $mensaje = "No se encuentra el servidor";
                break;
            default:
                $mensaje = $ex->getCode() . ' - ' . $ex->getMessage();
                break;
        }
    }
    return $mensaje;
}

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

function insertCantante($imagen, $nombre, $fecha_nacimiento, $pais_id, $canciones_ids)
{
    echo "hola";
    try {
        $conn = openBD();

        $rutaImg = "/Repte-Col-leccions/img/";

        $fechaActual = date("Ymd_His");

        $nombreArchivo = $fechaActual . "-" . $_FILES['imagen']['name'];
        $imgSubida = $rutaImg . $nombreArchivo;

        move_uploaded_file($_FILES['imagen']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $imgSubida);

        $sentenciaText = "insert into musica.cantantes (imagen, nombre, fecha_nacimiento, pais_id) values (:imagen, :nombre, :fecha_nacimiento, :pais_id)";
        $sentencia = $conn->prepare($sentenciaText);
        $sentencia->bindParam(':imagen', $imagen);
        $sentencia->bindParam(':nombre', $nombre);
        $sentencia->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $sentencia->bindParam(':pais_id', $pais_id);
        $sentencia->execute();

        $sentenciaText = "insert into spbd.cantantes_canciones (cantante_id, cancion_id) values (:cantante_id, :cancion_id)";
        $sentencia = $conn->prepare($sentenciaText);
        foreach ($canciones_ids as $cancion_id) {
            $sentencia->bindParam(':cantante_id', $lastInsertedId);
            $sentencia->bindParam(':cancion_id', $cancion_id);
            $sentencia->execute();
        }

        $_SESSION['mensaje'] = 'Registro insertado correctamente';
    } catch (PDOException $ex) {
        $_SESSION['error'] = errorMessage($ex);
        $cantante['imagen'] = $imagen;
        $cantante['nombre'] = $nombre;
        $cantante['fecha_nacimiento'] = $fecha_nacimiento;
        $cantante['pais_id'] = $pais_id;
        $_SESSION['cantante'] = $cantante;
    }

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

function editarCantante()
{
    try {
        $conexion = openBD();

        $sentenciaText = "DELETE FROM musica.cantantes WHERE id = :id";
        $sentencia = $conexion->prepare($sentenciaText);
        $sentencia->bindParam(':id', $id);
        $sentencia->execute();

        $_SESSION['mensaje'] = 'Registro editado correctamente';
    } catch (PDOException $e) {
        return false;
    }
}

function eliminarCantante($id)
{
    try {
        $conexion = openBD();

        $sentenciaText = "DELETE FROM musica.cantantes WHERE id = :id";
        $sentencia = $conexion->prepare($sentenciaText);
        $sentencia->bindParam(':id', $id);
        $sentencia->execute();

        $_SESSION['mensaje'] = 'Registro eliminado correctamente';
    } catch (PDOException $e) {
        return false;
    }
}
