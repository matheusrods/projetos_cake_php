<?php
class ProprietarioEndereco extends AppModel {
	public $name = 'ProprietarioEndereco';
	public $tableSchema = 'publico';
	public $databaseTable = 'dbBuonny';
	public $useTable = 'proprietario_endereco';
	public $primaryKey = 'codigo';
	public $actsAs = array(
		'Secure',
		'Loggable' => array('foreign_key' => 'codigo_proprietario_endereco')
	);

	
    public $validate = array(
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

	public function validarDados($data, $tem_veiculo){
		if(!$tem_veiculo){
			$this->validate = array();
		}else{
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
		}
	
		return $this->saveAll($data, array('validate' => 'only'));
	}
	
	public function salvarProprietarioEnderecoScorecard($endereco, $codigo_proprietario, $origem_portal=FALSE){
		if(!$this->validarDados($endereco, true))
			return array('ProprietarioEnderecoLog'=>null);
		
		App::import('Model', 'TipoContato');
		$this->ProprietarioEnderecoLog = ClassRegistry::init('ProprietarioEnderecoLog');
		if( $origem_portal )
			$this->Behaviors->attach('Loggable', array('foreign_key' => 'codigo_proprietario_endereco'));	
		$this->deleteAll(array('codigo_proprietario'=>$codigo_proprietario));	
		$endereco['codigo_proprietario'] = $codigo_proprietario;
		$endereco['codigo_tipo_contato'] = TipoContato::TIPO_CONTATO_RESIDENCIAL;
		$this->create();
		$this->save($endereco, array('validate'=>false));
		$proprietario_endereco_log['ProprietarioEnderecoLog'] = $this->ProprietarioEnderecoLog->id;
		
		return $proprietario_endereco_log;
	}

    public function buscaCodigoEndereco ($codigo_proprietario){
         
         return $this->find('all',array('order'=> 'codigo desc' ,'conditions'=> array('codigo_proprietario'=>$codigo_proprietario)));    	
    }

    public function buscaCodigoEnderecoProprietario($codigo_proprietario,$codigo_tipo_contato = 2){
        return $this->find('first',array(
    		'conditions'=> array(
    			'codigo_proprietario'=>$codigo_proprietario,
    			'codigo_tipo_contato'=>$codigo_tipo_contato
    		),
    		'order' => 'codigo DESC' 
        ));    	
    }

    function salvar($dados) {
    	if (empty($dados['ProprietarioEndereco']['codigo'])) {
			return $this->incluir($dados);
		} else {
			return $this->atualizar($dados);
		}
    }

}