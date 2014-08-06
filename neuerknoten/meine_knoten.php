<?

require('include.php');

echo "<html><head><title>Freifunk Lueneburg</title></head><body>";
echo "<a href='meine_knoten.php'><img src='ff_lg_web.png'></a><hr><p>";


if(empty($_POST['submit']) or empty($_POST['mail'])) {
  $button = "OK";
  echo "<h2>Willkommen beim Freifunk Lüneburg!</h2>";
  echo "Mit Hilfe dieser Seite kann man alle Knoten anzeigen lassen, die auf eine e-mail Adresse registriert wurden.<p>";

  echo "<form action='meine_knoten.php' method='post'>\n";
  echo "<table>\n";
  echo "<tr><td>E-Mail Adresse:</td><td><input type='text' size='65' name='mail' value=''/></td></tr>\n";
  echo "<tr><td><input type='submit' name='submit' value='", $button, "'/></td><td>", $keyErr, "</td></tr>\n";
  echo "</table></form>\n";
} else {
    show_nodes_by_mail($_POST['mail']);

}

foot();



?>