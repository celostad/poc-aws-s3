<?php

require 'vendor/autoload.php';
require 'config.php';

if (!class_exists('S3')) require_once 'S3.php';

// Check for CURL
if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll'))
    exit("\nERROR: CURL extension not loaded\n\n");

S3::setAuth(ACCESS_KEY, SECRET_KEY);

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
        
        $s3 = new Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => $region,
            'credentials' => array(
                'key' => ACCESS_KEY,
                'secret' => SECRET_KEY
            )
        ]);

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

        echo "<script>window.location.href='index.php?success=true';</script>";

    } catch (Exception $e) {
        throw "Erro" . $e;
    }
}
?>