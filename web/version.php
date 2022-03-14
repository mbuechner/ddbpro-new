<?php
  header("Content-Type:text/plain");
  ob_start();
  echo getenv('VERSION'); 
  ob_end_flush();
?>
