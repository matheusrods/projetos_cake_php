<?php
class PosObsLocalController extends AppController
{
    public $name = 'PosObsLocal';
    var    $uses = array('PosObsLocal', 'Cliente');

    /**
     * beforeFilter callback
     * @todo retirar permissão para action: editar_externo
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow(array('listagem_clientes', 'index_locais'));
    }

    private function carregaDados($codigo_cliente = null)
    {
        $nome_empresa = null;
        $is_admin     = null;
        $usuarioComum = ($this->authUsuario['Usuario']['codigo_uperfil'] != 1);

        $is_admin     = $usuarioComum ? 0 : 1;

        if (empty($codigo_cliente)) {
            $codigo_cliente = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $cliente = $this->Cliente->find('first', array('fields' => array('nome_fantasia'), 'conditions' => array('codigo' => $codigo_cliente)));

        if (isset($cliente['Cliente']['nome_fantasia'])) {
            $nome_empresa = $cliente['Cliente']['nome_fantasia'];
        }

        return compact('codigo_cliente', 'nome_empresa', 'is_admin');
    }

    public function index($codigo_cliente = null)
    {
        $multicliente = isset($authUsuario['Usuario']['multicliente']) ? $authUsuario['Usuario']['multicliente'] : null;

        if ($multicliente) {
            if (empty($codigo_cliente)) {
                if (is_array($this->authUsuario['Usuario']['codigo_cliente'])) {
                    $codigo_cliente = implode(',', $this->authUsuario['Usuario']['codigo_cliente']);
                }
                $codigo_cliente = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        $this->pageTitle = 'Local da Observação';

        $this->set($this->carregaDados($codigo_cliente));
    }

    public function index_locais($codigo_cliente = null)
    {
        $this->pageTitle = 'Local da Observação';

        $this->set($this->carregaDados($codigo_cliente));
    }

    public function listagem($codigo_cliente = null)
    {
        $this->layout = 'ajax';
        $filtros      = $this->Filtros->controla_sessao($this->data, $this->PosObsLocal->name);

        if (empty($codigo_cliente)) {
            $codigo_cliente = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        //$locais = $this->PosObsLocal->obterLocaisDeObservacaoPeloCliente($codigo_cliente, $filtros);
        $this->paginate['PosObsLocal'] = $this->PosObsLocal->obterPaginacao($codigo_cliente, $filtros);
        $locais = $this->paginate('PosObsLocal');

        $this->set($this->carregaDados($codigo_cliente));
        $this->set(compact('locais'));
    }

    public function listagem_clientes($codigo_cliente = null)
    {
        $this->layout = 'ajax';
        $filtros      = $this->Filtros->controla_sessao($this->data, $this->PosObsLocal->name);

        if (isset($filtros['codigo_cliente'])) {
            $codigo_cliente = $filtros['codigo_cliente'];
        }
        if (empty($codigo_cliente)) {
            $codigo_cliente = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $assinaturas = $this->Cliente->getAssinaturaPDASWTOBS($codigo_cliente, 'OBSERVADOR_EHS');
        $clientes = array();

        if (!empty($assinaturas)) {

            if (is_array($assinaturas)) {
                $assinaturas = implode(",", $assinaturas);
            }

            $filtros['codigo_cliente'] = $assinaturas;

            $clientes = $this->Cliente->find('all', array(
                'conditions' => array(
                    "codigo IN ({$assinaturas})"
                )
            ));
        }

        $this->set(compact('clientes'));
    }

    public function incluir($codigo_cliente = null)
    {
        if (empty($codigo_cliente)) {
            $this->BSession->setFlash('save_error');
            $this->redirect($this->referer());
        }

        $this->pageTitle = 'Incluir Local';

        if ($this->RequestHandler->isPost()) {
            if (empty($this->data['PosObsLocal']['descricao'])) {
                $this->BSession->setFlash('save_error');
                $this->redirect($this->referer());
            }

            $codigo_usuario = $this->authUsuario['Usuario']['codigo'];
            $codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];

            $this->data['PosObsLocal']['codigo_cliente']          = $codigo_cliente;
            $this->data['PosObsLocal']['codigo_empresa']          = $codigo_empresa;
            $this->data['PosObsLocal']['codigo_usuario_inclusao'] = $codigo_usuario;
            $this->data['PosObsLocal']['ativo']                   = 1;

            if ($this->PosObsLocal->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'pos_obs_local', 'action' => 'index_locais', $codigo_cliente));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->set($this->carregaDados($codigo_cliente));
    }

    public function editar($codigo_cliente = null, $codigo_local = null)
    {
        $this->pageTitle = 'Editar Local';

        if ($this->RequestHandler->isPost()) {

            if ($this->PosObsLocal->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(
                    array(
                        'controller' => 'pos_obs_local',
                        'action'     => 'index_locais',
                        $codigo_cliente
                    )
                );
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        if (isset($codigo_local)) {
            $local = $this->PosObsLocal->find(
                'first',
                array('conditions' => array('codigo' => $codigo_local))
            );

            $this->data = $local;
        }

        $this->set($this->carregaDados($codigo_cliente));
    }

    public function troca_status($codigo, $status)
    {
        $this->layout = 'ajax';

        $this->data['PosObsLocal']['codigo'] = $codigo;
        $this->data['PosObsLocal']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->PosObsLocal->atualizar($this->data, false)) {
            print 1;
        } else {
            print 0;
        }
        $this->render(false, false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }
}
