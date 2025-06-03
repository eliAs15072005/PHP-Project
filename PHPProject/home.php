<?php
session_start(); // Session starten, um angemeldeten Benutzer zu erkennen
require_once __DIR__ . "/db/database.php"; // DB-Verbindung einbinden

// Aktuell angemeldeter Benutzer aus der Session lesen
$currentUserId = $_SESSION['user_id'] ?? null;

// Benutzername aus DB abrufen (für Anzeige oben rechts)
$username = "Unbekannt"; // Fallback-Name, falls Benutzer nicht gefunden wird
if ($currentUserId) {
    $stmtUser = $conn->prepare("SELECT user_name FROM users WHERE id = ?");
    $stmtUser->bind_param("i", $currentUserId);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();
    if ($resultUser && $rowUser = $resultUser->fetch_assoc()) {
        $username = htmlspecialchars($rowUser['user_name']);
    }
}

// Alle Benutzer für eventuelle weitere Verwendungen abrufen
$users = [];
$result = $conn->query("SELECT id, user_name FROM users ORDER BY user_name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Messdaten einlesen</title>
    <meta charset="UTF-8">
</head>
<body class="home">

<!-- Anzeige des angemeldeten Benutzers oben rechts -->
<div style="position: fixed; top: 10px; right: 10px; font-weight: bold;">
    Angemeldet als: <?= $username ?>
</div>

<h1>Messdaten einlesen</h1>

<div>
    <?php
    // Prüfen, ob aktueller Benutzer Admin ist
    $isAdmin = false;
    $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->bind_param("i", $currentUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        $isAdmin = $row['is_admin'] == 1;
    }
    ?>

    <!-- Nur für Admins sichtbar: Admin-Bereich -->
    <?php if ($isAdmin): ?>
        <form action="admin_panel.php" method="get" style="display:inline-block; margin-right:10px;">
            <input type="submit" value="Admin-Bereich">
        </form>
    <?php endif; ?>

    <!-- Benutzerkonto wechseln oder neues erstellen -->
    <form action="http://localhost/PHP-Project/PHPProject/actions/registration.php" method="get" style="display:inline-block; margin-right:10px;">
        <input type="submit" value="Konto wechseln/erstellen">
    </form>

    <!-- Aktuelles Konto löschen -->
    <form action="http://localhost/PHP-Project/PHPProject/actions/delete_current_user.php" method="post" style="display:inline-block; margin-right:10px;" onsubmit="return confirm('Bist du sicher, dass du deinen Account löschen willst?');">
        <input type="submit" value="Mein Konto löschen">
    </form>

    <!-- Weiterleitung zum CSV Import/Export Bereich -->
    <form action="http://localhost/PHP-Project/PHPProject/actions/import_csv.php" method="get" style="display:inline-block; margin-right:10px;">
        <input type="submit" value="CSV import/export">
    </form>

    <!-- Hilfe-Seite aufrufen -->
    <form action="http://localhost/PHP-Project/PHPProject/hilfe.php" method="get" style="display:inline-block; margin-right:10px;">
        <input type="submit" value="Hilfe">
    </form>

    <!-- Logout-Formular -->
    <form action="http://localhost/PHP-Project/PHPProject/actions/logout.php" method="post" style="display:inline-block; margin-left:20px;">
        <input type="submit" value="Abmelden">
    </form>
</div>

</body>
</html>
