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
                    <th scope="col">Elimina</th>
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
                <th scope="row">
                    <a href="./info/?id=<?php echo $arr["id"] ?>">
                        <?php echo $arr["id"] ?>
                    </a>
                </th>
                <td><?php echo $arr["cognome"] ?></td>
                <td><?php echo $arr["nome"] ?></td>
                <td><?php echo $arr["data_nascita"] ?></td>
                <td>
                    <a href="remove.php?id=<?php echo $arr["id"] ?>" class="btn btn-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                            <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                        </svg>
                    </a>
                </td>
            </tr>
            <?php
        }
        ?>
            </tbody>
        </table>
        <div class="row fixed-bottom">
            <a href="add/" class="btn btn-primary">Aggiungi persona</a>
        </div>
    </body>
</html>