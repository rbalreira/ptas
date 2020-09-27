<?php
try {
  header('content-type: application/json; charset=utf-8');

  require "connect.php";

  // separa os valores do preço nas opções
  function splitPreco($num)
  {
    if ($num{
      strlen($num) - 1} === '+') return array(explode("+", $num)[0]);
    else {
      $num = explode(" ", $num);
      return array(0 => $num[0], 1 => $num[2]);
    }
  }

  $data = json_decode($_POST['myData']);

  $sql = $polygon = $output = $rowOutput = $result = '';
  $json;
  $begin;
  $end;

  if (isset($data->preco)) {
    $preco = $data->preco;
    $begin = splitPreco($preco)[0];
    if (isset(splitPreco($preco)[1])) $end = splitPreco($preco)[1];
  }

  if (isset($data->municipio)) {
    $mun = $data->municipio;
    if (isset($data->modalidade)) {
      $mod = $data->modalidade;
      if (isset($data->coberto)) {
        $coberto = $data->coberto;
        if (isset($data->preco)) {
          if (isset($end)) {
            $sql = $conn->prepare("SELECT DISTINCT(ed.id), ST_AsGeoJSON(ed.geom) AS geojson, ed.nome, ed.freguesia, ed.tipologia
              FROM municipios_bv m, filtros_eq_desportivos ed
              WHERE ST_Contains (m.geom , ed.geom)
              AND (m.municipio = :mun AND ed.modalidade = :mod AND ed.coberto = :coberto AND (ed.preco BETWEEN :b AND :e))");
            $sql->bindParam(':e', $end);
          } else
            $sql = $conn->prepare("SELECT DISTINCT(ed.id), ST_AsGeoJSON(ed.geom) AS geojson, ed.nome, ed.freguesia, ed.tipologia
              FROM municipios_bv m, filtros_eq_desportivos ed
              WHERE ST_Contains (m.geom , ed.geom)
              AND (m.municipio = :mun AND ed.modalidade = :mod AND ed.coberto = :coberto AND ed.preco = :b)");
          $sql->bindParam(':mun', $mun);
          $sql->bindParam(':mod', $mod);
          $sql->bindParam(':coberto', $coberto);
          $sql->bindParam(':b', $begin);
        } else {
          $sql = $conn->prepare("SELECT DISTINCT(ed.id), ST_AsGeoJSON(ed.geom) AS geojson, ed.nome, ed.freguesia, ed.tipologia
            FROM municipios_bv m, filtros_eq_desportivos ed
            WHERE ST_Contains (m.geom , ed.geom)
            AND (m.municipio = :mun AND ed.modalidade = :mod AND ed.coberto = :coberto)");
          $sql->bindParam(':mun', $mun);
          $sql->bindParam(':mod', $mod);
          $sql->bindParam(':coberto', $coberto);
        }
      } else if (isset($data->preco)) {
        if (isset($end)) {
          $sql = $conn->prepare("SELECT DISTINCT(ed.id), ST_AsGeoJSON(ed.geom) AS geojson, ed.nome, ed.freguesia, ed.tipologia
            FROM municipios_bv m, filtros_eq_desportivos ed
            WHERE ST_Contains (m.geom , ed.geom)
            AND (m.municipio = :mun AND ed.modalidade = :mod AND (ed.preco BETWEEN :b AND :e))");
          $sql->bindParam(':e', $end);
        } else
          $sql = $conn->prepare("SELECT DISTINCT(ed.id), ST_AsGeoJSON(ed.geom) AS geojson, ed.nome, ed.freguesia, ed.tipologia
            FROM municipios_bv m, filtros_eq_desportivos ed
            WHERE ST_Contains (m.geom , ed.geom)
            AND (m.municipio = :mun AND ed.modalidade = :mod AND ed.preco = :b)");
        $sql->bindParam(':mun', $mun);
        $sql->bindParam(':mod', $mod);
        $sql->bindParam(':b', $begin);
      } else {
        $sql = $conn->prepare("SELECT DISTINCT(ed.id), ST_AsGeoJSON(ed.geom) AS geojson, ed.nome, ed.freguesia, ed.tipologia
          FROM municipios_bv m, filtros_eq_desportivos ed
          WHERE ST_Contains (m.geom , ed.geom)
          AND (m.municipio = :mun AND ed.modalidade = :mod)");
        $sql->bindParam(':mun', $mun);
        $sql->bindParam(':mod', $mod);
      }
    } else if (isset($data->coberto)) {
      $coberto = $data->coberto;
      if (isset($data->preco)) {
        if (isset($end)) {
          $sql = $conn->prepare("SELECT DISTINCT(ed.id), ST_AsGeoJSON(ed.geom) AS geojson, ed.nome, ed.freguesia, ed.tipologia
            FROM municipios_bv m, filtros_eq_desportivos ed
            WHERE ST_Contains (m.geom , ed.geom)
            AND (m.municipio = :mun AND ed.coberto = :coberto AND (ed.preco BETWEEN :b AND :e))");
          $sql->bindParam(':e', $end);
        } else
          $sql = $conn->prepare("SELECT DISTINCT(ed.id), ST_AsGeoJSON(ed.geom) AS geojson, ed.nome, ed.freguesia, ed.tipologia
          FROM municipios_bv m, filtros_eq_desportivos ed
          WHERE ST_Contains (m.geom , ed.geom)
          AND (m.municipio = :mun AND ed.coberto = :coberto AND ed.preco = :b)");

        $sql->bindParam(':mun', $mun);
        $sql->bindParam(':coberto', $coberto);
        $sql->bindParam(':b', $begin);
      } else {
        $sql = $conn->prepare("SELECT DISTINCT(ed.id), ST_AsGeoJSON(ed.geom) AS geojson, ed.nome, ed.freguesia, ed.tipologia
          FROM municipios_bv m, filtros_eq_desportivos ed
          WHERE ST_Contains (m.geom , ed.geom)
          AND (m.municipio = :mun AND ed.coberto = :coberto)");
        $sql->bindParam(':mun', $mun);
        $sql->bindParam(':coberto', $coberto);
      }
    } else if (isset($data->preco)) {
      if (isset($end)) {
        $sql = $conn->prepare("SELECT DISTINCT(ed.id), ST_AsGeoJSON(ed.geom) AS geojson, ed.nome, ed.freguesia, ed.tipologia
            FROM municipios_bv m, filtros_eq_desportivos ed
            WHERE ST_Contains (m.geom , ed.geom)
            AND (m.municipio = :mun AND (ed.preco BETWEEN :b AND :e))");
        $sql->bindParam(':e', $end);
      } else
        $sql = $conn->prepare("SELECT DISTINCT(ed.id), ST_AsGeoJSON(ed.geom) AS geojson, ed.nome, ed.freguesia, ed.tipologia
        FROM municipios_bv m, filtros_eq_desportivos ed
        WHERE ST_Contains (m.geom , ed.geom)
        AND (m.municipio = :mun AND ed.preco = :b)");
      $sql->bindParam(':mun', $mun);
      $sql->bindParam(':b', $begin);
    } else {
      $sql = $conn->prepare("SELECT DISTINCT(ed.id), ST_AsGeoJSON(ed.geom) AS geojson, ed.nome, ed.freguesia, ed.tipologia
            FROM municipios_bv m, filtros_eq_desportivos ed
            WHERE ST_Contains (m.geom , ed.geom)
            AND (m.municipio = :mun)");
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
      if (isset($data->coberto)) {
        $coberto = $data->coberto;
        if (isset($data->preco)) {
          if (isset($end)) {
            $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
              FROM filtros_eq_desportivos
              WHERE freguesia = :freg AND modalidade = :mod AND coberto = :coberto AND (preco BETWEEN :b AND :e)");
            $sql->bindParam(':e', $end);
          } else
            $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
              FROM filtros_eq_desportivos
              WHERE freguesia = :freg AND modalidade = :mod AND coberto = :coberto AND preco = :b");
          $sql->bindParam(':freg', $freg);
          $sql->bindParam(':mod', $mod);
          $sql->bindParam(':coberto', $coberto);
          $sql->bindParam(':b', $begin);
        } else {
          $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
            FROM filtros_eq_desportivos
            WHERE freguesia = :freg AND modalidade = :mod AND coberto = :coberto");
          $sql->bindParam(':freg', $freg);
          $sql->bindParam(':mod', $mod);
          $sql->bindParam(':coberto', $coberto);
        }
      } else if (isset($data->preco)) {
        if (isset($end)) {
          $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
            FROM filtros_eq_desportivos
            WHERE freguesia = :freg AND modalidade = :mod AND (preco BETWEEN :b AND :e)");
          $sql->bindParam(':e', $end);
        } else
          $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
            FROM filtros_eq_desportivos
            WHERE freguesia = :freg AND modalidade = :mod AND preco = :b");
        $sql->bindParam(':freg', $freg);
        $sql->bindParam(':mod', $mod);
        $sql->bindParam(':b', $begin);
      } else {
        $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
          FROM filtros_eq_desportivos
          WHERE freguesia = :freg AND modalidade = :mod");
        $sql->bindParam(':freg', $freg);
        $sql->bindParam(':mod', $mod);
      }
    } else if (isset($data->coberto)) {
      $coberto = $data->coberto;
      if (isset($data->preco)) {
        if (isset($end)) {
          $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
            FROM filtros_eq_desportivos
            WHERE freguesia = :freg AND coberto = :coberto AND (preco BETWEEN :b AND :e)");
          $sql->bindParam(':e', $end);
        } else
          $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
          FROM filtros_eq_desportivos
          WHERE freguesia = :freg AND coberto = :coberto AND preco = :b");
        $sql->bindParam(':freg', $freg);
        $sql->bindParam(':coberto', $coberto);
        $sql->bindParam(':b', $begin);
      } else {
        $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
          FROM filtros_eq_desportivos
          WHERE freguesia = :freg AND coberto = :coberto");
        $sql->bindParam(':freg', $freg);
        $sql->bindParam(':coberto', $coberto);
      }
    } else if (isset($data->preco)) {
      if (isset($end)) {
        $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
            FROM filtros_eq_desportivos
            WHERE freguesia = :freg AND (preco BETWEEN :b AND :e)");
        $sql->bindParam(':e', $end);
      } else
        $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
        FROM filtros_eq_desportivos
        WHERE freguesia = :freg AND preco = :b");
      $sql->bindParam(':freg', $freg);
      $sql->bindParam(':b', $begin);
    } else {
      $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
            FROM filtros_eq_desportivos
            WHERE freguesia = :freg");
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
      if (isset($data->coberto)) {
        $coberto = $data->coberto;
        if (isset($data->preco)) {
          if (isset($end)) {
            $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
              FROM filtros_eq_desportivos
              WHERE modalidade = :mod AND coberto = :coberto AND (preco BETWEEN :b AND :e)");
            $sql->bindParam(':e', $end);
          } else
            $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
              FROM filtros_eq_desportivos
              WHERE modalidade = :mod AND coberto = :coberto AND preco = :b");
          $sql->bindParam(':mod', $mod);
          $sql->bindParam(':coberto', $coberto);
          $sql->bindParam(':b', $begin);
        } else {
          $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
            FROM filtros_eq_desportivos
            WHERE modalidade = :mod AND coberto = :coberto");
          $sql->bindParam(':mod', $mod);
          $sql->bindParam(':coberto', $coberto);
        }
      } else if (isset($data->preco)) {
        if (isset($end)) {
          $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
            FROM filtros_eq_desportivos
            WHERE modalidade = :mod AND (preco BETWEEN :b AND :e)");
          $sql->bindParam(':e', $end);
        } else
          $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
            FROM filtros_eq_desportivos
            WHERE modalidade = :mod AND preco = :b");
        $sql->bindParam(':mod', $mod);
        $sql->bindParam(':b', $begin);
      } else {
        $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
          FROM filtros_eq_desportivos
          WHERE modalidade = :mod");
        $sql->bindParam(':mod', $mod);
      }
    } else if (isset($data->coberto)) {
      $coberto = $data->coberto;
      if (isset($data->preco)) {
        if (isset($end)) {
          $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
            FROM filtros_eq_desportivos
            WHERE coberto = :coberto AND (preco BETWEEN :b AND :e)");
          $sql->bindParam(':e', $end);
        } else
          $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
          FROM filtros_eq_desportivos
          WHERE coberto = :coberto AND preco = :b");
        $sql->bindParam(':coberto', $coberto);
        $sql->bindParam(':b', $begin);
      } else {
        $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
          FROM filtros_eq_desportivos
          WHERE coberto = :coberto");
        $sql->bindParam(':coberto', $coberto);
      }
    } else if (isset($data->preco)) {
      if (isset($end)) {
        $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
            FROM filtros_eq_desportivos
            WHERE preco BETWEEN :b AND :e");
        $sql->bindParam(':e', $end);
      } else
        $sql = $conn->prepare("SELECT DISTINCT(id), ST_AsGeoJSON(geom) AS geojson, nome, freguesia, tipologia
        FROM filtros_eq_desportivos
        WHERE preco = :b");
      $sql->bindParam(':b', $begin);
    } else
      $sql = $conn->prepare("SELECT DISTINCT(id), nome, tipologia, freguesia, ST_AsGeoJSON(geom) AS geojson
      FROM filtros_eq_desportivos");
  }

  $sql->execute();

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
