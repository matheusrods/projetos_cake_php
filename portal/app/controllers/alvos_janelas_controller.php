<?php
 
class AlvosJanelasController extends AppController {
    public $name = 'AlvosJanelas';
	public $components = array('Filtros', 'RequestHandler');
	public $helpers = array('Html', 'Ajax','Buonny');
	public $uses = array('TRefeReferencia', 'TCajaConfAlvoJanela', 'Cliente');

	function alvos(){
		$this->loadModel('TPaisPais');
		$this->loadModel('TEstaEstado');
		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('TBandBandeira');
		$this->loadModel('TRegiRegiao');
		$this->loadModel('TCrefClasseReferencia');
		$authUsuario = $this->BAuth->user();
		$this->pageTitle = 'Janelas de Alvos';
		$filtros 	= $this->Filtros->controla_sessao($this->data, 'Referencia');
		$classes	= $this->TCrefClasseReferencia->combo();
		$estados 	= $this->TEstaEstado->comboPorPais( TPaisPais::BRASIL );
		$bandeiras 	= array();
		$regioes	= array();
		if(!empty($authUsuario['Usuario']['codigo_cliente']))
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];

		if($filtros['codigo_cliente']){
			$cliente_pjur 	= $this->TPjurPessoaJuridica->buscaClienteCentralizador($filtros['codigo_cliente']);
			if($cliente_pjur){
				$bandeiras		= $this->TBandBandeira->lista($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
				$regioes		= $this->TRegiRegiao->lista($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
			}
		}		
		$this->data['Referencia'] = $filtros;		
		$this->set(compact('estados','bandeiras','regioes','classes'));
	}
	function index($codigo_cliente = null, $codigo_referencia = null){
		die('foo');
		$this->pageTitle = 'Janelas de Alvos';
		App::Import('Component',array('DbbuonnyGuardian'));
		$janelas = array();
		
		if($codigo_cliente && $codigo_referencia){				
			$cliente    = $this->Cliente->carregar($codigo_cliente);
			$referencia = $this->TRefeReferencia->carregar($codigo_referencia);
			$codigo_pjur = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($codigo_cliente);

			if(!empty($codigo_pjur) && is_array($codigo_pjur) && count($codigo_pjur > 0)){

				$codigo_pjur = $codigo_pjur[0];
				$conditions = array(
						'TCajaConfAlvoJanela.caja_pjur_pess_oras_codigo' => $codigo_pjur,
						'TCajaConfAlvoJanela.caja_refe_codigo' => $codigo_referencia
					);
				$janelas = $this->TCajaConfAlvoJanela->find('all', compact('conditions'));				

				$this->data['TCajaConfAlvoJanela']['caja_pjur_pess_oras_codigo'] = $codigo_pjur;
				$this->data['TCajaConfAlvoJanela']['caja_refe_codigo'] = $codigo_referencia;				
				$this->Filtros->controla_sessao($this->data, 'TCajaConfAlvoJanela');		
			}		
			if($this->RequestHandler->isPost()){
			
				$filtros = $this->Filtros->controla_sessao($this->data, 'TCajaConfAlvoJanela');
				
				$incluir = array(
					'TCajaConfAlvoJanela' => array(
						'caja_janela_inicio'         => $this->data['TCajaConfAlvoJanela']['janela_inicio'],
					    'caja_janela_fim'            => $this->data['TCajaConfAlvoJanela']['janela_fim'],
						'caja_pjur_pess_oras_codigo' => $filtros['caja_pjur_pess_oras_codigo'],
						'caja_refe_codigo'           => $filtros['caja_refe_codigo']
					)
				);

				if ($this->TCajaConfAlvoJanela->incluir($incluir)) {
					$this->data['TCajaConfAlvoJanela']['janela_inicio'] = null;
					$this->data['TCajaConfAlvoJanela']['janela_fim'] = null;
					$this->BSession->setFlash('save_success');					
				} else {
					if(isset($this->TCajaConfAlvoJanela->validationErrors['caja_janela_inicio'])){
						$this->TCajaConfAlvoJanela->validationErrors['janela_inicio'] = $this->TCajaConfAlvoJanela->validationErrors['caja_janela_inicio'];
					}
					if(isset($this->TCajaConfAlvoJanela->validationErrors['caja_janela_fim'])){
						$this->TCajaConfAlvoJanela->validationErrors['janela_fim'] = $this->TCajaConfAlvoJanela->validationErrors['caja_janela_fim'];
					}
					$this->BSession->setFlash('save_error');					
				}
				
			}
		}else{
			$this->redirect(array('action' => 'alvos'));
		}
		$this->set(compact('cliente', 'referencia', 'codigo_pjur', 'janelas'));
	}
	function listagem(){
		$this->loadModel('TCajaConfAlvoJanela');
		$this->loadModel('TRefeReferencia');

		App::Import('Component',array('DbbuonnyGuardian'));

		$filtros = $this->Filtros->controla_sessao($this->data, 'TCajaConfAlvoJanela');		

		$referencia = $filtros['caja_refe_codigo'];	
		$codigo_cliente_pjur = $filtros['caja_pjur_pess_oras_codigo'];

		if(!empty($codigo_cliente_pjur)){
			
			$fields = array(			
	        	'TCajaConfAlvoJanela.caja_codigo',
	        	'TCajaConfAlvoJanela.caja_janela_inicio',
	        	'TCajaConfAlvoJanela.caja_janela_fim',
	        );
			$conditions = array(
				'TCajaConfAlvoJanela.caja_refe_codigo' => $referencia,
				'TCajaConfAlvoJanela.caja_pjur_pess_oras_codigo' => $codigo_cliente_pjur,
			);
			$order = array(
				'TCajaConfAlvoJanela.caja_janela_inicio'
			);	
	        $janelas = $this->TCajaConfAlvoJanela->find('all',compact('fields','conditions','order'));	        
		}
		$this->set(compact('janelas','codigo_cliente_pjur','referencia'));
	}

	function excluir( $caja_codigo){
		$this->loadModel('TCajaConfAlvoJanela');
		if(!$this->TCajaConfAlvoJanela->delete($caja_codigo)){
			$this->BSession->setFlash('delete_error');
			echo false;
			exit;
		} else {
			$this->BSession->setFlash('delete_success');
			echo true;
			exit;
		}
	}

}