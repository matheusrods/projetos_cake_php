<?php
class FornecedorMedico extends AppModel {

    var $name = 'FornecedorMedico';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'fornecedores_medicos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

	var $validate = array(
        'codigo_fornecedor' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código de Fornecedor!'
		),	
        'codigo_medico' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código do Médico!'
		)	
    );
    
    /**
	 * @see http://book.cakephp.org/2.0/en/core-libraries/components/pagination.html#custom-query-pagination
	 */
	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) 
	{
		if (isset($extra['contain'])) {
			$contain = $extra['contain'];
		}

		if (isset($extra['joins'])) {
			$joins = $extra['joins'];
		}

		$group = array();
		if(isset($extra['groupBy'])) {
			$group = $extra['groupBy'];
		}

		return $this->find('all', compact('conditions', 'fields', 'joins', 'order', 'limit', 'page', 'recursive', 'group', 'contain'));
	}
}
