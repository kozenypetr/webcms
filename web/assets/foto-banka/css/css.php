<?php
  header("Content-type: text/css; charset: UTF-8");
  header("Cache-control: must-revalidate");
  header(sprintf("Expires: %s GMT", gmdate("D, d M Y H:i:s", time() + 10800)));

  require "../symfony/lib/lessphp/lessc.inc.php";

  header("Content-Type: text/css");
  
  $less = new lessc();
  $less->setFormatter("compressed");
  $file = $_GET['file'];
  
  try {
    echo $less->compileFile("less/" . $file . ".less");
  } catch (exception $e) {
    echo "fatal error: " . $e->getMessage();
  }

?>