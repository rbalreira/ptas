<?php
try {
    require "connect.php";

    $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                           FROM filtros_representacao");

    $sql->execute();

    # Build GeoJSON
    $output = '';
    $rowOutput = '';

    $result = $sql->setFetchMode(PDO::FETCH_ASSOC);

    foreach ($sql->fetchAll() as $row => $d) {
        $rowOutput = (strlen($rowOutput) > 0 ? ',' : '') .
            '{"type": "Feature", "geometry": ' . $d['geojson'] .
            ', "properties": {"nome": "' . $d['nome'] . '", "municipio": "' .
            $d['municipio'] . '", "tipo": "' . $d['tipo'] . '"}}';

        $output .= $rowOutput;
    }

    $json['clubes'] = '{ "type": "FeatureCollection", "features": [ ' . $output . ' ]}';
    $json['error'] = 'none';
    echo json_encode($json);
} catch (Exception $e) {
    $json['error'] = 'exception';
    echo json_encode($json);
}
