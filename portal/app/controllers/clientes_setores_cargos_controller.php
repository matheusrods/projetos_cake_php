<?php
class ClientesSetoresCargosController extends AppController {
	public $name = 'ClientesSetoresCargos';
	public $uses = array('ClienteSetorCargo', 'GrupoEconomico', 'Cliente', 'Setor', 'Cargo');
	
	/**
     * beforeFilter callback
     *
     * @return void
     */
	function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(
			array(
				'copiar_hierarquia','editar_status'
			)
		);
	}//FINAL FUNCTION beforeFilter   

	public function index($codigo_cliente, $referencia = 'sistema',$codigo_cargo = null, $terceiros_implantacao = 'interno') {
		$this->pageTitle = 'Cadastro de Hierarquia';

		$this->data = $this->Cliente->find('first', array('conditions' => array('codigo' => $codigo_cliente)));

		$this->set(compact('codigo_cliente', 'terceiros_implantacao'));

		if($codigo_cargo == 'null'){
			$codigo_cargo = null;
		}

		//monta os joins
		$join_ge = array(
			array(
				'table' => 'RHHealth.dbo.grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
					)
			)
		);
		//pega o codigo da matriz
		$cliente_unidade = $this->GrupoEconomico->find('first', array('joins' => $join_ge, 'conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));
		$codigo_cliente = $cliente_unidade['GrupoEconomico']['codigo_cliente'];

		$this->GrupoEconomico->virtualFields = array(
			'razao_social' => '(CONCAT(Cliente.codigo, \' - \', Cliente.razao_social))'
			);
		
		$unidades = $this->GrupoEconomico->find('list', array(
			'joins' => array(
				array(
					'table' => 'RHHealth.dbo.grupos_economicos_clientes',
					'alias' => 'GrupoEconomicoCliente',
					'type' => 'INNER',
					'conditions' => array(
						'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
						)
					),
				array(
					'table' => 'RHHealth.dbo.cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => array(
						'Cliente.codigo = GrupoEconomicoCliente.codigo_cliente AND Cliente.ativo = 1'
						)
					),
				),
			'conditions' => array(
				'GrupoEconomico.codigo_cliente' => $codigo_cliente
				),
			'fields' => array('Cliente.codigo', 'Cliente.nome_fantasia'),
			'order' => 'Cliente.nome_fantasia'
			)
		);

		$setores = $this->Setor->find('list', array('conditions' => array('Setor.ativo' => 1, 'Setor.codigo_cliente' => $codigo_cliente), 'fields' => array('Setor.codigo', 'Setor.descricao'), 'order' => 'Setor.descricao'));
		$setor = $setores;

		$cargos = $this->Cargo->find('list', array('conditions' => array('Cargo.ativo' => 1, 'Cargo.codigo_cliente' => $codigo_cliente), 'fields' => array('Cargo.codigo', 'Cargo.descricao'), 'order' => 'Cargo.descricao'));
		$cargo = $cargos;


		//verifica se existe o parametro codigo_cargo
		if(!empty($codigo_cargo)) {
			//seta o valor para filtrar
			$this->data['ClienteSetorCargo']['codigo_cargo'] = $codigo_cargo;
			$this->Filtros->controla_sessao($this->data, $this->ClienteSetorCargo->name);
		}//fim empty codigo_cargo



		$this->set(compact('unidades', 'setores', 'cargos', 'codigo_cliente', 'referencia', 'setor', 'cargo'));
	}
	
	public function listagem($codigo_cliente) {
		$this->layout = 'ajax'; 

		//model setor
		$this->loadModel('Setor');
		$this->loadModel('Cargo');
		$this->loadModel('GrupoHomogeneo');

		//implementa filtros da tela
		$filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteSetorCargo->name);

		$conditions = $this->ClienteSetorCargo->converteFiltroEmCondition($filtros);
		
		$this->paginate['ClienteSetorCargo'] = $this->ClienteSetorCargo->getHierarquias($conditions, true, $codigo_cliente, false);
		
		// pr( $this->ClienteSetorCargo->find('sql', $this->paginate['ClienteSetorCargo']));
		
		$cliente_setor_cargo = $this->paginate('ClienteSetorCargo');

		$this->set(compact('cliente_setor_cargo'));

		$this->GrupoEconomico->virtualFields = array(
			'razao_social' => '(CONCAT(Cliente.codigo, \' - \', Cliente.razao_social))'
			);

		$unidades = $this->GrupoEconomico->find('list', array(
			'joins' => array(
				array(
					'table' => 'grupos_economicos_clientes',
					'alias' => 'GrupoEconomicoCliente',
					'type' => 'INNER',
					'conditions' => array(
						'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
						)
					),
				array(
					'table' => 'cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => array(
						'Cliente.codigo = GrupoEconomicoCliente.codigo_cliente AND Cliente.ativo = 1'
						)
					),
				),
			'conditions' => array(
				'GrupoEconomico.codigo_cliente' => $codigo_cliente
				),
			'fields' => array('Cliente.codigo', 'razao_social'),
			'order' => 'Cliente.razao_social'
			)
		);


		$setores = $this->Setor->find('list', array('conditions' => array('Setor.ativo' => 1, 'Setor.codigo_cliente' => $codigo_cliente), 'fields' => array('Setor.codigo', 'Setor.descricao'), 'order' => 'Setor.descricao'));
		$setor = $setores;

		$cargos = $this->Cargo->find('list', array('conditions' => array('Cargo.ativo' => 1, 'Cargo.codigo_cliente' => $codigo_cliente), 'fields' => array('Cargo.codigo', 'Cargo.descricao'), 'order' => 'Cargo.descricao'));
		$cargo = $cargos;


		$this->set(compact('unidades', 'setores', 'cargos', 'codigo_cliente'));
	}

	public function incluir() {
		$this->autoRender = false;		
		
		$msg = "Erro ao incluir Hierarquia";
		$return = false;

		$verifica_hierarquia_exists = $this->ClienteSetorCargo->find('first', array('conditions' => array('codigo_cliente' => $_POST['codigo_matriz'], 'codigo_cliente_alocacao' => $_POST['codigo_cliente_alocacao'], 'codigo_setor' => $_POST['codigo_setor'], 'codigo_cargo' => $_POST['codigo_cargo'])));	

		if($verifica_hierarquia_exists){			
			$return = false;
			$msg = "Ja existe essa hierarquia, por favor tente novamente com dados diferentes.";
			return json_encode(array('return' => $return, 'msg' => !isset($msg) ? '0' : $msg));
		}		

		if($_POST) {
			$data = array(
				'ClienteSetorCargo' => array(
					'codigo_cliente' => $_POST['codigo_matriz'],
					'codigo_cliente_alocacao' => $_POST['codigo_cliente_alocacao'],
					'codigo_setor' => $_POST['codigo_setor'],
					'codigo_cargo' =>$_POST['codigo_cargo'],
					'bloqueado' => 0,
					'ativo' => 1,
					)
				);
			if($this->ClienteSetorCargo->incluir($data)) {
				$return = true;
				$msg = "A hierarquia foi salva com sucesso.";
				return json_encode(array('return' => $return, 'msg' => !isset($msg) ? '0' : $msg));
			} 
		} 
		return json_encode(array('return' => $return, 'msg' => !isset($msg) ? '0' : $msg));
	}
	
	public function editar() {
		$this->autoRender = false;
		if($_POST) {
			$data = array(
				'ClienteSetorCargo' => array(
					'codigo' => $_POST['codigo'],
					'codigo_cliente_alocacao' => $_POST['codigo_cliente_alocacao'],
					'codigo_setor' => $_POST['codigo_setor'],
					'codigo_cargo' =>$_POST['codigo_cargo'],
					'bloqueado' => 0,
					)
				);
			if($this->ClienteSetorCargo->atualizar($data)) {
				return json_encode(true);
			} 
		} 
		return json_encode(false);
	}

	public function excluir()
	{
		$this->autoRender = false;
		if($_POST) {
			if($this->ClienteSetorCargo->excluir($_POST['codigo'])) {
				return json_encode(true);
			} 
		} 
		return json_encode(false);
	}

	/**
	 * [copiar_hierarquia metodo para copiar a hierarquia da matriz escolhendo a unidade que sera replicada, e quais hierarquias devem ser replicadas]
	 * 
	 * @param  [type] $codigo_cliente [codigo do cliente matriz ]
	 * @return [type]                 [description]
	 */
	public function copiar_hierarquia($codigo_cliente ,$terceiros_implantacao = 'interno')
	{
		//seta o titulo da pagina
		$this->pageTitle = 'Copiar Hierarquia para a Unidade';

		//model setor
		$this->loadModel('Setor');
		$this->loadModel('Cargo');
		$this->loadModel('GrupoHomogeneo');

		//verifica se é enviado o post
		if($this->RequestHandler->isPost()) {
			//seta a variavel com o codigo da unidade
			$codigo_unidade_copia = $this->data['ClienteSetorCargo']['codigo_unidade'];
			$var_aux_retorno = true;

			//varre os dados da hierarquia selecionado
			foreach($this->data['hierarquia'] as $dados) {

				//nao foi selecionado
				if($dados['codigo'] == 0) {
					continue;
				}

				//pega a configuração da hierarquia
				$hierarquia = $this->ClienteSetorCargo->find('first',array('conditions' => array('ClienteSetorCargo.codigo' => $dados['codigo'])));

				//verifica se já existe esta hierarquia para esta unidade 
				$hierarquiaUnidade = $this->ClienteSetorCargo->find('first',array('conditions' => array('ClienteSetorCargo.codigo_cliente' => $codigo_unidade_copia,'codigo_setor' => $hierarquia['ClienteSetorCargo']['codigo_setor'],'codigo_cargo' => $hierarquia['ClienteSetorCargo']['codigo_cargo'])));

				//verifica se existe
				if(empty($hierarquiaUnidade)) {
					//seta os dados a serem incluidos
					$dadosHierarquia = array('ClienteSetorCargo' => 
						array(
							'codigo_cliente' => $codigo_unidade_copia,
							'codigo_setor' => $hierarquia['ClienteSetorCargo']['codigo_setor'],
							'codigo_cargo' => $hierarquia['ClienteSetorCargo']['codigo_cargo'],
							'bloqueado' => $hierarquia['ClienteSetorCargo']['bloqueado'],
						)
					);

					if(!$this->ClienteSetorCargo->incluir($dadosHierarquia)) {
						$var_aux_retorno = false;
					}

				}//fim if hierarquiaUnidade

			}//fim foreach

			if ($var_aux_retorno) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}

			if($terceiros_implantacao == 'terceiros_implantacao'){
				$this->redirect(array('controller' => 'clientes_setores_cargos','action' => 'index',$codigo_cliente,'implantacao','null','terceiros_implantacao'));
			} else {
				$this->redirect(array('controller' => 'clientes_setores_cargos','action' => 'index',$codigo_cliente,'implantacao'));
			}


		}//fim post


		//implementa filtros da tela
		$filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteSetorCargo->name);

		//monta a condições devidas		
		$conditions = array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente,'Setor.ativo' => 1,'Cargo.ativo' => 1);

		// debug($conditions);exit;

		$joins = array(
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => array(
					'Cliente.codigo = ClienteSetorCargo.codigo_cliente_alocacao'
					)
				),
			array(
				'table' => 'setores',
				'alias' => 'Setor',
				'type' => 'INNER',
				'conditions' => array(
					'Setor.codigo = ClienteSetorCargo.codigo_setor'
					)
				),
			array(
				'table' => 'cargos',
				'alias' => 'Cargo',
				'type' => 'INNER',
				'conditions' => array(
					'Cargo.codigo = ClienteSetorCargo.codigo_cargo'
					)
				),
			array(
				'table' 		=> 'grupos_economicos_clientes',
				'alias' 		=> 'GrupoEconomicoCliente',
				'type'			=> 'INNER',
				'conditions'	=> array(
					'ClienteSetorCargo.codigo_cliente_alocacao = GrupoEconomicoCliente.codigo_cliente'
					)
				),
			array(
				'table' 		=> 'grupos_economicos',
				'alias' 		=> 'GrupoEconomico',
				'type'			=> 'INNER',
				'conditions'	=> array(
					'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
					)
				)
			);
		$fields = array(
			'ClienteSetorCargo.codigo', 
			'ClienteSetorCargo.bloqueado', 
			'ClienteSetorCargo.data_inclusao',
			'Cliente.codigo', 
			'(CONCAT(Cliente.codigo, \' - \', Cliente.razao_social)) AS razao_social', 
			'Setor.codigo', 
			'Setor.descricao', 
			'Cargo.codigo', 
			'Cargo.descricao', 
			);
		$order = 'ClienteSetorCargo.codigo DESC';

		$cliente_setor_cargo = $this->ClienteSetorCargo->find('all',array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields,
			'order' => $order,
			)
		);

		// debug($cliente_setor_cargo);exit;


		$this->set(compact('cliente_setor_cargo'));

		$this->loadModel('GrupoEconomico');
		$this->GrupoEconomico->virtualFields = array(
			'razao_social' => '(CONCAT(Cliente.codigo, \' - \', Cliente.razao_social))'
			);

		$unidades = $this->GrupoEconomico->find('list', array(
			'joins' => array(
				array(
					'table' => 'grupos_economicos_clientes',
					'alias' => 'GrupoEconomicoCliente',
					'type' => 'INNER',
					'conditions' => array(
						'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
						)
					),
				array(
					'table' => 'cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => array(
						'Cliente.codigo = GrupoEconomicoCliente.codigo_cliente AND Cliente.ativo = 1'
						)
					),
				),
			'conditions' => array(
				'GrupoEconomico.codigo_cliente' => $codigo_cliente
				),
			'fields' => array('Cliente.codigo', 'razao_social'),
			'order' => 'Cliente.razao_social'
			)
		);

		$this->set(compact('unidades', 'codigo_cliente', 'terceiros_implantacao'));


	}//fim copiar_hierarquia

	public function editar_status($codigo) {
        $this->layout = 'ajax';

        $codigo_hierarquia = $this->ClienteSetorCargo->read(null, $codigo);
      
        $codigo_hierarquia['ClienteSetorCargo']['ativo'] = ($codigo_hierarquia['ClienteSetorCargo']['ativo'] === 0 ? 1 : 0);

        if ($this->ClienteSetorCargo->atualizar($codigo_hierarquia, false)) {
            $this->render(false, false);
            print 1;
        } else {
            $this->render(false, false);
            print 0;
        }        
    }
}