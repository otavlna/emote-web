<?php
  session_start();
  if(!isset($_SESSION["id"])) {
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
    <h1>Přidat emote</h1>
    <p>Vyplněním následujícího formuláře přidáte emote do naší databáze.</p>
    <form action="add-emote.php" method="post" enctype="multipart/form-data">
      <div class="form-input">
        <label for="name">Název emotu</label><br>
        <input type="text" name="name" id="name" required><br>
      </div>
      
      <div class="form-input">
        <label for="description">Popis</label><br>
        <textarea name="description" id="description" placeholder="(příklady využití, stručná historie...)" rows="4" cols="40" required></textarea><br>
      </div>

      <div class="form-input">
        <input type="radio" name="gif" id="gif-ano" value="ano" required>
        <label for="gif-ano">Je to gif</label><br>
        <input type="radio" name="gif" id="gif-ne" value="ne" required checked>
        <label for="gif-ne">Není to gif</label><br>
      </div>

      <div class="form-input">
        <input type="radio" name="server" id="server-ano" value="ano" required>
        <label for="server-ano">Aktuálně je na serveru</label><br>
        <input type="radio" name="server" id="server-ne" value="ne" required checked>
        <label for="server-ne">Aktuálně není na serveru</label><br>
      </div>

      <div class="form-input">
        <label for="category">Kategorie</label><br>
        <select name="category" id="category" required>
        <?php
          require_once("kategorie.php");
          for($i = 0; $i < sizeof($kategorie); $i++) {
            echo "<option value='" . $kategorie[$i] . "'>" . $kategorie[$i] . "</option>";
          }
        ?>
        </select><br>
      </div>

      <div class="form-input">
      <label for="file">Soubor emotu</label><br>
        <input type="file" name="file" id="file">
      </div>
      
      <p>Odesláním formuláře se vzdáváte jakýchkoli autorských práv na vložené materiály.</p>
      <div class="form-input">
        <input type="submit">
      </div>
    </form>
    

    <?php
      $name = $description = $gif = $server = $category = $file = "";
      $nameErr = $descriptionErr = $gifErr = $serverErr = $categoryErr = $fileErr = "";
      $uploadOk = 1;

      function fix_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
      }
      
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["name"])) {
          $nameErr = "Jméno emotu je povinné";
        } else {
          $name = fix_input($_POST["name"]);
          // check if name only contains letters and whitespace
          if (!preg_match("/^[a-zA-Z0-9_]*$/",$name)) {
            $nameErr = "Jméno obsahuje nepovolené znaky";
          }
          else if (strlen($name) > 255) {
            $nameErr = "Jméno je moc dlouhé";
          }
        }
    
        if (empty($_POST["description"])) {
          $descriptionErr = "Popis emotu je povinný";
        } else {
          $description = fix_input($_POST["description"]);
          if (strlen($description) > 10000) {
            $descriptionErr = "Popis je příliš dlouhý";
          }
        }
        
        if (empty($_POST["gif"])) {
          $gifErr = "Je nutno specifikovat, jedná-li se o gif";
        } else {
          $gif = fix_input($_POST["gif"]);
          if ($gif !== "ano" && $gif !== "ne") {
            $gifErr = "Emote buď je gif, nebo není";
          }
          if($gif == "ano") {
            $gif = 1;
          }
          else {
            $gif = 0;
          }
        }
    
        if (empty($_POST["server"])) {
          $serverErr = "Je nutno specifikovat, je-li emote na serveru";
        } else {
          $server = fix_input($_POST["server"]);
          if ($server !== "ano" && $server !== "ne") {
            $serverErr = "Emote buď je na serveru, nebo není";
          }
          if($server == "ano") {
            $server = 1;
          }
          else {
            $server = 0;
          }
        }
          
        if (empty($_POST["category"])) {
          $categoryErr = "Zadejte kategorii emotu";
        } else {
          $category = fix_input($_POST["category"]);
          if (!in_array($category, $kategorie)) {
            $categoryErr = "Zadaná kategorie neexistuje";
          }
        }
        
        if (empty($_FILES["file"])) {
          $fileErr = "Nenahrál jste soubor";
        } else {
          $target_dir = "assets/emotes/";
          $target_file = $target_dir . basename($_FILES["file"]["name"]);
          $file = basename($_FILES["file"]["name"]);
          $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

          // Check if image file is a actual image or fake image
          $check = getimagesize($_FILES["file"]["tmp_name"]);
          if($check !== false) {
            //echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
          } else {
            $fileErr .= " Soubor není obrázek.";
            $uploadOk = 0;
          }

          // Check if file already exists
          if (file_exists($target_file)) {
            $fileErr .= " Soubor se stejným názvem již existuje.";
            $uploadOk = 0;
          }

          // Check file size
          if ($_FILES["file"]["size"] > 500000) {
            $fileErr .= " Váš soubor je moc velký (max cca 500 KiB)";
            $uploadOk = 0;
          }

          // Allow certain file formats
          if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            $fileErr .= " Pouze JPG, JPEG, PNG a GIF formáty jsou dovoleny.";
            $uploadOk = 0;
          }

          // Check if $uploadOk is set to 0 by an error
          if ($uploadOk == 0) {
            $fileErr .= " Váš soubor nebyl nahrán.";
          // if everything is ok, try to upload file
          }
        }

        if($nameErr == "" && $descriptionErr == "" && $gifErr == "" && $serverErr == "" && $categoryErr == "" && $fileErr == "" && $uploadOk == 1) {
          if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            //echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
          } else {
            echo "FATAL ERROR";
          }
          echo "Váš soubor ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " se nahrál úspěšně";

          $conn = new mysqli("sql5.webzdarma.cz", "spsewzcz4279", "asdgasdg46321354*FDSFS", "spsewzcz4279");
          if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
          }
          $stmt = $conn->prepare("INSERT INTO emote (name, description, gif, server, category, file, `user_id`) VALUES (?, ?, ?, ?, ?, ?, ?)");
          $stmt->bind_param("ssiissi", $name, $description, $gif, $server, $category, $file, $_SESSION["id"]);
          $stmt->execute();
        }
        else {
          echo "<h3>Nalezlo se několik problémů</h3>";
          echo "<p>" . $nameErr . "</p>";
          echo "<p>" . $descriptionErr . "</p>";
          echo "<p>" . $gifErr . "</p>";
          echo "<p>" . $serverErr . "</p>";
          echo "<p>" . $categoryErr . "</p>";
          echo "<p>" . $fileErr . "</p>";
        }
      }
    ?>
  </main>
</body>
</html>