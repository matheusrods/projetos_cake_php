<?php
class CargosController extends AppController
{
    public $name = 'Cargos';
    var $uses = array('Cargo',
        'Cbo',
        'Cliente',
        'Gfip',
        'Corretora',
        'Gestor',
        'EnderecoRegiao',
        'PlanoDeSaude',
        'AtribuicaoCargo',
        'CargoAtribuicaoCargo',
        'CargoExterno'
    );

    /**
     * beforeFilter callback
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow('obtem_cargos_por_ajax', 'por_cliente', 'modal_detalhes');
    }

    public function obtem_cargos_por_ajax()
    {
        $this->autoRender = false;

        ############################COMENTADO PARA SEMPRE TRAZER OS DADOS FILTRADOS 14/09/2020#########################

        // $this->loadModel('GrupoEconomicoCliente');
        // $bloqueado = $this->GrupoEconomicoCliente->find('count', array(
        //     'recursive' => -1,
        //     'conditions' => array(
        //         'codigo_cliente' => $this->params['form']['codigo_cliente'], 
        //         'bloqueado' => 1
        //         )
        //     )
        // );
        $cargos = array();
        // if($bloqueado) {
        $this->loadModel('ClienteSetorCargo');
        $cargos = $this->ClienteSetorCargo->find('list', array(
            'recursive' => -1,
            'joins' => array(
                    array(
                    'table' => 'cargos',
                    'alias' => 'Cargo',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Cargo.codigo = ClienteSetorCargo.codigo_cargo'
                    )
                )
            ),
            'fields' => array(
                'Cargo.codigo',
                'Cargo.descricao'
            ),
            'conditions' => array(
                'Cargo.ativo' => 1,
                'ClienteSetorCargo.codigo_cliente_alocacao' => $this->params['form']['codigo_cliente'],
                'ClienteSetorCargo.codigo_setor' => $this->params['form']['codigo_setor'],
                '(ClienteSetorCargo.ativo = 1 OR ClienteSetorCargo.ativo IS NULL)',
            ),
            'order' => 'Cargo.descricao'
        )
        );
        // } else {
        //     //recupera o código da matriz
        //     $matriz = $this->GrupoEconomicoCliente->retorna_dados_cliente($this->params['form']['codigo_cliente']);

        //     $this->loadModel('Cargo');
        //     $cargos = $this->Cargo->find('list', array(
        //         'recursive' => -1,
        //         'conditions' => array(
        //             'Cargo.codigo_cliente' => $matriz['Matriz']['codigo'],
        //             'Cargo.ativo' => 1
        //             ),
        //         'fields' => array(
        //             'Cargo.codigo',
        //             'Cargo.descricao'
        //             ),
        //         'order' => 'Cargo.descricao'      
        //         )
        //     );
        // }
        $html = '<option value="">Selecione um cargo</option>';
        if (!empty($cargos)) {
            foreach ($cargos as $key => $cargo) {
                $html .= '<option value="' . $key . '">' . $cargo . '</option>';
            }
        }
        unset($cargos);
        return $html;
    }


    function index($codigo_cliente, $referencia, $terceiros_implantacao = 'interno')
    {

        $this->pageTitle = 'Cargos';

        $this->retorna_dados_cliente($codigo_cliente);

        $this->set(compact('referencia', 'terceiros_implantacao'));
    }

    function retorna_dados_cliente($codigo_cliente)
    {

        $this->data = $this->Cliente->find('first', array('conditions' => array('codigo' => $this->normalizaCodigoCliente($codigo_cliente))));

        $this->set(compact('codigo_cliente'));
    }

    function listagem($codigo_cliente, $referencia, $terceiros_implantacao = 'interno')
    {

        $this->layout = 'ajax';

        $this->retorna_dados_cliente($codigo_cliente);

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Cargo->name);

        $conditions = $this->Cargo->converteFiltroEmCondition($filtros);

        $this->loadModel('GrupoEconomico');
        //monta os joins
        $join_ge = array(
                array(
                'table' => 'RHHealth.dbo.grupos_economicos_clientes',
                'alias' => 'GrupoEconomicoCliente',
                'type' => 'INNER',
                'conditions' => array(
                    'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
                )
            )
        );
        //pega o codigo da matriz
        $codigo_cliente = $this->normalizaCodigoCliente($codigo_cliente);
        $cliente_unidade = $this->GrupoEconomico->find('all', array('joins' => $join_ge, 'conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));

        $matriz_codigo_cliente = array();
        foreach ($cliente_unidade as $key => $value) {
            if (isset($value['GrupoEconomico']['codigo_cliente'])) {
                $matriz_codigo_cliente[] = $value['GrupoEconomico']['codigo_cliente'];
            }
        }

        $conditions = array_merge($conditions, array('Cargo.codigo_cliente' => $matriz_codigo_cliente));

        $order = 'Cargo.descricao';

        $this->Cargo->bindModel(
            array(
            'belongsTo' => array(
                'Cbo' => array(
                    'foreignKey' => false,
                    'conditions' => array('Cbo.codigo_cbo = Cargo.codigo_cbo')
                ),
            )
        ), false
        );

        $this->paginate['Cargo'] = array(
            'conditions' => $conditions,
            'limit' => 50,
            'order' => $order,
        );

        // print $this->Cargo->find('sql',$this->paginate['Cargo']);exit;

        $cargos = $this->paginate('Cargo');
        $this->set(compact('cargos', 'referencia', 'terceiros_implantacao'));
    }

    function incluir($codigo_cliente, $referencia, $terceiros_implantacao = 'interno')
    {
        $this->pageTitle = 'Incluir Cargo';
        $this->carrega_combos();

        if ($this->RequestHandler->isPost()) {

            $this->data['Cargo']['codigo_cbo'] = COMUM::soNumero($this->data['Cargo']['codigo_cbo']);

            if ($this->Cargo->incluir($this->data)) {

                //insere os dados na cargoa_atribuicao_cargo
                if (isset($this->data['atribuicoes_cargos'])) {
                    //seta o novo codigo do cargo 
                    $this->data['Cargo']['codigo'] = $this->Cargo->id;
                    //metodo para gravar os dados de atribuicoes do cargo
                    $this->setCargoAtribuicaoCargo($this->data);
                } //fim atribuicoes cargos

                $this->BSession->setFlash('save_success');

                if ($terceiros_implantacao == 'terceiros_implantacao') {
                    $this->redirect(array('controller' => 'cargos', 'action' => 'index', $this->data['Cargo']['codigo_cliente'], $referencia, $terceiros_implantacao));
                }
                else {
                    $this->redirect(array('controller' => 'cargos', 'action' => 'index', $this->data['Cargo']['codigo_cliente'], $referencia));
                }
            }
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->retorna_dados_cliente($codigo_cliente);
        $gfip = $this->Gfip->find('list', array('fields' => array('codigo', 'descricao_gfip')));

        $atribuicoes_cargos = $this->AtribuicaoCargo->find('list', array('fields' => array('codigo', 'descricao'), 'order' => array('descricao')));

        $cargos_similares = $this->Cargo->find('list', array('fields' => array('codigo', 'descricao'), 'conditions' => array('codigo_cliente' => $codigo_cliente)));

        $this->set(compact('referencia', 'gfip', 'atribuicoes_cargos', 'cargos_similares', 'terceiros_implantacao'));
    }

    /**
     * [editar description]
     * 
     * metodo para editar os cargos do sistema
     * 
     * @param  [type] $codigo_cliente [description]
     * @param  [type] $codigo_cargo   [description]
     * @param  [type] $referencia     [description]
     * @return [type]                 [description]
     */
    public function editar($codigo_cliente, $codigo_cargo, $referencia, $terceiros_implantacao = 'interno')
    {
        $this->pageTitle = 'Editar Cargo';
        $this->carrega_combos();

        if ($this->RequestHandler->isPost()) {

            $this->data['Cargo']['codigo_cbo'] = COMUM::soNumero($this->data['Cargo']['codigo_cbo']);

            if ($this->Cargo->atualizar($this->data)) {

                //insere os dados na cargoa_atribuicao_cargo
                if (isset($this->data['atribuicoes_cargos'])) {
                    $this->setCargoAtribuicaoCargo($this->data);
                } //fim atribuicoes cargos

                $this->BSession->setFlash('save_success');
                if ($terceiros_implantacao == 'terceiros_implantacao') {
                    $this->redirect(array('controller' => 'cargos', 'action' => 'index', $this->data['Cargo']['codigo_cliente'], $referencia, $terceiros_implantacao));
                }
                else {
                    $this->redirect(array('controller' => 'cargos', 'action' => 'index', $this->data['Cargo']['codigo_cliente'], $referencia));
                }
            }
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->retorna_dados_cliente($codigo_cliente);

        if (isset($this->passedArgs[1])) {
            $cargos = $this->Cargo->find('first', array('conditions' => array('codigo' => $this->passedArgs[1])));
            $this->data = array_merge($this->data, $cargos);

            $gfip = $this->Gfip->find('list', array('fields' => array('codigo', 'descricao_gfip')));
        }

        //pega os atributos do cargo
        $cargo_atribuicoes_cargos = $this->CargoAtribuicaoCargo->find('list', array('fields' => array('codigo_atribuicao_cargo', 'codigo'), 'conditions' => array('codigo_cargo' => $codigo_cargo)));

        //para montar a lista de cargos
        $atribuicoes_cargos = $this->AtribuicaoCargo->find('list', array('fields' => array('codigo', 'descricao'), 'order' => array('descricao')));

        //cargos similares
        $cargos_similares = $this->Cargo->find('list', array('fields' => array('codigo', 'descricao'), 'conditions' => array('codigo_cliente' => $codigo_cliente)));

        $this->set(compact('referencia', 'gfip', 'atribuicoes_cargos', 'cargo_atribuicoes_cargos', 'cargos_similares', 'terceiros_implantacao'));

    } //fim editar

    function atualiza_status($codigo, $status)
    {
        $this->layout = 'ajax';

        $cargo = $this->Cargo->read(null, $codigo);
        $cargo['Cargo']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->Cargo->atualizar($cargo, false)) {
            print 1;
        }
        else {
            print 0;
        }
        $this->render(false, false);
    // 0 -> ERRO | 1 -> SUCESSO
    }

