<?php

class ChamadosController extends AppController
{
    public $name = 'Chamados';

    public $uses = array(
        'Chamado',
        'ChamadoTipo',
        'ChamadoStatus',
        'Usuario',
        'Cliente'
    );

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow();
    }

    public function index()
    {
        $this->pageTitle = 'Lista de Chamados';
        
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Chamado->name);
        $this->data = $filtros;

        if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {
            //Filtro para usuario não admin
            $codigo_cliente =  $this->authUsuario['Usuario']['codigo_cliente'];

            $nome_fantasia = $this->Cliente->find('first', array(
                'fields' => array(
                    'nome_fantasia'
                ),
                'conditions' => array(
                    'codigo' => $codigo_cliente
                )
            ));

            $is_admin = 0;
        } else {
            //Filtro para usuario admin
            $codigo_cliente = null;
            $is_admin = 1;
            $nome_fantasia = null;
        }

        $this->comboChamadoTipo();
        $this->comboChamadoStatus();

        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }

    public function combo_usuarios($codigo_cliente)
    {
        $combo_usuarios = $this->Usuario->listaPorClienteListAtivo($codigo_cliente);
        $this->set(compact('combo_usuarios'));
    }

    public function combo_usuarios_ajax()
    {
        $this->autoRender = false;

        $usuarios = null;
        if (!empty($this->params['form']['codigo_cliente'])) {
            $usuarios = $this->Usuario->listaPorClienteListAtivo($this->params['form']['codigo_cliente']);
        }
        
        $html = '<option value="">Selecione</option>';
        if(!empty($usuarios)) {
            foreach ($usuarios as $key => $usuario) {
                $html .= '<option value="'. $key .'">' . $usuario . '</option>';
            }
        }

        return $html;
    }

    public function comboChamadoTipo($data = null)
    {
        $this->loadModel('ChamadoTipo');

        $combo_chamado_tipo = $this->ChamadoTipo->retorna_tipo();

        if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {
            //Filtro para usuario não admin
            $codigo_cliente =  $this->authUsuario['Usuario']['codigo_cliente'];

            $nome_fantasia = $this->Cliente->find('first', array(
                'fields' => array(
                    'nome_fantasia'
                ),
                'conditions' => array(
                    'codigo' => $codigo_cliente
                )
            ));

            $is_admin = 0;
        } else {
            //Filtro para usuario admin
            $codigo_cliente = null;
            $is_admin = 1;
            $nome_fantasia = null;
        }

        $this->set(compact('combo_chamado_tipo'));
        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }

    public function comboChamadoStatus($data = null)
    {
        $this->loadModel('ChamadoStatus');

        $combo_chamado_status = $this->ChamadoStatus->retorna_status();
        $this->set(compact('combo_chamado_status'));
    }

    public function listagem()
    {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Chamado->name);
        
        // INICIO - filtrar a lista de chamados por usuário logado
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }
                
        // A função normalizaCodigoCliente() lista todos se usuário logado for interno
        if (!empty($filtros['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->normalizaCodigoCliente($filtros['codigo_cliente']);
        }

        $this->data['Chamado'] = $filtros;
        // FIM - filtrar a lista de chamados por usuário logado

        $this->paginate['Chamado'] = $this->Chamado->getListaChamados($filtros);
        
        $chamados = $this->paginate('Chamado');
        $this->set(compact('chamados'));
    }

    public function incluir()
    {
        $this->pageTitle = 'Incluir Novo Chamado';

        if ($this->RequestHandler->isPost()) {
            if ($this->Chamado->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'chamados', 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->comboChamadoTipo();
        $this->combo_usuarios($this->authUsuario['Usuario']['codigo_cliente']);
    }

    public function editar($codigo)
    {
        $this->pageTitle = 'Editar Chamado';
                
        if ($this->RequestHandler->isPost()) {
            if ($this->Chamado->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'chamados', 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
                
        $this->data = $this->Chamado->getByCodigo($codigo);

        if (empty($this->data)) {
            $this->redirect(array('controller' => 'chamados', 'action' => 'index'));
        }

        $this->comboChamadoTipo();
        $this->comboChamadoStatus();
        $this->combo_usuarios($this->data['Cliente']['codigo']);
    }
}
