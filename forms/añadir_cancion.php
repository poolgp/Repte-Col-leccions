<?php
require_once('../php_librarys/bd.php');
$cantantes = selectCantantes();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Canción</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h2 class="mt-5">Añadir Canción</h2>
        <form action="../php_controllers/cancionController.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre de la Canción</label>
                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre de la Canción" required>
            </div>
            <div class="form-group">
                <label for="cantante_id">Cantantes</label>
                <select multiple class="form-control" id="cantante_id" name="cantante_id[]">
                    <?php foreach ($cantantes as $cantante) : ?>
                        <option value="<?php echo $cantante['id']; ?>"><?php echo $cantante['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="insertCancion">Aceptar</button>
            <a href="../index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>