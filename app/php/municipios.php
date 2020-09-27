<?php

try {
    require "connect.php";

    $sql = $conn->prepare("SELECT municipio AS m FROM municipios_bv ORDER BY municipio");
    $sql->execute();

    $json['municipios'] = $sql->fetchAll(PDO::FETCH_ASSOC);
    $json['error'] = 'none';
    echo json_encode($json);

} catch (Exception $e) {
    $json['error'] = 'exception';
    echo json_encode($json);
}
