<?php

class RiscosTiposController extends AppController
{
    public $name = 'RiscosTipos';

    public $uses = array(
        'RiscosTipo',
        'Cliente'
    );

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow('*');
    }

    public function index()
    {
        $this->pageTitle = 'Lista de Riscos Tipo';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->RiscosTipo->name);
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

        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }

    public function listagem()
    {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->RiscosTipo->name);

        // INICIO - filtrar por usuário logado
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        $this->data['RiscosTipo'] = $filtros;
        // FIM - filtrar por usuário logado

        $this->paginate['RiscosTipo'] = $this->RiscosTipo->getListaRiscosTipo($filtros);

        $riscos_tipo = $this->paginate('RiscosTipo');
        $this->set(compact('riscos_tipo'));
    }

    public function incluir()
    {
        $this->pageTitle = 'Incluir Novo Risco Tipo';

        if ($this->RequestHandler->isPost()) {
            $this->data['RiscosTipo']['ativo'] = 1; //Adiciona codigo ativo na criação

            if ($this->RiscosTipo->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'riscos_tipos', 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

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

        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }

    public function editar($codigo)
    {
        $this->pageTitle = 'Editar Risco Tipo';

        if ($this->RequestHandler->isPut()) {
            if ($this->RiscosTipo->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'riscos_tipos', 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->data = $this->RiscosTipo->getByCodigo($codigo);

        if (empty($this->data)) {
            $this->redirect(array('controller' => 'riscos_tipos', 'action' => 'index'));
        }

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

        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
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

    public function comboRiscosTipo($data = null)
    {
        $this->loadModel('RiscosTipo');

        //Verifica se usuário logado não é admin e se tem perfil 43 (Gestão de risco)
        // para retornar apenas os itens refetes ao cliente
        if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {

            if ($data != $this->authUsuario['Usuario']['codigo_cliente']) {
                $data['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

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

        $combo_riscos_tipo = $this->RiscosTipo->retornaRiscoTipo($data);

        $this->set(compact('combo_riscos_tipo'));
        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }

}
