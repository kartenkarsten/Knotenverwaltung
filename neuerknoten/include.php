<?

$DB = "/home/www-data/fflg-knoten";

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
    if (empty($result)) {
      print "Name leider nicht gefunden.";
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
    }

    $db = NULL;
  } catch  (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
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

	 if (!empty($tmp) and $tmp['knoten_id'] == $data['knoten_id'])
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

?>