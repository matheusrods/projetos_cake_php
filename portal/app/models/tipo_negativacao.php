<?php

class TipoNegativacao extends AppModel {

	public $name = 'TipoNegativacao';
	public $tableSchema = 'informacoes';
	public $databaseTable = 'dbTeleconsult';
	public $useTable = 'negativacao';
	public $primaryKey = 'codigo';
	public $displayField = 'descricao';
	public $actsAs = array('Secure');
    public $notin = array(21,7);
	
	var $validate = array(
        'descricao' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a descrição',
                'required' => true
            ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Já existe este tipo de negativação',
                'required' => true
            )
        )
    );


	function listarTipoNegativacao() {
		return $this->find('all');
	}


	function listar(){
		$order = array('descricao');
        $conditions = array('codigo not' => $this->notin);
		return $this->find('list',compact('conditions'));
	}
	
    function converteFiltroEmCondition($data) {
        $conditions = array();
        if (!empty($data['codigo']))
            $conditions['TipoNegativacao.codigo'] = $data['codigo'];
        if (!empty($data['descricao']))
            $conditions['TipoNegativacao.descricao'] = $data['descricao'];
        return $conditions;
    }

}