    function busca_descricao_atividades($codigo)
    {
        $this->layout = 'ajax';
        $this->render(false, false);

        $descricao_cargo = $this->Cargo->find("first", array('conditions' => array('codigo' => $codigo), 'fields' => array('descricao_cargo')));

        echo json_encode($descricao_cargo);
    }

    function carrega_combos()
    {
        $this->loadModel('Funcao');
        $conditions = array('ativo' => 1);
        $fields = array('codigo', 'descricao');
        $order = 'descricao';

        $funcoes = $this->Funcao->find('list', array('conditions' => $conditions, 'order' => $order, 'fields' => $fields));

        $this->set(compact('funcoes'));
    }

    // public function por_cliente($codigo_cliente) {
    //     $list = $this->Cargo->lista($codigo_cliente);
    //     $result = array();
    //     foreach ($list as $key => $value) {
    //         $result[] = array('codigo' => $key, 'descricao' => $value);
    //     }
    //     echo json_encode($result);
    //     die();
    // }

    /**
     * Obter lista de Cargos por código(s) de cliente(s)
     *
     * @param [array] $codigo_cliente
     * @return array
     * @todo implementar token
     */
    public function por_cliente($codigo_cliente = null)
    {

        if (is_null($codigo_cliente)) {
            $this->responseJson();
        }

        $codigo_cliente = $this->normalizaCodigoCliente($codigo_cliente); // normaliza codigo

        $dados = $this->Cargo->obterLista($codigo_cliente);

        $this->responseJson($dados);

    } //FINAL FUNCTION por_cliente    

