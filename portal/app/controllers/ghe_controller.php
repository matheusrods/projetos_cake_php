<?php

class GheController extends AppController
{
    public $name = 'Ghe';

    public $uses = array(
        'Ghe',
        'RiscosImpactos',
        'Cliente'
    );

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow('*');
    }

    public function index()
    {
        $this->pageTitle = 'Lista de GHE';
        
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Ghe->name);

        $this->data = $filtros;

        $this->dadosCliente();
        
        $this->buscar_unidades();
    }

    public function listagem()
    {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Ghe->name);

        // INICIO - filtrar a lista de ghe por usuário logado
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }
                
        // A função normalizaCodigoCliente() lista todos se usuário logado for interno
        if (!empty($filtros['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->normalizaCodigoCliente($filtros['codigo_cliente']);
        }

        $this->data['Ghe'] = $filtros;
        // FIM - filtrar a lista de ghe por usuário logado

        $this->paginate['Ghe'] = $this->Ghe->getListaGhe($filtros);
        
        $ghes = $this->paginate('Ghe');
        $this->set(compact('ghes'));
    }

    public function trocar_status($codigo)
    {
        $this->layout = 'ajax';

        $ghe = $this->Ghe->read(null, $codigo);
        $ghe['Ghe']['ativo'] = ($ghe['Ghe']['ativo'] == 0 ? 1 : 0);

        if ($this->Ghe->atualizar($ghe, false)) {
            print 1;
            $this->BSession->setFlash('save_success');
        } else {
            print 0;
            $this->BSession->setFlash(array('alert alert-error', 'Não foi possível trocar o status do ghe'));
        }

        $this->render(false, false);
        $this->redirect(array('controller' => 'ghe', 'action' => 'index'));
    }

    public function incluir()
    {
        $this->pageTitle = 'Incluir Novo Ghe';

        if ($this->RequestHandler->isPost()) {
            // debug($this->data); die;
            if ($this->Ghe->cadastrar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'ghe', 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->dadosCliente();
    }

    public function editar($codigo)
    {
        $this->pageTitle = 'Editar Ghe';

        if ($this->RequestHandler->isPut()) {
            if ($this->Ghe->editar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'ghe', 'action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $dados = $this->Ghe->getByCodigo($codigo);

        if (isset($dados['Ghe']) && isset($dados['Ghe']['codigo_cliente'])) {
            try {
                $this->loadModel('GrupoEconomico');

                $matriz = $this->GrupoEconomico->matrizPeloCodigoFilial($dados['Ghe']['codigo_cliente']);

                $dados['Ghe']['codigo_matriz'] = isset($matriz['Cliente']['codigo']) ? $matriz['Cliente']['codigo'] : null;
                $dados['Ghe']['codigo_matriz_name'] = isset($matriz['Cliente']['nome_fantasia']) ? $matriz['Cliente']['nome_fantasia'] : null;
            } catch (\Exception $e) {
                $dados['Ghe']['codigo_matriz'] = null;
                $dados['Ghe']['codigo_matriz_name'] = null;
            }
        } else {
            $dados['Ghe']['codigo_matriz'] = null;
            $dados['Ghe']['codigo_matriz_name'] = null;
        }

        $unidades = (isset($dados['Ghe']['codigo_cliente'])) 
            ? $this->consultar_clientes_codigo_cliente($dados['Ghe']['codigo_cliente'], true) 
            : array();

        $dados['Unidades'] = $unidades;
        
        $this->data = $dados;
        
        if (empty($this->data)) {
            $this->redirect(array('controller' => 'ghe', 'action' => 'index'));
        }

        $this->dadosCliente();
    }

    public function buscar_unidades()
    {
        $filtrosCliente = $this->Filtros->controla_sessao($this->data, "Cliente");
        
        $unidades = array();

        if (isset($filtrosCliente['codigo_cliente'])) {
            if (strripos($filtrosCliente['codigo_cliente'], ",") !== false) {
                $codigo_clientes = explode(",", $filtrosCliente['codigo_cliente']);
    
                $unidades = self::consultar_clientes_codigo_cliente($codigo_clientes, true, true);
            } else {
                $unidades = array("" => "Selecione uma unidade") + self::consultar_clientes_codigo_cliente($filtrosCliente['codigo_cliente'], true);
            }
        } else {
            $usuario = $this->Session->read('Auth.Usuario');
			
            if (isset($usuario['multicliente']) && is_array($usuario['multicliente'])) {
                $codigo_clientes = array_keys($usuario['multicliente']);

                $unidades = self::consultar_clientes_codigo_cliente($codigo_clientes, true, true);
            } else if (isset($usuario['codigo_cliente']) && !empty($usuario['codigo_cliente'])) {
                $unidades = self::consultar_clientes_codigo_cliente((int) $usuario['codigo_cliente'], true, true);
            }
        }

        $this->set(compact('unidades'));
    }

    public function combo_clientes_ajax($codigo_cliente) 
    {
        $this->layout = 'ajax';
        $this->autoRender = false;

        $codigo_clientes = array();
        $clientes = array();

        if (strripos($codigo_cliente, ",") !== false) {
            $codigo_clientes = explode(",", $codigo_cliente);

            $clientes = $this->consultar_clientes_codigo_cliente($codigo_clientes);
        } else {
            $clientes = $this->consultar_clientes_codigo_cliente($codigo_cliente);
        }

        echo json_encode($clientes);
    }

    public function consultar_clientes_codigo_cliente($codigo_cliente, $formatar = false, $adicionar_opcao_todos = false)
    {
        $this->loadModel('GrupoEconomico');
        $this->loadModel('GrupoEconomicoCliente');

        $clientes = array();

        if (!empty($codigo_cliente) && is_array($codigo_cliente)) {
            foreach ($codigo_cliente as $cliente) {
                $clientes = array_merge($clientes, $this->GrupoEconomicoCliente->listaAjax((int) $cliente));
            }

            if ($formatar) {
                $clientes = self::formatar_clientes($clientes, $adicionar_opcao_todos);
            }
        } else if (!empty($codigo_cliente)){
            $codigo_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

            $clientes = $this->GrupoEconomicoCliente->listaAjax($codigo_matriz);

            if ($formatar) {
                $clientes = self::formatar_clientes($clientes, $adicionar_opcao_todos);
            }
        }

        return $clientes;
    }

    public function formatar_clientes($clientes = array(), $adicionar_opcao_todos = false)
    {
        $unidades = array();

        foreach ($clientes as $cliente) {
            $unidades[$cliente['Cliente']['codigo']] = $cliente['Cliente']['nome_fantasia'];
        }
        
        if ($adicionar_opcao_todos) {
            $chaves = array_keys($unidades);

            if (count($chaves) > 0) {
                $chave =  implode(",", $chaves);
    
                $unidades = array($chave => "Todos (" . count($chaves) . ")") + $unidades;
            }
        }
        
        return $unidades;
    }

    public function combo_riscos_impactos()
    {
        $this->autoRender = false;

        $codigo_cliente = isset($this->params['form']['codigo_cliente']) ? $this->params['form']['codigo_cliente'] : null;

        $riscos_impactos = $this->RiscosImpactos->getRiscosImpactosCombo($codigo_cliente);

        $html = '<option value="">Selecione um risco/impacto</option>';

        if (!empty($riscos_impactos)) {
            foreach ($riscos_impactos as $key => $risco_impacto) {
                $total = count($riscos_impactos);

                if ($key == 0) {
                    $html .=
                        "<optgroup label='{$risco_impacto['perigos_aspectos']}'>" .
                            "<option value='{$risco_impacto['codigo_arrtpa_ri']}'>" .
                                $risco_impacto['riscos_impactos'] .
                            "</option>";
                    continue;
                }

                if ($riscos_impactos[$key--]['perigos_aspectos'] == $riscos_impactos[$key]['perigos_aspectos']) {
                    $html .= '<option value="'.$risco_impacto['codigo_arrtpa_ri'].'">' .
                            $risco_impacto['riscos_impactos'] .
                    '</option>';
                } else {
                    $html .=
                        '</optgroup>' .
                        "<optgroup label='{$risco_impacto['perigos_aspectos']}'>" .
                            "<option value='{$risco_impacto['codigo_arrtpa_ri']}'>" .
                                $risco_impacto['riscos_impactos'] .
                            "</option>";
                }

                if (($total - 1) == $key) {
                    $html .= '</optgroup>';
                }
            }
        }

        return $html;
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
