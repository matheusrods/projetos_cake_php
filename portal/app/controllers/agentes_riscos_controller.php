<?php

class AgentesRiscosController extends AppController
{
    public $name = 'AgentesRiscos';

    public $uses = array(
        'RiscosTipo',
        'AgentesRiscosClientes',
        'ArrtpaRi',
        'MedidasControleAnexos',
        'Cliente'
    );

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow();
    }

    public function index()
    {
        $this->pageTitle = 'Lista de Agentes de Risco';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->AgentesRiscosClientes->name);
        $this->data = $filtros;

        $this->dadosCliente();
    }

    public function listagem()
    {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->AgentesRiscosClientes->name);

        // INICIO - filtrar por usuário logado
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        // FIM - filtrar por usuário logado

        $this->paginate['AgentesRiscosClientes'] = $this->AgentesRiscosClientes->getListaAgentesRiscosCliente($filtros);

        $agentes_riscos = $this->paginate('AgentesRiscosClientes');
        $this->set(compact('agentes_riscos'));
    }

    public function incluir()
    {
        $this->pageTitle = 'Incluir Novo Tipo de Risco';

        if($this->RequestHandler->isPost()) {
            if ($this->RiscosTipo->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'agentes_riscos', 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
    }

    public function editar($codigo)
    {
        $this->pageTitle = 'Editar Tipo de Risco';

        if ($this->RequestHandler->isPut()) {

            if ($this->RiscosTipo->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'agentes_riscos', 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->data = $this->RiscosTipo->getByCodigo($codigo);

        if(empty($this->data)){
            $this->redirect(array('controller' => 'agentes_riscos', 'action' => 'index'));
        }
    }

    function editar_status($codigo, $descricao, $cor, $icone, $status)
    {
        $this->layout = 'ajax';

        $this->loadModel('RiscosTipo');

        $codigo = trim($codigo);
        $status= ($status == 0) ? $status = 1 : $status = 0;

        $this->data['RiscosTipo']['codigo'] = $codigo;
        $this->data['RiscosTipo']['ativo'] = $status;
        $this->data['RiscosTipo']['descricao'] = $descricao;
        $this->data['RiscosTipo']['cor'] = $cor;
        $this->data['RiscosTipo']['icone'] = $icone;

        if ($this->RiscosTipo->atualizar($this->data)) {
            $this->render(false,false);
            print 1;
        } else {
            $this->render(false,false);
            print 0;
        }

        // 0 -> ERRO | 1 -> SUCESSO
    }

    function carregar_agente_risco($codigo_arrtpa_ri)
    {
        $this->layout = 'ajax';

        $agentes_riscos = $this->ArrtpaRi->getDadosAgentesRiscos($codigo_arrtpa_ri);

        foreach ($agentes_riscos as $key => $ag) {

            $agentes_riscos[$key]['MedidasControle']['MedidasControleAnexos'] = array();
            $medidasControleAnexos = $this->MedidasControleAnexos->getMedidasControleAnexos($ag['MedidasControle']['codigo']);

            $limpa_dados = array();

            if (!empty($medidasControleAnexos)) {
                foreach ($medidasControleAnexos as $mca) {
                    $limpa_dados[] = $mca['MedidasControleAnexos'];
                }
            }

            $agentes_riscos[$key]['MedidasControle']['MedidasControleAnexos'] = $limpa_dados;
        }

        $this->render(false,false);
        echo json_encode($agentes_riscos);
    }

    public function dadosCliente() {
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
}
