<?php
class FornecedorFoto extends AppModel {

    var $name = 'FornecedorFoto';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'fornecedores_fotos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

	var $validate = array(
        'codigo_fornecedor' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código de Fornecedor!'
		),	
        'caminho_arquivo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Caminho do Arquivo!'
		),	
        'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Descrição!'
		)		
	);
	 
}
