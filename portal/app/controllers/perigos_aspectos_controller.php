<?php

class PerigosAspectosController extends AppController
{
    public $name = 'PerigosAspectos';

    public $uses = array(
        'RiscosTipo',
        'PerigosAspectos',
        'PerigosAspectosTipo',
        'Cliente'
    );

    public $codigo_cliente;

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow();
    }

    public function index()
    {
        $this->pageTitle = 'Lista de Perigos Aspectos';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->PerigosAspectos->name);
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

        $this->comboRiscosTipo();

        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }

    public function listagem()
    {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->PerigosAspectos->name);

        // INICIO - filtrar por usuário logado
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        $this->data['PerigosAspectos'] = $filtros;
        // FIM - filtrar por usuário logado

        $this->paginate['PerigosAspectos'] = $this->PerigosAspectos->getListaPerigosAspectos($filtros);

        $perigos_aspectos = $this->paginate('PerigosAspectos');
        $this->set(compact('perigos_aspectos'));
    }

    public function incluir()
    {
        $this->pageTitle = 'Incluir Novo Perigo ou Aspecto';

        if($this->RequestHandler->isPost()) {
            $this->data['PerigosAspectos']['ativo'] = 1; //Adiciona codigo ativo na criação

            if ($this->PerigosAspectos->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'perigos_aspectos', 'action' => 'index'));
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

        $this->comboRiscosTipo();

        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }

    public function editar($codigo)
    {
        $this->pageTitle = 'Editar Perigo ou Aspecto';

        if ($this->RequestHandler->isPut()) {

            if ($this->PerigosAspectos->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'perigos_aspectos', 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->data = $this->PerigosAspectos->getByCodigo($codigo);

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

        $data = array();
        $data['codigo_cliente'] = $codigo_cliente;

        if(empty($this->data)){
            $this->redirect(array('controller' => 'perigos_aspectos', 'action' => 'index'));
        }

        $this->comboRiscosTipo();

        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }

    public function comboRiscosTipo($data = null)
    {
        $this->loadModel('RiscosTipo');

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

    function editar_status($codigo, $descricao, $codigo_risco_tipo, $status, $codigo_cliente)
    {
        $this->layout = 'ajax';

        $this->loadModel('PerigosAspectos');

        $codigo = trim($codigo);
        $status= ($status == 0) ? $status = 1 : $status = 0;

        $this->data['PerigosAspectos']['codigo'] = $codigo;
        $this->data['PerigosAspectos']['ativo'] = $status;
        $this->data['PerigosAspectos']['descricao'] = $descricao;
        $this->data['PerigosAspectos']['codigo_risco_tipo'] = $codigo_risco_tipo;
        $this->data['PerigosAspectos']['codigo_cliente'] = $codigo_cliente;

        if ($this->PerigosAspectos->atualizar($this->data)) {
            $this->render(false,false);
            print 1;
        } else {
            $this->render(false,false);
            print 0;
        }

        // 0 -> ERRO | 1 -> SUCESSO
    }

    public function obtem_riscos_tipo_por_ajax()
    {
        $this->autoRender = false;

        // if($bloqueado) {
        $this->loadModel('RiscosTipo');

        if (isset($this->params['form']['codigo_cliente']) && !empty($this->params['form']['codigo_cliente'])) {
            $codigo_cliente = array(
                'RiscosTipo.codigo_cliente' => $this->params['form']['codigo_cliente']
            );
        } else {
            $codigo_cliente = array();
        }

        $riscos_tipo = $this->RiscosTipo->find('list', array(
                'recursive' => -1,
                'fields' => array(
                    'RiscosTipo.codigo',
                    'RiscosTipo.descricao'
                ),
                'conditions' => array(
                    'RiscosTipo.ativo' => 1,
                    'RiscosTipo.codigo_cliente' => $this->params['form']['codigo_cliente']
                ),
                'order' => 'RiscosTipo.descricao'
            )
        );

        $html = '<option value="">Selecione</option>';
        if(!empty($riscos_tipo)) {
            foreach ($riscos_tipo as $key => $rt) {
                $html .= '<option value="'.$key.'">'.$rt.'</option>';
            }
        }
        unset($riscos_tipo);
        return $html;
    }
}
