<?php session_start() ?>

<header>
  <a href="/index.php" class="header-logo">
  <?php if($_SESSION["id"] > -1) {
    echo '<img src="/assets/ussr-logo.png" alt="logo vaší vlády">'
    . '<h1 class="ussr">SPŠE Discord</h1>';
    }
    else {
      echo '<img src="/assets/spse-logo.png" alt="logo spše plzeň">'
      . '<h1 class="spse">SPŠE Discord</h1>';
    }
   ?> 
    
  </a>
  <nav>
    <a href="/gallery.php" class="nav-item">Galerie</a>
    <?php
      if(isset($_SESSION["id"])) {
        echo '<a href="/add-emote.php" class="nav-item">Přidat emote</a>';
      }
    ?>
    
    <?php
      if($_SESSION["rank"] === "spravce") {
        echo '<a href="/admin.php" class="nav-item">Administrace</a>';
      }
    ?>
    
    <?php
      if(!isset($_SESSION["id"])) {
        echo '<a href="/login.php" class="nav-item">Přihlásit se</a>';
      }
      else {
        echo '<a href="/logout.php" class="nav-item">Odhlásit se</a>';
      }
    ?>
    
  </nav>
</header>

<?php
//logování
  $file = fopen("logs/log.txt", "a") or die("Nejde otevřít soubor");
  if(isset($_SESSION["username"])) {
    $data = $_SESSION["username"] . " " . $_SERVER['REMOTE_ADDR'] . " " . $_SERVER['REQUEST_URI'] . " " . $_SERVER['QUERY_STRING'] . "\n";
    fwrite($file, $data);
    fclose($file);
  }
  else {
    $data = $_SERVER['REMOTE_ADDR'] . " " . $_SERVER['REQUEST_URI'] . " " . $_SERVER['QUERY_STRING'] . "\n";
    fwrite($file, $data);
    fclose($file);
  }
    
?>