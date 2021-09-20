<?php session_start(); ?>

<?php include('./conexion.php'); ?>

<?php include('./header.php'); ?>

<?php
if (isset($_SESSION['username'])) { ?>
  <meta http-equiv="refresh" content="0;URL=./" />
  <?php
} else {
  if ($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['csrf'])) {
    $errores = 0;
    $token = filter_var($_POST['csrf'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

    if (isset($_POST['user']) and $token === $_SESSION['csrf']) {
      $username = filter_var($_POST['user'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
      if (strlen($username) <= 20) {
        $sql = "SELECT * FROM Usuarios WHERE id_usuario='{$username}'";
        $query = mysqli_query($link, $sql);
        if (mysqli_num_rows($query) === 1 and isset($_POST['password'])) {
          $user = mysqli_fetch_array($query, MYSQLI_ASSOC);
          if (password_verify($_POST['password'], $user['password_hash'])) {
            $_SESSION['username'] = $user['id_usuario'];
            $_SESSION['nombres'] = $user['nombres'];
            $_SESSION['apellidos'] = $user['apellidos'];
            $_SESSION['cedula'] = $user['cedula'];
            $_SESSION['tipo'] = $user['tipo']; ?>
            <meta http-equiv="refresh" content="0;URL=./" />
      <?php } else {
            $errores++;
          }
        } else {
          $errores++;
        }
      } else {
        $errores++;
      }
    } else {
      $errores++;
    }

    if ($errores !== 0) { ?>
      <div class="error"><small>Sus credenciales no pudieron ser autenticadas</small></div>
    <?php } else { ?>
      <meta http-equiv="refresh" content="0;URL=./" />
  <?php }
  }
  $_SESSION['csrf'] = md5(uniqid(rand(), true));
  ?>

  <h2>Login</h2>

  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="insertar">
    <label for="user">Usuario</label>
    <input type="text" name="user" id="user" maxlength="20" required>
    <label for="password">Contraseña</label>
    <input type="password" name="password" id="password" maxlength="72" required>
    <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
    <input type="submit" value="Entrar">
  </form>
<?php }
if(isset($query)) mysqli_free_result($query); ?>
<?php include('./footer.php'); ?>