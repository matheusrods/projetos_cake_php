<?php

class UnidadesMedicaoController extends AppController
{
    public $name = 'UnidadesMedicao';

    public $uses = array(
        'UnidadesMedicao'
    );

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow('*');
    }

    public function index()
    {
        $this->pageTitle = 'Lista de Unidades de Medida';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->UnidadesMedicao->name);
        $this->data = $filtros;
    }

    public function listagem()
    {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->UnidadesMedicao->name);

        // INICIO - filtrar por usuário logado
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        // A função normalizaCodigoCliente() lista todos se usuário logado for interno
        if (!empty($filtros['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->normalizaCodigoCliente($filtros['codigo_cliente']);
        }

        $this->data['UnidadeMedicao'] = $filtros;
        // FIM - filtrar por usuário logado

        $this->paginate['UnidadeMedicao'] = $this->UnidadesMedicao->getListaUnidadesMedicao($filtros);

        $unidades_medicao = $this->paginate('UnidadesMedicao');

        $this->set(compact('unidades_medicao'));
    }

    public function incluir()
    {
        $this->pageTitle = 'Incluir Nova Unidade de Medida';

        if ($this->RequestHandler->isPost()) {
//            $this->data['UnidadeMedicao']['ativo'] = 1; //Adiciona codigo ativo na criação

            if ($this->UnidadesMedicao->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'unidades_medicao', 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
    }

    public function editar($codigo)
    {
        $this->pageTitle = 'Editar Unidade de Medida';

        if ($this->RequestHandler->isPut()) {
            if ($this->UnidadesMedicao->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'unidades_medicao', 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->data = $this->UnidadesMedicao->getByCodigo($codigo);

        if (empty($this->data)) {
            $this->redirect(array('controller' => 'unidades_medicao', 'action' => 'index'));
        }
    }

    public function editar_status($codigo)
    {
        $this->layout = 'ajax';
        
        $riscosTipo = $this->RiscosTipo->read(null, $codigo);
        $riscosTipo['RiscosTipo']['ativo'] = ($riscosTipo['RiscosTipo']['ativo'] == 0 ? 1 : 0);

        if ($this->RiscosTipo->atualizar($riscosTipo, false)) {
            $this->render(false, false);
            print 1;
        } else {
            $this->render(false, false);
            print 0;
        }

        // 0 -> ERRO | 1 -> SUCESSO
    }
}
