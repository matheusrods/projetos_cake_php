<?php
class CotacoesController extends AppController {
	public $name = 'Cotacoes';
	public $uses = array('Cotacao');

	public function index()
	{
		$this->pageTitle = 'Cotações realizadas';
	}

	public function listagem() {
		$this->layout = 'ajax'; 
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cotacao->name);
		$conditions = $this->Cotacao->converteFiltroEmCondition($filtros);
		$fields = array('Cotacao.codigo', 'Cotacao.data_inclusao', 'Cotacao.valor_total', 'Cliente.nome_fantasia', 'Cotacao.emails', 'Vendedor.nome', 'FormaPagto.descricao');
		$order = 'Cotacao.codigo DESC';
		$this->paginate['Cotacao'] = array(
			'contain' => array(
				'ItemCotacao.Servico',
				'Cliente',
				'Vendedor',
				'FormaPagto'
				),
			'fields' => $fields,
			'conditions' => $conditions,
			'limit' => 50,
			'order' => $order,
			);
		$this->loadModel('ClienteContato');
		$this->Cotacao->virtualFields = array('valor_total' => 'SELECT SUM(quantidade*valor_unitario) FROM itens_cotacoes WHERE codigo_cotacao = Cotacao.codigo',
			'emails' => $this->ClienteContato->obtemEmailsCliente('Cliente.codigo', true));
		$cotacoes = $this->paginate('Cotacao');
		$this->set(compact('cotacoes'));
	}

	public function incluir($passo = NULL, $codigo = NULL) 
	{
		$this->pageTitle = 'Incluir Cotação';
		if(is_null($passo)) {  
			if($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
				$this->data['Cotacao'] = array('nome' => '');
				if($this->Cotacao->incluirTodos($this->data)) {
					$this->BSession->setFlash(array('alert alert-success', 'Serviço(s) salvo(s) com sucesso.'));
					return $this->redirect(array('action' => 'incluir', 'passo_2', $this->Cotacao->id));
				} else {
					$this->BSession->setFlash(array('alert alert-error', 'Falha na inclusão dos serviços. Por favor tente novamente'));
				}
			}
		} else {
			if($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
				$this->data['Cotacao']['codigo'] = $codigo;
				if($this->Cotacao->atualizar($this->data)) {
					if(isset($this->data['Cotacao']['enviar_email']) && $this->data['Cotacao']['enviar_email']) {
						$this->loadModel('ClienteContato');
						$emails = $this->ClienteContato->obtemEmailsCliente($this->data['Cotacao']['codigo_cliente']);
						$this->disparar_email($this->data['Cotacao']['codigo'], $emails);
					}
					$this->BSession->setFlash(array('alert alert-success', 'Cotação salva com sucesso.'));
					return $this->redirect(array('action' => 'index'));
				} else {
					$this->BSession->setFlash(array('alert alert-error', 'Falha na criação da cotação. Tente novamente.'));
				}
			}	
			$this->Cotacao->virtualFields = array('valor_total' => 'SELECT SUM(quantidade*valor_unitario) FROM itens_cotacoes WHERE codigo_cotacao = Cotacao.codigo');
			$this->Cotacao->contain('ItemCotacao.Servico');
			$cotacao = $this->Cotacao->findByCodigo($codigo);
			$this->set(compact('cotacao'));
			$this->loadModel('FormaPagto');
			$formas_pagto = $this->FormaPagto->find('list', array('order' => 'descricao', 'fields' => array('codigo', 'descricao')));
			$this->set(compact('formas_pagto'));
		}
		$this->set(compact('passo', 'codigo'));
	}

	public function excluir($codigo = null)
	{
		if(is_null($codigo)) {
			return $this->redirect(array('action' => 'incluir'));
		}
		$this->Cotacao->id = $codigo;
		if(!$this->Cotacao->exists()) {
			$this->BSession->setFlash(array('alert alert-error', 'A cotação não existe ou já foi excluído'));
			return $this->redirect(array('action' => 'incluir'));
		}
		if($this->Cotacao->excluir($codigo)) {
			$this->BSession->setFlash(array('alert alert-success', 'Cotação excluído com sucesso.'));
		} else {
			$this->BSession->setFlash(array('alert alert-error', 'Falha ao excluir a cotação. Tente novamente.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	public function disparar_email($codigo = NULL, $email = NULL)
	{
		if(is_null($codigo)) $codigo = $this->data['Cotacao']['codigo'];
		if(is_null($email)) $email = $this->data['Cotacao']['email'];

		$this->Cotacao->virtualFields = array('valor_total' => 'SELECT SUM(quantidade*valor_unitario) FROM itens_cotacoes WHERE codigo_cotacao = Cotacao.codigo');
		$this->Cotacao->contain(array('ItemCotacao.Servico', 'Cliente', 'Vendedor', 'FormaPagto'));
		$cotacao = $this->Cotacao->findByCodigo($codigo);

		if($this->Cotacao->enviaCotacaoPorEmail($cotacao['Cliente']['nome_fantasia'], $email, $cotacao['Vendedor']['nome'], $cotacao['FormaPagto']['descricao'], $cotacao)) {
			$this->BSession->setFlash(array('alert alert-success', 'E-mail enviado com sucesso'));
		} else {
			$this->BSession->setFlash(array('alert alert-error', 'Falha ao enviar o e-mail. Tente novamente.'));

		}
		return $this->redirect(array('action' => 'index'));
	}


}