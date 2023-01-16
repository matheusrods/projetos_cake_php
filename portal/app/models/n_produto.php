<?php
class NProduto extends AppModel {
    var $name = 'NProduto';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
    var $useTable = 'produto';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';

	function listar() {
		return $this->find('list', array(
			'conditions' => array(
				'grupo' => array(
 					'050',
					'100',
					'200',
					'250',
 					'265',
					'280',
					'290',
					'300',
					'600'
				),
			),
			'order' => array('descricao')
		));
	}

	function listVinculadoPortal() {
		$this->bindModel(array('hasOne' => array(
			'Produto' => array('foreignKey' => 'codigo_naveg', 'type' => 'INNER'),
		)));
		return $this->find('list', array('fields' => array($this->name.'.'.$this->primaryKey, $this->name.'.descricao'), 'recursive' => 1));
	}
}

class NProdutoTest extends NProduto {
	var $name 			= 'NProdutoTest';
	var $useDbConfig 	= 'test';
	var $useTable 		= 'n_produto';
}

?>