<?php
class GruposEconomicosClientesController extends appController {
	var $name = 'GruposEconomicosClientes';
	var $uses = array('GrupoEconomicoCliente');

		/**
	 * beforeFilter callback
	 *
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow('bloqueia', 'por_cliente');  // criar as migrations desses caras
	}

	function index($codigo_grupo_economico) {
		$this->pageTitle = 'Clientes do Grupo Econômico';
		$grupo_economico = $this->GrupoEconomicoCliente->GrupoEconomico->carregar($codigo_grupo_economico);
		$clientes = $this->GrupoEconomicoCliente->find('all', array('conditions' => array('codigo_grupo_economico' => $codigo_grupo_economico)));
		$valida_exclusao_matriz = $this->GrupoEconomicoCliente->valida_exclusao_matriz($codigo_grupo_economico);
		$this->set(compact('grupo_economico', 'clientes','valida_exclusao_matriz'));
	}

	function incluir($codigo_grupo_economico) {
		$grupo_economico = $this->GrupoEconomicoCliente->GrupoEconomico->carregar($codigo_grupo_economico);
		$this->set(compact('grupo_economico'));
		if (!empty($this->data)) {
			if ($this->GrupoEconomicoCliente->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index', $codigo_grupo_economico));	
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data['GrupoEconomicoCliente']['codigo_grupo_economico'] = $codigo_grupo_economico;
		}
	}

	function excluir($codigo, $codigo_grupo_economico) {
		if ($this->GrupoEconomicoCliente->excluir($codigo)) {
			$this->BSession->setFlash('delete_success');
		} else {
			$this->BSession->setFlash('delete_error');
		}
		$this->redirect(array('action' => 'index', $codigo_grupo_economico));
	}
	
	public function bloqueia()
	{
		$this->autoRender = false;
		$this->GrupoEconomicoCliente->recursive = -1;
		$return = -1;
		if($_POST) {
			$verifica = $this->GrupoEconomicoCliente->findByCodigo($_POST['codigo'], array('bloqueado'));
			if($verifica['GrupoEconomicoCliente']['bloqueado'] == 1) {
				$return = $this->GrupoEconomicoCliente->atualizaBloqueio(array('GrupoEconomicoCliente' => array('codigo' => $_POST['codigo'], 'bloqueado' => 0)));
			} else {
				$return = $this->GrupoEconomicoCliente->atualizaBloqueio(array('GrupoEconomicoCliente' => array('codigo' => $_POST['codigo'], 'bloqueado' => 1)));
			}
		}
		return $return;
	}

	// public function por_cliente($codigo_cliente) {
	// 	$list = $this->GrupoEconomicoCliente->lista($codigo_cliente);
	// 	$result = array();
    //     foreach ($list as $key => $value) {
    //         $result[] = array('codigo' => $key, 'descricao' => $value);
    //     }
    //     echo json_encode($result);
	// 	die();
	// }

	/**
	 * Obter lista de Clientes e Matriz por código de cliente
	 *
	 * @param [array] $codigo_cliente
	 * @return void
	 * @todo implementar token
	 */
	public function por_cliente($codigo_cliente = null) {
		
		if(is_null($codigo_cliente)){
			$this->responseJson();
		}
		
		$codigo_cliente = $this->normalizaCodigoCliente($codigo_cliente); // normaliza codigo
		
		$dados = $this->GrupoEconomicoCliente->obterLista($codigo_cliente);

		$this->responseJson($dados);
		
	}//FINAL FUNCTION por_cliente	
}
