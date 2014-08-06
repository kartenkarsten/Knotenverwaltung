<?

require('include.php');

if (empty($_GET['email']) or empty($_GET['hash']) or empty($_GET['node'])) {
  echo "No input no cry.";
  exit;
}

echo "<html><head><title>Freifunk Lueneburg</title></head><body>";
echo "<img src='ff_lg_web.png'><hr><p>";


$tmp = get_node_by_name($_GET['node']);

// Hash mit DB vergleichen
if ($tmp['email'] == $_GET['email'] and $tmp['delhash'] == $_GET['hash'] and $tmp['name'] == $_GET['node']) {
  if (del_node($tmp['knoten_id']) == 1) {
    echo "Danke. Der Knoten wurde gelöscht und wird in den nächsten 10 Minuten automatisch aus dem Netz entfernt.";
  } else {
    echo "Leider ist ein Fehler aufgetreten. Der Knoten wurde nicht gelöscht. Wende Dich bitte an info@freifunk-lueneburg.de";
  }
} else {
  echo "Tut mir leid.";
  echo "</body></html>";
  exit;
}

echo "</body></html>";

?>