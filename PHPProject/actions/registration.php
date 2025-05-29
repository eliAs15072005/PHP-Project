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
        <form action="/PHP-Project/PHPProject/db/register_action.php" method="post">
            <input type="text" name="username" placeholder="Benutzername" required>
            <input type="password" name="password" placeholder="Passwort" required>
            <input type="password" name="confirm_password" placeholder="Passwort wiederholen" required>
            <input type="submit" value="Registrieren">
        </form>
        <div class="login-link">
            Bereits ein Konto? <a href="http://localhost/PHP-Project/PHPProject/actions/login.php">Hier anmelden</a>
        </div>
    </div>
</body>
</html>
