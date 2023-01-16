<?php
//App::import('Component','ApiGoogle');
class SistemasController extends AppController {
	public $name = 'Sistemas';
	public $components = array('Maplink','ApiGoogle','Session','Jasper');

	function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array('descriptografar',
			'descriptografar_teste',
			'conversor_folha',
			'index',
			'criptografar',
			'janela_mapa',
			'limpa_cache',
			'limpar_diretorio_acl',
			'limpar_diretorio_models',
			'lista_ramais',
			'consultar_documentos',
			'sm_soap',
			'gpa_files_check',
			'lg_files_check',
			'localizar_arquivo_lg',
			'localizar_arquivo_lg',
			'testa_funcao',
			'aviso_manutencao',
			'trocar_status_criterios',
			'buscarprodutoseservicos',
			'pesquisar_google',
			'obj_to_xml',
			'generateSchema',
			'corrige_db_utf8',
			'srvinfo',
			'corrige_utf8_endereco',
			'create_table_log',
			'dispara_push',
			'azure_push',
			'links_demonstrativo',
			'relatorio_grupo_risco_corona',
			'funcionarios_rel',
			'deletar_usuario_lyn',
			'comparacao_codigo_cnpj',
			'gerar_token',
			'dispara_alertas',
			'dispara_email',
			'criptografar_teste',
			'relatorio_financeiro',
			'log_api_ppra',
			'reenviar_email_pedido',
			'voltar_pedido_pendente',
			'get_reg_id',
			'get_jasper',
			'disparar_email_faturamento',
			'tipos_resultados_exames',
			'inserindo_dados_tempo_liberacao',
			'dispara_email_para_fornecedores',
			'importacao_folha_pagto',
			'importacao_layouts',
			'dispara_get_integracao_tecnospeed',
			'disparar_email_exames_reprovados',
			'tipos_resultados_exames',
			'atualiza_esocial',
			'retorno_nexo_pe',
			'retorno_nexo_aso',
			'retorno_query_alerta_ppra_pcmso',
			'relatorio_produtos_servicos_clientes',
			'pega_arquivo',
			'retorno_query_alerta_ppra_pcmso',
			'relatorio_produtos_servicos_clientes',
			'corrigir_cpf',
			'riscos_conciliar_medicao'
			)
		);
	}

	function corrige_db_utf8($model, $campo) {	
		ini_set('max_execution_time', '999999');
		ini_set('memory_limit', '512M');
		$model = Inflector::classify($model);
		$this->loadModel($model);
		$table = $this->$model->useTable;
		$verifica_documento = $this->$model->query("IF COL_LENGTH('{$table}', 'codigo_documento') IS NOT NULL BEGIN SELECT 1 existe_documento END ELSE BEGIN SELECT 0 existe_documento END");
		$fields = array(
			"{$model}.{$this->$model->primaryKey}",
			"{$model}.$campo"
			);
		if($verifica_documento[0][0]['existe_documento']) {
			$fields[] = "{$model}.codigo_documento";
		}
		$dados = $this->$model->find('all', array(
			'recursive' => -1,
			'fields' => $fields
			)
		);
		$this->$model->validate = array();
		foreach ($dados as $key => $dado) {
			$this->$model->id = $dado[$model][$this->$model->primaryKey];
			if($this->$model->save($dado)) {
				echo '<br>salvou o codigo: '.$dado[$model][$this->$model->primaryKey];
			} else {
				echo '<br>não salvou o codigo: '.$dado[$model][$this->$model->primaryKey];
				ve($this->$model->validationErrors);
				echo '<br>';
			}
		}
		
		exit;
	}

function corrige_utf8_endereco($model) {	
		ini_set('max_execution_time', '999999');
		ini_set('memory_limit', '512M');
		$model = Inflector::classify($model);
		$this->loadModel($model);
		$table = $this->$model->useTable;
		$verifica_documento = $this->$model->query("IF COL_LENGTH('{$table}', 'codigo_documento') IS NOT NULL BEGIN SELECT 1 existe_documento END ELSE BEGIN SELECT 0 existe_documento END");
		$fields = array(
			"{$model}.{$this->$model->primaryKey}",
			"{$model}.logradouro",
			"{$model}.bairro",
			"{$model}.cidade"
			);
		if($verifica_documento[0][0]['existe_documento']) {
			$fields[] = "{$model}.codigo_documento";
		}
		$dados = $this->$model->find('all', array(
			'recursive' => -1,
			'fields' => $fields
			)
		);


		$this->$model->validate = array();
		foreach ($dados as $key => $dado) {
			$this->$model->id = $dado[$model][$this->$model->primaryKey];
			$dado['ClienteEndereco']['codigo_endereco'] = NULL;
			if($this->$model->save($dado)) {
				echo '<br>salvou o codigo: '.$dado[$model][$this->$model->primaryKey];
			} else {
				echo '<br>não salvou o codigo: '.$dado[$model][$this->$model->primaryKey];
				ve($this->$model->validationErrors);
				echo '<br>';
			}
		}
		exit;
	}


	function buscarprodutoseservicos() {
		file_put_contents(TMP."/logs/login_busca_prodserv.log", date('Y-m-d H:i:s')."|".$_SERVER['REMOTE_ADDR']."|".$_SERVER['HTTP_USER_AGENT']."|".$this->data['Produto']['descricao']."\n", FILE_APPEND);
		$this->redirect("http://www.guialogbrasil.com.br/produtos-servicos/index.php");
	}

	function obj_to_xml() {
		App::import('Vendor', 'xml'.DS.'array2_xml');
		$obj = 	Array (
			'autenticacao' => Array (
				'token' => '4ceddb910298c85a1450cf0d49aba5cc',
				),

			'cnpj_cliente' => '06229799000179',
			'cnpj_embarcador' => '06229799000179',
			'cnpj_transportador' => '06229799000179',
			'cnpj_gerenciadora_de_risco' => '43884606000140',
			'pedido_cliente' => '1106',
			'numero_liberacao' => 'PL065022927',
			'tipo_de_transporte' => '2',
			'observacao' => '',
			'controle_temperatura' => Array (
				'de' => '',
				'ate' => '',
				),

			'motorista' => Array (
				'nome' => 'EDJANIO CAVALCANTE MELO',
				'cpf' => '27428831857',
				'telefone' => '11-8678-7114',
				'radio' => '',
				),

			'veiculos' => Array (
				'placa' => Array (
					'0' => 'HMV4422',
					),

				),

			'origem' => Array (
				'codigo_externo' => '06229799000179',
				'descricao' => 'Host Logistica - Matriz',
				'logradouro' => 'Av Jornalista Paulo Zingg',
				'numero' => '301',
				'complemento' => '',
				'bairro' => 'Jaragua',
				'cep' => '5157030',
				'cidade' => 'Sao Paulo',
				'estado' => 'SP ',
				'latitude' => '',
				'longitude' => '',
				),

			'monitorar_retorno' => '1',
			'data_previsao_inicio' => '11/09/2015 12:00:00',
			'data_previsao_fim' => '11/09/2015 18:00:00',
			'itinerario' => Array (
				'alvo' => Array (
					'0' => Array (
						'codigo_externo' => '30122110915105606',
						'descricao' => 'LOJAS R18393',
						'cep' => '9852070',
						'logradouro' => 'ESTRADA SADAE TAKAGI',
						'numero' => '2290',
						'complemento' => '',
						'bairro' => 'COOPERATIVA',
						'cidade' => 'Sao Bernardo do Campo',
						'estado' => 'SP ',
						'latitude' => '',
						'longitude' => '',
						'tipo_parada' => '3',
						'janela_inicio' => '',
						'janela_fim' => '',
						'previsao_de_chegada' => '18:00:00',
						'dados_da_carga' => Array (
							'carga' => Array (
								'loadplan_chassi' => '',
								'nf' => '30122',
								'serie_nf' => '11',
								'tipo_produto' => '198',
								'valor_total_nf' => '58391.68',
								'volume' => '81',
								'peso' => '371',
								),

							),

						),

					'1' => Array (
						'codigo_externo' => '30123110915105606',
						'descricao' => 'LOJAS R18393',
						'cep' => '9852070',
						'logradouro' => 'ESTRADA SADAE TAKAGI',
						'numero' => '2290',
						'complemento' => '',
						'bairro' => 'COOPERATIVA',
						'cidade' => 'Sao Bernardo do Campo',
						'estado' => 'SP ',
						'latitude' => '',
						'longitude' => '',
						'tipo_parada' => '3',
						'janela_inicio' => '',
						'janela_fim' => '',
						'previsao_de_chegada' => '18:00:00',
						'dados_da_carga' => Array (
							'carga' => Array (
								'loadplan_chassi' => '',
								'nf' => '30123',
								'serie_nf' => '11',
								'tipo_produto' => '198',
								'valor_total_nf' => '8114.32',
								'volume' => '13',
								'peso' => '51',
								),

							),

						),

					'2' => Array (
						'codigo_externo' => '30124110915105606',
						'descricao' => 'LOJAS R18393',
						'cep' => '9852070',
						'logradouro' => 'ESTRADA SADAE TAKAGI',
						'numero' => '2290',
						'complemento' => '',
						'bairro' => 'COOPERATIVA',
						'cidade' => 'Sao Bernardo do Campo',
						'estado' => 'SP ',
						'latitude' => '',
						'longitude' => '',
						'tipo_parada' => '3',
						'janela_inicio' => '',
						'janela_fim' => '',
						'previsao_de_chegada' => '18:00:00',
						'dados_da_carga' => Array (
							'carga' => Array (
								'loadplan_chassi' => '',
								'nf' => '30124',
								'serie_nf' => '11',
								'tipo_produto' => '198',
								'valor_total_nf' => '20270.78',
								'volume' => '31',
								'peso' => '118',
								),

							),

						),

					'3' => Array (
						'codigo_externo' => '30125110915105606',
						'descricao' => 'LOJAS R18393',
						'cep' => '9852070',
						'logradouro' => 'ESTRADA SADAE TAKAGI',
						'numero' => '2290',
						'complemento' => '',
						'bairro' => 'COOPERATIVA',
						'cidade' => 'Sao Bernardo do Campo',
						'estado' => 'SP ',
						'latitude' => '',
						'longitude' => '',
						'tipo_parada' => '3',
						'janela_inicio' => '',
						'janela_fim' => '',
						'previsao_de_chegada' => '18:00:00',
						'dados_da_carga' => Array (
							'carga' => Array (
								'loadplan_chassi' => '',
								'nf' => '30125',
								'serie_nf' => '11',
								'tipo_produto' => '198',
								'valor_total_nf' => '4033.64',
								'volume' => '6',
								'peso' => '23',
								),

							),

						),

					'4' => Array (
						'codigo_externo' => '30126110915105606',
						'descricao' => 'LOJAS R18393',
						'cep' => '9852070',
						'logradouro' => 'ESTRADA SADAE TAKAGI',
						'numero' => '2290',
						'complemento' => '',
						'bairro' => 'COOPERATIVA',
						'cidade' => 'Sao Bernardo do Campo',
						'estado' => 'SP ',
						'latitude' => '',
						'longitude' => '',
						'tipo_parada' => '3',
						'janela_inicio' => '',
						'janela_fim' => '',
						'previsao_de_chegada' => '18:00:00',
						'dados_da_carga' => Array (
							'carga' => Array (
								'loadplan_chassi' => '',
								'nf' => '30126',
								'serie_nf' => '11',
								'tipo_produto' => '198',
								'valor_total_nf' => '8067.28',
								'volume' => '12',
								'peso' => '46',
								),

							),

						),

					'5' => Array (
						'codigo_externo' => '30127110915105606',
						'descricao' => 'LOJAS R18393',
						'cep' => '9852070',
						'logradouro' => 'ESTRADA SADAE TAKAGI',
						'numero' => '2290',
						'complemento' => '',
						'bairro' => 'COOPERATIVA',
						'cidade' => 'Sao Bernardo do Campo',
						'estado' => 'SP ',
						'latitude' => '',
						'longitude' => '',
						'tipo_parada' => '3',
						'janela_inicio' => '',
						'janela_fim' => '',
						'previsao_de_chegada' => '18:00:00',
						'dados_da_carga' => Array (
							'carga' => Array (
								'loadplan_chassi' => '',
								'nf' => '30127',
								'serie_nf' => '11',
								'tipo_produto' => '198',
								'valor_total_nf' => '1188.85',
								'volume' => '1',
								'peso' => '7',
								),

							),

						),

					'6' => Array (
						'codigo_externo' => '30128110915105606',
						'descricao' => 'LOJAS R18393',
						'cep' => '9852070',
						'logradouro' => 'ESTRADA SADAE TAKAGI',
						'numero' => '2290',
						'complemento' => '',
						'bairro' => 'COOPERATIVA',
						'cidade' => 'Sao Bernardo do Campo',
						'estado' => 'SP ',
						'latitude' => '',
						'longitude' => '',
						'tipo_parada' => '3',
						'janela_inicio' => '',
						'janela_fim' => '',
						'previsao_de_chegada' => '18:00:00',
						'dados_da_carga' => Array (
							'carga' => Array (
								'loadplan_chassi' => '',
								'nf' => '30128',
								'serie_nf' => '11',
								'tipo_produto' => '198',
								'valor_total_nf' => '8114.32',
								'volume' => '13',
								'peso' => '51',
								),

							),

						),

					'7' => Array (
						'codigo_externo' => '30130110915105606',
						'descricao' => 'LOJAS R18393',
						'cep' => '9852070',
						'logradouro' => 'ESTRADA SADAE TAKAGI',
						'numero' => '2290',
						'complemento' => '',
						'bairro' => 'COOPERATIVA',
						'cidade' => 'Sao Bernardo do Campo',
						'estado' => 'SP ',
						'latitude' => '',
						'longitude' => '',
						'tipo_parada' => '3',
						'janela_inicio' => '',
						'janela_fim' => '',
						'previsao_de_chegada' => '18:00:00',
						'dados_da_carga' => Array (
							'carga' => Array (
								'loadplan_chassi' => '',
								'nf' => '30130',
								'serie_nf' => '11',
								'tipo_produto' => '198',
								'valor_total_nf' => '411.7',
								'volume' => '1',
								'peso' => '1',
								),

							),

						),

					),

),

'iscas' => Array (
	),

'escolta' => Array (
	),
);
$my_xml = Array2XML::createXML('viagem',$obj);
$xml = $my_xml->saveXml();
echo '<pre>';
echo htmlentities($xml);
echo '</pre>';
exit;

}

function generateSchema($model = null) {
	if(!is_null($model)) {
		$return = array();
		$Tabela = ClassRegistry::Init($model);
		$schema = $this->Sistema->query("EXEC sp_columns $Tabela->useTable;");
		if(!empty($schema)) {	
			foreach ($schema as $key => $value) {
				$primaryKey = false;
				switch ($value[0]['TYPE_NAME']) {
					case 'int identity':
					case 'smallint identity':
					$value[0]['TYPE_NAME'] = 'integer';
					$primaryKey = true;
					break;

					case 'int':
					case 'tinyint':
					case 'bit':
					case 'smallint':
					$value[0]['TYPE_NAME'] = 'integer';
					break;

					case 'decimal':
					case 'money':
					$value[0]['TYPE_NAME'] = 'float';
					break;

					case 'varchar':
					case 'char':
					$value[0]['TYPE_NAME'] = 'string';
					break;

					case 'text':
					$length = 16;
					break;
				}
				$return[$value[0]['COLUMN_NAME']] = array(
					'type' 		=> $value[0]['TYPE_NAME'],
					'null' 		=> (($value[0]['NULLABLE'] === 1)? true : false),
					'default' 	=> $value[0]['COLUMN_DEF'],
					'length' 	=> (($value[0]['TYPE_NAME'] == 'string')? 255 : null)
					);
				if($primaryKey) {
					$return[$value[0]['COLUMN_NAME']]['key'] = 'primary';
				}
			}
		}
		return $return;
	}
}

function getDataBaseValues($model) {
	$Tabela = ClassRegistry::Init($model);
	$return = $Tabela->find('all',array('limit' => 10, 'order' => array($Tabela->name.'.'.$Tabela->primaryKey => 'DESC')));
	$values = array();
	foreach ($return as $key => $value) {
		$values[$key] = $value[$Tabela->name];
		if(!empty($values[$key]['data_inclusao'])) {	
			$validate = explode(' ', $values[$key]['data_inclusao']);
			if(count($validate) == 2) {
				$dateFormat = 'd/m/Y H:i:s';
			} else {
				$dateFormat = 'd/m/Y';
			}
			$date = DateTime::createFromFormat($dateFormat, $values[$key]['data_inclusao']);
			$values[$key]['data_inclusao'] = $date->format('Y-m-d H:i:s');
		}
		if(!empty($values[$key]['data_documento'])) {
			$date = DateTime::createFromFormat($dateFormat, $values[$key]['data_documento']);
			$values[$key]['data_documento'] = $date->format('Y-m-d H:i:s');
		} 
	}
	$return = $values;
	unset($values);
	return $return;
}

function index( $model_name = false, $limit = 10 ) {
		if( $model_name ){
			$Tabela = ClassRegistry::Init( $model_name );
		}else {
			$Tabela = ClassRegistry::Init( 'Ficha' );
			$Tabela->bindLazyProfissional();
			$Tabela->bindLazyproduto();
			$Tabela->bindLazyCliente();
		}
		$conditions 	= array();
		$order      	= ($Tabela->primaryKey && ($Tabela->primaryKey != 'id') ? $Tabela->name.'.'.$Tabela->primaryKey : null );
		$resultados  	= $Tabela->find('all', compact('conditions', 'order', 'limit'));
		if(!empty($resultados[0])) {
			$models = array_keys($resultados[0]);
			foreach ($models as $model) {
					$info_table = ClassRegistry::Init( $model );
					echo "class ".$info_table->name."Fixture extends CakeTestFixture {";
					echo "<br />";
					echo 'var $name  = '."'".$info_table->name."';";
					echo "<br />";
					echo 'var $table = '."'".$info_table->table."';";
					echo "<br /><br />";
					echo 'var $fields = array( ';
					echo "<br />";
					$fields = $info_table->schema();		
					foreach ($fields as $campo => $tipos ) {
						echo "'$campo' => ";
						var_export($tipos);
						echo ",<br />";
					} 
					echo ');';
					echo '<br><br>';
					$dados_formatados = Set::extract("{n}.".$info_table->name, $resultados );		
					echo '<pre>';
					echo 'var $records = array( ';
					echo "\n";
					foreach ($dados_formatados as $value ) {
						echo ", \n". var_export( $value ). " \n";			
					}
					echo ');';
					echo '<br><br>}';
					echo '</pre>';

					echo "------------------------------------------------------------------------------------------------------------------";
					echo '<br><br><br><br>';
			}

		}else {
			echo "Nenhuma Informação localizada";
		}
		exit;
	}

	// function index() {
	// 	echo '<pre>';
	// 	$Tabela = ClassRegistry::Init('Risco');
	// 	var_export($Tabela->schema());
	// 	echo '</pre>';
	// 	echo '<br><br>';
	// 	echo '<pre>';
	// 	var_export($Tabela->find('all',array('limit' => 10, 'conditions' => array('codigo' => 511), 'order' => array($Tabela->name.'.'.$Tabela->primaryKey))));
	// 	echo '</pre>';
	// 	exit;
	// }

