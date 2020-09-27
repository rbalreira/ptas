<?php
try {
    require "connect.php";

    $sql = $conn->prepare("SELECT DISTINCT(id), nome, tipologia, freguesia, ST_AsGeoJSON(geom) AS geojson
                            FROM filtros_eq_desportivos");

    $sql->execute();

    # Build GeoJSON
    $output = '';
    $rowOutput = '';

    $result = $sql->setFetchMode(PDO::FETCH_ASSOC);

    foreach ($sql->fetchAll() as $row => $d) {
        $rowOutput = (strlen($rowOutput) > 0 ? ',' : '') .
            '{"type": "Feature", "geometry": ' . $d['geojson'] .
            ', "properties": {"nome": "' . $d['nome'] . '", "tipologia": "' .
            $d['tipologia'] . '", "freguesia": "' . $d['freguesia'] . '"}}';

        $output .= $rowOutput;
    }

    $json['pavilhoes'] = '{ "type": "FeatureCollection", "features": [ ' . $output . ' ]}';
    $json['error'] = 'none';
    echo json_encode($json);
} catch (Exception $e) {
    $json['error'] = 'exception';
    echo json_encode($json);
}
