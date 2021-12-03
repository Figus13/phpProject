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
  return mysqli_real_escape_string($c ,$var);
}

if(isset($_SESSION['user']) && isset($_POST['user'])){
  if ($_SESSION['user']== $_POST['user']) {
    if(isset( $_POST['riga']) && isset( $_POST['colonna']) && isset( $_POST['classe'])) {
      $conn = mysqli_connect('localhost', 'root', '', 's256799');
      if(mysqli_connect_error())
      { echo "Errore di collegamento al DB"; }
      $user =sanitizeString($_POST['user'], $conn);
      $classe=sanitizeString($_POST['classe'], $conn);
      $riga= sanitizeString($_POST['riga'], $conn);
      $colonna= sanitizeString($_POST['colonna'], $conn);

      $sql="SELECT count(*) as cont FROM prenotazioni WHERE riga='$riga' AND colonna='$colonna'";
      if(!$risposta=mysqli_query($conn, $sql)){
            echo "E";
      }else{
        $record=mysqli_fetch_array($risposta);
        $risultato=$record['cont'];
        if($risultato == 0){
         //posto libero
          if($classe=="prenotato_da_me"){//qualcuno aveva rubato la mia prenotazione per poi rilasciarla, la rilascio anche io
            $sql="DELETE FROM prenotazioni WHERE riga='$riga' AND colonna='$colonna'";
            if(!mysqli_query($conn, $sql)) {
                echo "Errore query";
            }
            mysqli_close($conn);
            echo "M";
          }else{//sia nel caso che fosse libero anche per me sia prenotato da un altro lo posso prenotare.
            $stato="P";
            $sql = "INSERT INTO prenotazioni(riga, colonna, stato, utente)
                VALUES('".$riga."','".$colonna."','".$stato."','".$user."')";
            if(!mysqli_query($conn, $sql)) {
                  echo "Errore query";
            }
            echo "L";
          }
        }else{
          //posto presente nel db. controllo se occupato o prenotato
          if($risultato==1){
            $sql="SELECT riga, colonna, stato, utente FROM prenotazioni WHERE riga='$riga' AND colonna='$colonna'";
            if(!$risposta=mysqli_query($conn, $sql)){
              echo "E";
            }else{
              $record=mysqli_fetch_array($risposta);
              $s=$record['stato'];
              if($s=="O"){//è già occupato.
                echo "O";
              }
              if($s=="P"){//controllo se la prenotazione è mia
                $u=$record['utente'];
                if($u==$user){ //prenotato da me. togliere prenotazione
                  $sql="DELETE FROM prenotazioni WHERE riga='$riga' AND colonna='$colonna'";
                  if(!mysqli_query($conn, $sql)) {
                      echo "Errore query";
                  }
                  mysqli_close($conn);
                  echo "M";
                }else{
                  //prenotato da qualcun altro. Se lo vedo verde o arancione diventa giallo, se lo vedo giallo  lo devo trasformare in arancione
                  //perchè vuol dire che sto cercando di liberarlo anche se non è mia la prenotazione.
                  if($classe=="prenotato_da_me"){
                    mysqli_close($conn);
                    echo "A";
                  }else{
                    $sql="UPDATE prenotazioni SET utente= '$user' WHERE riga='$riga' AND colonna='$colonna'";
                    if(!mysqli_query($conn, $sql)) {
                      echo "Errore query";
                    }
                    mysqli_close($conn);
                    echo "P";
                  }
                }
              }
            }
          }else{
            echo "E"; // trovate più di una prenotazione:errore
          }
        }
      }
    }else{echo "E";}
  }else{header("Location: index.php");}
}else{header("Location: index.php");}
?>
