<?php

$data = json_decode($_POST['myData']);

$x = $data->x;
$y = $data->y;

try {
    require "connect.php";

    $sql = $conn->prepare("SELECT * FROM getNearestPoi(:x, :y)");

    $sql->bindParam(':x', $x);
    $sql->bindParam(':y', $y);
    $sql->execute();

    #Build GeoJson
    $output    = '';
    $rowoutput = '';

    $result = $sql->setFetchMode(PDO::FETCH_ASSOC);

    foreach ($sql->fetchAll() as $row => $d) {
        $rowoutput = (strlen($rowoutput) > 0 ? ',' : '') .
            '{"type": "Feature", "geometry": ' . $d['geojson'] .
            ', "properties": {"nome": "' . $d['nome'] . 
            '", "categoria": "' . $d['categoria'] . '"}}';

        $output .= $rowoutput;
    }

    $json['poi'] = ' {"type": "FeatureCollection", "features": [ ' . $output . ' ]}';
    $json['error'] = 'none';

    echo json_encode($json);
} catch (PDOException $e) {
    $json['erro'] = 'exception';
    echo json_encode($json);
}
