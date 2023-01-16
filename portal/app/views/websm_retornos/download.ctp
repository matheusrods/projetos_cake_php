<?php

	if($retorno)
	{
		$nome = $retorno['WebsmRetorno']['arquivo_nome'];
		$arquivo = APP.'webroot'.DS.'retorno'.DS.$nome;

		if(isset($arquivo) && file_exists($arquivo))
		{ // faz o teste se a variavel não esta vazia e se o arquivo realmente existe
			header("Content-Type: application/text"); // informa o tipo do arquivo ao navegador
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
		else 
		{
			echo 'Arquivo inexistente';
		}
?>
