<?php
setcookie("cookie_test", "cookie_value");
if(!isset($_COOKIE["cookie_test"])){
  echo "Per il funzionamento del sito abilitare i cookie;";
  exit;
}

session_start();
$t=time();
$diff=0;
$new=false;
if (isset($_SESSION['time'])){
    $t0=$_SESSION['time'];
    $diff=($t-$t0);  // inactivity period
}
if($diff > 120) {
  header("Location: logout.php");
}else{
  $_SESSION['time']=$t;
}

function sanitizeString($var, $c)
{
  $var = strip_tags($var);
  $var = htmlentities($var);
  $var = stripslashes($var);
  return mysqli_real_escape_string($c, $var);
}

function controlloCredenziali($username, $password){
  if (  ((preg_match('/^[a-z]+[A-Z|0-9]+/', $password)) || (preg_match('/^[A-Z|0-9]+[a-z]+/', $password)))
        && (preg_match('/^[A-z0-9\.\+_-]+@[A-z0-9\._-]+[.][A-z]{2,6}/', $username))  ) {
    $ritorno = '1';
  } else {
    $ritorno = '0';
  }
  return $ritorno;
}
if(isset( $_POST['user']) && isset( $_POST['password'])){
  $conn = mysqli_connect('localhost', 'root', '', 's256799');
  if(mysqli_connect_error())
    { echo "E"; }
  $userpulito= sanitizeString($_POST['user'], $conn);
  $password= $_POST['password'];
  if(!controlloCredenziali($userpulito, $password)){
    echo "P";
    mysqli_close($conn);
  }
  else{
    $sql="SELECT count(*) as cont FROM utenti WHERE user='$userpulito'";
    if(!$risposta=mysqli_query($conn, $sql)){
          echo "E";
    }else{
      $riga=mysqli_fetch_array($risposta);
      $risultato=$riga['cont'];
      if($risultato != 0){
        echo "U";
      }else{
        $hash=md5($password);
        $sql = "INSERT INTO utenti(user, password)
          VALUES('".$userpulito."','".$hash."')";
        if(!mysqli_query($conn, $sql)) {
            echo "E";
        }
        mysqli_close($conn);
        $_SESSION['user']=$userpulito;
        echo "S"; // "Utente registrato con successo";
      }
    }
  }
}else{
  if (isset($_SESSION['user'])) {
    header("Location: prenota.php");
  }else{
    echo "P";
  }
}
?>