function pesquisar_google() {
		//$str_pesquisa_end = 'ALAMEDA DOS GUATÁS, 191 - SAÚDE - SÃO PAULO - SP - 04053040';
	$str_pesquisa_end = 'ALAMEDA DOS GUATAS, 191 - SAO PAULO - SP - 04053040';
	$excedido = false;
	$lat_lgn = $this->ApiGoogle->retornaLatitudeLongitudeDoEndereco($str_pesquisa_end,$excedido);
	debug($lat_lgn);
	exit;
}

function ordernar_array_sort_por_campo($array, $on, $order=SORT_ASC) {
	$new_array = array();
	$sortable_array = array();
	if (count($array) > 0) {
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $k2 => $v2) {
					if ($k2 == $on) {
						$sortable_array[$k] = $v2;
					}
				}
			} else {
				$sortable_array[$k] = $v;
			}
		}
		switch ($order) {
			case SORT_ASC:
			asort($sortable_array);
			break;
			case SORT_DESC:
			arsort($sortable_array);
			break;
		}
		foreach ($sortable_array as $k => $v) {
			$new_array[$k] = $array[$k];
		}
	}
	return $new_array;
}


function aviso_manutencao(){
	$this->layout = 'default';
	$this->set('title_for_layout','Sistema em Manutenção');
	$caminho = $_SERVER['DOCUMENT_ROOT']."/arquivos/desativar.txt";
	if(file_exists($caminho)){
		if(!($this->ler_status_log_desativado($caminho) == false)){
			$infoUserOnOffAplication = $this->ler_status_log_desativado($caminho);
			$this->set('info',$infoUserOnOffAplication);
		} else {
			$this->BSession->setFlash('nao_processou_on_off_aplicacao');
		}
	} else {
		$this->redirect(array('controller' => 'usuarios', 'action' => 'login'));
	}
}

function listar_log_manutencao(){
	$caminho = $_SERVER['DOCUMENT_ROOT']."/arquivos/log.txt";
	if(!filesize($caminho) == 0){
		$fp = fopen($caminho, "r");
		if($fp) {
			$i = 0;
			while (!feof($fp)) {
				$buffer = fgets($fp);
				$arr[$i] = explode('|', $buffer);
				$i++;
			}
			fclose($fp);
			if(!isset($arr)){
				return false;
			} else {
				$arr = $this->ordernar_array_sort_por_campo($arr, '1', SORT_DESC);
				$totalPorPage = 50;
				$i=1;
				foreach ($arr as $key => $value) {
					if($i <= $totalPorPage) {
						$arrLimite[] = $value;
					}
					$i++;
				}
				return $arrLimite;
			}
		}
	}
	return false;
}

function ler_status_log_desativado($caminho){
	$fp = fopen($caminho, 'r');
	if($fp){
		while(!feof($fp)){
			$pTexto = fgets($fp);
		}
		$infoUserOnOffAplication = explode('|', $pTexto);
		fclose($fp);
		return $infoUserOnOffAplication;
	} else {
		return false;
	}
}

function manutencao(){
	$this->set('title_for_layout','Manutenção do Sistema');
	$caminho = $_SERVER['DOCUMENT_ROOT']."/arquivos/desativar.txt";
	$dados = $this->data;
	if(!empty($dados)){
		if(($dados['Manutencao']['aplicacao_ativa'] == 0) && (empty($dados['Manutencao']['data_retorno']))){
			$this->BSession->setFlash('informe_data_retorno_aplicacao');
		} elseif($dados['Manutencao']['aplicacao_ativa'] == 1) {
				if($this->log_manutencao(1)){ // 1 igual ativar
					if(file_exists($caminho)){
						$apaga = unlink ($caminho);
						if ($apaga){
							$this->BSession->setFlash('save_success');
							$this->redirect(array('controller'=>'sistemas','action'=>'manutencao'));
						} else {
							$this->BSession->setFlash('save_error');
						}
					} else {
						$this->BSession->setFlash('save_success');
					}
				} else {
					$this->BSession->setFlash('save_error');
				}
			} elseif ($dados['Manutencao']['aplicacao_ativa'] == 0) {
				if($this->log_manutencao(0)){ // 0 igual desativar
					if(isset($this->authUsuario)){
						$pData          = $dados['Manutencao']['data_retorno'];
						$codigoUsuario  = $this->authUsuario['Usuario']['cn'];
						$nomeUsuario    = $this->authUsuario['Usuario']['codigo'];
						$reg = $pData.' | '.$codigoUsuario.' | '.$nomeUsuario.' | '.time();
						$fp = fopen($caminho, "w");
						fwrite($fp, "$reg");
						if($fp){
							$this->BSession->setFlash('save_success');
							$this->redirect(array('controller'=>'sistemas','action'=>'manutencao'));
						} else {
							$this->BSession->setFlash('save_error');
						}
						fclose($fp);
					} else {
						$this->BSession->setFlash('save_error');
					}
				} else {
					$this->BSession->setFlash('save_error');
				}
			} else {
				$this->BSession->setFlash('informe_on_off_aplicacao');
			}
		}
		if(file_exists($caminho)){
			$valor = 0;
			if(!($this->ler_status_log_desativado($caminho) == false)){
				$infoUserOnOffAplication = $this->ler_status_log_desativado($caminho);
			} else {
				$infoUserOnOffAplication = '';
			}
		} else {
			$valor = 1;
			$infoUserOnOffAplication = '';
		}
		$this->data['Manutencao']['aplicacao_ativa'] = $valor;
		$aplicacao_ativa = array('2'=>'Selecione','0'=>'Aplicação Desativada','1'=>'Aplicação Ativa');
		$logs = $this->listar_log_manutencao();
		$this->set(compact('aplicacao_ativa','infoUserOnOffAplication','logs'));
	}

	function testa_funcao() {
		if (!empty($this->data)) {
			$model = $this->data['Model']['name'];
			$model = ClassRegistry::init($model);
			$function = $this->data['Model']['function'];
			$parameters = explode(",", $this->data['Model']['parameters']);
			foreach ($parameters as $key => $parameter) {
				if (strtolower($parameter) == 'null') {
					$parameters[$key] = null;
				} elseif (strtolower($parameter) == 'false') {
					$parameters[$key] = false;
				}
			}
			$result = call_user_func_array(array($model,$function),$parameters);
			$this->set(compact('result'));
		}
	}

	function log_manutencao($tipo){
		$caminho = $_SERVER['DOCUMENT_ROOT']."/arquivos/log.txt";
		if(isset($this->authUsuario)){
			$codigoUsuario  = $this->authUsuario['Usuario']['cn'];
			$nomeUsuario    = $this->authUsuario['Usuario']['codigo'];
			$codeData       = time();
			$dataManutencao = date('d/m/Y H:i:s', $codeData);
			$registro = $dataManutencao.' | '.$codigoUsuario.' | '.$nomeUsuario.' | '.$codeData;
			if($tipo == 0){ // 0 igual desativar
				$acao = 'OFF';
				$registro = $acao.' | '.$registro;
				if(file_exists($caminho)){
					if($this->log_existe_manutencao($caminho,$registro)){
						return true;
					}
					return false;
				} else {
					if($this->log_naoexiste_manutencao($caminho,$registro)){
						return true;
					}
					return false;
				}
			} else {
				$acao = 'ON ';
				$registro = $acao.' | '.$registro;
				if(file_exists($caminho)){
					if($this->log_existe_manutencao($caminho,$registro)){
						return true;
					}
					return false;
				} else {
					if($this->log_naoexiste_manutencao($caminho,$registro)){
						return true;
					}
					return false;
				}
			}
		} else {
			return false;
		}
	}
	function log_existe_manutencao($caminho, $registro){
		$fp = fopen($caminho, "a+");
		fwrite($fp, "$registro \r\n");
		if(!$fp){ return false;	}
		fclose($fp);
		return true;
	}
	function log_naoexiste_manutencao($caminho, $registro){
		$fp = fopen($caminho, "w");
		fwrite($fp, "$registro \r\n");
		if(!$fp){ return false; }
		fclose($fp);
		return true;
	}


	function sm_soap(){
		$obj 				= new stdClass;
		$obj->cnpj_cliente 	= '01691041000800';
		$obj->tipo 			= 'PLACA';
		$obj->valor 		= 'JVT0755';
		$obj->autenticacao 	= new stdClass;
		$obj->autenticacao->token = 'b63ba5adb011f7d4ae65fe91fab30c2f';

		app::import('Component','SmSoap');
		$this->SmSoap = new SmSoapComponent;
		try{
			debug($this->SmSoap->posicao($obj));
		//debug($this->SmSoap->posicaoEmViagem($obj));
		} catch( Exception $e){
			echo $e->getMessage();
		}
		die;

		$TPgpgPg = ClassRegistry::Init('TPgpgPg');

		
		


		/*
		$obj = unserialize('O:8:"stdClass":20:{s:12:"autenticacao";O:8:"stdClass":1:{s:5:"token";s:32:"25e3cc849e55676a6109a11e348260c6";}s:14:"sistema_origem";s:5:"WS GV";s:12:"cnpj_cliente";s:14:"04887927000146";s:15:"cnpj_embarcador";s:14:"04887927000146";s:18:"cnpj_transportador";s:14:"04887927000146";s:26:"cnpj_gerenciadora_de_risco";s:14:"00000000000000";s:14:"pedido_cliente";s:0:"";s:16:"numero_liberacao";s:1:"N";s:18:"tipo_de_transporte";s:1:"2";s:10:"observacao";s:3:"obs";s:20:"controle_temperatura";O:8:"stdClass":2:{s:2:"de";i:0;s:3:"ate";i:0;}s:9:"motorista";O:8:"stdClass":4:{s:4:"nome";s:20:"MARCELO PIRES FERRAZ";s:3:"cpf";s:11:"07076610728";s:8:"telefone";s:0:"";s:5:"radio";s:0:"";}s:8:"veiculos";O:8:"stdClass":1:{s:5:"placa";a:1:{i:0;s:7:"KXX5637";}}s:6:"origem";O:8:"stdClass":11:{s:14:"codigo_externo";s:6:"019592";s:9:"descricao";s:14:"RIO DE JANEIRO";s:10:"logradouro";s:0:"";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:3:"cep";s:8:"21371230";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";}s:17:"monitorar_retorno";b:0;s:20:"data_previsao_inicio";s:19:"10/10/2013 07:13:23";s:17:"data_previsao_fim";s:19:"10/10/2013 07:23:23";s:10:"itinerario";O:8:"stdClass":1:{s:4:"alvo";a:41:{i:0;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21371230_RINALDO_AG";s:9:"descricao";s:40:"RINALDO AGATI COM DE PECAS E SERVICOS ME";s:3:"cep";s:8:"21371230";s:10:"logradouro";s:23:"R OURO FINO 323 APT 301";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1178527;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:6:"149,99";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:1;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21250380_GAA_AUTO_S";s:9:"descricao";s:25:"GAA AUTO SOCORRO LTDA EPP";s:3:"cep";s:8:"21250380";s:10:"logradouro";s:16:"RUA IRANDUBA 244";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:583435;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:4:"1,01";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:2;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21250230_MARCOS_CES";s:9:"descricao";s:29:"MARCOS CESAR DA SILVA BARBOZA";s:3:"cep";s:8:"21250230";s:10:"logradouro";s:19:"R PEDRO RUFINO, 250";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1178830;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:3:"9,9";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:3;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21231190_FERNANDO_J";s:9:"descricao";s:27:"FERNANDO JOSE MOCO MARIEIRO";s:3:"cep";s:8:"21231190";s:10:"logradouro";s:23:"R CRUZ JOBIM 117 AP 101";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1178980;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"17";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:4;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21230085_ANA_LUCIA_";s:9:"descricao";s:26:"ANA LUCIA AZEVEDO DE SOUZA";s:3:"cep";s:8:"21230085";s:10:"logradouro";s:27:"R CANUDOS, SNBL 92  APT 101";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179017;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"34";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:5;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21236060_DRIENEANE_";s:9:"descricao";s:24:"DRIENEANE HORST TILLMANN";s:3:"cep";s:8:"21236060";s:10:"logradouro";s:35:"R PROFESSOR JOAO MASSENA, 155AP 301";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1178891;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"17";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:6;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21220320_CRISTIANE_";s:9:"descricao";s:24:"CRISTIANE FORTUNATO DIAS";s:3:"cep";s:8:"21220320";s:10:"logradouro";s:18:"R MUPIA, 402FUNDOS";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1178995;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"17";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:7;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21241200_RUBENS_ASS";s:9:"descricao";s:23:"RUBENS ASSIS DOS SANTOS";s:3:"cep";s:8:"21241200";s:10:"logradouro";s:17:"R PORTO RICO, 263";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179014;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"34";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:8;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21250510_FABIO_CARL";s:9:"descricao";s:30:"FABIO CARLOS TEODORO DE ARAUJO";s:3:"cep";s:8:"21250510";s:10:"logradouro";s:25:"R COMANDANTE COELHO, 1018";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1178880;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"17";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:9;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21250550_CLEONICE_D";s:9:"descricao";s:15:"CLEONICE DA PAZ";s:3:"cep";s:8:"21250550";s:10:"logradouro";s:25:"R TENENTE PALESTRINA, 214";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179018;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"34";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:10;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21235160_EDNA_RIBEI";s:9:"descricao";s:18:"EDNA RIBEIRO SILVA";s:3:"cep";s:8:"21235160";s:10:"logradouro";s:22:"R OLIVEIRA ALVARES, 80";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179026;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"34";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:11;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21530900_BORGES_COM";s:9:"descricao";s:32:"BORGES COMERCIO DE PESCADOS LTDA";s:3:"cep";s:8:"21530900";s:10:"logradouro";s:34:"AV BRASIL, 19001PAVILHAO 12 BOX 40";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179093;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:3:"119";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:12;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21250560_TRANSPORTE";s:9:"descricao";s:25:"TRANSPORTES MOBILINE LTDA";s:3:"cep";s:8:"21250560";s:10:"logradouro";s:23:"R JUVENCIO MENEZES, 110";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179077;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"17";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:13;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21250450_DANILO_ORT";s:9:"descricao";s:25:"DANILO ORTEGA DE OLIVEIRA";s:3:"cep";s:8:"21250450";s:10:"logradouro";s:29:"R CORDOVIL, 1300APTO 103 BL 1";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179057;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:6:"149,99";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:14;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21361320_JOSE_WALTE";s:9:"descricao";s:29:"JOSE WALTER JACINTHO DE SOUZA";s:3:"cep";s:8:"21361320";s:10:"logradouro";s:25:"R CAROLINA AMADO, 767CASA";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179351;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:6:"149,99";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:15;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21210000_CELY_DA_FO";s:9:"descricao";s:30:"CELY DA FONSECA CARNEIRO DE MA";s:3:"cep";s:8:"21210000";s:10:"logradouro";s:32:"AVENIDA VICENTE DE CARVALHO 1086";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:915682;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:4:"1509";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:16;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21210000_CELY_DA_FO";s:9:"descricao";s:30:"CELY DA FONSECA CARNEIRO DE MA";s:3:"cep";s:8:"21210000";s:10:"logradouro";s:32:"AVENIDA VICENTE DE CARVALHO 1086";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:915683;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:1:"0";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:17;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21220160_LANCHONETE";s:9:"descricao";s:30:"LANCHONETE E CAFETERIA VEM COM";s:3:"cep";s:8:"21220160";s:10:"logradouro";s:14:"RUA ABAGERU 17";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:105605;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:5:"208,7";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:18;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21361570_SEBASTIAO_";s:9:"descricao";s:20:"SEBASTIAO PANO PAIVA";s:3:"cep";s:8:"21361570";s:10:"logradouro";s:16:"R CAXAMBU, 168CS";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179333;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"34";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:19;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21240010_PRIMUS_SER";s:9:"descricao";s:33:"PRIMUS SERVICOS ESPEC DE SEG LTDA";s:3:"cep";s:8:"21240010";s:10:"logradouro";s:35:"R PROFESSOR FRANCA AMARAL, 84GALPAO";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179334;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"34";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:20;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21240660_VANIA_DA_C";s:9:"descricao";s:21:"VANIA DA COSTA DUARTE";s:3:"cep";s:8:"21240660";s:10:"logradouro";s:24:"RUA FIGUEIREDO ROCHA 483";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:916218;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:3:"739";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:21;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21230362_VANUSA_PER";s:9:"descricao";s:25:"VANUSA PEREIRA DOS SANTOS";s:3:"cep";s:8:"21230362";s:10:"logradouro";s:26:"ESTRADA DA AGUA GRANDE 221";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:915948;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:3:"149";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:22;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21530001_JOSE_LUIZ_";s:9:"descricao";s:17:"JOSE LUIZ MACHADO";s:3:"cep";s:8:"21530001";s:10:"logradouro";s:29:"AV BRASIL 19001 KM19 POSTO BR";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179378;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"17";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:23;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21235450_THIAGO_MAT";s:9:"descricao";s:26:"THIAGO MATHEUS DE OLIVEIRA";s:3:"cep";s:8:"21235450";s:10:"logradouro";s:23:"RUA CLAUDIO DA COSTA 33";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:916063;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:3:"739";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:24;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21231160_CAROLINA_V";s:9:"descricao";s:29:"CAROLINA VIANA FELIX DA SILVA";s:3:"cep";s:8:"21231160";s:10:"logradouro";s:22:"RUA LUPERCE MIRANDA 80";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:916346;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"10";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:25;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21230240_MARCOS_AUR";s:9:"descricao";s:26:"MARCOS AURELIO ANDRADE GIL";s:3:"cep";s:8:"21230240";s:10:"logradouro";s:18:"R JOSE SOMBRA, 121";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179493;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"17";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:26;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21220310_LUANA_ANDR";s:9:"descricao";s:22:"LUANA ANDREZA DE SOUZA";s:3:"cep";s:8:"21220310";s:10:"logradouro";s:23:"EST CORONEL VIEIRA, 226";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179570;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"17";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:27;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21241320_ROSEMARY_F";s:9:"descricao";s:25:"ROSEMARY FREITAS HIPOLITO";s:3:"cep";s:8:"21241320";s:10:"logradouro";s:26:"R XAVIER PINHEIRO, 230CA 3";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179469;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"17";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:28;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21241062_SERGIO_MUR";s:9:"descricao";s:27:"SERGIO MURILO SOARES SANTOS";s:3:"cep";s:8:"21241062";s:10:"logradouro";s:22:"R GUADALUPE, 296CASA 2";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179441;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"17";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:29;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21250150_PEDRO_HENR";s:9:"descricao";s:31:"PEDRO HENRIQUE GOMES DOS SANTOS";s:3:"cep";s:8:"21250150";s:10:"logradouro";s:20:"R ANTONIO JOAO, 1003";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179590;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"17";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:30;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21240300_CARLA_CRIS";s:9:"descricao";s:30:"CARLA CRISTINA SANTOS DA SILVA";s:3:"cep";s:8:"21240300";s:10:"logradouro";s:28:"R RODOLFO CHAMBELLAND, 578CS";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179600;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:3:"9,9";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:31;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21250360_ANA_CARLA_";s:9:"descricao";s:26:"ANA CARLA PENHA FIGUEIREDO";s:3:"cep";s:8:"21250360";s:10:"logradouro";s:16:"R DANTE, 115CS 1";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179667;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"17";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:32;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21250280_R_C_S_NASC";s:9:"descricao";s:37:"R C S NASCIMENTO SERVICOS GRAFICOS ME";s:3:"cep";s:8:"21250280";s:10:"logradouro";s:20:"R MAJOR CONRADO, 268";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179688;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:6:"149,99";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:33;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21371360_PAOLA_MARC";s:9:"descricao";s:30:"PAOLA MARCIA FERNANDES DA SILV";s:3:"cep";s:8:"21371360";s:10:"logradouro";s:14:"RUA URUCARA 98";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:917444;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:3:"699";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:34;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21235602_DENILSON_A";s:9:"descricao";s:25:"DENILSON ALEXANDRE AGUIAR";s:3:"cep";s:8:"21235602";s:10:"logradouro";s:21:"AV BRAZ DE PINA, 1771";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179741;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"17";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:35;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21220310_NELSON_DE_";s:9:"descricao";s:15:"NELSON DE SOUZA";s:3:"cep";s:8:"21220310";s:10:"logradouro";s:26:"ESTRADA CORONEL VIEIRA 306";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:916233;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:3:"149";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:36;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21230043_CARLOS_HEN";s:9:"descricao";s:26:"CARLOS HENRIQUE DOS SANTOS";s:3:"cep";s:8:"21230043";s:10:"logradouro";s:25:"AV BRASIL, 17191BL 29/301";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179933;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"17";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:37;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21250290_ANSELMO_WA";s:9:"descricao";s:32:"ANSELMO WALLACE FERREIRA MARQUES";s:3:"cep";s:8:"21250290";s:10:"logradouro";s:29:"R CORONEL CAMISAO, 1125FUNDOS";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179903;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"17";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:38;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21241300_IAPONAN_JO";s:9:"descricao";s:26:"IAPONAN JORGE GOMES RABELO";s:3:"cep";s:8:"21241300";s:10:"logradouro";s:34:"R FERNANDES DA CUNHA, 470APTO. 201";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1179982;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:2:"34";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:39;O:8:"stdClass":16:{s:14:"codigo_externo";s:19:"21235490_MARCOS_DA_";s:9:"descricao";s:24:"MARCOS DA SILVA CARVALHO";s:3:"cep";s:8:"21235490";s:10:"logradouro";s:26:"R HONORIO ALMEIDA 195 CASA";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"3";s:13:"janela_inicio";s:5:"00:00";s:10:"janela_fim";s:5:"23:59";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":1:{s:5:"carga";O:8:"stdClass":7:{s:15:"loadplan_chassi";s:0:"";s:2:"nf";i:1180268;s:8:"serie_nf";s:0:"";s:12:"tipo_produto";s:1:"5";s:14:"valor_total_nf";s:3:"9,9";s:6:"volume";N;s:4:"peso";s:0:"";}}}i:40;O:8:"stdClass":16:{s:14:"codigo_externo";s:6:"019592";s:9:"descricao";s:14:"RIO DE JANEIRO";s:3:"cep";s:8:"21235490";s:10:"logradouro";s:0:"";s:6:"numero";N;s:11:"complemento";s:0:"";s:6:"bairro";s:0:"";s:6:"cidade";s:14:"RIO DE JANEIRO";s:6:"estado";s:2:"RJ";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"5";s:13:"janela_inicio";s:0:"";s:10:"janela_fim";s:0:"";s:19:"previsao_de_chegada";s:19:"10/10/2013 07:23:23";s:14:"dados_da_carga";O:8:"stdClass":0:{}}}}s:5:"iscas";O:8:"stdClass":0:{}s:7:"escolta";O:8:"stdClass":0:{}} ');

		debug($obj);die;
		*/
		/*
		$TViagViagem = ClassRegistry::Init('TViagViagem');
		$TViagViagem->incluir_sm_viagem($data);
		die;

		*/

		/*
		App::import('Component', 'Maplink');
		$this->Maplink = new MaplinkComponent();

		$point = array('point' => array());

		$point['point']['lat']  = -20.8072597347;
		$point['point']['long'] = -48.9301753603;

		$MapAddress = $this->Maplink->busca_endereco_xy($point);
		echo '<pre>';
		var_dump($MapAddress);
		die;
		*/
		app::import('Component','SmSoap');
		$this->SmSoap = new SmSoapComponent;

		//$this->SmSoap->historico_posicao();



		$cliente_cnpj = '17434299000195';
		$cliente_token= '8e2eca5d8827259129374119bd1bf49c';
		/*
		$obj 				= new stdClass;
		$obj->tipo  		= 'placa';
		$obj->valor 		= 'AAA0000';
		$obj->cnpj_cliente 	= '03094658000793';
		$obj->autenticacao 	= new stdClass;
		$obj->autenticacao->token = '47ce0f1111cc03ad94b877ea6c86f875';
		debug($this->SmSoap->posicao($obj));
		die;

		debug($this->SmSoap->autenticar($cliente_token,$cliente_cnpj));
		die;
		*/

		$obj 	 = unserialize('O:8:"stdClass":20:{s:12:"autenticacao";O:8:"stdClass":1:{s:5:"token";s:32:"7c28dab66575d84b07abd8ae02773a22";}s:14:"sistema_origem";s:13:"TESTE ROTTENY";s:12:"cnpj_cliente";s:14:"06326025000166";s:15:"cnpj_embarcador";s:0:"";s:18:"cnpj_transportador";s:14:"00599791000118";s:26:"cnpj_gerenciadora_de_risco";s:14:"00000000000000";s:14:"pedido_cliente";s:4:"5000";s:16:"numero_liberacao";s:0:"";s:18:"tipo_de_transporte";s:1:"2";s:10:"observacao";s:0:"";s:20:"controle_temperatura";O:8:"stdClass":2:{s:2:"de";i:0;s:3:"ate";i:0;}s:9:"motorista";O:8:"stdClass":4:{s:4:"nome";s:5:"TESTE";s:3:"cpf";s:11:"64484483300";s:8:"telefone";s:0:"";s:5:"radio";s:0:"";}s:8:"veiculos";O:8:"stdClass":1:{s:5:"placa";a:1:{i:0;s:7:"ABC3333";}}s:6:"origem";O:8:"stdClass":11:{s:14:"codigo_externo";s:3:"101";s:9:"descricao";s:12:"TESTE ORIGEM";s:10:"logradouro";s:5:"TESTE";s:6:"numero";i:101;s:11:"complemento";s:0:"";s:6:"bairro";s:5:"Saude";s:3:"cep";s:0:"";s:6:"cidade";s:9:"Sao Paulo";s:6:"estado";s:2:"SP";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";}s:17:"monitorar_retorno";b:0;s:20:"data_previsao_inicio";s:19:"20/12/2013 15:00:00";s:17:"data_previsao_fim";s:19:"20/12/2013 18:00:00";s:10:"itinerario";O:8:"stdClass":1:{s:4:"alvo";O:8:"stdClass":14:{s:14:"codigo_externo";s:3:"102";s:10:"logradouro";s:18:"alameda dos guatas";s:6:"numero";i:102;s:11:"complemento";s:0:"";s:6:"bairro";s:5:"Saude";s:6:"cidade";s:9:"Sao Paulo";s:6:"estado";s:2:"SP";s:8:"latitude";s:0:"";s:9:"longitude";s:0:"";s:11:"tipo_parada";s:1:"2";s:13:"janela_inicio";s:0:"";s:10:"janela_fim";s:0:"";s:19:"previsao_de_chegada";s:19:"20/12/2013 18:00:00";s:14:"dados_da_carga";O:8:"stdClass":0:{}}}s:5:"iscas";O:8:"stdClass":0:{}s:7:"escolta";O:8:"stdClass":0:{}}');
		$retorno = $this->SmSoap->incluirSM($obj);

		debug($retorno);
		exit;
	}

	function janela_mapa($titulo = null, $latitude = 0, $longitude = 0, $marker_title = '') {
		$this->layout = false;
		$this->pageTitle = empty($titulo) ? 'Localização no mapa' : $titulo;
		$this->set(compact('latitude', 'longitude', 'marker_title','titulo'));
	}

	function string($objetos) {
		$string = "";
		foreach ($objetos as $objeto) {
			$string .= "<ObjetoAcl>";
			$string .= "<descricao>{$objeto['ObjetoAcl']['descricao']}</descricao>";
			$string .= "<aco_string>{$objeto['ObjetoAcl']['aco_string']}</aco_string>";
			$dependencias = "";
			foreach ($objeto['DependenciaObjAcl'] as $dependencia) {
				$dependencias .= "<aco_string>{$dependencia['aco_string']}</aco_string>";
			}
			$string .= "<Dependencias>$dependencias</Dependencias>";
			if (count($objeto['children']) > 0)
				$string .= $this->string($objeto['children']);
			$string .= "</ObjetoAcl>";
		}
		return $string;
	}

	function build_acl() {
		if (!Configure::read('debug')) {
			return $this->_stop();
		}
		$log = array();
		echo "Não utilizar mais esta action, use o shell MIGRATION";exit;
		$aco =& $this->Acl->Aco;
		$root = $aco->node('buonny');
		if (!$root) {
			$aco->create(array('parent_id' => null, 'model' => null, 'alias' => 'buonny'));
			$root = $aco->save();
			$root['Aco']['id'] = $aco->id;
			$log[] = 'Created Aco node for buonny';
		} else {
			$root = $root[0];
		}

		App::import('Core', 'File');
		$Controllers = App::objects('controller');
		$appIndex = array_search('App', $Controllers);
		if ($appIndex !== false ) {
			unset($Controllers[$appIndex]);
		}
		$baseMethods = get_class_methods('Controller');
		$baseMethods[] = 'build_acl';

		$Plugins = $this->_getPluginControllerNames();
		$Controllers = array_merge($Controllers, $Plugins);

		// look at each controller in app/controllers
		foreach ($Controllers as $ctrlName) {
			$methods = $this->_getClassMethods($this->_getPluginControllerPath($ctrlName));

			// Do all Plugins First
			if ($this->_isPlugin($ctrlName)){
				$pluginNode = $aco->node('buonny/'.$this->_getPluginName($ctrlName));
				if (!$pluginNode) {
					$aco->create(array('parent_id' => $root['Aco']['id'], 'model' => null, 'alias' => $this->_getPluginName($ctrlName)));
					$pluginNode = $aco->save();
					$pluginNode['Aco']['id'] = $aco->id;
					$log[] = 'Created Aco node for ' . $this->_getPluginName($ctrlName) . ' Plugin';
				}
			}
			// find / make controller node
			$controllerNode = $aco->node('buonny/'.$ctrlName);
			if (!$controllerNode) {
				if ($this->_isPlugin($ctrlName)){
					$pluginNode = $aco->node('buonny/' . $this->_getPluginName($ctrlName));
					$aco->create(array('parent_id' => $pluginNode['0']['Aco']['id'], 'model' => null, 'alias' => $this->_getPluginControllerName($ctrlName)));
					$controllerNode = $aco->save();
					$controllerNode['Aco']['id'] = $aco->id;
					$log[] = 'Created Aco node for ' . $this->_getPluginControllerName($ctrlName) . ' ' . $this->_getPluginName($ctrlName) . ' Plugin Controller';
				} else {
					$aco->create(array('parent_id' => $root['Aco']['id'], 'model' => null, 'alias' => $ctrlName));
					$controllerNode = $aco->save();
					$controllerNode['Aco']['id'] = $aco->id;
					$log[] = 'Created Aco node for ' . $ctrlName;
				}
			} else {
				$controllerNode = $controllerNode[0];
			}

			//clean the methods. to remove those in Controller and private actions.
			foreach ($methods as $k => $method) {
				if (strpos($method, '_', 0) === 0) {
					unset($methods[$k]);
					continue;
				}
				if (in_array($method, $baseMethods)) {
					unset($methods[$k]);
					continue;
				}
				$methodNode = $aco->node('buonny/'.$ctrlName.'/'.$method);
				if (!$methodNode) {
					$aco->create(array('parent_id' => $controllerNode['Aco']['id'], 'model' => null, 'alias' => $method));
					$methodNode = $aco->save();
					$log[] = 'Created Aco node for '. $method;
				}
			}
		}

		$obj = $aco->node('buonny/obj_operador-buonnysat');
		if (!$obj) {
			$aco->create(array('parent_id' => 1, 'model' => null, 'alias' => 'obj_operador-buonnysat'));
			$methodNode = $aco->save();
			$log[] = 'Created Aco node for obj_operador-buonnysat';
		}
		$obj = $aco->node('buonny/obj_acionamento-buonnysat');
		if (!$obj) {
			$aco->create(array('parent_id' => 1, 'model' => null, 'alias' => 'obj_acionamento-buonnysat'));
			$methodNode = $aco->save();
			$log[] = 'Created Aco node for obj_operador-buonnysat';
		}
		$obj = $aco->node('buonny/obj_operador-pronta-resposta');
		if (!$obj) {
			$aco->create(array('parent_id' => 1, 'model' => null, 'alias' => 'obj_operador-pronta-resposta'));
			$methodNode = $aco->save();
			$log[] = 'Created Aco node for obj_operador-buonnysat';
		}

		$Uperfil = ClassRegistry::init('Uperfil');
		if ($Uperfil->criarAdmin())
			$log[] = 'Created Admin Role';

		if(count($log)>0) {
			debug($log);
		}
		exit;
	}

	function _getClassMethods($ctrlName = null) {
		App::import('Controller', $ctrlName);
		if (strlen(strstr($ctrlName, '.')) > 0) {
			// plugin's controller
			$num = strpos($ctrlName, '.');
			$ctrlName = substr($ctrlName, $num+1);
		}
		$ctrlclass = $ctrlName . 'Controller';
		$methods = get_class_methods($ctrlclass);

		// Add scaffold defaults if scaffolds are being used
		$properties = get_class_vars($ctrlclass);
		if (array_key_exists('scaffold',$properties)) {
			if($properties['scaffold'] == 'admin') {
				$methods = array_merge($methods, array('admin_add', 'admin_edit', 'admin_index', 'admin_view', 'admin_delete'));
			} else {
				$methods = array_merge($methods, array('add', 'edit', 'index', 'view', 'delete'));
			}
		}
		return $methods;
	}

	function _isPlugin($ctrlName = null) {
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) > 1) {
			return true;
		} else {
			return false;
		}
	}

	function _getPluginControllerPath($ctrlName = null) {
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) == 2) {
			return $arr[0] . '.' . $arr[1];
		} else {
			return $arr[0];
		}
	}

	function _getPluginName($ctrlName = null) {
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) == 2) {
			return $arr[0];
		} else {
			return false;
		}
	}

	function _getPluginControllerName($ctrlName = null) {
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) == 2) {
			return $arr[1];
		} else {
			return false;
		}
	}

