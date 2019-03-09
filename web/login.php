<?php

$pwd = "1234";

if(isset($_GET['password'])) {

     if($_GET['password'] != $pwd) {
          echo "invalid password";
          exit;
     }

}

?>