<?php
  session_start();
  if($_SESSION["rank"] !== "spravce") {
    http_response_code( 403 );
    exit();
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>SPŠE galerie emotů</title>
</head>
<body>
  <?php include 'header.php'; ?>
  <main>
    <a href="/phpinfo.php">PHP info</a><br>
    <a href="/logs/log.txt">Logy</a>
    <br>Session<br>
    <?= var_dump($_SESSION) ?>
  </main>
</body>
</html>