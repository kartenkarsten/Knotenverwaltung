<?

require('include.php');

echo "<html><head><title>Freifunk Lueneburg</title></head><body>";
echo "<a href='meine_knoten.php'><img src='ff_lg_web.png'></a><hr><p>";



if(!empty($_POST) and !empty($_POST['get_key'])) {

  $bla = get_nodes_by_id($_POST['get_key']);
  keys_per_mail($bla);
  echo "Du solltest in wenigen Minuten eine Mail mit einer Liste Deiner Knoten bekommen. Dort kannst Du die VPN-Keys auslesen.";
} else {
  echo "Fehler. Bitte rufen Sie die Support Hotline an.<p>";
}
foot();

?>