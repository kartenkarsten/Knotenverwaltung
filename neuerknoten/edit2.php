<?php
/*
  Script, um Knoten des Lüneburger Freifunk Gateways zu editieren.
  arnim wiezer (arnim@posteo.de) 03/2014
*/

require('include.php');

echo "<html><head><title>Freifunk Lueneburg</title></head><body>";
echo "<img src='ff_lg_web.png'><hr><p>";


if (empty($_POST) or empty($_POST['key'])) {
  $_POST['key'] = "";
  echo "<table>";
  echo "<form action='edit2.php' method='post'>
    <tr><td>Router Schlüssel:</td><td><input type='text' size='65' name='key' value='", $_POST['key'], "' /></td></tr>
    <tr><td><input type='submit' name='submit' value='OK' /></td></tr>
    </form></table>";
} elseif ($_POST['submit'] == "OK" and !empty($_POST['key'])) {
  
  $data = get_node_by_key(htmlspecialchars($_POST['key']));

  echo "<table>";
  echo "<form action='edit2.php' method='post'>
  <tr><td>Name des Knotens:</td><td><input type='text' size='35' name='name' value='", $data['name'], "'/></td></tr>
  <tr><td>Vorname:</td><td><input type='text' size='20' name='firstname' value='", $data['firstname'], "' /></td><td></tr>
  <tr><td>Nachname:</td><td><input type='text' size='20' name='lastname' value='", $data['lastname'], "' /></td><td></tr>
  <tr><td>E-Mail:</td><td><input type='text' size='40' name='email' value='", $data['email'], "' /></td><td></tr>
  <tr><td>Router Schlüssel:</td><td>", $data['key'], "</td></tr>
  <tr><td>Standort des Knotens:</td><td><input type='text' size='65' name='location' value='", $data['location'], "' /></td></tr>
  <tr><td><input type='submit' name='submit' value='Änderungen speichern' /></td><td>", errortext("Achtung: Es gibt keine weiter Nachfrage. Die Daten werden so geschrieben."), "</td></tr>
  <input type='hidden' name='key' value='", $data['key'], "'>
  <input type='hidden' name='knoten_id' value='", $data['knoten_id'], "'>
  </form></table>";
} elseif ($_POST['submit'] == "Änderungen speichern") {

  if (update_key($_POST)) {
    echo "Die Daten wurden erfolgreich geändert.";
  } else {
    echo "Es ist ein Fehler aufgetreten. Mehr Details stehen leider nicht zur Verfügung.";
  }
}

foot();
?>