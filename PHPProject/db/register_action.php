<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $errors = array();

    if (empty($username) || empty($password) || empty($confirm_password)) {
        array_push($errors, "Alle Felder müssen ausgefüllt sein.");
    }

    if ($password !== $confirm_password) {
        array_push($errors, "Die Passwörter stimmen nicht überein.");
    }

    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<div class='achtung'>$error</div>";
        }
    } else {
        $sql = "INSERT INTO users (user_name, password) VALUES (?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $username, $password_hash);
            if (mysqli_stmt_execute($stmt)) {
                echo "<div class='erfolg'>✅ Registrierung erfolgreich!</div>";
            } else {
                echo "<div class='achtung'>❌ Fehler beim Speichern: " . mysqli_error($conn) . "</div>";
            }
        } else {
            echo "<div class='achtung'>❌ Fehler beim Vorbereiten der Datenbankabfrage.</div>";
        }
    }
}
?>
