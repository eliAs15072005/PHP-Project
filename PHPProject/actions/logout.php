<?php
session_start();        // Session starten, um Zugriff auf die aktuelle Session zu bekommen
session_unset();        // Alle Session-Variablen löschen
session_destroy();      // Die Session selbst zerstören, um Benutzer abzumelden
header("Location: http://localhost/PHP-Project/PHPProject/actions/login.php");  // Weiterleitung zur Login-Seite
exit;                   // Script-Ausführung beenden, damit kein weiterer Code ausgeführt wird
