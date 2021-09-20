<?php session_start(); ?>

<?php include './conexion.php'; ?>

<?php include './header.php'; ?>

<?php
// Se chequea si la entrada pro es válida
if (isset($_GET['pro']) and filter_var($_GET['pro'], FILTER_VALIDATE_INT, array("options" => array("min_range" => "1")))) {
  $id_producto = (int)$_GET['pro'];
  $sql_producto = "SELECT * FROM Productos WHERE id_producto={$id_producto}";
  $resultado_producto = mysqli_query($link, $sql_producto);

  // Se chequea el producto exista
  if (mysqli_num_rows($resultado_producto) === 1) {
    $producto = mysqli_fetch_array($resultado_producto, MYSQLI_ASSOC);
    $errores = array();

    // Se chequea si se envió el formulario con el método POST
    if ($_SERVER['REQUEST_METHOD'] === "POST") {

      // Se chequea que la entrada id_producto es válida
      if (isset($_POST['id_producto']) and filter_var($_POST['id_producto'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)))) {

        // Se chequa si el producto solicitado es el producto actual
        if ((int)$_POST['id_producto'] === (int)$_GET['pro']) {

          // Se verifica si la entrada cantidad es válida
          if (filter_var($_POST['cantidad'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)))) {
            $sql_carritos = "SELECT * FROM Carritos WHERE id_usuario='{$_SESSION['username']}'";
            $carritos = mysqli_query($link, $sql_carritos);
            // Se chequea que el usuario posee un carrito
            if (mysqli_num_rows($carritos) === 0) {
              $sql_crear_carrito = "INSERT INTO Carritos(id_usuario) VALUES ('{$_SESSION['username']}')";

              // Se chequea que el carrito sea creado correctamente
              if (!mysqli_query($link, $sql_crear_carrito)) {
                die(mysqli_error($link));
              } else {
                $carritos = mysqli_query($link, $sql_carritos);
              }
            }
            $carrito = mysqli_fetch_array($carritos, MYSQLI_ASSOC);
            $sql_en_carritos = "SELECT * FROM EnCarrito WHERE id_carrito='{$carrito['id_carrito']}' and id_producto='{$_POST['id_producto']}'";
            $query_en_carritos = mysqli_query($link, $sql_en_carritos);

            // Se chequea que hay la cantidad disponible
            if ((int)$_POST['cantidad'] <= (int)$producto['cantidad']) {
              $unidades_restantes = (int)$producto['cantidad'] - (int)$_POST['cantidad'];
              $sql_unidades_restantes = "UPDATE Productos SET cantidad={$unidades_restantes} WHERE id_producto={$producto['id_producto']}";

              // Se chequea que se actualiza la cantidad disponible del producto
              if (!mysqli_query($link, $sql_unidades_restantes)) {
                array_push($errores, "Error: " . mysqli_error($link));
              } else {

                // Se chequea si el producto se encuentra en el carrito
                if (mysqli_num_rows($query_en_carritos) === 0) {
                  $sql_en_carritos = "INSERT INTO EnCarrito(id_carrito,id_producto,cantidad) VALUES ('{$carrito['id_carrito']}','{$_POST['id_producto']}','{$_POST['cantidad']}')";
                } else {
                  // Se agregará la nueva cantidad al carrito
                  $en_carrito = mysqli_fetch_array($query_en_carritos, MYSQLI_ASSOC);
                  $unidades_a_comprar = (int)$_POST['cantidad'] + (int)$en_carrito['cantidad'];
                  $sql_en_carritos = "UPDATE EnCarrito SET cantidad={$unidades_a_comprar} WHERE id_carrito={$carrito['id_carrito']} and id_producto={$_GET['pro']}";
                }

                // Se chequea que se agrega correctamente al carrito
                if (!mysqli_query($link, $sql_en_carritos)) {
                  array_push($errores, "Error: " . mysqli_error($link));
                  // Al no poder agregarlo al carrito, devolvemos los productos al inventario
                  $unidades_restantes += (int)$_POST['cantidad'];
                  $sql_unidades_restantes = "UPDATE Productos SET cantidad={$unidades_restantes} WHERE id_producto={$producto['id_producto']}";
                }
              }
            } else {
              array_push($errores, "No hay {$_POST['cantidad']} unidades disponibles del producto: {$producto['nombre']}");
            }
          } else {
            array_push($errores, "La entrada Cantidad no es válida");
          }
        } else {
          array_push($errores, "El producto a agregar no es el selecionado");
        }
      } else {
        array_push($errores, "La producto requerido no es válido");
      }

      if (sizeof($errores) > 0) {
        foreach ($errores as $error) { ?>
          <div class="error"><small><?php echo $error ?></small></div>
        <?php }
      } else { ?>
        <meta http-equiv="refresh" content="0;URL=./carrito.php" />
      <?php }
    }

    if ($producto['cantidad'] > 0) { ?>
      <article class="agregar">
        <figure>
          <img src="./imagenes/productos/<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['descripcion']; ?>">
          <figcaption><?php echo $producto['nombre']; ?></figcaption>
        </figure>
        <section class="producto-info">
          <b><?php echo $producto['precio']; ?> $</b>
          <p>Disponibles: <b><?php echo $producto['cantidad']; ?></b> </p>
          <p><?php echo $producto['descripcion']; ?></p>
        </section>
        <form action="<?php echo $_SERVER['PHP_SELF'] . "?pro={$producto['id_producto']}"; ?>" method="post">
          <label for="cantidad">Cantidad</label>
          <input type="number" name="cantidad" id="cantidad" step="1" min="1" max="<?php echo $producto['cantidad']; ?>">
          <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
          <input type="submit" value="Agregar">
        </form>
      </article>
    <?php
    }
  } else { ?>
    <p>El producto seleccionado no es válido, ingrese <b><a href="./">aquí</a></b> para buscar un nuevo producto</p>
  <?php }
} else { ?>
  <p>Debe selecionar un producto, ingrese <b><a href="./">aquí</a></b> para buscar un producto</p>
<?php } ?>

<?php include './footer.php'; ?>