<?php
session_start(); // Session starten, um Nachrichten (Erfolg/Fehler) zwischen Seitenaufrufen zu speichern
error_reporting(E_ALL);
ini_set('display_errors', 1); // Fehleranzeige aktivieren (nur für Entwicklung sinnvoll)
require_once __DIR__ . "/../db/database.php"; // Datenbankverbindung einbinden

// Prüfen, ob das Formular abgeschickt wurde (POST-Methode)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Benutzereingaben aus dem Formular auslesen und trimmen (Leerzeichen entfernen)
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Passwort-Hash erzeugen (sicheres Speichern)
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $errors = array(); // Array für Fehlermeldungen vorbereiten

    // Pflichtfelder prüfen
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $errors[] = "Alle Felder müssen ausgefüllt sein.";
    }

    // Prüfen, ob Passwort und Wiederholung übereinstimmen
    if ($password !== $confirm_password) {
        $errors[] = "Die Passwörter stimmen nicht überein.";
    }

    // Benutzername auf Existenz prüfen, falls bisher keine Fehler aufgetreten sind
    if (count($errors) === 0) {
        $checkSql = "SELECT user_name FROM users WHERE user_name = ?";
        $stmtCheck = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmtCheck, $checkSql)) {
            // Benutzername als Parameter binden und ausführen
            mysqli_stmt_bind_param($stmtCheck, "s", $username);
            mysqli_stmt_execute($stmtCheck);
            mysqli_stmt_store_result($stmtCheck);

            // Wenn Benutzername bereits existiert, Fehlermeldung hinzufügen
            if (mysqli_stmt_num_rows($stmtCheck) > 0) {
                $errors[] = "Benutzername ist bereits vergeben. Bitte wähle einen anderen.";
            }
            mysqli_stmt_close($stmtCheck);
        } else {
            $errors[] = "Fehler bei der Überprüfung des Benutzernamens.";
        }
    }

    // Wenn keine Fehler vorhanden sind, Benutzer in der Datenbank speichern
    if (count($errors) === 0) {
        $sql = "INSERT INTO users (user_name, password) VALUES (?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            // Benutzername und Passwort-Hash als Parameter binden
            mysqli_stmt_bind_param($stmt, "ss", $username, $password_hash);

            // Ausführen und prüfen, ob erfolgreich gespeichert
            if (mysqli_stmt_execute($stmt)) {
                // Erfolgsmeldung in Session speichern und Seite neu laden
                $_SESSION['success'] = "✅ Registrierung erfolgreich!";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $errors[] = "❌ Fehler beim Speichern: " . mysqli_error($conn);
            }
        } else {
            $errors[] = "❌ Fehler beim Vorbereiten der Datenbankabfrage.";
        }
    }

    // Wenn Fehler vorhanden sind, in der Session speichern und Seite neu laden
    if (count($errors) > 0) {
        $_SESSION['errors'] = $errors;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Registrieren</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="registration">

<div class="register-box">
    <h2>Registrieren</h2>
    <!-- Formular für Registrierung, bei Absenden wird die eigene Seite aufgerufen -->
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <input type="text" name="username" placeholder="Benutzername" required>
        <input type="password" name="password" placeholder="Passwort" required>
        <input type="password" name="confirm_password" placeholder="Passwort wiederholen" required>
        <input type="submit" value="Registrieren">
    </form>
    <div class="login-link">
        Bereits ein Konto? <a href="http://localhost/PHP-Project/PHPProject/actions/login.php">Hier anmelden</a>
    </div>
</div>

<?php
// Erfolgsnachricht anzeigen (wenn vorhanden)
if (isset($_SESSION['success'])) {
    echo "<div class='erfolg'>{$_SESSION['success']}</div>";
    unset($_SESSION['success']);
}

// Fehlermeldungen ausgeben (wenn vorhanden)
if (isset($_SESSION['errors'])) {
    foreach ($_SESSION['errors'] as $error) {
        echo "<div class='achtung'>$error</div>";
    }
    unset($_SESSION['errors']);
}
?>

</body>
</html>
