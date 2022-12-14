<?php
if (empty($_GET["id"])) die("inserisci un id nella richiesta");
$id = $_GET["id"];
require __DIR__ . '/lib/db.php';

$db = getDB();
$stmt = $db->prepare("DELETE FROM dati_txt WHERE id = ?");

if (!$stmt) {
    die($db->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();

header('Location: .');