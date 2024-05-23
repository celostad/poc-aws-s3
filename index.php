<?php
require 'vendor/autoload.php';
require 'config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POC - AWS S3</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</head>

<body>

<?php
    $s3 = new Aws\S3\S3Client([
        'version' => 'latest',
        'region'  => $region,
        'credentials' => array(
            'key' => ACCESS_KEY,
            'secret' => SECRET_KEY
        )
    ]);
    
    $arrArquivos = $s3->ListObjects(['Bucket' => $bucket, 'Delimiter' => '/', 'Prefix' => $prefix]);
    $habDivAlertSucesso = 'display: none;';
    $habDivAlertDelete = 'display: none;';
    if (isset($_REQUEST['success'])) {
        if ($_REQUEST['success'] == true) {
            $habDivAlertSucesso = 'display: block;';
        }
    }
    if (isset($_REQUEST['delete'])) {
        if ($_REQUEST['delete'] == true) {
            $habDivAlertDelete = 'display: block;';
        }
    }
?>

    <div class="alert alert-success alert-dismissible" style="<?php echo $habDivAlertSucesso; ?>">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Enviado!</strong> O arquivo foi carregado com sucesso.
    </div>
    <div class="alert alert-danger alert-dismissible" style="<?php echo $habDivAlertDelete; ?>">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Excluído!</strong> O arquivo foi apagado com sucesso.
    </div>
    <div style="margin-top:50px;"></div>
    <table style="margin:0 auto;border: 1px solid #eee;">
        <tr style="border: 1px solid #eee;font-weight: bolder;">
            <td style="border: 1px solid #eee;width:250px;">Nome</td>
            <td style="border: 1px solid #eee;width:100px;">Tipo</td>
            <td style="border: 1px solid #eee;width:150px">Tamanho</td>
            <td style="border: 1px solid #eee;width:200px">Classe de armazenanmento</td>
            <td style="border: 1px solid #eee;width:100px">Ações</td>
        </tr>

<?php
        function sizeFilter($bytes)
        {
            $label = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
            for ($i = 0; $bytes >= 1024 && $i < (count($label) - 1); $bytes /= 1024, $i++);
            return (round($bytes, 2) . " " . $label[$i]);
        }

        function excluir($region, $bucket, $arquivo)
        {
            $s3 = new Aws\S3\S3Client([
                'version' => 'latest',
                'region'  => $region,
                'credentials' => array(
                    'key' => ACCESS_KEY,
                    'secret' => SECRET_KEY
                )
            ]);

            $result = $s3->deleteObject(array(
                'Bucket' => $bucket,
                'Key'    => $arquivo
            ));
            return $result;
        }

        if (!is_null($arrArquivos['Contents'])) {
            foreach ($arrArquivos['Contents'] as $key => $arquivo) {

                $strFile = $arquivo['Key'];
                $strTamanho = sizeFilter($arquivo['Size']);
                $strTipo = pathinfo($strFile, PATHINFO_EXTENSION);

                switch ($arquivo['StorageClass']) {
                    case 'REDUCED_REDUNDANCY':
                        $strTipoArmazenamento = 'Redundância Reduzida';
                        break;
                    case 'STANDARD':
                        $strTipoArmazenamento = 'Padrão';
                        break;
                }
                $urlPathFile = 'https://' . $bucket . '.s3.amazonaws.com/' . $prefix . $strFile;

                echo '<tr>';
                echo '<td style="border: 1px solid #eee;text-align: left;"><a href="' . $urlPathFile . '" target="_blank">' . $strFile . '</a></td>';
                echo '<td style="border: 1px solid #eee;">' . $strTipo . '</td>';
                echo '<td style="border: 1px solid #eee;">' . $strTamanho . '</td>';
                echo '<td style="border: 1px solid #eee;">' . $strTipoArmazenamento . '</td>';
                echo '<td style="border: 1px solid #eee;">
            <form id="form_' . $key . '" method="post">
                <input type="hidden" name="key" value="' . $strFile . '">
                <input type="button" onclick="confirmaExclusao(' . $key . ')"  value="Delete">
            </form>
        </td>';
            }
        } else {
            echo '<tr style="text-align:center;">';
            echo '<td colspan="5">Não existem registros.</td>';
            echo '</tr>';
        }
?>

        <tr style="border: 1px solid #eee;font-weight: bolder; text-align:center; height:110px;">
            <form method="post" action="upload.php" enctype="multipart/form-data">
                <td colspan="3" style="border: 0px solid #eee;height:60px;text-align:right !important;">
                    <input type="file" id="files" name="file" style="display:inline;" />
                </td>
                <td colspan="2" style="height:60px;text-align:left;">
                    <input type="submit" style="display:inline;" value="Upload" />
                </td>
            </form>
        </tr>

    </table>

</html>

<script>
    function confirmaExclusao(value) {

        var frm = document.getElementById('form_' + value) || null;
        if (confirm('Confirma a exclusão do arquivo?')) {
            // Save it!
            frm.action = 'delete.php';
            frm.submit();
        }
    }
</script>