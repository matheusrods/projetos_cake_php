<?php
class BandeirasController extends appController {
	var $name = 'Bandeiras';
	var $uses = array('TBandBandeira');

	function index() {

		$filtros = $this->Filtros->controla_sessao($this->data, 'Bandeiras');
		
		$this->data['Bandeiras'] = $filtros;
	}

	function listagem() {
		$this->loadModel('Cliente');
		$this->loadModel('TPjurPessoaJuridica');
		$filtros = $this->Filtros->controla_sessao($this->data, 'Bandeiras');
		$authUsuario = $this->BAuth->user();

		if(!empty($authUsuario['Usuario']['codigo_cliente']))
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];

		$cliente_pjur 	= NULL;
		$listagem		= array();
		$cliente 		= $this->Cliente->buscaPorCodigo($filtros['codigo_cliente']);
		if($cliente){
			$cliente_pjur 	= $this->TPjurPessoaJuridica->buscaClienteCentralizador($filtros['codigo_cliente']);
			
			if($cliente_pjur){
				$descricao 	= (isset($filtros['descricao']))?$filtros['descricao']:'';
				$listagem	= $this->TBandBandeira->lista($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],$descricao);
			}
		}

		$this->set(compact('cliente_pjur','listagem','cliente'));
		
	}

	function incluir($codigo_cliente) {
		$this->PageTitle= 'Incluir Bandeira';
		$this->loadModel('Cliente');
		$this->loadModel('TPjurPessoaJuridica');
		$cliente 		= $this->Cliente->buscaPorCodigo($codigo_cliente);
		$cliente_pjur 	= $this->TPjurPessoaJuridica->buscaClienteCentralizador($codigo_cliente);

		if(!$cliente_pjur){
			$this->BSession->setFlash('cadastro_com_problema');
			$this->redirect(array('controller' => 'Bandeiras','action' => 'index'));
		}

		if($this->data['TBandBandeira']){
			$validate = true;

			if(empty($this->data['TBandBandeira']['band_descricao'])){
				$this->TBandBandeira->invalidate('band_descricao','Informe uma descrição para a bandeira.');
				$validate = false;
			}

			// Verifica se foi reportado algum erro acima
			if($validate) {
				if($this->TBandBandeira->save($this->data)){
					$this->BSession->setFlash('save_success');
					$this->redirect(array('controller' => 'Bandeiras','action' => 'index'));
				} else {
					$this->BSession->setFlash('save_error');
				}

			} else {
					$this->BSession->setFlash('save_error');
			}
		}

		$this->set(compact('cliente_pjur','cliente'));
	}

	function alterar($codigo_cliente,$band_codigo) {
		$this->PageTitle= 'Alterar Bandeira';
		$this->loadModel('Cliente');
		$cliente 		= $this->Cliente->buscaPorCodigo($codigo_cliente);

		if($this->data['TBandBandeira']){
			$validate = true;

			if(empty($this->data['TBandBandeira']['band_descricao'])){
				$this->TBandBandeira->invalidate('band_descricao','Informe uma descrição para a bandeira.');
				$validate = false;
			}

			// Verifica se foi reportado algum erro acima
			if($validate) {
				if($this->TBandBandeira->save($this->data)){
					$this->BSession->setFlash('save_success');
					$this->redirect(array('controller' => 'Bandeiras','action' => 'index'));
				} else {
					$this->BSession->setFlash('save_error');
				}

			} else {
					$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data = $this->TBandBandeira->buscaPorCodigo($band_codigo);
		}

		$this->set(compact('cliente'));
	}

	function remover($band_codigo) {
		$bandeira = $this->TBandBandeira->carregar($band_codigo);
		$remover  = $this->TBandBandeira->valida_remover($band_codigo);

		if (isset($this->data['TBandBandeira']) && $remover) {
			if($this->TBandBandeira->delete($band_codigo)){
				$this->BSession->setFlash('delete_success');
			} else {
				$this->BSession->setFlash('delete_error');
			}
		}

		$this->set(compact('bandeira','remover'));
		
	}

}
