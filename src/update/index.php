<?php
function update() {
    require __DIR__ . '/../lib/db.php';
    $fields = [
        'nome', 'cognome', 'data_nascita',
        'nas_luogo', 'cod_fiscale', 'res_luogo', 'res_prov', 'sesso', 
        'res_cap', 'indirizzo', 'titolo_studio', 'professione',
        'patrimonio', 'autovettura', 'targa', 'stato_civile'
    ];

    $data = [];

    foreach ($fields as $field) {
        $data[$field] = null;
    }

    foreach ($_GET as $key => $value) {
        if ($value != "") {
            $data[$key] = htmlentities($value);
        }
    }

    if ($data["sesso"] == "F") {
        switch ($data["stato_civile"]) {
            case 'Coniugato':
                $data["stato_civile"] = 'Coniugata';
                break;
            case 'Divorziato':
                $data["stato_civile"] = 'Divorziata';
                break;
            case 'Separato':
                $data["stato_civile"] = 'Separata';
                break;
            case 'Celibe':
                $data["stato_civile"] = 'Nubile';
                break;
            case 'Vedovo':
                $data["stato_civile"] = 'Vedova';
                break;
        }
    }
    if ($data["sesso"] == "*") {
        switch ($data["stato_civile"]) {
            case 'Coniugato':
                $data["stato_civile"] = 'Coniugat*';
                break;
            case 'Divorziato':
                $data["stato_civile"] = 'Divorziat*';
                break;
            case 'Separato':
                $data["stato_civile"] = 'Separat*';
                break;
            case 'Celibe':
                $data["stato_civile"] = 'Nubil*';
                break;
            case 'Vedovo':
                $data["stato_civile"] = 'Vedov*';
                break;
        }
    }

    $db = getDB();
    foreach ($data as $key => $value) {
        if ($value == null || !in_array($key, $fields)) {
            continue;
        }


        $stmt = $db->prepare("UPDATE dati_txt SET $key = ? WHERE id = ?");
        if (!$stmt) {
            die($db->error);
        }
        $stmt->bind_param("si", $value, $_GET['id']);
        if (!$stmt->execute()) {
            die($stmt->error);
        }
    }

    header("Location: ..");
}

if (empty($_GET['id'])) {
    header('Location: ..');
    exit;
}

$fields = [
    'nome', 'cognome', 'data_nascita',
    'nas_luogo', 'cod_fiscale', 'res_luogo', 'res_prov', 'sesso', 
    'res_cap', 'indirizzo', 'titolo_studio', 'professione',
    'patrimonio', 'autovettura', 'targa', 'stato_civile'
];
$ok = false;
foreach ($fields as $field) {
    if (!empty($_GET[$field])) {
        $ok = true;
        break;
    }
}

if ($ok) update();

$fields = [
    'nas_luogo' => 'Luogo di nascita',
    'cod_fiscale' => 'Codice fiscale',
    'res_luogo' => 'Luogo di residenza',
    'res_prov' => 'Provincia',
    'res_cap' => 'CAP',
    'indirizzo' => 'Indirizzo',
    'titolo_studio' => 'Titolo di studio',
    'professione' => 'Professione',
    'patrimonio' => 'Patrimonio',
    'autovettura' => 'Autovettura',
    'targa' => 'Targa'
];

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body class="m-4">
    <form action="" method="get">
        <input type="hidden" name="id" value="<?php echo $_GET["id"]?>">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" name="nome" id="nome">
        </div>
        <div class="mb-3">
            <label for="cognome" class="form-label">Cognome</label>
            <input type="text" class="form-control" name="cognome" id="cognome">
        </div>
        <div class="mb-3">
            <label for="data_nascita" class="form-label">Data di nascita</label>
            <input type="date" class="form-control" name="data_nascita" id="data_nascita">
        </div>
        <div class="mb-3">
            <label for="sesso" class="form-label">Sesso</label>
            <select name="sesso" class="form-select" id="sesso">
                <option value="" selected>Lascia come è</option>
                <option value="*">Preferisco non dire</option>
                <option value="M">Maschio</option>
                <option value="F">Femmina</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="stato_civile">Stato civile</label>
            <select name="stato_civile" class="form-select" id="stato_civile">
                <option value="" selected>Preferisco non dire</option>
                <option value="Coniugato">Coniugato/a</option>
                <option value="Divorziato">Divorziato/a</option>
                <option value="Separato">Separato/a</option>
                <option value="Celibe">Celibe/Nubile</option>
                <option value="Vedovo">Vedovo/a</option>
            </select>
        </div>
        <?php
        foreach ($fields as $key => $value) {
            ?>
            <div class="mb-3">
                <label for="<?php echo $key?>" class="form-label"><?php echo $value?></label>
                <input type="text" class="form-control" name="<?php echo $key?>" id="<?php echo $key?>">
            </div>
            <?php
        }
        ?>
        <div class="row">
            <button type="submit" class="btn btn-primary mb-3">Aggiorna persona</button>
        </div>
    </form>
</body>
</html>