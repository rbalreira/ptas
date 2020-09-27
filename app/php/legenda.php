<?php


    try {
        require "connect.php";

        $local = filter_input(INPUT_POST, "nomeP");

        $sql = $conn->prepare("SELECT equipamentos_desportivos.nome,equipamentos_desportivos.imagem, equipamentos_desportivos.morada, tipologia.nome AS tipologia, equipamentos_desportivos.cod_postal, equipamentos_desportivos.freguesia, equipamentos_desportivos.contacto, equipamentos_desportivos.h_abertura, equipamentos_desportivos.h_fecho, equipamentos_desportivos.preco
        FROM equipamentos_desportivos
        INNER JOIN tipologia ON equipamentos_desportivos.tipologia = tipologia.id
        WHERE equipamentos_desportivos.nome = :local");
        $sql->bindParam(":local", $local);
        $sql->execute();
    

        $nomeE = array();
        $imagemE = array();
        $moradaE = array();
        $tipologiaE = array();
        $codPostalE = array();
        $freguesiaE = array();
        $contactoE = array();
        $hAberturaE = array();
        $hFechoE = array();
        $precoE = array();

        $result = $sql->setFetchMode(PDO::FETCH_ASSOC);

        foreach ($sql->fetchAll() as $row => $d)
            $nomeE[] = ($d["nome"]);
            $imagemE[] = ($d["imagem"]);
            $moradaE[] = ($d["morada"]);
            $tipologiaE[] = ($d["tipologia"]);
            $codPostalE[] = ($d["cod_postal"]);
            $freguesiaE[] = ($d["freguesia"]);
            $contactoE[] = ($d["contacto"]);
            $hAberturaE[] = ($d["h_abertura"]);
            $hFechoE[] = ($d["h_fecho"]);
            $precoE[] = ($d["preco"]);

        echo json_encode(array("nomeE" => $nomeE, "imagemE" => $imagemE, "moradaE" => $moradaE, "tipologiaE" => $tipologiaE, "codPostalE" => $codPostalE, "freguesiaE" => $freguesiaE, "contactoE" => $contactoE, "hAberturaE" => $hAberturaE, "hFechoE" => $hFechoE, "precoE" => $precoE));
        
    
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    
    

?>