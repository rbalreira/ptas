
<?php


try {
    require "connect.php";


    $sql = $conn->prepare("SELECT DISTINCT (nome) FROM equipamentos_desportivos WHERE (nome) ILIKE '%".$_GET['query']."%'");
    $sql->execute();

    $result = $sql->setFetchMode(PDO::FETCH_ASSOC);

    $json = [];
    
    foreach ($sql->fetchAll() as $row => $d){

        $json[] =  $d["nome"];

    }

    echo json_encode($json);


} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>