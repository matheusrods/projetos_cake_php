<?php
class VerificaTamanhoAnexosShell extends Shell {
	var $uses = array(
		'AnexoExame'

	);

	function main() {
		echo "verifica_tamanho_anexos run $codigo_exame \n";
	}

	private function im_running($tipo) {
		$cmd = shell_exec("ps aux | grep 'verifica_tamanho_anexos '");
		
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 1;
	}

	/**
	 * [run pega os anexos para calcular o tamanho do arquivo padrão aso]
	 * @param  string $codigo_exame [description]
	 * @return [type]               [description]
	 */
	public function run($codigo_exame = '52') {

		$query = "SELECT
					pe.codigo as pedido_exame,
					pe.codigo_cliente as codigo_cliente,
					c.nome_fantasia as nome_fantasia,
					c.razao_social as razao_social,
					spe.descricao as status_desc,
					pe.data_inclusao as data_inclusao,

					ipe.codigo_fornecedor as codigo_fornecedor,
					(CASE WHEN f.ambulatorio = 1 THEN 'SIM' ELSE 'NAO' END) as ambulatorio,
					(CASE WHEN f.prestador_particular = 1 THEN 'SIM' ELSE 'NAO' END) as prestador_particular,
					ipeb.data_realizacao_exame as data_resultado_exame,	
					u.apelido as apelido,
					perfil.descricao as perfil,
					tp.descricao as perfil_login_upload,

					e.descricao as exame,
					ae.caminho_arquivo
				from anexos_exames ae
					inner join itens_pedidos_exames ipe on ae.codigo_item_pedido_exame = ipe.codigo
					inner join exames e on ipe.codigo_exame = e.codigo
					inner join pedidos_exames pe on ipe.codigo_pedidos_exames = pe.codigo
					inner join cliente c on pe.codigo_cliente = c.codigo
					inner join status_pedidos_exames spe on pe.codigo_status_pedidos_exames = spe.codigo
					inner join fornecedores f on ipe.codigo_fornecedor = f.codigo
					inner join usuario u on ae.codigo_usuario_inclusao = u.codigo
					inner join uperfis perfil on u.codigo_uperfil = perfil.codigo
					inner join tipos_perfis tp on perfil.codigo_tipo_perfil = tp.codigo
					left join itens_pedidos_exames_baixa ipeb on ipe.codigo = ipeb.codigo_itens_pedidos_exames
				where ipe.codigo_exame = " . $codigo_exame."
					 --and ae.codigo IN (33,101085)
				";

		$dados = $this->AnexoExame->query($query);

		if(!empty($dados)) {
			$tamanho_arquivo = 0;

			$arquivo = "/home/sistemas/rhhealth/c-care/portal/app/tmp/rel_tamanho_arquivos.csv";

			if(file_exists($arquivo)) {
				unlink($arquivo);
			}

			$cabecalho = "COD PEDIDO;COD CLIENTE;NOME FANTASIA;RAZAO SOCIAL;STATUS;DATA INCLUSAO; COD FORNECEDOR; AMBULATORIO; PRESTADOR PARTICULAR; DATA RESULTADO EXAME; LOGIN; PERFIL; TIPO PERFIL; EXAME;TAMANHO ARQUIVO FORMATADO;TAMANHO ARQUIVO BYTES\n";
			file_put_contents($arquivo, $cabecalho, FILE_APPEND);

			foreach($dados AS $dado) {
				$dado = $dado[0];
				// debug($dado);exit;				
				$tamanho_arquivo = 0;
				$path_anexo = $dado['caminho_arquivo'];

				$path_api = "/home/sistemas/rhhealth/samba-share/public/fileserver";

				if(strstr($path_anexo,'https://api.rhhealth.com.br')) {

					$dados_api = explode('https://api.rhhealth.com.br',$path_anexo);
					// debug($dados_api);

                    $arquivo_exame = $path_api.$dados_api[1];
                }
                else if(strstr($path_anexo,'http://api.rhhealth.com.br')) {

                	$dados_api = explode('http://api.rhhealth.com.br',$path_anexo);
					// debug($dados_api);
					
                	$arquivo_exame = $path_api.$dados_api[1];
                }
                else {
	                $arquivo_exame = "/home/sistemas/rhhealth/samba-share/arquivos/anexos_exames/".$path_anexo;
                }
				// debug($dado);
	            $dado['tamanho_arquivo_bytes'] = filesize($arquivo_exame);
	            $dado['caminho_arquivo'] = $this->tamanho_arquivo($arquivo_exame);


                // print $arquivo_exame."--->>".$tamanho_arquivo."\n";
	            $data = implode(';', $dado);
	            $data .= "\n";
	            file_put_contents($arquivo, $data, FILE_APPEND);
			}//fim $dados

		}// empty dados


		
	}//fim run


	/**
	    * Retorna o tamanho de um determinado arquivo em KB, MB, GB TB, etc
	    * @author Rafael Wendel Pinheiro
	    * @param String $arquivo O arquivo a ser verificado
	    * @return String O tamanho do arquivo (já formatado)
	*/
	function tamanho_arquivo($arquivo) {
	    $tamanhoarquivo = filesize($arquivo);
	 
	    /* Medidas */
	    $medidas = array('KB', 'MB', 'GB', 'TB');
	 
	    /* Se for menor que 1KB arredonda para 1KB */
	    if($tamanhoarquivo < 999){
	        $tamanhoarquivo = 1000;
	    }
	 
	    for ($i = 0; $tamanhoarquivo > 999; $i++){
	        $tamanhoarquivo /= 1024;
	    }
	 
	    return round($tamanhoarquivo) . $medidas[$i - 1];
	}

}
?>