<?php

$r = [];
if (!empty($_GET["min_eta"]) && !empty($_GET["max_eta"])) {
    require __DIR__ . "/../lib/db.php";
    $db = getDB();

    $q = $db->prepare("SELECT res_prov, COUNT(id) AS n_persone, SUM(patrimonio) AS totale_patrimoni, AVG(patrimonio) AS media_patrimoni FROM dati_txt WHERE YEAR(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(data_nascita))) >= ? AND YEAR(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(data_nascita))) < ? GROUP BY res_prov");
    if (!$q) {
        die($db->error);
    }

    $q->bind_param("ii", $_GET["min_eta"], $_GET["max_eta"]);
    if (!$q->execute()) {
        die($q->error);
    }

    if (isset($_GET["download"])) {
        require __DIR__ . "/../lib/utils.php";
        header("Content-Type: text/csv");
        echo to_csv($q->get_result()->fetch_all(MYSQLI_ASSOC));
        exit;
    } else {
        $r = $q->get_result()->fetch_all(MYSQLI_NUM);
    }
}


?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiche intervallo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body class="m-4">
    <form action="" method="get" class="col">
        <h1 class="text-danger my-4">NB: L'intervallo &egrave; [min, max)</h1>
        <div class="mb-3 row">
            <label for="min_eta" class="col-sm-2 col-form-label">Et&agrave; minima</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="min_eta" name="min_eta" required pattern="^[0-9]+$" title="Inserisci un numero">
            </div>
        </div>
        <div class="mb-3 row">
            <label for="max_eta" class="col-sm-2 col-form-label">Et&agrave; massima</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="max_eta" name="max_eta" required pattern="^[0-9]+$" title="Inserisci un numero">
            </div>
        </div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Invia</button>
        </div>
    </form>

    <table class="col table table-striped my-4">
        <thead>
            <tr>
                <th scope="col">Provincia</th>
                <th scope="col">Numero persone</th>
                <th scope="col">Patrimonio Totale</th>
                <th scope="col">Patrimonio Medio</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($r as $provincia) { ?>
                <tr>
                    <th scope="row"><?php echo $provincia[0] ?></th>
                    <td><?php echo $provincia[1] ?></td>
                    <td><?php echo $provincia[2] ?>&euro;</td>
                    <td><?php echo $provincia[3] ?>&euro;</td>
                </tr>
            <?php }?>
        </tbody>
    </table>
    <?php if (!empty($_GET["min_eta"]) && !empty($_GET["max_eta"])) { ?>
    <div class="row fixed-bottom">
        <a download="report_<?php echo $_GET["min_eta"] ?>-<?php echo $_GET["max_eta"] ?>.csv" href="?min_eta=<?php echo urlencode($_GET["min_eta"])?>&max_eta=<?php echo urlencode($_GET["max_eta"])?>&download" class="btn btn-primary">Download</a>
    </div>
    <?php } ?>
</body>
</html>