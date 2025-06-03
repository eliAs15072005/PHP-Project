<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Registrieren</title>
    <!-- Einbindung der externen CSS-Datei für das Styling -->
    <link rel="stylesheet" href="../style.css">  
</head>
<!-- Body mit der Klasse "registration" für spezifisches Styling -->
<body class="registration">
    <!-- Container für das Registrierungsformular -->
    <div class="register-box">
        <h2>Registrieren</h2>
        
        <!-- Formular zur Benutzerregistrierung -->
        <!-- Daten werden per POST an register_action.php gesendet -->
        <form action="/PHP-Project/PHPProject/db/register_action.php" method="post">
            <!-- Eingabefeld für den gewünschten Benutzernamen -->
            <input type="text" name="username" placeholder="Benutzername" required>
            <!-- Eingabefeld für das Passwort -->
            <input type="password" name="password" placeholder="Passwort" required>
            <!-- Eingabefeld zur Bestätigung des Passworts -->
            <input type="password" name="confirm_password" placeholder="Passwort wiederholen" required>
            <!-- Absende-Button zum Registrieren -->
            <input type="submit" value="Registrieren">
        </form>

        <!-- Hinweis und Link für bereits registrierte Nutzer -->
        <div class="login-link">
            Bereits ein Konto? 
            <!-- Link zurück zur Login-Seite -->
            <a href="http://localhost/PHP-Project/PHPProject/actions/login.php">Hier anmelden</a>
        </div>
    </div>
</body>
</html>
