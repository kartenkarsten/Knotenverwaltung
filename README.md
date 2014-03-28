Knotenverwaltung der Lüneburger FreifunkerInnen

Die folgenden files/Verzeichnisse müssen auf dem Gateway liegen.

- update_peers.pl liegt in /usr/local/bin und wird alle 10 Minuten von Cron ausgeführt
- neuerknoten/ liegt in /var/www/freifunk-lueneburg.de/ und enthält die Webseiten und PHP Scripte, zum einragen und aktualisieren von Knoten
- knoten.pl liegt in /usr/local/bin und dient zum Verwalten der Knoten (auch löschen) in der Shell auf dem Gateway
- fflg-knoten ist eine SQLite Datenbank die in unserem Fall unter /home/www-data/ liegt. Dort hat der Webserver Schreibrechte.
- Eine leere Datenbank kann mit dem SQLite Script fflg-knoten.sql erstellt
  werden: sqlite3 neuerdatenbank < fflg-knoten.sql

Arnim Wiezer (arnim@posteo.de), März 2014
