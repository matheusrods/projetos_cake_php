<?php

class AcoesMelhoriasTipoController extends AppController
{
    public $name = 'AcoesMelhoriasTipo';

    public $uses = array(
        'AcoesMelhoriasTipo',
        'Cliente'
    );

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow('*');
    }

    public function index()
    {
        $this->pageTitle = 'Lista de Ações de Melhorias Tipo';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->AcoesMelhoriasTipo->name);
        $this->data = $filtros;

        //Filtro para usuario admin
        $codigo_cliente = null;
        $is_admin = 1;
        $nome_fantasia = null;

        if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1 ) {

            //Filtro para usuario não admin
            $codigo_cliente =  $this->authUsuario['Usuario']['codigo_cliente'];

            $nome_fantasia = $this->cliente_nome($codigo_cliente);

            $is_admin = 0;
        }

        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }

    public function listagem()
    {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->AcoesMelhoriasTipo->name);


        // INICIO - filtrar por usuário logado
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $acoes_melhorias_tipo =array();
        $codigo_cliente = '';
        if(!empty($filtros['codigo_cliente'])) {

            //pega as assinaturas
            $assinaturas = $this->Cliente->getAssinaturaPDASWTOBS($filtros['codigo_cliente'],'PLANO_DE_ACAO');
            if(!empty($assinaturas)) {
                $filtros['codigo_cliente'] = $assinaturas;
                
                $codigo_cliente = (is_array($filtros['codigo_cliente'])) ? implode(',',$filtros['codigo_cliente']) : $filtros['codigo_cliente'];

                $this->data['AcoesMelhoriasTipo'] = $filtros;
                // FIM - filtrar por usuário logado

                $this->paginate['AcoesMelhoriasTipo'] = $this->AcoesMelhoriasTipo->getListaAcoesMelhoriasTipo($filtros);

                $acoes_melhorias_tipo = $this->paginate('AcoesMelhoriasTipo');
            }
        }

        $this->set(compact('acoes_melhorias_tipo','codigo_cliente'));
    }

    public function incluir($codigo_cliente)
    {
        $this->pageTitle = 'Incluir Novo Ações Melhorias Tipo';

        if ($this->RequestHandler->isPost()) {

            //Declaro para começar inserir na tabela SubperfilAcoes
            $this->AcoesMelhoriasTipo->query('begin transaction');

            try {

                if ($this->AcoesMelhoriasTipo->incluir($this->data)) {

                    $this->AcoesMelhoriasTipo->commit();
                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('controller' => 'acoes_melhorias_tipo', 'action' => 'index'));
                } else {
                    $this->AcoesMelhoriasTipo->rollback();
                    $this->BSession->setFlash('save_error');
                }

            } catch(Exception $e) {
                // debug($e->getmessage());
                $msg = $e->getmessage();
                $this->AcoesMelhoriasTipo->rollback();
                $this->BSession->setFlash(array(MSGT_ERROR, $msg));
            }

        }

        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }

    public function editar($codigo)
    {
        $this->pageTitle = 'Editar Ações Melhorias Tipo';

        if ($this->RequestHandler->isPut()) {
            if ($this->AcoesMelhoriasTipo->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'acoes_melhorias_tipo', 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->data = $this->AcoesMelhoriasTipo->getByCodigo($codigo);

        if (empty($this->data)) {
            $this->redirect(array('controller' => 'acoes_melhorias_tipo', 'action' => 'index'));
        }

        $codigo_cliente =  $this->data['AcoesMelhoriasTipo']['codigo_cliente'];

        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }

    public function editar_status($codigo)
    {
        $this->layout = 'ajax';

        $acoes_melhorias_tipo = $this->AcoesMelhoriasTipo->read(null, $codigo);
        $acoes_melhorias_tipo['AcoesMelhoriasTipo']['ativo'] = ($acoes_melhorias_tipo['AcoesMelhoriasTipo']['ativo'] == 0 ? 1 : 0);

        if ($this->AcoesMelhoriasTipo->atualizar($acoes_melhorias_tipo, false)) {
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
