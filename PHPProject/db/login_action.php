<?php
session_start();
require_once "database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE user_name = ?";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            if (password_verify($password, $user["password"])) {
                // User-ID in Session speichern
                $_SESSION['user_id'] = $user['id']; 

                header("Location: /PHP-Project/PHPProject/home.php");
                exit();
            } else {
                echo "<div class='achtung'>❌ Passwort stimmt nicht überein.</div>";
            }
        } else {
            echo "<div class='achtung'>❌ Benutzername existiert nicht.</div>";
        }
    } else {
        echo "<div class='achtung'>❌ Fehler bei der Datenbankabfrage.</div>";
    }
}
?>
