<?php
  session_start();
  if($_SESSION["rank"] !== "spravce") {
    http_response_code( 403 );
    exit();
  }
  phpinfo();
?>