/**
 * Get the names of the plugin controllers ...
 *
 * This function will get an array of the plugin controller names, and
 * also makes sure the controllers are available for us to get the
 * method names by doing an App::import for each plugin controller.
 *
 * @return array of plugin names.
 *
 */

function consultar_documentos()   {
	$this->pageTitle = "Documentos";
	$usuario = $this->BAuth->user();
	$url = array('controller'=>$this->name, 'action'=>'excluir_documentos_rh');
	$temPermissao = $this->BAuth->temPermissao($usuario['Usuario']['codigo_uperfil'], $url);
	$diretorio = APP . "webroot".DS."documentos";
	$arquivos=glob($diretorio.DS.'*.*');
	$lista_arquivos=array();

	foreach ($arquivos as $arquivo)
	{
			//$separa = explode('/',$arquivo);
		$separa = explode(DS,$arquivo);
		$lista_arquivos[array_pop($separa)] = $arquivo;
	}
	$this->set(compact('lista_arquivos','temPermissao'));
}

function download_documento($arquivo){
	$arquivo = urldecode($arquivo);
	$this->set(compact('arquivo'));
}

public function excluir_documentos_rh($arquivo)	{
	$diretorio = APP . "webroot".DS."documentos".DS.$arquivo;
	if($diretorio==true){
		unlink($diretorio);
		$this->BSession->setFlash("delete_success");
	}
	else {
		$this->BSession->setFlash("delete_error");
	}

	$this->redirect(array('action'=>'consultar_documentos'));

}

function _getPluginControllerNames() {
	App::import('Core', 'File', 'Folder');
	$paths = Configure::getInstance();
	$folder =& new Folder();
	$folder->cd(APP . 'plugins');

		// Get the list of plugins
	$Plugins = $folder->read();
	$Plugins = $Plugins[0];
	$arr = array();

		// Loop through the plugins
	foreach($Plugins as $pluginName) {
			// Change directory to the plugin
		$didCD = $folder->cd(APP . 'plugins'. DS . $pluginName . DS . 'controllers');
			// Get a list of the files that have a file name that ends
			// with controller.php
		$files = $folder->findRecursive('.*_controller\.php');

			// Loop through the controllers we found in the plugins directory
		foreach($files as $fileName) {
				// Get the base file name
			$file = basename($fileName);

				// Get the controller name
			$file = Inflector::camelize(substr($file, 0, strlen($file)-strlen('_controller.php')));
			if (!preg_match('/^'. Inflector::humanize($pluginName). 'App/', $file)) {
				if (!App::import('Controller', $pluginName.'.'.$file)) {
					debug('Error importing '.$file.' for plugin '.$pluginName);
				} else {
						/// Now prepend the Plugin name ...
						// This is required to allow us to fetch the method names.
					$arr[] = Inflector::humanize($pluginName) . "/" . $file;
				}
			}
		}
	}
	return $arr;
}

function descriptografar(){
	$descriptografado = '';
	if (!empty($this->data)) {
		App::import('Vendor', 'encriptacao');
		$encriptacao = new Buonny_Encriptacao();
		$descriptografado = $encriptacao->desencriptar($this->data['Sistema']['texto']);
	}
	$this->set(compact('descriptografado'));
}

public function descriptografar_teste($senha="null"){

	$this->layout = false;

	echo "opa<br>";

	 $senha = 'DStMEzt72R6Mw8ecDr2BxUhnT97gmjmvj1SHBviNR3LPNBLqg0ZhjVwy51ZH9Gpj26DtEoUbkwn3HTdSYZAZ1vDz5mZLpVWPTldTOBklLH4tU6a7Ll7+fls61VLg51RPtqkfuN4+6YA/fXBgSFiPuJie9cxf2D+VZxvo8vzwpgI=';
	
	// $senha = 's+2Md6d6YBYg1taq+u8M5zCnT0WU1ybcR2D8vAmdZTU0jU7AgjQW5kytMIyNBsZpEkoFvgr4bIGsFfk86pOjzBhS6337BEcobhb1CNPQwCBNGsa5gQJzTzofb4gD+iP1Wy4uDdx9lJsZGNTgtBxnrpbxp4cMHjA4bhQU8H/F7Vw=';

	App::import('Vendor', 'encriptacao');
	$encriptacao = new Buonny_Encriptacao();
	$descriptografado = $encriptacao->desencriptar($senha);

	echo $descriptografado;
	exit;
	
}


