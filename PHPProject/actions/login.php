<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Anmelden</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class = "login">

<div class="login-box">
    <h2>Anmelden Hyper Hyper</h2>
    <form action="/PHP-Project/PHPProject/db/login_action.php" method="post">
        <input type="text" name="username" placeholder="Benutzername" required>
        <input type="password" name="password" placeholder="Passwort" required>
        <input type="submit" value="Login">
    </form>
    <div class="register-link">
        Noch kein Konto? <a href="http://localhost/PHP-Project/PHPProject/actions/registration.php">Registrieren</a>
    </div>
</div>

</body>
</html>
