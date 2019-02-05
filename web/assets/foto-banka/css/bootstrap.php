<?php
  header("Content-type: text/css; charset: UTF-8");
  header("Cache-control: must-revalidate");
  header(sprintf("Expires: %s GMT", gmdate("D, d M Y H:i:s", time() + 10800)));
  
  if (file_exists('bootstrap.css'))
  {
      $css = file_get_contents('bootstrap.css'); echo $css;
  }
  else
  {
    // require "../../lib/lessphp/lessc.inc.php";
    require "../symfony/lib/lessphp/Less.php";

    header("Content-Type: text/css");
  
    // $less = new lessc();
    // $less->setFormatter("compressed");
  
    try {
      //$css = $less->compileFile("bootstrap/less/bootstrap.less");
      $options = array( 'compress' => true );
      $parser = new Less_Parser($options);
      $parser->parseFile( 'bootstrap/less/bootstrap.less', 'http://example.com/mysite/' );
      // $css = $parser->getCss(); 
      $css = $parser->getCss();
      file_put_contents('bootstrap.css', $css);
      chmod('bootstrap.css', 0777);
      echo $css;
    } catch (exception $e) {
      echo "fatal error: " . $e->getMessage();
    }
  }
?>
