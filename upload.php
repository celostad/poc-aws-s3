<?php

require 'vendor/autoload.php';
require 'config.php';

if (!class_exists('S3')) require_once 'S3.php';

// Check for CURL
if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll'))
    exit("\nERROR: CURL extension not loaded\n\n");

// Pointless without your keys!
if (awsAccessKey == 'change-this' || awsSecretKey == 'change-this')
    exit("\nERROR: AWS access information required\n\nPlease edit the following lines in this file:\n\n" .
        "define('awsAccessKey', 'change-me');\ndefine('awsSecretKey', 'change-me');\n\n");


S3::setAuth(awsAccessKey, awsSecretKey);

$lifetime = 3600; // Period for which the parameters are valid
$maxFileSize = (1024 * 1024 * 100); // 100 MB

$metaHeaders = array('uid' => 123);
$requestHeaders = array(
    'Content-Type' => 'application/octet-stream',
    'Content-Disposition' => 'attachment; filename=${filename}'
);

$params = S3::getHttpUploadPostParams(
    $bucket,
    $path,
    S3::ACL_PUBLIC_READ,
    $lifetime,
    $maxFileSize,
    201, // Or a URL to redirect to on success
    $metaHeaders,
    $requestHeaders,
    false // False since we're not using flash
);

if ($_FILES) {

    try {
        $keyname = $_FILES['file']['name'];
        $filepath = $_FILES['file']['tmp_name'];
        // Upload a file.
        $result = $s3->putObject(array(
            'Bucket'       => $bucket,
            'Key'          => $keyname,
            'SourceFile'   => $filepath,
            'ContentType'  => 'text/plain',
            'ACL'          => 'public-read',
            'StorageClass' => 'STANDARD'
        ));

        
        echo "<script>alert('Arquivo carregado com sucesso!');</script>";
        echo "<script>window.location.href='index.php';</script>";

    } catch (Exception $e) {
        throw "Erro" . $e;
    }
}
?>