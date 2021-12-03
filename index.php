<?php
setcookie("cookie_test", "cookie_value");
if(!isset($_COOKIE["cookie_test"])){
  header("Location: cookietest.php");
  echo "Per il funzionamento del sito abilitare i cookie;";
  exit;
}


include './myfunctions.php';
//ini_set("session.gc_maxlifetime","120");
//ini_set("session.cookie_lifetime","120");
session_start();

$er="";
if(isset($_SESSION['user']))
  header("Location: prenota.php");
if(!(isset($_SESSION['user']))){
  if (isset($_POST['auser']) && isset($_POST['apassword'])){

    $hash = md5($_POST['apassword']);
    $conn = dbConnect();
    $user = sanitizeString($_POST['auser'], $conn);
    $sql = "SELECT count(*) as cont FROM utenti WHERE user='$user' AND password='$hash'";
    if(!$risposta=mysqli_query($conn, $sql)){
        echo "errore query";
        exit;
    }else{
      $riga=mysqli_fetch_array($risposta);
      $risultato = $riga['cont'];
      if($risultato == 1) {
        //l'utente non è registrato
        $_SESSION['user'] = $user;
        header("Location: prenota.php");
        exit;
      }else{
        //provato un accesso ma con credenziali sbagliate
        $er="Password o username errati.";
      }
    }
  }
}
 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="keywords" content="homepage"/>
    <link rel="stylesheet" href="./stile.css" type="text/css"/>
		<title>
			Benvenuti in VOLI
		</title>
    <?php
    usehttps();
    $lettera= array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    ?>
    <noscript><h1> ATTENZIONE: javascript disabilitato sul browser, per il corretto funzionamento del sito è necessario riattivarlo </h1></noscript>
    <script type="text/javascript" src="./jquery.js"></script>
    <script type="text/javascript">
      function l(x){
        let stringa="";
        var err= "<?php echo $er ?>";
        if(x==1){//accedi
          stringa+="<form method=\"post\" action=\"./index.php\" name=\"Form_accesso\"><div id=\"risposta_acc\">"+err+"</div><input class=\"in\" type=\"email\" name=\"auser\" onfocus=\"this.value=\'\'\" value=\"Username\"><br><input class=\"in\" type=\"password\"  name=\"apassword\" onfocus=\"this.value=\'\'\" value=\"Password\"><br><input class=\"in\" type=\"submit\" value=\"Accedi\"></form>";
          document.getElementById('login').style.backgroundColor= "hsl(0, 0%, 90%)";
        }else{//registrati
          stringa+="<div id=\"risposta_reg\"></div><input class=\"in\" type=\"email\" id=\"ruser\" name=\"ruser\" onfocus=\"this.value=\'\'\" value=\"Username\"><br><input class=\"in\" id=\"rpassword\" name=\"rpassword\" type=\"password\" onfocus=\"this.value=\'\'\" value=\"Password\"><br><input class=\"in\" type=\"button\" value=\"Registrati\" onclick=\"registra()\">"+
          "<p id='desc_reg'> Inserire una e-mail valida e una una password che deve contenere almeno un carattere alfabetico minuscolo, ed almeno un altro carattere che sia alfabetico maiuscolo oppure un carattere numerico";
          document.getElementById('login').style.backgroundColor= "hsl(0, 0%, 70%)";
        }
        document.getElementById('contenutologin').innerHTML = stringa;
      }

      $("document").ready(function () {
      let larg = document.getElementById('aereo').clientWidth;
      let alt=larg*2/3;
      let stringa = "<img id=\"testa\" src=\"./testa.png\" width=\""+larg+"px\" height=\""+alt+"px\" align=\"middle\">";
      document.getElementById('testa').innerHTML = stringa;
      l(1);
    });
    </script>
    <title></title>
  </head>
  <body id="bhome">
    <div id="posti">
      <h1 id="benvenuto"> Benvenuto </h1>
      <h2 id="descrizione"> Qui sotto puoi trovare la mappa dei posti per il volo Roma - Parigi </h2>

      <?php
        $conn= mysqli_connect('localhost', 'root', '', 's256799');
        if(mysqli_connect_errno()){
          echo "Connessione fallita: ".mysqli_connect_error();
          exit();
        }
        $sql="SELECT riga, colonna, stato FROM prenotazioni";
        $risposta=mysqli_query($conn, $sql);
        while($riga=mysqli_fetch_array($risposta)){
          $stato[$riga['riga']][$riga['colonna']]=$riga['stato'];
        }
        //-----
        mysqli_close($conn);
        $o=0;
        $l=0;
        $p=0;
        //LE RIGHE E LE COLONNE SONO DICHIARATE IN myfunctions
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
        for($i=1; $i<=$righe ;$i++){
          echo "<tr>";
          for($j=0; $j<$colonne; $j++){
            if($j==$disparita)
              echo "<th class=\"idposti\">".$i."</th>";
            if(!isset($stato[$i][$lettera[$j]]) || $stato[$i][$lettera[$j]]=="L"){
                $l++;
                echo "<td class=\"libero\"></td>";
            }else{
              if($stato[$i][$lettera[$j]]=="O"){
                $o++;
                echo "<td class=\"occupato\"></td>";
              }
              if ($stato[$i][$lettera[$j]]=="P"){
                $p++;
                echo "<td class=\"prenotato\"></td>";
              }
            }
          }
          if($colonne==1)
            echo "<th class=\"idposti\">".$i."</th>"; // far uscire le righe nel caso di una sola colonna
          echo "</tr>";
        }
        $totaleposti=$l+$p+$o;
        echo "</table>";
        echo "<table><tr><td class=\"libero\"></td><td> Liberi:".$l."</td>".
            "<td class=\"prenotato\"></td><td> Prenotati:".$p."</td>".
            "<td class=\"occupato\"></td><td> Occupati:".$o."</td>".
            "<td></td><td> Posti totali:".$totaleposti."</td></tr></table>";
       ?>

     </div>
       <table id="login">
         <tr><th id="accesso"><input type="button" class="bottone" id="baccedi" onclick="l(1)" value="Accedi"></th>
            <th id="registrati"><input type="button" class="bottone" id="bregistrati" onclick="l(2)" value="Registrati"></th></tr>
          <tr><td id="contenutologin" colspan="2"></td></tr>
       </table>
  </body>
</html>
