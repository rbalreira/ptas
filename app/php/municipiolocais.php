<?php


    try {
        require "connect.php";

        $local = filter_input(INPUT_POST, "nomeP");

        $sql = $conn->prepare("SELECT DISTINCT(id), nome, tipo, municipio FROM filtros_representacao
        WHERE nome = :local");
        $sql->bindParam(":local", $local);
        $sql->execute();
    

        $nomeE = array();
        $tipologiaE = array();
        $municipioE = array();

        $result = $sql->setFetchMode(PDO::FETCH_ASSOC);

        foreach ($sql->fetchAll() as $row => $d)
            $nomeE[] = ($d["nome"]);
            $tipologiaE[] = ($d["tipo"]);
            $municipioE[] = ($d["municipio"]);

        echo json_encode(array("nomeE" => $nomeE, "tipologiaE" => $tipologiaE, "municipioE" => $municipioE));
        
    
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    
    

?>