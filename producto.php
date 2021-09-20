<?php session_start(); ?>

<?php include './conexion.php'; ?>

<?php include './header.php'; ?>
<?php if (isset($_SESSION['username']) and $_SESSION['username'] != '' and (int)$_SESSION['tipo'] === 1) { ?>

  <?php
  $nombre = $arch = $precio = $cantidad = $descripcion = '';
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errores = array();
    if ($_POST['csrf'] === $_SESSION['csrf']) {
      if (isset($_FILES['imagen']['name']) and $_FILES['imagen']['name'] != '') {
        $extensiones = array(1 => '.gif', 2 => '.jpg', 3 => '.png');
        $temporal = $_FILES['imagen']['tmp_name'];
        $tipo = getimagesize($temporal);

        if ($ipo[2] == 1 or $tipo[2] == 2 or $tipo[2] == 3) {
          $arch = md5(uniqid($_FILES['imagen']['name'], true)) . $extensiones[$tipo[2]];

          if (!copy($temporal, './imagenes/productos/' . $arch)) {
            array_push($errores, 'La imagen no pudo ser guardada');
          } else {
            $imagen = $arch;
          }
        } else {
          array_push($errores, 'El archivo no es una imagen del tipo esperado (GIF, JPG o PNG) ');
        }
      }

      if (isset($_POST['nombre']) and $_POST['nombre'] != '') {
        $nombre  = filter_var($_POST['nombre'],  FILTER_SANITIZE_SPECIAL_CHARS);
        if (strlen($nombre) > 30) {
          array_push($errores, "El nombre no debe tener más de 30 caracteres");
        }
      } else {
        array_push($errores, "Debe ingresar un nombre.");
      }

      if (isset($_POST['precio'])) {
        if (filter_var($_POST['precio'], FILTER_VALIDATE_FLOAT, array("options" => array("min_range" => 0.01)))) {
          $precio = $_POST['precio'];
        } else {
          array_push($errores, "Debe ingresar un precio válido");
        }
      } else {
        array_push($errores, "Debe ingresar un precio");
      }

      if (isset($_POST['cantidad'])) {
        if (filter_var($_POST['cantidad'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)))) {
          $cantidad = $_POST['cantidad'];
        } else {
          array_push($errores, "Debe ingresar una cantidad válida");
        }
      } else {
        array_push($errores, "Debe ingresar una cantidad");
      }

      if (isset($_POST['descripcion']) and $_POST['descripcion'] != '') {
        $descripcion = filter_var($_POST['descripcion'], FILTER_SANITIZE_SPECIAL_CHARS);
      } else {
        array_push($errores, "Debe ingresar una descripción.");
      }

      if (sizeof($errores) === 0) {
        $sql = "INSERT INTO Productos(nombre,precio,cantidad,descripcion,imagen) VALUES ('{$nombre}','{$precio}','{$cantidad}','{$descripcion}','{$imagen}')";
        if (!mysqli_query($link, $sql)) {
          array_push($errores, "Error de BD: " . mysqli_error($link));
        }
      }
    } else {
      array_push($errores, "Se ha detectado un ataque csrf.");
    }

    if (sizeof($errores) > 0) {
      foreach ($errores as $error) { ?>
        <div class="error"><small><?php echo $error ?></small></div>
      <?php }
    } else { ?>
      <meta http-equiv="refresh" content="0;URL=./" />
  <?php }
  } ?>

  <?php $_SESSION['csrf'] = md5(uniqid(rand(), true)); ?>

  <h2>Agregar producto</h2>

  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" class="insertar" required>
    <input type="hidden" name="csrf" value='<?php echo $_SESSION['csrf']; ?>' required>
    <label for="imagen">Imagen</label>
    <input type="file" name="imagen" id="imagen" accept="image/gif, image/jpeg, image/png" required>
    <label for="nombre">Nombre</label>
    <input type="text" name="nombre" id="nombre" value="<?php echo $nombre; ?>" required>
    <label for="precio">Precio</label>
    <input type="number" name="precio" id="precio" step="0.01" value="1.00" value="<?php $precio; ?>" required>
    <label for="cantidad">Cantidad</label>
    <input type="number" name="cantidad" id="cantidad" step="1" min="1" value="<?php $cantidad; ?>" required>
    <label for="descripcion">Descripcion</label>
    <textarea name="descripcion" id="descripcion" cols="10" rows="3"><?php echo $descripcion; ?></textarea>
    <input type="reset" value="Limpiar">
    <input type="submit" value="Agregar">
  </form>
<?php } else { ?>
  <meta http-equiv="refresh" content="0;URL=./login.php" />
<?php } ?>

<?php include './footer.php'; ?>