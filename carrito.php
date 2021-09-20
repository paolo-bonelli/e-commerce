<?php session_start(); ?>

<?php include './conexion.php'; ?>

<?php include './header.php'; ?>

<?php
if (isset($_SESSION['username']) and $_SESSION['username'] != '') {
  $sql_carrito = "SELECT * FROM Carritos WHERE id_usuario='{$_SESSION['username']}'";
  $resultado_carrito = mysqli_query($link, $sql_carrito);
  $errores = array();

  if ($resultado_carrito) {
    $carrito = mysqli_fetch_array($resultado_carrito, MYSQLI_ASSOC);
    $sql_en_carrito = "SELECT p.id_producto, p.nombre, p.precio, p.descripcion, p.imagen, enc.cantidad FROM Productos AS p INNER JOIN EnCarrito as enc ON p.id_producto=enc.id_producto WHERE enc.id_carrito={$carrito['id_carrito']}";
    $resultado_en_carrito = mysqli_query($link, $sql_en_carrito);
  
    if ($resultado_en_carrito) {
  
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
        if (isset($_POST['id_producto']) and $_POST['id_producto'] != '') {
          $sql_producto_en_carrito = "SELECT cantidad FROM EnCarrito WHERE id_carrito='{$carrito['id_carrito']}' and id_producto='{$_POST['id_producto']}'";
          $resultado_producto_en_carrito = mysqli_query($link, $sql_producto_en_carrito);

          if ($producto_en_carrito = mysqli_fetch_array($resultado_producto_en_carrito, MYSQLI_ASSOC)) {
            $sql_inventario = "SELECT cantidad FROM Productos WHERE id_producto={$_POST['id_producto']}";
  
            if ($resultado_inventario = mysqli_query($link, $sql_inventario)) {
              $en_inventario = mysqli_fetch_array($resultado_inventario);
              $disponibles = (int)$producto_en_carrito['cantidad'] +  (int)$en_inventario['cantidad'];
              $sql_elim_carrito = "DELETE FROM EnCarrito WHERE id_carrito='{$carrito['id_carrito']}' and id_producto='{$_POST['id_producto']}'";
  
              if (mysqli_query($link, $sql_elim_carrito)) {
                $sql_producto = "UPDATE Productos SET cantidad={$disponibles} WHERE id_producto={$_POST['id_producto']}";
  
                if (!mysqli_query($link, $sql_producto)) {
                  array_push($errores, "Error: " . mysqli_error($link));
                }
              } else {
                array_push($errores, "Error: " . mysqli_error($link));
              }
            } else {
              array_push($errores, "Error: " . mysqli_error($link));
            }
          } else {
            array_push($errores, "Error: " . mysqli_error($link));
          }
        } else {
          array_push($errores, "No hay un producto especificado");
        }
      }

      if (sizeof($errores) > 0) {
        foreach ($errores as $error) { ?>
<div class="error"><small><?php echo $error ?></small></div>
<?php }
      }

      $resultado_nuevo_carrito = mysqli_query($link, $sql_en_carrito);
      $total = 0;

      if (mysqli_num_rows($resultado_nuevo_carrito) > 0) {
        while ($producto = mysqli_fetch_array($resultado_nuevo_carrito)) {
          $subtotal = (float)$producto['precio'] * (int)$producto['cantidad'];
          $total += $subtotal; ?>
<article class="agregar">
  <figure>
    <img src="./imagenes/productos/<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>">
    <figcaption><?php echo $producto['nombre']; ?></figcaption>
  </figure>
  <section class="producto-info">
    <b>
      <p><?php echo $producto['precio']; ?> $</p>
    </b>
    <p>En carrito <b><?php echo $producto['cantidad']; ?></b></p>
    <p>Sub-total: <b><?php echo $subtotal; ?> $</b></p>
  </section>
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
    <input type="image" src="./imagenes/quitar-cesta.png" alt="Cancelar <?php echo $producto['nombre']; ?>">
  </form>
</article>
<?php } ?>
<section class="total">
  <p>Total a pagar: <b><?php echo $total; ?> $</b></p>
  <a href="./comprar.php" class="boton">Comprar</a>
</section>
<?php }
    }
  } else { ?>
<h2>Aparte su primer producto <a href="./">Aquí</a></h2>
<?php }
} else { ?>
<meta http-equiv="refresh" content="0;URL=./login.php" />
<?php }
mysqli_free_result($resultado_carrito);
mysqli_free_result($resultado_en_carrito);
if (isset($resultado_producto_en_carrito)) mysqli_free_result($resultado_producto_en_carrito);
if (isset($resultado_inventario)) mysqli_free_result($resultado_inventario); 
if (isset($resultado_nuevo_carrito)) mysqli_free_result($resultado_nuevo_carrito); ?>

<?php include './footer.php'; ?>