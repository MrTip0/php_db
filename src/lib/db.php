<?php
function getDB(): mysqli {
    $db = mysqli_connect("127.0.0.1", "utente", "password", "persone");
    if (!$db) {
        die("impossibile connttersi al database");
    }
    return $db;
}