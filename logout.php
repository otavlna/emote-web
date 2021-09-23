<?php
  session_start();
  unset($_SESSION["id"]);
  unset($_SESSION["username"]);
  unset($_SESSION["rank"]);
  session_destroy();
  echo '<script>window.location.replace("/index.php");</script>'
?>