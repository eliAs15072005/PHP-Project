<?php
session_start(); // Session starten, um Session-Daten (z.B. Benutzer-ID) zu nutzen
require_once __DIR__ . "/../db/database.php"; // Datenbankverbindung einbinden (Pfad ggf. anpassen)

// Prüfen, ob ein Benutzer eingeloggt ist (Session-Variable user_id existiert)
if (!isset($_SESSION['user_id'])) {
    // Falls nicht eingeloggt, Weiterleitung zur Login-Seite und Skript beenden
    header("Location: actions/login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Eingeloggte Benutzer-ID aus Session speichern

// Prüfen, ob der eingeloggte Benutzer Admin-Rechte besitzt
$stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$isAdmin = ($result && $result->fetch_assoc()['is_admin'] == 1); // true wenn is_admin = 1

// Falls kein Admin, Zugriff verweigern und Skript beenden
if (!$isAdmin) {
    echo "<h2>Zugriff verweigert</h2><p>Nur Administratoren dürfen diese Aktion durchführen.</p>";
    exit;
}

// Prüfen, ob eine Benutzer-ID zum Löschen per POST übergeben wurde
if (!isset($_POST['user_id'])) {
    echo "Keine Benutzer-ID angegeben."; // Fehlermeldung ausgeben, wenn keine ID vorhanden
    exit;
}

$delete_id = (int)$_POST['user_id']; // Benutzer-ID zum Löschen aus POST-Daten holen und in Integer umwandeln

// Optional: Verhindern, dass sich der Admin selbst löscht
if ($delete_id === $user_id) {
    echo "Sie können sich nicht selbst löschen."; // Warnung ausgeben und Skript beenden
    exit;
}

// SQL-Statement vorbereiten, um Benutzer mit der angegebenen ID zu löschen
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $delete_id);

// SQL ausführen und Erfolg prüfen
if ($stmt->execute()) {
    // Bei Erfolg: Weiterleitung zurück zum Admin-Panel (Pfad ggf. anpassen)
    header("Location: http://localhost/PHP-Project/PHPProject/admin_panel.php");
    exit;
} else {
    // Fehler beim Löschen melden
    echo "Fehler beim Löschen des Benutzers.";
}
