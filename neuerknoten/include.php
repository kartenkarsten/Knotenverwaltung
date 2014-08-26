<?

// Arnim Wiezer (arnim@posteo.de) 2014

// You man use this stuff for freifunk purpose and change it as you like
// NOTE: There are still some hardcoded freifunk-lueneburg things in the code, please adjust these to your needs!

$DB = "/home/www-data/fflg-knoten";
$DEL_URL = "https://freifunk-lueneburg.de/neuerknoten/del.php";
$EDIT_URL = "https://freifunk-lueneburg.de/neuerknoten/edit2.php";

function debug($in) {
	 echo "<pre>$in</pre>";
}

function debug_a($in) {
	 echo "<pre>";
	 print_r($in);
	 echo "</pre>";
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function errortext($in) {
  $out = "<font color='#FF0000'>$in</font>";
  return $out;
}


function fastd_key_exists($key, $name) {

  $result = 0;
  if (get_node_by_key($key) != "") 
    $result = 1;

  if (get_node_by_name($name) != "") 
     $result = 1;

  return $result;
}

function get_nodes_by_id($ids) {

  $dbfile = $GLOBALS['DB'];
  $dir = 'sqlite:/'.$dbfile;
  $tmp = "";

  try {
    $dbh  = new PDO($dir) or die("cannot open the database");

    if (is_array($ids)) {
        $string = join(" OR knoten_id = ", $ids); 
    } else {
        $string = $ids;
    }
    $query = "SELECT * FROM knoten WHERE knoten_id = ".$string;

    $result = $dbh->query($query, 2); 

    if (empty($result)) {
      print "Leider wurden keien Knoten unter den IDs gefunden.";
      die();
    }

    $tmp = [];
    foreach ($result as $row) {
      $id = $row['knoten_id'];
      $tmp[$id]['name'] = $row['name'];
      $tmp[$id]['firstname'] = $row['firstname'];
      $tmp[$id]['lastname'] = $row['lastname'];
      $tmp[$id]['email'] = $row['email'];
      $tmp[$id]['key'] = $row['key'];
      $tmp[$id]['location'] = $row['location'];
    }

    $db = NULL;
  } catch  (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
  }
  return $tmp;
}




function get_node_by_key($key) {

  $dbfile = $GLOBALS['DB'];
  $dir = 'sqlite:/'.$dbfile;
  $tmp = "";
  

  try {
    $dbh  = new PDO($dir) or die("cannot open the database");
    $query = "SELECT * FROM knoten WHERE key = '$key'";
    $result = $dbh->query($query); 

    if (empty($result)) {
      print "Schlüssel leider nicht gefunden.";
      die();
    }
    foreach ($result as $row) {
      $tmp['knoten_id'] = $row['knoten_id'];
      $tmp['name'] = $row['name'];
      $tmp['firstname'] = $row['firstname'];
      $tmp['lastname'] = $row['lastname'];
      $tmp['email'] = $row['email'];
      $tmp['key'] = $row['key'];
      $tmp['location'] = $row['location'];
      $tmp['delhash'] = $row['delhash'];
    }

    $db = NULL;
  } catch  (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
  }
  return $tmp;
}

function get_node_by_name($name) {

  $dbfile = $GLOBALS['DB'];
  $dir = 'sqlite:/'.$dbfile;
  $tmp = "";

  try {
    $dbh  = new PDO($dir) or die("cannot open the database");
    $query = "SELECT * FROM knoten WHERE name = '$name'";
    $result = $dbh->query($query); 

    foreach ($result as $row) {
      $tmp['knoten_id'] = $row['knoten_id'];
      $tmp['name'] = $row['name'];
      $tmp['firstname'] = $row['firstname'];
      $tmp['lastname'] = $row['lastname'];
      $tmp['email'] = $row['email'];
      $tmp['key'] = $row['key'];
      $tmp['location'] = $row['location'];
      $tmp['delhash'] = $row['delhash'];
    }

    $db = NULL;
  } catch  (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    exit;
  }

  return $tmp;
}



function write_key($data) {
    if (fastd_key_exists($data['key'], $data['name']) == 0) {
      try {
	$dbfile = $GLOBALS['DB'];
	$dir = 'sqlite:/'.$dbfile;
	$dbh  = new PDO($dir) or die("cannot open the database");
	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$dbh->beginTransaction();
	$dbh->exec("insert into knoten (name, firstname, lastname, email, key, location, edit) values ('$data[name]', '$data[firstname]', '$data[lastname]', '$data[mail]', '$data[key]', '$data[location]', 1)");
	$dbh->commit();
	$dbh = NULL;
      } catch (Exception $e) {
	$dbh->rollBack();
	echo "Failed: " . $e->getMessage();
	return 0;
      }
    } else {
      return 1;
    }
    return 0;
}


function update_key($data) {
	 $ok = 0;
	 $tmp = get_node_by_name($data['name']);

	 if (empty($tmp['name']) and $tmp['knoten_id'] == $data['knoten_id'])
	    $ok = 1;
         elseif (empty($tmp['name']))
	    $ok = 1;
	    elseif ($tmp['knoten_id'] == $data['knoten_id'])
	    $ok = 1;
	 else 
	    $ok = 0;

	 if ($ok == 1) {
	   try {
	       $dbfile = $GLOBALS['DB'];
	       $dir = 'sqlite:/'.$dbfile;
	       $dbh  = new PDO($dir) or die("cannot open the database");
    
	       $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
               $dbh->beginTransaction();
               $query = "UPDATE knoten SET name='$data[name]', firstname='$data[firstname]', lastname='$data[lastname]', email='$data[email]', location='$data[location]', edit=1 WHERE knoten_id=$data[knoten_id]";
               $dbh->exec($query);
               $dbh->commit();
               $dbh = NULL;
               return 1;
               } 
          catch (Exception $e) {
               $dbh->rollBack();
               echo "Failed: " . $e->getMessage();
               return 0;
	     }
	 } else {
	     echo "Fehler! Der Name oder der Key existieren schon!<p>";
	     return 0;
	 }
}


function get_all_nodes() {
  $dbfile = $GLOBALS['DB'];
  $dir = 'sqlite:/'.$dbfile;

  try {
    $dbh  = new PDO($dir) or die("cannot open the database");
    $query = "SELECT * FROM knoten";
    $result = $dbh->query($query); 

    $alle_knoten = [];
    
    foreach ($result as $row) {
      $alle_knoten[$row['knoten_id']]['name'] = $row['name'];
      $alle_knoten[$row['knoten_id']]['firstname'] = $row['firstname'];
      $alle_knoten[$row['knoten_id']]['lastname'] = $row['lastname'];
      $alle_knoten[$row['knoten_id']]['email'] = $row['email'];
      $alle_knoten[$row['knoten_id']]['location'] = $row['location'];
      $alle_knoten[$row['knoten_id']]['key'] = $row['key'];
    }
    $dbh = NULL;
  } catch  (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
  }
  
  return $alle_knoten;
}

function show_nodes_by_mail($mail) {
 
  $mail = test_input($mail);
  $dbfile = $GLOBALS['DB'];
  $dir = 'sqlite:/'.$dbfile;

  $row = [];

  try {
    $dbh  = new PDO($dir) or die("cannot open the database");
    $query = "SELECT * FROM knoten WHERE email='$mail'";
    $result = $dbh->query($query); 

    echo "<form action='get_key.php' method='post'>\n";
    echo "<table border=1><tr><th>Name</th><th>e-mail</th><th>VPN Key anfordern</th></tr>\n";

    foreach ($result as $row) {
    	    echo "<tr><td>".$row['name']."</td><td>".$row['email']."</td><td align=center> <input type='checkbox' name='get_key[]' value=".$row['knoten_id']."></td></tr>\n";
    }

    echo "</table><p>";
    echo "<input type='submit' name='submit' value='OK'>\n";	
    echo "</form>";

    $dbh = NULL;
  } catch  (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
  }
}



function insert_hash($knoten_id, $hash) {
  try {
    $dbfile = $GLOBALS['DB'];
    $dir = 'sqlite:/'.$dbfile;
    $dbh  = new PDO($dir) or die("cannot open the database");
    
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $dbh->beginTransaction();
    $query = "UPDATE knoten SET delhash='$hash' WHERE knoten_id=$knoten_id";
                       
    $dbh->exec($query);
    $dbh->commit();
    $dbh = NULL;
  } catch (Exception $e) {
    $dbh->rollBack();
    echo "<pre>Failed: " . $e->getMessage()."</pre>";
    return 0;
  }
}

function keys_per_mail($tmp) {
    $edit_url = $GLOBALS['EDIT_URL'];
    $del_url  = $GLOBALS['DEL_URL'];
    $txt = "";
    $i = count($tmp);
    $txt = "Auf Dich sind $i Knoten registriert:<br>\n--------------------------------------<br>\n";
    foreach ($tmp as $row) {

    	    $txt .= "Knoten: ".$row['name']."<br>\n";
	    $txt .= "Vorname: ".$row['firstname']."<br>\n";
	    $txt .= "Nachname: ".$row['lastname']."<br>\n";
	    $txt .= "email: ".$row['email']."<br>\n";
	    $txt .= "VPN key (Router Schlüssel): ".$row['key']."<br>\n";
	    $txt .= "Standort: ".$row['location']."<br>\n";
	    $txt .= "---------------------------------------------<br>\n";
    }

    $txt .= "Du kannst mit den VPN keys unter <a href='$edit_url'>diesem Link</a> editieren oder unter <a href='$del_url'>diesem Link</a> löschen.<br>\nViele Grüße, Dein Freifunk-Lüneburg Team.";
  
  $to         = $row['email']; // Send email to our user
  $name       = $row['firstname']." ".$row['lastname'];

  $subject = 'Dein Knoten';
  $message = '
Hallo '.$name.'!<p>'.$txt;


  $header  = 'MIME-Version: 1.0' . "\r\n"; 
  $header .= 'Content-type: text/html; charset=utf-8'. "\r\n";
  $header .= 'From: Freifunk Lueneburg <noreply@freifunk-lueneburg.de>'. "\r\n";
  $header .= 'Reply-to: noreply@freifunk-lueneburg.de' ."\r\n";
  $header .= 'X-Mailer: PHP '. phpversion(). "\r\n";

  mail($to, $subject, $message, $header); // Send our email

}


function verify_mail($tmp) {
  $hash       = md5( rand(0,1000) );
  $to         = $tmp['email']; // Send email to our user
  $name       = $tmp['firstname']." ".$tmp['lastname'];
  $knotenname = $tmp['name'];

  $subject = 'Bitte bestätigen';
  $link    = 'http://freifunk-lueneburg.de/neuerknoten/verify.php?email='.$to.'&hash='.$hash.'&node='.$knotenname;
  $message = '
Hallo '.$name.'!

Du bist in der Freifunk-Lüneburg Datenbank als VerwalterIn des Knotens >'.$knotenname.'< vermerkt.<br>

Auf unserer Webseite wurde die Löschung Des Knotens >'.$knotenname.'< beantragt.<br>
Falls Du das warst, bestätige die Löschung bitte, indem Du auf den unten stehenden Link klickst. Danke.<br>

<a href="'.$link.'">Hier zum Bestätigen klicken</a><br>

Falls Du das nicht warst, wende Dich bitte an info@freifunk-lueneburg.de<br>

';


  $header  = 'MIME-Version: 1.0' . "\r\n"; 
  $header .= 'Content-type: text/html; charset=utf-8'. "\r\n";
  $header .= 'From: Freifunk Lueneburg <noreply@freifunk-lueneburg.de>'. "\r\n";
  $header .= 'Reply-to: noreply@freifunk-lueneburg.de' ."\r\n";
  $header .= 'X-Mailer: PHP '. phpversion(). "\r\n";

  mail($to, $subject, $message, $header); // Send our email
  insert_hash($tmp['knoten_id'], $hash); // hash in DB hinterlegen, so dass das verify script diesen auslesen und vergleichen kann.
}

function del_node($knoten_id) {

  $dbfile = $GLOBALS['DB'];
  $dir = 'sqlite:/'.$dbfile;

  try {
    $dbh  = new PDO($dir) or die("cannot open the database");
    $query = "DELETE FROM knoten WHERE knoten_id = $knoten_id";

    $dbh->beginTransaction();
    $dbh->exec($query);
    $dbh->commit();
    $dbh = NULL;
    return 1;
  } catch  (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
    return 0;
  }

}

function foot() {

  echo "<hr>";
  echo "<table><tr><td><small><a href='http://freifunk-lueneburg.de/neuerknoten/'>Neuer Knoten</a></small></td><td>|</td><td><small><a href='http://freifunk-lueneburg.de/neuerknoten/edit2.php'>Knoten bearbeiten</a></small></td><td>|</td><td><small><a href='http://freifunk-lueneburg.de/neuerknoten/del.php'>Knoten löschen</a></small></td><td>|</td><td><small><a href='http://freifunk-lueneburg.de/neuerknoten/meine_knoten.php'>Meine Knoten anzeigen</a></small></td></tr></table>";
  
  echo "<small>Bei problemen mit dem Formular bitte eine mail an info(at)freifunk-lueneburg(Punkt)de</small><P>";
  echo "</body></html>";

}
?>