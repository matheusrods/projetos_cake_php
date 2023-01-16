<?php
class FornecedorContato extends AppModel {
    var $name           = 'FornecedorContato';
    var $tableSchema    = 'dbo';
    var $databaseTable  = 'RHHealth';
    var $useTable       = 'fornecedores_contato';
    var $primaryKey     = 'codigo';
    var $actsAs         = array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_fornecedor_contato'));
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
        'codigo_fornecedor' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o cliente',
	        'required' => true
        ),
        'codigo_tipo_contato' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o tipo',
	        'required' => true
        ),
        'codigo_tipo_retorno' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o tipo',
	        'required' => true
        ),
        'descricao' => array(
            'rule' => 'trataDescricao',
            'message' => 'Informação inválida',
	        'required' => true
        ),
        'nome' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o representante',
	        'required' => true
        ),
	);
	
    const TIPO_TELEFONE = 1;
    const TIPO_EMAIL = 2;
    const TIPO_FAX = 3;
    const TIPO_0800 = 4;
    const TIPO_CELULAR_MOTORISTA = 5;
    const TIPO_RADIO = 6;
    const TIPO_CELULAR = 7;
    const TIPO_3G = 8;
    const TIPO_SMS = 9;
    const TIPO_RAMAL = 10;
    const TIPO_MENSALIDAE = 11;
    
	function trataDescricao($check) {
	    if ($this->data[$this->name]['codigo_tipo_retorno'] == TipoRetorno::TIPO_RETORNO_EMAIL)
	        return Validation::email($check['descricao']);
	    return !empty($check['descricao']);
	}
	
	function bindLazy() {
	    $this->bindModel(
	        array('belongsTo' => 
	            array('Fornecedor' => 
    	            array(
            	        'className' => 'Fornecedor',
            	        'foreignKey' => 'codigo_fornecedor'
            	    )
            	)
	        )
	    );
	}
	
    function emailsFinanceirosPorCliente($codigo_fornecedor, $utilizar_email_buonny = false) {
       $emails = $this->find('all', array('conditions' => array('codigo_fornecedor' => $codigo_fornecedor, 'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_FINANCEIRO, 'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL)));
       if ($utilizar_email_buonny && count($emails) < 1)
            $emails = array(array('FornecedorContato' => array('descricao' => 'cobranca@buonny.com.br')));
       $emails = Set::extract($emails, '/FornecedorContato/descricao');
       return $emails;
    }
    
    function retornaTodosEmailsFinanceirosPorCliente($codigo_fornecedor = null) {
        if(isset($codigo_fornecedor)){
            $emails = $this->find('all',
                array(
                    'conditions' => array(
                        'codigo_fornecedor' => $codigo_fornecedor, 
                        'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_FINANCEIRO, 
                        'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL
                     ),
                    'order' => array(
                        'FornecedorContato.data_inclusao DESC'
                    ),
                    'fields' => array(
                        'FornecedorContato.codigo',
                        'FornecedorContato.codigo_fornecedor',
                        'FornecedorContato.nome',
                        'FornecedorContato.descricao',
                        'FornecedorContato.data_inclusao'
                    )
                )

            );
        }
        return $emails;
    }
    
    function incluirContato($dados) {
    	
        $contatos = array();
        $codigos_tipo_contato = $dados[0]['FornecedorContato']['codigo_tipo_contato'];
        $dados[0]['FornecedorContato']['codigo_tipo_contato'] = (isset($codigos_tipo_contato[0]) ? $codigos_tipo_contato[0] : null);
        $codigo_fornecedor = $dados[0]['FornecedorContato']['codigo_fornecedor'];
        
		$codigo_tipo_contato = $dados[0]['FornecedorContato']['codigo_tipo_contato'];
        $nome = $dados[0]['FornecedorContato']['nome'];
		for ($indice = 1; $indice < count($dados); $indice++) {
            $dados[$indice]['FornecedorContato']['codigo_fornecedor'] = $codigo_fornecedor;
            $dados[$indice]['FornecedorContato']['codigo_tipo_contato'] = $codigo_tipo_contato;
            $dados[$indice]['FornecedorContato']['nome'] = $nome;
        }
        $contatos = array_merge($contatos, $dados);
        for ($indice = 1; $indice < count($codigos_tipo_contato); $indice++) {
            $novo_contato = $dados;
            foreach ($novo_contato as $key => $contato) {
                $novo_contato[$key]['FornecedorContato']['codigo_tipo_contato'] = $codigos_tipo_contato[$indice];
            }
            $contatos = array_merge($contatos, $novo_contato);
        }
        
        $result = $this->saveAll($contatos);
        return $result;
    }
    
    function incluir($dados) {
        try {
            $this->query('begin transaction');
			if (!parent::incluir($dados['FornecedorContato']))
                throw new Exception();
            $this->commit();
            return true;
        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }

}
?>