<?php session_start() ?>
<?php
  if(isset($_SESSION["id"])) {
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
    <h1>Přihlášení</h1>
    <p>Pokud nebude nalezen účet s daným jménem, bude vytvořen.</p>
    <form action="/login.php" method="POST">
      <div class="form-input">
        <label for="username">Uživatelské jméno</label><br>
        <input type="text" name="username" id="username" required><br>
      </div>

      <div class="form-input">
        <label for="password">Heslo</label><br>
        <input type="password" name="password" id="password" required><br>
      </div>

      <div class="form-input">
        <input type="submit">
      </div>
    </form>
    <p>Vaše heslo je s námi v bezpečí, avšak z důvodu použití http protokolu vám může být odcizeno třetí stranou.</p>
    <p>UPOZORNĚNÍ: Tento web je školním projektem, důsledně nedoporučuji zadávat jakékoli citlivé informace. Za nezávadnost uživateli vložených dat neručím. Máte-li pocit, že se na této stránce nachází Vaše osobní údaje, neprodleně nás kontaktuje na adrese rybarp@spseplzen.cz.</p>
    <?php
      $username = $password = "";
      $usernameErr = $passwordErr = "";

      function fix_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
      }

      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["username"])) {
          $usernameErr = "Nezadal jste jméno";
        } else {
          $username = fix_input($_POST["username"]);
          // check if name only contains letters and whitespace
          if (!preg_match("/^[a-zA-Z0-9_]*$/",$username)) {
            $usernameErr = "Jméno obsahuje nepovolené znaky";
          }
          else if (strlen($username) > 255) {
            $usernameErr = "Jméno je moc dlouhé";
          }
        }

        if (empty($_POST["password"])) {
          $passwordErr = "Nezadal jste heslo";
        } else {
          $password = fix_input($_POST["password"]);
          // check if pass only contains letters and whitespace
          if (!preg_match("/^[a-zA-Z0-9_]*$/",$password)) {
            $passwordErr = "Heslo obsahuje nepovolené znaky";
          }
          else if (strlen($password) > 255) {
            $passwordErr = "Heslo je moc dlouhé";
          }
        }

        if($usernameErr == "" && $passwordErr == "") {
          $conn = new mysqli("sql5.webzdarma.cz", "spsewzcz4279", "asdgasdg46321354*FDSFS", "spsewzcz4279");
          if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
          }
          $stmt = $conn->prepare("SELECT username, password, `rank`, `id` FROM user WHERE username = ?");
          $stmt->bind_param("s", $username);
          if(!$stmt) {
            echo "FATAL ERROR";
          }
          $stmt->execute();
          $stmt->bind_result($dbUsername, $dbPassword, $dbRank, $dbId);
          if ($stmt->fetch()) {
            //přihlášení
            $isCorrectPassword = password_verify($password, $dbPassword);
            if($isCorrectPassword) {
              echo "Vítejte, " . $dbRank . ".";
              echo '<script>window.location.replace("/index.php");</script>';
              $_SESSION['id'] = $dbId;
              $_SESSION['username'] = $dbUsername;
              $_SESSION['rank'] = $dbRank;
            }
            else {
              echo "Zadal jste špatné heslo.";
            }
          }
          else {
            //registrace
            $stmt = $conn->prepare("INSERT INTO user (username, password, `rank`) VALUES (?, ?, ?)");
            $options = [
              'cost' => 12,
            ];
            $passwordHash = password_hash($password, PASSWORD_BCRYPT, $options);
            $defaultRank = "uzivatel";
            $stmt->bind_param("sss", $username, $passwordHash, $defaultRank);
            $stmt->execute();
            echo "Uživatel " . $username . " byl úspěšně zaregistrován. Vaše hodnost je " . $defaultRank . ".";
            echo '<script>window.location.replace("/index.php");</script>';
            $_SESSION['id'] = $stmt->insert_id;
            $_SESSION['username'] = $username;
            $_SESSION['rank'] = $defaultRank;
          }
        }
        else {
          echo "<h3>Nalezlo se několik problémů</h3>";
          echo "<p>" . $usernameErr . "</p>";
          echo "<p>" . $passwordErr . "</p>";
        }
      }
    ?>
  </main>
</body>
</html>