<?php session_start(); ?>

<?php include('./conexion.php'); ?>

<?php include('./header.php'); ?>

<?php
$sql = "SELECT * FROM Productos WHERE cantidad > 0";
$productos = mysqli_query($link, $sql);
if (mysqli_num_rows($productos) > 0) { ?>
  <h2>Productos disponibles</h2>
  <section id="productos">
    <?php while ($producto = mysqli_fetch_array($productos, MYSQLI_ASSOC)) { ?>
      <article class="producto">
        <figure>
          <img class="producto-imagen" src="./imagenes/productos/<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>">
          <figcaption>
            <h3><?php echo $producto['nombre']; ?></h3>
          </figcaption>
        </figure>
        <section class="producto-info">
          <b><?php echo $producto['precio']; ?> $</b>
          <p>Disponibles: <b><?php echo $producto['cantidad']; ?></b> </p>
          <p><?php echo $producto['descripcion']; ?></p>
        </section>
        <?php if (isset($_SESSION['tipo'])) {
          if ((int)$_SESSION['tipo'] === 2) { ?>
            <a class=" boton" href="./agregar-producto.php?pro=<?php echo $producto['id_producto']; ?>"><img src="./imagenes/agregar-cesta.png" alt="Agrega el producto a la cesta."></a>
        <?php }
        } ?>
      </article>
    <?php } ?>
  </section>
<?php } else { ?>

  <h2>No hay productos disponibles</h2>

<?php }
mysqli_free_result($productos);
?>

<?php include('./footer.php'); ?>