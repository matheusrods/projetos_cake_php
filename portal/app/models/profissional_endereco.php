<?php
class ProfissionalEndereco extends AppModel {
	public $name = 'ProfissionalEndereco';
	public $tableSchema = 'publico';
	public $databaseTable = 'dbBuonny';
	public $useTable = 'profissional_endereco';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_profissional_endereco'));
	
	public function validarDados($data){
		$this->validate = array(
			'endereco_cep' => array(
				'rule' => 'NotEmpty',
				'message' => 'Informe o CEP'
			),
			'codigo_endereco' => array(
				'rule' => 'NotEmpty',
				'message' => 'Selecione o endereço'
			),
			'numero' => array(
				'rule' => 'NotEmpty',
				'message' => 'Informe o número'
			),
		);
	
		return $this->saveAll($data, array('validate' => 'only'));
	}
	
	public function salvarProfissionalEnderecoScorecard($endereco, $codigo_profissional){
		if( $codigo_profissional > 0 && $endereco['codigo_endereco'] && $endereco['endereco_cep'] ) {
			App::import('Model', 'TipoContato');
			$this->ProfissionalEnderecoLog  = ClassRegistry::init('ProfissionalEnderecoLog');
			//$this->Behaviors->attach('Loggable', array('foreign_key' => 'codigo_profissional_endereco'));	
			$this->deleteAll(array('codigo_profissional'=>$codigo_profissional));	
			$endereco['codigo_profissional'] = $codigo_profissional;
			$endereco['codigo_tipo_contato'] = TipoContato::TIPO_CONTATO_RESIDENCIAL;
			$this->create();
			$this->save($endereco, array('validate'=>false));
			$profissional_endereco_log['ProfissionalEnderecoLog'] = $this->ProfissionalEnderecoLog->id;		
			return $profissional_endereco_log;
		} else {
			return false;
		}
	}

	public function salvarProfissionalEndereco($endereco, $codigo_profissional, $importacao = false){
		if( $codigo_profissional > 0 && $codigo_profissional>0 ) {
			App::import('Model', 'TipoContato');
			$this->ProfissionalEnderecoLog  = ClassRegistry::init('ProfissionalEnderecoLog');
			if($importacao)
				$this->Behaviors->attach('Loggable', array('foreign_key' => 'codigo_profissional_endereco'));	
			
			$this->deleteAll(array('codigo_profissional'=>$codigo_profissional));	

			$this->create();
			$this->save($endereco, array('validate'=>false));
			$profissional_endereco_log['ProfissionalEnderecoLog'] = $this->ProfissionalEnderecoLog->id;		
			return $profissional_endereco_log;
		} else {
			return false;
		}
	}
}