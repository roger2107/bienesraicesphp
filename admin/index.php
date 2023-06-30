<?php

    //Importar la conexion
    require '../includes/config/database.php';
    $db = conectarDB();

    //Escribir el query
    $query = "SELECT * FROM propiedades";

    //Consultar la BD (Ejecutar el query)
    $resultadoConsulta = mysqli_query($db, $query);

    //Muestra mensaje condicional
    $resultado = $_GET['resultado']??null;

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $id = $_POST['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if($id){
            //Eliminar Archivo de Imagen
            $query = "SELECT imagen FROM propiedades WHERE id = $id";
            $resultado = mysqli_query($db, $query);
            $propiedad = mysqli_fetch_assoc($resultado);
            unlink('../imagenes' .  $propiedad['imagen']);

            //Eliminar Propiedad
            $query = "DELETE FROM propiedades WHERE id = $id";
            $resultado = mysqli_query($db, $query);
            if($resultado){
                header('Location: /bienesraices/admin/index.php?resultado=3');
            }
        }
    }

    //echo $_GET['resultado'];
    require '../includes/funciones.php';
    //Incluye un template
    incluirTemplate('header');
?>

    <main class="contenedor seccion">
        <h1>Administrador de BienesRaices</h1>  
        <?php if(intval($resultado) === 1): ?>
            <p class="alerta exito">Anuncio Creado Correctamente</p>
            <?php elseif(intval($resultado) === 2): ?>
                <p class="alerta exito">Anuncio Actualizado Correctamente</p>
            <?php elseif(intval($resultado) === 3): ?>
                <p class="alerta exito">Anuncio Eliminado Correctamente</p>
        <?php endif ?>
        <a href="/bienesraices/admin/propiedades/crear.php" class="boton boton-verde">Nueva Propiedad</a>

        <table class="propiedades">
            <thead>
                <th>ID</th>
                <th>Titulo</th>
                <th>Imagen</th>
                <th>Precio</th>
                <th>Acciones</th>
            </thead>
            <tbody> <!-- Mostrar los resulados -->

                <?php  while($propiedad = mysqli_fetch_assoc($resultadoConsulta)): ?>
                <tr>
                    <td> <?php echo $propiedad['id']; ?> </td>
                    <td> <?php echo $propiedad['titulo']; ?> </td>
                    <td><img src="/bienesraices/imagenes/<?php echo $propiedad['imagen']; ?>" alt="imagen casa" class="imagen-tabla"></td>   
                    <td>$<?php echo $propiedad['precio']; ?></td>
                    <td> 
                        <form method="POST" class="w-100">
                            <input type="hidden" name="id" value="<?php  echo $propiedad['id'] ?>">
                            <input type="submit" class="boton-rojo-block" value="Eliminar">
                        </form>
                        <a href="/bienesraices/admin/propiedades/actualizar.php?id=<?php echo $propiedad['id'] ?>" class="boton-amarillo-block">Actualizar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
    </table>
    </main>
    
    
<?php

    //Cerrar la conexion 
    mysqli_close($db);
    
    incluirTemplate('footer');
?>