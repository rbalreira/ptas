<?php
try {
    header('content-type: application/json; charset=utf-8');

    require "connect.php";

    $data = json_decode($_POST['myData']);

    $sql = $polygon = $output = $rowOutput = $result = '';
    $json;

    if (isset($data->municipio)) {
        $mun = $data->municipio;
        if (isset($data->modalidade)) {
            $mod = $data->modalidade;
            if (isset($data->escalao)) {
                $escalao = $data->escalao;
                if (isset($data->genero)) {
                    $genero = $data->genero;
                    if (isset($data->adaptado)) {
                        $adaptado = $data->adaptado;
                        $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                        FROM filtros_representacao
                        WHERE municipio = :mun AND modalidade = :mod AND escalao_equipa = :escalao AND genero_equipa = :genero AND adaptado_equipa = :adaptado");
                        $sql->bindParam(':mun', $mun);
                        $sql->bindParam(':mod', $mod);
                        $sql->bindParam(':escalao', $escalao);
                        $sql->bindParam(':genero', $genero);
                        $sql->bindParam(':adaptado', $adaptado);
                    } else {
                        $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                        FROM filtros_representacao
                        WHERE municipio = :mun AND modalidade = :mod AND escalao_equipa = :escalao AND genero_equipa = :genero");
                        $sql->bindParam(':mun', $mun);
                        $sql->bindParam(':mod', $mod);
                        $sql->bindParam(':escalao', $escalao);
                        $sql->bindParam(':genero', $genero);
                    }
                } else if (isset($data->adaptado)) {
                    $adaptado = $data->adaptado;
                    $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                    FROM filtros_representacao
                    WHERE municipio = :mun AND modalidade = :mod AND escalao_equipa = :escalao AND adaptado_equipa = :adaptado");
                    $sql->bindParam(':mun', $mun);
                    $sql->bindParam(':mod', $mod);
                    $sql->bindParam(':escalao', $escalao);
                    $sql->bindParam(':adaptado', $adaptado);
                } else {
                    $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                    FROM filtros_representacao
                    WHERE municipio = :mun AND modalidade = :mod AND escalao_equipa = :escalao");
                    $sql->bindParam(':mun', $mun);
                    $sql->bindParam(':mod', $mod);
                    $sql->bindParam(':escalao', $escalao);
                }
            } else if (isset($data->genero)) {
                $genero = $data->genero;
                if (isset($data->adaptado)) {
                    $adaptado = $data->adaptado;
                    $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                    FROM filtros_representacao
                    WHERE municipio = :mun AND modalidade = :mod AND genero_equipa = :genero AND adaptado_equipa = :adaptado");
                    $sql->bindParam(':mun', $mun);
                    $sql->bindParam(':mod', $mod);
                    $sql->bindParam(':genero', $genero);
                    $sql->bindParam(':adaptado', $adaptado);
                } else {
                    $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                    FROM filtros_representacao
                    WHERE municipio = :mun AND modalidade = :mod AND genero_equipa = :genero");
                    $sql->bindParam(':mun', $mun);
                    $sql->bindParam(':mod', $mod);
                    $sql->bindParam(':genero', $genero);
                }
            } else if (isset($data->adaptado)) {
                $adaptado = $data->adaptado;
                $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                FROM filtros_representacao
                WHERE municipio = :mun AND modalidade = :mod AND adaptado_equipa = :adaptado");
                $sql->bindParam(':mun', $mun);
                $sql->bindParam(':mod', $mod);
                $sql->bindParam(':adaptado', $adaptado);
            } else {
                $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                FROM filtros_representacao
                WHERE municipio = :mun AND modalidade = :mod");
                $sql->bindParam(':mun', $mun);
                $sql->bindParam(':mod', $mod);
            }
        } else if (isset($data->genero)) {
            $genero = $data->genero;
            if (isset($data->adaptado)) {
                $adaptado = $data->adaptado;
                $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                FROM filtros_representacao
                WHERE municipio = :mun AND genero_equipa = :genero AND adaptado_equipa = :adaptado");
                $sql->bindParam(':mun', $mun);
                $sql->bindParam(':genero', $genero);
                $sql->bindParam(':adaptado', $adaptado);
            } else {
                $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                FROM filtros_representacao
                WHERE municipio = :mun AND genero_equipa = :genero");
                $sql->bindParam(':mun', $mun);
                $sql->bindParam(':genero', $genero);
            }
        } else if (isset($data->adaptado)) {
            $adaptado = $data->adaptado;
            $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
            FROM filtros_representacao
            WHERE municipio = :mun AND adaptado_equipa = :adaptado");
            $sql->bindParam(':mun', $mun);
            $sql->bindParam(':adaptado', $adaptado);
        } else {
            $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
            FROM filtros_representacao
            WHERE municipio = :mun");
            $sql->bindParam(':mun', $mun);
        }
        $polygon = $conn->prepare("SELECT ST_AsGeoJSON(geom) AS geojson
        FROM municipios_bv WHERE municipio = :mun");
        $polygon->bindParam(':mun', $mun);

        $polygon->execute();

        $result = $polygon->setFetchMode(PDO::FETCH_ASSOC);

        foreach ($polygon->fetchAll() as $row => $d)
            $output = '{
        "type": "FeatureCollection",
        "features": [' . $d["geojson"] . ']
      }';

        $json['municipio'] = $output;

        $output = '';
    } else if (isset($data->freguesia)) {
        $freg = $data->freguesia;
        if (isset($data->modalidade)) {
            $mod = $data->modalidade;
            if (isset($data->escalao)) {
                $escalao = $data->escalao;
                if (isset($data->genero)) {
                    $genero = $data->genero;
                    if (isset($data->adaptado)) {
                        $adaptado = $data->adaptado;
                        $sql = $conn->prepare("SELECT DISTINCT(fr.id), ST_AsGeoJSON(fr.geom) AS geojson, fr.nome, fr.municipio, fr.tipo
                        FROM freguesias_bv f, filtros_representacao fr
                        WHERE ST_Contains (f.geom , fr.geom)
                        AND f.freguesia = :freg AND fr.modalidade = :mod AND fr.escalao_equipa = :escalao AND fr.genero_equipa = :genero AND fr.adaptado_equipa = :adaptado");
                        $sql->bindParam(':freg', $freg);
                        $sql->bindParam(':mod', $mod);
                        $sql->bindParam(':escalao', $escalao);
                        $sql->bindParam(':genero', $genero);
                        $sql->bindParam(':adaptado', $adaptado);
                    } else {
                        $sql = $conn->prepare("SELECT DISTINCT(fr.id), ST_AsGeoJSON(fr.geom) AS geojson, fr.nome, fr.municipio, fr.tipo
                        FROM freguesias_bv f, filtros_representacao fr
                        WHERE ST_Contains (f.geom , fr.geom)
                        AND f.freguesia = :freg AND fr.modalidade = :mod AND fr.escalao_equipa = :escalao AND fr.genero_equipa = :genero");
                        $sql->bindParam(':freg', $freg);
                        $sql->bindParam(':mod', $mod);
                        $sql->bindParam(':escalao', $escalao);
                        $sql->bindParam(':genero', $genero);
                    }
                } else if (isset($data->adaptado)) {
                    $adaptado = $data->adaptado;
                    $sql = $conn->prepare("SELECT DISTINCT(fr.id), ST_AsGeoJSON(fr.geom) AS geojson, fr.nome, fr.municipio, fr.tipo
                    FROM freguesias_bv f, filtros_representacao fr
                    WHERE ST_Contains (f.geom , fr.geom)
                    AND f.freguesia = :freg AND fr.modalidade = :mod AND fr.escalao_equipa = :escalao AND fr.adaptado_equipa = :adaptado");
                    $sql->bindParam(':freg', $freg);
                    $sql->bindParam(':mod', $mod);
                    $sql->bindParam(':escalao', $escalao);
                    $sql->bindParam(':adaptado', $adaptado);
                } else {
                    $sql = $conn->prepare("SELECT DISTINCT(fr.id), ST_AsGeoJSON(fr.geom) AS geojson, fr.nome, fr.municipio, fr.tipo
                    FROM freguesias_bv f, filtros_representacao fr
                    WHERE ST_Contains (f.geom , fr.geom)
                    AND f.freguesia = :freg AND fr.modalidade = :mod AND fr.escalao_equipa = :escalao");
                    $sql->bindParam(':freg', $freg);
                    $sql->bindParam(':mod', $mod);
                    $sql->bindParam(':escalao', $escalao);
                }
            } else if (isset($data->genero)) {
                $genero = $data->genero;
                if (isset($data->adaptado)) {
                    $adaptado = $data->adaptado;
                    $sql = $conn->prepare("SELECT DISTINCT(fr.id), ST_AsGeoJSON(fr.geom) AS geojson, fr.nome, fr.municipio, fr.tipo
                    FROM freguesias_bv f, filtros_representacao fr
                    WHERE ST_Contains (f.geom , fr.geom)
                    AND f.freguesia = :freg AND fr.modalidade = :mod AND fr.genero_equipa = :genero AND fr.adaptado_equipa = :adaptado");
                    $sql->bindParam(':freg', $freg);
                    $sql->bindParam(':mod', $mod);
                    $sql->bindParam(':genero', $genero);
                    $sql->bindParam(':adaptado', $adaptado);
                } else {
                    $sql = $conn->prepare("SELECT DISTINCT(fr.id), ST_AsGeoJSON(fr.geom) AS geojson, fr.nome, fr.municipio, fr.tipo
                    FROM freguesias_bv f, filtros_representacao fr
                    WHERE ST_Contains (f.geom , fr.geom)
                    AND f.freguesia = :freg AND fr.modalidade = :mod AND fr.genero_equipa = :genero");
                    $sql->bindParam(':freg', $freg);
                    $sql->bindParam(':mod', $mod);
                    $sql->bindParam(':genero', $genero);
                }
            } else if (isset($data->adaptado)) {
                $adaptado = $data->adaptado;
                $sql = $conn->prepare("SELECT DISTINCT(fr.id), ST_AsGeoJSON(fr.geom) AS geojson, fr.nome, fr.municipio, fr.tipo
                FROM freguesias_bv f, filtros_representacao fr
                WHERE ST_Contains (f.geom , fr.geom)
                AND f.freguesia = :freg AND fr.modalidade = :mod AND fr.adaptado_equipa = :adaptado");
                $sql->bindParam(':freg', $freg);
                $sql->bindParam(':mod', $mod);
                $sql->bindParam(':adaptado', $adaptado);
            } else {
                $sql = $conn->prepare("SELECT DISTINCT(fr.id), ST_AsGeoJSON(fr.geom) AS geojson, fr.nome, fr.municipio, fr.tipo
                FROM freguesias_bv f, filtros_representacao fr
                WHERE ST_Contains (f.geom , fr.geom)
                AND f.freguesia = :freg AND fr.modalidade = :mod");
                $sql->bindParam(':freg', $freg);
                $sql->bindParam(':mod', $mod);
            }
        } else if (isset($data->genero)) {
            $genero = $data->genero;
            if (isset($data->adaptado)) {
                $adaptado = $data->adaptado;
                $sql = $conn->prepare("SELECT DISTINCT(fr.id), ST_AsGeoJSON(fr.geom) AS geojson, fr.nome, fr.municipio, fr.tipo
                FROM freguesias_bv f, filtros_representacao fr
                WHERE ST_Contains (f.geom , fr.geom)
                AND f.freguesia = :freg AND fr.genero_equipa = :genero AND fr.adaptado_equipa = :adaptado");
                $sql->bindParam(':freg', $freg);
                $sql->bindParam(':genero', $genero);
                $sql->bindParam(':adaptado', $adaptado);
            } else {
                $sql = $conn->prepare("SELECT DISTINCT(fr.id), ST_AsGeoJSON(fr.geom) AS geojson, fr.nome, fr.municipio, fr.tipo
                FROM freguesias_bv f, filtros_representacao fr
                WHERE ST_Contains (f.geom , fr.geom)
                AND f.freguesia = :freg AND fr.genero_equipa = :genero");
                $sql->bindParam(':freg', $freg);
                $sql->bindParam(':genero', $genero);
            }
        } else if (isset($data->adaptado)) {
            $adaptado = $data->adaptado;
            $sql = $conn->prepare("SELECT DISTINCT(fr.id), ST_AsGeoJSON(fr.geom) AS geojson, fr.nome, fr.municipio, fr.tipo
            FROM freguesias_bv f, filtros_representacao fr
            WHERE ST_Contains (f.geom , fr.geom)
            AND f.freguesia = :freg AND fr.adaptado_equipa = :adaptado");
            $sql->bindParam(':freg', $freg);
            $sql->bindParam(':adaptado', $adaptado);
        } else {
            $sql = $conn->prepare("SELECT DISTINCT(fr.id), ST_AsGeoJSON(fr.geom) AS geojson, fr.nome, fr.municipio, fr.tipo
            FROM freguesias_bv f, filtros_representacao fr
            WHERE ST_Contains (f.geom , fr.geom)
            AND f.freguesia = :freg");
            $sql->bindParam(':freg', $freg);
        }
        $polygon = $conn->prepare("SELECT ST_AsGeoJSON(geom) AS geojson
        FROM freguesias_bv WHERE freguesia = :freg");
        $polygon->bindParam(':freg', $freg);

        $polygon->execute();

        $result = $polygon->setFetchMode(PDO::FETCH_ASSOC);

        foreach ($polygon->fetchAll() as $row => $d)
            $output = '{
        "type": "FeatureCollection",
        "features": [' . $d["geojson"] . ']
      }';

        $json['freguesia'] = $output;

        $output = '';
    } else {
        if (isset($data->modalidade)) {
            $mod = $data->modalidade;
            if (isset($data->escalao)) {
                $escalao = $data->escalao;
                if (isset($data->genero)) {
                    $genero = $data->genero;
                    if (isset($data->adaptado)) {
                        $adaptado = $data->adaptado;
                        $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                        FROM filtros_representacao
                        WHERE modalidade = :mod AND escalao_equipa = :escalao AND genero_equipa = :genero AND adaptado_equipa = :adaptado");
                        $sql->bindParam(':mod', $mod);
                        $sql->bindParam(':escalao', $escalao);
                        $sql->bindParam(':genero', $genero);
                        $sql->bindParam(':adaptado', $adaptado);
                    } else {
                        $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                        FROM filtros_representacao
                        WHERE modalidade = :mod AND escalao_equipa = :escalao AND genero_equipa = :genero");
                        $sql->bindParam(':mod', $mod);
                        $sql->bindParam(':escalao', $escalao);
                        $sql->bindParam(':genero', $genero);
                    }
                } else if (isset($data->adaptado)) {
                    $adaptado = $data->adaptado;
                    $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                    FROM filtros_representacao
                    WHERE modalidade = :mod AND escalao_equipa = :escalao AND adaptado_equipa = :adaptado");
                    $sql->bindParam(':mod', $mod);
                    $sql->bindParam(':escalao', $escalao);
                    $sql->bindParam(':adaptado', $adaptado);
                } else {
                    $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                    FROM filtros_representacao
                    WHERE modalidade = :mod AND escalao_equipa = :escalao");
                    $sql->bindParam(':mod', $mod);
                    $sql->bindParam(':escalao', $escalao);
                }
            } else if (isset($data->genero)) {
                $genero = $data->genero;
                if (isset($data->adaptado)) {
                    $adaptado = $data->adaptado;
                    $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                    FROM filtros_representacao
                    WHERE modalidade = :mod AND genero_equipa = :genero AND adaptado_equipa = :adaptado");
                    $sql->bindParam(':mod', $mod);
                    $sql->bindParam(':genero', $genero);
                    $sql->bindParam(':adaptado', $adaptado);
                } else {
                    $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                    FROM filtros_representacao
                    WHERE modalidade = :mod AND genero_equipa = :genero");
                    $sql->bindParam(':mod', $mod);
                    $sql->bindParam(':genero', $genero);
                }
            } else if (isset($data->adaptado)) {
                $adaptado = $data->adaptado;
                $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                FROM filtros_representacao
                WHERE modalidade = :mod AND adaptado_equipa = :adaptado");
                $sql->bindParam(':mod', $mod);
                $sql->bindParam(':adaptado', $adaptado);
            } else {
                $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                FROM filtros_representacao
                WHERE modalidade = :mod");
                $sql->bindParam(':mod', $mod);
            }
        } else if (isset($data->genero)) {
            $genero = $data->genero;
            if (isset($data->adaptado)) {
                $adaptado = $data->adaptado;
                $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                FROM filtros_representacao
                WHERE genero_equipa = :genero AND adaptado_equipa = :adaptado");
                $sql->bindParam(':genero', $genero);
                $sql->bindParam(':adaptado', $adaptado);
            } else {
                $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
                FROM filtros_representacao
                WHERE genero_equipa = :genero");
                $sql->bindParam(':genero', $genero);
            }
        } else if (isset($data->adaptado)) {
            $adaptado = $data->adaptado;
            $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
            FROM filtros_representacao
            WHERE adaptado_equipa = :adaptado");
            $sql->bindParam(':adaptado', $adaptado);
        } else
            $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, municipio, tipo
      FROM filtros_representacao");
    }

    $sql->execute();

    $result = $sql->setFetchMode(PDO::FETCH_ASSOC);

    foreach ($sql->fetchAll() as $row => $d) {
        $rowOutput = (strlen($rowOutput) > 0 ? ',' : '') .
            '{"type": "Feature", "geometry": ' . $d['geojson'] .
            ', "properties": {"nome": "' . $d['nome'] . '", "municipio": "' .
            $d['municipio'] . '", "tipo": "' . $d['tipo'] . '"}}';

        $output .= $rowOutput;
    }


    $json['rep'] = '{ "type": "FeatureCollection", "features": [ ' . $output . ' ]}';
    $json['error'] = 'none';
    echo json_encode($json);
} catch (Exception $e) {
    $json['error'] = 'exception';
    echo json_encode($json);
}
