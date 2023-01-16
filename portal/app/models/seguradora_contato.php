<?php
class SeguradoraContato extends AppModel {
	var $name = 'SeguradoraContato';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
	var $useTable = 'seguradora_contato';
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
        'codigo_seguradora' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o cliente',
	        'required' => true
        ),
        'codigo_tipo_contato' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o tipo',
	        //'required' => true
        ),
        'codigo_tipo_retorno' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o tipo',
	        //'required' => true
        ),
        'descricao' => array(
            'rule' => 'trataDescricao',
            'message' => 'Informação inválida',
	        //'required' => true
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
	            array('Seguradora' => 
    	            array(
            	        'className' => 'Seguradora',
            	        'foreignKey' => 'codigo_seguradora'
            	    )
            	)
	        )
	    );
	}
	
    function contatosDaSeguradora($codigo_seguradora) {
       
        $contatosDaSeguradora = $this->find('all', array('conditions' => array('codigo_seguradora' => $codigo_seguradora)));
        return $contatosDaSeguradora;
    }
    
    function emailsFinanceirosPorCliente($codigo_seguradora, $utilizar_email_buonny = false) {
       $emails = $this->find('all', array('conditions' => array('codigo_seguradora' => $codigo_seguradora, 'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_FINANCEIRO, 'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL)));
       if ($utilizar_email_buonny && count($emails) < 1)
            $emails = array(array('SeguradoraContato' => array('descricao' => 'cobranca@buonny.com.br')));
       $emails = Set::extract($emails, '/SeguradoraContato/descricao');
       return $emails;
    }
    
    function retornaTodosEmailsFinanceirosPorCliente($codigo_seguradora = null) {
        if(isset($codigo_seguradora)){
            $emails = $this->find('all',
                array(
                    'conditions' => array(
                        'codigo_seguradora' => $codigo_seguradora, 
                        'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_FINANCEIRO, 
                        'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL
                     ),
                    'order' => array(
                        'SeguradoraContato.data_inclusao DESC'
                    ),
                    'fields' => array(
                        'SeguradoraContato.codigo',
                        'SeguradoraContato.codigo_seguradora',
                        'SeguradoraContato.nome',
                        'SeguradoraContato.descricao',
                        'SeguradoraContato.data_inclusao'
                    )
                )

            );
        }
        return $emails;
    }
    
    function incluirContato($dados) {
        $contatos = array();
        //debug($dados); die();
        $codigos_tipo_contato = $dados[0]['SeguradoraContato']['codigo_tipo_contato'];
        $dados[0]['SeguradoraContato']['codigo_tipo_contato'] = (isset($codigos_tipo_contato[0]) ? $codigos_tipo_contato[0] : null);
        $codigo_seguradora = $dados[0]['SeguradoraContato']['codigo_seguradora'];
	$codigo_tipo_contato = $dados[0]['SeguradoraContato']['codigo_tipo_contato'];
        $nome = $dados[0]['SeguradoraContato']['nome'];
	for ($indice = 1; $indice < count($dados); $indice++) {
            $dados[$indice]['SeguradoraContato']['codigo_seguradora'] = $codigo_seguradora;
            $dados[$indice]['SeguradoraContato']['codigo_tipo_contato'] = $codigo_tipo_contato;
            $dados[$indice]['SeguradoraContato']['nome'] = $nome;
        }
        $contatos = array_merge($contatos, $dados);
        for ($indice = 1; $indice < count($codigos_tipo_contato); $indice++) {
            $novo_contato = $dados;
            foreach ($novo_contato as $key => $contato) {
                $novo_contato[$key]['SeguradoraContato']['codigo_tipo_contato'] = $codigos_tipo_contato[$indice];
            }
            $contatos = array_merge($contatos, $novo_contato);
        }
        $result = $this->saveAll($contatos);
        return $result;
    }

}
?>