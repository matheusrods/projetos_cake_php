<?php
App::import('Model', 'Vendedor');
class VendedorTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.vendedor'
		);

	public function startTest() {
		$this->Vendedor = & ClassRegistry::init('Vendedor');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	public function testConverteFiltroEmCondition($value='')
	{
		$dados = array(
			'codigo' => 123,
			'nome' => 'Teste Unitario'
			);
		$retorno = array (
			'Vendedor.codigo' => 123,
			'Vendedor.nome LIKE' => '%Teste Unitario%',
			);
		$this->assertEqual($this->Vendedor->converteFiltroEmCondition($dados), $retorno);
	}

	public function testCarrega_vendedores_por_ajax()
	{
		$data['string'] = 'Marco Ruas';
		$vendedores = $this->Vendedor->find('all', array(
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
		$html = false;
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
		}
		$retorno = $html;
		$this->assertEqual($this->Vendedor->carrega_vendedores_por_ajax($data), $retorno);
		$this->assertFalse($this->Vendedor->carrega_vendedores_por_ajax(array('string' => 'Lorem Ipsun')));
	}

	public function endTest() {
		unset($this->Vendedor);
		ClassRegistry::flush();
	}
}