<?php
/** 
 * Shell para carregar os arquivos de setores e cargos da simens
 * 
 * @author Willians Paulo Pedroso <williansbuonny@gmail.com>
 * @version 0.1 
 * @package Cron
 * @example cake/console/cake -app ./app carregar_codigo_externo (cargo/setor)
 */


class MigracaoFileServerShell extends Shell {
	var $uses = array('AnexoExame');
	
	public $pathFileServer = "https://api.rhhealth.com.br";

	public function main() {
		echo "*******************************************************************\n";
		echo "* SHELL PARA MIGRAR DE NFS DO PORTAL PARA O FILESERVER 			 \n";
		echo "*******************************************************************\n";
	}

	public function migracao_exames()
	{
		print "INICIANDO PROCESSAMENTO MIGRACAO EXAMES\n";
		
		//pega os exames da tabela
		$query = "select * from anexos_exames where caminho_arquivo not like '%http%' ORDER BY codigo DESC;";
		$anexos = $this->AnexoExame->query($query);

		// debug($anexos);exit;

		//pega o arquivo anexado		
		$path_base = "https://portal.rhhealth.com.br/portal/files/anexos_exames/";

		$count = 0;
		$queryUpdate = '';
		$queryUpdateRollback = '';
		$error = "";

		//varre a tabela
		foreach($anexos as $dado){

			//pega o codigo do anexo
			$codigo = $dado['0']['codigo'];
			$path = $dado['0']['caminho_arquivo'];

			$path_completo = $path_base.$path;			

			if(!$handle=@fopen($path_completo,r)){
				$error .= "Erro: ".$codigo." | caminho: " . $path_base.$path."\n";
				continue;			
			}

			//verifica o tipo mime
			//
			$extensao = end(explode(".",$path_completo));
			$dataType = "image/png";
			if($extensao == "pdf") {
				$dataType = "application/pdf";
			}
			else if($extensao == "jpeg" || $extensao == "jpg") {
				$dataType = "image/jpg";
			}

			//arquivo transformado
			$arquivo_64 = "data:".$dataType.";base64,". base64_encode(file_get_contents($path_completo));

			// print $arquivo_64."\n";exit;

			//monta o array para enviar
	        $dados = array(
	            'file'   => $arquivo_64,
	            'prefix' => 'ithealth',
	            'type'   => 'base64'
	        );

            $retorno = $this->sendFileToServer($dados);
        	// debug($retorno);
        	if(isset($retorno->{'response'}->{'path'})) {
	            $update = $this->pathFileServer.$retorno->{'response'}->{'path'};

	            $queryUpdateRollback .= "UPDATE RHHealth.dbo.anexos_exames SET caminho_arquivo='".$path."' WHERE codigo = ".$codigo.";\n";
	            $queryUpdate .= "UPDATE RHHealth.dbo.anexos_exames SET caminho_arquivo='".$update."' WHERE codigo = ".$codigo.";\n";

				$count++;
				print $count."\n";
        	}
        	else {
        		$error .= "Erro PATH FILE_SERVER: ".$codigo." | caminho: " . $path_base.$path."\n";
        	}


		}//fim foreach
	
		if(!empty($error)) {
			file_put_contents("C:/home/sistemas/rhhealth/portal/app/tmp/erro.log", $error);
		}        

		if(!empty($queryUpdate)) {
			file_put_contents("C:/home/sistemas/rhhealth/portal/app/tmp/update_anexo_exame.sql", $queryUpdate);
		}

		if(!empty($queryUpdateRollback)) {
			file_put_contents("C:/home/sistemas/rhhealth/portal/app/tmp/update_anexo_exame_rollback.sql", $queryUpdateRollback);
		}


		print "FIM PROCESSAMENTO FILE_SERVER\n";

	}//fim migracao


	public function sendFileToServer($data)
    {
        // $data = array(
        //     'file'=> $absFileName, 
        //     'prefix' => $prefix);
        
        // debug($data);
        $path = "https://api.rhhealth.com.br";

        $cURL = curl_init();
        curl_setopt( $cURL, CURLOPT_URL, $path."/upload" );
        curl_setopt( $cURL, CURLOPT_POST, true );
        curl_setopt( $cURL, CURLOPT_POSTFIELDS, $data);
        curl_setopt( $cURL, CURLOPT_RETURNTRANSFER, true );

        $result = curl_exec( $cURL );
        
        // $err = curl_error($cURL);
        // curl_close($cURL);
        // debug($err);
        // debug($result);
        // exit;


        $result = json_decode($result);
        curl_close ($cURL);

        return $result;
    }//fim sendFileToServer


}
?>
