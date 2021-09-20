<?php session_start(); ?>

<?php include './conexion.php'; ?>

<?php include './header.php'; ?>

<?php
if (isset($_SESSION['username']) and $_SESSION['username'] != '') {
  $sql_compras = "SELECT * FROM Compra WHERE id_usuario='{$_SESSION['username']}'";
  $resultados_compras = mysqli_query($link, $sql_compras);
  if ($resultados_compras) { ?>
    <h2>Tus compras</h2>
    <section class="compras">
      <?php while ($compra = mysqli_fetch_array($resultados_compras, MYSQLI_ASSOC)) { ?>
        <article class="compra">
          <p>Fecha: <?php echo date("d M y", strtotime($compra['fecha'])); ?></p>
          <p>Total: <b><?php echo $compra['total']; ?> $</b></p>
        </article>
      <?php } ?>
    </section>
  <?php } else { ?>
    <h3>No ha realizado ninguna compra.</h3>
  <?php }
} else { ?>
  <meta http-equiv="refresh" content="0;URL=./login.php" />
<?php }
?>

<?php include './footer.php'; ?>