function criptografar(){
	$criptografado = '';
	if (!empty($this->data)) {
		App::import('Vendor', 'encriptacao');
		$encriptacao = new Buonny_Encriptacao();
		$criptografado = $encriptacao->encriptar($this->data['Sistema']['texto']);
	}
	$this->set(compact('criptografado'));
}

function criptografar_teste(){
	
	App::import('Vendor', 'encriptacao');
	$encriptacao = new Buonny_Encriptacao();

	$array = array(
		"teste para o joão",
		"teste para o joão teste para o joão teste para o joão teste para o joão ",
		"willians paulo pedroso",
		"aqui é o teste para tentar descriptografar",
		"agora vai...",
		'{
    "status": 200,
    "result": {
        "data": {
            "codigo_usuario": 73260,
            "nome": "Segurança Thermal Care",
            "email": "pablojsalcantara@gmail.com",
            "data_nascimento": null,
            "celular": null,
            "telefone": null,
            "cpf": null,
            "sexo": null,
            "notificacao": "0",
            "cliente": [],
            "contato_emergencia": {
                "nome": null,
                "telefone": null,
                "celular": null,
                "grau_parentesco": null,
                "email": null
            },
            "permissoes": {
                "Lyn": {
                    "menu": [],
                    "skin": []
                }
            }
        }
    }
}'
	);
	$ops = array();
	foreach ($array as $key => $value) {
		$criptografado = $encriptacao->encriptar($value);

		$ops[$key]['texto'] = $value;
		$ops[$key]['cript'] = $criptografado;
		
	}
	
	debug($ops);exit;
}

function zerar_senhas(){
	if (!empty($this->data)) {
		if ($this->data['Sistema']['senha'] == 'zerar') {
			if (Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO) {
				$this->Usuario = ClassRegistry::init('Usuario');
				$this->Usuario->updateAll(
					array('Usuario.senha' => 'tiN5Pp8swP5XXJhUWYEHaVMUbYhGpv18ki8fu5lGZ37dY3nSNFqEFnD+xcPAnlwMTBY99gLqLnUAXSPfwTZAwvqelF7QKolWC8WCjDy+00t2iPngmhUSwtkHs88WXm99sc1A37bVRxnBoSMj4lPBGZLGfxDxIVZauPZ7MaL6wDA='),
					null
					);
				$this->ClienteContato = ClassRegistry::init('ClienteContato');
				$contatos = $this->ClienteContato->find('list', array('conditions' => array('descricao like' => '%@%')));
				foreach ($contatos as $codigo => $field) {
					$this->ClienteContato->read(null, $codigo);
					$this->ClienteContato->saveField('descricao', 'ti.monitora@buonny.com.br');
				}
			}
		}
	}
}

function conversor_folha() {
	$this->pageTitle = 'Conversor Folha';
	if (!empty($this->data)) {
		if ($this->data['Sistema']['arquivo']['name'] != null) {
			if (strpos($this->data['Sistema']['arquivo']['name'], ".xls") > 0) {
				$destino = APP . "/tmp/cache/views/f".time().".tmp";
				if (file_exists($destino))
					unlink($destino);
				move_uploaded_file($this->data['Sistema']['arquivo']['tmp_name'], $destino);
				require_once APP . 'vendors' . DS . 'excel_reader' . DS . 'excel_reader2.php';
				$data = new Spreadsheet_Excel_Reader($destino);
				$conta = $this->data['Sistema']['conta'];
				if ($this->data['Sistema']['tipo_arquivo'] == 1) {
					$arquivo = $this->_sal_file_reader($data, $conta);
				} else {
					$arquivo = $this->_contrib_file_reader($data, $conta);
				}
				Configure::write('debug',0);
				header("Content-Type: application/force-download");
				header('Content-Disposition: attachment; filename="'.str_replace(".xls",".txt", $this->data['Sistema']['arquivo']['name']).'"');
				echo "\n\n\n\n\n".$arquivo."final_planilha\n";
				exit;
			} else {
				$this->BSession->setFlash('invalid_file');
			}
		} else {
			$this->BSession->setFlash('no_file');
		}
	}
}

function upload_documentos_internos()
{
	$this->pageTitle = 'Upload de Documentos';
	if (!empty($this->data)) {
		if ($this->data['Sistema']['arquivo']['name'] != null) {
			if (strpos($this->data['Sistema']['arquivo']['name'],"*.*") !== 0) {
				$destino = APP . "webroot".DS."documentos".DS.$this->data['Sistema']['arquivo']['name'];


				if (move_uploaded_file($this->data['Sistema']['arquivo']['tmp_name'], $destino) == true) {
					$this->BSession->setFlash("envio_arquivo");
				}
				else {
					$this->BSession->setFlash("envio_arquivo_error");
				}

			} else {
				$this->BSession->setFlash('invalid_file');
			}
		} else {
			$this->BSession->setFlash('no_file');
		}
	}


}





function upload_planilha_ramais() {
	$this->pageTitle = 'Upload de Planilha de Ramais';
	$this->loadModel('Uperfil');
	$usuario = $this->BAuth->user();
	$modulo_selecionado=$this->Session->read('modulo_selecionado');
	$this->set(compact('usuario','modulo_selecionado'));
	if (!empty($this->data)) {
		if ($this->data['Sistema']['arquivo']['name'] != null) {
			if (strpos($this->data['Sistema']['arquivo']['name'], ".xls") > 0) {

				if ($this->data['Sistema']['tipo_arquivo'] == 1) {
					$destino = APP . "webroot/ramais/ramais_lider_brasil.xls";
				} elseif ($this->data['Sistema']['tipo_arquivo'] == 2) {
					$destino = APP . "webroot/ramais/ramais_predio_102.xls";
				} elseif ($this->data['Sistema']['tipo_arquivo'] == 3) {
					$destino = APP . "webroot/ramais/ramais_predio_191.xls";
				} elseif ($this->data['Sistema']['tipo_arquivo'] == 4 && $usuario['Usuario']['codigo_uperfil'] == Uperfil::ADMIN ){
					$destino = APP . "webroot/ramais/ramais_ti.xls";
				}


				if (move_uploaded_file($this->data['Sistema']['arquivo']['tmp_name'], $destino) == true) {
					$this->BSession->setFlash("envio_arquivo");
				}
				else {
					$this->BSession->setFlash("envio_arquivo_error");
				}

			} else {
				$this->BSession->setFlash('invalid_file');
			}
		} else {
			$this->BSession->setFlash('no_file');
		}
	}
}


function _sal_file_reader($data, $conta) {
	$rows = $data->rowcount($sheet_index = 0);
	$cols = $data->rowcount($sheet_index = 0);
	$valor = null;
	$depto = "";
	$arquivo = "";
	for($row = 0; $row < $rows; $row++) {
		$celula = $data->val($row,5);
		if (!empty($celula)) {
			$celula = iconv('ISO-8859-1', 'UTF-8', $celula);
			if (substr($celula,0,6) == 'Depto.')
				$depto = $this->_converte_depto_conta_contabil($celula);
		} else {
			$celula = $data->val($row,19);
			if (!empty($celula)) {
				$celula = iconv('ISO-8859-1', 'UTF-8', $celula);
				if (substr($celula,0,12) == 'TOTAL DEPTO:') {
					$valor = str_replace(".", ",", str_replace(",", "", $data->val($row,28)));
				}
			}
		}
		if (!empty($depto) && $valor != null) {
			$arquivo .= $depto.' '.$conta.' '.$valor."\n";
			$depto = "";
			$valor = null;
		}
	}
	return $arquivo;
}

function _contrib_file_reader($data, $conta) {
	$rows = $data->rowcount($sheet_index = 0);
	$cols = $data->rowcount($sheet_index = 0);
	$valor = null;
	$depto = "";
	$arquivo = "";
	for($row = 0; $row < $rows; $row++) {
		$celula = $data->val($row,5);
		if (!empty($celula)) {
			$celula = iconv('ISO-8859-1', 'UTF-8', $celula);
			if (substr($celula,0,6) == 'Depto.') {
				if ($valor > 0) {
					$arquivo .= $depto.' '.$conta.' '.$valor."\n";
					$valor = null;
				}
				$depto = $this->_converte_depto_conta_contabil($celula);
			}
		} else {
			$celula = $data->val($row,44);
			if (!empty($celula)) {
				$celula = iconv('ISO-8859-1', 'UTF-8', $celula);
				$valor += str_replace(",", "", $celula);
			}
		}
	}
	if ($valor > 0)
		$arquivo .= $depto.' '.$conta.' '.$valor."\n";
	return $arquivo;
}

function _converte_depto_conta_contabil($string) {
	$string = str_replace(' -->', '', str_replace(' - Seção: ', '', str_replace(' - Setor: ', '', str_replace('Depto.: ', '', $string))));
	$depto = substr($string,0,4);
	$setor = substr($string,4,4);
	$secao = substr($string,8,4);
	return str_pad($depto + $setor + $secao, 4, '0', STR_PAD_LEFT);
}

function limpa_cache(){
	$this->layout = false;

		// LIMPA DIRETORIO ACL
	$diretorio = APP.'tmp/cache/acl/';

	if(is_dir($diretorio))
	{
		$this->limpar_diretorio_acl($diretorio);
	}
	else
	{
		die("Erro ao abrir dir: $diretorio");
	}
		// FIM DO DIRETORIO ACL

	echo '//----------------------------------------<br>';
	echo '//----------------------------------------<br>';

		// LIMPA DIRETORIO MODELS
	$diretorio = APP.'tmp/cache/models/';

	if(is_dir($diretorio))
	{
		$this->limpar_diretorio_models($diretorio);
	}
	else
	{
		die("Erro ao abrir dir: $diretorio");
	}
		// FIM DO DIRETORIO MODELS

	exit;
}

function limpar_diretorio_acl($diretorio){
	$this->layout = false;
	$scan = scandir($diretorio);
	if(count($scan) > 2){
		if($handle = opendir($diretorio))
		{
			while(($file = readdir($handle)) !== false)
			{
				if($file != '.' && $file != '..')
				{
					if(is_dir($diretorio.$file)){
						$this->limpar_diretorio_acl($diretorio.$file.'/');
						$scan = scandir($diretorio);
						if(count($scan) > 2){
							if(rmdir($diretorio))
								echo '---Diretorio: '.$diretorio.' removido<br />';
							else
								echo '---Diretorio: '.$diretorio.' não pode ser removido<br />';
						}
					}else{
						if(unlink($diretorio.$file))
							echo 'Arquivo: '.$diretorio.$file.' removido<br />';
						else
							echo 'Arquivo: '.$diretorio.$file.' não pode ser removido<br />';
					}
				}
			}
		}
	} else {
		if(rmdir($diretorio))
			echo '---Diretorio: '.$diretorio.' removido<br />';
		else
			echo '---Diretorio: '.$diretorio.' não pode ser removido<br />';
	}
}

function limpar_diretorio_models($diretorio){
	$this->layout = false;
	$scan = scandir($diretorio);
	if(count($scan) > 2){
		if($handle = opendir($diretorio))
		{
			while(($file = readdir($handle)) !== false)
			{
				if($file != '.' && $file != '..')
				{
					if(is_dir($diretorio.$file)){
						$this->limpar_diretorio_models($diretorio.$file.'/');
					}else{
						str_replace('cake_model', '', $file, $cake_model);
						if($cake_model){
							if(unlink($diretorio.$file))
								echo 'Arquivo: '.$diretorio.$file.' removido<br />';
							else
								echo 'Arquivo: '.$diretorio.$file.' não pode ser removido<br />';
						}
					}
				}
			}
		}
	}
}

function indentificar_codigos_clientes() {
	$this->pageTitle = 'Rastrear Clientes';
	$this->loadModel('Cliente');
	$this->loadModel('ClientEmpresa');
	if ($this->RequestHandler->isPost()) {
		if (!empty($this->data['Cliente']['codigo_cliente'])) {
			$cliente = $this->Cliente->carregar($this->data['Cliente']['codigo_cliente']);
			$this->Cliente->bindModel(
				array(
					'hasOne' => array(
						'ClienteProduto' => array('foreignKey' => 'codigo_cliente', 'conditions' => array('codigo_produto' => 82))
						),
					)
				);
			$clientes_dbbuonny = $this->Cliente->find('all', array('conditions' => array('codigo_documento LIKE' => substr($cliente['Cliente']['codigo_documento'],0,7).'%' ), 'order' => 'codigo_documento'));
			$clientes_monitora = $this->ClientEmpresa->find('all', array('fields' => array('Codigo', 'Raz_Social', 'CNPJCPF', 'TipoEmpresa', 'Status', 'BloqFinanc'), 'conditions' => array("REPLACE(REPLACE(REPLACE(cnpjcpf,'.',''),'/',''),'-','') LIKE" => substr($cliente['Cliente']['codigo_documento'],0,7).'%' ), 'order' => 'cnpjcpf'));
		} else {
			$this->Cliente->invalidate('codigo_cliente', 'Informe o código');
		}

	}
	$this->set(compact('clientes_dbbuonny', 'clientes_monitora'));
}

function lista_ramais($file) {
	if(!strpos($file, 'xls')) $file .= '.xls';
	require_once APP . 'vendors' . DS . 'excel_reader' . DS . 'excel_reader2.php';
	$data = new Spreadsheet_Excel_Reader(APP.'webroot'.DS.'ramais'.DS.$file);
	echo $data->dump(false, false, 0);
	exit;
}

function lista_log(){
	$this->pageTitle 	= "Log Cake";
	$usuario 			= $this->BAuth->user();
	$url 				= array('controller'=>$this->name, 'action'=>'excluir_log');
		//$temPermissao = $this->BAuth->temPermissao($usuario['Usuario']['codigo_uperfil'], $url);
	$diretorio 			= APP."tmp".DS."logs";
	$arquivos 			= glob($diretorio.DS.'*.log');
	
	$lista_arquivos		=array();
	$lista_arquivos 	= $arquivos;
	
	$this->set(compact('arquivos'));

	foreach ($arquivos as $arquivo)
	{

		$separa 		= explode(DS, $arquivo);
		
			//$lista_arquivos[array_pop($separa[11])] = $separa[11];
		$localiza 		= array_pop($separa);
		$lista_arquivos	= $localiza;
		
		$this->set(compact($lista_arquivos));

		if($lista_arquivos == 'debug.log'){
			$debug_log 		=file($diretorio.DS.'debug.log');
			$this->set(compact('debug_log'));
		}

		if($lista_arquivos == 'error.log'){
			$error_log 		= file($diretorio.DS.'error.log');
			$this->set(compact('error_log'));
		}

		if($lista_arquivos =='ldap.error.log'){
			$ldap_error_log = file($diretorio.DS.'ldap.error.log');
			$this->set(compact('ldap_error_log'));
		}
	}
}

public function excluir_log($nome_arquivo)	{

	$diretorio 	= APP."tmp".DS."logs".DS.$nome_arquivo.".log";
	if($diretorio==true){
		unlink($diretorio);
		$this->BSession->setFlash("delete_success");
		$this->redirect(array('action'=>'lista_log'));
	}
	else {
		$this->BSession->setFlash("delete_error");
	}
	$this->redirect(array('action'=>'lista_log'));
}

function tarefas_desenvolvimento(){
	$this->loadModel('TarefaDesenvolvimento');
	$this->loadModel('TarefaDesenvolvimentoTipo');
	$this->loadModel('Usuario');
	$tipo = $this->TarefaDesenvolvimentoTipo->listarTarefasDesenvolvimentoTipo();

	$this->pageTitle = 'Controle de Tarefas ';
	$this->data['TarefaDesenvolvimento'] = $this->Filtros->controla_sessao($this->data, 'TarefaDesenvolvimento');

	$nome_usuario 	= $this->Usuario->listarUsuariosTarefas();

	$this->set(compact('nome_usuario','tipo'));

}

function listar_tarefas_desenvolvimento(){

	$this->loadModel('TarefaDesenvolvimento');
	$this->layout 							 = 'ajax';
	$authUsuario 							 = $this->BAuth->user();
	$filtros 								 = $this->Filtros->controla_sessao($this->data, 'TarefaDesenvolvimento');
	$params 	 							 = $this->TarefaDesenvolvimento->listarTarefasDesenvolvimento($filtros);
	$count 									 = $this->TarefaDesenvolvimento->contarTarefaDesenvolvimento($filtros);
	$this->paginate['TarefaDesenvolvimento'] = $params;
	$lista_tarefas 							 = $this->paginate('TarefaDesenvolvimento');
	$this->set(compact('lista_tarefas','count'));
}

function incluir_tarefas_desenvolvimento(){
	$this->loadModel('TarefaDesenvolvimento');
	$this->loadModel('TarefaDesenvolvimentoTipo');
	$this->pageTitle	= 'Inclusão de Tarefa';

	if ($this->data){
			// if(Ambiente::getServidor() ==  Ambiente::SERVIDOR_PRODUCAO){
		$this->data['TarefaDesenvolvimento']['codigo_usuario_inclusao'] = $this->authUsuario['Usuario']['codigo'];

		if ($this->TarefaDesenvolvimento->incluir($this->data)) {
			$this->BSession->setFlash('save_success');
			$this->redirect(array('controller' => 'Sistemas','action' => 'tarefas_desenvolvimento'));

		} else {
			$this->BSession->setFlash('save_error');
		}
			// }else{
			// 	$this->BSession->setFlash(array(MSGT_ERROR, 'Não é possível incluir tarefa fora do ambiente de produção.'));
			// }

	}

	$tipo = $this->TarefaDesenvolvimentoTipo->listarTarefasDesenvolvimentoTipo();
	$this->set(compact('tipo'));
}

function editar_tarefas_desenvolvimento($codigo){
	$this->loadModel('TarefaDesenvolvimento');
	$this->loadModel('TarefaDesenvolvimentoTipo');
	$this->pageTitle	= 'Atualizar Tarefa';

	if($this->data){

		$this->data['TarefaDesenvolvimento']['data_alteracao']	= date(" Y-m-d H:i:s");
		if ($this->TarefaDesenvolvimento->atualizar($this->data)) {
			$this->BSession->setFlash('save_success');
			$this->redirect(array('controller' => 'Sistemas' ,'action' => 'tarefas_desenvolvimento'));

		} else {

			$this->BSession->setFlash('save_error');
		}
	} else {
		$this->data = $this->TarefaDesenvolvimento->carregar($codigo);
	}

	$tipo = $this->TarefaDesenvolvimentoTipo->listarTarefasDesenvolvimentoTipo();
	$this->set(compact('codigo','tipo'));
}


function editar_status_tarefas_desenvolvimento($codigo,$status){
	$this->loadModel('TarefaDesenvolvimento');

	$this->data['TarefaDesenvolvimento']['codigo']			= $codigo;
	$this->data['TarefaDesenvolvimento']['status']			= $status;
	$this->data['TarefaDesenvolvimento']['data_alteracao']	= date(" Y-m-d H:i:s");

	if (!empty($this->data)){

		if ($this->TarefaDesenvolvimento->atualizar($this->data)) {
			$this->BSession->setFlash('save_success');
			$this->redirect(array('controller' => 'Sistemas', 'action' => 'listar_tarefas_desenvolvimento'));

		} else {
			$this->BSession->setFlash('save_error');
		}
	}
}

