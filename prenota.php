<!DOCTYPE html>
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


include './myfunctions.php';
usehttps();
if(!(isset($_SESSION['user']))){
  // password o user non settati
  session_destroy();
  header("Location: index.php");
  exit;
}

include './funzioniPrenotazione.php';
  $lettera= array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
 ?>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="keywords" content="pagina prenotazioni"/>
    <link rel="stylesheet" href="./stile2.css" type="text/css"/>
    <link rel="stylesheet" href="./stile.css" type="text/css"/>
    <title>Prenota il tuo posto</title>
    <script type="text/javascript" src="./jquery.js"></script>
    <script type="text/javascript">
    var elem=[];
    var prenotazioni_mie=0;
    $("document").ready(function () {
      $attivo=1; // quando scade il timeout va a zero e non sarà possibile fare azioni
      $timeout = setTimeout(tout, 120000);
      let larg = document.getElementById('aereo').clientWidth;
      let alt=larg*2/3;
      let stringa = "<img id=\"testa\" src=\"./testa.png\" width=\""+larg+"px\" height=\""+alt+"px\" align=\"middle\">";
      document.getElementById('testa').innerHTML = stringa;
    });
    </script>
  </head>
  <body id="bprenota">
    <div id="zona_bottoni">
     <form action="logout.php"> <input id="logout" type="submit" value="Logout"></form>
     <input type="button" value="Aggiorna" id="aggiorna" onclick="aggiorna_pag()">
     <input type="button" value="Acquista" id="acquista" onclick="acquista()">
   </div>
    <div id="facciata_posti">
      <h1> Benvenuto </h1>
      <h2> Questa è la pagina per la prenotazione dei suoi posti sul volo </h2>
      <table id="legenda">
        <tr><td class="libero"></td><td class="desc_posto">  Libero</td></tr>
        <tr><td class="occupato"></td><td class="desc_posto">  Occupato</td></tr>
        <tr><td class="prenotato"></td><td class="desc_posto">  Prenotato da un altro utente</td></tr>
        <tr><td class="prenotato_da_me"></td><td class="desc_posto">  Prenotato da te</td></tr>
      </table>
      <div id="aggiornamenti"></div>
      <?php

      $conn= mysqli_connect('localhost', 'root', '', 's256799');
      if(mysqli_connect_errno()){
        echo "Connessione fallita: ".mysqli_connect_error();
        exit();
      }
      $n_pren_mie=0;
      $sql="SELECT riga, colonna, stato, utente FROM prenotazioni";
      $risposta=mysqli_query($conn, $sql);
      while($riga=mysqli_fetch_array($risposta)){
        $stato[$riga['riga']][$riga['colonna']]=$riga['stato'];
        $utenti[$riga['riga']][$riga['colonna']]=$riga['utente'];
      }
      //-----
      mysqli_close($conn);

      $righe=10;
      $colonne=6;
      $disparita=$colonne%2;
      if($disparita==0){
        $disparita=$colonne/2;
      }else {
        $disparita=($colonne+1)/2;
      }
      echo "<div id='testa'></div>";
      echo "<table id=\"aereo\">";

      for($i=0; $i<$colonne ;$i++){
        if($i==$disparita)
          echo "<th class=\"idposti\"> </th>";

        echo "<th class=\"idposti\">".$lettera[$i]."</th>";
      }
      $user=sanitizeString2($_SESSION['user']);
      for($i=1; $i<=$righe ;$i++){
        echo "<tr>";
        for($j=0; $j<$colonne; $j++){
          if($j==$disparita)
            echo "<th class=\"idposti\">".$i."</th>";
          if(!isset($stato[$i][$lettera[$j]]) || $stato[$i][$lettera[$j]]=="L"){
              echo "<td  id='".$i.$lettera[$j]."' class=\"libero\"><input type='button' class='bprenotazione' onclick='processo_prenotazione(".$i.", \"".$lettera[$j]."\", \"".$user."\")'></td>";
          }else{
            if($stato[$i][$lettera[$j]]=="O"){
              echo "<td  id='".$i.$lettera[$j]."' class=\"occupato\"></td>";
            }
            if ($stato[$i][$lettera[$j]]=="P"){
              if($utenti[$i][$lettera[$j]]!=$user){
                echo "<td  id='".$i.$lettera[$j]."' class=\"prenotato\"><input type='button' class='bprenotazione' onclick='processo_prenotazione(".$i.", \"".$lettera[$j]."\", \"".$user."\")'></td>";
              }else{
                $pren_mie[$n_pren_mie]=$i."_".$lettera[$j]; ?>
                <script>   elem[elem.length]= "<?php echo $pren_mie[$n_pren_mie]; ?>";  prenotazioni_mie++; </script>
                <?php $n_pren_mie++;
                echo "<td id='".$i.$lettera[$j]."' class=\"prenotato_da_me\"><input type='button' class='bprenotazione' onclick='processo_prenotazione(".$i.", \"".$lettera[$j]."\", \"".$user."\")'></td>";
              }
            }
          }
        }
        if($colonne==1)
            echo "<th class=\"idposti\">".$i."</th>"; // far uscire le righe nel caso di una sola colonna
        echo "</tr>";
      }
      echo "</table>";
     ?>
    </div>
    <p>
  </body>
</html>
