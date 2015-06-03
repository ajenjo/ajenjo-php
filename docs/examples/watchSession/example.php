<?php
require "../../../vendor/autoload.php";
require "../../../src/ajenjo/ajenjo.php";

use \ajenjo\ajenjo;

// Crea a new ajenjo
$ajenjo = new ajenjo("http://ajenjo:1337/");


/******************************************************************************/
$data_session = $ajenjo->data_session->body;

$url_status = $ajenjo->urls->status;

$session_data = $ajenjo->data_cookie;
/******************************************************************************/

?><!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>Panel</title>

    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.96.1/css/materialize.min.css">

    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.96.1/js/materialize.min.js"></script>

  </head>
  <body>
    <div class="container center-align">
      <h1>Tu Sesión</h1>
      <ul>
        <li><a href="../GoTologin/example.php">Iniciar Sesión</a></li>
      </ul>
      <div class="section">
        <h5 class="left-align"><a href="<?php echo $url_status ?>" class="blue-text" target="_blank">URL</a> Para El Status De La Sesión <a href="<?php echo $url_status ?>" class="blue-text" target="_blank">[open]</a>:</h5>
        <div class="z-depth-1 section white-text blue accent-2">
          <?php echo $url_status ?>
        </div>
      </div>
    </div>
    <div class="container">
      <h5>Request:</h5>
    </div>
    <div class="z-depth-1 container section grey lighten-3 section">
      <pre><?php echo json_encode($data_session,JSON_PRETTY_PRINT) ?></pre>
    </div>
    <div class="container">
      <h5>Session:</h5>
      <p class="center-align z-depth-1 section grey lighten-3"><?php echo $session_data ?></p>
    </div>
  </body>
</html>
