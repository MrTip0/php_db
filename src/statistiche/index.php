<?php
require __DIR__ . "/../lib/db.php";
$db = getDB();

$top_five = $db->query("SELECT id, nome, cognome, patrimonio FROM dati_txt ORDER BY patrimonio DESC LIMIT 5");
if (!$top_five) {
    die($db->error);
}
$top_five = $top_five->fetch_all(MYSQLI_ASSOC);

$statistiche = $db->query("SELECT COUNT(id) AS 'n_persone', SUM(patrimonio) AS 'tot_patrimoni', AVG(patrimonio) AS 'avg_patrimoni' FROM dati_txt");
if (!$statistiche) {
    die($db->error);
}
$statistiche = $statistiche->fetch_all(MYSQLI_ASSOC)[0];

$statistiche_prov = $db->query("SELECT res_prov, COUNT(id) AS 'n_persone', SUM(patrimonio) AS 'tot_patrimoni', AVG(patrimonio) AS 'avg_patrimoni' FROM dati_txt GROUP BY res_prov");
if (!$statistiche) {
    die($db->error);
}
$statistiche_prov = $statistiche_prov->fetch_all(MYSQLI_NUM);

$omonimi = $db->query("SELECT nome, cognome, COUNT(nome) AS occorrenze FROM dati_txt GROUP BY nome, cognome HAVING COUNT(nome) > 1");
if (!$omonimi) {
    die($db->error);
}
$omonimi = $omonimi->fetch_all(MYSQLI_NUM);

$top_province = $db->query("SELECT res_prov, COUNT(id) AS n_abitanti FROM dati_txt GROUP BY res_prov HAVING COUNT(id) > 500 ORDER BY COUNT(id)");
if (!$top_province) {
    die($db->error);
}
$top_province = $top_province->fetch_all(MYSQLI_NUM);

$media_prov = $db->query("SELECT AVG(n_persone) AS media FROM (SELECT COUNT(id) AS n_persone FROM dati_txt GROUP BY res_prov) AS pers_prov");
if (!$media_prov) {
    die($db->error);
}
$media_prov = $media_prov->fetch_all(MYSQLI_NUM)[0][0];

$prov_piu_media = $db->query("SELECT res_prov, COUNT(id) AS n_persone FROM dati_txt GROUP BY res_prov HAVING COUNT(id) > (SELECT AVG(n_persone) AS media FROM (SELECT COUNT(id) AS n_persone FROM dati_txt GROUP BY res_prov) AS pers_prov)");
if (!$prov_piu_media) {
    die($db->error);
}
$prov_piu_media = $prov_piu_media->fetch_all(MYSQLI_NUM);

$contributo_min_25 = $db->query("SELECT id, nome, cognome, (YEAR(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(data_nascita)))-18)*50 AS contributo FROM dati_txt WHERE YEAR(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(data_nascita))) > 18 AND YEAR(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(data_nascita))) < 25 AND stato_civile <> 'Coniugato' AND stato_civile <> 'Coniugata'");
if (!$contributo_min_25) {
    die($db->error);
}
$contributo_min_25 = $contributo_min_25->fetch_all(MYSQLI_NUM);

