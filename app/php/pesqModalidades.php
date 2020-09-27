
<?php


try {
    require "connect.php";

    $local = filter_input(INPUT_POST, "nomeM");

    $sql = $conn->prepare("SELECT e.nome AS pavilhao,m.nome as modalidade
    FROM modalidade m
    INNER JOIN modalidade_equipamentos me ON me.modalidadeid = m.id
    INNER JOIN equipamentos_desportivos e ON e.id = me.equipamentosid
    WHERE e.nome = :local");
    $sql->bindParam(":local", $local);
    $sql->execute();


    $json;

    $result = $sql->setFetchMode(PDO::FETCH_ASSOC);

    foreach ($sql->fetchAll() as $row => $d){

        $json[] = array(
            "pavilhao"=> $d["pavilhao"], "modalidade"=> $d["modalidade"]
        );

    }

    $json = array("pesquisa"=> $json);


    echo json_encode($json);


} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}




?>