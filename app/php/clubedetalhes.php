
<?php


try {
    require "connect.php";

    $local = filter_input(INPUT_POST, "nomeM");

    $sql = $conn->prepare("SELECT nome, escalao_equipa as escalao, genero_equipa as genero, adaptado_equipa as adaptado, modalidade FROM filtros_representacao WHERE nome = :local");
    $sql->bindParam(":local", $local);
    $sql->execute();


    $json;

    $result = $sql->setFetchMode(PDO::FETCH_ASSOC);

    foreach ($sql->fetchAll() as $row => $d){

        $json[] = array(
            "nome"=> $d["nome"], "escalao"=> $d["escalao"], "genero"=> $d["genero"], "adaptado"=> $d["adaptado"], "modalidade"=> $d["modalidade"]
        );

    }

    $json = array("pesquisa"=> $json);


    echo json_encode($json);


} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}




?>