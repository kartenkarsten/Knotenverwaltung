<?php

// fd62e945a0fb80eabded9e1a4be1a8ae0e6a21fde4c80257fe029246514af0c1

include("include.php");
$keyErr = "";

echo "<html><head><title>Freifunk Lueneburg</title></head><body>";
echo "<a href='del.php'><img src='ff_lg_web.png'></a><hr><p>";

if(empty($_POST)) {
  $button = "LÖSCHEN";
  echo "<h2>Willkommen beim Freifunk Lüneburg!</h2>";
  echo "Mit Hilfe dieser Seite kann ein bereits registrierter Freifunk Knoten aus der Datenbank gelöscht werden. Danach wird der VPN-Key auf dem Gateway gelöscht.<p>\n";
  echo "Wenn Du den VPN-Schlüssel Deines Knotens nicht kennst, kannst Du ihn im Config-Mode auslesen. Mehr dazu findest Du <a href='http://wiki.freifunk.net/Freifunk_Hamburg/Firmware#Weg_1:_Config_Mode'>hier</a>. Wenn Du Dich auf Deinem Knoten per SSH einloggen kannst, liest Du den Key per >/etc/init.d/fastd show_key mesh_vpn< aus";

  echo "<form action='del.php' method='post'>\n";
  echo "<table>\n";
  echo "<tr><td>Router Schlüssel:</td><td><input type='text' size='65' name='key' value=''/></td></tr>\n";
  echo "<tr><td><input type='submit' name='submit' value='", $button, "'/></td><td>", $keyErr, "</td></tr>\n";
  echo "</table></form>\n";

} else {  

  if (empty($_POST['key'])) {
    $keyErr = errortext("* Der öffentliche Schlüssel des Knoten wird benötigt.");
    $checkOK = false;
  } else {
    $key = test_input($_POST['key']);

    if (!  empty($key)) {
       	   verify_mail(get_node_by_key($key));
           echo "Es wurde eine Verifikationsmail verschickt. Klick bitte auf den Bestätigungslink in der Mail. Falls Du keinen Zugang mehr zu der E-Mail Adresse hast, die bei der Registrierung hinterlegt wurde, wende Dich bitte an info@freifunk-lueneburg.de";
   } else {
    echo "Leider wurde kein Knoten mit dem eingegebenen Key gefunden. Du musst einen gültigen key und nicht den Namen des Knoten eingeben.\n";

   }  
  }
}


foot();


?>
