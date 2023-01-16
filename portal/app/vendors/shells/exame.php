<?php
class ExameShell extends Shell {
	
	//atributo que instancia as models
    var $uses = array(
    	'Exame',
		'Processamento',
		'PedidoExame',
		'PedidoExame'
    	);
	
	function startup(){

	}
	
	function main() {
        echo "cake/console/cake -app ./app exame gerar_csv\n";
	}

	public function gerar_csv(){

	    $dados = (isset($this->args[0])) ? $this->args[0] : '';

	    $dados = json_decode($dados, true);//converte o json para array

	    // $this->log('opaa entrei', 'debug');
	    // $this->log($dados, 'debug');

	    foreach($dados as $key => $dado){
	    	if(isset($dado[1])){
	    		if(isset($dado[1]['OR'])){

	    			foreach($dado[1]['OR'] as $key2 => $array_cond){
		    			if($key2 == 0){
		    				$dado[1]['OR'][$key2][0] = str_replace('"', "'", $dado[1]['OR'][$key2][0]);
		    				$dados['conditions'][1]['OR'][0][0] = $dado[1]['OR'][$key2][0] ;
		    			} else if($key2 == 1) {
		    				$dado[1]['OR'][$key2][0] = str_replace('"', "'", $dado[1]['OR'][$key2][0]);		    				
		    				$dados['conditions'][1]['OR'][1][0] = $dado[1]['OR'][$key2][0];
		    			}	    				
	    			}
	    		}
	    	}
	    }

	    $codigo_processamento = $dados['codigo_processamento'];
	    $conditions = $dados['conditions'];

	    // $this->log($codigo_processamento, 'debug');
	    // $this->log($conditions, 'debug');

	    //verifica se foi passado o codigo do processamento cadastrado
	    if(!empty($codigo_processamento)){

	    	// $this->log('opaa entrei 2', 'debug');

	    	//pega o tipo de esocial que deve gerar o zip
	    	$dados_proc = $this->Processamento->find('first', array('conditions' => array('codigo' => $codigo_processamento)));

	    	//pega o caminho onde vai gerar o arquivo
	    	$path = TMP."csv_posicao_exames_analitico".DS.$codigo_processamento.DS;

	    	//verifica se existe o arquivo
	    	if(!is_dir($path)) {
	    		//cria o diretorio
	    		mkdir($path,0777,true);
	    	}
	    	else {	    		
	    		//remove o diretorio para criar um novo
	    		array_map('unlink', glob($path."*.csv"));
	    		array_map('unlink', glob($path."*.zip"));
	    	}

	    	//atualiza o status do processamento para em processamento
	    	$proc = array('Processamento' => array('codigo' => $codigo_processamento,'codigo_processamento_status' => 2));
	    	$this->Processamento->atualizar($proc);

            $this->gerarPosicaoExames($codigo_processamento, $conditions);//gera o arquivo csv e armazena no diretorio

            //aguarda 1 seg
	    	sleep(1);

	    	//gera os zip com o arquivo csv
	    	$zip = new ZipArchive();
	    	
	    	//nome e caminho do zip
	    	$nome_zip = 'posicao_exames_analitico_'.date('YmdHis').'.zip';
	    	$path_zip = $path.$nome_zip;

 			//verifica se existe o arquivo zip
			if( $zip->open( $path_zip , ZipArchive::CREATE )  === true) {
				//pega os arquivos da pasta
				$arquivos = glob($path."*.csv");

				//para pegar o nome do arquivo
				$nome = end(explode("/", $arquivos[0]));

				//adiciona no zip o arquivo
				$zip->addFile($arquivos[0], $codigo_processamento.DS.$nome); 
				
				//finaliza o zip 
				$zip->close();

			}//fim verifica zip

			//sobe o zip no file server
	    	$url = $this->enviarFileServer("@".$path_zip);

	    	//atualiza a tabela de processamento com o caminho do zip e o status para processado
 		    $proc = array('Processamento' => array('codigo' => $codigo_processamento,'codigo_processamento_status' => 3, 'caminho' => 'https://api.rhhealth.com.br'.$url));
	    	$this->Processamento->atualizar($proc);

	    	//limpa a pasta e os arquivos do servidor onde gerou fisicamente
	    	array_map('unlink', glob($path."*.xml"));
	    	array_map('unlink', glob($path."*.zip"));
	    	rmdir($path);

	    }//fim verificacao codigousuario

	    // print "fim";

	}//fim

	public function gerarPosicaoExames($codigo_processamento, $conditions){

        $order = array('unidade_descricao','setor_descricao','nome', 'cargo');

        // echo "processando query..."."\n";

        ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 600); // 5min

        //pega a query sql para executar o downlod das informações
        $query = $this->Exame->posicao_exames_analitico_otimizado('sql', compact('conditions'));

