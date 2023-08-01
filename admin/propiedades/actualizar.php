<?php
    require '../../includes/funciones.php';
    $auth = estaAutenticado();
    if(!$auth){
        header('Location: /bienesraices/index.php');
    }

    //Validar el id en la URL
    $id = $_GET['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if(!$id){
        header('Location: /bienesraices/admin/index.php');
    }
    //DB
    require '../../includes/config/database.php';
    $db = conectarDB();


    //Consulta para obtener los datos de la propiedad
    $consulta = "SELECT * FROM propiedades WHERE id = $id";
    $resultado = mysqli_query($db, $consulta);
    $propiedad = mysqli_fetch_assoc($resultado);

    // echo '<pre>';
    // var_dump($propiedad);
    // echo '</pre>';


    //Consulta para oobtener los vendedores de la BD
    $consulta = 'SELECT * FROM vendedores';
    $resultado = mysqli_query($db, $consulta);

    //Arreglo con mensajes de errores
    $errores = [];

    $titulo = $propiedad['titulo'];
    $precio = $propiedad['precio'];
    $descripcion = $propiedad['descripcion'];;
    $habitaciones = $propiedad['habitaciones'];;
    $wc = $propiedad['wc'];;
    $estacionamiento = $propiedad['estacionamiento'];;
    $vendedorId = $propiedad['vendedorId'];;
    $imagenPropiedad = $propiedad['imagen'];


    //Ejecutar el codigo despues que el usuarrio envía el formulario
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        // echo '<pre>';
        // var_dump($_POST);
        // echo '</pre>';

        // echo '<pre>';
        // var_dump($_FILES);
        // echo '</pre>';

        //exit;
        $titulo = mysqli_real_escape_string( $db, $_POST['titulo']);
        $precio = mysqli_real_escape_string( $db, $_POST['precio']);
        $descripcion = mysqli_real_escape_string( $db, $_POST['descripcion']);
        $habitaciones = mysqli_real_escape_string( $db, $_POST['habitaciones']);
        $wc = mysqli_real_escape_string( $db, $_POST['wc']);
        $estacionamiento = mysqli_real_escape_string( $db, $_POST['estacionamiento']);
        $vendedorId = mysqli_real_escape_string( $db, $_POST['vendedor']);
        $creado = date('Y/m/d');

        //Asignar FILES a una variable
        $imagen = $_FILES['imagen'];
       
        if(!$titulo){
            $errores[] = "El título es obligatorio";
        }
        if(!$precio){
            $errores[] = "El precio es obligatorio";
        }
        if(strlen($descripcion)<50 ){
            $errores[] = "La descripción es obligatoria y debe tener al menos 50 caracteres";
        }
        if(!$habitaciones){
            $errores[] = "El numero de habitaciones es obligatorio";
        }
        if(!$wc){
            $errores[] = "El numero de baños es obligatorio";
        }
        if(!$estacionamiento){
            $errores[] = "El numero de lugares de estacionamientos es obligatorio";
        }
        if(!$vendedorId){
            $errores[] = "Elige un vendedor";
        }
        

        //Validacion por tamaños (Max 1mb)
        $medida = 1000*1000;
        if($imagen['size'] > $medida){
            $errores[] = "La imagen debe ser menor de 100 kb";
        }

        // echo '<pre>';
        // var_dump($errores);
        // echo '</pre>';

        //Revisar que el arreglo de errores esté vacío
        if(empty($errores)){

            //Subida de archivos
            //Crear carpeta
            $carpetaImagenes = '../../imagenes/';

            if(!is_dir($carpetaImagenes)){
                 mkdir($carpetaImagenes);
            }
            $nombreImagen = '';
            
            if($imagen['name']){
                //eliminar la imagen previa en caso de actualizacion 
                unlink($carpetaImagenes . $propiedad['imagen']);

                // //Generar nombre unico
                $nombreImagen = md5( uniqid(rand(), true) ) . ".jpg";

                // //Subir la imagen
                move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);
            }else{
                $nombreImagen = $propiedad['imagen'];
            }
            
            
            
            //Insertar en BD
            $query = " UPDATE propiedades 
                       SET titulo = '$titulo', precio = '$precio', imagen = '$nombreImagen', descripcion = '$descripcion', habitaciones = $habitaciones, wc = $wc, estacionamiento = $estacionamiento, vendedorId = $vendedorId
                       WHERE id = $id";
            
            //echo $query;

            //exit;
            $resultado = mysqli_query($db, $query);
            
            if($resultado){
                //echo 'Insertado correctamente';
                header('Location: /bienesraices/admin/index.php?resultado=2');
            }
        }

        
    }

    
    incluirTemplate('header');
?>

    <main class="contenedor seccion">
        <h1>Actualizar Propiedad</h1>
        <a href="/bienesraices/admin/index.php" class="boton boton-verde">Volver</a>

        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
            
        <?php endforeach ?>

        <form class="formulario" method="POST"  enctype="multipart/form-data">
            <fieldset>
                <legend>Información General</legend>

                <label for="titulo">Titulo</label>
                <input type="text" id="titulo" name="titulo" placeholder="Título Propiedad" value="<?php echo $titulo; ?>">

                <label for="precio">Precio</label>
                <input type="number" id="precio" name="precio" placeholder="Precio Propiedad" value="<?php echo $precio; ?>">

                <label for="imagen">Imagen</label>
                <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen">
                <img src="/bienesraices/imagenes/<?php echo $imagenPropiedad ?>" alt="imagen propiedad" class="imagen-small">

                <label for="descripcion">Descripción</label>
                <textarea  id="descripcion" name="descripcion" ><?php echo $descripcion; ?></textarea>
            </fieldset>

            <fieldset>
                <legend>Información Propiedad</legend>

                <label for="habitaciones">Num. Habitaciones</label>
                <input type="number" id="habitaciones" name="habitaciones" placeholder="Ej. 3" min="1" max"9" value="<?php echo $habitaciones; ?>">

                <label for="wc">Num. Baños</label>
                <input type="number" id="wc" name="wc" placeholder="Ej. 3" min="1" max"9" value="<?php echo $wc; ?>">

                <label for="estacionamiento">Num. Estacionamientos</label>
                <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ej. 3" min="1" max"9" value="<?php echo $estacionamiento; ?>">
            </fieldset>

            <fieldset>
                <legend>Vendedor</legend>
                <select name="vendedor">
                    <option value="">---Seleccione---</option>
                    <?php while($vendedor = mysqli_fetch_assoc($resultado)): ?>
                        <option  <?php echo $vendedorId === $vendedor['id'] ? 'selected' : ''; ?> value="<?php echo $vendedor['id']; ?>"> <?php echo $vendedor['nombre'] . " " . $vendedor['apellido']; ?> </option>
                    <?php endwhile; ?>
                </select>
            </fieldset>
            <input type="submit" value="Actualizar Propiedad" class="boton boton-verde">
        </form>
    </main>

<?php
    incluirTemplate('footer');
?>