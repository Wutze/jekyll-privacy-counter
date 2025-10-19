<?php
// logger.php


$db = new SQLite3(__DIR__ . '/logger/tracker.sqlite');
$db->busyTimeout(1000);
// Seitentitel aus Query übernehmen (von Jekyll übergeben)
$page = isset($_GET['page']) ? trim($_GET['page']) : 'unknown';

$page = mb_convert_encoding($page, 'UTF-8', 'UTF-8');
$page = mb_substr($page, 0, 150);

// Prepared Statement gegen SQL-Injection
$stmt = $db->prepare("INSERT INTO hits (page, count)
                      VALUES (:page, 1)
                      ON CONFLICT(page) DO UPDATE SET count = count + 1");
$stmt->bindValue(':page', $page, SQLITE3_TEXT);
$stmt->execute();


header('Content-Type: text/css');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
echo "/* ok */";