function plano_senhas(){
	$this->pageTitle	= 'Plano de Senhas';

	$this->loadModel('Uperfil');
	$usuario = $this->BAuth->user();
	$this->set(compact('usuario'));

	$diretorio 			= APP."webroot".DS."files".DS."plano_senhas";
	$arquivos 			= glob($diretorio.DS.'*.*');
	$lista_arquivos     = array();
	$localiza 			= array();
	foreach ($arquivos as $arquivo)
	{
		$separa = explode(DS,$arquivo);
		$localiza[] = array_pop($separa);
		$pos = strpos($arquivo,'-');
		$separa = substr($arquivo,$pos);
		$separa = explode('-',$separa);
		$lista_arquivos[array_pop($separa)] = $arquivo;
	}
	$this->set(compact('lista_arquivos','localiza'));
}

function gpa_files_check() {
	if ($_SERVER['SERVER_NAME'] == 'portal.buonny.com.br') {
		$this->redirect('http://portal1.buonny.com.br/sistemas/gpa_files_check');
	}
	$this->loadModel('LogIntegracao');
	$path = DS.'home'.DS.'paodeacucar'.DS.'gpa'.DS;
	$extension = 'dat';
	$diretorioProcessado = $path.'processado';
	$lista = glob($diretorioProcessado.DS.'*.'.$extension);
	echo "<h3>Arquivos não integrados</h3>";
	foreach ($lista as $key => $value) {
		$data_arquivo = date("Ymd", filemtime($value));
		if (date('Ymd') ==  $data_arquivo || date('Ymd', strtotime('-1 day')) == $data_arquivo) {
			$arquivo = end(explode(DS, $value));
			$conditions = array('arquivo' => $arquivo, 'data_inclusao >=' => date("Ymd H:i:00", filemtime($value)) );
			$existe = $this->LogIntegracao->find('count', array('conditions' => $conditions));
			if (!$existe) {
				echo $arquivo." ".date("Y-m-d H:i:s", filemtime($value))."<br>";
			}
		}
	}
	exit;
}

function lg_files_check() {
	if ($_SERVER['SERVER_NAME'] == 'portal.buonny.com.br') {
		$this->redirect('http://portal1.buonny.com.br/sistemas/lg_files_check');
	}
	$this->loadModel('LogIntegracao');
	$path = DS.'home'.DS.'lg'.DS.'sm'.DS;
	$extension = 'xml';
	$diretorioProcessado = $path.'processado';
	$lista = glob($diretorioProcessado.DS.'*.'.$extension);
	echo "<h3>Arquivos não integrados</h3>";
	foreach ($lista as $key => $value) {
		if (date('Ymd') == date("Ymd", filemtime($value)) || date('Ymd', strtotime('-1 day')) == date("Ymd", filemtime($value))) {
			$arquivo = end(explode(DS, $value));
			$conditions = array('arquivo' => $arquivo);
			$existe = $this->LogIntegracao->find('count', array('conditions' => $conditions));
			if (!$existe) {
				echo $arquivo."<br>";
			}
		}
	}
	exit;
}

