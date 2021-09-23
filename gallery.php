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
    <h1>Galerie</h1>
    <?php
      if(!isset($_SESSION["id"])) {
        echo '<p>Věděli jste, že přihlášení uživatelé mohou hodnotit emoty? <a href="/login.php">Přihlašte se nebo se registrujte nyní!</a></p>';
      }
    ?>
    <form action="/gallery.php" method="GET">
      <label for="kategorie">Kterou kategorii chcete zobrazit?</label>
      <select name="kategorie" id="kategorie">
        <option value="všechny">všechny</option>
        <?php
          require_once("kategorie.php");
          for($i = 0; $i < sizeof($kategorie); $i++) {
            echo "<option value='" . $kategorie[$i] . "'>" . $kategorie[$i] . "</option>";
          }
        ?>
      </select>
      <input type="submit">
    </form>
    <?php
      function fix_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
      }
      if(isset($_GET["kategorie"]) && in_array($_GET["kategorie"], $kategorie)) {
        $filtr = fix_input($_GET["kategorie"]);
        $conn = new mysqli("sql5.webzdarma.cz", "spsewzcz4279", "asdgasdg46321354*FDSFS", "spsewzcz4279");
        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }
        $stmt = $conn->prepare("SELECT name, description, gif, server, category, file, `user_id`, hodnoceni, id FROM emote WHERE category = ? ORDER BY hodnoceni DESC");
        if(!$stmt) {
          echo "FATAL ERROR";
        }
        $stmt->bind_param("s", $filtr);
        $stmt->execute();
        $stmt->bind_result($name, $description, $gif, $server, $category, $file, $user_id, $hodnoceni, $emote_id);
      }
      else {
        $conn = new mysqli("sql5.webzdarma.cz", "spsewzcz4279", "asdgasdg46321354*FDSFS", "spsewzcz4279");
        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }
        $stmt = $conn->prepare("SELECT name, description, gif, server, category, file, `user_id`, hodnoceni, id FROM emote ORDER BY hodnoceni DESC");
        if(!$stmt) {
          echo "FATAL ERROR";
        }
        $stmt->execute();
        $stmt->bind_result($name, $description, $gif, $server, $category, $file, $user_id, $hodnoceni, $emote_id);
      }
      
    ?>
    <div class="gallery">
      <?php
        while ($stmt->fetch()) {
          $conn2 = new mysqli("sql5.webzdarma.cz", "spsewzcz4279", "asdgasdg46321354*FDSFS", "spsewzcz4279");
          $stmt2 = $conn2->prepare("SELECT username FROM user WHERE id = ?");
          if(!$stmt2) {
            echo "FATAL ERROR<br>";
          }
          $stmt2->bind_param("i", $user_id);
          $stmt2->execute();
          $stmt2->bind_result($username);
          $stmt2->fetch();
          $tableData = "";
          $tableData .= "<td>" . $hodnoceni . "</td>";
          if($server == 1) {
            $tableData .= "<td>ano</td>";
          }
          else {
            $tableData .= "<td>ne</td>";
          }
          if($gif == 1) {
            $tableData .= "<td>ano</td>";
          }
          else {
            $tableData .= "<td>ne</td>";
          }
          $tableData .= "<td>" . $category . "</td>";
          $tableData .= "<td>" . $username . "</td>";

          echo "<div class='gallery-item'><h3>" . $name . "</h3>"
          . "<p>" . $description . "</p>"
          . "<table><tr><th>Hodnocení</th><th>Na serveru</th><th>GIF</th><th>Kategorie</th><th>Přidal</th></tr>"
          . "<tr>" . $tableData . "</tr>"
          . "</table><br>"
          . "<img src='/assets/emotes/" . $file . "'>";

          if(isset($_SESSION["id"])) {
            echo '<div class="hodnoceni">Hodnotit: <a class="upvote" href="/hodnotit.php?operace=1&emote=' . $emote_id . '">+1</a>'
            . '<a class="downvote" href="/hodnotit.php?operace=-1&emote=' . $emote_id . '">-1</a></div>';
          }

          echo "</div>";
        }
      ?>
    </div>
  </main>
</body>
</html>