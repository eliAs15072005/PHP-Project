<?php
session_start(); // Session starten, um auf Login-Status zuzugreifen
require_once __DIR__ . "/db/database.php"; // Datenbankverbindung einbinden

// Prüfen, ob ein Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header("Location: actions/login.php"); // Wenn nicht eingeloggt, Weiterleitung zum Login
    exit;
}

$user_id = $_SESSION['user_id'];

// Prüfen, ob aktueller Benutzer Admin-Rechte hat
$stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$isAdmin = ($result && $result->fetch_assoc()['is_admin'] == 1);

if (!$isAdmin) {
    // Zugriff verweigern, wenn kein Admin
    echo "<h2>Zugriff verweigert</h2><p>Nur Administratoren dürfen diese Seite aufrufen.</p>";
    exit;
}

// Alle Benutzer abrufen (inkl. Passwort — Achtung: Passwörter sollten nie unverschlüsselt angezeigt werden!)
$users = $conn->query("SELECT id, user_name, password FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Admin-Bereich – Benutzerübersicht</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        table { border-collapse: collapse; width: 80%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f4f4f4; }
        input[type="submit"] {
            padding: 8px 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <h1>Admin-Bereich – Benutzerübersicht</h1>

    <table>
    <tr>
        <th>ID</th>
        <th>Benutzername</th>
        <th>Aktion</th> <!-- Spalte für Aktionen (z.B. Löschen) -->
    </tr>
    <?php while ($user = $users->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($user['id']) ?></td>
        <td><?= htmlspecialchars($user['user_name']) ?></td>
        <td>
            <!-- Formular zum Löschen des Benutzers -->
            <form action="/PHP-Project/PHPProject/actions/delete_user.php" method="post" onsubmit="return confirm('Benutzer wirklich löschen?');">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']) ?>">
                <input type="submit" value="Löschen">
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
    </table>

    <!-- Button zum Zurückkehren zur Startseite -->
    <form action="http://localhost/PHP-Project/PHPProject/home.php" method="get" style="margin-top: 20px;">
        <input type="submit" value="Zurück zur Startseite">
    </form>
</body>
</html>
