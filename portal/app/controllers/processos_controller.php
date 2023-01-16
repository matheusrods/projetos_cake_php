<?php

class ProcessosController extends AppController
{
    public $name = 'Processos';

    public $uses = array(
        'Processo',
        'ProcessoTipo',
        'ProcessoAnexo',
        'ProcessoFerramenta',
        'Cliente'
    );

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow();
    }

    public function index()
    {
        $this->pageTitle = 'Lista de Processos';
        
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Processo->name);
        $this->data = $filtros;
        
        $this->comboProcessoTipo();
        $this->dadosCliente();
    }

    public function comboProcessoTipo($data = null)
    {
        $this->loadModel('ProcessoTipo');

        $combo_processo_tipo = $this->ProcessoTipo->retorna_tipo();
        $this->set(compact('combo_processo_tipo'));
    }

    public function listagem()
    {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Processo->name);
        
        // INICIO - filtrar a lista de processos por usuário logado
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }
                
        // A função normalizaCodigoCliente() lista todos se usuário logado for interno
        if (!empty($filtros['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->normalizaCodigoCliente($filtros['codigo_cliente']);
        }

        $this->data['Processo'] = $filtros;
        // FIM - filtrar a lista de processos por usuário logado

        $this->paginate['Processo'] = $this->Processo->getListaProcessos($filtros);
        
        $processos = $this->paginate('Processo');
        
        $novoProcessos = array();
        foreach ($processos as $key => $processo) {
            $anexos = $this->ProcessoAnexo->getLista($processo['Processo']['codigo']);
            if (!empty($anexos)) {
                $processo['Processo']['ProcessoAnexos'] = $anexos;
            }

            array_push($novoProcessos, $processo);
        }

        $processos = $novoProcessos;
        
        $this->set(compact('processos'));
    }

    public function modal_processos()
    {
        $this->autoRender = false;

        $processoFerramentas = $this->ProcessoFerramenta->getLista($this->params['url']['codigo']);
        if (!empty($processoFerramentas)) {
            return json_encode($processoFerramentas);
        }
        
        return json_encode(array());
    }

    public function modal_anexos()
    {
        $this->autoRender = false;

        $anexos = $this->ProcessoAnexo->getLista($this->params['url']['codigo_processo']);
        if (!empty($anexos)) {
            return json_encode($anexos);
        }
        
        return json_encode(array());
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
