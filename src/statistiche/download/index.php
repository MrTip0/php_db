<?php
require __DIR__ . "/../../lib/db.php";
require __DIR__ . "/../../lib/utils.php";

$db = getDB();

if (empty($_GET["qid"])) {
    die("Inserisci il parametro qid");
}

$id = intval($_GET["qid"]);
$q = "";

switch ($id) {
    case 1:
        $q = "SELECT id, nome, cognome, patrimonio FROM dati_txt ORDER BY patrimonio DESC LIMIT 5";
        break;
    
    case 2:
        $q = "SELECT COUNT(id) AS 'n_persone', SUM(patrimonio) AS 'tot_patrimoni', AVG(patrimonio) AS 'avg_patrimoni' FROM dati_txt";
        break;
    
    case 3:
        $q = "SELECT res_prov, COUNT(id) AS 'n_persone', SUM(patrimonio) AS 'tot_patrimoni', AVG(patrimonio) AS 'avg_patrimoni' FROM dati_txt GROUP BY res_prov";
        break;
    
    case 4:
        $q = "SELECT nome, cognome, COUNT(nome) AS occorrenze FROM dati_txt GROUP BY nome, cognome HAVING COUNT(nome) > 1";
        break;
    
    case 5:
        $q = "SELECT res_prov, COUNT(id) AS n_abitanti FROM dati_txt GROUP BY res_prov HAVING COUNT(id) > 500 ORDER BY COUNT(id)";
        break;
    
    case 6:
        $q = "SELECT res_prov, COUNT(id) AS n_persone FROM dati_txt GROUP BY res_prov HAVING COUNT(id) > (SELECT AVG(n_persone) AS media FROM (SELECT COUNT(id) AS n_persone FROM dati_txt GROUP BY res_prov) AS pers_prov)";
        break;
    
    case 7:
        $q = "SELECT id, nome, cognome, (YEAR(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(data_nascita)))-18)*50 AS contributo FROM dati_txt WHERE YEAR(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(data_nascita))) > 18 AND YEAR(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(data_nascita))) < 25 AND stato_civile <> 'Coniugato' AND stato_civile <> 'Coniugata'";
        break;
    
    case 8:
        $q = "SELECT id, nome, cognome, stato_civile FROM dati_txt WHERE stato_civile <> 'Coniugata' AND sesso = 'F' AND (res_prov = 'BG' OR res_prov = 'BS' OR res_prov = 'CO' OR res_prov = 'CR' OR res_prov = 'LC' OR res_prov = 'LO' OR res_prov = 'MN' OR res_prov = 'MI' OR res_prov = 'MB' OR res_prov = 'PV' OR res_prov = 'SO' OR res_prov = 'VA')";
        break;
    default:
        die("qid non valido");
        break;
}

$r = $db->query($q);
if (!$r) {
    die($db->error);
}
$r = $r->fetch_all(MYSQLI_ASSOC);

header("Content-Type: text/csv");
echo to_csv($r);
