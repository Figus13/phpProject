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
function sanitizeString2($var)
{
  $var = strip_tags($var);
  $var = htmlentities($var);
  return stripslashes($var);
}

if(isset($_SESSION['user'])){
  if(isset($_POST['posti']) && isset($_POST['elementi'])){
    $el[]=$_POST['elementi'];
    $e=explode(',', $el[0]);
    $conn = mysqli_connect('localhost', 'root', '', 's256799');
      if(mysqli_connect_error())
      { echo "Errore di collegamento al DB"; }
    $cont=0;
    $n=sanitizeString2($_POST['posti']);
    $user=sanitizeString($_SESSION['user'], $conn);
    for($i=0;$i<$n;$i++){
      $elem_split[$i]=explode('_', $e[$i]);;
      $elem_split[$i][0]=sanitizeString($elem_split[$i][0], $conn);
      $elem_split[$i][1]=sanitizeString($elem_split[$i][1], $conn);
    }

    $conn = mysqli_connect('localhost', 'root', '', 's256799');
      if(mysqli_connect_error())
      { echo "Errore di collegamento al DB"; }
    mysqli_autocommit($conn,false);
    $sql="SELECT * FROM prenotazioni FOR UPDATE";
    if(!$risposta=mysqli_query($conn, $sql)){
          echo "errore nella query1";
    }else{
      while($record=mysqli_fetch_array($risposta)){
        if(isset($record['stato'])){
          $db_stati[$record['riga']][$record['colonna']]=$record['stato'];
          $db_utenti[$record['riga']][$record['colonna']]=$record['utente'];
        }
      }
      $flag=0;
      for($i=0;$i<$n && $flag==0; $i++){
        if(isset($db_stati[$elem_split[$i][0]][$elem_split[$i][1]])){
          if(!($db_stati[$elem_split[$i][0]][$elem_split[$i][1]]=="P" && $db_utenti[$elem_split[$i][0]][$elem_split[$i][1]]==$user)){
            $flag=1;
          }
        }
      }
    }
    if($flag==0){//i posti erano tutti o liberi o occupati dall user
      //occupo i prenotati:
      $sql="UPDATE prenotazioni SET stato= 'O' WHERE utente='$user' AND stato='P'";
      if(!$risposta=mysqli_query($conn, $sql)){
            echo "errore nella query2";
      }
      //occupo quelli che risultavano liberi nel db:
      for($i=0;$i<$n;$i++){
        if(!isset($db_stati[$elem_split[$i][0]][$elem_split[$i][1]])){
          $sql="INSERT INTO prenotazioni(riga, colonna, stato, utente)
              VALUES('".$elem_split[$i][0]."','".$elem_split[$i][1]."','O','".$user."')";
          if(!$risposta=mysqli_query($conn, $sql)){
                echo "errore nella query2";
          }
        }
      }
      mysqli_commit($conn);
      mysqli_autocommit($conn,true);
      echo "S";
    }else{ //non tutti i posti erano disponibili.
      $sql="DELETE FROM prenotazioni WHERE utente='$user' AND stato='P'";
      if(!$risposta=mysqli_query($conn, $sql)){
            echo "errore nella query3";
      }
      mysqli_commit($conn);
      mysqli_autocommit($conn,true);
      echo "D";
    }
  }else{header("Location: index.php");}

}else{header("Location: index.php");}
