<?php
class PropostaCredFoto extends AppModel {

    var $name = 'PropostaCredFoto';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'propostas_credenciamento_fotos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

	var $validate = array(
        'codigo_proposta_credenciamento' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código de Proposta!'
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

	
    function converteFiltroEmCondition($data) {
        $conditions = array();
        if (!empty($data['descricao']))
            $conditions['PropostaCredFoto.descricao like'] = $data['descricao'] . '%';
       
       	$conditions['PropostaCredFoto.status = '] = 1;
        return $conditions;
    }

    function retornaFotos($idProposta) {
    	
    }
}
