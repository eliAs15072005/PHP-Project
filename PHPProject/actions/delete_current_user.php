<?php
session_start(); // Session starten, um auf Session-Daten zugreifen zu können
require_once __DIR__ . "/../db/database.php"; // Datenbankverbindung einbinden

// Prüfen, ob ein Benutzer eingeloggt ist, indem die user_id in der Session geprüft wird
if (!isset($_SESSION['user_id'])) {
    die("Kein Benutzer angemeldet."); // Skript beenden, falls kein Benutzer angemeldet ist
}

$userId = $_SESSION['user_id']; // Benutzer-ID aus der Session auslesen

// SQL-Statement vorbereiten, um den Benutzer mit der entsprechenden ID aus der Tabelle 'users' zu löschen
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $userId); // Benutzer-ID als Integer binden

// SQL-Statement ausführen und prüfen, ob das Löschen erfolgreich war
if ($stmt->execute()) {
    // Wenn erfolgreich, Session-Daten leeren und Session zerstören (Benutzer ausloggen)
    session_unset();
    session_destroy();

    // Benutzer zur Startseite oder Login-Seite weiterleiten
    header("Location: ../home.php");
    exit; // Weiteren Skriptcode verhindern
} else {
    // Fehlerfall: Meldung ausgeben, falls Löschen fehlschlägt
    echo "Fehler beim Löschen des Benutzers.";
}

// Ressourcen freigeben
$stmt->close();
$conn->close();
