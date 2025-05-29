<?php
    $servername = "localhost";
    $username = "root"; // Dein DB-Benutzername
    $password = ""; // Dein DB-Passwort
    $db_name = "projekt_datenbank";
    $conn = mysqli_connect($servername, $username, $password, $db_name);
  
    if(!$conn) {
        die("Verbindung fehlgeschlagen");
    }
?>