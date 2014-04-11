<?php
/*
  Script, um neue Knoten auf dem Lüneburger Freifunk Gateway einzutragen.
  arnim wiezer (arnim@posteo.de) 03/2014
*/

require('include.php');
$firnameErr = $lastnameErr = $mailErr = $keyErr = $locationErr = $nameErr = "";
$firname = $lastname = $mail = $key = $location = $name = "";
$checkOK = true;

echo "<html><head><title>Freifunk Lueneburg</title></head><body>";
echo "<img src='ff_lg_web.png'><hr><p>";

if(empty($_POST)) {
  echo "<h2>Willkommen beim Freifunk Lüneburg!</h2>";
  echo "Schön, daß Du mitmachen möchtest. Bitte trag Deine Informationen hier ein damit wir Deinen Freifunk-Knoten aktivieren können.";
  echo "Fülle bitte alle Felder aus. Die Informationen bleiben beim Freifunk Lüneburg und werden an niemanden weitergegeben.";
  $button = "OK";

} else {  
  $button = "Absenden!";

  // Inhalte überprüfen
  if (empty($_POST["name"])) {
    $nameErr = errortext("* Der Name des Knoten wird benötigt.");
    $checkOK = false;
  } else {$name = test_input($_POST["name"]);}

  if (preg_match("/[\s\#\$\%\&]/", $_POST["name"])) {
    $nameErr = errortext("* Der Name darf keine Whitespace-Zeichen (wie z.B. Leerzeichen) oder # enthalten.");
    $checkOK = false;
  }

  if (empty($_POST["firstname"])) {
    $firstnameErr = errortext("* Vorname wird benötigt.");
    $checkOK = false;
  } else {$firstname = test_input($_POST["firstname"]);}

  if (empty($_POST["lastname"])) {
    $lastnameErr = errortext("* Nachname wird benötigt.");
    $checkOK = false;
  } else {$lastname = test_input($_POST["lastname"]);}
  
  if (empty($_POST["mail"])) {
    $mailErr = errortext("* Email wird benötigt.");
    $checkOK = false;
  } else {$mail = test_input($_POST["mail"]);}

  if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $mail)) {
    $mailErr = errortext("* Ungültiges E-Mail Format.");
    $checkOK = false;
  }

  if (empty($_POST["key"])) {
    $keyErr = errortext("* Der öffentliche Schlüssel des Knoten wird benötigt.");
    $checkOK = false;
  } else {$key = test_input($_POST["key"]);}
  
  if (empty($_POST["location"])) {
    $locationErr = errortext("* Der Standort wird benötigt.");
    $checkOK = false;
  } else {$location = test_input($_POST["location"]);}

}

if ($checkOK == false or empty($_POST['submit']) or $_POST['submit'] == "OK" ) {
  if ($checkOK == false or $_POST['submit'] == "OK") {
    echo "Du hast folgende Informationen eingetragen. Bitte prüfe noch mal alles auf Richtigkeit und korrigiere bei Bedarf falls nötig!";
  }
  echo "<table>";
  echo "<form action='index.php' method='post'>
    <tr><td>Name des Knotens:</td><td><input type='text' size='35' name='name' value='", $name, "' /> (selbst gewählt oder die Vorgabe der Konfigurationsseite. Aber bitte ohne Leerzeichen und #)</td><td>", $nameErr ,"</td></tr>
    <tr><td>Vorname:</td><td><input type='text' size='20' name='firstname' value='", $firstname, "' /></td><td>", $firstnameErr, "</td></tr>
    <tr><td>Nachname:</td><td><input type='text' size='20' name='lastname' value='", $lastname, "' /></td><td>", $lastnameErr, "</td></tr>
    <tr><td>E-Mail:</td><td><input type='text' size='40' name='mail' value='", $mail, "' /></td><td>", $mailErr,"</td></tr>
    <tr><td>Router Schlüssel:</td><td><input type='text' size='65' name='key' value='", $key, "' /></td><td>", $keyErr, "</td></tr>
    <tr><td>Standort des Knotens:</td><td><input type='text' size='65' name='location' value='", $location, "' /> (z.B. Straße und Ort. Die GPS-Daten werden nur auf dem Knoten selber eingetragen.)</td><td>", $locationErr ,"</td></tr>
    <tr><td><input type='submit' name='submit' value='", $button, "' /></td></tr>
    </form></table>";
  
} else {

  echo "<h2>Fertig!</h2>";
  echo "Dein neuer Freifunk Knoten wurde mit folgen Informationen registriert und sollte in wenigen Minuten funktionieren.";
  echo "<table>";
  echo "<form action='index.php' method='post'>
    <tr><td>Name des Knotens (wie auf der Konfigurationsseite angegeben!):</td><td>", htmlspecialchars($_POST['name']), "</td></tr>
    <tr><td>Vorname:</td><td>", htmlspecialchars($_POST['firstname']), "</td></tr>
    <tr><td>Nachname:</td><td>", htmlspecialchars($_POST['lastname']), "</td></tr>
    <tr><td>E-Mail:</td><td>", htmlspecialchars($_POST['mail']), "</td></tr>
    <tr><td>Router Schlüssel:</td><td>", htmlspecialchars($_POST['key']), "</td></tr>
    <tr><td>Standort des Knotens:</td><td>", htmlspecialchars($_POST['location']), "</td></tr>
  </form></table>";
  if (write_key($_POST) == 0) {
    echo "<p>Knoten wurde erfolgreich eingetragen!<p>";
  } else {
    echo "<p>Leider ist ein Fehler aufgetreten: Es existiert bereits ein andere Knoten mit demselben Schlüssel oder Namen.<p>";
  }
} 


echo "<hr><small>Bei problemen mit dem Formular bitte eine mail an arnim(at)posteo(Punkt)de</small><P>";
echo "</body></html>";


?>
