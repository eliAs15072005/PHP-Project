<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Anmelden</title>
    <!-- Einbindung einer externen CSS-Datei zur Gestaltung der Login-Seite -->
    <link rel="stylesheet" href="../style.css">  
</head>
<!-- Dem Body wird die Klasse "login" zugewiesen, um ihn im CSS speziell zu stylen -->
<body class="login">

<!-- Container für das Login-Formular -->
<div class="login-box">
    <h2>Anmelden Hyper Hyper</h2>

    <!-- Formular zur Benutzeranmeldung -->
    <!-- Die eingegebenen Daten werden per POST an login_action.php geschickt -->
    <form action="/PHP-Project/PHPProject/db/login_action.php" method="post">
        <!-- Eingabefeld für den Benutzernamen -->
        <input type="text" name="username" placeholder="Benutzername" required>
        <!-- Eingabefeld für das Passwort -->
        <input type="password" name="password" placeholder="Passwort" required>
        <!-- Button zum Absenden des Formulars -->
        <input type="submit" value="Login">
    </form>

    <!-- Hinweis für Benutzer, die noch kein Konto haben -->
    <div class="register-link">
        Noch kein Konto?
        <!-- Link zur Registrierungsseite -->
        <a href="http://localhost/PHP-Project/PHPProject/actions/registration.php">Registrieren</a>
    </div>
</div>

</body>
</html>
