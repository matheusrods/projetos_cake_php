<?php
class PropostaCredProduto  extends AppModel {

    var $name = 'PropostaCredProduto';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'propostas_credenciamento_produto';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
	var $validate = array(
        'codigo_produto' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o código do produto!'
        ),		
        'codigo_proposta_credenciamento' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o código da proposta!'
		)
	);
}
