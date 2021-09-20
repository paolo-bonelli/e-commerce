<?php


session_start(); ?>

<?php include('./conexion.php'); ?>

<?php include('./header.php'); ?>

<?php
if (isset($_SESSION['username'])) { ?>
  <meta http-equiv="refresh" content="0;URL=./" />
  <?php
} else {
  $id_usuario = $email = $nombre = $apellido = $password_hash = $password = $cedula = '';
  if ($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['csrf'])) {
    $errores = array();
    $token = filter_var($_POST['csrf'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

    if ($token === $_SESSION['csrf']) {
      if (isset($_POST['id_usuario']) and $_POST['id_usuario'] != '') {
        $id_usuario = filter_var($_POST['id_usuario'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
      } else {
        array_push($errores, 'Usuario no válido');
      }

      if (isset($_POST['email']) and $_POST['email'] != '') {
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
          $email = $_POST['email'];
        } else {
          array_push($errores, 'Email no válido');
        }
      } else {
        array_push($errores, 'Debe ingresar un email');
      }

      if (isset($_POST['password'])) {
        if (strlen($_POST['password']) >= 8) {
          $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        } else {
          array_push($errores, 'La contraseña debe tener al menos 8 caracteres.');
        }
      } else {
        array_push($errores, 'No ingresó una contraseña');
      }

      if (isset($_POST['nombres'])) {
        if (strlen($_POST['nombres']) > 0) {
          $nombres = filter_var($_POST['nombres'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        } else {
          array_push($errores, 'Debe ingresar su nombre');
        }
      } else {
        array_push($errores, 'No ingresó un nombre');
      }

      if (isset($_POST['apellidos'])) {
        if (strlen($_POST['apellidos']) > 0) {
          $apellidos = filter_var($_POST['apellidos'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        } else {
          array_push($errores, 'Debe ingresar su apellido');
        }
      } else {
        array_push($errores, 'No ingresó un apellido');
      }


      if (isset($_POST['cedula']) and $_POST['cedula'] !== 0) {
        if (filter_var($_POST['cedula'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1))) === false) {
          array_push($errores, 'Este número de cédula no existe.');
        } else {
          $cedula = $_POST['cedula'];
        }
      } else {
        array_push($errores, 'Debe ingresar su cédula');
      }

      if (sizeof($errores) === 0) {
        $sql = "INSERT INTO Usuarios(id_usuario,email,password_hash,nombres,apellidos,cedula) VALUES ('{$id_usuario}','{$email}','{$password_hash}','{$nombres}','{$apellidos}','{$cedula}')";
        if (!mysqli_query($link, $sql)) {
          array_push($errores, "Error: " . mysqli_error($link));
        }
      }
    } else {
      array_push($errores, 'Se detectó un ataque CSRF.');
    }

    if (sizeof($errores) > 0) {
      foreach ($errores as $error) { ?>
        <div class="error"><small><?php echo $error ?></small></div>
      <?php }
    } else { ?>
      <meta http-equiv="refresh" content="0;URL=./" />
  <?php }
  }
  $_SESSION['csrf'] = md5(uniqid(rand(), true));
  ?>

  <h2>Registrarse</h2>

  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="insertar">
    <label for="id_usuario">Usuario</label>
    <input type="text" name="id_usuario" id="id_usuario" maxlength="20" value="<?php echo $id_usuario; ?>" required>
    <label for="password">Contraseña</label>
    <input type="password" name="password" id="password" maxlength="72" value="<?php echo $password; ?>" required>
    <label for="email">Email</label>
    <input type="email" name="email" id="email" value="<?php echo $email; ?>" required>
    <label for="nombres">Nombres</label>
    <input type="text" name="nombres" id="nombres" value=<?php echo $nombre; ?>>
    <label for="apellidos">Apellidos</label>
    <input type="text" name="apellidos" id="apellidos" value="<?php echo $apellido; ?>">
    <label for="cedula">Cedula</label>
    <input type="number" name="cedula" id="cedula" value="<?php echo $cedula; ?>">
    <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
    <input type="reset" value="Limpiar">
    <input type="submit" value="Entrar">
  </form>
<?php } ?>
<?php include('./footer.php'); ?>