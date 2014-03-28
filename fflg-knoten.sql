PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE "knoten" (
    "knoten_id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    "name" TEXT,
    "firstname" TEXT,
    "lastname" TEXT,
    "email" TEXT,
    "key" TEXT,
    "location" TEXT,
    "edit" INTEGER
);
COMMIT;
