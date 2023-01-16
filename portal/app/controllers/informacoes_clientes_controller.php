<?php
class InformacoesClientesController extends AppController {
    public $name = 'InformacoesClientes';
    var $uses = array('InformacaoCliente', 'SistemaMonitoramento');
    
    function index() {
        $this->pageTitle = 'Informações Clientes';
        $this->data['InformacaoCliente'] = $this->Filtros->controla_sessao($this->data, $this->InformacaoCliente->name);

				$areasAtuacao = $this->InformacaoCliente->AreaAtuacao->find('list');
				$sistemasMonitoramento = SistemaMonitoramento::lista();
				$this->set(compact('areasAtuacao', 'sistemasMonitoramento'));
    }
    
    function listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->InformacaoCliente->name);
        $conditions = $this->InformacaoCliente->converteFiltroEmCondition($filtros);
        $this->paginate['InformacaoCliente'] = array(
						'fields' => array('InformacaoCliente.codigo', 'InformacaoCliente.razao_social', 'InformacaoCliente.codigo_area_atuacao', 'InformacaoCliente.codigo_sistema_monitoramento', 'AreaAtuacao.descricao'),
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'InformacaoCliente.razao_social',
        );

        $informacoes_clientes = $this->paginate('InformacaoCliente');

        $this->set(compact('informacoes_clientes'));
    }
    
    function editar($codigo_cliente = null) {
        $this->pageTitle = 'Atualizar Informações do Cliente';
        if (!$codigo_cliente && empty($this->data)) {
            $this->BSession->setFlash('codigo_invalido');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
						$this->data['InformacaoCliente']['codigo'] = $codigo_cliente;
            if ($this->InformacaoCliente->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $informacao_cliente = $this->InformacaoCliente->find('first', array(
					'fields' => array('InformacaoCliente.codigo', 'InformacaoCliente.razao_social', 'InformacaoCliente.nome_fantasia', 'InformacaoCliente.codigo_documento', 'InformacaoCliente.codigo_area_atuacao', 'InformacaoCliente.codigo_sistema_monitoramento', 'AreaAtuacao.descricao'),
					'conditions' => array('InformacaoCliente.codigo' => $codigo_cliente),           	
        ));

				$this->data = empty($this->data) ? $informacao_cliente : array_merge($informacao_cliente, $this->data);

				$areasAtuacao = $this->InformacaoCliente->AreaAtuacao->find('list');
				$sistemasMonitoramento = SistemaMonitoramento::lista();
				$this->set(compact('areasAtuacao', 'sistemasMonitoramento'));
    }

}