<?php
/**
 * Spezialfall - Scriptfehler!
 * Quellübergreifende (Cross-Origin) Anfrage blockiert: Die Gleiche-Quelle-Regel verbietet das Lesen der externen Ressource 
 * auf https://tools.home/logger/api.php?page=Konservativ%20-%20Mindesthaltbarkeit%20-%20bis%20gestern. 
 * (Grund: CORS-Kopfzeile 'Access-Control-Allow-Origin' stimmt nicht mit 'https://tools.home' überein)
 * 
 * Nachfolgend der Aufruf im Webbrowser der Jekyll-Testumgebung
 * Die IP wird bei jedem anders aussehen
 * http://172.16.16.147:4000/
 * 
 * Der Scriptfehler, wie oben beschrieben, passiert wenn normales HTTP und HTTPS gemischt werden
 * Da meine gesamte Entwicklungsumgebung mit HTTPS läuft, alle JavaScript- und CSS-Bibilotheken zentral
 * verwaltet werden, sind die ersten 3 Headerangaben im PHP Script notwendig.
 * 
 * 
 */


//header("Access-Control-Allow-Origin: *"); // allow all Domains
header("Access-Control-Allow-Origin: https://tools.home");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');


$dbFile = __DIR__ . '/tracker.sqlite';

if (!file_exists($dbFile)) {
    echo json_encode(['error' => 'database not found']);
    exit;
}

try {
    $db = new SQLite3($dbFile);


    // Parameter page (optional) – Einzelwert abrufen
    if (isset($page)) {
        $page = trim($_GET['page']);
        $stmt = $db->prepare("SELECT page, count FROM hits WHERE page = :page");
        $stmt->bindValue(':page', $page, SQLITE3_TEXT);
        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        echo json_encode($result ?: ['page' => $page, 'count' => 0]);
        exit;
    }

    // Standard: komplette Liste ausgeben (sortiert nach Hits)
    $query = $db->query("SELECT page, count FROM hits ORDER BY count DESC");
    $data = [];
    while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
        $data[] = $row;
    }

    echo json_encode([
        'status' => 'ok',
        'entries' => count($data),
        'data' => $data
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
