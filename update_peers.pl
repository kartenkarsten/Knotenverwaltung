#!/usr/bin/perl

# Script holt sich die Knotendatenbank die via webinterface aktualisiert wurde (machen die Knotenbetreiber)
# Neue Knoten werden in die fastd config eingetragen und alte gelöscht
# arnim wiezer (arnim@posteo.de) 03/2014


# ggf. anpassen
$db        = "/home/www-data/fflg-knoten"; # sqlite Datenbank
$peers     = "/etc/fastd/fflg-mesh-vpn/peers/";

# ab hier bitte nicht mehr ändern
$knoten = {};
$fastd  = {};
$peers_neu = {};
$update = 0;

use DBI;
#use strict;

my $dsn = "DBI:SQLite:dbname=$db";
my $userid = "";
my $password = "";
my $dbh = DBI->connect($dsn, $userid, $password, { RaiseError => 1 })
                      or die $DBI::errstr;
print "Opened database successfully\n";



sub trim($) {
    my $string = shift;
    $string =~ s/^\s+//;
    $string =~ s/\s+$//;
    return $string;
}

# DB einlesen
my $stmt = qq(SELECT * from knoten;);
my $sth = $dbh->prepare( $stmt );
my $rv = $sth->execute() or die $DBI::errstr;
if($rv < 0){
   print $DBI::errstr;
}

$knoten = {};


my $result = $sth->fetchall_arrayref;
foreach my $row ( @$result ) {
#  print "@$row\n";
  ($knoten_id, $name, $firstname, $lastname, $email, $key, $location, $edit) =  @$row;

  $knoten->{$knoten_id}->{name}      = $name;
  $knoten->{$knoten_id}->{firstname} = $firstname;
  $knoten->{$knoten_id}->{lastname}  = $lastname;
  $knoten->{$knoten_id}->{email}     = $email;
  $knoten->{$knoten_id}->{key}       = $key;
  $knoten->{$knoten_id}->{location}  = $location;
  $knoten->{$knoten_id}->{edit}      = $edit;
  $knoten->{$knoten_id}->{desc}      = $name.";".$firstname.";".$lastname.";".$email.";".$location;

  my $stmt = qq(UPDATE knoten SET edit=0 WHERE knoten_id=$knoten_id;);
  my $sth = $dbh->prepare( $stmt );
  my $rv = $sth->execute() or die $DBI::errstr;
  if($rv < 0){
      print $DBI::errstr;
  }
}
$dbh->disconnect();


foreach $peer (keys %$knoten) {
    $nodefile = $peers."/".$knoten->{$peer}->{name};
    if (-e $nodefile and $knoten->{$peer}->{edit} == 0) {
	print "$nodefile wird ignoriert - schon vorhanden.\n";
    } else {
	print "Neue peer Datei oder update: $nodefile\n";
        $update = 1;
	open (OUT, "+>$nodefile") || die "Konnte output Datei >$nodefile< nicht schreiben.\n";
	print OUT "# ".$knoten->{$peer}->{desc}."\n";
	print OUT "key \"".$knoten->{$peer}->{key}."\";\n";
	close(OUT);
    }
}

if ($update == 1) {
  system("kill -1 `cat /var/run/fastd.fflg-mesh-vpn.pid`");
}
