<?php
class CorretoraContato extends AppModel {
	var $name = 'CorretoraContato';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
	var $useTable = 'corretora_contato';
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
        'codigo_corretora' => array(
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
	
	function trataDescricao($check) {
	    if ($this->data[$this->name]['codigo_tipo_retorno'] == TipoRetorno::TIPO_RETORNO_EMAIL)
	        return Validation::email($check['descricao']);
	    return !empty($check['descricao']);
	}
	
	function bindLazy() {
	    $this->bindModel(
	        array('belongsTo' => 
	            array('Corretora' => 
    	            array(
            	        'className' => 'Corretora',
            	        'foreignKey' => 'codigo_corretora'
            	    )
            	)
	        )
	    );
	}
	
    function contatosDaCorretora($codigo_corretora) {
       
        $contatosDaCorretora = $this->find('all', array('conditions' => array('codigo_corretora' => $codigo_corretora)));
        return $contatosDaCorretora;
    }
    
    function emailsFinanceirosPorCliente($codigo_corretora, $utilizar_email_buonny = false) {
       $emails = $this->find('all', array('conditions' => array('codigo_corretora' => $codigo_corretora, 'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_FINANCEIRO, 'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL)));
       if ($utilizar_email_buonny && count($emails) < 1)
            $emails = array(array('CorretoraContato' => array('descricao' => 'cobranca@buonny.com.br')));
       $emails = Set::extract($emails, '/CorretoraContato/descricao');
       return $emails;
    }
    
    function retornaTodosEmailsFinanceirosPorCliente($codigo_corretora = null) {
        if(isset($codigo_corretora)){
            $emails = $this->find('all',
                array(
                    'conditions' => array(
                        'codigo_corretora' => $codigo_corretora, 
                        'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_FINANCEIRO, 
                        'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL
                     ),
                    'order' => array(
                        'CorretoraContato.data_inclusao DESC'
                    ),
                    'fields' => array(
                        'CorretoraContato.codigo',
                        'CorretoraContato.codigo_corretora',
                        'CorretoraContato.nome',
                        'CorretoraContato.descricao',
                        'CorretoraContato.data_inclusao'
                    )
                )

            );
        }
        return $emails;
    }
    
    function incluirContato($dados) {
        $contatos = array();
        $codigos_tipo_contato = $dados[0]['CorretoraContato']['codigo_tipo_contato'];
        $dados[0]['CorretoraContato']['codigo_tipo_contato'] = (isset($codigos_tipo_contato[0]) ? $codigos_tipo_contato[0] : null);
        $codigo_corretora = $dados[0]['CorretoraContato']['codigo_corretora'];
	$codigo_tipo_contato = $dados[0]['CorretoraContato']['codigo_tipo_contato'];
        $nome = $dados[0]['CorretoraContato']['nome'];
	for ($indice = 1; $indice < count($dados); $indice++) {
            $dados[$indice]['CorretoraContato']['codigo_corretora'] = $codigo_corretora;
            $dados[$indice]['CorretoraContato']['codigo_tipo_contato'] = $codigo_tipo_contato;
            $dados[$indice]['CorretoraContato']['nome'] = $nome;
        }
        $contatos = array_merge($contatos, $dados);
        for ($indice = 1; $indice < count($codigos_tipo_contato); $indice++) {
            $novo_contato = $dados;
            foreach ($novo_contato as $key => $contato) {
                $novo_contato[$key]['CorretoraContato']['codigo_tipo_contato'] = $codigos_tipo_contato[$indice];
            }
            $contatos = array_merge($contatos, $novo_contato);
        }
        $result = $this->saveAll($contatos);
        return $result;
    }

}
?>