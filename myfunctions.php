<?php
$righe=10;
$colonne=6;

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


function myDestroySession() {
    $_SESSION=array();
    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time()-3600*24,
      $params["path"],$params["domain"],
      $params["secure"], $params["httponly"]);
    }
    session_destroy(); // destroy session
}

function dbConnect() {
  $conn = mysqli_connect('localhost', 'root', '', 's256799');
  if(mysqli_connect_error())
    { myRedirect("Errore di collegamento al DB"); }
    return $conn;
}

function usehttps(){
  if ( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
      // La richiesta e' stata fatta su HTTPS
    } else {
      // Redirect su HTTPS
      // eventuale distruzione sessione e cookie relativo
      $redirect = 'https://' . $_SERVER['HTTP_HOST'] .
      $_SERVER['REQUEST_URI'];
      header('HTTP/1.1 301 Moved Permanently');
      header('Location: ' . $redirect);
      exit();
    }
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
?>
<script type="text/javascript" src="./jquery.js"></script>
<script type="text/javascript">
function tout() {
  $attivo=0;
  /*if (isset($_COOKIE[session_name()]))
  {
     setcookie(session_name(), '', time()-42000, '/');
  }
  session_destroy();**/
}
function ajaxRequest() {
    try { // Non IE Browser?
        var $request = new XMLHttpRequest()
    } catch(e1){ // No
        try { // IE 6+?
            $request = new ActiveXObject("Msxml2.XMLHTTP")
        } catch(e2){ // No
          try { // IE 5?
              $request = new ActiveXObject("Microsoft.XMLHTTP")
          } catch(e3){ // No AJAX Support
          $request = false
          }
        }
      }
      return $request
}
function registra(){
    $user=document.getElementById('ruser').value;
    $pass=document.getElementById('rpassword').value;
    let contrpass1 = new RegExp('^[a-z]+[A-Z|0-9]+');
    let contrpass2 = new RegExp('^[A-Z|0-9]+[a-z]+');
    let contruser = new RegExp('^[A-z0-9\.\+_-]+@[A-z0-9\._-]+[.][A-z]{2,6}');

    if (!((contrpass1.test($pass) || contrpass2.test($pass)) && contruser.test($user))){
      document.getElementById('risposta_reg').innerHTML= "Password o username non valide";
      return;
    }

    $request = new ajaxRequest();
    $request.open('POST', 'procRegistrazione.php', true);
    $request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    $request.send('user='+$user+'&password='+$pass);
    $request.onreadystatechange = function()
      {
        if( (this.readyState == 4) && (this.status == 200) && (this.responseText != null) ) {
          $rit= this.responseText;
          if($rit == "S"){
            location.replace("prenota.php");
          }
          if($rit == "P"){
            document.getElementById('risposta_reg').innerHTML="La password o l'user non rispettano i requisiti";
          }
          if($rit == "U"){
            document.getElementById('risposta_reg').innerHTML="Utente già presente";
          }
          if($rit == "E"){
            document.getElementById('risposta_reg').innerHTML="Errore, riprovare";
          }

        }
    }
}
function acquista(){
  if($attivo==0){
    aggiorna_pag();
  }else{
  clearTimeout($timeout);
  $timeout = setTimeout(tout, 120000);
  if(prenotazioni_mie==0){
      alert("Bisogna selezionare dei posti prima di procedere con l'acquisto");
      return;
  }
  $request = new ajaxRequest();
  $request.open('POST', 'procAcquisto.php', true);
  $request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  $request.send('posti='+ prenotazioni_mie+'&elementi='+elem);
  $request.onreadystatechange = function()
    {
      if( (this.readyState == 4) && (this.status == 200) && (this.responseText != null) ) {
        $rit= this.responseText;
        console.log($rit);
        if($rit == "S"){
          alert("I posti scelti sono stati acquistati");
          aggiorna_pag();
        }
        if($rit == "D"){
          alert("Non tutti i posti scelti erano disponibili, il suo acquisto non è andato a buon fine. Riprovare.");
          aggiorna_pag();
        }
      }
    }
  }
}
function aggiorna_pag(){
  if($attivo==0){
    location.replace("logout.php");
  }else{
  clearTimeout($timeout);
  window.location.reload();
  }
}
</script>
