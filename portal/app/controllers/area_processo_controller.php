<?php

class AreaProcessoController extends AppController
{
    public $name = 'AreaProcesso';

    public $uses = array(
        'Cliente',
        'AreaProcesso'
    );

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow('*');
    }

    public function index()
    {
        $this->pageTitle = 'Lista de Área de Processos';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->AreaProcesso->name);
        $this->data = $filtros;

        if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {
            //Filtro para usuario não admin
            $codigo_cliente =  $this->authUsuario['Usuario']['codigo_cliente'];

            $nome_fantasia = $this->cliente_nome($codigo_cliente);

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

        $filtros = $this->Filtros->controla_sessao($this->data, $this->AreaProcesso->name);

        // INICIO - filtrar por usuário logado
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        //pega as assinaturas
        $area_atuacao = array();
        $codigo_cliente = '';

        if(!empty($filtros['codigo_cliente'])) {

            //pega as assinaturas
            $assinaturas = $this->Cliente->getAssinaturaPDASWTOBS($filtros['codigo_cliente'],'PLANO_DE_ACAO');
            if(!empty($assinaturas)) {
                $filtros['codigo_cliente'] = $assinaturas;

                $codigo_cliente = (is_array($filtros['codigo_cliente'])) ? implode(',',$filtros['codigo_cliente']) : $filtros['codigo_cliente'];

                $this->data['AreaProcesso'] = $filtros;

                // FIM - filtrar por usuário logado
                $this->paginate['AreaProcesso'] = $this->AreaProcesso->getListaAreaProcesso($filtros);
                $area_processo = $this->paginate('AreaProcesso');
            }
        }


        $this->set(compact('area_processo', 'codigo_cliente'));
    }

    public function incluir($codigo_cliente)
    {
        $this->pageTitle = 'Incluir Nova Área de Processo';

        if ($this->RequestHandler->isPost()) {

            //Declaro para começar inserir na tabela SubperfilAcoes
            $this->AreaProcesso->query('begin transaction');

            try {

                $this->data['AreaProcesso']['codigo_empresa'] = $this->authUsuario['Usuario']['codigo_empresa'];

                if (!$this->AreaProcesso->incluir($this->data)) {
                    throw new Exception("Erro ao incluir area de processo");
                    
                    // $this->AreaAtuacao->rollback();
                    // $this->BSession->setFlash('save_error');
                } 


                $this->AreaProcesso->commit();
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'area_processo', 'action' => 'index'));
            } catch(Exception $e) {
                // debug($e->getmessage());
                $msg = $e->getmessage();
                $this->AreaProcesso->rollback();
                $this->BSession->setFlash(array(MSGT_ERROR, $msg));
            }

        }

        $nome_fantasia = $this->cliente_nome($codigo_cliente);
        if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {
            $is_admin = 0;
        } else {
            $is_admin = 1;
        }

        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }

    public function editar($codigo)
    {
        $this->pageTitle = 'Editar Área de Processo';

        if ($this->RequestHandler->isPut()) {

            $this->data['AreaProcesso']['codigo_empresa'] = $this->authUsuario['Usuario']['codigo_empresa'];

            if ($this->AreaProcesso->atualizar($this->data)) {

                $this->AreaProcesso->commit();

                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'area_processo', 'action' => 'index'));
            } else {
                $this->AreaProcesso->rollback();
                $this->BSession->setFlash('save_error');
            }
        }

        $area_processo = $this->AreaProcesso->getByCodigo($codigo);

        $this->data = $area_processo;

        if (empty($this->data)) {
            $this->redirect(array('controller' => 'area_processo', 'action' => 'index'));
        }

        $codigo_cliente = $this->data['AreaProcesso']['codigo_cliente'];

        $nome_fantasia = $this->Cliente->find('first', array(
            'fields' => array(
                'nome_fantasia'
            ),
            'conditions' => array(
                'codigo' => $codigo_cliente
            )
        ));

        $nome_fantasia = $nome_fantasia['Cliente']['nome_fantasia'];

        $this->set(compact('codigo_cliente', 'nome_fantasia'));
    }

    public function editar_status($codigo)
    {
        $this->layout = 'ajax';

        $area_processo = $this->AreaProcesso->read(null, $codigo);
        $area_processo['AreaProcesso']['ativo'] = ($area_processo['AreaProcesso']['ativo'] == 0 ? 1 : 0);

        if ($this->AreaProcesso->atualizar($area_processo, false)) {
            $this->render(false, false);
            print 1;
        } else {
            $this->render(false, false);
            print 0;
        }

        // 0 -> ERRO | 1 -> SUCESSO
    }

    public function cliente_nome($codigo_cliente)
    {
        $this->loadModel("Cliente");

        if (!empty($codigo_cliente)) {
            $nome_fantasia = $this->Cliente->find('first', array(
                'fields' => array(
                    'nome_fantasia'
                ),
                'conditions' => array(
                    'codigo' => $codigo_cliente
                )
            ));
            return $nome_fantasia['Cliente']['nome_fantasia'];
        } else {
            return '';
        }
    }
}