function localizar_arquivo_lg() {
	if ($_SERVER['SERVER_NAME'] == 'portal.buonny.com.br') {
		$this->redirect('http://portal1.buonny.com.br/sistemas/lg_files_check');
	}
	$this->pageTitle = 'Localizador arquivo LG';
	$enviada = "";
	$processado = "";
	if (!empty($this->data)) {
		$cmd_enviada = "grep -il '{$this->data['Sistema']['content']}' /home/lg/sm/enviada/*";
		$cmd_processado = "grep -il '{$this->data['Sistema']['content']}' /home/lg/sm/processado/*";
		$processado = shell_exec($cmd_processado);
		$enviada = shell_exec($cmd_enviada);
	}
	$this->set(compact('processado', 'enviada'));
}

	/*
	* Funcao que troca o Status dos Criterios do Scorecard
	*
	* parametros:   ?criterio=xx&status=0/1
	*/
	function trocar_status_criterios($criterio=null,$status=null,$usuario=null) {
		$this->layout = false;
		$this->autoRender = false;
		//34986 e 34948 restrito ate achar uma forma
		$authUser = $this->BAuth->user();
		if ($authUser['Usuario']['codigo']=='34986' || $authUser['Usuario']['codigo']=='34948') {
			//Checa o Get
			if(isset($this->params['url']['criterio']) && isset($this->params['url']['status'])) {
				//Carrega a Model
				$this->loadModel('Criterio');
				$this->Criterio->id = $this->params['url']['criterio'];
				$salvar = $this->Criterio->savefield('campo_sistema',$this->params['url']['status']);
				echo "<h1>Mensagem</h1>";
				if ($salvar) {
					echo "<h2>Critério = ".$this->params['url']['criterio']." alterado com sucesso!</h2>";
				} else {
					die("<h2>Erro ao mudar o Status do Critério!</h2>");
				}
			} else {
				$resul1=' - Indicar o Critério'; $resul2=' - Indicar o Status';
				if (isset($this->params['url']['criterio'])) { $resul1='- Ok';}
				if (isset($this->params['url']['status'])) { $resul2='- Ok';}
				echo "<ul><h2>Necessita Parametros:</h2><ul>criterio $resul1 <br/>status $resul2</ul></ul>";
				$this->loadModel('Criterio');
				echo '<ul><table border="1" width="400">';
				echo '<thead><td colspan="3" align="center"><span style="font-size:26px;"><b>Tabela de Critérios do Scorecard</b></span></td></thead><tbody>';
				echo "<tr><td>Critério</td><td>Descrição</td><td>Status</td></tr>";
				$criterio_lista  = $this->Criterio->find('all',array('fields'=>array('codigo','descricao','campo_sistema'),'order'=>array('campo_sistema'=>'DESC','descricao'=>'ASC')));
				foreach ($criterio_lista as $key => $criterio['Criterio']) {
					echo '<tr>';
					echo '<td>'.$criterio['Criterio']['Criterio']['codigo']. '</td>';
					echo '<td>'.$criterio['Criterio']['Criterio']['descricao'].'</td>';
					echo '<td>'. (($criterio['Criterio']['Criterio']['campo_sistema'])?'SIM':'NAO') .'</td>';
					echo '</tr>';
				}
				echo "</tbody></table></ul>";
			}
		} else {
			echo "<h1>Necessita Permissão.</h1><h2>Checar SistemasController</h2> <br/>";
		}
	}

	function getLastQuery1() {
		$dbo = $this->getDatasource();
		$logs = $dbo->_queriesLog;
		return end($logs);
	}

	function getLastQuery2() {
		$dbo = $this->getDatasource();
		$logs = $dbo->getLog();
		$lastLog = end($logs['log']);
		return $lastLog['query'];
	}

	public function srvinfo() {
    	$this->autoRender = false;
    	phpinfo();
    }

    /**
     * [create_table_log description]
     * 
     * 
     * 
     * @param  [type] $table [description]
     * @return [type]        [description]
     */
    public function create_table_log($model_name = null)
    {

    	
    	if(is_null($model_name)) {
    		print "Necessario passar a model que ira gerar o log: por exemplo Cliente irá gerar a tabela cliente_log.";
    		exit;
    	}

    	$tabela = ClassRegistry::Init($model_name);

    	// debug($tabela);exit;
		
		$chave = $tabela->primaryKey."_".$tabela->useTable;

		$script = "<pre>CREATE TABLE RHHealth.dbo.{$tabela->useTable}_log( "."<br>";
		$script .= "	codigo int IDENTITY(1,1) NOT NULL,"."<br>";

		//varre as colunas para montar o script
		foreach($tabela->_schema as $campo => $dados){

			// $script .= "codigo int IDENTITY(1,1) NOT NULL,<br>";
			// codigo_lista_de_preco int NOT NULL,
			
			$dados['null'] = 'NOT NULL';
			if($dados['null'] == '1') {
				$dados['null'] = 'NULL';
			}

			if(isset($dados['key'])) {
				if($dados['key'] == 'primary')
					$campo = $chave;				
			}


			$script .= "	{$campo} {$dados['type']}({$dados['length']}) {$dados['null']},"."<br>";

		}//fim foreach

		$script .= ");</pre>"."<br>";

		print $script;

		exit;

		



    }//fim table


    function dispara_push() {
		$this->loadModel('PushOutbox');

		// $serverKey = 'AAAA-BG54JE:APA91bEXcoLwKBy8C2t2YkqzLv3qU8ZlgUGrB3durIlVHGjByZHDnnQ6eTR57ODpfGf_PGc1fN0nW5z-USHiCs_fLJO7Hb6dWOosvw8QTU-s_nGy6XCpkJEhs_Q0PJ1q_tr2PAKKje_E';

		$serverKey = 'AAAAdh-XJiA:APA91bFdoo4u1bKoauh4hUWdsOI4CZ-EfHCvmnyRbIWlsvoNQOEeY5JyWK3TmLLUa1JHpg4Erj9SllhMkN53XQiINnVi9u0MH6YJScRY_91jlxA_J76BgVIUhCCWJbLFFPUOuJkIutk4';

		$push_keys = Array(
			'fdcHH8B9z0E:APA91bF0KlxOyJTMuVKTi_57hKBxONSPtoQ1DiL7T1bW8UTyDYXkpqr29_OzpHdvDn0KnLH8pDfkFBiekMrePIgXNGCwZtxGcRA3f7CzsgfRpy_14Kt_CoatgiLZ39BTvM2fum8YVLTi',
			'f--01yhrMw4:APA91bHkbKzBBU0KpMY5ZeeH6sumaL_tppBht6C9B7JQSl5AM4ZoNmkb42M7Dwg4XS4nNILt7fn8kvKfiSsi13zOAsK9vEXjwUClm4xA9a-XFGs3fpZydS1vffAsdVLcInszTK0tCYMt'
		);

		echo $this->PushOutbox->sendPushNotificationFCM($serverKey, $push_keys, "Teste", "Teste", Array(), "android");
		exit;
	}

	public function azure_push()
	{

		$regIds = $this->data['Sistema']['regIds'];
		$titulo = $this->data['Sistema']['titulo'];
		$mensagem = $this->data['Sistema']['mensagem'];
		$platform = $this->data['Sistema']['platform'];


		$resultado = '';

		if(!empty($regIds) && !empty($titulo) && !empty($mensagem) && !empty($platform)) {
			
			// $serverKey = 'MxUlE6Ug3y4QQv/pL9GZcingmMhTY8DwLw3KaBe3WGwa28fxDIaeVg==';
			$serverKey = 'Pw2ibrXtRA6ae/0Vpaq8kEKjraXMEOJ80ev/R3FVVrIMlNwTPuvdaw==';

			// debug(array($serverKey,$regIds,$titulo,$mensagem,$platform));exit;
			$this->loadModel('PushOutbox');
			$resultado = $this->PushOutbox->sendPushNotificationAzure($serverKey, $regIds, $titulo, $mensagem, Array(), $platform);
			
			if(empty($resultado)) {
				$resultado = "Erro ao enviar msg";
			}
			
		}

		
		$this->set(compact('resultado'));
	}

	function links_demonstrativo($mes_referencia,$ano_referencia,$codigo_cliente_pagador)
	{
		
		$this->loadModel('Pedido');

		// $codigo_cliente_pagador  = '80618';

		// $mes_referencia = '02';
		// $ano_referencia = '2020';
		
		$pedidos_rhhealth = $this->Pedido->find('all', 
            array(                
                'conditions' => 
                    array(
                        'Pedido.manual' => '0' ,
                        'Pedido.mes_referencia' => $mes_referencia,
                        'Pedido.ano_referencia' => $ano_referencia,
                        'Pedido.codigo_cliente_pagador IN ('.$codigo_cliente_pagador.')'

                    )
            )); 

        //verifica se existe pedido automatico 
        if(empty($pedidos_rhhealth)) {
        	print "Não tem pedidos para este cliente";
            exit;
        }

        $mes_ped_comp = 0;
        $ano_ped_comp = 0;
        $mes_ped_capita = 0;
        $ano_ped_capita = 0;

        $codigo_servico_percapita = 0;
        $codigo_servico_complementar = 0;

        //Recupera as datas de referencia dos pedidos
        foreach($pedidos_rhhealth as $ped){

        	$cliente_pagador = $ped['Pedido']['codigo_cliente_pagador'];

            //pedido per capita
            if($ped['Pedido']['codigo_servico'] == '001'){
                $mes_ped_capita = $ped['Pedido']['mes_referencia'];
                $ano_ped_capita = $ped['Pedido']['ano_referencia'];
                // $codigo_servico_percapita = '001';

                //seta o mes e o ano
            
                if(!empty($mes_ped_capita) && !empty($ano_ped_capita)){
                    $mes = $mes_ped_capita;
                    $ano = $ano_ped_capita;

                } else {
                    $mes =  Date('m', $base_periodo);
                    $ano =  Date('Y', $base_periodo);
                }

                $links[$cliente_pagador]['percapita'] = $this->linkDemonstrativoPercapita($cliente_pagador,$mes, $ano);
            }

            //pedido de exame complementar
            if($ped['Pedido']['codigo_servico'] == '002'){
   
                $mes_ped_comp = $ped['Pedido']['mes_referencia'];
                $ano_ped_comp = $ped['Pedido']['ano_referencia'];
                // $codigo_servico_complementar = '002';
                //seta a data de inicio/fim
                if(!empty($mes_ped_comp) && !empty($ano_ped_comp)){
                    $data_inicial = Date('Ym01', mktime(0,0,0,$mes_ped_comp,1,$ano_ped_comp));
                    $data_fim = Date('Ymt', mktime(0,0,0,$mes_ped_comp,1,$ano_ped_comp));

    
                } else {
                    $data_inicial = Date('Ym01', $base_periodo);
                    $data_fim = Date('Ymt', $base_periodo);
                }                        
                //demonsrtativo exames complementares
                $links[$cliente_pagador]['exame_complementar'] = $this->linkDemonstrativoExamesComplementares($cliente_pagador,$data_inicial, $data_fim);
            }
        }

        print"<pre>";
        print_r($links);
        exit;

	}// fim demonstrativo_percapita

	/**
     * @param  [codigo_cliente] codigo do clietne que irá gerar o hash
     * @param  [data_inicial] data inicial do demonstrativo
     * @param  [data_final] data final do demonstrativo
     * @return [link] link para acessar o relatorio de demonstrativo
     */
    private function linkDemonstrativoExamesComplementares($codigo_cliente, $data_inicial, $data_final) {
        //monta o hash para colocar no link
        $hash = Comum::geraParametroLinkDemonstrativoExameComplementar('demonstrativos', $codigo_cliente, $data_inicial, $data_final);
        //monta o host
        // $host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "portal.rhhealth.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "tstportal.rhhealth.com.br" : "portal.localhost"));
        $host = "portal.rhhealth.com.br";
        //monta o link
        $link_demonstrativo = "https://{$host}/portal/clientes/gera_demonstrativo_exames_complemetares?key=".urlencode($hash);

        //retorno o link a ser acessado
        return $link_demonstrativo;
    }

    /**
     * @param  [codigo_cliente] codigo do clietne que irá gerar o hash
     * @param  [mes] mes
     * @param  [ano] ano
     * @return [link] link para acessar o relatorio de demonstrativo
     */
    private function linkDemonstrativoPercapita($codigo_cliente, $mes, $ano) {
        //monta o hash para colocar no link
        $hash = Comum::geraParametroLinkDemonstrativoPercapita('demonstrativos', $codigo_cliente, $mes, $ano);
        //monta o host
        // $host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "portal.rhhealth.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "tstportal.rhhealth.com.br" : "portal.localhost"));
        $host = "portal.rhhealth.com.br";
        //monta o link
        $link_demonstrativo = "https://{$host}/portal/clientes/gera_demonstrativo_percapita?key=".urlencode($hash);

        //retorno o link a ser acessado
        return $link_demonstrativo;
    } //linkDemonstrativoPercapita

    /**
     * [relatorio_corona metodo para geraro o reltorio do grupo de risco do corona virus dos clientes]
     * 
     * @param  [type] $codigo_cliente [codigo do cliente que está querendo gerar o reltorio]
     * @return [type]                 [description]
     */
    public function relatorio_grupo_risco_corona($codigo_cliente)
    {
    	ini_set('memory_limit', '2G');
    	ini_set('max_execution_time', 0);
		set_time_limit(0);

    	//verifica se o codigo do cliente existe
    	if(empty($codigo_cliente)) {
    		print "Favor passar um codigo de cliente valido";
    		exit;
    	}

    	//verifica se é um codigo de cliente do grupo economico
    	$this->loadModel('GrupoEconomico');
    	$ge = $this->GrupoEconomico->find('first',array('conditions' => array('codigo_cliente' => $codigo_cliente)));
    	
    	//verifica se existe registro
    	if(empty($ge)){
    		print "Favor passar um codigo cliente do grupo economico!";
    		exit;
    	} 
    	//executa os dados
    	$this->loadModel('PedidoExame');
    	$query = $this->PedidoExame->get_grupo_risco_corona($ge['GrupoEconomico']['codigo']);
    	// exit;
    	//montagem para executar a query
		// $dados = $this->FichaClinica->query($query);

    	$dbo = $this->PedidoExame->getDataSource();
		$dbo->results = $dbo->rawQuery($query);
		
		// debug($dados);exit;


		// if(empty($dados)) {
		// 	print "Não foi encontrado dados para este grupo economico!";
		// 	exit;
		// }
		
		ob_clean();
		header('Content-Encoding: UTF-8');
		header("Content-Type: application/force-download;charset=utf-8");
		header('Content-Disposition: attachment; filename="grupo_risco_corona_virus_'.date('YmdHis').'.csv"');

		echo utf8_decode('"NOME FUNCIONÁRIO";"CPF";"RAZÃO SOCIAL";"NOME FANTASIA";"SETOR";"CARGO";"DATA NASCIMENTO";"IDADE";"SEXO";"PA DIASTOLICA";"PA SISTOLICA";"ALTURA MT";"ALTURA CM";"PESO KG";"PESO GR";"IMC";"DATA FICHA";"HIPERTENSÃO";"DIABÉTICO";"PROBLEMA RESPIRATÓRIO";"DESCRICAO PROBLEMA RESPIRATÓRIO";"FUMANTE";"MEDICAMENTO";"OBSERVAÇÃO"')."\n";

		while ($value = $dbo->fetchRow()) {
		// foreach ($dados as $value) {

			// debug($value);

			$linha = $value[0]['nome_funcionario'].';';
			$linha .= $value[0]['cpf'].';';
			$linha .= $value[0]['alocacao_razao_social'].';';
			$linha .= $value[0]['alocacao_nome_fantasia'].';';
			$linha .= $value[0]['setor'].';';
			$linha .= $value[0]['cargo'].';';
			$linha .= AppModel::dbDateToDate($value[0]['data_nascimento']).';';
			$linha .= $value[0]['idade'].';';
			$linha .= $value[0]['sexo'].';';
			$linha .= $value[0]['pa_diastolica'].';';
			$linha .= $value[0]['pa_sistolica'].';';
			$linha .= $value[0]['altura_mt'].';';
			$linha .= $value[0]['altura_cm'].';';
			$linha .= $value[0]['peso_kg'].';';
			$linha .= $value[0]['peso_gr'].';';
			$linha .= $value[0]['imc'].';';
			$linha .= AppModel::dbDateToDate($value[0]['data_ficha']).';';
			$linha .= $value[0]['hipertensao'].';';
			$linha .= $value[0]['diabetico'].';';
			$linha .= $value[0]['problema_respiratorio'].';';
			$linha .= $value[0]['descricao_problema_respiratori'].';';
			$linha .= $value[0]['fumante'].';';
			$linha .= $value[0]['medicamento'].';';
			$linha .= str_replace(';','|',str_replace(array("\n", "\r"),'',$value[0]['observacao'])).';';
			// $linha .= $value[0]['observacao'].';';

			echo utf8_decode($linha)."\n";
		}
		die();

    }// fim relatorio_grupo_risco_corona


    public function funcionarios_rel()
    {

    	$anos = array(
    		'2018',
    		'2019',
    		'2020'
    	);

    	$meses = array(
    		'01','02','03','04','05','06','07','08','09','10','11','12'
    	);
		$this->loadModel('ClienteFuncionario');
		$dado = array();
    	foreach($anos as $ano ) {
    		foreach($meses as $mes) {
    			
    			if($ano == '2020' && $mes > '03') {
    				continue;
    			}


    			$ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano));
    			$data = $ano.'-'.$mes.'-'.$ultimo_dia;

    			$query = "
					select
					    c.codigo,
					    c.nome_fantasia,
					    c.razao_social,
					    count( distinct codigo_funcionario) as total
					from cliente_funcionario cf
					    inner join cliente c on cf.codigo_cliente = c.codigo
					where (data_demissao is null or data_demissao >= '".$data."')
					group by c.codigo, c.nome_fantasia, c.razao_social
						;";
						// and cf.codigo_cliente = 79
		    	//montagem para executar a query
				$dados = $this->ClienteFuncionario->query($query);

				foreach ($dados as $key => $val) {
					$dado[$val[0]['codigo']]['codigo'] = $val[0]['codigo'];
					$dado[$val[0]['codigo']]['nome'] = $val[0]['nome_fantasia'];
					$dado[$val[0]['codigo']]['razao'] = $val[0]['razao_social'];
					$dado[$val[0]['codigo']][$ano][$mes] = $val[0]['total'];
					
				}

				// if($mes == '05') {
				// 	debug($dado);exit;
				// }


				// $qtd = $dados[0][0]['total'];

    		}// fim mes

    	}// fim ano

    	// debug($dado);
    	// exit;

    	ob_clean();
		header('Content-Encoding: UTF-8');
		header("Content-Type: application/force-download;charset=utf-8");
		header('Content-Disposition: attachment; filename="evolucao_funcionarios_ano_mes'.date('YmdHis').'.csv"');

    	echo "codigo_cliente;nome_cliente;razao_social;ano;01;2;3;4;5;6;7;8;9;10;11;12\n";

    	foreach ($dado AS $codigo_cliente => $d) {

    		$linha = '';

    		foreach($anos as $ano) {

    			if(isset($d[$ano])) {

		    		$linha .= $codigo_cliente.";";
		    		$linha .= $d['nome'].";";
		    		$linha .= $d['razao'].";";
	    			$linha .= $ano.";";

	    			foreach($d[$ano] AS $mes => $qtd) {

	    				switch($mes) {
	    					case '01':
	    						$linha .= "{$qtd};";
	    						break;
	    					case '02':
								$linha .= "{$qtd};";
	    						break;
	    					case '03':
								$linha .= "{$qtd};";
	    						break;
	    					case '04':
								$linha .= "{$qtd};";
	    						break;
	    					case '05':
								$linha .= "{$qtd};";
	    						break;
	    					case '06':
								$linha .= "{$qtd};";
	    						break;
	    					case '07':
								$linha .= "{$qtd};";
	    						break;
	    					case '08':
								$linha .= "{$qtd};";
	    						break;
	    					case '09':
								$linha .= "{$qtd};";
	    						break;
	    					case '10':
								$linha .= "{$qtd};";
	    						break;
	    					case '11':
								$linha .= "{$qtd};";
	    						break;
	    					case '12':
								$linha .= "{$qtd}\n";
	    						break;
	    				}

	    			}

	    		}//fim isset

    		}

    		if(!empty($linha)) {
    			echo utf8_decode($linha)."\n";
    		}


    	}

    	// debug($dado);
    	exit;


    }

    /**
     * [deletar_usuario_lyn metodo para auxiliar a implantacao de clientes quando precisar zerar um determinado usuario]
     * @param  [type] $cpf [description]
     * @return [type]      [description]
     */
    public function deletar_usuario_lyn() 
    {
    	$this->pageTitle = "Zerar usuario LYN";
    	$msg = '';
    	// 
    	if (!empty($this->data)) {

			if (!empty($this->data['Sistema']['cpf'])) {

				$cpf = $this->data['Sistema']['cpf'];
				unset($this->data['Sistema']['cpf']);


				$this->loadModel('Usuario');
				$usuario = $this->Usuario->find('first',array('conditions' => array('apelido' => $cpf, 'codigo_uperfil' => 9)));

				//para zerar o usuario do lyn
				if(!empty($usuario)) {
					$codigo = $usuario['Usuario']['codigo'];

					$query = "
						DELETE FROM usuario_sistema WHERE codigo_usuario = {$codigo};
						DELETE FROM usuarios_dados WHERE codigo_usuario = {$codigo};
						DELETE FROM resultado_covid WHERE codigo_usuario = {$codigo};
						DELETE FROM respostas WHERE codigo_usuario = {$codigo};
						DELETE FROM usuario_grupo_covid WHERE codigo_usuario = {$codigo};
						DELETE FROM usuario_endereco WHERE codigo_usuario = {$codigo};
						DELETE FROM usuarios_imc WHERE codigo_usuario = {$codigo};
						DELETE FROM usuarios_questionarios WHERE codigo_usuario = {$codigo};
						DELETE FROM usuario_gca WHERE codigo_usuario = {$codigo};
						DELETE FROM usuarios_historicos WHERE codigo_usuario = {$codigo};
						DELETE FROM usuarios_pressao_arterial WHERE codigo_usuario = {$codigo};
						DELETE FROM usuarios_abdominal WHERE codigo_usuario = {$codigo};
						DELETE FROM usuario_contato_emergencia WHERE codigo_usuario = {$codigo};
						DELETE FROM usuarios_colesterol WHERE codigo_usuario = {$codigo};
						DELETE FROM usuarios_glicose WHERE codigo_usuario = {$codigo};
						DELETE FROM usuarios_medicamentos_status WHERE codigo_usuario_medicamento IN (SELECT codigo FROM usuarios_medicamentos WHERE codigo_usuario = {$codigo});
						DELETE FROM usuarios_medicamentos WHERE codigo_usuario = {$codigo};
						DELETE FROM push_outbox WHERE codigo_usuario = {$codigo};
						DELETE FROM usuario WHERE codigo = {$codigo};
					";

					// debug($query);

					if($this->Usuario->query($query)) {
						$msg = "Usuario deletado dos sistemas.";
					}
					else {
						$msg = "Erro ao deletar usuario dos sistemas.";
					}
				}

			}
		}

		$this->set(compact('msg'));

    }//fim deletar_usuario_lyn


    /**
     * [comparacao_codigo_cnpj metodo para comparar o codigo do cliente com o cnpj]
     * @return [type] [description]
     */
    public function comparacao_codigo_cnpj()
    {
    	$this->loadModel('Cliente');
    	$path = APP."tmp/codigo_alocacao.txt";
    	$path_2 = APP."tmp/codigo_alocacao_correto.txt";

    	$dados = file($path);

    	$countCnpjIgual = 0;
    	$countCnpjDiff = 0;

    	// debug($dados);exit;
    	foreach($dados as $d) {

    		$dado = explode(';',$d);
    		$codigo_alocacao = $dado[0];
    		$cnpj_planilha = trim($dado[1]);

    		if(empty($codigo_alocacao)) {
    			$novo = ";".$cnpj_planilha."\n";
    			file_put_contents($path_2,$novo, FILE_APPEND);
    			continue;
    		}

    		$query = "SELECT codigo_documento FROM cliente WHERE codigo = " . $codigo_alocacao.";";
    		$cliente = $this->Cliente->query($query);

    		$codigo_documento = $cliente[0][0]['codigo_documento'];
    		
    		//debug($cnpj_planilha .'=='. $codigo_documento);

    		if($cnpj_planilha == $codigo_documento) {
    			$countCnpjIgual++;
    		}
    		else {
    			$countCnpjDiff++;
    		}

			$novo = $codigo_alocacao.";".$codigo_documento."\n";
			file_put_contents($path_2,$novo, FILE_APPEND);

    		// debug($cliente);exit;

    	}

    	print "CPNJ Igual: " . $countCnpjIgual.", CNPJ DIFF: " . $countCnpjDiff."<br>";

    	print "finalizou<br>";
    	// debug($dados);
    	exit;

    }//fim comparacao_codigo_cnpj

    // public function gerar_token()
    // {
    // 	$this->loadModel('Usuario');
    	
    // 	debug(md5(uniqid(rand(), true)));
    // 	debug($this->Usuario->gerarToken());
    // 	exit;

    // }

    public function dispara_alertas()
    {
    	Comum::execInBackground(ROOT . '/cake/console/cake -app '. ROOT . DS . 'app agendar_alertas run');
    	print "agendando alertas para os emails.";
    	exit;
    }

    public function dispara_email()
    {
		///home/sistemas/rhhealth/portal/cake/console/cake -app /home/sistemas/rhhealth/portal/app cron hourly azportal.rhhealth.com.br

    	Comum::execInBackground(ROOT . '/cake/console/cake -app '. ROOT . DS . 'app cron hourly azportal.rhhealth.com.br');
    	print "emails disparando.";
    	exit;
    }

    /**
     * Description metodo para exportar os dados financeiros cnts a pagar e receber
     * @return type
     */
    public function relatorio_financeiro()
    {

    	//verifica se está logado
    	if(!$this->authUsuario['Usuario']['codigo']) {
    		$this->redirect(array('controller' => 'usuarios','action' => 'logout'));
    	}


    	//validação do usuarios
    	//Eduardo, Roberta.binot, Jennifer.Oliveira, WIllians
    	$user_per = array(
    		'61614', //Eduardo
    		'63063', // roberta.binot
    		'73859', // jennifer.oliveira
    		'64922' //willians.pedroso
    	);
    	//verifica se está liberado para buscar esses dados
    	if(!in_array($this->authUsuario['Usuario']['codigo'],$user_per)) {
    		$this->redirect(array('controller' => 'usuarios','action' => 'logout'));
    	}

    	$this->pageTitle = "Relatório Financeiro";
    	$msg = '';
    	// 
    	if (!empty($this->data)) {
    		$validacoes = false;
    		$dados = $this->data['Sistema'];

    		//verificações se tem os campos que precisa para gerar
    		if(empty($dados['data_inicial'])) {
    			$msg[] = "Necessario data inicial";
    			$validacoes = true;
    		}

    		if(empty($dados['data_final'])) {
    			$msg[] = "Necessario data final";
    			$validacoes = true;
    		}

    		if(empty($dados['tipos'])) {
    			$msg[] = "Necessario algum tipo selecionado";
    			$validacoes = true;	
    		}
    		//verifica se tem alguma validação necessaria para gera o relatorio
    		if(!$validacoes) {


    			$this->loadModel('RelatorioNaveg');

    			$data_inicial = Comum::formataData($dados['data_inicial'],'dmy','ymd');
    			$data_final = Comum::formataData($dados['data_final'],'dmy','ymd');
    			

    			//varre os tipos
    			foreach($dados['tipos'] AS $tp) {

	    			//grava o usuario que está solicitando os dados e o filtro feito com a data_inclusao 
	    			$query_insert = "INSERT INTO RHHealth.dbo.rel_fin_usu_log (codigo_usuario_inclusao,data_inclusao,data_inicio,data_fim,tipo) VALUES ('".$this->authUsuario['Usuario']['codigo']."','".date('Y-m-d H:i:s')."','".$data_inicial."','".$data_final."','".$tp."')";
	    			// echo $query_insert;exit;
	    			$this->RelatorioNaveg->query($query_insert);

    				//verifica se é contas a pagar
    				if($tp == 1) {
    					$val = $this->RelatorioNaveg->getCntPagar($data_inicial,$data_final);
    					$nome_rel = "cnt_pagar_";
    				}

    				//verifica se contas receber liquidada
    				if($tp == 2) {
    					$val = $this->RelatorioNaveg->getCntReceberLiquidada($data_inicial,$data_final);
    					$nome_rel = "cnt_receber_liquidada_";
    				}

    				//verifica se contas receber aberta
    				if($tp == 3) {
    					$val = $this->RelatorioNaveg->getCntReceberAberto($data_inicial,$data_final);
    					$nome_rel = "cnt_receber_liquidada_";
    				}
    				
    			}//fim foreach

    			//gera o relatorio em exportado
    			$this->exportRelContas($nome_rel,$val[0],$val[1]);

    			
    		}//fim validacoes

    	}//fim this->data

    	if(!empty($msg)) {
    		$msg = implode(',',$msg);
    	}

    	$tipos= array(
    		'1' => 'Contas Pagar',
    		'2' => 'Contas Receber Liquidada',
    		'3' => 'Contas Receber Aberta',
    	);

    	$this->set(compact('tipos','msg'));
    	

    }//fim relatorio_financeiro

    //monta o export
    private function exportRelContas($nome_rel,$campos,$dados)
    {
    
    	$this->layout = false;

    	// debug($campos);
    	// debug($dados);
    	// exit;
		
		ob_clean();		
		header('Content-Encoding: UTF-8');
		header("Content-Type: application/force-download;charset=utf-8");
		header('Content-Disposition: attachment; filename="'.$nome_rel."_".date('YmdHis').'.csv"');
		
		
		//monta o cabecalho
		$cabecalho = implode(";", $campos);	
		echo $cabecalho."\n";

		//array para remover os acentos
		$conversao = array(
			'á' => 'a','à' => 'a','ã' => 'a','â' => 'a', 'é' => 'e',
 			'ê' => 'e', 'í' => 'i', 'ï'=>'i', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', "ö"=>"o",
 			'ú' => 'u', 'ü' => 'u', 'ç' => 'c', 'ñ'=>'n', 'Á' => 'A', 'À' => 'A', 'Ã' => 'A',
 			'Â' => 'A', 'É' => 'E', 'Ê' => 'E', 'Í' => 'I', 'Ï'=>'I', "Ö"=>"O", 'Ó' => 'O',
 			'Ô' => 'O', 'Õ' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ç' =>'C', 'Ñ'=>'N'
 		);

		//varre os dados
		foreach ($dados as $key => $dado) { 			
			$linha = '';
			//monta as colunas
			foreach ($campos as $desc_coluna) {
				$linha .= '"' . strtoupper(strtr(strtr($dado[0][$desc_coluna], ';', ':'), $conversao)) . '";';
			}//fim foreach colunas

			$linha .= "\n";
			echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
		}//fim dados
		die();


    }//fim exportRelContas

    public function log_api_ppra()
    {

    	// debug(date("Y-m-d H:i:s"));
    	// debug(date("Y-m-d H:i:s",strtotime(date("H:i:s")."- 10 minutes")));
    	// exit;
    	
    	$this->loadModel('GrupoExposicao');

    	$dados = $this->GrupoExposicao->reprocessamento_api_ppra_log();

    	debug($dados);

    	print "opa";exit;

    }

    /**
     * [reenviar_email_pedido metodo para reenviar os pedidos de exames com anexo]
     * @return [type] [description]
     */
    public function reenviar_email_pedido($codigo_pedido=null,$data_inicio=null,$data_fim=null,$nome_cliente = null)
    {

    	print "INICIANDO O PROCESSAMENTO <br>";

    	$where_pe = '';
    	$where_email = '';
    	//verifica se tem parametro
    	if(!is_null($codigo_pedido) && !empty($codigo_pedido) && $codigo_pedido != 'null') {
    		$where_pe .= ' AND codigo = '.$codigo_pedido;
    		$where_email .= " AND [content] like '%".$codigo_pedido."%'";
    	}

    	if(!is_null($data_inicio) && !empty($data_inicio) && $data_inicio != 'null') {
    		$where_pe .= " AND data_inclusao >= '".$data_inicio." 00:00:00"."'";
    		$where_email .= " AND created >= '".$data_inicio." 00:00:00"."'";
    	}

    	if(!is_null($data_fim) && !empty($data_fim) && $data_fim != 'null') {
    		
    		$where_pe .= " AND data_inclusao <= '".$data_fim." 23:59:59"."'";
    		$where_email .= " AND created <= '".$data_fim." 23:59:59"."'";


    		// $where_pe .= " AND data_inclusao <= '".$data_fim." 11:00:00"."'";
    		// $where_email .= " AND created <= '".$data_fim." 11:00:00"."'";
    	}

    	if(!is_null($nome_cliente) && !empty($nome_cliente) && $nome_cliente != 'null') {
    		$where_email .= " AND [subject] like '%".$nome_cliente."%'";
    	}

    	$this->loadModel('PedidoExame');
    	$sql_emails = "SELECT [to], subject,attachments,content 
    					FROM mailer_outbox 
    					WHERE [subject] like '%RH HEALTH%' 
    						AND attachments is not null " . $where_email . " order by [created] ";
    	$dados_email = $this->PedidoExame->query($sql_emails);

    	// debug($sql_emails);exit;
    	
    	$pedidos_exame_email = array();
    	if(!empty($dados_email)) {

    		//setando o auth
    		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;

    		$dados_tipos_notificacoes = array(
    			'1' => 'Pedido de Exame',
	            '2' => 'ASO',
	            '3' => 'Ficha Clínica',
	            '4' => 'Laudo Caracterizador de Deficiência',
	            '5' => 'Recomendações',
	            '6' => 'Audiometria',
	            '7' => 'Ficha Assistencial',
	            '8' => 'Avaliação Psicossocial',
    		);

    		print "IDENTIFICANDO OS EMAILS E AS CONFIGURACOES DOS PEDIDOS A SEREM DISPARADOS <br>";

    		//varre os emails
    		foreach($dados_email AS $dado_e) {
    			
    			$dado = $dado_e[0];

    			$email = $dado['to'];
    			if(!strstr($email,"@")) {
    				continue;
    			}

    			$titulo = $dado['subject'];
    			$anexo = $dado['attachments'];
    			$conteudo = $dado['content'];

    			//verifica se é funcioanrio, fornecedor ou solicitante
    			$msg_fornecedor = 'Estamos entrando em contato para confirmar o agendamento referente ao pedido do exame';
    			$msg_funcionario = 'Estamos entrando em contato para dizer que';
    			$msg_solicitante = 'Estamos entrando em contato para informar que o(s) exame(s) do funcion';

    			//variavel auxiliar
    			$tipo_email = '';
    			
    			if(strstr($conteudo,$msg_fornecedor)) {
    				$tipo_email = 'fornecedor';
    			}
    			else if(strstr($conteudo,$msg_funcionario)) {
    				$tipo_email = 'funcionario';
    			}
    			else if(strstr($conteudo,$msg_solicitante)) {
    				$tipo_email = 'solicitante';
    			}

    			$codigo_fornecedor = 0;
    			if(strstr($anexo,'PEDIDO_')) {
    				$array_anexo = explode('PEDIDO_',$anexo);
    				$array_anexo = explode('_',$array_anexo[1]);
    				$codigo_pedido_exame = $array_anexo[0];

    			}
    			else {
    				$array_anexo = explode('pedido_',$anexo);
    				$array_anexo = explode('_',$array_anexo[1]);
    				$codigo_pedido_exame = $array_anexo[0];
    			}

				if($tipo_email == "fornecedor") {

					//busca o fornecedor no anexo 
					if(strstr($anexo,'pedidos_exame_f')) {
						$array_fornecedor = explode("pedidos_exame_f",$anexo);
						$array_fornecedor = explode("_",$array_fornecedor[1]);
						$codigo_fornecedor = $array_fornecedor[0];
					}//fim strstr anexo

				}//fim tipo email

    			$pedidos_exame_email[$codigo_pedido_exame][$tipo_email][$codigo_fornecedor] = $email;

				// debug(array($email,$titulo,$anexo,$codigo_pedido_exame));
				// debug($array_anexo);


    		}//fim foreach

    		// exit;

    		// debug($pedidos_exame_email);exit;
    		App::import('Controller', 'PedidosExames');
    		$pedido_exame_controller = new PedidosExamesController();
    		$pedido_exame_controller->constructClasses();

    		print "PREPARANDO PARA REENVIAR OS EMAILS COM OS ANEXOS <br>";

    		$this->loadModel('TipoNotificacaoValor');
    		//varre os pedidos de exames
    		foreach($pedidos_exame_email AS $codigo_pedido_exame => $dados) {

				$dadosPedido = $this->PedidoExame->read(null, $codigo_pedido_exame);
				$codigo_funcionario_setor_cargo = $dadosPedido['PedidoExame']['codigo_func_setor_cargo'];
				$codigo_cliente_funcionario = $dadosPedido['PedidoExame']['codigo_cliente_funcionario'];
				
    			$dados_itens_pedido = $this->PedidoExame->retornaItensDoPedidoExame($codigo_pedido_exame);
				$dados_tipo_notificacao_valor = $this->TipoNotificacaoValor->find('all', array('conditions' => array('codigo_pedidos_exames' => $codigo_pedido_exame)));

				$contatosClienteFuncionario = $this->PedidoExame->retornaContatosClienteFuncionario($codigo_funcionario_setor_cargo);
				//Dados do Cliente e Funcionario
				$cliente_nome       = $contatosClienteFuncionario['FuncionarioSetorCargo']['cliente_razao_social'];
				$funcionario_nome   = $contatosClienteFuncionario['FuncionarioSetorCargo']['funcionario_nome'];
				
				//padra de exames a serem disparados
	    		$dados_post = array(
	    			'PedidosExames' => array(
	    				'funcionario' => array(),
	    				'cliente' => array(),
	    				'fornecedor' => array(),
	    				'vias_aso' => ''
	    			),
	    			'EmailFuncionario' => array(
	    				'email' => $dados['funcionario'][0]
	    			),
	            	'EmailCliente' => array(
	            		'email' => $dados['solicitante'][0]
	            	),
	            	'EmailFornecedor' => array(),
	            	'cliente_nome' => $cliente_nome,
	            	'funcionario_nome' => $funcionario_nome
	    		);
		        
				foreach($dados_tipo_notificacao_valor as $dadoTNV) {

					if($dadoTNV['TipoNotificacaoValor']['campo_funcionario'] == 1) {
						$dados_post['PedidosExames']['funcionario'][$dadoTNV['TipoNotificacaoValor']['codigo_tipo_notificacao']] = 1;
					}

					if($dadoTNV['TipoNotificacaoValor']['campo_cliente'] == 1) {
						$dados_post['PedidosExames']['cliente'][$dadoTNV['TipoNotificacaoValor']['codigo_tipo_notificacao']] = 1;
					}

					if($dadoTNV['TipoNotificacaoValor']['campo_fornecedor'] == 1) {
						$dados_post['PedidosExames']['fornecedor'][$dadoTNV['TipoNotificacaoValor']['codigo_tipo_notificacao']] = 1;
					}

					if(!empty($dadoTNV['TipoNotificacaoValor']['vias_aso'])) {
						$dados_post['PedidosExames']['vias_aso'] = $dadoTNV['TipoNotificacaoValor']['vias_aso'];	
					}
				}

				foreach($dados['fornecedor'] as $codigo_fornecedor => $email_forn) {
					$dados_post['EmailFornecedor'][$codigo_fornecedor]['fornecedor'] = $email_forn;
				}
				
				// debug($dados_post);

				//chamando a controller que vai reenviar os relatorios				
				if($pedido_exame_controller->__enviaRelatorios($dados_post, $dados_itens_pedido, $codigo_cliente_funcionario, $codigo_pedido_exame, $dados_tipos_notificacoes)) {
					print "PEDIDO: ".$codigo_pedido_exame." REENVIADO <br>";
				}
				// debug($dados_post);exit;

    		}

    	}// fim emails

    	print "FIM DO REENVIO DOS EMAILS <br>";
    	exit;

    }//fim reenviar_email_pedido

    /**
     * Description metodo para voltar o pedido para pendente
     * @return type
     */
    public function voltar_pedido_pendente()
    {

    	$this->pageTitle = "Voltar Pedido Exames Pendente";
    	$msg = '';
    	// 
    	if (!empty($this->data)) {

			if (!empty($this->data['Sistema']['codigos'])) {

				$codigos = $this->data['Sistema']['codigos'];
				unset($this->data['Sistema']['codigos']);

				$this->loadModel('PedidosExames');

				$query = "
					UPDATE RHHealth.dbo.pedidos_exames SET codigo_status_pedidos_exames = 1 WHERE codigo IN ({$codigos});
				";

				// debug($query);exit;

				if($this->PedidosExames->query($query)) {
					$msg = "Pedidos alterados com sucesso.";
				}
				else {
					$msg = "Erro ao voltar pedidos.";
				}

			}
		}

		$this->set(compact('msg'));

    }//fim voltar_pedido_pendente


    public function get_reg_id($reg_id=null)
    {
    	
    	$this->autoRender = false;

    	$this->loadModel('PushOutbox');

    	$uri = "ithealth.servicebus.windows.net/ithealth-notification-hub";
    	$sasKeyName = "RootManageSharedAccessKey";
    	$sasKeyValue = "YT0BTVxAzLP2QoF6Tq0lo+Ic8nJ3TerTkm1DrKSjtZs=";

    	$token = $this->PushOutbox->generateSasToken($uri, $sasKeyName, $sasKeyValue);

    	// debug($reg_id);
    	// debug($token);


    	$data = $this->PushOutbox->get_registration($token,$reg_id); //registration
    	// $data = $this->PushOutbox->get_registration($token); //all
    	$xml = simplexml_load_string($data);
    	debug($xml);

    	// $data = $this->PushOutbox->get_instalation($token,$reg_id);
    	// print $data."<br>";
    	
    	// $data = $this->PushOutbox->get_tags($token,$reg_id);
    	// $xml = simplexml_load_string($data);
    	// debug($xml);

    	
    	

    	// if(substr($registration,0,3) == '404') {
    	// 	echo 'erro';
    	// }
    	// else {
    	// 	echo $registration;
    	// }

		exit;


    }//fim get_regId


    /**
     * [get_jasper description]
     * @return [type] [description]
     */
    public function get_jasper()
    {

    	$this->autoRender = false;
    	$nome_relatorio = 'gd_ppra_dinamico';
		
		$report_name = '/reports/RHHealth/' . $nome_relatorio;
		$file_name = basename( $nome_relatorio.'.pdf' );

		// opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME'=>$report_name, // especificar qual relatório
			'FILE_NAME'=> $file_name // nome do relatório para saida
		);
		$this->loadModel('PedidoExame');
		$query = "select 
					gdev.codigo
					,gde.campo
					,gdev.valor
				from gestao_doc_estrutura gde 
					inner join gestao_doc_estrutura_valor gdev on gde.codigo = gdev.codigo_gestao_doc_estrutura
				where gdev.codigo_gestao_doc = 1";
		$dados = $this->PedidoExame->query($query);
		$codigo_cliente = 79;
		$parametros = array(
			'CODIGO_CLIENTE' => $codigo_cliente
		);
		foreach($dados AS $dado) {

			if($dado[0]['valor'] == '[ppra_dados_cliente]') {

				//pega os dados da empresa
				$sql = "SELECT 
							c.razao_social,
							c.nome_fantasia,
							c.codigo_documento,
							c.cnae,
							concat(ce.logradouro,', ',ce.numero,' ',ce.complemento) as endereco,
							ce.bairro,
							ce.cidade,
							ce.estado_abreviacao
						FROM cliente c 
							inner join cliente_endereco ce on c.codigo = ce.codigo_cliente
						WHERE c.codigo = {$codigo_cliente}
					";
				$dado_cliente = $this->PedidoExame->query($sql);

				$html_cliente = "
					<table border='1'>
						<tr>
							<td colspan='2'><b>EMPRESA</b></td>
						</tr>
						<tr>
							<td>Razão Social:</td>
							<td>{$dado_cliente[0][0]['razao_social']}</td>
						</tr>
						<tr>
							<td>Nome Fantasia:</td>
							<td>{$dado_cliente[0][0]['nome_fantasia']}</td>
						</tr>
					</table>
				";
				$parametros[strtoupper($dado[0]['campo'])] = $html_cliente;
			}
			else {
				$parametros[strtoupper($dado[0]['campo'])] = utf8_encode($dado[0]['valor']);

			}

		}

		// debug($parametros);
		// exit;
		


		// parametros do relatorio
		/*$parametros = array(
			'CODIGO_FORNECEDOR' => $codigo_fornecedor,
			'CODIGO_CLIENTE_FUNCIONARIO' => $codigo_cliente_funcionario,
			'CODIGO_PEDIDO_EXAME' => $codigo_pedido_exame,
			'CODIGO_FUNC_SETOR_CARGO' => $codigo_func_setor_cargo,
            'CODIGO_EXAME_ASO' => $codigo_exame_aso,
		);

        $exibe_nome_fantasia_aso = 'false';
        $exibe_rqe_aso = 'false';

		if($relatorio == 2){//ASO

		    if(!empty($codigo_pedido_exame) && !is_null($codigo_pedido_exame)){

                $codigo_cliente = $this->PedidoExame->getCodigoCliente($codigo_pedido_exame);
                
                if(!is_null($codigo_cliente)){

                    $return = $this->GrupoEconomico->getCampoPorCliente('exibir_nome_fantasia_aso', $codigo_cliente);
                    $exibe_nome_fantasia_aso = ($return ? 'true' : 'false');

                    $retorno_rqe = $this->GrupoEconomico->getCampoPorClienteRqe('exibir_rqe_aso', $codigo_cliente);
					$exibe_rqe_aso = ($retorno_rqe ? 'true' : 'false');
                }
            }
        }

        $parametros['EXIBE_NOME_FANTASIA_ASO'] = $exibe_nome_fantasia_aso;

        $parametros['EXIBE_RQE_ASO'] = $exibe_rqe_aso;

		$this->loadModel('Cliente');
		$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
		*/

		try {
			// envia dados ao componente para gerar
			$url = $this->Jasper->generate( $parametros, $opcoes );

			// debug($url);exit;

			if(!empty($url)){
				// se obter retorno apresenta usando cabeçalho apropriado
				header(sprintf('Content-Disposition: attachment; filename="%s"', $opcoes['FILE_NAME']));
				header('Pragma: no-cache');
				header('Content-type: application/pdf');
				echo $url; exit;
			}

		} catch (Exception $e) {
			// se ocorreu erro
			debug($e); exit;
		}		

		exit;


    }//fim get_jasper


	/*
	* Função para ativar o disparo a cron de disparo de e-mail de validação de pré-faturamento
	*/
	function disparar_email_faturamento()
	{
		$this->layout = false;

		$cmd =  ROOT . "/cake/console/cake -app ". ROOT . DS . "app validacao_faturamento validarFaturamento";
		shell_exec($cmd);

		echo "Emails de validação de pré-faturamento prontos para serem disparados...";
		exit;
	}

	
	function disparar_email_exames_reprovados()
	{
		$this->layout = false;

		$cmd =  ROOT . "/cake/console/cake -app ". ROOT . DS . "app envio_email_anexos_reprovados enviar_email_anexos_reprovados";
		shell_exec($cmd);

		echo "Emails de exames reprovados prontos para serem disparados...";
		exit;
	}

	public function tipos_resultados_exames()
	{
		$this->layout = false;
		
		$this->loadModel('TiposResultadosExames');
		$this->loadModel('Exames');

		$get_tipos_resultados_exames = $this->TiposResultadosExames->find("all");
		$get_exames = $this->Exames->find("all", array(
			"fields" => array("codigo"),
			"recursive" => -1,
			"order" => "codigo ASC"
		));

		if (empty($get_tipos_resultados_exames)) {
			
			foreach ($get_exames as $exame) {

				$inserir['TiposResultadosExames'] = array(
					'codigo_tipo_resultado' => 1,
					'codigo_exame' => $exame['Exames']['codigo']
				);

				if (!$this->TiposResultadosExames->incluir($inserir)) {
					echo $exame['Exames']['codigo'] . " \n";
				}
			}
		
			echo " \n FINALISADO...";

		}
		
		exit;
	}

	public function inserindo_dados_tempo_liberacao()
	{
		$this->layout = false;
		
		$this->loadModel('TempoLiberacao');

		$tempo_liberacao = array(		
			1 => "Liberação imediata",
			2 => "1h",
			3 => "2h",
			4 => "3h",
			5 => "4h",
			6 => "5h",
			7 => "6h",
			8 => "7h",
			9 => "8h",
			10 => "9h",
			11 => "10h",
			12 => "11h",
			13 => "12h",
			14 => "13h",
			15 => "14h",
			16 => "15h",
			17 => "16h",
			18 => "17h",
			19 => "18h",
			20 => "19h",
			21 => "20h",
			22 => "21h",
			23 => "22h",
			24 => "23h",
			25 => "24h",
			26 => "1 dias",
			27 => "2 dias",
			28 => "3 dias",
			29 => "4 dias",
			30 => "5 dias",
			31 => "6 dias",
			32 => "7 dias",
			33 => "8 dias",
			34 => "9 dias",
			35 => "10 dias",
			36 => "11 dias",
			37 => "12 dias",
			38 => "13 dias",
			39 => "14 dias",
			40 => "15 dias",
			41 => "16 dias",
			42 => "17 dias",
			43 => "18 dias",
			44 => "19 dias",
			45 => "20 dias",
			46 => "21 dias",
			47 => "22 dias",
			48 => "23 dias",
			49 => "24 dias",
			50 => "25 dias",
			51 => "26 dias",
			52 => "27 dias",
			53 => "28 dias",
			54 => "29 dias",
			55 => "30 dias",
			56 => "31 dias",
			57 => "32 dias",
			58 => "33 dias",
			59 => "34 dias",
			60 => "35 dias",
			61 => "36 dias",
			62 => "37 dias",
			63 => "38 dias",
			64 => "39 dias",
			65 => "40 dias",
			66 => "41 dias",
			67 => "42 dias",
			68 => "43 dias",
			69 => "44 dias",
			70 => "45 dias",
			71 => "46 dias",
			72 => "47 dias",
			73 => "48 dias",
			74 => "49 dias",
			75 => "50 dias",
			76 => "51 dias",
			77 => "52 dias",
			78 => "53 dias",
			79 => "54 dias",
			80 => "55 dias",
			81 => "56 dias",
			82 => "57 dias",
			83 => "58 dias",
			84 => "59 dias",			
			85 => "60 dias"			
		);

		$pegar_dados = $this->TempoLiberacao->find("all");

		if (empty($pegar_dados)) {
			foreach ($tempo_liberacao as $data) {

				$arr_dados = array(
					'descricao' => $data
				);
	
				$inserir['TempoLiberacao'] = $arr_dados;
	
				//pr($data);
				$this->TempoLiberacao->incluir($inserir);
			}
			echo "Inseriu!!!";
		} else {
			echo "Já preenchida";
		}
		
		exit;
	}

	public function dispara_email_para_fornecedores()
	{
		$this->layout = false;
		
		$this->loadModel('Fornecedor');
		$this->loadModel('FornecedorContato');
		$this->loadModel('TempoLiberacaoServico');

		$get_fornecedores = $this->Fornecedor->find("all",
			array(
				"fields" => array("Fornecedor.codigo"),
				"conditions" => array('Fornecedor.ativo' => 1),
				"limit" => 1000,
				"recursive" => -1
			)
		);

		foreach ($get_fornecedores as $key => $dados) {

			$get_tempo_liberacao = $this->TempoLiberacaoServico->find("all",
				array(
					"fields" => array("TempoLiberacaoServico.codigo_tempo_liberacao", "TempoLiberacaoServico.codigo_fornecedor"),
					"conditions" => array(
						'TempoLiberacaoServico.codigo_fornecedor' => $dados['Fornecedor']['codigo']
					),									
				)
			);

			$get_fornecedor_contato = $this->FornecedorContato->find("first",
					array(
						"fields" => array("FornecedorContato.descricao"),
						"conditions" => array(
							'FornecedorContato.codigo_fornecedor' => $dados['Fornecedor']['codigo'],
							'FornecedorContato.codigo_tipo_retorno' => 2
						),									
					)
				);

			if (empty($get_tempo_liberacao)) {
				
				$email = $get_fornecedor_contato['FornecedorContato']['descricao'];
				//$this->TempoLiberacaoServico->scheduleMailTempoLiberacao($email, $dados['Fornecedor']['codigo']);

			} else {

				$qtd_exames_nao_preenchido = 0;

				foreach ($get_tempo_liberacao as $exames) {
					
					if (empty($exames['TempoLiberacaoServico']['codigo_tempo_liberacao'])) {
						$qtd_exames_nao_preenchido++;
					}
				}

				if ($qtd_exames_nao_preenchido > 0) {
					
					$email = $get_fornecedor_contato['FornecedorContato']['descricao'];
					$this->TempoLiberacaoServico->scheduleMailTempoLiberacao($email, $dados['Fornecedor']['codigo']);

				}
			}			
		}

		//pr($get_fornecedores);
		exit;
	}

	
    /**
     * [importacao_folha_pagto metodo para rodar manualmente o processamento da stage de importacao para as tabelas principais, o metodo iniciais]
     * @param  [type] $metodo [os metodos iniciais int_cliente_empresa, int_cliente_setores,int_cliente_cargos,int_cliente_centro_resultado,int_cliente_funcionarios,int_cliente_int_cliente_funcionarios_empresa]
     * @param  [type] $codigo [codigo da tabela int_upload_cliente]
     * @return [type]         [description]
     */
    public function importacao_folha_pagto($metodo,$codigo = null) 
    {
    	print "iniciando processamento <br>";
    	
    	Comum::execInBackground(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . ' importacao_folha_pagto ' . $metodo);
		
		print "fim processamento <br>";
		exit;
    
    }// fim importacao_folha_pagto

    public function importacao_layouts()
    {
    	print "iniciando processamento <br>";
    	
    	Comum::execInBackground(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . ' importacao_layouts run 1');

    	print "fim processamento <br>";
		exit;
    
    }// fim importacao_layouts

	function dispara_get_integracao_tecnospeed() {
		$this->layout = false;

		$cmd =  ROOT . "/cake/console/cake -app ". ROOT . DS . "app esocial get_integracao_tecnospeed";
		shell_exec($cmd);

		echo "verificando se tem algum registro aguardando processamento...";
		exit;
	}

	public function atualiza_esocial()
	{
		$this->loadModel('Esocial');
		$esocial_all = $this->Esocial->find('all',array('conditions' => array('tabela' => '24', 'ativo' => 1 )));

		foreach($esocial_all as $dados) {
			debug($dados);

			$this->Esocial->atualizar($dados);

		}

	}//fim atualiza_esocial

	/**
	 * metodo para testar o retorno da nexo pelo pedido de exame
	 */
	public function retorno_nexo_pe($codigo_pedido_exame)
	{
		$this->loadModel('PedidoExame');

		$codigo_cliente = array('79929','79927','86400','79936');
		$codigo_cliente = implode(',',$codigo_cliente);
		$exames = $this->PedidoExame->busca_itens_pedidos_exames_nexo($codigo_pedido_exame, $codigo_cliente);

		debug($exames);exit;
		

	}// fim retorno_nexo_pe

	public function retorno_nexo_aso($codigo_pedido_exame)
	{
		$this->loadModel('PedidoExame');

		$codigo_cliente = array('79929','79927','86400','79936');
		$codigo_cliente = implode(',',$codigo_cliente);
		$exames = $this->PedidoExame->busca_aso_pedido_exame($codigo_pedido_exame, $codigo_cliente);

		debug($exames);exit;
		

	}// fim retorno_nexo_aso

	public function pega_arquivo()
	{
		$this->layout = false;
		$this->autoRender = false;

		//Caminho do Certificado
		$pfxCertPrivado = '/home/sistemas/rhhealth/c-care/portal/app/tmp/certificados/certificado_15_170120221701.pfx';
		$cert_password  = 'Chefs2021@';

		if (!file_exists($pfxCertPrivado)) {
			echo "Certificado não encontrado!! " . $pfxCertPrivado;
		}

		$pfxContent = file_get_contents($pfxCertPrivado);

		if (!openssl_pkcs12_read($pfxContent, $x509certdata, $cert_password)) {
			echo "O certificado não pode ser lido!!";
		} else {

			$CertPriv   = array();
			$CertPriv   = openssl_x509_parse(openssl_x509_read($x509certdata['cert']));

			$PrivateKey = $x509certdata['pkey'];

			$pub_key = openssl_pkey_get_public($x509certdata['cert']);
			$keyData = openssl_pkey_get_details($pub_key);

			$PublicKey  = $keyData['key'];

			echo '<br>'.'<br>'.'--- Dados do Certificado ---'.'<br>'.'<br>';
			echo $CertPriv['name'].'<br>';                           //Nome
			echo $CertPriv['hash'].'<br>';                           //hash
			echo $CertPriv['subject']['C'].'<br>';                   //País
			echo $CertPriv['subject']['ST'].'<br>';                  //Estado
			echo $CertPriv['subject']['L'].'<br>';                   //Município
			echo $CertPriv['subject']['CN'].'<br>';                  //Razão Social e CNPJ / CPF
			echo date('d/m/Y', $CertPriv['validTo_time_t'] ).'<br>'; //Validade
			echo $CertPriv['extensions']['subjectAltName'].'<br>';   //Emails Cadastrados separado por ,
			echo $CertPriv['extensions']['authorityKeyIdentifier'].'<br>'; 
			echo $CertPriv['issuer']['OU'].'<br>';                   //Emissor 
			echo '<br>'.'<br>'.'--- Chave Pública ---'.'<br>'.'<br>';
			print_r($PublicKey);
			echo '<br>'.'<br>'.'--- Chave Privada ---'.'<br>'.'<br>';
			echo $PrivateKey;
		}
	}

	public function retorno_query_alerta_ppra_pcmso(){
		$this->loadModel('Consulta');

		$dados = $this->Consulta->gerar_arquivo_pendencia_ppra_pcmso();

	}

	public function relatorio_produtos_servicos_clientes(){

		$this->loadModel('GrupoEconomicoCliente');

		$query = "
			SELECT 
				ge.codigo_cliente as codigo_matriz, 
				matriz.nome_fantasia as matriz, 
				gec.codigo_cliente as codigo_unidade, 
				unidade.nome_fantasia as nome_unidade,
				cps2.codigo_cliente_pagador,
				p.descricao as produto,
				s.descricao as servico,
				cps2.valor,
				(SELECT count(*) FROM listas_de_preco_produto_servico LPPS
				INNER JOIN listas_de_preco_produto LPP ON(LPP.codigo = LPPS.codigo_lista_de_preco_produto)
				INNER JOIN listas_de_preco LP ON(LP.codigo = LPP.codigo_lista_de_preco)
				INNER JOIN clientes_fornecedores CF ON(CF.codigo_fornecedor = LP.codigo_fornecedor AND CF.ativo = 1)
				WHERE LPPS.codigo_servico = cps2.codigo_servico AND CF.codigo_cliente = cps2.codigo_cliente_pagador) as credenciados
			FROM grupos_economicos_clientes gec
				INNER join grupos_economicos ge on ge.codigo  = gec.codigo_grupo_economico
				INNER join cliente_produto cp on cp.codigo_cliente = gec.codigo_cliente
				INNER join produto p on p.codigo = cp.codigo_produto
				INNER join cliente_produto_servico2 cps2 on cps2.codigo_cliente_produto = cp.codigo
				INNER join servico s on s.codigo = cps2.codigo_servico and s.ativo = 1
				--inner join produto_servico ps on ps.codigo_produto = cp.codigo_produto and ps.codigo_servico = s.codigo
				INNER join cliente unidade on gec.codigo_cliente = unidade.codigo
				INNER join cliente matriz on matriz.codigo = ge.codigo_cliente
			WHERE  ge.codigo_empresa = 1 --  empresa Rhhealth
				and ge.codigo_cliente <> 10011
				and matriz.ativo = 1 and unidade.ativo = 1
			order by p.descricao asc
		";

		$consulta = $this->GrupoEconomicoCliente->query($query);


		if(!empty($consulta)) {

			foreach($consulta as $key => $dados) {
				$consulta[$key] = $dados[0];
				$consulta[$key]['matriz'] = mb_strtoupper($dados[0]['matriz'], mb_internal_encoding());
				$consulta[$key]['nome_unidade'] = mb_strtoupper($dados[0]['nome_unidade'], mb_internal_encoding());
				$consulta[$key]['produto'] = mb_strtoupper($dados[0]['produto'], mb_internal_encoding());
				$consulta[$key]['servico'] = mb_strtoupper($dados[0]['servico'], mb_internal_encoding());
			}
		} else {
			echo "Nenhum Resultado Encontrado.";
			exit;
		}

		echo "Baixando Relatório...";

		ob_clean();
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="relatorio_produtos_servico_clientes'.date('YmdHis').'.csv"');
        header('Pragma: no-cache');

		$cabecalho = '"Código Matriz";"Matriz";"Código Unidade";"Nome Unidade";"Código Cliente Pagador";"Produto";"Serviço";"Valor";"Credenciados";';
		//concatena o cabecalho
        echo $cabecalho."\n";

		foreach($consulta as $consulta_dados){

			$linha  = $consulta_dados['codigo_matriz'].';';
			$linha .= $consulta_dados['matriz'].';';
			$linha .= $consulta_dados['codigo_unidade'].';';
			$linha .= $consulta_dados['nome_unidade'].';';
			$linha .= $consulta_dados['codigo_cliente_pagador'].';';
			$linha .= $consulta_dados['produto'].';';
			$linha .= str_replace("\n", " ", str_replace(";", " ", $consulta_dados['servico'])).';';
			$linha .= Comum::formataMoeda($consulta_dados['valor']).';';
			$linha .= $consulta_dados['credenciados'].';';

			$linha .= "\n";

			echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
		}

		die();	
	}

	public function corrigir_cpf()
	{
		$caminho = $caminho = $_SERVER['DOCUMENT_ROOT']."portal/app/tmp/";

		$arquivo = $caminho."zeros_usuarios.txt";

		$this->loadModel('Usuario');

		$fp = fopen($arquivo, "r");
		if($fp) {
			$i = 0;

			$arr = "";
			$arr71758 = "";
			$arr79667 = "";
			$arr79928 = "";
			$arr79930 = "";
			$arr79933 = "";
			$arr79934 = "";
			$query_update = "";

			while (!feof($fp)) {


				$buffer = fgets($fp);
				$dados = explode(';', $buffer);
				
				$i++;


				if(!isset($dados[0])) {
					continue;
				}

				if($dados[0] == 'Login') {
					continue;
				}

				if($dados[0] == '') {
					continue;
				}


				if(strlen($dados[0]) < 11) {

					$query = "select codigo from usuario where codigo_uperfil = 50 and apelido = '".$dados[0]."';";
					$qDados = $this->Usuario->query($query);

					// debug($qDados);
					//exit;
					echo $dados[0]."<br>";
					$dados[0] = str_pad($dados[0], 11, "0", STR_PAD_LEFT);

					$query_update .= "UPDATE RHHealth.dbo.usuario SET apelido = '".$dados[0]."' WHERE codigo = " . $qDados[0][0]['codigo'].";\n";
				}
				else {
					continue;
				}

	
				// 71758
				// 79667
				// 79928
				// 79930
				// 79933
				// 79934

				if(!isset($dados[3])) {
					continue;
				}

				// switch(trim($dados[3])) {
				// 	case '71758':
				// 		$arr71758 .= implode(";",$dados);
				// 		break;
				// 	case '79667':
				// 		$arr79667 .= implode(";",$dados);
				// 		break;
				// 	case '79928':
				// 		$arr79928 .= implode(";",$dados);
				// 		break;
				// 	case '79930':
				// 		$arr79930 .= implode(";",$dados);
				// 		break;
				// 	case '79933':
				// 		$arr79933 .= implode(";",$dados);
				// 		break;
				// 	case '79934':
				// 		$arr79934 .= implode(";",$dados);
				// 		break;
				// 	default:
				// 		break;
				// }

				// if($i == 200) {
				// 	break;
				// }
			}
			fclose($fp);

			// $novo_arquivo_71758 = $caminho."novo_zero_esqueda_71758.txt";
			// file_put_contents($novo_arquivo_71758,$arr71758);	 
			// // 79667
			// $novo_arquivo_79667 = $caminho."novo_zero_esqueda_79667.txt";
			// file_put_contents($novo_arquivo_79667,$arr79667);
			// // 79928
			// $novo_arquivo_79928 = $caminho."novo_zero_esqueda_79928.txt";
			// file_put_contents($novo_arquivo_79928,$arr79928);
			// // 79930
			// $novo_arquivo_79930 = $caminho."novo_zero_esqueda_79930.txt";
			// file_put_contents($novo_arquivo_79930,$arr79930);
			// // 79933
			// $novo_arquivo_79933 = $caminho."novo_zero_esqueda_79933.txt";
			// file_put_contents($novo_arquivo_79933,$arr79933);
			// // 79934
			// $novo_arquivo_79934 = $caminho."novo_zero_esqueda_79934.txt";
			// file_put_contents($novo_arquivo_79934,$arr79934);

			$novo_arquivo = $caminho."update_usuario.txt";
			file_put_contents($novo_arquivo,$query_update);



			echo "feito";
			exit;

			
		}
	}

	public function riscos_conciliar_medicao() {

		ini_set('max_execution_time', 600);

		// $codigos_matrizes = array(
		// 	20,
		// 	37,
		// 	38,
		// 	79,
		// 	116,
		// 	1942,
		// 	2394,
		// 	8821,
		// 	10011,
		// 	10155,
		// 	51321,
		// 	56977,
		// 	58127,
		// 	58399,
		// 	58522,
		// 	59917,
		// 	69891,
		// 	70148,
		// 	72293,
		// 	80177,
		// 	81071,
		// 	81157,
		// 	81731,
		// 	82476,
		// 	82513,
		// 	84706,
		// 	86300,
		// 	90602,
		// 	92338,
		// 	93852,
		// 	94286,
		// 	94422,
		// 	94514,
		// 	94547,
		// 	94573,
		// 	94577,
		// 	94581,
		// 	94697,
		// 	94848,
		// 	96368,
		// 	96454,
		// 	317218
		// );

		/**
		 * ATENÇÃO!!!!
		 * tecnicas_medicao_ppra
		 * codigo_tec_med_ppra
		 */

		$this->layout = false;
		/**
		* 1. Varrer todos os clientes (matriz) com código de empresa 1;
		* 2. Varrer os riscos cadastrados, ativos e quantitativos com técnica de medição vazia;
		* 3.1 Verificar se há apenas uma técnica de medição cadastrada para o dado grupo econômico / matriz
		* 3.2 Caso sim, usar este para atualizar
		* 3.3 Caso contrário buscar pela última técnica de medição cadastrada para o dado grupo econômico / matriz
		* 4. Atualizar os riscos encontrados então para a técnica de medição encontrada;
		* 5. Caso não encontrar técnica de medição cadastrada, gerar log e prosseguir;
		*/

		$this->loadModel('GrupoEconomicoCliente');				
		$this->loadModel('GrupoExposicaoRisco');
		$this->loadModel('TecnicaMedicaoPpra');		


		$arrayOutputHeaders = array('GRUPO ECONOMICO', 'UNIDADE', 'RISCO', 'SETOR', 'TECNICA MEDICAO ENCONTRADA', 'TECNICA MEDICAO ENCONTRADA UNIDADE', 'RETORNO');
		$arrayOutput = array();

		$outputLinha = '';

		$arrGruposExposicaoRisco = $this->GrupoExposicaoRisco->getClientesSemTecnicaMedicao();

		if(!empty($arrGruposExposicaoRisco) && count($arrGruposExposicaoRisco)) {

			foreach($arrGruposExposicaoRisco as $indiceGruposExposicao => $grupoExposicaoRisco) {

					$outputLinha = $grupoExposicaoRisco['ClienteMatriz']['nome_fantasia'] . ';' . 
					$grupoExposicaoRisco['Cliente']['nome_fantasia'] . ';' .
					$grupoExposicaoRisco['Risco']['nome_agente'] . 
						' (código ' . $grupoExposicaoRisco['Risco']['codigo'] .
						' / código vínculo grupo de exposição ' . $grupoExposicaoRisco['GrupoExposicaoRisco']['codigo'] . ')' . ';' .
					$grupoExposicaoRisco['Setor']['descricao'] . ';';				
	
					try {
						
		
					/**
					 * 
					 * Verificar se há apenas uma técnica de medição cadastrada para aquele grupo econômico/matriz
					 * Caso sim, usar esse para todos as unidades daquela matriz.
					 */
					$unicaTecnicaMedicao = $this->TecnicaMedicaoPpra->verificarObterUnicaTecnicaClienteMatriz($grupoExposicaoRisco['ClienteMatriz']['codigo']);

					if(!empty($unicaTecnicaMedicao)) {

						$ultimaTecnicaMedicaoRiscoCliente = $unicaTecnicaMedicao;
					}
					else {

						$ultimaTecnicaMedicaoRiscoCliente = $this->TecnicaMedicaoPpra->getUltimaClienteMatriz($grupoExposicaoRisco['ClienteMatriz']['codigo']);

					}	

					// echo '<pre>';
					// print_r($ultimaTecnicaMedicaoRiscoCliente);			
					// echo '</pre>';
										

					if(empty($ultimaTecnicaMedicaoRiscoCliente)) {

						$outputLinha .= '-;-;';

						$outputLinha .= 'Não foi encontrada técnica de medição cadastrada para cliente ' .
						$grupoExposicaoRisco['Cliente']['razao_social'] . '(' . $grupoExposicaoRisco['Cliente']['codigo']  . ').';

						$arrayOutput[] = $outputLinha;
					}	
					else {

						$outputLinha .= $ultimaTecnicaMedicaoRiscoCliente['TecnicaMedicaoPpra']['nome'] .
						(!empty($ultimaTecnicaMedicaoRiscoCliente['TecnicaMedicaoPpra']['descricao']) &&
							trim($ultimaTecnicaMedicaoRiscoCliente['TecnicaMedicaoPpra']['descricao']) != '' ?
							' - ' . $ultimaTecnicaMedicaoRiscoCliente['TecnicaMedicaoPpra']['descricao'] : '') . ';';

						$outputLinha .= $ultimaTecnicaMedicaoRiscoCliente['Cliente']['nome_fantasia'] . 
							' (código ' . $ultimaTecnicaMedicaoRiscoCliente['Cliente']['codigo'] . ')' . ';';

						
						
						//$this->GrupoExposicaoRisco->begin();

						$this->GrupoExposicaoRisco->save(array(
							'codigo' => $grupoExposicaoRisco['GrupoExposicaoRisco']['codigo'],
							'codigo_tec_med_ppra' => $ultimaTecnicaMedicaoRiscoCliente['TecnicaMedicaoPpra']['codigo']
						));						

						$outputLinha .= 'Técnica de medição atualizada para ' .
							$ultimaTecnicaMedicaoRiscoCliente['TecnicaMedicaoPpra']['nome'] .
							' (código ' . $ultimaTecnicaMedicaoRiscoCliente['TecnicaMedicaoPpra']['codigo'] . ')';

						//$this->GrupoExposicaoRisco->rollback();
						//$this->GrupoExposicaoRisco->commit();
					}

				}
				catch(Exception $e) {

					$outputLinha .= $outputLinha .= 'Não foi possível atualizar a técnica de medição.';

					if(!empty($ultimaTecnicaMedicaoRiscoCliente['TecnicaMedicao'])) {

						$outputLinha .= ' Tentou atualizar para ' . $ultimaTecnicaMedicaoRiscoCliente['TecnicaMedicao']['nome'] .
							' (código ' . $outputLinha['TecnicaMedicao']['codigo'] . ').';
					}

					//$this->GrupoExposicaoRisco->rollback();
				}

				if(!empty($outputLinha))
					$arrayOutput[] = $outputLinha;					

			}		
		}		
		
		ob_clean();
		header('Content-Encoding: UTF-8');
		header("Content-Type: application/force-download;charset=utf-8");
		header('Content-Disposition: attachment; filename="conciliacao_risco_tecnica_medicao_vazia_' . date('YmdHis') . '.csv"');
		header('Pragma: no-cache');
		
		echo implode($arrayOutputHeaders, ';') . PHP_EOL;
		echo implode($arrayOutput, PHP_EOL);
		exit;
	}	
}