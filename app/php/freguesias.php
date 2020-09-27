<?php

try {
    require "connect.php";

    $m = $_POST['myData'];

    $sql = $conn->prepare("SELECT freguesia AS f FROM freguesias_bv WHERE municipio ILIKE '$m' ORDER BY freguesia");
    $sql->execute();

    $json['freguesias'] = $sql->fetchAll(PDO::FETCH_ASSOC);
    $json['error'] = 'none';

    echo json_encode($json);

} catch (Exception $e) {
    $json['error'] = 'exception';
    echo json_encode($json);
}
