<?php
class MotivoAtendimento extends AppModel {

    var $name = 'MotivoAtendimento';
    var $tableSchema = 'vendas';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'motivos_atendimento';
    var $displayField = 'descricao';
    var $primaryKey = 'codigo'; 
    var $actsAs = array('Secure');

    var $validate = array(
    	'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a descricao',
		));

	public function converteFiltroEmCondition($dados) {
		$conditions = array();
		if (!empty($dados['MotivoAtendimento']['descricao'])) {
			$conditions["MotivoAtendimento.descricao LIKE"] = "%".$dados['MotivoAtendimento']["descricao"]."%";
		}
		return $conditions; 
	}

	public function findMotivoCodigo($codigo, $tipo_find='list') {
		$conditions = array('MotivoAtendimento.codigo' => $codigo);
		$fields = array('MotivoAtendimento.descricao');
		return $this->MotivoAtendimento->find($tipo_find, compact('conditions', 'fields'));
	}


}
