<DOCTYPE html>
<html lang="it">
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body class="m-4">
        <form action="" method="get">
            <div class="mb-3 row">
                <label for="inputId" class="col-sm-2 col-form-label">Id</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputId" name="id">
                </div>
            </div>
            <div class="mb-3 row">
                <label for="inputNome" class="col-sm-2 col-form-label">Nome</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputNome" name="nome">
                </div>
            </div>
            <div class="mb-3 row">
                <label for="inputCognome" class="col-sm-2 col-form-label">Cognome</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputCognome" name="cognome">
                </div>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Invia</button>
            </div>
        </form>
        <table class="table table-striped mt-5">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Cognome</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Data di nascita</th>
                </tr>
            </thead>
            <tbody>
        <?php
        require __DIR__ . "/lib/db.php";
        $db = getDB();

        $fields = 'id, nome, cognome, DATE_FORMAT(data_nascita, \'%d/%m/%Y\') AS data_nascita';
        $q = "SELECT $fields FROM dati_txt WHERE id = ?";
        $data = "0";

        if (!empty($_GET["id"])) {
            $q = "SELECT $fields FROM dati_txt WHERE id = ?";
            $data = $_GET["id"];
        } elseif(!empty($_GET["cognome"])) {
            $q = "SELECT $fields FROM dati_txt WHERE cognome LIKE ?";
            $data = '%'.$_GET["cognome"].'%';
        } elseif(!empty($_GET["nome"])) {
            $q = "SELECT $fields FROM dati_txt WHERE nome LIKE ?";
            $data = '%'.$_GET["nome"].'%';
        }

        $q = $db->prepare($q);
        if (!$q) {
            die($db->error);
        }

        $q->bind_param("s", $data);

        if (!$q->execute()) {
            die($q->error);
        }


        $r = $q->get_result();
        while ($arr = $r->fetch_assoc()) {
            ?>
            <tr>
                <th scope="row"><a href="./info/?id=<?php echo $arr["id"] ?>"><?php echo $arr["id"] ?></a></th>
                <td><?php echo $arr["cognome"] ?></td>
                <td><?php echo $arr["nome"] ?></td>
                <td><?php echo $arr["data_nascita"] ?></td>
            </tr>
            <?php
        }
        ?>
            </tbody>
        </table>
    </body>
</html>