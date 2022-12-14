<!DOCTYPE html>
<html lang="it">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>More info</title>
</head>
<body class="m-4">
    <a href=".." class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
        </svg>&ThickSpace;Torna indietro</a>
    <h1 class="my-4">Più informazioni</h1>
    <table class="table table-striped mt-3">
        <?php
        require __DIR__ . "/../lib/db.php";
        $db = getDB();

        $fields = 'id, nome, cognome, sesso, nas_luogo, DATE_FORMAT(data_nascita, \'%d/%m/%Y\') AS data_nascita,
        cod_fiscale, res_luogo, res_prov, res_cap, indirizzo, titolo_studio, professione,
        patrimonio, stato_civile, autovettura, targa';
        $q = "SELECT $fields FROM dati_txt WHERE id = ?";

        $q = $db->prepare($q);
        if (!$q) {
            die($db->error);
        }

        $q->bind_param("s", $_GET["id"]);

        if (!$q->execute()) {
            die($q->error);
        }

        $r = $q->get_result();
        while ($arr = $r->fetch_assoc()) {
            ?>
        <tr>
            <th scope="row">#</th>
            <th scope="col"><?php echo $arr["id"] ?></th>
        </tr>
        <tr>
            <th scope="row">Cognome</th>
            <td><?php echo $arr["cognome"] ?></td>
        </tr>
        <tr>
            <th scope="row">Nome</th>
            <td><?php echo $arr["nome"] ?></td>
        </tr>
        <tr>
            <th scope="row">Sesso</th>
            <td><?php echo $arr["sesso"] ?></td>
        </tr>
        <tr>
            <th scope="row">Data di nascita</th>
            <td><?php echo $arr["data_nascita"] ?></td>
        </tr>
        <tr>
            <th scope="row">Luogo di nascita</th>
            <td><?php echo $arr["nas_luogo"] ?></td>
        </tr>
        <tr>
            <th scope="row">Codice Fiscale</th>
            <td><?php echo $arr["cod_fiscale"] ?></td>
        </tr>
        <tr>
            <th scope="row">Luogo di residenza</th>
            <td><?php echo $arr["res_luogo"] ?></td>
        </tr>
        <tr>
            <th scope="row">Provincia</th>
            <td><?php echo $arr["res_prov"] ?></td>
        </tr>
        <tr>
            <th scope="row">CAP</th>
            <td><?php echo $arr["res_cap"] ?></td>
        </tr>
        <tr>
            <th scope="row">Indirizzo</th>
            <td><?php echo $arr["indirizzo"] ?></td>
        </tr>
        <tr>
            <th scope="row">Titolo di studio</th>
            <td><?php echo $arr["titolo_studio"] ?></td>
        </tr>
        <tr>
            <th scope="row">Professione</th>
            <td><?php echo $arr["professione"] ?></td>
        </tr>
        <tr>
            <th scope="row">Patrimonio</th>
            <td>€<?php echo $arr["patrimonio"]?></td>
        </tr>
        <tr>
            <th scope="row">Stato civile</th>
            <td><?php echo $arr["stato_civile"]?></td>
        </tr>
        <tr>
            <th scope="row">Autovettura</th>
            <td><?php echo $arr["autovettura"]?></td>
        </tr>
        <tr>
            <th scope="row">Targa</th>
            <td><?php echo $arr["targa"]?></td>
        </tr>
        <?php } ?>
    </table>
    <div class="row">
        <a href="../update/?id=<?php echo $_GET['id'] ?>" class="btn btn-primary">Modifica</a>
    </div>
</body>
</html>