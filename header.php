<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Comercio - Vincenzo Bonelli PHP 3</title>
  <link rel="stylesheet" href="css/principal.css">
</head>

<body>
  <header id="cabecera-principal">
    <div class="marca">
      <a href="./">Compra todo</a>
    </div>
    <nav id="nav-principal">
      <ul>
        <li>
          <a href="./">Productos</a>
        </li>
        <?php if (!isset($_SESSION['username']) or $_SESSION['username'] ===  '') { ?>
          <li>
            <a href="./register.php">Registrarse</a>
          </li>
          <li>
            <a href="./login.php">Entrar</a>
          </li>
          <?php } else {
          if ((int)$_SESSION['tipo'] === 1) { ?>
            <li>
              <a href="./producto.php">Agregar producto</a>
            </li>
            <li>
              <a href="./perfil.php" class="link-icono"><img class="link-icono-img" src="./imagenes/user.png" alt="Tu perfil."><?php echo $_SESSION['username'] ?></a>
            </li>
          <?php }
          if ((int)$_SESSION['tipo'] === 2) { ?>
            <li>
              <a href="./carrito.php"><img src="./imagenes/cesta.png" alt="Tu cesta" class="link-icono-img"> Tu carrito</a>
            </li>
            <li>
              <a href="./perfil.php" class="link-icono"><img class="link-icono-img" src="./imagenes/user.png" alt="Tu perfil."><?php echo $_SESSION['username'] ?></a>
            </li>
          <?php } ?>
          <li>
            <a href="./logout.php">Salir</a>
          </li>
        <?php } ?>
      </ul>
    </nav>
  </header>
  <section class="contenido">