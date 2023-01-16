<?php

   $nome = $arquivo;
   $arquivo = APP.'webroot'.DS.'documentos'.DS.$nome;

   if(isset($arquivo) && file_exists($arquivo))
   { /* faz o teste se a variavel não esta vazia e se o arquivo realmente existe
      */
      if(isset($arquivo) && file_exists($arquivo))
      { // faz o teste se a variavel não esta vazia e se o arquivo realmente existe
         switch(strtolower(substr(strrchr(basename($arquivo),"."),1)))
         { // verifica a extensão do arquivo para pegar o tipo
             case "txt": $tipo="application/text"; break;
            case "pdf": $tipo="application/pdf"; break;
            case "exe": $tipo="application/octet-stream"; break;
            case "zip": $tipo="application/zip"; break;
            case "doc": $tipo="application/msword"; break;
            case "xls": $tipo="application/vnd.ms-excel"; break;
            case "ppt": $tipo="application/vnd.ms-powerpoint"; break;
            case "gif": $tipo="webroot/documentos/gif"; break;
            case "png": $tipo="webroot/documentos/png"; break;
            case "jpg": $tipo="webroot/documentos/jpg"; break;
            case "mp3": $tipo="webroot/documentos/mpeg"; break;
            
         }

         header("Content-Type:".$tipo); // informa o tipo do arquivo ao navegador
         header("Content-Length: ".filesize($arquivo)); // informa o tamanho do arquivo ao navegador
         header("Content-Disposition: attachment; filename=".basename($nome)); // informa ao navegador que é tipo anexo e faz abrir a janela de download, tambem informa o nome do arquivo
         readfile($arquivo); // lê o arquivo
         
         exit; // aborta pós-ações
      } 
      else 
      {
       echo 'Arquivo inexistente';
      } 
   } 

        
?>
