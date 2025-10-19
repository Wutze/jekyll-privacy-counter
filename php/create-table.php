<?php


$dbFile = __DIR__ . '/logger/tracker.sqlite';
$db = new SQLite3($dbFile);
$db->busyTimeout(1000);

// Tabelle anlegen, falls nicht vorhanden
$db->exec("CREATE TABLE IF NOT EXISTS hits (
  page TEXT PRIMARY KEY,
  count INTEGER DEFAULT 0
)");