    /**
     * [cliente_terceiros METODO PARA MONTAR O FILTRO DOS CLIENTES TERCEIROS ESTE FILTRO É SOMENTE PARA AS UNIDADES DELE.]
     * 
     * @param  [type] $codigo_cliente [CODIGO DO CLIENTE DO GRUPO ECONOMICO]
     * @return [type]                 [description]
     */
    public function cargo_terceiros($codigo_cliente = null)
    {

        //verifica se o usuario logado é de cliente
        if ($this->BAuth->user('codigo_cliente')) {
            $codigo_cliente = $this->BAuth->user('codigo_cliente');
        }

        //redireciona para os clientes da unidade.
        if (!empty($codigo_cliente)) {
            $codigo_cliente = is_array($codigo_cliente) ? implode(',', $codigo_cliente) : $codigo_cliente;
            $this->redirect(array('controller' => 'cargos', 'action' => 'index', $codigo_cliente, 'cargo_implantacao_terceiros'));
        }

        $this->pageTitle = 'Cargos por Cliente';
        $this->carrega_combos_filtro_cliente();
        $this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

    } //fim cliente_terceiros

    /**
     * [carrega_combos description]
     * 
     * metodo para carregar os combos dos filtros de cliente
     * 
     * @param  boolean $listar_npe_nome [description]
     * @return [type]                   [description]
     */
    private function carrega_combos_filtro_cliente($listar_npe_nome = false)
    {
        $this->loadModel('MotivoBloqueio');
        $corretoras = $this->Corretora->find('list', array('order' => 'nome'));
        $gestores = $this->Gestor->listarNomesGestoresAtivos();
        $filiais = $this->EnderecoRegiao->listarRegioes();
        $somente_buonnysay = array(1 => 'Cliente BuonnySat', 2 => 'Todos');
        $motivos = $this->MotivoBloqueio->find('list', array('conditions' => array('codigo' => array(1, 8, 17)), 'order' => 'descricao DESC'));
        $ativo = 'Ativos';
        $plano_saude = $this->PlanoDeSaude->listarPlanosAtivos();

        $this->set(compact('clientes_tipos', 'clientes_sub_tipos', 'corretoras', 'seguradoras', 'gestores', 'ativo', 'filiais', 'somente_buonnysay', 'motivos', 'plano_saude'));

    } //FINAL FUNCTION carrega_combos


