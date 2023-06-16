<?php
    $resultado = $_GET['resultado']??null;
    //echo $_GET['resultado'];
    require '../includes/funciones.php';
    
    incluirTemplate('header');
?>

    <main class="contenedor seccion">
        <h1>Admisntrador de BienesRaices</h1>
        <?php if(intval($resultado) === 1): ?>
            <p class="alerta exito">Anuncio creado correctamente</p>
        <?php endif ?>
        <a href="/bienesraices/admin/propiedades/crear.php" class="boton boton-verde">Nueva Propiedad</a>
    </main>

<?php
    incluirTemplate('footer');
?>