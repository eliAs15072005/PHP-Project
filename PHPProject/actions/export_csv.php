<?php
session_start(); // Session starten, um Zugriff auf eingeloggte Benutzer-ID zu erhalten
require_once __DIR__ . "/../db/database.php"; // Datenbankverbindung einbinden (Pfad ggf. anpassen)

// Prüfen, ob ein Benutzer eingeloggt ist (Session enthält user_id)
if (!isset($_SESSION['user_id'])) {
    // Falls nicht eingeloggt, Weiterleitung zur Login-Seite und Skript beenden
    header("Location: /PHP-Project/PHPProject/actions/login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // User-ID aus Session speichern

// Benutzername aus Datenbank holen, um ihn im Dateinamen zu verwenden
$stmtUser = $conn->prepare("SELECT user_name FROM users WHERE id = ?");
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();

if ($resultUser && $rowUser = $resultUser->fetch_assoc()) {
    $username = $rowUser['user_name']; // User-Name aus DB übernehmen
} else {
    $username = "user_" . $user_id; // Fallback-Name falls DB-Abfrage fehlschlägt
}

// Prüfen, ob der Benutzer überhaupt Daten in der Tabelle "temperature" hat
$stmtCheck = $conn->prepare("SELECT COUNT(*) as count FROM temperature WHERE user_id = ?");
$stmtCheck->bind_param("i", $user_id);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();
$rowCheck = $resultCheck->fetch_assoc();

if ($rowCheck['count'] == 0) {
    // Falls keine Daten vorhanden, HTTP-Status 400 zurückgeben und Skript beenden
    http_response_code(400);
    die("Fehler: Keine Daten zum Exportieren vorhanden.");
}

// HTTP Header setzen, damit der Browser eine CSV-Datei herunterlädt
header('Content-Type: text/csv; charset=utf-8');

// Dateiname mit Username und Suffix "_Messdaten.csv"
$filename = $username . "_Messdaten.csv";
header('Content-Disposition: attachment; filename=' . $filename);

// Öffnen eines PHP-Ausgabe-Streams zum direkten Schreiben der CSV-Daten in den HTTP-Response
$output = fopen('php://output', 'w');

// CSV-Headerzeile mit Spaltennamen schreiben
fputcsv($output, ['Datum', 'Uhrzeit', 'Temperatur (°C)', 'Luftfeuchtigkeit (%)']);

// Daten aus der Tabelle temperature des aktuellen Benutzers abfragen, sortiert nach Datum und Uhrzeit absteigend
$stmt = $conn->prepare("SELECT date, time, temperature, humidity FROM temperature WHERE user_id = ? ORDER BY date DESC, time DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Jede Zeile der Abfrage als CSV-Zeile ausgeben
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [$row['date'], $row['time'], $row['temperature'], $row['humidity']]);
}

// Stream schließen (optional, da Script hier endet)
fclose($output);

exit; // Script beenden, damit keine weitere Ausgabe erfolgt
