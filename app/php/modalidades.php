<?php

try {
    require "connect.php";

    $sql = $conn->prepare("SELECT nome FROM modalidade ORDER BY nome");
    $sql->execute();

    $json['modalidades'] = $sql->fetchAll(PDO::FETCH_ASSOC);
    $json['error'] = 'none';
    echo json_encode($json);

} catch (Exception $e) {
    $json['error'] = 'exception';
    echo json_encode($json);
}
