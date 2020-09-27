<?php

try {
    require "connect.php";

    $m = $_POST['myData'];

    $sql = $conn->prepare("SELECT escalao FROM modalidade_escalao WHERE modalidade = '$m' ORDER BY escalao");
    $sql->execute();

    $row = $sql->rowCount();

    if($row === 0) $json['rows'] = 0;
    if($row === 1) $json['rows'] = 1;
    if($row === 2) $json['rows'] = 2;

    if ($row > 0) {
        $json['escaloes'] = $sql->fetchAll(PDO::FETCH_ASSOC);
        $json['error'] = 'none';
    }

    echo json_encode($json);
} catch (Exception $e) {
    $json['error'] = 'exception';
    echo json_encode($json);
}
