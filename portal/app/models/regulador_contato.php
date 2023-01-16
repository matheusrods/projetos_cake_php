<?php
class ReguladorContato extends AppModel {
	var $name = 'ReguladorContato';
	var $tableSchema = 'publico';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'reguladores_contato'; 
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
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
        'codigo_regulador' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o cliente',
	        'required' => true
        ),
        'codigo_tipo_contato' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o tipo',
        ),
        'codigo_tipo_retorno' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o tipo',
        ),
        'descricao' => array(
            'rule' => 'trataDescricao',
            'message' => 'Informação inválida',
        ),
        'nome' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o representante',
	        'required' => true
        ),
	);
	
	function trataDescricao($check) {
	    if ($this->data[$this->name]['codigo_tipo_retorno'] == TipoRetorno::TIPO_RETORNO_EMAIL)
	        return Validation::email($check['descricao']);
	    return !empty($check['descricao']);
	}
	
	function bindLazy() {
	    $this->bindModel(
	        array('belongsTo' => 
	            array('Prestador' => 
    	            array(
            	        'className' => 'Prestador',
            	        'foreignKey' => 'codigo_regulador'
            	    )
            	)
	        )
	    );
	}
	
    function contatosDoRegulador($codigo_regulador) {       
        $contatosDoRegulador = $this->find('all', array('conditions' => array('codigo_regulador' => $codigo_regulador)));
        return $contatosDoRegulador;
    }
  
    
    function incluirContato($dados) {
        $contatos = array();
        $codigos_tipo_contato = $dados[0]['ReguladorContato']['codigo_tipo_contato'];
        $dados[0]['ReguladorContato']['codigo_tipo_contato'] = (isset($codigos_tipo_contato[0]) ? $codigos_tipo_contato[0] : null);
        $codigo_regulador = $dados[0]['ReguladorContato']['codigo_regulador'];
        $codigo_tipo_contato = $dados[0]['ReguladorContato']['codigo_tipo_contato'];
        $nome = $dados[0]['ReguladorContato']['nome'];
	   for ($indice = 1; $indice < count($dados); $indice++) {
            $dados[$indice]['ReguladorContato']['codigo_regulador'] = $codigo_regulador;
            $dados[$indice]['ReguladorContato']['codigo_tipo_contato'] = $codigo_tipo_contato;
            $dados[$indice]['ReguladorContato']['nome'] = $nome;
        }
        $contatos = array_merge($contatos, $dados);
                debug($contatos);
        for ($indice = 1; $indice < count($codigos_tipo_contato); $indice++) {
            $novo_contato = $dados;
            foreach ($novo_contato as $key => $contato) {
                $novo_contato[$key]['ReguladorContato']['codigo_tipo_contato'] = $codigos_tipo_contato[$indice];
            }
            $contatos = array_merge($contatos, $novo_contato);
        }
        $result = $this->saveAll($contatos);

        return $result;
    }

    function valida_contato($dado){
        $validate = true;
        if(empty($dado['nome'])){
           $this->invalidate('nome','Informe o Representante');
           $validate = false;
        } 
                                   
        if(empty($dado['codigo_tipo_contato'])){
            $this->invalidate('codigo_tipo_contato','Informe o tipo de contato');
            $validate = false;
        }

        switch ($dado) {                
            case ($dado['codigo_tipo_retorno']==NULL): 
                $this->invalidate('codigo_tipo_retorno','Informe o retorno.');
                $validate = false;
                return $validate;
            case ($dado['codigo_tipo_retorno']==1 && $dado['descricao']==NULL): 
                $this->invalidate('descricao','Informe o número de telefone.');
                $validate = false;
                return $validate;
            case ($dado['codigo_tipo_retorno']==2 && ($dado['descricao']==NULL || (COMUM::validaEmail($dado['descricao']) == NULL) ) ) : 
                $this->invalidate('descricao','Informe um e-mail válido.');
                $validate = false;
                return $validate;                    

            case ($dado['codigo_tipo_retorno']==3 && $dado['descricao']==NULL): 
                $this->invalidate('descricao','Informe o número de fax.');
                $validate = false;
                return $validate;                       

            case ($dado['codigo_tipo_retorno']== 4 && $dado['descricao']==NULL): 
                $this->invalidate('descricao','Informe o número 0800.');
                $validate = false;
                return $validate;                    

            case ($dado['codigo_tipo_retorno']== 5 && $dado['descricao']==NULL): 
                $this->invalidate('descricao','Informe o celular do motorista.');
                $validate = false;
                return $validate;
            
            case ($dado['codigo_tipo_retorno']== 6 && $dado['descricao']==NULL): 
                $this->invalidate('descricao','Informe o número do radio.');
                $validate = false;
                return $validate; 
        }
        if(!$validate){
            return false;
        }
        return true;
    }

}
?>