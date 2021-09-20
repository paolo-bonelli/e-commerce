<?php session_start(); ?>

<?php include './conexion.php'; ?>

<?php include './header.php'; ?>

<?php
$sql_carrito = "SELECT * FROM Carritos WHERE id_usuario='{$_SESSION['username']}'";
$resultado_carrito = mysqli_query($link, $sql_carrito);
$errores = array();

if ($resultado_carrito) {
  $carrito = mysqli_fetch_array($resultado_carrito, MYSQLI_ASSOC);
  $sql_en_carrito = "SELECT p.id_producto, p.precio, p.descripcion, enc.cantidad FROM Productos AS p INNER JOIN EnCarrito as enc ON p.id_producto=enc.id_producto WHERE enc.id_carrito={$carrito['id_carrito']}";
  $resultado_en_carrito = mysqli_query($link, $sql_en_carrito);
  if (mysqli_num_rows($resultado_en_carrito) > 0) {
    $total = 0;
    while ($en_carrito = mysqli_fetch_array($resultado_en_carrito, MYSQLI_ASSOC)) {
      $total += (int)$en_carrito['cantidad'] * (float)$en_carrito['precio'];
    }
    $sql_compra = "INSERT INTO Compra(id_usuario,total) VALUES ('{$_SESSION['username']}', '{$total}')";
    if (!mysqli_query($link, $sql_compra)) {
      array_push($errores, "Error: " . mysqli_error($link));
    } else {
      $sql_eliminar = "DELETE FROM EnCarrito WHERE id_carrito='{$carrito['id_carrito']}'";
      if (mysqli_query($link, $sql_eliminar)) { ?>
        <meta http-equiv="refresh" content="0;URL=./perfil.php" />
  <?php } else {
        array_push($errores, "Error: " . mysqli_error($link));
      }
    }
  } else {
    array_push($errores, "No tiene productos en su carrito");
  }
} else {
  array_push($errores, "Usted no ha reservado su primer producto");
}
foreach ($errores as $error) { ?>
  <div class="error"><small><?php echo $error ?></small></div>
<?php }
mysqli_free_result($resultado_carrito);
?>

<?php include './footer.php'; ?>