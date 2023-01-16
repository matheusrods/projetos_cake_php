<?php

class RiscosImpactosController extends AppController
{
    public $name = 'RiscosImpactos';

    public $uses = array(
        'RiscosTipo',
        'PerigosAspectos',
        'PerigosAspectosTipo',
        'RiscosImpactos',
        'RiscosImpactosTipo',
        'Cliente',
        'RiscosEsocial',
        'Risco'
    );

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow('obtem_perigos_aspectos_por_ajax','relacionar_risco_impacto_esocial');
    }

    public function index()
    {
        $this->pageTitle = 'Lista de Riscos Impactos';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->PerigosAspectos->name);
        $this->data = $filtros;

        $this->comboRiscosTipo();
        $this->comboPerigosAspectos();
        $this->comboMetodosTipo();
        $this->comboRiscosImpactosTipo();
    }

    public function listagem()
    {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->RiscosImpactos->name);

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

        $this->data['RiscosImpactos'] = $filtros;
        // FIM - filtrar por usuário logado

        $this->paginate['RiscosImpactos'] = $this->RiscosImpactos->getListaRiscosImpactos($filtros);

        $riscos_impactos = $this->paginate('RiscosImpactos');
        $this->set(compact('riscos_impactos'));
    }

    public function incluir()
    {
        $this->pageTitle = 'Incluir Novo Risco ou Impacto';

        if($this->RequestHandler->isPost()) {
            $this->data['RiscosImpactos']['ativo'] = 1; //Adiciona codigo ativo na criação

            if ($this->RiscosImpactos->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'riscos_impactos', 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->comboPerigosAspectos();
        $this->comboMetodosTipo();
        $this->comboRiscosImpactosTipo();

        $adicionar_novo = true;
        $this->set(compact('adicionar_novo'));

    }

    public function editar($codigo)
    {
        $this->pageTitle = 'Editar Risco ou Impacto';

        if ($this->RequestHandler->isPut()) {

            if ($this->RiscosImpactos->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'riscos_impactos', 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->data = $this->RiscosImpactos->getByCodigo($codigo);

        if(empty($this->data)){
            $this->redirect(array('controller' => 'riscos_impactos', 'action' => 'index'));
        }

        if (!empty($this->data['RiscosImpactos']['codigo_cliente'])) {
            $codigo_cliente = $this->data['RiscosImpactos']['codigo_cliente'];
        } else {
            $codigo_cliente = null;
        }

        $this->comboPerigosAspectos();
        $this->comboMetodosTipo();
        $this->comboRiscosImpactosTipo();

        $this->listagem_essocial();

        $codigo_risco = $this->data['RiscosImpactos']['codigo_risco'];
        $this->data['Cliente']['codigo_cliente'] = $this->data['RiscosImpactos']['codigo_cliente'];
        
        $riscosesocial = array();

        //Se o risco_impacto tiver um risco_esocial vinculado, retornar ele na listagem
        if ($codigo_risco != null) {

            $riscosesocial = $this->Risco->getByCodigoRiscosImpactos($codigo);
        }

        $this->set(compact('codigo', 'codigo_risco', 'riscosesocial'));
    }

    public function comboPerigosAspectos($data = null)
    {
        $this->loadModel('PerigosAspectos');

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

        $combo_perigos_aspectos = $this->PerigosAspectos->retornaPerigosAspectos($data);

        $this->set(compact('combo_perigos_aspectos'));
        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }

    public function comboMetodosTipo($data = null)
    {
        $this->loadModel('MetodosTipo');

        $combo_metodos_tipo = $this->MetodosTipo->retornaMetodosTipo();
        $this->set(compact('combo_metodos_tipo'));
    }

    function editar_status($codigo, $codigo_perigo_aspecto, $descricao, $status, $codigo_cliente, $codigo_metodo_tipo, $codigo_risco_impacto_tipo){
        $this->layout = 'ajax';

        $this->loadModel('RiscosImpactos');

        if(!is_numeric($status)){
            print 10;
            exit;
        }
        $codigo = trim($codigo);
        $status= ($status == 0) ? $status = 1 : $status = 0;

        $this->data['RiscosImpactos']['codigo'] = $codigo;
        $this->data['RiscosImpactos']['ativo'] = $status;
        $this->data['RiscosImpactos']['descricao'] = $descricao;
        $this->data['RiscosImpactos']['codigo_perigo_aspecto'] = $codigo_perigo_aspecto;
        $this->data['RiscosImpactos']['codigo_cliente'] = $codigo_cliente;
        $this->data['RiscosImpactos']['codigo_metodo_tipo'] = $codigo_metodo_tipo;
        $this->data['RiscosImpactos']['codigo_risco_impacto_tipo'] = $codigo_risco_impacto_tipo;

        if ($this->RiscosImpactos->atualizar($this->data)) {
            $this->render(false,false);
            print 1;
        } else {
            $this->render(false,false);
            print 0;
        }

        // 0 -> ERRO | 1 -> SUCESSO
    }

    public function obtem_perigos_aspectos_por_ajax()
    {
        $this->autoRender = false;

        // if($bloqueado) {
        $this->loadModel('PerigosAspectos');
        $perigos_aspectos = $this->PerigosAspectos->find('list', array(
                'recursive' => -1,
                'fields' => array(
                    'PerigosAspectos.codigo',
                    'PerigosAspectos.descricao'
                ),
                'conditions' => array(
                    'PerigosAspectos.ativo' => 1,
                    'PerigosAspectos.codigo_cliente' => $this->params['form']['codigo_cliente']
                ),
                'order' => 'PerigosAspectos.descricao'
            )
        );

        $html = '<option value="">Selecione</option>';
        if(!empty($perigos_aspectos)) {
            foreach ($perigos_aspectos as $key => $pa) {
                $html .= '<option value="'.$key.'">'.$pa.'</option>';
            }
        }
        unset($perigos_aspectos);
        return $html;
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

    public function comboRiscosImpactosTipo()
    {
        $this->loadModel('RiscosImpactosTipo');

        $combo_riscos_impactos_tipo = $this->RiscosImpactosTipo->retornaRiscosImpactosTipo();
        $this->set(compact('combo_riscos_impactos_tipo'));
    }

    public function listagem_essocial($filtros = null)
    {

        $this->paginate['Risco'] = $this->Risco->getListaRiscos($filtros);

        $riscos_esocial = $this->paginate('Risco');
        $this->set(compact('riscos_esocial'));
    }

    public function relacionar_risco_impacto_esocial(){
        $this->layout = 'ajax';

        $codigo_risco_impacto = $this->data['RiscosImpactos']['codigo_risco_impacto'];

        $codigo_risco = $this->data['RiscosEsocial']['codigo_risco'];
        $RiscosImpactos = $this->RiscosImpactos->find('first', array('conditions' => array(
            'RiscosImpactos.codigo' => $codigo_risco_impacto
        )));

        $RiscosImpactos['RiscosImpactos']['codigo_risco'] = $codigo_risco;

        if ($this->RiscosImpactos->atualizar($RiscosImpactos)) {

            $riscosesocial = $this->Risco->getByCodigoRiscosImpactos($codigo_risco_impacto);

            $result = array(
                'result' => 1,
                'riscos' => $riscosesocial
            );
            echo json_encode($result);
        } else {
            $result = array(
                'result' => 2,
                'riscos' => array()
            );
            echo json_encode($result);
        }
    }
}
