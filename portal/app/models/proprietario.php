<?php
class Proprietario extends AppModel {
    var $name = 'Proprietario';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'proprietario';
    var $primaryKey = 'codigo';
    var $actsAs  = array(
        'Secure',
        'SincronizarCodigoDocumento',
        'Loggable' => array('foreign_key' => 'codigo_proprietario')
    );
    var $validate = array(
		'codigo_documento' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o documento',
             ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'CPF/CNPJ já existente na base',
                'on' => 'create'
            ),
            'documentoValido' => array(
                'rule' => 'documentoValido',
                'message' => 'CPF/CNPJ é invalido!',
                'on' => 'create'
            )

        ),
		'nome_razao_social'=> array(
			'notEmpty' => array(
				'rule'  => 'notEmpty',
				'message' =>'Nome /Razão Social é obrigatório'
		 	)
        ) 
    );   
    		
    public function converteFiltroEmCondition($dados){
        $condition = array();
        if (isset($dados['cpfcnpj']) && !empty($dados["cpfcnpj"])) {
            $codigo_documento = preg_replace('/\W/', '', $dados["cpfcnpj"] );
            $condition["codigo_documento LIKE"] = $codigo_documento."%";
        }
        if (isset($dados['nomerazaosocial']) && !empty($dados["nomerazaosocial"])) {
            $condition["nome_razao_social LIKE"] = "%".$dados["nomerazaosocial"]."%";
        }
        return $condition; 
    }

    function verficarPessoaFisicaJuridica($codigo) {
        $cod_documento = $this->find('first',
            array(
                'conditions' => array(
                    'codigo' => $codigo
                ),
                'fields' => 'codigo_documento'
            )
        );

        if(strlen($cod_documento['Proprietario']['codigo_documento']) == 11){
            return true;
        }
        else{
            return false;
        }
    }
    
    function existeProprietario($codigo){ 
        $result = $this->find('first', array(
            'fields' => 'codigo_documento',
            'conditions' => array(
                'codigo_documento' => $codigo
            )
        ));
        if (empty($result)) return 0;
        return 1;
    }

    function buscaDocumento($codigo){
        $result = $this->find('first', array('fields' => 'codigo_documento', 'conditions' => array('codigo' => $codigo)));
        if (empty($result)) return null;
        return $result['Proprietario']['codigo_documento'];
    }

    function buscaCodigoProprietario($codigo){
        $result = $this->find('first', array('fields' => 'codigo', 'conditions' => array('codigo_documento' => $codigo)));
        if (empty($result)) return null;
        return $result['Proprietario']['codigo'];
    }

    function buscaProprietario($documento){
         $result = $this->find('first', array('fields' =>'*,EnderecoCep.cep,ProprietarioEndereco.numero,
    ProprietarioEndereco.complemento, ProprietarioEndereco.codigo_endereco',
         'joins' => array(
            array(
                'table' => 'dbBuonny.publico.proprietario_endereco',
                'alias' => 'ProprietarioEndereco',
                'type' => 'INNER',
                'conditions' => array(
                    'ProprietarioEndereco.codigo_proprietario = Proprietario.codigo'
                ) ),
                
            array(
                'table' => 'dbBuonny.publico.endereco',
                'alias' => 'Endereco',
                'type' => 'INNER',
                'conditions' => array(
                    'ProprietarioEndereco.codigo_endereco = Endereco.codigo'
                ) ),
            array(
                'table' => 'dbBuonny.publico.endereco_cep',
                'alias' => 'EnderecoCep',
                'type' => 'INNER',
                'conditions' => array(
                    'Endereco.codigo_endereco_cep = EnderecoCep.codigo'
                ) )  

            
        ),'conditions' => array('codigo_documento' => $documento)));
         if (empty($result)) return null;
         return $result;

    }
    
    function documentoValido() {
    	if(empty($this->data[$this->name]['codigo_documento']))
    		return true; 
    	$Documento  =& ClassRegistry::init('Documento');
    	return $Documento->isCPF($this->data[$this->name]['codigo_documento']) || $Documento->isCNPJ($this->data[$this->name]['codigo_documento']);
    }
    
    public function validarDados($data, $tem_veiculo){
    	$this->validate = array(
    		'codigo_documento' => array(
    			'documentoValido' => array(
    				'rule' => 'documentoValido',
    				'message' => 'CPF/CNPJ é invalido!'
    			),
    		),
    		'nome_razao_social'=> array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' =>'Nome /Razão Social é obrigatório',
			 	),
			), 	
    	);
    	
    	if($tem_veiculo){
    		$this->validate['codigo_documento']['notEmpty'] = array(
    			'rule' => 'NotEmpty',
    			'message' => 'O proprietário é obrigatório quando há veículo'
    		);
    	}
        
    	return $this->saveAll($data, array('validate' => 'only'));
    }

    function salvarProprietarioScorecard($data, $origem_portal=FALSE){
    	$data['Proprietario']['codigo_documento'] = preg_replace('/\D/', '', $data['Proprietario']['codigo_documento']);
    
    	$codigo_proprietario = $this->field('codigo', array('codigo_documento'=>$data['Proprietario']['codigo_documento']));
    
    	if(!empty($codigo_proprietario)) {
    		$data['Proprietario']['codigo'] = $codigo_proprietario;
    	}
    	//$this->Behaviors->attach('Loggable', array('foreign_key' => 'codigo_proprietario'));
    	//$this->Behaviors->attach('SincronizarCodigoDocumento');
    	$proprietario_log = array('ProprietarioLog'=>null);
    	$proprietario_contato_logs = array('ProprietarioContatoLog'=>array()); 
    	$proprietario_endereco_log = array('ProprietarioEnderecoLog'=>null);
    	$this->create();
    	// if($this->save($data['Proprietario'], array('validate' => false))){
        if($this->save($data['Proprietario'])){
    		$this->ProprietarioLog = ClassRegistry::init('ProprietarioLog');
    		$proprietario_log['ProprietarioLog'] = $this->ProprietarioLog->id;
    
    		$this->ProprietarioContato = ClassRegistry::init('ProprietarioContato');
    		$proprietario_contato_logs = $this->ProprietarioContato->salvarProprietarioContatoScorecard($data['ProprietarioContato'], $this->id, $origem_portal);
    
    		$this->ProprietarioEndereco = ClassRegistry::init('ProprietarioEndereco');
    		$proprietario_endereco_log = $this->ProprietarioEndereco->salvarProprietarioEnderecoScorecard($data['ProprietarioEndereco'], $this->id, $origem_portal );
    	}
    	 
    	return array_merge($proprietario_log, $proprietario_contato_logs, $proprietario_endereco_log);
    }
     

     public function deletaProprietario($codigo){
           
           try{
            $this->query('BEGIN TRANSACTION');
           
            $ProprietarioContato  =  &ClassRegistry::init('ProprietarioContato');
            $proprietario_contato = $ProprietarioContato->find('list',array('conditions'=>array(
                'ProprietarioContato.codigo_proprietario'=>$codigo)));

            foreach($proprietario_contato as $k=>$v) {
                $ProprietarioContato->excluir($k);
            }

            $ProprietarioEndereco  =  &ClassRegistry::init('ProprietarioEndereco');
            $proprietario_endereco = $ProprietarioEndereco->find('list',array('conditions'=>array(
                'ProprietarioEndereco.codigo_proprietario'=>$codigo)));

            foreach($proprietario_endereco as $k=>$v) {
                $ProprietarioEndereco->excluir($k);
            }

            
            $this->excluir($codigo);
            
           $this->commit();
            
            return TRUE;

        } catch( Exception $ex ) {

            $this->rollback();

            return FALSE;
        }


   } 
        
    public function carregarParaEdicao ($codigo) {
      $dados = $this->read(null, $codigo);
      return $dados;
   }

    function porCodigoDocumento($codigo_documento) {
        $codigo_documento = preg_replace('/\W/', '', $codigo_documento);
        return $this->find('first', array('conditions' => array('codigo_documento' => $codigo_documento)));
    }

    function salvar($dados){ 
        if (!empty($dados[$this->name]['codigo'])) {
            return $this->atualizar($dados);
        } else {
            return $this->incluir($dados);
        }
    }

    public function carregarDados( $codigo ){
        $ProprietarioContato  =  &ClassRegistry::init('ProprietarioContato');
        $proprietario_contato = $ProprietarioContato->find('all',array('conditions'=>array('ProprietarioContato.codigo_proprietario'=>$codigo)));
        $this->bindModel(array('belongsTo'=>array('ProprietarioEndereco' => array('foreignKey' => 'codigo'))));
        $dados = $this->find('all', array('conditions'=> array('Proprietario.codigo'=>$codigo)));
        $dados['ProprietarioContato'] = Set::extract('/ProprietarioContato/.', $proprietario_contato);
        return $dados;
    }
    

}
?>
