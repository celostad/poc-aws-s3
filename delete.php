<?php

require 'vendor/autoload.php';
require 'config.php';

if(isset($_REQUEST['key'])){

    try {
        $s3->deleteObject(array(
            'Bucket' => $bucket,
            'Key'    => $_REQUEST['key']
        ));
        
        echo "<script>window.location.href='index.php?delete=true';</script>";

    } catch (Exception $e) {
        throw "Erro" . $e;
    }

}

?>
