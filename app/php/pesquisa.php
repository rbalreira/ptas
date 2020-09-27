

<?php


    try {
        require "connect.php";

        $string = filter_input(INPUT_POST, "local");
        
        $len = strlen($string);
        $first = substr_replace($string, '%', 0,0); // insert % before the first character
        $local = substr_replace($first, '%', $len+1,0); // insert % after the last one

        $sql = $conn->prepare("SELECT DISTINCT(nome), (morada), (imagem) FROM equipamentos_desportivos WHERE (nome) ILIKE :local");
        $sql->bindParam(":local", $local);
        $sql->execute();
    
        
        $json;

        $result = $sql->setFetchMode(PDO::FETCH_ASSOC);

        foreach ($sql->fetchAll() as $row => $d){

            $json[] = array(
                "nome"=> $d["nome"], "morada"=> $d["morada"], "imagem"=> $d["imagem"]
            );

        }

        $json = array("pesquisa"=> $json);

        echo json_encode($json);
        

    
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    
    

?>