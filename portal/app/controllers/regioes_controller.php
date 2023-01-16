<?php
class RegioesController extends appController {
	var $name = 'Regioes';
	var $uses = array('TRegiRegiao');

	function index() {

		$filtros = $this->Filtros->controla_sessao($this->data, 'Regioes');
		
		$this->data['Regioes'] = $filtros;
	}

	function listagem() {
		$this->loadModel('Cliente');
		$this->loadModel('TPjurPessoaJuridica');
		$filtros = $this->Filtros->controla_sessao($this->data, 'Regioes');
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
				$listagem	= $this->TRegiRegiao->lista($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],$descricao);
			}
		}

		$this->set(compact('cliente_pjur','listagem','cliente'));
		
	}

	function incluir($codigo_cliente) {
		$this->PageTitle= 'Incluir Região';
		$this->loadModel('Cliente');
		$this->loadModel('TPjurPessoaJuridica');
		$cliente 		= $this->Cliente->buscaPorCodigo($codigo_cliente);
		$cliente_pjur 	= $this->TPjurPessoaJuridica->buscaClienteCentralizador($codigo_cliente);

		if(!$cliente_pjur){
			$this->BSession->setFlash('cadastro_com_problema');
			$this->redirect(array('controller' => 'Regioes','action' => 'index'));
		}

		if($this->data['TRegiRegiao']){
			$validate = true;

			if(empty($this->data['TRegiRegiao']['regi_descricao'])){
				$this->TRegiRegiao->invalidate('regi_descricao','Informe uma descrição para a bandeira.');
				$validate = false;
			}

			// Verifica se foi reportado algum erro acima
			if($validate) {
				if($this->TRegiRegiao->save($this->data)){
					$this->BSession->setFlash('save_success');
					$this->redirect(array('controller' => 'Regioes','action' => 'index'));
				} else {
					$this->BSession->setFlash('save_error');
				}

			} else {
					$this->BSession->setFlash('save_error');
			}
		}

		$this->set(compact('cliente_pjur','cliente'));
	}

	function alterar($codigo_cliente,$regi_codigo) {
		$this->PageTitle= 'Alterar Região';
		$this->loadModel('Cliente');
		$cliente 		= $this->Cliente->buscaPorCodigo($codigo_cliente);

		if($this->data['TRegiRegiao']){
			$validate = true;

			if(empty($this->data['TRegiRegiao']['regi_descricao'])){
				$this->TRegiRegiao->invalidate('regi_descricao','Informe uma descrição para a bandeira.');
				$validate = false;
			}

			// Verifica se foi reportado algum erro acima
			if($validate) {
				if($this->TRegiRegiao->save($this->data)){
					$this->BSession->setFlash('save_success');
					$this->redirect(array('controller' => 'Regioes','action' => 'index'));
				} else {
					$this->BSession->setFlash('save_error');
				}

			} else {
					$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data = $this->TRegiRegiao->buscaPorCodigo($regi_codigo);
		}

		$this->set(compact('cliente'));
	}

	function remover($regi_codigo) {
		$regiao = $this->TRegiRegiao->carregar($regi_codigo);
		$remover  = $this->TRegiRegiao->valida_remover($regi_codigo);

		if (isset($this->data['TRegiRegiao']) && $remover) {
			if($this->TRegiRegiao->delete($regi_codigo)){
				$this->BSession->setFlash('delete_success');
			} else {
				$this->BSession->setFlash('delete_error');
			}
		}

		$this->set(compact('regiao','remover'));
		
	}

}
