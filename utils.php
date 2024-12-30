<?php 

// functions
function redirect($page) {
    header("Location: {$page}");
    exit();
  }

?>