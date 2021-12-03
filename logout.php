<?php
setcookie("cookie_test", "cookie_value", time()+60);
if(!isset($_COOKIE["cookie_test"])){
  echo "Per il funzionamento del sito abilitare i cookie;";
  exit;
}

if (isset($_COOKIE[session_name()]))
{
   setcookie(session_name(), '', time()-42000, '/');
}
session_destroy();
header("Location: index.php");
?>