$donne_non_sposate_lombardia = $db->query("SELECT id, nome, cognome, stato_civile FROM dati_txt WHERE stato_civile <> 'Coniugata' AND sesso = 'F' AND (res_prov = 'BG' OR res_prov = 'BS' OR res_prov = 'CO' OR res_prov = 'CR' OR res_prov = 'LC' OR res_prov = 'LO' OR res_prov = 'MN' OR res_prov = 'MI' OR res_prov = 'MB' OR res_prov = 'PV' OR res_prov = 'SO' OR res_prov = 'VA')");
if (!$donne_non_sposate_lombardia) {
    die($db->error);
}
$donne_non_sposate_lombardia = $donne_non_sposate_lombardia->fetch_all(MYSQLI_NUM);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiche</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body class="m-4">
    <a href=".." class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
        </svg>&ThickSpace;Torna indietro</a>
    <div class="row my-3">
        <h2>TOP 5 RICCHI</h2>
        <table class="table table-striped mx-3 col">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Cognome</th>
                    <th scope="col">Patrimonio</th>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach($top_five as $row) {
                ?>
                <tr>
                    <th scope="row"><?php echo $row["id"] ?></th>
                    <td><?php echo $row["nome"] ?></td>
                    <td><?php echo $row["cognome"] ?></td>
                    <td><?php echo $row["patrimonio"] ?>&euro;</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="row mb-5 mx-1">
        <a href="download/?qid=1" class="btn btn-primary">Download CSV</a>
    </div>
    <div class="row my-3">
        <h2>Statistiche</h2>
        <table class="table table-striped mx-3 col">
            <tr>
                <th scope="row">Totale Persone</th>
                <td><?php echo $statistiche['n_persone'] ?> Persone</td>
            </tr>
            <tr>
                <th scope="row">Totale Patrimoni</th>
                <td><?php echo $statistiche['tot_patrimoni'] ?>&euro;</td>
            </tr>
            <tr>
                <th scope="row">Media Patrimoni</th>
                <td><?php echo $statistiche['avg_patrimoni'] ?>&euro;</td>
            </tr>
        </table>
    </div>
    <div class="row mb-5 mx-1">
        <a href="download/?qid=2" class="btn btn-primary">Download CSV</a>
    </div>
    <div class="row my-3">
        <h2>Statistiche per provincia</h2>
        <table class="table table-striped mx-3 col">
            <thead>
                <tr>
                    <th scope="col">Provincia</th>
                    <th scope="col">Numero persone</th>
                    <th scope="col">Patrimonio totale</th>
                    <th scope="col">Patrimonio medio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($statistiche_prov as $provincia) { ?>
                    <tr>
                        <th scope="row"><?php echo $provincia[0] ?></th>
                        <td><?php echo $provincia[1] ?></td>
                        <td><?php echo $provincia[2] ?>&euro;</td>
                        <td><?php echo $provincia[3] ?>&euro;</td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
    <div class="row mb-5 mx-1">
        <a href="download/?qid=3" class="btn btn-primary">Download CSV</a>
    </div>
    <div class="row my-3">
        <h2>Omonimi</h2>
        <table class="table table-striped mx-3 col">
            <thead>
                <tr>
                    <th scope="col">Nome</th>
                    <th scope="col">Cognome</th>
                    <th scope="col">Occorrenze</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($omonimi as $omonimo) { ?>
                    <tr>
                        <th scope="row"><?php echo $omonimo[0] ?></th>
                        <th scope="row"><?php echo $omonimo[1] ?></th>
                        <td><?php echo $omonimo[2] ?></td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
    <div class="row mb-5 mx-1">
        <a href="download/?qid=4" class="btn btn-primary">Download CSV</a>
    </div>
    <div class="row my-3">
        <h2>Provincie con più di 500 abitanti</h2>
        <table class="table table-striped mx-3 col">
            <thead>
                <tr>
                    <th scope="col">Provincia</th>
                    <th scope="col">Numero abitanti</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($top_province as $provincia) { ?>
                    <tr>
                        <th scope="row"><?php echo $provincia[0] ?></th>
                        <td><?php echo $provincia[1] ?></td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
    <div class="row mb-5 mx-1">
        <a href="download/?qid=5" class="btn btn-primary">Download CSV</a>
    </div>
    <h2 class="row my-3 mx-1">Media abitanti per provincia: <?php echo $media_prov ?></h2>
    <div class="row my-3">
        <h2>Province con più abitanti della media</h2>
        <table class="table table-striped mx-3 col">
            <thead>
                <tr>
                    <th scope="col">Provincia</th>
                    <th scope="col">Numero persone</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($prov_piu_media as $provincia) { ?>
                    <tr>
                        <th scope="row"><?php echo $provincia[0] ?></th>
                        <td><?php echo $provincia[1] ?></td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
    <div class="row mb-5 mx-1">
        <a href="download/?qid=6" class="btn btn-primary">Download CSV</a>
    </div>
    <div class="row my-3">
        <h2>Spettanti contributo</h2>
        <table class="table table-striped mx-3 col">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Cognome</th>
                    <th scope="col">Contributo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($contributo_min_25 as $persone) { ?>
                    <tr>
                        <th scope="row"><?php echo $persone[0] ?></th>
                        <td><?php echo $persone[1] ?></td>
                        <td><?php echo $persone[2] ?></td>
                        <td><?php echo $persone[3] ?>&euro;</td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
    <div class="row mb-5 mx-1">
        <a href="download/?qid=7" class="btn btn-primary">Download CSV</a>
    </div>
    <div class="row my-3">
        <h2>Donne non sposate in lombardia</h2>
        <table class="table table-striped mx-3 col">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Cognome</th>
                    <th scope="col">Stato civile</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($donne_non_sposate_lombardia as $donna) { ?>
                    <tr>
                        <th scope="row"><?php echo $donna[0] ?></th>
                        <td><?php echo $donna[1] ?></td>
                        <td><?php echo $donna[2] ?></td>
                        <td><?php echo $donna[3] ?></td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
    <div class="row mb-5 mx-1">
        <a href="download/?qid=8" class="btn btn-primary">Download CSV</a>
    </div>
</body>
</html>