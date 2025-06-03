<?php
session_start(); // Session starten, um Benutzer-Session-Daten zu nutzen (z.B. user_id)

require_once __DIR__ . "/../db/database.php"; // Datenbankverbindung einbinden (Pfad ggf. anpassen)

// Prüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    // Wenn nicht eingeloggt, Weiterleitung zur Login-Seite
    header("Location: /PHP-Project/PHPProject/actions/login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // User-ID aus Session auslesen

// Benutzername aus der DB holen anhand user_id, für Anzeige und Dateinamen
$username = "Unbekannt";
$stmtUser = $conn->prepare("SELECT user_name FROM users WHERE id = ?");
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
if ($resultUser && $rowUser = $resultUser->fetch_assoc()) {
    // Nutzername HTML-sicher ausgeben (XSS-Schutz)
    $username = htmlspecialchars($rowUser['user_name']);
}

// --- Verarbeitung wenn "Alle Daten löschen" Button gedrückt wurde ---
if (isset($_POST["delete"])) {
    // Löscht alle Temperatureinträge des aktuellen Nutzers
    $stmt1 = $conn->prepare("DELETE FROM temperature WHERE user_id = ?");
    $stmt1->bind_param("i", $user_id);
    $ok1 = $stmt1->execute();

    // Löscht alle importierten Dateien (Hashes) des aktuellen Nutzers
    $stmt2 = $conn->prepare("DELETE FROM imported_files WHERE user_id = ?");
    $stmt2->bind_param("i", $user_id);
    $ok2 = $stmt2->execute();

    if ($ok1 && $ok2) {
        // Erfolgsmeldung in Session speichern
        $_SESSION['message'] = "<p class='success'>Alle Daten wurden erfolgreich gelöscht.</p>";
    } else {
        // Fehler ausgeben, z.B. wegen DB-Problemen
        $_SESSION['message'] = "<p class='error'>Fehler beim Löschen: " . $conn->error . "</p>";
    }

    // Nach Löschen neu laden, um Status anzuzeigen
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// --- Verarbeitung des CSV-Imports wenn Formular abgesendet wurde ---
if (isset($_POST["submit"])) {
    // Prüfen, ob Datei erfolgreich hochgeladen wurde
    if ($_FILES["csv_file"]["error"] === 0) {
        $filePath = $_FILES["csv_file"]["tmp_name"]; // Temporärer Pfad zur Datei
        $fileHash = md5_file($filePath); // MD5-Hash der Datei zur Duplikaterkennung

        // Prüfen, ob Datei mit gleichem Hash schon vom Nutzer importiert wurde
        $check = $conn->prepare("SELECT id FROM imported_files WHERE file_hash = ? AND user_id = ?");
        $check->bind_param("si", $fileHash, $user_id);
        $check->execute();
        $checkResult = $check->get_result();

        if ($checkResult && $checkResult->num_rows > 0) {
            // Wenn ja, Fehlermeldung ausgeben und Seite neu laden
            $_SESSION['message'] = "<p class='error'>Diese Datei wurde bereits importiert.</p>";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        // CSV Datei öffnen und erste Zeile (Header) überspringen
        $csvFile = fopen($filePath, "r");
        fgetcsv($csvFile);

        $importiert = 0; // Zähler für erfolgreich importierte Zeilen
        $fehler = 0;     // Zähler für fehlerhafte Zeilen
        $csvData = [];   // Array für späteren Zugriff auf importierte Daten

        // Zeilenweise CSV auslesen
        while (($row = fgetcsv($csvFile, 1000, ",")) !== FALSE) {
            if (count($row) < 4) {
                // Wenn weniger als 4 Spalten, Zeile überspringen und Fehler zählen
                $fehler++;
                continue;
            }

            // Datum aus CSV im Format d.m.Y in Y-m-d konvertieren
            $dateInput = trim($row[0]);
            $dateObj = DateTime::createFromFormat('d.m.Y', $dateInput);
            $date = $dateObj ? $dateObj->format('Y-m-d') : null;

            // Zeit aus CSV umwandeln (z.B. "14Uhr" zu "14:00:00")
            $timeRaw = trim($row[1]);
            $hour = str_replace("Uhr", "", $timeRaw);
            $hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
            $time = "$hour:00:00";

            // Temperatur und Luftfeuchtigkeit als float umwandeln
            $temperature = floatval(str_replace(",", ".", $row[2]));
            $humidity = floatval(str_replace(["%", ","], ["", "."], $row[3]));

            if ($date && $time) {
                // Insert in DB vorbereiten und ausführen
                $sql = "INSERT INTO temperature (date, time, temperature, humidity, user_id) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssdi", $date, $time, $temperature, $humidity, $user_id);

                if ($stmt->execute()) {
                    $importiert++;
                } else {
                    $fehler++;
                }

                // Für Anzeige in Tabelle zwischenspeichern
                $csvData[] = [
                    'date' => $date,
                    'time' => $time,
                    'temperature' => $temperature,
                    'humidity' => $humidity
                ];
            } else {
                $fehler++;
            }
        }

        fclose($csvFile); // Datei schließen

        // Datei-Hash in Tabelle imported_files speichern, damit Doppelimporte verhindert werden können
        $insertHash = $conn->prepare("INSERT INTO imported_files (file_hash, user_id) VALUES (?, ?)");
        $insertHash->bind_param("si", $fileHash, $user_id);
        $insertHash->execute();

        // Erfolgs- und Fehlerzahlen als Session-Nachricht speichern
        $_SESSION['message'] = "<p class='success'><strong>Import abgeschlossen.</strong></p>
                                <p>Importierte Zeilen: $importiert</p>
                                <p>Fehlerhafte Zeilen: $fehler</p>";

        $_SESSION['csvData'] = $csvData; // importierte Daten für weitere Verwendung speichern

    } else {
        // Fehler beim Datei-Upload
        $_SESSION['message'] = "<p class='error'>Fehler beim Hochladen der Datei.</p>";
    }

    // Seite neu laden um Statusmeldung anzuzeigen
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>CSV importieren</title>
    <link rel="stylesheet" href="../style.css">    
</head>
<body>

<!-- Anzeige des aktuell angemeldeten Benutzers rechts oben -->
<div style="position: fixed; top: 10px; right: 10px; font-weight: bold;">
    Angemeldet als: <?= $username ?>
</div>

<h2>CSV-Datei hochladen</h2>

<div class="button-group">
    <!-- Formular für Datei-Upload und Datenlöschung -->
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="csv_file" accept=".csv" required>
        <input type="submit" name="submit" value="Importieren">
        <input type="submit" name="delete" value="Alle Daten löschen" onclick="return confirm('Wirklich alle Daten löschen?');">
    </form>

    <!-- Export Button -->
    <form action="export_csv.php" method="get">
        <input type="submit" value="Daten als CSV exportieren">
    </form>

    <!-- Link zurück zur Startseite -->
    <form action="http://localhost/PHP-Project/PHPProject/home.php" method="get">
        <input type="submit" value="Zurück zur Startseite">
    </form>
</div>

<?php
// Erfolg-/Fehlermeldungen aus Session ausgeben und dann löschen
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}

// Alle Temperaturdaten des aktuellen Benutzers aus DB holen und anzeigen
$stmt = $conn->prepare("SELECT date, time, temperature, humidity FROM temperature WHERE user_id = ? ORDER BY date ASC, time ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    echo "<h3>Importierte CSV-Daten:</h3>";
    echo "<table>";
    echo "<tr><th>Datum</th><th>Uhrzeit</th><th>Temperatur (°C)</th><th>Luftfeuchtigkeit (%)</th></tr>";

    // Tabellendaten sicher ausgeben (HTML-Sonderzeichen escapen)
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['time']) . "</td>";
        echo "<td>" . htmlspecialchars($row['temperature']) . "</td>";
        echo "<td>" . htmlspecialchars($row['humidity']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>Keine Daten vorhanden.</p>";
}
?>

</body>
</html>
