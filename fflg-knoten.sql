PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE knoten (knoten_id INTEGER PRIMARY KEY, name TEXT, firstname TEXT, lastname TEXT, email TEXT, key TEXT, location TEXT, edit INTEGER, new_date TEXT, last_seen TEXT, delhash TEXT);
COMMIT;
