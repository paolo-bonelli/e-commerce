<?php session_start(); ?>

<?php include('./conexion.php'); ?>

<?php include('./header.php'); ?>

<?php
session_unset();
session_destroy();
?>
<meta http-equiv="refresh" content="0;URL=./" />

<?php include('./footer.php'); ?>