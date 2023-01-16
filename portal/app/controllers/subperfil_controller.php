<?php


class SubperfilController extends AppController
{
    public $name = 'Subperfil';

    public $uses = array(
        'Subperfil',
        'Cliente',
        'SubperfilAcoes'
    );

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow('*');
    }

    public function index()
    {
        $this->pageTitle = 'Lista de Subperfil';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Subperfil->name);

        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $this->data = $filtros;

        $cliente = $this->Cliente->find('first', array(
            'fields' => array(
                'nome_fantasia'
            ),
            'conditions' => array(
                'codigo' => $filtros['codigo_cliente']
            )
        ));

        $nome_fantasia = $cliente['Cliente']['nome_fantasia'];

        //Filtro para usuario admin
        $codigo_cliente = null;
        $is_admin = 1;
        if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {
            //Filtro para usuario não admin
            $codigo_cliente =  $this->authUsuario['Usuario']['codigo_cliente'];
            $is_admin = 0;
        } 

        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }

    public function listagem()
    {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Subperfil->name);

        // INICIO - filtrar por usuário logado
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $subperfil = array();
        $codigo_cliente = '';
        if(!empty($filtros['codigo_cliente'])) {
            //pega as assinaturas
            $assinaturas = $this->Cliente->getAssinaturaPDASWTOBS($filtros['codigo_cliente'],'PLANO_DE_ACAO');
            if(!empty($assinaturas)) {
                $filtros['codigo_cliente'] = $assinaturas;

                $codigo_cliente = (is_array($filtros['codigo_cliente'])) ? implode(',',$filtros['codigo_cliente']) : $filtros['codigo_cliente'];

                $this->data['Subperfil'] = $filtros;
                // FIM - filtrar por usuário logado

                $this->paginate['Subperfil'] = $this->Subperfil->getListaSubperfil($filtros);

                $subperfil = $this->paginate('Subperfil');
            }
        }


        $this->set(compact('subperfil','codigo_cliente'));
    }

    public function incluir($codigo_cliente)
    {
        $this->pageTitle = 'Incluir Novo Subperfil';

        if ($this->RequestHandler->isPost()) {

            //Declaro para começar inserir na tabela SubperfilAcoes
            $this->Subperfil->query('begin transaction');

            try {

                if ($this->data['Subperfil']['tipo_interno'] == 1) {
                    $this->data['Subperfil']['interno'] = 1;
                } else {
                    $this->data['Subperfil']['interno'] = 0;
                }

                unset($this->data['Subperfil']['tipo_interno']);
                unset($this->data['Subperfil']['tipo_externo']);

                if ($this->Subperfil->incluir($this->data)) {

                    $codigo_subperfil = $this->Subperfil->getLastInsertID();//Retorna o ultimo codigo inserido

                    $acao_tipo = $this->data['acao_tipo'];//Retorna todos os checkebox enviado no post

                    //Declaro para começar inserir na tabela SubperfilAcoes
                    $this->SubperfilAcoes->query('begin transaction');
                    $subperfil_acao_error_count = 0;

                    foreach ($acao_tipo as $key => $obj) {//Verifica cada valor do checkbox enviado e trata o objeto para pegar os codigo referentes a cada campo da tabela subperfil_acoes

                        if ($obj != '0') {

                            $separa_array = explode(".", $obj);//$obj = null.1.null
                            $codigo_acao = $separa_array[1];

                            $subperfil_acao = array(
                                'codigo_subperfil' => $codigo_subperfil,
                                'codigo_acao' => $codigo_acao,
                                'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo']
                            );

                            if (!$this->SubperfilAcoes->incluir($subperfil_acao)) {
                                $subperfil_acao_error_count++;
                            }
                        }
                    }

                    if ($subperfil_acao_error_count > 0) {
                        $this->Subperfil->rollback();
                        $this->SubperfilAcoes->rollback();
                    } else {
                        $this->Subperfil->commit();
                        $this->SubperfilAcoes->commit();
                        $this->BSession->setFlash('save_success');
                        $this->redirect(array('controller' => 'subperfil', 'action' => 'index'));
                    }

                } else {
                    $this->SubperfilAcoes->rollback();
                    $this->BSession->setFlash('save_error');
                }

            } catch(Exception $e) {
                // debug($e->getmessage());
                $msg = $e->getmessage();
                $this->SubperfilAcoes->rollback();
                $this->BSession->setFlash(array(MSGT_ERROR, $msg));
            }

        }

        $subperfil = $this->Subperfil->getListaSubperfil();

        $this->data = $subperfil;

        $this->set(compact('codigo_cliente'));
    }

    public function editar($codigo)
    {
        $this->pageTitle = 'Editar Subperfil';

        if ($this->RequestHandler->isPut()) {

            if ($this->data['Subperfil']['tipo_interno'] == 1) {
                $this->data['Subperfil']['interno'] = 1;
            } else {
                $this->data['Subperfil']['interno'] = 0;
            }

            unset($this->data['Subperfil']['tipo_interno']);
            unset($this->data['Subperfil']['tipo_externo']);

            $this->data['Subperfil']['codigo_usuario_alteracao'] = $this->authUsuario['Usuario']['codigo'];
            $this->data['Subperfil']['data_alteracao'] = date("d/m/Y");

            if ($this->Subperfil->atualizar($this->data)) {

                $acao_tipo = $this->data['acao_tipo'];//Retorna todos os checkebox enviado no post

                //Declaro para começar inserir na tabela SubperfilAcoes
                $this->SubperfilAcoes->query('begin transaction');
                $subperfil_acao_error_count = 0;

                $this->SubperfilAcoes->deleteAll(array('codigo_subperfil' => $codigo));

                foreach ($acao_tipo as $key => $obj) {//Verifica cada valor do checkbox enviado e trata o objeto para pegar os codigo referentes a cada campo da tabela subperfil_acoes

                    if ($obj != '0') {

                        $separa_array = explode(".", $obj);//$obj = null.1.null
                        $codigo_acao = $separa_array[1];

                        $subperfil_acao = array(
                            'codigo_subperfil' => $codigo,
                            'codigo_acao' => $codigo_acao,
                            'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
                            'codigo_usuario_alteracao' => $this->authUsuario['Usuario']['codigo'],
                            'data_alteracao' => date("d/m/Y")
                        );

                        if (!$this->SubperfilAcoes->incluir($subperfil_acao)) {
                            $subperfil_acao_error_count++;
                        }
                    }
                }

                if ($subperfil_acao_error_count > 0) {

                    $this->SubperfilAcoes->rollback();

                } else {
                    $this->Subperfil->commit();
                    $this->SubperfilAcoes->commit();

                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('controller' => 'subperfil', 'action' => 'index'));
                }

            } else {
                $this->SubperfilAcoes->rollback();
                $this->BSession->setFlash('save_error');
            }
        }

        $subperfil = $this->Subperfil->getByCodigo($codigo);

        $this->data = $subperfil;

        if (empty($this->data)) {
            $this->redirect(array('controller' => 'subperfil', 'action' => 'index'));
        }

        $codigo_cliente = $subperfil['Subperfil']['codigo_cliente'];

        $this->set(compact('codigo_cliente'));
    }

    public function editar_status($codigo)
    {
        $this->layout = 'ajax';

        $subperfil = $this->Subperfil->read(null, $codigo);
        $subperfil['Subperfil']['ativo'] = ($subperfil['Subperfil']['ativo'] == 0 ? 1 : 0);

        if ($this->Subperfil->atualizar($subperfil, false)) {
            $this->render(false, false);
            print 1;
        } else {
            $this->render(false, false);
            print 0;
        }

        // 0 -> ERRO | 1 -> SUCESSO
    }

}
