<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POC - AWS S3</title>
</head>

<body>

    <?php
    require 'vendor/autoload.php';
    require 'config.php';

    $arrArquivos = $s3->ListObjects(['Bucket' => $bucket, 'Delimiter' => '/', 'Prefix' => $prefix]);

    ?>
    <div style="margin-top:50px;"></div>
    <table style="margin:0 auto;border: 1px solid #eee;">
    <tr style="border: 1px solid #eee;font-weight: bolder; text-align:center;">
    <td colspan="5" style="height:60px;">
    <form method="post" action="upload.php" enctype="multipart/form-data"> 
    <label for="files">Selecione o arquivo</label>         
        <input type="file" id="files" name="file" style="display:none"/>&#160;<input type="submit" value="Upload" />
    </form>
    </td>
    </tr>
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

        if (count($arrArquivos) > 0) {
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
                echo '<td style="border: 1px solid #eee;text-align: left;"><a href="' . $urlPathFile . '">' . $strFile . '</a></td>';
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
    </table>

</body>

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