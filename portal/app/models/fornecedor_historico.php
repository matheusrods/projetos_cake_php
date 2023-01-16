<?php
class FornecedorHistorico extends AppModel {
	var $name = 'FornecedorHistorico';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'fornecedores_historico';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	
	var $validate = array(
        'caminho_arquivo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Arquivo!'
		),
    );
}
?>