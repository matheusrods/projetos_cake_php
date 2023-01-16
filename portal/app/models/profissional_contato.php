<?php
App::import('Model', 'ContatoRetornoBase');
class ProfissionalContato extends ContatoRetornoBase {

	public $name = 'ProfissionalContato';
	public $tableSchema = 'publico';
	public $databaseTable = 'dbBuonny';
	public $useTable = 'profissional_contato';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');
	
	function bindProfissional() {
		$this->bindModel(array(
				'belongsTo' => array(
					'Profissional' => array(
						'foreignKey' => FALSE,
						'conditions' => 'profissional_codigo = codigo'
						),
				),
		));
	}	

	public function atualizar_contato_motorista($data){
		$Profissional 	=& ClassRegistry::Init('Profissional');
		$motorista		= $Profissional->buscaPorCPF($data['motorista_cpf']);

		if(isset($data['telefone']) && !empty($data['telefone'])){
			
			$contato 		= $this->buscaPorTipoEProfissional(5,$motorista['Profissional']['codigo']);
			if(empty($contato)){
				$contato['ProfissionalContato']['codigo_profissional'] 	= $motorista['Profissional']['codigo'];
				$contato['ProfissionalContato']['nome'] 				= $motorista['Profissional']['nome'];
				$contato['ProfissionalContato']['codigo_tipo_contato'] 	= 2; //COMERCIAL
				$contato['ProfissionalContato']['codigo_tipo_retorno'] 	= 5; //CELULAR MOTORISTA
				$contato['ProfissionalContato']['ddi'] 					= 55;
				$contato['ProfissionalContato']['data_inclusao']		= date('Y-m-d H:i:s');
				$contato['ProfissionalContato']['codigo_usuario_inclusao']= 2;
			}

		
			$contato['ProfissionalContato']['descricao']			= $data['telefone'];
			
			$this->create();
			if(!$this->save($contato))
				throw new Exception('Erro ao salvar as informações do telefone do motorista');

			//Sincronia de número de telefones entre as bases
			
			try
			{

				$Motorista 	=& ClassRegistry::Init('Motorista');
				$Motorista->atualizaCelular($data['motorista_cpf'], $data['telefone']);

				// PGSQL não será sincronizado, pois não há dados de contatos sendo utilizados
				//$Pessoa     =& ClassRegistry::Init('TPfisPessoaFisica');
				//$Pessoa->atualizaCelular($data['motorista_cpf'], $data['telefone']);


			}
			catch(Exception $ex)
			{
				// Pode dar erro caso o Motorista não seja localizado nas outras bases
				// Não faz nada para não quebrar o processo, gerando a exceção interna
			}


		}

		if(isset($data['radio']) && !empty($data['radio'])){
			
			$contato 		= $this->buscaPorTipoEProfissional(6,$motorista['Profissional']['codigo']);
			if(empty($contato)){
				$contato['ProfissionalContato']['codigo_profissional'] 	= $motorista['Profissional']['codigo'];
				$contato['ProfissionalContato']['nome'] 				= $motorista['Profissional']['nome'];
				$contato['ProfissionalContato']['codigo_tipo_contato'] 	= 2; //COMERCIAL
				$contato['ProfissionalContato']['codigo_tipo_retorno'] 	= 6; //RADIO
				$contato['ProfissionalContato']['ddi'] 					= 55;
				$contato['ProfissionalContato']['data_inclusao']		= date('Y-m-d H:i:s');
				$contato['ProfissionalContato']['codigo_usuario_inclusao']= 2;
			}
	
			$contato['ProfissionalContato']['descricao']			= $data['radio'];
			
			$this->create();
			if(!$this->save($contato))
				throw new Exception('Erro ao salvar as informações do radio do motorista');

		}

	}

	public function buscaPorTipoEProfissional($tipo,$codigo_profissional){
		$conditions = array('codigo_tipo_retorno' => $tipo, 'codigo_profissional' => $codigo_profissional);
		return $this->find('first',compact('conditions'));
	}
	
	public function salvarProfissionalContatoScorecard($contatos, $codigo_profissional){
		$this->ProfissionalContatoLog = ClassRegistry::init('ProfissionalContatoLog');
		$this->Behaviors->attach('Loggable', array('foreign_key' => 'codigo_profissional_contato'));
		
		$this->deleteAll(array('codigo_profissional'=>$codigo_profissional));
		
		$profissional_contato_logs = array('ProfissionalContatoLog'=>array());
		foreach($contatos as $key=>$contato){
			if(!empty($contato['codigo_tipo_retorno'])){
				$contato['codigo_profissional'] = $codigo_profissional;
				$this->create();
				$this->save($contato, array('validate'=>false));
				$profissional_contato_logs['ProfissionalContatoLog'][] = $this->ProfissionalContatoLog->id;
			}
		}
		return $profissional_contato_logs;
	}

	public function salvarProfissionalContato($contatos, $codigo_profissional){

		$this->ProfissionalContatoLog = ClassRegistry::init('ProfissionalContatoLog');
		$this->Behaviors->attach('Loggable', array('foreign_key' => 'codigo_profissional_contato'));
		
		$this->deleteAll(array('codigo_profissional'=>$codigo_profissional));
		
		$profissional_contato_logs = array('ProfissionalContatoLog'=>array());
		foreach($contatos as $key=>$contato){
			if(!empty($contato['codigo_tipo_retorno'])){
				$contato['codigo_profissional'] = $codigo_profissional;
				$this->create();
				$this->save($contato, array('validate'=>false));
				$profissional_contato_logs['ProfissionalContatoLog'][] = $this->ProfissionalContatoLog->id;
			}
		}
		return $profissional_contato_logs;
	}

	public function buscaPorCPF($cpf){
		$this->Profissional = ClassRegistry::init('Profissional');
		$this->ProfissionalContato = ClassRegistry::init('ProfissionalContato');
		$this->TipoContato = ClassRegistry::init('TipoContato');

		return $this->ProfissionalContato->find('all',array(
			'fields' => array('ProfissionalContato.codigo_profissional','ProfissionalContato.descricao'),
			'joins' => array(
				array(
					'table' => "{$this->Profissional->databaseTable}.{$this->Profissional->tableSchema}.{$this->Profissional->useTable}",
					"alias" => "Profissional",
					"type" => "INNER",
					"conditions" => array(
						"Profissional.codigo = ProfissionalContato.codigo_profissional",
					)
				),
				array(
					'table' => "{$this->TipoContato->databaseTable}.{$this->TipoContato->tableSchema}.{$this->TipoContato->useTable}",
					"alias" => "TipoContato",
					"type" => "INNER",
					"conditions" => array(
						"TipoContato.codigo = ProfissionalContato.codigo_tipo_contato",
					)
				),
			),
			'conditions' => array(
				'Profissional.codigo_documento' => $cpf,
					'TipoContato.codigo ' => array(1,2),  // 1 - Telefone Residencial | 2 - Telefone Comercial
				),
			)
		);
	}
}