<?php
 
class InformacoesTecnicasController extends AppController {
    public $name = 'InformacoesTecnicas';
	public $layout = 'cliente';
	public $components = array('Filtros', 'RequestHandler');
	public $helpers = array('Html', 'Ajax', 'Buonny');

	public $uses = array(
		'Cliente', 'QuantidadeEmbarque', 'ValorEmbarque', 'PrincipalCliente',
		'SinistroUltimoMes', 'TipoSinistro'
	) ;	

	function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow('*');		
	}   
	function adicionar_quantidade_embarque($codigo_cliente){
		$this->pageTitle = 'Cadastrar Quantidade de Embarques';
		$this->layout = 'ajax';
		if(isset($this->data)){		
			$this->data['QuantidadeEmbarque']['codigo_cliente'] = $codigo_cliente;
			if(empty($this->data['QuantidadeEmbarque']['diario']) &&
				empty($this->data['QuantidadeEmbarque']['mensal']) &&
				empty($this->data['QuantidadeEmbarque']['semanal'])
			  ){
				$this->BSession->setFlash(array(MSGT_ERROR, 'Preenche ao menos um campo'));
			}else{
				if($this->QuantidadeEmbarque->incluir($this->data)){
					$this->BSession->setFlash('save_success');
				} else {
					$this->BSession->setFlash('save_error');
				}
			}		
		} 
		$this->set(compact('codigo_cliente'));
	}

	function editar_quantidade_embarque($codigo_quantidade_embarque = null){		
		$this->pageTitle = 'Cadastrar Quantidade de Embarques';
		$this->layout = 'ajax';
		
		if(isset($this->data)){
			if(empty($this->data['QuantidadeEmbarque']['diario']) &&
				empty($this->data['QuantidadeEmbarque']['mensal']) &&
				empty($this->data['QuantidadeEmbarque']['semanal'])
			  ){
				$this->BSession->setFlash(array(MSGT_ERROR, 'Preenche ao menos um campo'));
			}else{
		
				if($this->QuantidadeEmbarque->atualizar($this->data)){
					$this->BSession->setFlash('save_success');
				} else {
					
					$this->BSession->setFlash('save_error');
				}
			}
		
		} else {
			$conditions = array('codigo' => $codigo_quantidade_embarque);
			$quantidade_embarque = $this->QuantidadeEmbarque->find('first',compact('conditions'));
			$this->data = $quantidade_embarque;
		}

	}

	function remove_quantidade_embarque($codigo_quantidade_embarque)
	{
		
		$conditions = array('codigo' => $codigo_quantidade_embarque);
		$quantidade_embarque = $this->QuantidadeEmbarque->find('first',compact('conditions'));

		$retorno = null;
		if($codigo_quantidade_embarque){
			if($this->QuantidadeEmbarque->delete($codigo_quantidade_embarque)){
				$retorno = 'Registro removido com sucesso';
			}else{
				$retorno = 'Erro ao apagar registro';
			}
		} else {
			$retorno = 'Quantidade de Embarques não encontrada.';
		}

		$this->set(compact('retorno'));
		$this->render('remove');

	}

	function adicionar_valor_embarque($codigo_cliente){
		$this->pageTitle = 'Cadastrar Valor de Embarques';
		$this->layout = 'ajax';
		if(isset($this->data)){	

			$this->data['ValorEmbarque']['codigo_cliente'] = $codigo_cliente;
			if(empty($this->data['ValorEmbarque']['minimo']) &&
				empty($this->data['ValorEmbarque']['medio']) &&
				empty($this->data['ValorEmbarque']['maximo'])
			  ){
				$this->BSession->setFlash(array(MSGT_ERROR, 'Preenche ao menos um campo'));
			}else{
				if($this->ValorEmbarque->incluir($this->data)){
					$this->BSession->setFlash('save_success');
				} else {
					$this->BSession->setFlash('save_error');
				}	
			}		
		} 
		$this->set(compact('codigo_cliente'));
	}

	function editar_valor_embarque($codigo_valor_embarque = null){		
		$this->pageTitle = 'Cadastrar Valor de Embarques';
		$this->layout = 'ajax';
		
		if(isset($this->data)){
			if(empty($this->data['ValorEmbarque']['minimo']) &&
				empty($this->data['ValorEmbarque']['medio']) &&
				empty($this->data['ValorEmbarque']['maximo'])
			  ){
			  	$this->BSession->setFlash(array(MSGT_ERROR, 'Preenche ao menos um campo'));
			}else{
				if($this->ValorEmbarque->atualizar($this->data)){
					$this->BSession->setFlash('save_success');
				} else {
					
					$this->BSession->setFlash('save_error');
				}
			}
		
		} else {
			$conditions = array('codigo' => $codigo_valor_embarque);
			$valor_embarque = $this->ValorEmbarque->find('first',compact('conditions'));
			$this->data = $valor_embarque;
		}

	}

	function remove_valor_embarque($codigo_valor_embarque){
		
		$conditions = array('codigo' => $codigo_valor_embarque);
		$valor_embarque = $this->ValorEmbarque->find('first',compact('conditions'));

		$retorno = null;
		if($codigo_valor_embarque){
			if($this->ValorEmbarque->delete($codigo_valor_embarque)){
				$retorno = 'Registro removido com sucesso';
			}else{
				$retorno = 'Erro ao apagar registro';
			}
		} else {
			$retorno = 'Valor de Embarques não encontrada.';
		}

		$this->set(compact('retorno'));
		$this->render('remove');

	}

	function adicionar_principal_cliente($codigo_cliente){
		$this->pageTitle = 'Cadastrar Principais Clientes';
		$this->layout = 'ajax';
		if(isset($this->data)){		
			$this->data['PrincipalCliente']['codigo_cliente'] = $codigo_cliente;
			if(empty($this->data['PrincipalCliente']['cliente']) &&
				empty($this->data['PrincipalCliente']['produto']) 
			  ){
				$this->BSession->setFlash(array(MSGT_ERROR, 'Preenche ao menos um campo'));
			}else{
				if($this->PrincipalCliente->incluir($this->data)){
					$this->BSession->setFlash('save_success');
				} else {
					$this->BSession->setFlash('save_error');
				}	
			}		
		} 
		$this->set(compact('codigo_cliente'));
	}

	function editar_principal_cliente($codigo_principal_cliente = null){		
		$this->pageTitle = 'Cadastrar Principais Clientes';
		$this->layout = 'ajax';
		
		if(isset($this->data)){
			if(empty($this->data['PrincipalCliente']['cliente']) &&
				empty($this->data['PrincipalCliente']['produto']) 
			  ){
				$this->BSession->setFlash(array(MSGT_ERROR, 'Preenche ao menos um campo'));
			}else{
				if($this->PrincipalCliente->atualizar($this->data)){
					$this->BSession->setFlash('save_success');
				} else {
					
					$this->BSession->setFlash('save_error');
				}
			}
		
		} else {
			$conditions = array('codigo' => $codigo_principal_cliente);
			$principal_cliente = $this->PrincipalCliente->find('first',compact('conditions'));
			$this->data = $principal_cliente;
		}

	}

	function remove_principal_cliente($codigo_principal_cliente){
		
		$conditions = array('codigo' => $codigo_principal_cliente);
		$principal_cliente = $this->PrincipalCliente->find('first',compact('conditions'));

		$retorno = null;
		if($codigo_principal_cliente){
			if($this->PrincipalCliente->delete($codigo_principal_cliente)){
				$retorno = 'Registro removido com sucesso';
			}else{
				$retorno = 'Erro ao apagar registro';
			}
		} else {
			$retorno = 'Principais Clientes não encontrada.';
		}

		$this->set(compact('retorno'));
		$this->render('remove');

	}


	function adicionar_sinistro_ultimo_mes($codigo_cliente){
		$this->pageTitle = 'Cadastrar Sinistros nos Últimos 12 Meses';
		$this->loadModel('TipoSinistro');
		$tipo_sinistro = $this->TipoSinistro->find('list', array('order' => 'TipoSinistro.descricao'));
		$this->layout = 'ajax';
		if(isset($this->data)){		
			$this->data['SinistroUltimoMes']['codigo_cliente'] = $codigo_cliente;
			if(empty($this->data['SinistroUltimoMes']['data'])){
				$this->SinistroUltimoMes->invalidate('data', 'Informe a data');
			}else{
				if($this->SinistroUltimoMes->incluir($this->data)){
					$this->BSession->setFlash('save_success');
				} else {
					$this->BSession->setFlash('save_error');
				}	
			}		
		} 
		$this->set(compact('codigo_cliente', 'tipo_sinistro'));
	}

	function editar_sinistro_ultimo_mes($codigo_sinistro_ultimo_mes = null){		
		$this->pageTitle = 'Cadastrar Sinistros nos Últimos 12 Meses';
		$this->loadModel('TipoSinistro');
		$tipo_sinistro  = $this->TipoSinistro->find('list', array('order' => 'TipoSinistro.descricao'));
		$this->layout = 'ajax';
		
		if(isset($this->data)){
			if(empty($this->data['SinistroUltimoMes']['data'])){
				$this->SinistroUltimoMes->invalidate('data', 'Informe a data');
			}else{
				if($this->SinistroUltimoMes->atualizar($this->data)){
					$this->BSession->setFlash('save_success');
				} else {					
					$this->BSession->setFlash('save_error');
				}
			}
		
		} else {
			$conditions = array('SinistroUltimoMes.codigo' => $codigo_sinistro_ultimo_mes);
			$sinistro_ultimo_mes = $this->SinistroUltimoMes->find('first',compact('conditions'));
			$this->data = $sinistro_ultimo_mes;
		}
		$this->set(compact('tipo_sinistro'));

	}

	function remove_sinistro_ultimo_mes($codigo_sinistro_ultimo_mes){
		
		$conditions = array('codigo' => $codigo_sinistro_ultimo_mes);
		$sinistro_ultimo_mes = $this->SinistroUltimoMes->find('first',compact('conditions'));

		$retorno = null;
		if($codigo_sinistro_ultimo_mes){
			if($this->SinistroUltimoMes->delete($codigo_sinistro_ultimo_mes)){
				$retorno = 'Registro removido com sucesso';
			}else{
				$retorno = 'Erro ao apagar registro';
			}
		} else {
			$retorno = 'Sinistros nos Últimos 12 Meses não encontrada.';
		}

		$this->set(compact('retorno'));
		$this->render('remove');

	}

}