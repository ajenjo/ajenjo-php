<?php
date_default_timezone_set('America/Santiago');

require "../../../vendor/autoload.php";
require "../../../src/ajenjo/ajenjo.php";

use \ajenjo\ajenjo;

// Crea a new ajenjo
$ajenjo = new ajenjo([
  // "mode" => "demo",
  "URLConnect" => "http://postulacion.cottolengo.cl:8080/",
  // "URL" => "http://ajenjo:30700/"
  ]);


/******************************************************************************/
$link_to_login = $ajenjo->urls->login;
/******************************************************************************/

?>
<!DOCTYPE html>
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
      <h1>Inicia Sesión</h1>

      <ul>
        <li><a href="../watchSession/example.php">Ver Status</a></li>
      </ul>

      <a class="waves-effect waves-light btn blue accent-2" href="<?php echo $link_to_login ?>" target="_blank">Iniciar Sesión</a>
      <h4>o ingresando a</h4>
      <div class="section white-text blue accent-1"><?php echo $link_to_login ?></div>
    </div>
  </body>
</html>
