<?php
/*
provide function to check login and access
*/
session_start();

function f_shouldDie($requiredAccess="") {
  $shouldDie = false;
  if ($_SESSION["user"] == "") {
    $shouldDie = true;
  }else{
    if ($_SESSION["access"] != NULL) {
      $access = $_SESSION["access"];
      if (!(strstr($access,$requiredAccess))) {
        $shouldDie = true;
      }
    }else{
      $shouldDie = true;
    }
  }
  return $shouldDie;
}
?>