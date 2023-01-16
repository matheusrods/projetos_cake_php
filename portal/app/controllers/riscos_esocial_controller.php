<?php

class RiscosEsocialController extends AppController
{
    public $name = 'RiscosEsocial';

    public $uses = array(
        'RiscosEsocial',
        'Cliente',
        'Risco'
    );

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow('*');
    }

    public function index()
    {
        $this->pageTitle = 'Lista de Riscos do e-Social';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Risco->name);
        $this->data = $filtros;

        $this->comboGrupoRisco();

    }

    public function listagem()
    {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Risco->name);

        $this->data['Risco'] = $filtros;
        // FIM - filtrar por usuário logado

        $this->paginate['Risco'] = $this->Risco->getListaRiscos($filtros);

        $riscos = $this->paginate('Risco');
        $this->set(compact('riscos'));
    }

    public function incluir()
    {
        $this->pageTitle = 'Incluir Risco do e-Social';

        if ($this->RequestHandler->isPost()) {
            $this->data['RiscosEsocial']['ativo'] = 1; //Adiciona codigo ativo na criação

            if ($this->RiscosEsocial->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'riscos_esocial', 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->comboGrupoRisco();
    }

    public function editar($codigo)
    {
        $this->pageTitle = 'Editar Risco e-Social';

        if ($this->RequestHandler->isPut()) {
            if ($this->RiscosEsocial->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'riscos_esocial', 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->data = $this->RiscosEsocial->getByCodigo($codigo);

        if (empty($this->data)) {
            $this->redirect(array('controller' => 'riscos_esocial', 'action' => 'index'));
        }

        $this->comboGrupoRisco();
    }

    public function editar_status($codigo)
    {
        $this->layout = 'ajax';

        $riscosEsocial = $this->RiscosEsocial->read(null, $codigo);
        $riscosEsocial['RiscosEsocial']['ativo'] = ($riscosEsocial['RiscosEsocial']['ativo'] == 0 ? 1 : 0);

        if ($this->RiscosEsocial->atualizar($riscosEsocial, false)) {
            $this->render(false, false);
            print 1;
        } else {
            $this->render(false, false);
            print 0;
        }

        // 0 -> ERRO | 1 -> SUCESSO
    }

    public function comboGrupoRisco($data = null)
    {
        $this->loadModel('GrupoRisco');

        $combo_grupo_risco = $this->GrupoRisco->retorna_grupo();
        $this->set(compact('combo_grupo_risco'));
    }

    /*
     * function filtrar()
     *
     * Função para filtrar riscos do e-Social no modal das telas de criar e editar riscos_impactos
    */
    public function filtrar()
    {
        $this->layout = 'ajax';

        $filtros = $_POST;

        $this->paginate['RiscosEsocial'] = $this->RiscosEsocial->getListaRiscosEsocial($filtros);

        $riscos_esocial = $this->paginate('RiscosEsocial');

        echo json_encode($riscos_esocial);
    }

}
