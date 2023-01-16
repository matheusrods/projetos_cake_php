<?php

class LoadplanShell extends Shell {
	var $uses = array(
		'TLoadLoadplan',
		'SmLg'
	);

	function main() {
		echo "**********************************************\n";
		echo "$ \n";
		echo "$ Dados de integração LG Loadplan \n";
		echo "$ \n";
		echo "**********************************************\n\n";
		echo "sincroniza: \n\n";
		echo "# importar_dados() \n";

		echo "\n";
	}
	
	function importar_dados(){
		App::import('Vendor', 'xml'.DS.'xml2_array');
		$this->LogIntegracao 	=& ClassRegistry::init('LogIntegracao');
		$this->TViagViagem 		=& ClassRegistry::init('TViagViagem');
		$this->TRefeReferencia 	=& ClassRegistry::init('TRefeReferencia');
		
		$fields = array(
			'conteudo AS conteudo',
			'loadplan AS loadplan',
		);

		$conditions = array(
			"codigo IN (SELECT MAX(codigo) FROM dbmonitora.dbo.logs_integracoes WHERE sistema_origem = 'SmLg_FTP' GROUP BY loadplan)",
			"loadplan>''"
		);
$limit = 10;
		$dados = $this->LogIntegracao->find('all', compact('fields', 'conditions', 'limit'));	
	
		if(!$dados){
			echo "Nenhum dado localizado!\n";
			return false;
		}
		foreach ($dados as $key => $xml) {
			$ja_tem = $this->TLoadLoadplan->find('count', array('conditions' => array('load_loadplan' => trim($xml[0]['loadplan']))));
			if (!$ja_tem) {
				try{
					echo $key.'/'.count($dados);
					//echo "LOADPLAN [{$xml[0]['loadplan']}] - ";

					$objXml = XML2Array::createArray(trim($xml[0]['conteudo']));	
					if(!$objXml) throw new Exception("Nao foi possivel ler o arquivo");

					if(!isset($objXml['CustomXML']['MessageBody']['ContentList']['DetailList'][0]))
						$objXml['CustomXML']['MessageBody']['ContentList']['DetailList'] = array($objXml['CustomXML']['MessageBody']['ContentList']['DetailList']);

					$depara_origem 	= $objXml['CustomXML']['MessageBody']['ContentList']['SHIP_FROM_CODE'];
					$alvo_origem 	= $this->TRefeReferencia->buscaPorDePara($this->SmLg->cliente_guardian,$depara_origem);
					if(!$alvo_origem) throw new Exception("Alvo origem \"{$depara_origem}\" nao localizado");

					$depara_destino = end($objXml['CustomXML']['MessageBody']['ContentList']['DetailList']);
					$depara_destino = $depara_destino['SHIP_TO_CD'];
					$alvo_destino 	= $this->TRefeReferencia->buscaPorDePara($this->SmLg->cliente_guardian,$depara_destino);
					if(!$alvo_destino) throw new Exception("Alvo destino \"{$depara_destino}\" nao localizado");

					$viagem 		= $this->TViagViagem->carregarUltimaSmLoadplanPorLoad($xml[0]['loadplan']);
					$data_finalizado= NULL;
					$ultima_sm 		= NULL;

					if($viagem){
						$data_finalizado 	= ($viagem['TRefeDestino']['refe_codigo'] == $alvo_destino['TRefeReferencia']['refe_codigo'])?
							date('Ymd H:i:s',strtotime(str_replace('/', '-', $viagem['TViagViagem']['viag_data_cadastro']))):NULL;
						$ultima_sm 			= $viagem['TViagViagem']['viag_codigo_sm'];
					}
					$data_cadastro = $this->LogIntegracao->find('first', array('fields' => 'CONVERT(VARCHAR, MIN(data_inclusao), 120) AS data_cadastro', 'conditions' => array('loadplan' => trim($xml[0]['loadplan']))));
					$data_cadastro = $data_cadastro[0]['data_cadastro'];
					$loadplan = array(
						'TLoadLoadplan' => array(
							'load_loadplan'				=> trim($xml[0]['loadplan']),
							'load_codigo_ultima_sm'		=> $ultima_sm,
							'load_data_cadastro'		=> $data_cadastro,
							'load_data_finalizado'		=> $data_finalizado,
							'load_cnpj_transportador' 	=> trim($objXml['CustomXML']['MessageBody']['ContentList']['EDI_RECEIVER_ID']),
							'load_refe_codigo_origem' 	=> $alvo_origem['TRefeReferencia']['refe_codigo'],
							'load_refe_codigo_destino' 	=> $alvo_destino['TRefeReferencia']['refe_codigo'],
						),
					);
					print_r($loadplan);
					if(!$this->TLoadLoadplan->incluir($loadplan)) throw new Exception(Comum::implodeRecursivo(';',$this->TLoadLoadplan->invalidFields()));
					
					//echo "Incluido com sucesso!\n";
					
				} catch (Exception $ex){
					echo $ex->getMessage()."\n";
					
				}
			} else {
				echo "ja tem ".trim($xml[0]['loadplan'])."\n";
			}

		}
		

	}


}
?>