        //seta a variavel como nula para ajudar na consulta
        $codigo_cliente_matriz = null;
        //verifica se existe o codigo da matriz setado
        if(isset($conditions['analitico.codigo_matriz'])) {
            //seta o codigo da matriz na consulta
            $codigo_cliente_matriz = $conditions['analitico.codigo_matriz'];
        } else if(isset($conditions['conditions']['analitico.codigo_matriz'])) { //senao proruca se o codigo da matriz está em outro indice do array
            //seta o codigo da matriz
            $codigo_cliente_matriz = $conditions['conditions']['analitico.codigo_matriz'];
        }//fim if

        //busca a query dos dados
        $ctes = $this->Exame->cte_posicao_exames_otimizada_periodico($codigo_cliente_matriz);

        $dados_posicao_exames_analitico = $this->Exame->query($ctes. $query);

        //pega o caminho onde vai gerar o arquivo
	    $path = TMP."csv_posicao_exames_analitico".DS.$codigo_processamento.DS;

    	//verifica se existe o arquivo
    	if(!is_dir($path)) {
    		//cria o diretorio
    		mkdir($path,0777,true);
    	} else {	    		
    		//remove o diretorio para criar um novo
    		array_map('unlink', glob($path."*.csv"));
    		array_map('unlink', glob($path."*.zip"));
    	}

        $nome_arquivo = 'posicao_exames_analitico'.'.csv';
    	$arquivo = $path.$nome_arquivo;
    	
        $linha = '';

        $linha .= 'Unidade;Setor;Cargo;CPF;Funcionário;Código Matrícula;Matrícula;Admissão;Situação;Tipo Exame;Exame;Periodicidade;Status;Último Pedido;Comparecimento;Data Resultado;Vencimento;'."\n";
		
		$linha = utf8_decode($linha);

		if(!empty($dados_posicao_exames_analitico)){

	   		foreach($dados_posicao_exames_analitico as $value){
	            $situacao = "";
	            $status = "";

	            //Preenche situacao
	            if($value['0']['situacao'] == 0){
	                $situacao =  "Inativo";
	            }elseif($value['0']['situacao'] == 2){
	                $situacao = "Férias";
	            }elseif($value['0']['situacao'] == 3){
	                $situacao = "Afastado";
	            }else{
	                $situacao = "Ativo";
	            }
	            //Preenche status
	            if($value['0']['pendente'] == 1){
	                $status =  "Pendente";
	            }elseif($value['0']['vencido'] == 1){
	                $status =  "Vencido";
	            }elseif($value['0']['vencer'] == 1){
	                $status =  "À vencer";
	            }
	            //tipo de exame descricao
	            $tipo_exame_descricao = $value[0]['tipo_exame_descricao'];
	            if($value[0]['tipo_exame_descricao_monitorac'] == "MT") {
	                $tipo_exame_descricao = "Monitoramento";
	            }

	            $linha .= utf8_decode($value[0]['unidade_descricao']).';';
	            $linha .= utf8_decode($value[0]['setor_descricao']).';';
	            $linha .= utf8_decode($value[0]['cargo']).';';
	            $linha .= $value[0]['cpf'].';';
	            $linha .= utf8_decode($value[0]['nome']).';';
	            $linha .= $value[0]['codigo_cf'].';';
	            $linha .= $value[0]['matricula'].';';
	            $linha .= AppModel::dbDateToDate( $value[0]['admissao']).';';
	            $linha .= utf8_decode($situacao).';';
	            $linha .= utf8_decode($tipo_exame_descricao).';';
	            $linha .= utf8_decode($value[0]['exame_descricao']).';';
	            $linha .= utf8_decode($value[0]['periodicidade']).';';
	            $linha .= utf8_decode($status).';';
	            $linha .= AppModel::dbDateToDate($value['0']['ultimo_pedido']).';';
	            $linha .= utf8_decode($value[0]['compareceu']).';';
	            $linha .= AppModel::dbDateToDate($value['0']['data_realizacao_exame']).';';
	            $linha .= AppModel::dbDateToDate($value['0']['vencimento']).';';
	           
	           	$linha .= "\n";
	    	}

		}
        
        //cria o arquivo
		file_put_contents($arquivo, $linha."\r\n");
    }

	/**
	 * [enviarFileServer description]
	 * 
	 * metodo para enviar o zip para o file-server
	 * 
	 * @param  [type] $path_zip [description]
	 * @return [type]           [description]
	 */
	public function enviarFileServer($path_zip)
	{

		//pega o metodo para enviar para o file server
    	$url = AppModel::sendFileToServer($path_zip, 'it_health');
    	//recupera os caminho do fileserver
    	$url_path = $url->{'response'}->{'path'};

    	return $url_path;

	}//fim enviarFileServer

}
?>
