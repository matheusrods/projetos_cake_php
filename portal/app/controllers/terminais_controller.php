<?php
class TerminaisController extends AppController {
	public $name = 'Terminais';
	public $uses = array('TTermTerminal', 'TOrteObjetoRastreadoTermina');
	public $components = array('RequestHandler');
	public $helpers = array('Html', 'Ajax');
	
	function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('carregar_mensagem_livre');
    }

	function vincular($placa) {
		$this->loadModel('TVeicVeiculo');
		$this->loadModel('TPpadPerifericoPadrao');
		$this->loadModel('TPpinPerifericoPadraoInstal');
		$this->loadModel('TTecnTecnologia');

		$this->pageTitle 	= 'Veincular Terminal';
		
		$perifericos 	= $this->TPpadPerifericoPadrao->listarFormulario();

		if($this->RequestHandler->isPost()) {
			
			$this->data['Usuario'] = $this->authUsuario['Usuario'];
			if($this->TTermTerminal->sincronizaTerminal($this->data)){
				$this->BSession->setFlash('save_success');
				$this->redirect(array('controller' => 'Veiculos', 'action' => 'adicionar_veiculo'));
				exit;
			} else {
				$this->BSession->setFlash('save_error');
			}
			
		} else {
			$veic_veiculo 	=& $this->TVeicVeiculo->buscaPorPlaca($placa,NULL,TRUE);
			$this->data		=& $veic_veiculo;
			$term_codigo 	=  $this->data['TTermTerminal']['term_codigo'];	
			
			$my_perifericos =  array();
			if($term_codigo)		
				$my_perifericos =  $this->TPpinPerifericoPadraoInstal->listarPorTerminal($this->data['TTermTerminal']['term_codigo']);

			$this->data['TPpinPerifericoPadraoInstal'] = array();
			foreach ($perifericos as $ppad_codigo => $ppad_descricao) {
				if(in_array($ppad_codigo,$my_perifericos))
					$this->data['TPpinPerifericoPadraoInstal'][]['TPpadPerifericoPadrao']['ppad_codigo'] = $ppad_codigo;
			}

		}
		
		$tecnologias = $this->TTecnTecnologia->listaFicticios();
		$this->set(compact('perifericos','tecnologias','my_perifericos'));

	}

	function carregar_mensagem_livre() {  
		if (isset($this->params['form']['dados']) && !empty($this->params['form']['dados'])) {
			$this->data = unserialize($this->params['form']['dados']);
		}
		$msg_livre = array();
		$this->TOrteObjetoRastreadoTermina = ClassRegistry::init('TOrteObjetoRastreadoTermina');
		$filtros = $this->Filtros->controla_sessao($this->data, 'TViagViagem');
		if( !empty($filtros['placa']) && !empty($filtros['data_inicial']) && !empty($filtros['data_final']) ){
	        $filtros['TOrteObjetoRastreadoTermina'] = $filtros; 
			$conditions = $this->TOrteObjetoRastreadoTermina->converteFiltroEmCondition($filtros);
			if(!empty($conditions)) {
				$this->paginate = array('TOrteObjetoRastreadoTermina' => array(
					'conditions' => $conditions, 
					'fields' => array(
						'TRmliRecebimentoMensagLivre.rmli_texto',
						'TReceRecebimento.rece_data_cadastro',
						'TReceRecebimento.rece_data_computador_bordo',
						'TRposRecebimentoPosicao.rpos_latitude',
						'TRposRecebimentoPosicao.rpos_longitude'
					), 
					'order' => array('rece_data_cadastro DESC'),
					'limit' => 50, 
				));
				$msg_livre = $this->paginate('TOrteObjetoRastreadoTermina');
			}
		}
		$this->set(compact('msg_livre'));
	}

	function carregar_versao($tecn_codigo){
		$this->loadModel('TVtecVersaoTecnologia');
		$listagem 	= $this->TVtecVersaoTecnologia->listaParaCombo($tecn_codigo);
		$this->set(compact('listagem'));
	}
	
}
