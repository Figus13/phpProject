<script type="text/javascript" src="./jquery.js"></script>
<script type="text/javascript">
function processo_prenotazione($r, $c, $u){
      if($attivo==0){
        aggiorna_pag();
      }else{
        clearTimeout($timeout);
        $timeout = setTimeout(tout, 120000);
        $classe = document.getElementById($r+$c).className;
        manda_richiesta_prenotazione($r, $c, $u, $classe);
      }
}
function manda_richiesta_prenotazione($r, $c, $u, $classe){
    if($attivo==0){
      aggiorna_pag();
    }else{
    $request = new ajaxRequest();
    $request.open('POST', 'procPrenotazione.php', true);
    $request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    $request.send('riga='+$r+'&colonna='+$c+'&user='+ $u+'&classe='+ $classe);
    $request.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200 && this.responseText != null) {
          $rit= this.responseText;
          if($rit == "L"){
            elem[prenotazioni_mie]=$r+"_"+$c;
            prenotazioni_mie++;

            document.getElementById("aggiornamenti").innerHTML="Il posto è stato prenotato"
            document.getElementById($r+$c).className="prenotato_da_me";
          }

          if($rit == "P"){
            elem[prenotazioni_mie]=$r+"_"+$c;
            prenotazioni_mie++;

            document.getElementById("aggiornamenti").innerHTML="Il posto era prenotato da un altro utente, ma ora è tuo.";
            document.getElementById($r+$c).className="prenotato_da_me";
          }

          if($rit == "M"){
            let flag=0;
            for(var i=0; i<prenotazioni_mie && flag==0; i++){
              if(elem[i]==$r+"_"+$c){
                elem[i]=elem[prenotazioni_mie-1];
                elem.pop;
                flag=1
              }
            }
            prenotazioni_mie--;

            document.getElementById("aggiornamenti").innerHTML="La sua prenotazione è stata eliminata";
            document.getElementById($r+$c).className="libero";
          }
          if($rit == "O"){
            document.getElementById("aggiornamenti").innerHTML="Il posto è già stato comprato da qualcuno";
            document.getElementById($r+$c).className="occupato";
          }
          if($rit == "A"){
            let flag=0;
            for(var i=0; i<prenotazioni_mie && flag==0; i++){
              if(elem[i]==$r+"_"+$c){
                elem[i]=elem[prenotazioni_mie-1];
                elem.pop;
                flag=1
              }
            }
            prenotazioni_mie--;

            document.getElementById("aggiornamenti").innerHTML="Il posto che hai provato a liberare non era più prenotato da te.";
            document.getElementById($r+$c).className="prenotato";
          }

          if($rit == "E"){
            document.getElementById("aggiornamenti").innerHTML="ERRORE";
          }
        }
    }
  }
}


</script>