    /**
     * [setCargoAtribuicaoCargo description]
     * 
     * metodo para inserir ou deletar o relacionamento do cargo com as atribuicoes_cargos
     * 
     * @param [type] $dados [description]
     */
    public function setCargoAtribuicaoCargo($dados)
    {

        //codigos da atribuicao
        $codigo_cargo = $dados['Cargo']['codigo'];

        //pega os dados para não deletar ou inserir novamente
        $dadosCargosAtribuicaoCargo = $this->CargoAtribuicaoCargo->find('list', array('fields' => array('codigo_atribuicao_cargo', 'codigo'), 'conditions' => array('codigo_cargo' => $codigo_cargo)));

        //variavel auxiliar para controle do retorno
        $tipoReturn = true;

        //verifica se existem dados na tabela
        if (!empty($dadosCargosAtribuicaoCargo)) {

            //seta os valores relacionados
            $deletes = $dadosCargosAtribuicaoCargo;

            //varre os dados dos cargos da atribuicao
            foreach ($dados['atribuicoes_cargos'] as $cac => $val) {

                if (isset($dadosCargosAtribuicaoCargo[$cac])) {
                    //retira da variavel os indices que estao selecionados
                    unset($deletes[$cac]);
                }
                //se nao existir na base insere
                else if (!isset($dadosCargosAtribuicaoCargo[$cac])) {
                    //montao array para inserir os dados selecionados
                    $dadoCAC['CargoAtribuicaoCargo']['codigo_cargo'] = $codigo_cargo;
                    $dadoCAC['CargoAtribuicaoCargo']['codigo_atribuicao_cargo'] = $cac;

                    //verifica se inseriu na tabela de relacionamento
                    if (!$this->CargoAtribuicaoCargo->incluir($dadoCAC)) {
                        $tipoReturn = false;
                    }
                } //fim isset
            } //fim foreach $dadoCargosAtribuicaoCargo

            //monta o array para deletar
            if (!empty($deletes)) {
                // $array_del = implode(',',$deletes);
                $this->CargoAtribuicaoCargo->delete($deletes);

            } //fim deletes


        } //fim if
        else {
            //varre os dados do input
            foreach ($dados['atribuicoes_cargos'] as $cac) {
                //montao array para inserir os dados selecionados
                $dadoCAC['CargoAtribuicaoCargo']['codigo_cargo'] = $codigo_cargo;
                $dadoCAC['CargoAtribuicaoCargo']['codigo_atribuicao_cargo'] = $cac;

                //verifica se inseriu na tabela de relacionamento
                if (!$this->CargoAtribuicaoCargo->incluir($dadoCAC)) {
                    $tipoReturn = false;
                }
            } //fim foreach dos dados selecionados

        } //fim if/else empty dados cargos_atribuicao_cargo

        return $tipoReturn;

    } //fim setCargoAtribuicaoCargo

