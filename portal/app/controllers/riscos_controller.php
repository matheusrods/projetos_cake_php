<?php
class RiscosController extends AppController
{
    public $name = 'riscos';
    var $uses = array(
        'Risco',
        'Periodicidade',
        'GrupoRisco',
        'RiscoAtributo',
        'RiscoExterno',
        'RiscosImpactos',
        'Esocial'
    );

    function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow('trocar_status', 'esocial', 'filtrar', 'relacionar_risco_impacto_esocial', 'listar_riscos', 'export_excel');
        //$this->BAuth->allow(array('index_externo','listagem_externo','editar_externo'));
    }

    function index()
    {
        $this->pageTitle = 'Cadastro de Riscos';

        $filtros    = $this->Filtros->controla_sessao($this->data, $this->Risco->name);
        $this->data = $filtros;
        $array_grupo = $this->GrupoRisco->retorna_grupo();
        $this->set(compact('array_grupo'));
    }

    public function esocial()
    {
        $this->pageTitle = 'Lista de Riscos do e-Social';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Risco->name);
        $this->data = $filtros;

        $this->comboGrupoRisco();
    }

    public function comboGrupoRisco($data = null)
    {
        $this->loadModel('GrupoRisco');

        $combo_grupo_risco = $this->GrupoRisco->retorna_grupo();
        $this->set(compact('combo_grupo_risco'));
    }

    function listagem()
    {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Risco->name);

        $fields = array('Risco.codigo', 'Risco.nome_agente', 'Risco.codigo_grupo', 'GrupoRisco.codigo', 'GrupoRisco.descricao', 'Risco.ativo');
        $conditions = $this->Risco->converteFiltroEmCondition($filtros);
        $order = 'CAST(nome_agente AS VARCHAR(254))';

        $joins  = array(
            array(
                'table' => $this->GrupoRisco->databaseTable . '.' . $this->GrupoRisco->tableSchema . '.' . $this->GrupoRisco->useTable,
                'alias' => 'GrupoRisco',
                'type' => 'LEFT',
                'conditions' => 'GrupoRisco.codigo = Risco.codigo_grupo',
            )
        );

        $this->paginate['Risco'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'joins' => $joins,
            'limit' => 50,
            'order' => $order,
        );


        $riscos = $this->paginate('Risco');
        $this->set(compact('riscos'));
    }

    public function listagem_esocial()
    {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Risco->name);

        $this->data['Risco'] = $filtros;
        // FIM - filtrar por usuário logado

        $this->paginate['Risco'] = $this->Risco->getListaRiscos($filtros);

        $riscos = $this->paginate('Risco');
        $this->set(compact('riscos'));
    }

    function incluir()
    {
        $this->pageTitle = 'Incluir Novo Risco';

        if ($this->RequestHandler->isPost()) {
            if ($this->Risco->incluir($this->data)) {
                $this->Risco->determina_ausencia_risco($this->Risco->id);
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'riscos'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }        
        
        $array_grupo = $this->GrupoRisco->retorna_grupo();
        $array_efeito = $this->RiscoAtributo->retorna_exposicao(RiscoAtributo::CLASSIFICACAO_EFEITO_CRITICO);
        $array_exposicao = $this->RiscoAtributo->retorna_exposicao(RiscoAtributo::MEIO_EXPOSICAO);
        $array_classificacao = array('1' => 'Quantitativo', '2' => 'Qualitativo', '3' => 'por Faixa de Conforto');
        $array_formula = array('1' => 'Milhões de particulas por dc3', '2' => 'Poeira Respirável', '3' => 'Poeira Total');

        $esocial_tabela_24 = $this->Esocial->find('list', array('fields' => array('codigo', 'cod_desc'), 'conditions' => array('tabela' => '24', 'ativo' => 1)));

        $this->set(compact('array_grupo', 'array_classificacao', 'array_efeito', 'array_formula', 'array_exposicao', 'esocial_tabela_24'));
    }

    function editar($codigo)
    {
        $this->pageTitle = 'Editar Risco';

        if ($this->RequestHandler->isPost()) {
            if ($this->Risco->atualizar($this->data)) {
                $this->Risco->determina_ausencia_risco($this->Risco->id);
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'riscos'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $periodicidade = $this->Periodicidade->find('all', array('conditions' => array('codigo_risco' => $codigo), 'order' => 'de ASC'));

            if (count($periodicidade)) {
                foreach ($periodicidade as $key => $campo) {
                    $this->data['Periodicidade'][$key] = array(
                        'de' => $campo['Periodicidade']['de'],
                        'ate' => $campo['Periodicidade']['ate'],
                        'meses' => $campo['Periodicidade']['meses'],
                        'codigo_risco' => $campo['Periodicidade']['codigo_risco'],
                    );
                }
            }
        }

        if ($codigo) {
            if ($this->data) {
                $this->data = $this->data + $this->Risco->carregar($codigo);
            } else {
                $this->data = $this->Risco->carregar($codigo);
            }
        }

        $this->set(compact('risco'));
        $array_grupo = $this->GrupoRisco->retorna_grupo();
        $array_efeito = $this->RiscoAtributo->retorna_exposicao(RiscoAtributo::CLASSIFICACAO_EFEITO_CRITICO);
        $array_exposicao = $this->RiscoAtributo->retorna_exposicao(RiscoAtributo::MEIO_EXPOSICAO);
        $array_classificacao = array('1' => 'Quantitativo', '2' => 'Qualitativo', '3' => 'por Faixa de Conforto');
        $array_formula = array('1' => 'Milhões de particulas por dc3', '2' => 'Poeira Respirável', '3' => 'Poeira Total');

        $esocial_tabela_24 = $this->Esocial->find('list', array('fields' => array('codigo', 'cod_desc'), 'conditions' => array('tabela' => '24', 'ativo' => 1)));

        $this->set(compact('risco', 'array_grupo', 'array_classificacao', 'array_efeito', 'array_formula', 'array_exposicao', 'esocial_tabela_24'));
    }

    function buscar_risco()
    {

        $this->layout = 'ajax_placeholder';
        $input_id = !empty($this->passedArgs['input_id']) ? $this->passedArgs['input_id'] : '';
        $input_display = !empty($this->passedArgs['input_display']) ? $this->passedArgs['input_display'] : $this->data['Risco']['input_display'];

        $this->data['Risco'] = $this->Filtros->controla_sessao($this->data, $this->Risco->name);
        $array_grupo = $this->Risco->carrega_grupo();

        $this->set(compact('input_id', 'input_display', 'array_grupo'));
    }

    function buscar_listagem($destino)
    {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Risco->name);

        $conditions = $this->Risco->converteFiltroEmCondition($filtros);

        $this->paginate['Risco'] = array(
            'conditions' => $conditions,
            'limit' => 10,
            'order' => 'nome_agente',
        );

        $riscos = $this->paginate('Risco');
        $this->set(compact('riscos', 'destino'));

        if (isset($this->passedArgs['input_id'])) {
            $this->set('input_id', str_replace('-search', '', $this->passedArgs['input_id']));
        }

        if (isset($this->passedArgs['input_display']))
            $this->set('input_display', str_replace('-search', '', $this->passedArgs['input_display']));
    }

    function buscar_risco_por_grupo($codigo_grupo)
    {
        $this->layout = 'ajax';
        $this->render(false, false);

        $risco = $this->Risco->find('all', array('conditions' => array('codigo_grupo' => $codigo_grupo, 'ativo' => 1)));

        echo json_encode($risco);
    }

    function retorna_risco_por_grupo($codigo_risco)
    {
        $this->layout = 'ajax';
        $this->render(false, false);

        $this->Risco->virtualFields['codigo_class_efeito'] = '(
        SELECT  codigo 
        FROM    riscos_atributos_detalhes 
        WHERE   codigo_risco_atributo = ' . RiscoAtributo::CLASSIFICACAO_EFEITO_CRITICO . '  
        AND codigo = Risco.classificacao_efeito)';

        $this->Risco->virtualFields['meio_exposicao'] = '(
        SELECT  descricao 
        FROM    riscos_atributos_detalhes 
        WHERE   codigo_risco_atributo = ' . RiscoAtributo::MEIO_EXPOSICAO . ' 
        AND codigo = Risco.codigo_meio_propagacao)';

        $this->Risco->virtualFields['classificacao_efeito'] = '(
        SELECT  descricao 
        FROM    riscos_atributos_detalhes 
        WHERE   codigo_risco_atributo = ' . RiscoAtributo::CLASSIFICACAO_EFEITO_CRITICO . '  
        AND codigo = Risco.classificacao_efeito)';

        $risco = $this->Risco->find('all', array('conditions' => array('codigo' => $codigo_risco)));
        echo json_encode($risco);
    }

    function buscar_epi_riscos($codigo_epi)
    {
        $this->layout = 'ajax_placeholder';
        $this->data = $this->Filtros->controla_sessao($this->data, $this->Risco->name);

        $grupo_risco = $this->GrupoRisco->retorna_grupo();
        $this->set(compact('codigo_epi', 'grupo_risco'));
    }

    function buscar_epc_riscos($codigo_epc)
    {
        $this->layout = 'ajax_placeholder';
        $this->data = $this->Filtros->controla_sessao($this->data, $this->Risco->name);

        $grupo_risco = $this->GrupoRisco->retorna_grupo();
        $this->set(compact('codigo_epc', 'grupo_risco'));
    }

    function listagem_epi_riscos($destino, $codigo_epi)
    {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Risco->name);
        $conditions = $this->Risco->converteFiltroEmCondition($filtros);

        $fields = array('Risco.codigo', 'Risco.nome_agente', 'GrupoRisco.codigo', 'GrupoRisco.descricao',);
        $order = array('Risco.nome_agente', 'GrupoRisco.descricao');

        $joins  = array(
            array(
                'table' => $this->GrupoRisco->databaseTable . '.' . $this->GrupoRisco->tableSchema . '.' . $this->GrupoRisco->useTable,
                'alias' => 'GrupoRisco',
                'type' => 'LEFT',
                'conditions' => 'GrupoRisco.codigo = Risco.codigo_grupo',
            ),
        );

        $this->paginate['Risco'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'joins' => $joins,
            'limit' => 10,
            'order' => $order,
        );

        $riscos = $this->paginate('Risco');
        $this->set(compact('riscos', 'destino', 'codigo_epi'));
    }

    function listagem_epc_riscos($destino, $codigo_epc)
    {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Risco->name);
        $conditions = $this->Risco->converteFiltroEmCondition($filtros);

        $fields = array('Risco.codigo', 'Risco.nome_agente', 'GrupoRisco.codigo', 'GrupoRisco.descricao',);
        $order = array('GrupoRisco.descricao', 'Risco.nome_agente');

        $joins  = array(
            array(
                'table' => $this->GrupoRisco->databaseTable . '.' . $this->GrupoRisco->tableSchema . '.' . $this->GrupoRisco->useTable,
                'alias' => 'GrupoRisco',
                'type' => 'LEFT',
                'conditions' => 'GrupoRisco.codigo = Risco.codigo_grupo',
            ),
        );

        $this->paginate['Risco'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'joins' => $joins,
            'limit' => 10,
            'order' => $order,
        );

        $riscos = $this->paginate('Risco');
        $this->set(compact('riscos', 'destino', 'codigo_epc'));
    }

    function trocar_status($codigo_risco = null)
    {
        $this->data = $this->Risco->read(null, $codigo_risco);
        $this->data['Risco']['ativo'] = ($this->data['Risco']['ativo'] == 0 ? 1 : 0);
        if ($this->Risco->atualizar($this->data)) {
            $this->BSession->setFlash('save_success');
            $this->redirect(array('action' => 'index', 'controller' => 'riscos'));
        } else {
            $this->BSession->setFlash('save_error');
        }
    }

    function index_externo()
    {
        $this->pageTitle = 'Riscos Externos';
        $this->data[$this->RiscoExterno->name] = $this->Filtros->controla_sessao($this->data, $this->RiscoExterno->name);
    }

    function listagem_externo()
    {
        $this->layout = 'ajax';
        $riscos = array();
        $listagem = false;

        $filtros = $this->Filtros->controla_sessao($this->data, $this->RiscoExterno->name);

        $this->loadModel('GrupoEconomico');
        $codigo_cliente_filial = $filtros['codigo_cliente'];
        $codigo_cliente_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente_filial);

        if (!empty($filtros['codigo_cliente'])) {

            $conditions = $this->RiscoExterno->converteFiltroEmCondition($filtros);

            $fields = array('Risco.codigo', 'RiscoExterno.codigo', 'Risco.nome_agente', 'Risco.ativo', 'RiscoExterno.codigo_externo');
            $order = 'CAST(nome_agente AS VARCHAR(254))';

            $this->Risco->bindModel(
                array(
                    'hasOne' => array(
                        'RiscoExterno' => array(
                            'foreignKey' => 'codigo_riscos',
                            'conditions' => array('RiscoExterno.codigo_cliente' => $codigo_cliente_matriz)
                        )
                    )
                ),
                false
            );

            $this->paginate['Risco'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
            );

            $riscos = $this->paginate('Risco');
            $listagem = true;
        }

        $this->set(compact('riscos', 'listagem'));
        $this->set('codigo_cliente_filtro', $codigo_cliente_matriz);
    }

    function editar_externo()
    {
        $this->pageTitle = 'Riscos Externos';

        $codigoRisco = $this->RequestHandler->params['pass'][1];
        $codigo_cliente = $this->RequestHandler->params['pass'][0];
        if (isset($this->RequestHandler->params['pass'][2])) {
            $codigoRiscoExterno = $this->RequestHandler->params['pass'][2];
        }

        $dadosRisco = $this->Risco->carregar($codigoRisco);

        if ($this->RequestHandler->isPost()) {
            if ($this->RiscoExterno->save($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index_externo', 'controller' => 'riscos'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        if (isset($this->passedArgs[2])) {
            $this->data = $this->RiscoExterno->find('first', array('conditions' => array('RiscoExterno.codigo' => $this->passedArgs[2])));
        } else {
            $this->data = $dadosRisco;
        }
        $this->set('codigo_cliente', $codigo_cliente);
    }

    /*
     * function filtrar()
     *
     * Função para filtrar riscos do e-Social no modal das telas de criar e editar riscos_impactos
    */
    public function filtrar()
    {
        $this->layout = 'ajax';

        $filtros = $_POST;

        $this->paginate['Risco'] = $this->Risco->getListaRiscos($filtros);

        $riscos = $this->paginate('Risco');

        echo json_encode($riscos);
    }

    public function relacionar_risco_impacto_esocial()
    {
        $this->layout = 'ajax';

        $codigo_risco_impacto = $this->data['RiscosImpactos']['codigo_risco_impacto'];

        $codigo_risco = $this->data['RiscosEsocial']['codigo_risco'];
        $RiscosImpactos = $this->RiscosImpactos->find('first', array('conditions' => array(
            'RiscosImpactos.codigo' => $codigo_risco_impacto
        )));

        $RiscosImpactos['RiscosImpactos']['codigo_risco'] = $codigo_risco;

        if ($this->RiscosImpactos->atualizar($RiscosImpactos)) {

            $riscosesocial = $this->Risco->getByCodigoRiscosImpactos($codigo_risco);

            $result = array(
                'result' => 1,
                'riscos_esocial' => $riscosesocial
            );
            echo json_encode($result);
        } else {
            $result = array(
                'result' => 2,
                'riscos_esocial' => array()
            );
            echo json_encode($result);
        }
    }

    public function listar_riscos()
    {
        $this->layout = 'ajax';


        $this->paginate['Risco'] = $this->Risco->getListaRiscos();

        $riscos = $this->paginate('Risco');
        $this->helpers['Paginator'] = array('ajax' => 'Ajax');

        echo json_encode($riscos);
    }

    public function export_excel()
    {
        $this->layout = 'ajax';

        $fileName = "riscos_" . date('Y-m-d_H_i_s') . ".xls";

        ob_clean();
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="relatorio_riscos_' . date('YmdHis') . '.csv"');
        header('Pragma: no-cache');

        //header('Content-Type: text/html; charset=utf-8');

        $riscosArr = $this->Risco->relatorio();

        $cabecalhoArr = array_keys($riscosArr[key($riscosArr)]);

        echo implode('; ', $cabecalhoArr) . PHP_EOL;

        foreach ($riscosArr as $indiceLinha => $risco) {

            echo implode('; ', $risco) . PHP_EOL;
        }

        die;
    }
}
