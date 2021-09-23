<?php
  session_start();
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
    <h1>
  <?php
    if(!isset($_SESSION["id"])) {
      http_response_code( 403 );
      exit();
    }
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
      $operace = $emote_id = 0;
      $user_id = $_SESSION["id"];
      if($_GET["operace"] === "1") {
        $operace = 1;
      }
      else {
        $operace = -1;
      }
      if($_GET["emote"] > -1) {
        $emote_id = $_GET["emote"];
      }
      else {
        http_response_code( 404 );
        exit();
      }

      $conn = new mysqli("sql5.webzdarma.cz", "spsewzcz4279", "asdgasdg46321354*FDSFS", "spsewzcz4279");
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }
      $stmt = $conn->prepare("SELECT `id` FROM hodnoceni WHERE `emote_id` = ? AND `user_id` = ?");
      $stmt->bind_param("ii", $emote_id, $user_id);
      if(!$stmt) {
        echo "FATAL ERROR";
      }
      $stmt->execute();
      $stmt->bind_result($id);
      if ($stmt->fetch()) {
        //už hodnotil
        echo 'Tento emote už jste hodnotil.';
      }
      else {
        //teď hodnotit
        $conn = new mysqli("sql5.webzdarma.cz", "spsewzcz4279", "asdgasdg46321354*FDSFS", "spsewzcz4279");
        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }
        $stmt = $conn->prepare("INSERT INTO hodnoceni (`emote_id`, `user_id`, `hodnoceni`) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $emote_id, $user_id, $operace);
        if(!$stmt) {
          echo "FATAL ERROR";
        }
        $stmt->execute();

        //přepočítat emote hodnocení
        $conn = new mysqli("sql5.webzdarma.cz", "spsewzcz4279", "asdgasdg46321354*FDSFS", "spsewzcz4279");
        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }
        $stmt = $conn->prepare("SELECT `hodnoceni` FROM hodnoceni WHERE emote_id = ?");
        $stmt->bind_param("i", $emote_id);
        if(!$stmt) {
          echo "FATAL ERROR";
        }
        $stmt->execute();
        $stmt->bind_result($hodnoceni);
        $soucet = 0;
        while ($stmt->fetch()) {
          $soucet += $hodnoceni;
        }

        //uložit
        $conn = new mysqli("sql5.webzdarma.cz", "spsewzcz4279", "asdgasdg46321354*FDSFS", "spsewzcz4279");
        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }
        $stmt = $conn->prepare("UPDATE emote SET hodnoceni = ? WHERE `id` = ?");
        $stmt->bind_param("ii", $soucet, $emote_id);
        if(!$stmt) {
          echo "FATAL ERROR";
        }
        $stmt->execute();
        echo 'Hodnocení proběhlo úspěšně.';
      }
    }
  ?>
  </h1>
  <a href="/gallery.php">Zpět do galerie</a>
  </main>
</body>
</html>