    /**
     * [detalhes description]
     * 
     * para montar a modal de detalhes do cargo
     * 
     * @param  [type] $codigo_cargo [description]
     * @return [type]               [description]
     */
    public function modal_detalhes($codigo_cargo)
    {

        //pega os dados do cargo
        $cargo = $this->Cargo->find('first', array('conditions' => array('codigo' => $codigo_cargo)));

        //pega os atributos do cargo
        $cargo_atribuicoes_cargos = $this->CargoAtribuicaoCargo->find('list', array('fields' => array('codigo_atribuicao_cargo', 'codigo'), 'conditions' => array('codigo_cargo' => $codigo_cargo)));

        //para montar a lista de cargos
        $atribuicoes_cargos = $this->AtribuicaoCargo->find('list', array('fields' => array('codigo', 'descricao'), 'order' => array('descricao')));

        //cargos similares
        $cargos_similares = $this->Cargo->find('first', array('fields' => array('codigo', 'descricao'), 'conditions' => array('codigo' => $cargo['Cargo']['codigo_cargo_similar'])));

        $this->set(compact('cargo', 'atribuicoes_cargos', 'cargo_atribuicoes_cargos', 'cargos_similares'));
    } //fim detalhes

    function index_externo()
    {
        $this->pageTitle = 'Cargos Externos';
        $this->data[$this->CargoExterno->name] = $this->Filtros->controla_sessao($this->data, $this->CargoExterno->name);
    }

    function listagem_externo()
    {

        $this->layout = 'ajax';
        $cargos = array();
        $listagem = false;

        $filtros = $this->Filtros->controla_sessao($this->data, $this->CargoExterno->name);

        $this->loadModel('GrupoEconomico');
        $codigo_cliente_filial = $filtros['codigo_cliente'];
        $codigo_cliente_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente_filial);

        if (!empty($filtros['codigo_cliente'])) {


            $conditions = $this->CargoExterno->converteFiltroEmCondition($filtros);

            $fields = array('Cargo.codigo', 'CargoExterno.codigo', 'Cargo.descricao', 'Cargo.ativo', 'CargoExterno.codigo_externo');
            $order = 'Cargo.descricao';

            $this->Cargo->bindModel(
                array('hasOne' => array(
                    'CargoExterno' => array(
                        'foreignKey' => 'codigo_cargo',
                        'conditions' => array('CargoExterno.codigo_cliente' => $codigo_cliente_matriz /*$filtros['codigo_cliente']*/)
                    ))), false
            );

            $this->paginate['Cargo'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
            );

            $cargos = $this->paginate('Cargo');
            $listagem = true;
        }

        $this->set(compact('cargos', 'listagem'));
        $this->set('codigo_cliente_filtro', $codigo_cliente_matriz /*$filtros['codigo_cliente']*/);
    }

    function editar_externo()
    {

        $this->pageTitle = 'Cargos Externos';

        $codigoCargo = $this->RequestHandler->params['pass'][1];

        if (isset($this->RequestHandler->params['pass'][2])) {
            $codigoCargoExterno = $this->RequestHandler->params['pass'][2];
        }

        $dadosCargo = $this->Cargo->carregar($codigoCargo);

        if ($this->RequestHandler->isPost()) {
            if ($this->CargoExterno->save($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index_externo', 'controller' => 'cargos'));
            }
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        if (isset($this->passedArgs[2])) {
            $this->data = $this->CargoExterno->find('first', array('conditions' => array('CargoExterno.codigo' => $this->passedArgs[2])));
        }
        else {
            $this->data = $dadosCargo;
        }

    }
}
