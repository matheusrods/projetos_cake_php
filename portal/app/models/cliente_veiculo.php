<?php
App::import('Model', 'Servico');
App::import('Model', 'TipoFrota');
class ClienteVeiculo extends AppModel {

	var $name = 'ClienteVeiculo';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cliente_veiculo';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_cliente_veiculo'));
	var $validate = array(
		'codigo_cliente' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o codigo do cliente'
			),
			'combinacao_unica' => array(
				'rule' => 'combinacao_unica',
				'message' => 'Este vínculo já foi feito',
			),
		),
	);

	const CODIGO_SISTEMA_PORTAL = 12;

	function combinacao_unica() {
		$conditions = array('codigo_veiculo' => $this->data[$this->name]['codigo_veiculo'], 'codigo_cliente' => $this->data[$this->name]['codigo_cliente'], 'codigo_sistema' => $this->data[$this->name]['codigo_sistema']);
		if (isset($this->data[$this->name]['codigo'])) {
			$conditions[] = array('codigo !=' =>$this->data[$this->name]['codigo']);
		}
		return $this->find('count', compact('conditions')) == 0;
	}

	function bindCliente() {
		$this->bindModel(array(
			'belongsTo' => array(
				'Cliente' => array(
					'foreignKey' => 'codigo_cliente'
				)
			)
		));
	}

	function bindVeiculo() {
		$this->bindModel(array(
			'belongsTo' => array(
				'Veiculo' => array(
					'foreignKey' => 'codigo_veiculo'
				)
			)
		));
	}

	public function incluirDiferenciado($data){

		$clienteVeiculo = array(
			'ClienteVeiculo'	=> array(
				'codigo_cliente'			=> $data['codigo_cliente'],
				'codigo_veiculo'			=> $data['codigo_veiculo'],
				'data_inclusao'	 			=> date('Y-m-d H:i:s'),
				'codigo_usuario_inclusao' 	=> $data['Usuario']['codigo'],
				'codigo_sistema'			=> self::CODIGO_SISTEMA_PORTAL,
				'codigo_tipo_frota'			=> isset($data['codigo_tipo_frota'])?$data['codigo_tipo_frota']:NULL,
			)
		);

		$this->create('ClienteVeiculo');
		return ($this->save($clienteVeiculo));
	}

	public function verificaClienteVeiculo($codigo_cliente,$codigo_veiculo){
		$conditions = array('codigo_cliente' => $codigo_cliente,'codigo_veiculo' => $codigo_veiculo, 'codigo_sistema' => self::CODIGO_SISTEMA_PORTAL);
		return ($this->find('count',compact('conditions')) > 0);
	}

	public function carregarClienteVeiculo($codigo_cliente,$codigo_veiculo){
		$conditions = array('codigo_cliente' => $codigo_cliente,'codigo_veiculo' => $codigo_veiculo, 'codigo_sistema' => self::CODIGO_SISTEMA_PORTAL);
		return $this->find('first',compact('conditions'));
	}

	public function cancelar($documento,$placa){
		$listagem 	= $this->listarPorDocumentoPlaca($documento,$placa);
		try{
			if($listagem){
				foreach ($listagem as $dado){
					if(!$this->delete($dado[$this->name][$this->primaryKey]))
						throw new Exception("Erro ao excluir vinculo no Portal");
				}
			}
			return true;
		} catch ( Exception $ex ) {
			return false;
		}

	}

	public function listarPorDocumentoPlaca($documento,$placa){
		$this->bindCliente();
		$this->bindVeiculo();

		$conditions = array('Cliente.codigo_documento' => $documento, 'Veiculo.placa' => $placa, 'codigo_sistema' => self::CODIGO_SISTEMA_PORTAL);
		return $this->find('all',compact('conditions'));

	}

	public function sincronizaVeiculo(&$data){
		$this->TipoFrota =& classRegistry::init('TipoFrota');
		$cliente_vinculo = $this->carregarClienteVeiculo($data['Cliente']['codigo'],$data['Veiculo']['codigo']);

		$tvco   = array();
		$tvco[] = isset($data['TVtraVeiculoTransportador']['vtra_tvco_codigo'])? $data['TVtraVeiculoTransportador']['vtra_tvco_codigo']:NULL;
		$tvco[] = isset($data['TVembVeiculoEmbarcador']['vemb_tvco_codigo'])? $data['TVembVeiculoEmbarcador']['vemb_tvco_codigo']:NULL;

		$clienteVeiculo = array(
			'ClienteVeiculo'	=> array(
				'codigo_cliente'			=> $data['Cliente']['codigo'],
				'codigo_veiculo'			=> $data['Veiculo']['codigo'],
				'data_inclusao'	 			=> date('Y-m-d H:i:s'),
				'codigo_usuario_inclusao' 	=> $data['Usuario']['codigo'],
				'codigo_sistema'			=> self::CODIGO_SISTEMA_PORTAL,
			)
		);
		$clienteVeiculo['ClienteVeiculo']['codigo_tipo_frota'] = $this->TipoFrota->converteTipoGuardian($tvco);

		if(!$cliente_vinculo){
			$this->create();
			return ($this->save($clienteVeiculo));
		}else{
			$clienteVeiculo['ClienteVeiculo']['codigo'] = $cliente_vinculo['ClienteVeiculo']['codigo'];
			return ($this->atualizar($clienteVeiculo));
		}
		return true;
	}

	function cancelar_veiculo($dados,$in_another_transaction = FALSE){
		$ClienteVeiculo 			=& classRegistry::init('ClienteVeiculo');
		$MCarroEmpresa 				=& classRegistry::init('MCarroEmpresa');
		$TPjurPessoaJuridica		=& classRegistry::init('TPjurPessoaJuridica');

		try{

			if(!$in_another_transaction){
				if($this->useDbConfig != 'test_suite')
					$TPjurPessoaJuridica->query('BEGIN TRANSACTION');
				$this->query('BEGIN TRANSACTION');
			}


			// PORTAL
			if(!$ClienteVeiculo->cancelar($dados['Cliente']['codigo_documento'],$dados['Veiculo']['placa']))
				throw new Exception("Falha na exclusão do vinculo do PORTAL");
			// PORTAL [FIM]

			//-------------------------
			
			// MONITORA
			if(!$MCarroEmpresa->cancelar($dados['Cliente']['codigo_documento'],$dados['Veiculo']['placa']))
				throw new Exception("Falha na exclusão do vinculo do MONITORA");
			// MONITORA [FIM]

			//-------------------------

			// GUARDIAN
			if(!$TPjurPessoaJuridica->cancelar_veiculo($dados['Cliente']['codigo_documento'],$dados['Veiculo']['placa']))
				throw new Exception("Falha na exclusão do vinculo do GUARDIAN");
			// GUARDIAN [FIM]
			
			//-------------------------

			if(!$in_another_transaction){
				if($this->useDbConfig != 'test_suite')
					$TPjurPessoaJuridica->commit();
				
				$this->commit();
			}

			return TRUE;

		} catch( Exeception $ex ) {
			if(!$in_another_transaction){
				if($this->useDbConfig != 'test_suite')
					$TPjurPessoaJuridica->rollback();

				$this->rollback();
			}
			
			$this->invalidate('codigo',$ex->getMessage());
			return FALSE;

		}
	}

	function vincular($dados, $in_another_transaction = false) {
		$this->bindVeiculo();
		$this->Cliente =& classRegistry::init('Cliente');
		$this->ClienteSubTipo =& classRegistry::init('ClienteSubTipo');
		$this->TPjurPessoaJuridica =& classRegistry::init('TPjurPessoaJuridica');
		$this->TVembVeiculoEmbarcador =& classRegistry::init('TVembVeiculoEmbarcador');
		$this->TVtraVeiculoTransportador =& classRegistry::init('TVtraVeiculoTransportador');
		$this->TPessPessoa =& classRegistry::init('TPessPessoa');
		
		
		$dados['codigo_veiculo'] = $this->Veiculo->buscaCodigodaPlaca($dados['placa']);
		
		try{
			if(!$in_another_transaction){
				if($this->useDbConfig != 'test_suite') $this->TVtraVeiculoTransportador->query('begin transaction');
				$this->query('begin transaction');
			}

				$cnpj_cliente = $this->Cliente->read(array('codigo_documento', 'codigo_cliente_sub_tipo'), $dados['codigo_cliente']);

				if(!isset($cnpj_cliente['Cliente']['codigo_documento']))
					throw new Exception("Cliente não encontrado");
			
			$cliente_tipo = $this->ClienteSubTipo->subTipo($cnpj_cliente['Cliente']['codigo_cliente_sub_tipo']);
			$dados['codigo_documento']	=	$cnpj_cliente['Cliente']['codigo_documento'];
			//USUARIO IMPORTACAO
			//Se for integracao nao tera usuario logado. Do contratario a Model irá sobrepor esse codigo com o codigo do usuario logado
			if( empty($_SESSION['Auth']['Usuario']['codigo']))
				$dados['codigo_usuario_inclusao'] = 2;
			if($cliente_tipo == ClienteSubTipo::SUBTIPO_TRANSPORTADOR){
				if(!$this->TVtraVeiculoTransportador->incluirPorCodigoDocumentoEPlaca($dados))
					throw new Exception("Não foi possivel incluir na TVtraVeiculoTransportador");
			}else{
				if(!$this->TVembVeiculoEmbarcador->incluirPorCodigoDocumentoEPlaca($dados))
					throw new Exception("Não foi possivel incluir na TVembVeiculoEmbarcador");
			}
			if(!$this->find('count', array('conditions' => array('codigo_cliente' => $dados['codigo_cliente'], 'codigo_veiculo' => $dados['codigo_veiculo'], 'codigo_sistema' => $dados['codigo_sistema'] ))))
				if(!$this->incluir($dados)) throw new Exception("Não foi possivel incluir na ClienteVeiculo");

			if(!$in_another_transaction){
				if($this->useDbConfig != 'test_suite') $this->TVtraVeiculoTransportador->commit();
				$this->commit();
			}
			return true;

		}catch(Exception $ex){
			if(!$in_another_transaction){
				if($this->useDbConfig != 'test_suite') $this->TVtraVeiculoTransportador->rollback();
				$this->rollback();
			}
			return false;

		}
	}
}

?>
