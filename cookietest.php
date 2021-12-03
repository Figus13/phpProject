<?php
setcookie("cookie_test", "cookie_value");
if(!isset($_COOKIE["cookie_test"])){
  echo "Per il funzionamento del sito abilitare i cookie. Abilitare i cookie e aggiornare la pagina. In caso il problema persista provare ad aggiornare la pagina una seconda volta";
  exit;
}else{
  header("Location: index.php");
}

 ?>
