<?php
class ErpProfissional extends AppModel {
	public $name = 'ErpProfissional';
	public $tableSchema = 'dbo';
	public $databaseTable = 'Erp_buonny';
	public $useTable = 'ERP_Profissional';
	public $primaryKey = 'PRF_Codigo';
	public $actsAs = array('Secure');


	public function buscaTreinamentosProfissional($codigo_documento){
		$this->CbqTurmaItem 	= ClassRegistry::init('CbqTurmaItem');
		$this->CbqTurmaCurso 	= ClassRegistry::init('CbqTurmaCurso');
		$this->ErpCurso 		= ClassRegistry::init('ErpCurso');

		$this->bindModel(array(
            'belongsTo' => array(
                'CbqTurmaItem' => array('foreignKey' => FALSE, 'conditions' => "CbqTurmaItem.PRF_Codigo = ErpProfissional.PRF_Codigo", 'type' => 'INNER'),
                'CbqTurmaCurso' => array('foreignKey' => FALSE, 'conditions' => 'CbqTurmaCurso.TUR_Codigo = CbqTurmaItem.TUR_Codigo', 'type' => 'INNER'),
                'ErpCurso' => array('foreignKey' => FALSE, 'conditions' => 'ErpCurso.CUR_Codigo = CbqTurmaCurso.CUR_Codigo', 'type' => 'INNER'),
            ),
        ));
		$conditions = array(
			"REPLACE(REPLACE(PRF_Cpf, '-', ''), '.', '')" => $codigo_documento,
		);
		$fields = array(
			'ErpCurso.CUR_Descricao',
			'CbqTurmaItem.Dta_Cad',
			'CbqTurmaItem.TUI_Nota'
		);
        return $this->find('all', compact('conditions', 'fields'));



	}

}
