<?php

require 'vendor/autoload.php';
require 'config.php';

if(isset($_REQUEST['key'])){

    try {
        $s3->deleteObject(array(
            'Bucket' => $bucket,
            'Key'    => $_REQUEST['key']
        ));
    
        echo "<script>alert('Excluido com sucesso!');</script>";
        echo "<script>window.location.href='index.php';</script>";

    } catch (Exception $e) {
        throw "Erro" . $e;
    }

}

?>
