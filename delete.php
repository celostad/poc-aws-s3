<?php

require 'vendor/autoload.php';
require 'config.php';

if(isset($_REQUEST['key'])){

    try {
        
        $s3 = new Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => $region,
            'credentials' => array(
                'key' => ACCESS_KEY,
                'secret' => SECRET_KEY
            )
        ]);

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
