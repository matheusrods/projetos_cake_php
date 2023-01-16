<?php
    
    $diretorioArquivoProcesso = $_SERVER['DOCUMENT_ROOT'].'/imagens_viagens/';

    if ( !empty($_FILES) ) 
    {
        conexao();

        $codigo_sm = $_GET['processo'];
        $tempFile     = $_FILES['Filedata']['tmp_name'];          
        $ext          = end(explode('.', $_FILES['Filedata']['name']));
        $newName      = md5($_FILES['Filedata']['name']).'-'.$codigo_sm.'-'.time().'.'.$ext;
        $newName      = strtolower($newName);
        $targetFile   = $diretorioArquivoProcesso.$newName;
        $targetThumbFile   = $diretorioArquivoProcesso.'thumbs/'.$newName;
        $status       = 0;
        
        // Validate the file type
        $fileTypes = array('jpg','jpeg','png','gif','pdf'); // File extensions
        $fileParts = pathinfo($_FILES['Filedata']['name']);
        
        if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
            
            try{                
                mssql_query("begin transaction");

                if( !inserirImagemProcesso($codigo_sm,$ano_processo,$newName,$status) )
                    throw new Exception("Erro no cadastro de upload!");
                    
                if( !move_uploaded_file($tempFile,$targetFile) )
                    throw new Exception("Erro no upload da imagem!");

                criarThumb($targetFile,$targetThumbFile);

                mssql_query("commit");
                echo '1';

               

            }catch(Exception $e){
                echo $e->getMessage();
                mssql_query("rollback");
            }
            
        } else {
            echo 'Invalid file type.';
        }
    }

    function conexao()
    {       
        if( file_exists('.production') )
            $conn = mssql_connect( 'sqlprod', 'app_monitora', '@app18' );
        else
            $conn = mssql_connect( 'homosql', 'sqldba', 'buonny1818' );
        
        if( !mssql_select_db( 'dbBuonny', $conn ) ) 
            exit();
    }

    function inserirImagemProcesso($num_processo,$ano_processo,$arquivo,$status)
    {
        $campos = "num_processo, ano_processo, arquivo, visivel";
        $sql    = "INSERT INTO Erp_Buonny.dbo.SIN_Sinistro_Comunica_Arquivo ({$campos}) VALUES({$num_processo},{$ano_processo},'{$arquivo}',{$status})";
        $res    = mssql_query($sql);
        return $res;
    }

    function criarThumb($origem,$destino){
        // File and new size
        $tipoarquivo   = strtolower(substr($origem,-3));
        if($tipoarquivo == "jpg" ||$tipoarquivo == "png" || $tipoarquivo == "gif"){
            $largura_max    = 158;
            $altura_max     = 108;

            // Get new sizes
            list($largura, $altura) = getimagesize($origem);

            // Calcula a altura e altura para manter a proporção
            if($largura > $altura){ 
                $largura_d = $largura_max;
                $altura_d = ($largura_d*$altura)/$largura; 
            }elseif($largura !=0 && $altura !=0){
                $altura_d = $altura_max;
                $largura_d = ($altura_d*$largura)/$altura; 
            }else{
                $altura_d = 0;
                $largura_d = 0;
            }

            // Load
            $thumb = imagecreatetruecolor($largura_d, $altura_d);
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);

            switch(strtolower(substr($origem, -3))){
                case "jpg":
                    $image = imagecreatefromjpeg($origem);    
                break;
                case "png":
                    $image = imagecreatefrompng($origem);
                break;
                case "gif":
                    $image = imagecreatefromgif($origem);
                break;
                default:
                    exit;
                break;
            }

            // Resize
            $bool = imagecopyresized($thumb,$image, 0, 0, 0, 0, $largura_d, $altura_d, $largura, $altura);

            if($bool){
                
                 
                switch(strtolower(substr($origem, -3))){
                    case "jpg":
                        header("Content-Type: image/jpeg");
                        imagejpeg($thumb,$destino,80);

                    break;
                    case "png":
                       header("Content-Type: image/png");
                        imagepng($thumb,$destino);
                    break;
                    case "gif":
                        header("Content-Type: image/gif");
                        imagegif($thumb,$destino);
                    break;
                }
            }
        }    
    }

?>