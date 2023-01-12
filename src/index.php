<DOCTYPE html>
<html lang="it">
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Persone</title>
    </head>
    <body class="m-4">
        <form action="" method="get">
            <input type="hidden" name="from" value="0">
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
        <table class="table table-striped mt-5" id="data-table">
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
        $q = "SELECT $fields FROM dati_txt WHERE id = ? LIMIT ?, 25";
        $nq = "SELECT COUNT(id) FROM dati_txt WHERE id = ?";
        $data = "0";

        if (!empty($_GET["id"])) {
            $q = "SELECT $fields FROM dati_txt WHERE id = ? ORDER BY id LIMIT ?, 25";
            $nq = "SELECT COUNT(id) FROM dati_txt WHERE id = ?";
            $data = $_GET["id"];
        } elseif(!empty($_GET["cognome"])) {
            $q = "SELECT $fields FROM dati_txt WHERE cognome LIKE ? ORDER BY id LIMIT ?, 25";
            $nq = "SELECT COUNT(id) FROM dati_txt WHERE cognome LIKE ?";
            $data = '%'.$_GET["cognome"].'%';
        } elseif(!empty($_GET["nome"])) {
            $q = "SELECT $fields FROM dati_txt WHERE nome LIKE ? ORDER BY id LIMIT ?, 25";
            $nq = "SELECT COUNT(id) FROM dati_txt WHERE nome LIKE ?";
            $data = '%'.$_GET["nome"].'%';
        }

        $q = $db->prepare($q);
        if (!$q) {
            die($db->error);
        }
        
        $from = intval($_GET["from"] ?? 0);
        $q->bind_param("si", $data, $from);
        
        if (!$q->execute()) {
            die($q->error);
        }
        
        
        $r = $q->get_result();


        $num = $db->prepare($nq);
        if (!$nq) {
            die($db->error);
        }
        
        $num->bind_param("s", $data);
        
        if (!$num->execute()) {
            die($num->error);
        }

        $rn = $num->get_result()->fetch_all(MYSQLI_NUM)[0][0];
        
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
        <div class="row text-center mb-5 mt-3">
            <a class="col btn btn-primary text-center <?php if (floor($from / 25 + 1) <= 1) { echo "disabled"; } ?>"
                        href="?from=<?php echo $from - 25?>&id=<?php echo $_GET["id"]??"" ?>&nome=<?php echo $_GET["nome"]??"" ?>&cognome=<?php echo $_GET["cognome"]??"" ?>#data-table">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
            </a>
            <div class="col align-content-center row">
                <h4 class="mb-0"><?php echo floor($from / 25 + 1) ?>/<?php echo max(1, floor($rn/25)) ?></h4>
            </div>
            <a class="col btn btn-primary <?php if (floor($from / 25 + 1) >= max(1, floor($rn/25))) { echo "disabled"; } ?>"
                href="?from=<?php echo $from + 25?>&id=<?php echo $_GET["id"]??"" ?>&nome=<?php echo $_GET["nome"]??"" ?>&cognome=<?php echo $_GET["cognome"]??"" ?>#data-table">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                </svg>
            </a>
        </div>
        <div class="row fixed-bottom">
            <a href="add/" class="btn btn-primary col">Aggiungi persona</a>
            <a href="statistiche/" class="btn btn-primary col">Statistiche</a>
        </div>
    </body>
</html>