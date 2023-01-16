<?php
class PrestadorContato extends AppModel {
	var $name = 'PrestadorContato';
	var $tableSchema = 'publico';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'prestadores_contato';
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
        'codigo_prestador' => array(
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
	            array('Prestador' => 
    	            array(
            	        'className' => 'Prestador',
            	        'foreignKey' => 'codigo_prestador'
            	    )
            	)
	        )
	    );
	}
	
    function contatosDoPrestador($codigo_prestador) {       
        $contatosDaPrestador = $this->find('all', array('conditions' => array('codigo_prestador' => $codigo_prestador)));
        return $contatosDaPrestador;
    }
    
    // function emailsFinanceirosPorCliente($codigo_prestador, $utilizar_email_buonny = false) {
    //    $emails = $this->find('all', array('conditions' => array('codigo_prestador' => $codigo_prestador, 'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_FINANCEIRO, 'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL)));
    //    if ($utilizar_email_buonny && count($emails) < 1)
    //         $emails = array(array('PrestadorContato' => array('descricao' => 'cobranca@buonny.com.br')));
    //    $emails = Set::extract($emails, '/PrestadorContato/descricao');
    //    return $emails;
    // }
    
    // function retornaTodosEmailsFinanceirosPorCliente($codigo_prestador = null) {
    //     if(isset($codigo_prestador)){
    //         $emails = $this->find('all',
    //             array(
    //                 'conditions' => array(
    //                     'codigo_prestador' => $codigo_prestador, 
    //                     'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_FINANCEIRO, 
    //                     'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL
    //                  ),
    //                 'order' => array(
    //                     'PrestadorContato.data_inclusao DESC'
    //                 ),
    //                 'fields' => array(
    //                     'PrestadorContato.codigo',
    //                     'PrestadorContato.codigo_prestador',
    //                     'PrestadorContato.nome',
    //                     'PrestadorContato.descricao',
    //                     'PrestadorContato.data_inclusao'
    //                 )
    //             )

    //         );
    //     }
    //     return $emails;
    // }
    
    function incluirContato($dados) {
        $contatos = array();
        $codigos_tipo_contato = $dados[0]['PrestadorContato']['codigo_tipo_contato'];
        $dados[0]['PrestadorContato']['codigo_tipo_contato'] = (isset($codigos_tipo_contato[0]) ? $codigos_tipo_contato[0] : null);
        $codigo_prestador = $dados[0]['PrestadorContato']['codigo_prestador'];
        $codigo_tipo_contato = $dados[0]['PrestadorContato']['codigo_tipo_contato'];
        $nome = $dados[0]['PrestadorContato']['nome'];
	   for ($indice = 1; $indice < count($dados); $indice++) {
            $dados[$indice]['PrestadorContato']['codigo_prestador'] = $codigo_prestador;
            $dados[$indice]['PrestadorContato']['codigo_tipo_contato'] = $codigo_tipo_contato;
            $dados[$indice]['PrestadorContato']['nome'] = $nome;
        }
        $contatos = array_merge($contatos, $dados);
        for ($indice = 1; $indice < count($codigos_tipo_contato); $indice++) {
            $novo_contato = $dados;
            foreach ($novo_contato as $key => $contato) {
                $novo_contato[$key]['PrestadorContato']['codigo_tipo_contato'] = $codigos_tipo_contato[$indice];
            }
            $contatos = array_merge($contatos, $novo_contato);
        }
        $result = $this->saveAll($contatos);
        return $result;
    }

}
?>