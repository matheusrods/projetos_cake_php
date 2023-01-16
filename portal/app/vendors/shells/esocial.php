<?php
class EsocialShell extends Shell {
	
	//atributo que instancia as models
    var $uses = array(
    	'Esocial',
		'Processamento',
		'ProcessamentoPedidoExame',
		'PedidoExame',
		'ProcessamentoCat',
		'GrupoExpoProcessamento',
		'AtestadoProcessamento',
		'MensageriaEsocial',
		'IntEsocialEventos'
    	);
	
	function startup(){
		//deverá ser passado o domínio completo, 
		//exemplo: buonny.com.br / gol.local.buonny / localhost
		// $_SERVER['SERVER_NAME'] = isset($this->args[0]) ? $this->args[0] : 'localhost';
	}
	
	function main() {
        echo "cake/console/cake -app ./app esocial s2220_gerar_zip\n";
	}

	/**
	 * [s2220_gerar_zip description]
	 * 
	 * metodo para criar os xmls e zipar o arquivo
	 * 
	 * @return [type] [description]
	 */
	public function gerar_zip(){

	    //pega o codigo do processamento que esta no parametro
	    $codigo_processamento = (isset($this->args[0])) ? $this->args[0] : '';

	    //verifica se foi passado o codigo do processamento cadastrado
	    if(!empty($codigo_processamento)){

	    	//pega o tipo de esocial que deve gerar o zip
	    	$dados_proc = $this->Processamento->find('first', array('conditions' => array('codigo' => $codigo_processamento)));

	    	//verifica qual tipo é de esocial
	    	if($dados_proc['Processamento']['codigo_processamento_tipo_arquivo'] == 1) { //tabela s2220
	    		$tipo = "s2220";
	    		$metodo = "gerar_s2220";
	    	}else if($dados_proc['Processamento']['codigo_processamento_tipo_arquivo'] == 2){//tabela s2221
				$tipo = "s2221";
				$metodo = "gerar_s2221";
			} else if($dados_proc['Processamento']['codigo_processamento_tipo_arquivo'] == 3){//tabela s2210
				$tipo = "s2210";
				$metodo = "gerar_s2210";
			}  else if($dados_proc['Processamento']['codigo_processamento_tipo_arquivo'] == 4){//tabela s2240
				$tipo = "s2240";
				$metodo = "gerar_s2240";
			} else if($dados_proc['Processamento']['codigo_processamento_tipo_arquivo'] == 5){//tabela s2230
				$tipo = "s2230";
				$metodo = "gerar_s2230";
			}//fim validacao		

	    	//pega o caminho onde vai gerar o arquivo
	    	$path = TMP."xml_esocial".DS.$codigo_processamento.DS;

	    	//verifica se existe o arquivo
	    	if(!is_dir($path)) {
	    		//cria o diretorio
	    		mkdir($path,0777,true);
	    	}
	    	else {	    		
	    		//remove o diretorio para criar um novo
	    		array_map('unlink', glob($path."*.xml"));
	    		array_map('unlink', glob($path."*.zip"));
	    	}

	    	//atualiza o status do processamento para em processamento
	    	$proc = array('Processamento' => array('codigo' => $codigo_processamento,'codigo_processamento_status' => 2));
	    	$this->Processamento->atualizar($proc);

	    	if($dados_proc['Processamento']['codigo_processamento_tipo_arquivo'] == 3){// S-2210	    		
	    		//pega os pedidos para gerar o xml
	    		$pedidos = $this->ProcessamentoCat->find('list', array('fields' => array('codigo','codigo_cat'),'conditions' => array('codigo_processamento' => $codigo_processamento)));

	    	} else if($dados_proc['Processamento']['codigo_processamento_tipo_arquivo'] == 4){// S-2240
	    		//pega os pedidos para gerar o xml
	    		$pedidos = $this->GrupoExpoProcessamento->find('all', array('fields' => array('codigo','codigo_grupo_exposicao', 'codigo_funcionario'),'conditions' => array('codigo_processamento' => $codigo_processamento)));

	    	} else if($dados_proc['Processamento']['codigo_processamento_tipo_arquivo'] == 5){// S-2230
	    		//pega os pedidos para gerar o xml
	    		$pedidos = $this->AtestadoProcessamento->find('list', array('fields' => array('codigo','codigo_atestado'),'conditions' => array('codigo_processamento' => $codigo_processamento)));

	    	} else { //S-2220
	    		//pega os pedidos para gerar o xml
	    		$pedidos = $this->ProcessamentoPedidoExame->find('list', array('fields' => array('codigo','codigo_pedido_exame'),'conditions' => array('codigo_processamento' => $codigo_processamento)));
	    	}

	    	//varre os pedidos
	    	foreach($pedidos as $codigo_id => $codigo) {

	    		//gera os xml fisicamente no servidor
	    		if($dados_proc['Processamento']['codigo_processamento_tipo_arquivo'] == 4){
	    			$xml = $this->Esocial->{$metodo}($codigo['GrupoExpoProcessamento']['codigo_grupo_exposicao'], $codigo['GrupoExpoProcessamento']['codigo_funcionario']);
	    		} else {
	    			$xml = $this->Esocial->{$metodo}($codigo);
	    		}

	    		//pega o xml gravado
				$dado_xml = $xml;
				//lendo xml
				$read_xml = simplexml_load_string($dado_xml);

				//switch para direcionamento da leitura e montagem do txt2 a partir do xml corretamente
				switch ($tipo) {
					
					case 's2210': //s2210
						$xmlns = 'http://www.esocial.gov.br/schema/evt/evtCAT/v_S_01_00_00';
						//pegando o atributo ID
						$attributeID = $this->MensageriaEsocial->xml_attribute($read_xml->evtCAT, 'Id');
						break;
					case 's2220': //s2220
						$xmlns = 'http://www.esocial.gov.br/schema/evt/evtMonit/v_S_01_00_00';
						//pegando o atributo ID
						$attributeID = $this->MensageriaEsocial->xml_attribute($read_xml->evtMonit, 'Id');
						break;
					case 's2230': //s2230
						$xmlns = 'http://www.esocial.gov.br/schema/evt/evtAfastTemp/v_S_01_00_00';
						//pegando o atributo ID
						$attributeID = $this->MensageriaEsocial->xml_attribute($read_xml->evtAfastTemp, 'Id');
						break;
					case 's2240': //s2240
						$xmlns = 'http://www.esocial.gov.br/schema/evt/evtExpRisco/v_S_01_00_00';
						//pegando o atributo ID
						$attributeID = $this->MensageriaEsocial->xml_attribute($read_xml->evtExpRisco, 'Id');
						break;
					case 's3000': //s3000
						$xmlns = 'http://www.esocial.gov.br/schema/evt/evtExclusao/v_S_01_00_00';
						//pegando o atributo ID
						$attributeID = $this->MensageriaEsocial->xml_attribute($read_xml->evtExclusao, 'Id');
						break;
				}//fim geracao do txt2 por evento

				//add o xmlns na tag esocial
				$obj_xml = new SimpleXMLElement($dado_xml);
				$obj_xml->addAttribute('xmlns', $xmlns);
				
				//formata o xml que vai ser enviado para a tecnospeed
				$dado_xml_envio = $obj_xml->asXML();
				$dado_xml_envio = str_replace("\n", "", $dado_xml_envio);
				
				$xml = str_replace('<?xml version="1.0"?>', '', $dado_xml_envio);

	    		//nome do arquivo
	    		$nome_arquivo = "esocial_".$tipo."_".date('YmdHis').".xml";
	    		$path_esocial = $path.$nome_arquivo;

	    		//cria o arquivo
	    		file_put_contents($path_esocial, $xml);

	    		if($dados_proc['Processamento']['codigo_processamento_tipo_arquivo'] == 3){// S-2210
	    			$proc_pedido = array(
		    			'ProcessamentoCat' => array(
		    				'codigo' => $codigo_id,
		    				'xml_gerado' => 1
		    			)
	    			);
	    			$this->ProcessamentoCat->atualizar($proc_pedido);
	    		} else if($dados_proc['Processamento']['codigo_processamento_tipo_arquivo'] == 4){
	    			$proc_pedido = array(
		    			'GrupoExpoProcessamento' => array(
		    				'codigo' => $codigo['GrupoExpoProcessamento']['codigo'],
		    				'xml_gerado' => 1
		    			)
	    			);
	    			$this->GrupoExpoProcessamento->atualizar($proc_pedido);
	    		} else if($dados_proc['Processamento']['codigo_processamento_tipo_arquivo'] == 5){
	    			$proc_pedido = array(
		    			'AtestadoProcessamento' => array(
		    				'codigo' => $codigo_id,
		    				'xml_gerado' => 1
		    			)
	    			);
	    			$this->AtestadoProcessamento->atualizar($proc_pedido);
	    		} else {
	    			//pega atualiza a tabela de processamento pedidos exames com os xmls
	    			$proc_pedido = array(
		    			'ProcessamentoPedidoExame' => array(
		    				'codigo' => $codigo_id,
		    				'xml_gerado' => 1
		    			)
	    			);
		    		//atualiza o processamento pedido exame
		    		$this->ProcessamentoPedidoExame->atualizar($proc_pedido);
	    		}   	

	    		//aguarda 1 seg para pois no arquivo do esocial nao pode ter o mesma tag ID
	    		sleep(1);

	    	}//fim foreach	

	    	//gera os zip com os xml's
	    	$zip = new ZipArchive();
	    	
	    	//nome e caminho do zip
	    	$nome_zip = 'esocial_'.date('YmdHis').'.zip';
	    	$path_zip = $path.$nome_zip;

 			//verifica se existe o arquivo zip
			if( $zip->open( $path_zip , ZipArchive::CREATE )  === true) {
				//pega os arquivos da pasta
				$arquivos = glob($path."*.xml");

				//varre os arquivos do diretorio
				foreach ($arquivos as $arq) {
					//para pegar o nome do arquivo
					$nome = end(explode("/", $arq));
					//adicona no zip o arquivo
				    $zip->addFile($arq, $codigo_processamento.DS.$nome);
				}//fim foreach
				
				//finaliza o zip 
				$zip->close();

			}//fim verifica zip
	    	
	    	//sobe o zip no file server
	    	$url = $this->enviarFileServer("@".$path_zip);

	    	//atualiza a tabela de processamento com o caminho do zip e o status para processado
 		    $proc = array('Processamento' => array('codigo' => $codigo_processamento,'codigo_processamento_status' => 3, 'caminho' => 'https://api.rhhealth.com.br'.$url));
	    	$this->Processamento->atualizar($proc);

	    	//se for cat nao verifica, e grupo exposicao
	    	if($dados_proc['Processamento']['codigo_processamento_tipo_arquivo'] != 3 || $dados_proc['Processamento']['codigo_processamento_tipo_arquivo'] != 4){
	    		self::enviaEmailAlertaToxicologico($codigo_processamento);
	    	} 

	    	//limpa a pasta e os arquivos do servidor onde gerou fisicamente
	    	array_map('unlink', glob($path."*.xml"));
	    	array_map('unlink', glob($path."*.zip"));
	    	rmdir($path);

	    }//fim verificacao codigousuario

	    // print "fim";

	}//fim gerar_zip

