<?php
session_start(); // Session starten, um auf Session-Variablen zugreifen zu können

// Prüfen, ob per POST eine user_id übergeben wurde
if (isset($_POST['user_id'])) {
    // user_id aus POST als Integer in die Session speichern (Login-Simulation oder Benutzerwechsel)
    $_SESSION['user_id'] = (int)$_POST['user_id'];
}

// Nach dem Setzen der Session-Variable zurück zur Startseite oder einer anderen gewünschten Seite weiterleiten
header("Location: http://localhost/PHP-Project/PHPProject/home.php");
exit;
