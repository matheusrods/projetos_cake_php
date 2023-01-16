<?php
class Vendedor extends AppModel {
	public $name = 'Vendedor';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'vendedores';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

	public $validate = array(
		'nome' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o nome do vendedor.',
			'required' => true
			),
		);

	public function converteFiltroEmCondition($data) {
		$conditions = array();

		if (!empty($data['codigo']))
			$conditions['Vendedor.codigo'] = $data['codigo'];

		if (!empty($data['nome']))
			$conditions['Vendedor.nome LIKE'] = '%'.$data['nome'].'%';

		return $conditions;
	}

	public function carrega_vendedores_por_ajax($data = null)
	{
		if(is_null($data)) return false;
		$vendedores = $this->find('all', array(
			'conditions' => array(
				'OR' => array(
					'Vendedor.nome LIKE' => '%'.$data['string'].'%',
					) 
				),
			'fields' => array(
				'Vendedor.codigo',
				'Vendedor.nome',
				),
			'limit' => 10,
			'order' => 'Vendedor.nome ASC'
			)
		);
		if(!empty($vendedores)) {
			$html = '<table class="table">';
			foreach ($vendedores as $key => $vendedor) {
				$html .= '<tr class="js-click-vendedor pointer" data-codigo="'.$vendedor['Vendedor']['codigo'].'">';
				$html .= '<td>';
				$html .= $vendedor['Vendedor']['codigo'];
				$html .= '</td>';
				$html .= '<td>';
				$html .= $vendedor['Vendedor']['nome'];
				$html .= '</td>';
				$html .= '</tr>';
			}
			$html .= '</table>';
			return $html;
		}
		return false;
	}

}