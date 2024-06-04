<?php
require_once('../Repte-Col-leccions/php_librarys/bd.php');
$cantantes = selectCantantes();
$jointPaises =  jointPais();
$canciones = selectCanciones();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MusicaApp</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>

<body>
    <?php include_once("./php_partials/navBar.php") ?>

    <div class="container mt-5">
        <div class="row">
            <?php foreach ($cantantes as $cantante) : ?>
                <div class="card" style="width: 18rem;">
                    <img src="<?php echo $cantante['imagen']; ?>" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php echo $cantante['nombre'] ?>
                        </h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <?php echo $cantante['fecha_nacimiento'] ?>
                        </li>
                        <li class="list-group-item">
                            <?php
                            foreach ($jointPaises as $jointPais) {
                                if ($jointPais['id'] == $cantante['id']) {
                                    echo $jointPais['nombre_pais'];
                                    break;
                                }
                            }
                            ?>
                        </li>
                        <li class="list-group-item">
                            <?php echo $cantante['canciones'] ? $cantante['canciones'] : 'No tiene canciones asignadas'; ?>
                        </li>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>