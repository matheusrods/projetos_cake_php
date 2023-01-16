<?php
class FuncionarioContato extends AppModel {
	var $name = 'FuncionarioContato';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
	var $useTable = 'funcionarios_contatos';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_funcionarios_contatos'));
	var $belongsTo = array(
		'TipoContato' => array(
				'className' => 'TipoContato',
				'foreignKey' => 'codigo_tipo_contato'
		),
		'TipoRetorno' => array(
				'className' => 'TipoRetorno',
				'foreignKey' => 'codigo_tipo_retorno'
		)
	);    
	
    var $validate = array(
        'codigo_funcionario' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Funcionário!',
			'required' => true
		),	
        'codigo_tipo_contato' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Tipo do Contato!',
			'required' => true
		),
		'codigo_tipo_retorno' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Meio do Contato!',
			'required' => true
		)
	);
    
    function importacao_contato_funcionario($data){
    	$this->TipoContato = & ClassRegistry::init('TipoContato');
        
        $retorno = '';
        foreach ($data as $chave => $dados) {
            $dados['FuncionarioContato']['codigo_tipo_contato'] = TipoContato::TIPO_CONTATO_COMERCIAL;

            if(!isset($dados['FuncionarioContato']['codigo']) && empty($dados['FuncionarioContato']['codigo'])) {
                if(!parent::incluir($dados)){
                    $erro_funcionario_contato = '';
                    foreach ($this->validationErrors as $key => $value) {
                        $erro_funcionario_contato .= utf8_decode($value).'|';
                        $this->validationErrors[$key] = $erro_funcionario_contato;
                    }
                    $retorno['FuncionarioContato'] = $this->validationErrors;
                }
            }
            else {
                if(!parent::atualizar($dados)){
                    $erro_funcionario_contato = '';
                    foreach ($this->validationErrors as $key => $value) {
                        $erro_funcionario_contato .= utf8_decode($value).'|';
                        $this->validationErrors[$key] = $erro_funcionario_contato;
                    }
                    $retorno['FuncionarioContato'] = $this->validationErrors;
                }
            }
        }
        return $retorno;
    }
    
    function contatosDoFuncionario($codigo_funcionario, $codigo_tipo_retorno = NULL ) {
    	$conditions = array('codigo_funcionario' => $codigo_funcionario);
    	
    	if( $codigo_tipo_retorno )
    		array_push( $conditions, array('codigo_tipo_retorno' => $codigo_tipo_retorno));
    	
		$dados = $this->find('all', compact('conditions') );
		
		return $dados;
    }
    
    function incluirContato($dados) {
    	
    	$contatos = array();
    	$codigos_tipo_contato = $dados[0]['FuncionarioContato']['codigo_tipo_contato'];
    	$dados[0]['FuncionarioContato']['codigo_tipo_contato'] = (isset($codigos_tipo_contato[0]) ? $codigos_tipo_contato[0] : null);
    	$codigo_funcionario = $dados[0]['FuncionarioContato']['codigo_funcionario'];
    	$codigo_tipo_contato = $dados[0]['FuncionarioContato']['codigo_tipo_contato'];
    	$nome = $dados[0]['FuncionarioContato']['nome'];
    	for ($indice = 1; $indice < count($dados); $indice++) {
    		$dados[$indice]['FuncionarioContato']['codigo_funcionario'] = $codigo_funcionario;
    		$dados[$indice]['FuncionarioContato']['codigo_tipo_contato'] = $codigo_tipo_contato;
    		$dados[$indice]['FuncionarioContato']['nome'] = $nome;
    	}
    	$contatos = array_merge($contatos, $dados);
    	for ($indice = 1; $indice < count($codigos_tipo_contato); $indice++) {
    		$novo_contato = $dados;
    		foreach ($novo_contato as $key => $contato) {
    			$novo_contato[$key]['FuncionarioContato']['codigo_tipo_contato'] = $codigos_tipo_contato[$indice];
    		}
    		$contatos = array_merge($contatos, $novo_contato);
    	}
    	$result = $this->saveAll($contatos);
    	return $result;
    }    

   public function atualizaContatos($codigo_funcionario,$contatos_novos){

    	$contatos_atuais = $this->contatosDoFuncionario($codigo_funcionario);
    	//cria um array temporario com os contatos atuais
    	foreach($contatos_atuais as $contatos){
    		$atuais[$contatos['FuncionarioContato']['codigo_tipo_retorno']]['FuncionarioContato'] = $contatos["FuncionarioContato"];
    	}
    	//varre os novos contatos para saber se deve atualizat ou inserir
    	foreach ($contatos_novos as $novos) {
    		if (isset($atuais[$novos['FuncionarioContato']['codigo_tipo_retorno']])){
    			//se ja existe contato do mesmo tipo, atualiza
    			$atuais[$novos['FuncionarioContato']['codigo_tipo_retorno']]['FuncionarioContato']['nome'] = $novos['FuncionarioContato']['nome'];
    			$atuais[$novos['FuncionarioContato']['codigo_tipo_retorno']]['FuncionarioContato']['descricao'] = $novos['FuncionarioContato']['descricao'];
    			
    			$this->atualizar($atuais[$novos['FuncionarioContato']['codigo_tipo_retorno']]);
    		}else{
    			//caso contrario insere novo
    			$novos['FuncionarioContato']['codigo_funcionario'] = $codigo_funcionario;
    			$novos['FuncionarioContato']['codigo_usuario_inclusao'] = 0;
    			
    			$this->incluir($novos);
    		}
    	}
    }
}