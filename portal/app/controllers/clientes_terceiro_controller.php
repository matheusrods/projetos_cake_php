<?php
class ClientesTerceiroController extends AppController {
    public $name = 'ClientesTerceiro';
    public $components = array('Maplink');
    var $uses = array('MMonClienteTerceiro');

	public function dispara_sincroniza(){
		$this->layout = false;
		$this->loadModel('ClientEmpresa');

		$group 		= array('ClientEmpresa.Codigo','ClientEmpresa.Raz_Social');
		$joins		= array(
						array(
							'table' => 'Monitora.dbo.MON_ClienteTerceiro',
							'alias'	=> 'TMonClienteTerceiro',
							'conditions' => 'TMonClienteTerceiro.FAV_Codigo COLLATE DATABASE_DEFAULT = ClientEmpresa.Codigo COLLATE DATABASE_DEFAULT',
							'type'	=> 'INNER'
						),
					);
		$conditions = array(
							'NOT' => array('TMonClienteTerceiro.CID_Codigo' => NULL),
							'TMonClienteTerceiro.codigo_trafegus_refe_referencia' => NULL);

		$total = $this->ClientEmpresa->find('list',compact('conditions','group','joins'));
		$total = count($total);
		$this->set(compact('total'));
	}

	public function sincroniza($pagina = 1,$tamanho = 100){
		$this->loadModel('ClientEmpresa');
		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('TRefeReferencia');
		$this->loadModel('Cidade');
		$simbolos = array('.','-','/');

		// Tempo de execução infinito!
		ini_set('max_execution_time',0);

		// Localiza todos os clientes que possuem alvos cadastrados
		$lista 	= $this->ClientEmpresa->paginaClientesMonCliente($pagina,$tamanho);

		
		//$alvos_log = fopen('/tmp/alvos_log('.$pagina.').txt','w');
		$alvos_log = '/tmp/alvos_log('.$pagina.').txt';
		foreach ($lista as $cliente) {

			// Localiza o cliente no trafegus
			$conditions 	= array("REPLACE(REPLACE(REPLACE(pjur_cnpj,'.',''),'/',''),'-','')" => str_replace($simbolos, '', $cliente[0]['CNPJCPF']));
			$cliente_pjur 	= $this->TPjurPessoaJuridica->find('first',compact('conditions'));
			
			//fwrite($alvos_log," Cliente ".$cliente[0]['Codigo']." - ".$cliente[0]['Raz_Social']." \r\n");
			file_put_contents($alvos_log, " Cliente ".$cliente[0]['Codigo']." - ".$cliente[0]['Raz_Social']." \r\n", FILE_APPEND);
			if($cliente_pjur){

				// Localiza os alvos do cliente no monitora
				$fields		= array(
								'MMonClienteTerceiro.CLT_Codigo',
								'MMonClienteTerceiro.codigo_trafegus_refe_referencia',
								'MMonClienteTerceiro.CLT_RazaoSocial',
								'MMonClienteTerceiro.CLT_Endereco',
								'MMonClienteTerceiro.CLT_Bairro',
								'MMonClienteTerceiro.CLT_CNPJ',
								'MMonClienteTerceiro.FAV_Codigo',
								'Cidade.Codigo',
								'Cidade.Descricao',
								'Cidade.Estado',
							);
				$conditions	= array('FAV_Codigo' => $cliente[0]['Codigo'], 'codigo_trafegus_refe_referencia' => NULL,'NOT' => array('Cidade.Codigo' => NULL));
				$terceiros 	= $this->MMonClienteTerceiro->find('all',compact('conditions','fields'));
				foreach ($terceiros as $terceiro) {

					// Busca a cidade equivalente no trafegus
					$tcidade = $this->Cidade->buscaCodigoCidadeTrafegus($terceiro['Cidade']['Codigo']);

					if($tcidade){
						$new_local = array(
										'endereco' 	=> $terceiro['MMonClienteTerceiro']['CLT_Endereco'],
										'bairro' 	=> $terceiro['MMonClienteTerceiro']['CLT_Bairro'],
										'numero' 	=> '',
										'cep' 		=> '',
										'cidade'	=> array(
														'nome'	=> $terceiro['Cidade']['Descricao'],
														'estado'=> $terceiro['Cidade']['Estado'])
									);
						
						$new_xy = $this->Maplink->busca_xy($new_local);
						
						if($new_xy){
							// Novo código nextval() para referencia
							$novo_codigo	 = $this->TRefeReferencia->novo_codigo_direto();
							$refe_referencia = array(
										'TRefeReferencia' => array(
											'refe_codigo' 			=> $novo_codigo,
											'refe_descricao'		=> $terceiro['MMonClienteTerceiro']['CLT_RazaoSocial'],
											'refe_latitude'			=> $new_xy->getXYResult->y,
											'refe_longitude'		=> $new_xy->getXYResult->x,
											'refe_cref_codigo'		=> 53,
											'refe_pess_oras_codigo_local' 	=> $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],
											'refe_raio'				=> 0.5,
											'refe_utilizado_sistema'=> 'N',
											'refe_data_cadastro'	=> date('Y-m-d H:i:s'),
											'refe_cida_codigo'		=> $tcidade['TCidaCidade']['cida_codigo'],
											'refe_empresa_terceiro' => $terceiro['MMonClienteTerceiro']['CLT_RazaoSocial'],
											'refe_cnpj_empresa_terceiro' 	=> $terceiro['MMonClienteTerceiro']['CLT_CNPJ'],
											'refe_endereco_empresa_terceiro'=> $terceiro['MMonClienteTerceiro']['CLT_Endereco'],
											'refe_bairro_empresa_terceiro' 	=> $terceiro['MMonClienteTerceiro']['CLT_Bairro'],
											'refe_usuario_adicionou'=> 'SINCRONIZA',
										)
									);
							
							
							try{

								$this->MMonClienteTerceiro->query('begin transaction');
								$this->TRefeReferencia->query('begin transaction');

								$this->TRefeReferencia->create();
								if(!$this->TRefeReferencia->save($refe_referencia))
									throw new Exception('Falha ao salvar a REFE_REFERENCIA para CLT_Codigo: '.$terceiro['MMonClienteTerceiro']['CLT_Codigo']);

								$terceiro['MMonClienteTerceiro']['codigo_trafegus_refe_referencia'] = $novo_codigo;
								if(!$this->MMonClienteTerceiro->save($terceiro))
									throw new Exception('Falha ao atualizar MON_ClienteTerceiro CLT_Codigo: '.$terceiro['MMonClienteTerceiro']['CLT_Codigo']);

								$this->MMonClienteTerceiro->commit();
								$this->TRefeReferencia->commit();
								//fwrite($alvos_log," =>Sucesso: Nova REFE_REFERENCIA ".$novo_codigo." criado com sucesso! \r\n");
								file_put_contents($alvos_log, " =>Sucesso: Nova REFE_REFERENCIA ".$novo_codigo." criado com sucesso! \r\n", FILE_APPEND);
							} catch (Exception $ex) {

								$this->MMonClienteTerceiro->rollback();
								$this->TRefeReferencia->rollback();
								fwrite($alvos_log," =>Erro: ".$ex->getMessage()." \r\n");

							}
							
						} else {
							$endereco = 'CLT_Codigo:'.$terceiro['MMonClienteTerceiro']['CLT_Codigo'].', ';
							foreach ($new_local as $key => $value) {
								if(is_array($value)){
									foreach ($value as $subkey => $subvalue) {
										$endereco .= $subkey.': '.$subvalue.', ';
									}
								} else {
									$endereco .= $key.': '.$value.', ';
								}
								
							}

							//fwrite($alvos_log," =>Erro: Coordenadas nao localizadas para o objeto ".$endereco." \r\n");
							file_put_contents($alvos_log, " =>Erro: Coordenadas nao localizadas para o objeto ".$endereco." \r\n", FILE_APPEND);
						}
					} else {
						//fwrite($alvos_log," =>Erro: Cidade ".$terceiro['Cidade']['Codigo']." - ".$terceiro['Cidade']['Descricao']." - ".$terceiro['Cidade']['Estado']." nao localizada no dbTrafegus \r\n");
						file_put_contents($alvos_log, " =>Erro: Cidade ".$terceiro['Cidade']['Codigo']." - ".$terceiro['Cidade']['Descricao']." - ".$terceiro['Cidade']['Estado']." nao localizada no dbTrafegus \r\n", FILE_APPEND);
					}

				}
			} else {
				//fwrite($alvos_log," =>Erro: Cliente nao localizado no dbTrafegus \r\n");
				file_put_contents($alvos_log, " =>Erro: Cliente nao localizado no dbTrafegus \r\n", FILE_APPEND);
			}
			//fwrite($alvos_log,"==================================================================== \r\n");
			file_put_contents($alvos_log, "==================================================================== \r\n", FILE_APPEND);
			//if(++$contador > 0) break;
		}
		
		//fclose($alvos_log);
		exit;
	}

	public function info(){
		ini_set('max_execution_time',0);
		phpinfo();
		exit;
	}

}

?>