	private function enviaEmailAlertaToxicologico($codigo_processamento){
		$fields = array("DISTINCT PedidoExame.codigo_cliente");
		$joins = array(
			array(
				'table' => 'Rhhealth.dbo.processamento_pedidos_exames',
				'alias' => 'ProcessamentoPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ProcessamentoPedidoExame.codigo_processamento = Processamento.codigo',
			),
			array(
				'table' => 'Rhhealth.dbo.pedidos_exames',
				'alias' => 'PedidoExame',
				'type' => 'INNER',
				'conditions' => 'ProcessamentoPedidoExame.codigo_pedido_exame = PedidoExame.codigo',
			),
			array(
				'table' => 'Rhhealth.dbo.itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
			),
		);
		$where = array(
			"Processamento.codigo" => $codigo_processamento,
			"ItemPedidoExame.codigo_exame IN(2195, 134, 135)"//exames toxicologicos
		);
		$data = $this->Processamento->find('all', array('fields' => $fields, 'joins' => $joins, 'conditions' => $where));
		if(!is_null($data) && is_array($data)){
			foreach($data as $p){
				$this->PedidoExame->enviaEmailClienteESocial($p['PedidoExame']['codigo_cliente'], 'email_esocial_s2221');
				$this->PedidoExame->alerta_esocial($p['PedidoExame']['codigo_cliente'], 's2221', 'email_esocial_s2221');
			}
		}
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

	/**
	 * 
	 */
	public function integracao_tecnospeed()
	{


		$codigo_int_esocial_evento = (isset($this->args[0])) ? $this->args[0] : '';

		print $codigo_int_esocial_evento."\n";

		$dados_integracao = $this->MensageriaEsocial->tecnospeed_evento_enviar_xml($codigo_int_esocial_evento);

		debug($dados_integracao);

		exit;


	}//fim integracao_tecnospeed

	/**
	 * [get_integracao_tecnospeed metodo que varre a tabela de integracao int_esocial_evento com status 2->processado e atualiza para 4->concluido ou 5->erro]
	 * @return [type] [description]
	 */
	public function get_integracao_tecnospeed() 
	{
		//pega os registros que estão aguardando processamento na sefaz
		$conditions = $this->IntEsocialEventos->convertFiltroEmCondition(array('status' => '2'));
		$dados = $this->IntEsocialEventos->getAll($conditions, false, "all");
		// debug($dados);exit;
		//verifica se tem algum registros aguardando processamento
		if(!empty($dados)) {

			//varre os resultados
			foreach($dados AS $arr_dado) {

				$retorno_tecnospeed = $this->MensageriaEsocial->tecnospeed_evento_consulta($arr_dado);

			}//fim foreach dados

		}//fim dados

	}//fim get_integracao_tecnospeed

}
?>
