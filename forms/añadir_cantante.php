<?php
require_once('../php_librarys/bd.php');
$paises = selectPaises();
$canciones = selectCanciones();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Cantante</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">

        <?php require_once('../php_partials/mensajes.php'); ?>

        <?php if (isset($_SESSION['cantante'])) {
            $cantante = $_SESSION['cantante'];
            unset($_SESSION['cantante']);
        } else {
            $cantante = ['id' => '', 'imagen' => '', 'nombre' => '', 'fecha_nacimiento' => '', 'pais_id' => ''];
        } ?>

        <h2 class="mt-5">Añadir Cantante</h2>
        <form action="../php_controllers/cantanteController.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre del Cantante</label>
                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del Cantante" required autofocus value="<?php echo $cantante['nombre'] ?>">
            </div>
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required value="<?php echo $cantante['fecha_nacimiento'] ?>">
            </div>
            <div class="form-group">
                <label for="pais_id">País</label>
                <select class="form-control" id="pais_id" name="pais_id" required value="<?php echo $cantante['pais_id'] ?>">
                    <?php foreach ($paises as $pais) : ?>
                        <option value="<?php echo $pais['id']; ?>"><?php echo $pais['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="cancion_id">Canciones</label>
                <select multiple class="form-control" id="cancion_id" name="cancion_id[]">
                    <?php foreach ($canciones as $cancion) : ?>
                        <option value="<?php echo $cancion['id']; ?>"><?php echo $cancion['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="imagen">Imagen</label>
                <input type="file" class="form-control-file" id="imagen" name="imagen" required value="<?php echo $cantante['imagen'] ?>">
            </div>
            <button type="submit" class="btn btn-primary" name="insertCantante">Aceptar</button>
            <a href="../index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies (Popper.js and jQuery) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>