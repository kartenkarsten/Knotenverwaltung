#!/usr/bin/perl 

use DBI;
use Getopt::Long;
use Text::ASCIITable;



my $db    = "/home/www-data/fflg-knoten"; # sqlite Datenbank
my $peers = "/etc/fastd/fflg-mesh-vpn/peers/";

GetOptions ("db=s" => \$db, "peers=s" => \$peers, "show" => \$show, "delete=i" => \$delete, "help" =>\$help)
    or die("Error in command line arguments\n");

if ($help) {
    print "Usage: knoten.pl --help --db [sqlite db] --peers [fastd peers directory] --show (alle Knoten anzeigen) --delete [ID]\n";
    exit;
}

my $dsn = "DBI:SQLite:dbname=$db";
my $userid = "";
my $password = "";
my $dbh = DBI->connect($dsn, $userid, $password, { RaiseError => 1 })
                      or die $DBI::errstr;
#print "Opened database successfully\n";


sub peer_name {
    my $knoten = shift;
    $stmt = qq(SELECT * from knoten where knoten_id = $knoten;);

    my $sth = $dbh->prepare( $stmt );
    my $rv = $sth->execute() or die $DBI::errstr;
    if($rv < 0){
	print $DBI::errstr;
    }
    
    my $result = $sth->fetchall_arrayref;
    foreach my $row ( @$result ) {
	($knoten_id, $name, $firstname, $lastname, $email, $key, $location, $edit) =  @$row;
    }

    return $name;
}

sub knoten_anzeigen {
    my $knoten = shift;
    if ($knoten eq "") {
	$stmt = qq(SELECT * from knoten;);
    } elsif ($knoten ne "") {
	$stmt = qq(SELECT * from knoten where knoten_id = $knoten;);
    }

    my $sth = $dbh->prepare( $stmt );
    my $rv = $sth->execute() or die $DBI::errstr;
    if($rv < 0){
	print $DBI::errstr;
    }
    
    $t = Text::ASCIITable->new({ headingText => 'FF LG Knoten' });
    $t->setCols('ID','Name','Firstname', 'Lastname', 'email', 'key', 'location');
    
    $count = 0;
    my $result = $sth->fetchall_arrayref;
    foreach my $row ( @$result ) {
	my ($knoten_id, $name, $firstname, $lastname, $email, $key, $location, $edit) =  @$row;
	$t->addRow($knoten_id, $name, $firstname, $lastname, $email, $key, $location);
	$count++;
    }
    print $t;

    return $count;
}

sub knoten_loeschen  {
    my $knoten = shift;
    
    $peername = peer_name($knoten);
    
    print "Folgender Eintrag wird gelöscht:\n";
    $anzahl = knoten_anzeigen($knoten);
    if ($anzahl == 0) {
	print "Unbekannte ID\n";
	$dbh->disconnect();
	exit;
    }
    
    $unklar = 1;
    while ($unklar) {
	print "Soll der angezeigte Knoten aus der DB gelöscht werden? (j/n)";
	$value=<STDIN>;
	if ($value =~ /[JjNn]/  ) {

	    chomp($value);
	    if( $value =~ /[jJ]/) {
		$unklar = 0;

		###################################
		my $stmt = qq(DELETE from knoten WHERE knoten_id=$knoten LIMIT 1;);
		my $sth = $dbh->prepare( $stmt );
		my $rv = $sth->execute() or die $DBI::errstr;
		if($rv < 0){
		    print $DBI::errstr;
		}
		peer_loeschen($peername);
   		print "Der Knoten mit der ID $knoten wurde aus der Datenbank gelöscht.\n";
	    } else {
		print "ABBRUCH!\n";
		$unklar = 0;
	    }
	}
    }
}


sub peer_loeschen {
    my $name = shift;    
    $peerfile = $peers."/".$name;
    if (-e $peerfile) {
	unlink $peerfile or warn "Peerfile $peerfile konnte nicht gelöscht werden: $!";unlink($peerfile);
    } else {
	print "Peerfile $peerfile wurde nicht gefunden!!!!\n";
    }
}

if ($show) {
    knoten_anzeigen();
}

if ($delete != "") {
    knoten_loeschen($delete);
    print "";
}

$dbh->disconnect();
