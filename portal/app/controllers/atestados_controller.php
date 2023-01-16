<?php
class AtestadosController extends AppController {
  public $name = 'Atestados';
  public $helpers = array('BForm', 'Html', 'Ajax', 'Highcharts');

  public function beforeFilter() {
    parent::beforeFilter();
    $this->BAuth->allow('sintetico', 'sintetico_listagem', 'analitico', 'analitico_listagem', 'buscar_afastamento_anterior', 'buscar_cnpj_alocado', 'busca_conflito_atestado');
  }//FINAL FUNCTION beforeFilter

  
  var $uses = array(
   'Atestado', 
   'TipoLocalAtendimento',
   'ClienteFuncionario',
   'MotivoAfastamento', 
   'LocalAtendimento',  
   'EnderecoEstado',
   'EnderecoCidade',
   'AtestadoCid',
   'Cid',
   'Medico',
   'Esocial',
   'GrupoEconomicoCliente',
   'Alerta'
  );

  public function index($codigo_unidade =  null) {
    $this->pageTitle = 'Absenteísmo';
    $filtros = $this->Filtros->controla_sessao($this->data, 'AtestadoFuncionario');
    
    // se tem dados na sessao então preencha o codigo cliente e se tem codigo_cliente em $filtros usuario deve estar pesquisando
		if(!empty($this->authUsuario['Usuario']['codigo_cliente']) && empty($filtros['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
    }
   
    $this->data['AtestadoFuncionario'] = $filtros;
    
    // alimenta os formularios
    $this->atestados_filtros($this->data['AtestadoFuncionario']);
  
    // $status_matricula = array('1' => 'Ativos', '0' => 'Inativos', '2' => 'Férias', '3' => 'Afastado');
    // $this->set(compact('status_matricula'));
    // $this->carrega_combos_grupo_economico('AtestadoFuncionario');
  }//FINAL FUNCTION index

    public function atestados_filtros($thisDataAtestadoFuncionario = null){
        $this->loadModel('Atestado');
        $this->loadModel('GrupoEconomicoCliente');
        $this->loadModel('Setor');
        $this->loadModel('Cargo');
        $this->loadModel('GrupoEconomico');

        // converte com $this->normalizaCodigoCliente pois codigo_cliente pode estar vindo do form como string ou da sessão como array
    	$codigo_cliente = $this->normalizaCodigoCliente($thisDataAtestadoFuncionario['codigo_cliente']);

        $thisDataAtestadoFuncionario['codigo_cliente'] = $codigo_cliente;

        $status_matricula = array('1' => 'Ativos', '0' => 'Inativos', '2' => 'Férias', '3' => 'Afastado');

        if(!empty($codigo_cliente)) {
            if(!$this->GrupoEconomico->verificaMatriz($codigo_cliente[0])){
              $codigo_cliente = $this->GrupoEconomicoCliente->getCodigoGrupoEconomico($codigo_cliente);
            }
        }

        $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
        $setores = $this->Setor->lista($codigo_cliente);
        $cargos = $this->Cargo->lista($codigo_cliente);

        // configura no $this->data
        $this->data['AtestadoFuncionario'] = $thisDataAtestadoFuncionario;

        $this->set(compact('status_matricula', 'tipos_agrupamento', 'unidades', 'setores', 'cargos'));
    }

  public function lista_funcionarios() {
    
    $this->loadModel('FuncionarioSetorCargo');

    ini_set('memory_limit', '536870912');
    ini_set('max_execution_time', '999999');
    set_time_limit(0);

    $filtros = $this->Filtros->controla_sessao($this->data, 'AtestadoFuncionario');
    
    // se tem dados na sessao então preencha o codigo cliente e se tem codigo_cliente em $filtros usuario deve estar pesquisando
    if(!empty($this->authUsuario['Usuario']['codigo_cliente']) && empty($filtros['codigo_cliente'])) {
      $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
    }

    if(isset($filtros['codigo_cliente_alocacao']) && !empty($filtros['codigo_cliente_alocacao']) && trim($filtros['codigo_cliente_alocacao']) != ''){

    } else {
      unset($filtros['codigo_cliente_alocacao']);
    }
    if(isset($filtros['codigo_setor']) && !empty($filtros['codigo_setor']) && trim($filtros['codigo_setor']) != ''){

    } else {
      unset($filtros['codigo_setor']);
    }
    if(isset($filtros['codigo_cargo']) && !empty($filtros['codigo_cargo']) && trim($filtros['codigo_cargo']) != ''){

    } else {
      unset($filtros['codigo_cargo']);
    }

    // die(debug($filtros));

    // limpa e ajusta campo cpf sem traços e posntos
    if(isset($filtros['cpf_funcionario'])){
      $cpf1 = trim($filtros['cpf_funcionario']);
      $cpf2 = str_replace(".", "", $cpf1);
      $cpf_funcionario = str_replace("-", "", $cpf2);

      $filtros['cpf'] = $cpf_funcionario;

    }
    
    $listagem = array();
    if (!empty($filtros['codigo_cliente'])) {
      $filtros['codigo_cliente'] = $this->normalizaCodigoCliente($filtros['codigo_cliente']);
      $conditions = $this->FuncionarioSetorCargo->converteFiltrosEmConditions($filtros);
      /* $this->FuncionarioSetorCargo->bindModel(array('belongsTo' => array(
      'Funcionario' => array('foreignKey' => false, 'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'),)),true);*/
      $joins = array(
      array(
      'table' => 'cliente_funcionario',
      'alias' => 'ClienteFuncionario',
      'type' => 'INNER',
      'conditions' => array('FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
      "FuncionarioSetorCargo.codigo = (Select top 1 codigo from funcionario_setores_cargos where codigo_cliente_funcionario = ClienteFuncionario.codigo order by 1 desc )"
      )
      ),
      array(
      'table' => 'cliente',
      'alias' => 'Cliente',
      'type' => 'INNER',
      'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao'
      ),
      array(
      'table' => 'setores',
      'alias' => 'Setor',
      'type' => 'INNER',
      'conditions' => 'Setor.codigo = FuncionarioSetorCargo.codigo_setor'
      ),
      array(
      'table' => 'cargos',
      'alias' => 'Cargo',
      'type' => 'INNER',
      'conditions' => 'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo'
      ),
      array(
      'table' => 'funcionarios',
      'alias' => 'Funcionario',
      'type' => 'INNER',
      'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
      )
      );

      $recursive = -1;

      $order = array('Cliente.razao_social', 'Setor.descricao', 'Cargo.descricao', 'Funcionario.nome');
      $fields = array('Cliente.razao_social', 'Cliente.nome_fantasia', 'Setor.descricao', 'Cargo.descricao',
      'ClienteFuncionario.ativo', 'Funcionario.nome', 'Funcionario.cpf', 'ClienteFuncionario.codigo','FuncionarioSetorCargo.codigo');
      $listagem = $this->FuncionarioSetorCargo->find('all', compact('conditions', 'order', 'joins', 'recursive','fields'));
      //die(debug($conditions));
      //die(debug($listagem));
      $this->set(compact('listagem'));
    }//FINAL SE $filtros['codigo_cliente'] NÃO FOR VAZIO

    /***************************************************
    * validacao adicionado para evitar o cliente de
    * burlar o acesso e ver dados de outros clientes;
    ***************************************************/
    /*if($this->BAuth->user('codigo_cliente'))) {
    $dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $this->BAuth->user('codigo_cliente')), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
    $codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
    }

    $this->layout = 'ajax';

    if($filtros['ativo'] == 'all') unset($filtros['ativo']);
    if(is_numeric($codigo_grupo_economico)) {
    $options['recursive'] = '-1';
    $options['conditions'] = $this->ClienteFuncionario->converteFiltroEmCondition($filtros);

    $options['conditions'] = $options['conditions'] + array("GrupoEconomicoCliente.codigo_grupo_economico = {$codigo_grupo_economico}");

    $options['fields'] = array(
    'ClienteFuncionario.codigo',
    'ClienteFuncionario.ativo',
    'Cliente.razao_social',
    //'Setor.descricao',
    //'Cargo.descricao',
    'Funcionario.nome',
    'cargo',
    'setor'
    );

    $options['joins'] = array(
    array(
    'table' => 'cliente_funcionario',
    'alias' => 'ClienteFuncionario',
    'type' => 'INNER',
    'conditions' => 'ClienteFuncionario.codigo_cliente = GrupoEconomicoCliente.codigo_cliente'
    ),
    array(
    'table' => 'cliente',
    'alias' => 'Cliente',
    'type' => 'INNER',
    'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente'
    ),
    //array(
    //    'table' => 'setores',
    //    'alias' => 'Setor',
    //    'type' => 'INNER',
    //    'conditions' => 'Setor.codigo = ClienteFuncionario.codigo_setor'
    //),
    //array(
    //    'table' => 'cargos',
    //    'alias' => 'Cargo',
    //    'type' => 'INNER',
    //    'conditions' => 'Cargo.codigo = ClienteFuncionario.codigo_cargo'
    //),
    array(
    'table' => 'funcionario_setores_cargos',
    'alias' => 'FuncionarioSetorCargo',
    'type' => 'LEFT',
    'conditions' => array(
        'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
        "FuncionarioSetorCargo.data_fim is null OR FuncionarioSetorCargo.data_fim = ''"
    )
    ),
    array(
    'table' => 'funcionarios',
    'alias' => 'Funcionario',
    'type' => 'INNER',
    'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
    )
    );

    $this->GrupoEconomicoCliente->virtualFields = array(
    'setor' => "(SELECT descricao FROM RHHealth.dbo.setores where codigo = (SELECT TOP 1 codigo_setor FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo AND (data_fim = '' OR data_fim IS NULL )  ORDER BY 1 DESC))",
    'cargo' => "(SELECT descricao FROM RHHealth.dbo.cargos where codigo = (SELECT TOP 1 codigo_cargo FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo  AND (data_fim = '' OR data_fim IS NULL ) ORDER BY 1 DESC))"
    );

    $this->set('listagem', $this->GrupoEconomicoCliente->find('all', $options));
    } else {
    $this->set('listagem', array());
    }
    */       
  }//FINAL FUNCTION lista_funcionarios
    
  public function retorna_codigo_grupo_economico() {

    /***************************************************
    * validacao adicionado para evitar o cliente de 
    * burlar o acesso e ver dados de outros clientes;
    ***************************************************/
    if(!is_null($this->BAuth->user('codigo_cliente'))) {
      $codigo_unidade = $this->BAuth->user('codigo_cliente');
    } else {
      $codigo_unidade = $this->params['form']['codigo_unidade'];
    }


    $this->GrupoEconomicoCliente->virtualFields = false;
    $dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_unidade), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));

    echo json_encode(array('codigo_grupo_economico' => $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico']));
    exit;
  }//FINAL FUNCTION retorna_codigo_grupo_economico

  public function sub_filtro_cliente_funcionario($codigo_grupo_economico, $codigo_unidade) {
      /***************************************************
       * validacao adicionado para evitar o cliente de
       * burlar o acesso e ver dados de outros clientes;
       ***************************************************/
      if(!is_null($this->BAuth->user('codigo_cliente'))) {
        $codigo_unidade = $this->BAuth->user('codigo_cliente');
        
        $dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_unidade), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
        $codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
      }
      
      
     $this->data['ClienteFuncionario'] = $this->Filtros->controla_sessao($this->data, 'ClienteFuncionario');
     if(!isset($this->data['ClienteFuncionario']['ativo']) OR $this->data['ClienteFuncionario']['ativo'] == '') {
      $this->data['ClienteFuncionario']['ativo'] = '1';
    }
    if(isset($codigo_grupo_economico) && $codigo_grupo_economico) {
     $lista_unidades = $this->GrupoEconomicoCliente->retorna_lista_de_unidades_de_um_grupo_economico($codigo_grupo_economico);
     $lista_cargos = $this->GrupoEconomicoCliente->listaCargos($codigo_grupo_economico);
     $lista_setores = $this->GrupoEconomicoCliente->listaSetores($codigo_grupo_economico);
     $lista_funcionarios = $this->GrupoEconomicoCliente->listaFuncionarios($codigo_grupo_economico);      
     $lista_status = array(
      '1' => 'Ativos', 
      '0' => 'Inativos',
      '2' => 'Férias',
      '3' => 'Afastado',
      'all' => 'Todos'
    );
   } else {
    $lista_unidades = array();
    $lista_cargos = array();
    $lista_setores = array();
    $lista_funcionarios = array();
    $lista_status = array();
  }
  $codigo_funcionario = isset($this->data['ClienteFuncionario']['codigo_funcionario']) ? $this->data['ClienteFuncionario']['codigo_funcionario'] : '';
  $codigo_setor = isset($this->data['ClienteFuncionario']['codigo_setor']) ? $this->data['ClienteFuncionario']['codigo_setor'] : '';
  $codigo_cargo = isset($this->data['ClienteFuncionario']['codigo_cargo']) ? $this->data['ClienteFuncionario']['codigo_cargo'] : '';
  $codigo_cliente = isset($this->data['ClienteFuncionario']['codigo_cliente']) ? $this->data['ClienteFuncionario']['codigo_cliente'] : '';
  $ativo = isset($this->data['ClienteFuncionario']['ativo']) ? $this->data['ClienteFuncionario']['ativo'] : '1';
  $this->set(compact('lista_funcionarios', 'lista_setores', 'lista_cargos', 'lista_unidades', 'lista_status', 'codigo_unidade', 'codigo_cliente', 'codigo_cargo', 'codigo_setor', 'codigo_funcionario', 'codigo_grupo_economico', 'ativo'));
}

  public function lista_atestados($codigo_cliente_funcionario, $codigo_funcionario_setor_cargo) {
    $this->loadModel('FuncionarioSetorCargo');
    $this->pageTitle = 'Lista Atestados';   

    $conditions = array('ClienteFuncionario.codigo' => $codigo_cliente_funcionario);
    $this->FuncionarioSetorCargo->bindModel(array('belongsTo' => array(
      'Funcionario' => array('foreignKey' => false, 'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'),
    )));
    $funcionario = $this->FuncionarioSetorCargo->carregar($codigo_funcionario_setor_cargo);
    $dados_clientefuncionario = $this->ClienteFuncionario->find('first', compact('conditions'));

    if($dados_clientefuncionario) {

      $this->Atestado->virtualFields = array (
        'anexo' => '(SELECT TOP 1 caminho_arquivo from anexos_atestados where codigo_atestado = Atestado.codigo order by 1 desc)',
      );

      $lista_atestados = $this->Atestado->find('all', array('conditions' => array('Atestado.ativo' => 1,'Atestado.codigo_cliente_funcionario' => $codigo_cliente_funcionario)));
 
    } else {
      $lista_atestados = array();
    }

    $this->set(compact('funcionario', 'lista_atestados', 'dados_clientefuncionario'));
  }//FINAL FUNCTION lista_atestados

  /**
   * [incluir description]
   * 
   * metodo para incluir o atestado.
   * 
   * @param  [type] $codigo_cliente_funcionario     [description]
   * @param  [type] $codigo_funcionario_setor_cargo [description]
   * @return [type]                                 [description]
   */
    public function incluir($codigo_cliente_funcionario, $codigo_funcionario_setor_cargo, $codigo_usuario_grupo_covid=null) 
    {
    
        $this->pageTitle = 'Incluir Atestado / Afastamento';      

        //verifica se é post
        if ($this->RequestHandler->isPost()) {
          
          // $this->Session->check('compara')
          
          try {
                $this->Atestado->query('begin transaction');
                $this->data['Atestado']['motivo_afastamento'];
                $this->data['Atestado']['origem_retificacao'];
                $this->data['Atestado']['tipo_acidente_transito'];
                $this->data['Atestado']['onus_requisicao'];
                $this->data['Atestado']['onus_remuneracao'];
                $this->data['Atestado']['numero_processo'];
                $this->data['Atestado']['tipo_processo'];
                $this->data['Atestado']['codigo_documento_entidade'] = Comum::soNumero($this->data['Atestado']['codigo_documento_entidade']);
                $this->data['Atestado']['codigo_cliente_funcionario'] = $codigo_cliente_funcionario;
                $this->data['Atestado']['codigo_func_setor_cargo'] = $codigo_funcionario_setor_cargo;
                $this->data['Atestado']['ativo'] = 1;                

                if($this->data['Atestado']['data_afastamento_periodo'] == $this->data['Atestado']['data_retorno_periodo']) {
                    if(($this->data['Atestado']['hora_afastamento'] == '00:00:00.0000000') || ($this->data['Atestado']['hora_retorno'] == '00:00:00.0000000'))
                        $this->Atestado->validationErrors = array('hora_afastamento' => 'Hora deve ser preenchida');
                }

                $validate = true;

                if(($this->data['Atestado']['origem_retificacao'] == 2) || ($this->data['Atestado']['origem_retificacao'] == 3)){
                    if(empty($this->data['Atestado']['tipo_processo']))
                        $this->Atestado->invalidate('tipo_processo', 'Informe o Tipo processo');
                        $validate = false;
                }
                if(($this->data['Atestado']['origem_retificacao'] == 2) || ($this->data['Atestado']['origem_retificacao'] == 3)){
                  if(empty($this->data['Atestado']['numero_processo']))
                    $this->Atestado->invalidate('numero_processo', 'Informe o numero do processo');
                    $validate = false;
                }

                if($this->data['Atestado']['codigo_motivo_licenca'] == 'Selecione'){                 
                    $this->Atestado->invalidate('codigo_motivo_licenca', 'O campo Motivo da Licença é obrigatório.');
                    $validate = false;
                }  
                
                //incluir o atestado
                if($this->Atestado->incluir($this->data)) {                

                    //inlcui o cid caso haja  
                    $codigo_atestado = $this->Atestado->id;

                    //variavel auxiliar
                    $var_cid = true;

                    //varre os dados do cids setados
                    foreach($this->data['cid10'] as $key => $val) {
                        //valor em branco
                        if($val['doenca'] != '') {

                            //para pegar o codigo que esta na tabela do cid
                            $cid = $this->Cid->find('first', array('conditions' => array('descricao' => $val['doenca'])));

                            //seta os dados para gravar o relacionamento
                            $dados = array(
                                'AtestadoCid' => array(
                                    'codigo_atestado'   => $codigo_atestado,
                                    'codigo_cid'        => $cid['Cid']['codigo']
                                )
                            );

                            //verifica se incluiu o relacionamento atestado com cid
                            if(!$this->AtestadoCid->incluir($dados)){
                                $var_cid = false;
                            }//fim atestado cid incluir

                        }//fim if xx
                    }//fim foreach

                    //verifica se esta verdadeiro a variavel cid
                    if($var_cid) {

                        //para disparar email quando nao tiver cid
                        $this->Atestado->notificacao_atestado_sem_cid($codigo_atestado);

                        $this->Atestado->commit();
                        $this->BSession->setFlash('save_success');
                        $this->redirect(array('controller' => 'atestados', 'action' => 'editar', $codigo_cliente_funcionario, $codigo_funcionario_setor_cargo, $this->Atestado->id));
                    }
                    else {
                        throw new Exception("Erro ao incluir o atestado");
                    }//fim else var cid
                
                }//fim atestados incluir

                

            }//try
            catch (Exception $e) {
                $this->BSession->setFlash('save_error');
                $this->Atestado->rollback();
            }//fim catch

        }//fim if post

        $edit_mode ='';

        if(!empty($codigo_usuario_grupo_covid)) {
            $this->set('codigo_usuario_grupo_covid');
        }
     
        $this->carrega_combos(1);
        $this->set(compact('codigo_cliente_funcionario', 'edit_mode','codigo_funcionario_setor_cargo'));


    }//FINAL FUNCTION incluir


    /**
     * [editar description]
     * 
     * metodo para buscar se houve algum afastamento anterior na base do funcionario
     * 
     * @param  [type] $codigo_cliente_funcionario     [description]
     * @return [type]                                 [description]
     */
    public function buscar_afastamento_anterior($codigo_cliente_funcionario)
    {
      $this->autoRender = false;
      //pega a data atual e a hora atual e busca na base se tem o registro de afastamento nos ultimos 60 dias
      $end_date = date("Y-m-d", strtotime( "now" )) . ' 23:59:59';
      $start_date = date("Y-m-d H:m:i", strtotime( "-60 days" ));
      //query da busca do afastamento do funcionario
      $atestado_inclusao = $this->Atestado->find('first', array(
        'conditions' => array(
          'data_inclusao BETWEEN ? and ?' => array($start_date, $end_date),
          'codigo_cliente_funcionario' => $codigo_cliente_funcionario,
        )
      ));
      $return = (!empty($atestado_inclusao['Atestado']['codigo_cliente_funcionario']) ? 1 : 0);
      return json_encode(array('return' => $return));
    }//fim buscar afastamento anterior
    

    /**
     * [editar description]
     * 
     * metodo para buscar na baase se ja existe CNPJ da alocação do funcionario 
     * 
     * @param  [type] $codigo_funcionario_setor_cargo     [description]
     * @param  [type] $param_cnpj_alocado                 [description]
     * @return [type]                                     [description]
     */
    public function buscar_cnpj_alocado($codigo_funcionario_setor_cargo, $param_cnpj_alocado = null){
      $this->autoRender = false;
      
      //query que busca na base se tem algum CNPJ da alocação do funcionario, para que no JavaScript via Ajax, acione o Alert para que o funcionario coloque um CNPJ diferente.  
      $retorno_cnpj = $this->Atestado->buscar_cnpj_alocado($codigo_funcionario_setor_cargo, $param_cnpj_alocado);

      return json_encode(array('return' => $retorno_cnpj));
    }//Fim da busca_cnpj_alocado


    /**
     * [editar description]
     * 
     * metodo para editar os dados do atestado
     * 
     * @param  [type] $codigo_cliente_funcionario     [description]
     * @param  [type] $codigo_funcionario_setor_cargo [description]
     * @param  [type] $codigo_atestado                [description]
     * @return [type]                                 [description]
     */
    public function editar($codigo_cliente_funcionario, $codigo_funcionario_setor_cargo, $codigo_atestado, $codigo_usuario_grupo_covid = null)
    {
        $this->pageTitle = 'Atualizar Atestado';
                
        if ($this->RequestHandler->isPost() OR $this->RequestHandler->isPut()) {

          debug($this->data['Atestado']);

            try {
                $this->Atestado->query('begin transaction');

                //Se possui arquivo anexo
                if(!empty($_FILES['data']['name']['Atestado']['anexo_atestado'])){
                    $retorno = 0;
                    $nome_arquivo = $_FILES['data']['name']['Atestado']['anexo_atestado'];

                    if (strpos($nome_arquivo, ".pdf") > 0 || strpos($nome_arquivo, ".jpg") > 0 || strpos($nome_arquivo, ".png") > 0){
                        $retorno = $this->salvar_anexo($codigo_atestado);

                        if(!$retorno){
                            $this->Atestado->rollback();
                            $this->BSession->setFlash('save_error');
                            $this->redirect(array('controller' => 'atestados', 'action' => 'lista_atestados', $this->passedArgs[0], $this->passedArgs[1]));
                        }
                        //Se a extensão é inválida 
                    } 
                    else {

                        $this->Atestado->rollback();
                        $this->BSession->setFlash(array(MSGT_ERROR, 'Somente as seguintes extensões são permitidas: JPG,PNG ou PDF. Tente Novamente.'));
                        $this->redirect(array('controller' => 'atestados', 'action' => 'lista_atestados', $this->passedArgs[0], $this->passedArgs[1]));
                    }
                }

                $this->data['Atestado']['motivo_afastamento'];
                $this->data['Atestado']['origem_retificacao'];
                $this->data['Atestado']['tipo_acidente_transito'];
                $this->data['Atestado']['onus_requisicao'];
                $this->data['Atestado']['onus_remuneracao'];
                $this->data['Atestado']['numero_processo'];
                $this->data['Atestado']['tipo_processo'];
                $this->data['Atestado']['codigo_documento_entidade'] = Comum::soNumero($this->data['Atestado']['codigo_documento_entidade']);
                $this->data['Atestado']['codigo_cliente_funcionario'] = $codigo_cliente_funcionario;
                $this->data['Atestado']['codigo'] = $codigo_atestado;
                $this->data['Atestado']['ativo'] = 1;               

                 $validate = true;
                //debug($this->data);

                if(($this->data['Atestado']['origem_retificacao'] == 2) || ($this->data['Atestado']['origem_retificacao'] == 3)){
                  if(empty($this->data['Atestado']['tipo_processo']))
                    $this->Atestado->invalidate('tipo_processo', 'Informe o Tipo processo');
                    $validate = false;
                }

                if(($this->data['Atestado']['origem_retificacao'] == 2) || ($this->data['Atestado']['origem_retificacao'] == 3)){                   
                  if(isset($this->data['Atestado']['numero_processo']) && empty($this->data['Atestado']['numero_processo'])){                   
                    $this->Atestado->invalidate('numero_processo', 'Informe o numero do processo');
                    $validate = false;                    
                  }
                }

                if($this->data['Atestado']['codigo_motivo_licenca'] == 'Selecione'){                 
                    $this->Atestado->invalidate('codigo_motivo_licenca', 'O campo Motivo da Licença é obrigatório.');
                    $validate = false;
                }

                //Se o endereço foi preenchido
                if(!empty($this->data['Atestado']['endereco'])){

                    if(Ambiente::TIPO_MAPA == 1) {
                        App::import('Component',array('ApiGoogle'));
                        $this->ApiMaps = new ApiGoogleComponent();
                    }
                    else if(Ambiente::TIPO_MAPA == 2) {
                        App::import('Component',array('ApiGeoPortal'));
                        $this->ApiMaps = new ApiGeoPortalComponent();
                    }

                    if(!empty($this->data['Atestado']['codigo_estado'])){
                        $dados_estado = $this->EnderecoEstado->find('first', array('conditions'=> array('codigo' => $this->data['Atestado']['codigo_estado'] ),'fields' => 'descricao','recursive' => -1));
                    }

                    if(!empty($this->data['Atestado']['codigo_cidade'])){
                        $dados_cidade = $this->EnderecoCidade->find('first', array('conditions'=> array('codigo' => $this->data['Atestado']['codigo_cidade'] ),'fields' => 'descricao','recursive' => -1));
                    }

                    if(!empty($dados_cidade['EnderecoCidade']['descricao']) && !empty($dados_estado['EnderecoEstado']['descricao'])){
                        $end_completo = $this->data['Atestado']['endereco'] . ', ' .$this->data['Atestado']['numero'] . ' - ' . $this->data['Atestado']['bairro'] . ' - ' . $dados_cidade['EnderecoCidade']['descricao'] . ' / ' . $dados_estado['EnderecoEstado']['descricao'];
                        list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($end_completo); 
                        if(!empty($latitude) && !empty($longitude)){
                            $this->data['Atestado']['latitude'] = $latitude;
                            $this->data['Atestado']['longitude'] = $longitude;
                        }
                    }
                }

                //cids
                //altera os cid caso haja
                //variavel auxiliar
                $var_cid = true;

                //verifica se este cid ja esta relacionado no atestado
                $query_del_atestado_cid = "DELETE FROM RHHealth.dbo.atestados_cid WHERE codigo_atestado = {$codigo_atestado}";
                $this->AtestadoCid->query($query_del_atestado_cid);

                //varre os dados do cids setados
                foreach($this->data['cid10'] as $keyCid => $valCid) {
                    
                    //valor em branco
                    if($valCid['doenca'] != '') {

                        //para pegar o codigo que esta na tabela do cid
                        $cid = $this->Cid->find('first', array('conditions' => array('descricao' => $valCid['doenca'])));

                        //seta os dados para gravar o relacionamento
                        $dados = array(
                            'AtestadoCid' => array(
                                'codigo_atestado'   => $codigo_atestado,
                                'codigo_cid'        => $cid['Cid']['codigo']
                            )
                        );
                        //verifica se incluiu o relacionamento atestado com cid
                        if(!$this->AtestadoCid->incluir($dados)){
                            $var_cid = false;
                        }//fim atestado cid incluir

                    }//fim if xx
                    
                }//fim foreach

                if($this->Atestado->atualizar($this->data)) {

                    //para disparar email quando nao tiver cid
                    $this->Atestado->notificacao_atestado_sem_cid($codigo_atestado);

                    $this->Atestado->commit();
                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('controller' => 'atestados', 'action' => 'lista_atestados', $this->passedArgs[0], $this->passedArgs[1]));
                } 
                else {

                    $this->Atestado->rollback();
                    $this->BSession->setFlash('save_error');
                    $this->set('cidades', $this->EnderecoCidade->combo($this->data['Atestado']['codigo_estado']));
                }

            }//try
            catch (Exception $e) {
                $this->BSession->setFlash('save_error');
                $this->Atestado->rollback();
            }//fim catch

        } 
        else {
            $this->Atestado->virtualFields = array ('anexo' => '(SELECT TOP 1 caminho_arquivo from anexos_atestados where codigo_atestado = Atestado.codigo order by 1 desc)');
            $this->data = $this->Atestado->find('first', array('conditions' => array('codigo' => $codigo_atestado)));
            
            $cidades = array('' => '(Selecione Primeiro o Estado)');
            if($this->data['Atestado']['codigo_estado']) {                
                $cidades = $this->EnderecoCidade->combo($this->data['Atestado']['codigo_estado']);
            }             
            $this->set('cidades', $cidades);

            $exibe_hora = 0;
            if($this->data["Atestado"]["data_afastamento_periodo"] == $this->data["Atestado"]["data_retorno_periodo"]){
                $exibe_hora = 1;
            } 
            
            $this->set(compact('exibe_hora'));
        }

        $edit_mode = true;

        if(!empty($this->data['Atestado']['codigo_medico'])){
            $dadosMedico = $this->Medico->find('first', array('conditions'=> array('codigo' => $this->data['Atestado']['codigo_medico']), 'recursive' => -1));

            if(!empty($dadosMedico) && !empty($this->data)){
                $this->data = array_merge($this->data, $dadosMedico);            
            }            
        }


        if(!empty($codigo_usuario_grupo_covid)) {//setar quando vier informacoes do grupo covid
            $this->set('codigo_usuario_grupo_covid');
        }
       
        $this->carrega_combos($this->data['Atestado']['tipo_atestado']);
        $this->set('dados_cids', $this->__retornaCids($codigo_atestado));
        $this->set(compact('codigo_cliente_funcionario', 'codigo_atestado', 'edit_mode','codigo_funcionario_setor_cargo'));

    }//FINAL FUNCTION editar
    
  function carrega_combos($tipo_atestado) {

    if(!empty($tipo_atestado) && $tipo_atestado == 2){
        $MotivoAfastamento = $this->MotivoAfastamento->find('list', array('fields' => 'descricao','order' => 'descricao','conditions' => array('codigo_tipo_afastamento IN (3,4)', 'ativo = 1')));
    } else {
        $MotivoAfastamento = $this->MotivoAfastamento->find('list', array('fields' => 'descricao','order' => 'descricao','conditions' => array('codigo_tipo_afastamento = 1', 'ativo = 1')));
    }
     
    $TipoLocalAtendimento = $this->TipoLocalAtendimento->find('list', array('fields' => 'descricao', 'order' => 'descricao'));

    $estados = $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => 1), 'fields' => array('codigo', 'descricao')));
    $estados[''] = 'UF';
    ksort($estados);

    //pega os dadod da tabela esocial tabela 18
    $motivo_afastamento_esocial = $this->Esocial->carrega_motivo_afastamento_esocial();
    $refactory = array(); //variavel auxiliar
    //varre os dados encontrados na tabela esocial
    foreach ($motivo_afastamento_esocial as $dados) {
      //monta como deve aparecer dentro do combo
      $refactory[$dados['Esocial']['codigo']] = $dados[0]['descricao'];
    }//fim foreach
    //reescreve a variavel do motivo esocial tabela 18
    $motivo_afastamento_esocial = $refactory;

    $this->set(compact('MotivoAfastamento','TipoLocalAtendimento', 'estados', 'motivo_afastamento_esocial'));    
  }//FINAL FUNCTION carrega_combos

  function __retornaCids($codigo_atestado){
    $this->AtestadoCid->bindModel(array(
      'belongsTo' => array(
        'Cid' => array(
          'alias' => 'Cid',
          'foreignKey' => FALSE,
          'type' => 'LEFT',
          'conditions' => 'Cid.codigo = AtestadoCid.codigo_cid'
        ),
      )
    ));

    return $this->AtestadoCid->find('all', array('conditions' => array('AtestadoCid.codigo_atestado' => $codigo_atestado)));
  }//FINAL FUNCTION __retornaCids

  /**
   * Metodo para excluir o atestado
   */ 
  public function excluir($codigo, $codigo_cliente_funcionario, $codigo_funcionario_setor_cargo) {

    $atestado_cid = $this->AtestadoCid->find('all', array('conditions' => 
    array('codigo_atestado' => $codigo), 'fields' => array('codigo')));

    //Remove os registros de Cid vinculados ao atestado
    if(!empty($atestado_cid)){
      foreach($atestado_cid as $cid){
        $this->AtestadoCid->excluir($cid["AtestadoCid"]["codigo"]);
      }
    }

    if($this->Atestado->excluir($codigo)) {
      $this->BSession->setFlash('delete_success');
    } else {
      $this->BSession->setFlash('delete_error');
    }
    $this->redirect(array('action' => 'lista_atestados', $codigo_cliente_funcionario, $codigo_funcionario_setor_cargo));
  }//FINAL FUNCTION excluir

    public function sintetico() {
    
        $this->pageTitle = 'Absenteísmo Sintético';
        
        $filtros = $this->Filtros->controla_sessao($this->data, 'Atestado');
        
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }
    
        $this->data['Atestado'] = $filtros;
        
        $tipos_agrupamento = $this->Atestado->tiposAgrupamento();
        
        $this->set(compact('tipos_agrupamento'));
        $this->carrega_combos_grupo_economico('Atestado');
        $this->carrega_combo_absenteismo();
    }//FINAL FUNCTION sintetico

    public function sintetico_listagem() {
    
        $filtros = $this->Filtros->controla_sessao($this->data, 'Atestado');
    
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {  
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }
        
        $this->data['Atestado'] = $filtros;
        
        $dados = array();        
        $agrupamento = $filtros['agrupamento'];
        
        if (!empty($filtros['codigo_cliente'])) {
        
            $conditions = $this->Atestado->converteFiltrosEmConditions($filtros);
            
            $dados = $this->Atestado->sintetico($agrupamento, $conditions);
        }
        
        $this->set(compact('dados', 'agrupamento'));
    }//FINAL FUNCTION sintetico_listagem

    public function analitico() {
    
        $this->pageTitle = 'Absenteísmo Analítico';
        $this->layout = 'new_window';
        
        $tipos_periodo = array(
            'I' => 'Inclusão',
            'A' => 'Afastamento',
            'R' => 'Retorno'
        );
        
        $filtros = $this->Filtros->controla_sessao($this->data, 'Atestado');
        
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $this->data['Atestado'] = $filtros;
        $this->carrega_combos_grupo_economico('Atestado');
        $this->carrega_combo_absenteismo();
    }//FINAL FUNCTION analitico

    public function analitico_listagem($export = false) {
    
        $filtros = $this->Filtros->controla_sessao($this->data, 'Atestado');
        
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }
        
        $dados = array();
        
        if (!empty($filtros['codigo_cliente'])) {
            
            $conditions = $this->Atestado->converteFiltrosEmConditions($filtros);
            
            if($export){
                $query = $this->Atestado->analitico('sql', compact('conditions'));
                $this->exportAtestados($query);
            }
            
            $dados = $this->Atestado->analitico('all', compact('conditions'));
        }
        
        $this->set(compact('dados'));
    }//FINAL FUNCTION analitico_listagem

    public function exportAtestados($query) {
        
        $dbo = $this->Atestado->getDataSource();
        $dbo->results   = $dbo->rawQuery($query);
        
        ob_clean();
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset= UTF-8");
        header('Content-Disposition: attachment; filename="absenteismo'.date('YmdHis').'.csv"');
        echo Comum::converterEncodingPara('"Empresa";"Unidade";"CNPJ";"Funcionário";"Setor";"Cargo";"Matrícula";"CPF";"RG";"Tipo";Data inclusão atestado";"Data início atestado";"Data final atestado";"Horário Inicial Atestado";"Horário Final Atestado";"Dia da Semana";"Quantidade de dias afastados";"Quantidade de horas afastadas";"Motivo da Licença";"Motivo da Licença (Tabela 18 - e-Social)";"Tipo de acidente de trânsito";"Afastamento decorre de mesmo motivo de afastamento anterior (60 dias)?";"Observação";"Ônus da cessão/requisição";"Ônus da Remuneração";"Renumeração do Cargo";"CNPJ";"Origem da retificação";"Tipo de processo";"Número do processo";Restrição para o retorno";"Nome do médico";"CRM";"UF";"CID10";"Nome CID10";"CNAE";"Descrição CNAE";"Nexo";"Endereço do Funcionário";"Número";"Complemento";"Endereço da Unidade";"Número";"Complemento";"Local de Atendimento";"CEP";"Endereço";"Distância do endereço do funcionário(Km)";"Distância do endereço da unidade(Km)"', 'ISO-8859-1')."\n";

        while ($value = $dbo->fetchRow()) {
            $linha = $value[0]['cliente_razao_social'].';';
            $linha .= $value[0]['unidade_nome_fantasia'].';';
            $linha .= $value[0]['unidade_codigo_documento'].';';
            $linha .= $value[0]['funcionario_nome'].';';
            $linha .= $value[0]['setor_descricao'].';';
            $linha .= $value[0]['cargo_descricao'].';';
            $linha .= $value[0]['cliente_funcionario_matricula'].';';
            $linha .= $value[0]['funcionario_cpf'].';';
            $linha .= $value[0]['funcionario_rg'].';';
            $linha .= $value[0]['tipo_atestado'].';';
            $linha .= AppModel::dbDateToDate($value[0]['atestado_data_inclusao']).';';
            $linha .= AppModel::dbDateToDate($value[0]['atestado_afastamento_periodo']).';';
            $linha .= AppModel::dbDateToDate($value[0]['atestado_data_retorno_periodo']).';';
            $linha .= $value[0]['atestado_hora_afastamento'].';';
            $linha .= $value[0]['atestado_hora_retorno'].';';
            $linha .= Comum::diaDaSemana($value[0]['dia_semana']).';';
            $linha .= $value[0]['atestado_afastamento_em_dias'].';';
            $linha .= $value[0]['atestado_afastamento_em_horas'].';';
            $linha .= $value[0]['motivo_afastamento_descricao'].';';
            $linha .= $value[0]['esocial_descricao'].';';
            $linha .= $value[0]['tipo_acidente_transito'].';';
            $linha .= $value[0]['motivo_afastamento'].';';
            $linha .= $value[0]['observacao'].';';
            $linha .= $value[0]['onus_requisicao'].';';
            $linha .= $value[0]['onus_remuneracao'].';';
            $linha .= $value[0]['renumeracao_cargo'].';';
            $linha .= Comum::formatarDocumento($value[0]['cnpj']).';';
            $linha .= $value[0]['origem_retificacao'].';';
            $linha .= $value[0]['tipo_processo'].';';
            $linha .= $value[0]['numero_processo'].';';
            $linha .= $value[0]['atestado_restricao'].';';
            $linha .= $value[0]['medico_nome'].';';
            $linha .= $value[0]['medico_numero_conselho'].';';
            $linha .= $value[0]['medico_conselho_uf'].';';
            $linha .= $value[0]['cid_codigo_cid10'].';';
            $linha .= $value[0]['cid_descricao'].';';
            $linha .= $value[0]['unidade_cnae'].';';
            $linha .= $value[0]['cnae_unidade_descricao'].';';
            $linha .= $value[0]['nexo'].';';
            $linha .= $value[0]['funcionario_endereco'].';';
            $linha .= $value[0]['funcionario_endereco_numero'].';';
            $linha .= $value[0]['funcionario_end_complemento'].';';
            $linha .= $value[0]['unidade_endereco'].';';
            $linha .= $value[0]['unidade_endereco_numero'].';';
            $linha .= $value[0]['unidade_endereco_complemento'].';';
            $linha .= $value[0]['tipo_local_atend_descricao'].';';
            $linha .= $value[0]['atestado_cep'].';';
            $linha .= $value[0]['atestado_endereco'].';';
            $linha .= $value[0]['distancia_funcionario'].';';
            $linha .= $value[0]['distancia_unidade'];
            $linha .= "\n";
            echo Comum::converterEncodingPara($linha, 'UTF-8');
        }
        die();
    }//FINAL FUNCTION exportAtestados
  
  // TESTE DESEMPENHO SERVICE E CACHE
  // public function carrega_combos_grupo_economico($model) {
  //   $this->loadModel('IthealthService');
    
  //   $codigo_cliente = $this->data[$model]['codigo_cliente'];
  //   $combosGrupoEconomico = $this->IthealthService->carregarCombosGrupoEconomico( $codigo_cliente );

  //   $unidades = $combosGrupoEconomico['unidades'];      
  //   $setores = $combosGrupoEconomico['setores'];     
  //   $cargos = $combosGrupoEconomico['cargos'];
    
  //   unset($combosGrupoEconomico);
  //   $this->set(compact('unidades', 'setores', 'cargos'));
  // }//FINAL FUNCTION carrega_combos_grupo_economico

  public function carrega_combos_grupo_economico($model) {
    $this->loadModel('Cargo');
    $this->loadModel('Setor');
    $this->loadModel('GrupoEconomico');

    $codigo_cliente = $this->data[$model]['codigo_cliente'];

    $unidades = '';
    $setores = '';
    $cargos = '';
    if(!empty($codigo_cliente)) {

      $codigo_cliente = is_array($codigo_cliente) ? $codigo_cliente : $this->normalizaCodigoCliente($codigo_cliente);
      if(!$this->GrupoEconomico->verificaMatriz($codigo_cliente[0]) && !empty($codigo_cliente)){
        $codigo_cliente = $this->GrupoEconomicoCliente->getCodigoGrupoEconomico($codigo_cliente);
      }
      
      $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);      
      $setores = $this->Setor->lista($codigo_cliente);     
      $cargos = $this->Cargo->lista($codigo_cliente);
    }
    
    $this->set(compact('unidades', 'setores', 'cargos'));
  }//FINAL FUNCTION carrega_combos_grupo_economico

  public function carrega_combo_absenteismo() {
    $tipos_periodo = array(
      'I' => 'Inclusão',
      'A' => 'Afastamento',
      'R' => 'Retorno'
    );
    $this->set(compact('tipos_periodo'));
  }//FINAL FUNCTION carrega_combo_absenteismo

  public function upload_anexo_atestado($codigo_atestado){

    if($this->RequestHandler->isPost()) {
      $retorno = 0;
      if(!empty($_FILES['data']['name']['Atestado']['anexo_atestado'])){
        $nome_arquivo = $_FILES['data']['name']['Atestado']['anexo_atestado'];
      
        if (strpos($nome_arquivo, ".pdf") > 0 || strpos($nome_arquivo, ".jpg") > 0 || strpos($nome_arquivo, ".png") > 0){
          $retorno = $this->salvar_anexo($codigo_atestado);
        
        } else{
          //Se o arquivo não possui a extensão correta
          $this->BSession->setFlash(array(MSGT_ERROR, 'Somente as seguintes extensões são permitidas: JPG,PNG ou PDF. Tente Novamente.'));
          $this->redirect(Router::url($this->referer(), true));

        }//if valida extensão
      }//if valida arquivo anexo

      if(!$retorno){
        $this->BSession->setFlash('save_error');
        $this->redirect(Router::url($this->referer(), true));
      } else {
        $this->BSession->setFlash('save_success');
        $this->redirect(Router::url($this->referer(), true));
      }

    //Se não for POST
    } else {
     $this->set(compact('codigo_atestado'));
    } 
  }

  public function salvar_anexo($codigo_atestado) {

      $this->loadModel('AnexoAtestado');
      $retorno = 0;

      //Se nenhum arquivo foi anexado
      if(empty($_FILES['data']['name']['Atestado']['anexo_atestado'])){
        return $retorno;        
      }
      
      $nome_arquivo =  strtolower($_FILES['data']['name']['Atestado']['anexo_atestado']);

      preg_match("/(\..*){1}$/i", $nome_arquivo, $ext);
      if (strpos($nome_arquivo, ".pdf") > 0 || strpos($nome_arquivo, ".jpg") > 0 || strpos($nome_arquivo, ".png") > 0){

        //Cria o diretório do atestado se não existe
        if(!is_dir(DIR_ANEXOS_ATESTADOS.$codigo_atestado.DS)){
          mkdir(DIR_ANEXOS_ATESTADOS.$codigo_atestado.DS);
        }  

        $arquivo_anexo = 'atestado_'.$codigo_atestado.'_'.date('dmYHi').$ext[0];
        $destino = DIR_ANEXOS_ATESTADOS.DS.$codigo_atestado.DS.$arquivo_anexo;

        $caminho_completo = end(glob(DIR_ANEXOS_ATESTADOS.$codigo_atestado.DS.'atestado_'.$codigo_atestado.'*'));

        //Apaga os arquivos existentes
        if (is_file($caminho_completo)){
           unlink($caminho_completo);
        } 
          
        if(move_uploaded_file($_FILES['data']['tmp_name']['Atestado']['anexo_atestado'],$destino)){
          $dados_anexo = array();

          $anexo = $this->AnexoAtestado->find('first',array('conditions' => array('codigo_atestado' => $codigo_atestado)));

          $dados_anexo['AnexoAtestado'] = array(
                            'codigo_atestado' => $codigo_atestado,
                            'caminho_arquivo' => $codigo_atestado.DS.$arquivo_anexo,
                        );

          //Se já existe registro, atualiza
          if(!empty($anexo)){
            $dados_anexo['AnexoAtestado']['codigo'] = $anexo['AnexoAtestado']['codigo'];
            $resultado =  $this->AnexoAtestado->atualizar($dados_anexo);
            $retorno = 1;
          } else {
            //Se não existe inclui
            $resultado = $this->AnexoAtestado->incluir($dados_anexo['AnexoAtestado']);
            $retorno = 1;
          }
        }//if arquivo movido corretamente
      }//if extensão
      return $retorno;
  }

    public function excluir_anexo($codigo_atestado) {

        $this->loadModel('AnexoAtestado');
        $sucesso = 0;

        if(isset($codigo_atestado)) {   
            $caminho_completo = end(glob(DIR_ANEXOS_ATESTADOS.$codigo_atestado.DS.'atestado_'.$codigo_atestado.'*'));

            if (is_file($caminho_completo)) {
                //Se o arquivo foi removido
                if(unlink($caminho_completo)) {
                    $codigo_anexo_atestado = $this->AnexoAtestado->find('first',array('conditions' => array('codigo_atestado' => $codigo_atestado),'fields' => array('codigo')));

                    if(!empty($codigo_anexo_atestado['AnexoAtestado']['codigo'])){
                        if($this->AnexoAtestado->delete($codigo_anexo_atestado['AnexoAtestado']['codigo'])){
                            $sucesso = 1;
                            $this->BSession->setFlash('delete_success');
                            $this->redirect(Router::url($this->referer(), true));
                        }
                    }
                }
            }
            else {
                $codigo_anexo_atestado = $this->AnexoAtestado->find('first',array('conditions' => array('codigo_atestado' => $codigo_atestado),'fields' => array('codigo','caminho_arquivo')));

                if(!empty($codigo_anexo_atestado['AnexoAtestado']['codigo'])){
                    if(strstr($atestado['AnexoAtestado']['caminho_arquivo'],'https://api.rhhealth.com.br')) {
                        if($this->AnexoAtestado->delete($codigo_anexo_atestado['AnexoAtestado']['codigo'])){
                            $sucesso = 1;
                            $this->BSession->setFlash('delete_success');
                            $this->redirect(Router::url($this->referer(), true));
                        }
                    }
                }
            }

        }

        if(!$sucesso){
            $this->BSession->setFlash('delete_error');
            $this->redirect(Router::url($this->referer(), true));
        }
    }


  public function listagem_log_anexo($codigo_atestado){
      //titulo da pagina
      $this->pageTitle = 'Log de Anexo';
      $this->layout = 'new_window';

      $this->loadModel('AnexoAtestadoLog');
      $this->loadModel('Atestado');


      
      //campos
      $fields = array(
        'AnexoAtestadoLog.codigo_atestado',
        'AnexoAtestadoLog.caminho_arquivo',
        'AnexoAtestadoLog.data_inclusao',
        'AnexoAtestadoLog.data_alteracao',
        'UsuarioInclusao.nome',
        'UsuarioAlteracao.nome',
        'AnexoAtestadoLog.acao_sistema',
        );

      //relacionamentos
      $joins = array(
          array(
              'table' => 'Rhhealth.dbo.usuario',
              'alias' => 'UsuarioInclusao',
              'type' => 'LEFT',
              'conditions' => 'AnexoAtestadoLog.codigo_usuario_inclusao = UsuarioInclusao.codigo',
          ),
          array(
              'table' => 'Rhhealth.dbo.usuario',
              'alias' => 'UsuarioAlteracao',
              'type' => 'LEFT',
              'conditions' => 'AnexoAtestadoLog.codigo_usuario_alteracao = UsuarioAlteracao.codigo',
          )
         
      );

      //dados do log
      $dados = $this->AnexoAtestadoLog->find('all',array('fields' => $fields, 'conditions' => array('AnexoAtestadoLog.codigo_atestado' => $codigo_atestado), 'joins' => $joins));

      //campos
      $fields_atestados = array(
        'Atestado.codigo',
        'Atestado.data_inclusao',
        'Atestado.data_alteracao',
        'UsuarioInclusao.nome',
        'UsuarioAlteracao.nome',
        );

      //relacionamentos
      $joins_atestado = array(
          array(
              'table' => 'Rhhealth.dbo.usuario',
              'alias' => 'UsuarioInclusao',
              'type' => 'LEFT',
              'conditions' => 'Atestado.codigo_usuario_inclusao = UsuarioInclusao.codigo',
          ),
          array(
              'table' => 'Rhhealth.dbo.usuario',
              'alias' => 'UsuarioAlteracao',
              'type' => 'LEFT',
              'conditions' => 'Atestado.codigo_usuario_alteracao = UsuarioAlteracao.codigo',
          )
         
      );

      //dados do atestado
      $dados_atestado = $this->Atestado->find('all', array('conditions' => array('codigo' => $codigo_atestado)));
      $dados_atestado_user = $this->Atestado->find('all',array('fields' => $fields_atestados, 'conditions' => array('Atestado.codigo' => $codigo_atestado), 'joins' => $joins_atestado));

     
      $dados_atestado[0]['Atestado']['atestado_usuario_inclusao'] = $dados_atestado_user[0]['UsuarioInclusao']['nome'];
      $dados_atestado[0]['Atestado']['atestado_usuario_alteracao'] = $dados_atestado_user[0]['UsuarioAlteracao']['nome'];

      // die(debug($dados_atestado));
      //tipos de acoes
      $acoes = array('0' => "Inclusão", "1" => "Atualização", "2" => "Exclusão");

      // pr($dados);exit;

      $this->set(compact('dados', 'dados_atestado', 'codigo_atestado','acoes'));
  }

  public function get_motivo_licenca($tipo_atestado, $codigo_atestado = null){
    $this->autoRender = false;
    
    if($tipo_atestado == 2){
        $MotivoAfastamento = $this->MotivoAfastamento->find('list', array('fields' => 'descricao','order' => 'descricao','conditions' => array('codigo_tipo_afastamento IN (3,4)', 'ativo = 1')));
    } else {
        $MotivoAfastamento = $this->MotivoAfastamento->find('list', array('fields' => 'descricao','order' => 'descricao','conditions' => array('codigo_tipo_afastamento = 1', 'ativo = 1')));
    }

    if(!empty($codigo_atestado)){
        $atestadoSearch = $this->Atestado->find('first', array('codigo' => $codigo_atestado));            
        $MotivoAfastamento['codigo_motivo_licenca'] = $atestadoSearch['Atestado']['codigo_motivo_licenca'];
    }

    echo json_encode($MotivoAfastamento);
  }


  /**
     * Task PC-3179 [editar description]
     * 
     * metodo para buscar se houve algum afastamento no mesmo período na base do funcionario
     * 
     * @param  [type] $codigo_cliente_funcionario     [description]
     * @param  [type] $data_inicio                    [description]
     * @param  [type] $data_fim                       [description]
     * @param  [type] $hora_inicio                    [description]
     * @param  [type] $hora_fim                       [description]
     * @return [type]                                 [description]
     */
    public function busca_conflito_atestado($codigo_cliente_funcionario, $data_inicio, $data_fim = null, $hora_inicio=null, $hora_fim=null)
    {
      $this->autoRender = false;
      //pega a data atual e a hora atual e busca na base se tem algum atestado conflitante
      $end_date = $data_fim;
      $start_date = $data_inicio;
      if(!empty($hora_inicio)){$hora_inicio = str_replace(':', ':00:00.0000000', $hora_inicio);}
      if(!empty($hora_fim)){$hora_inicio = str_replace(':', ':00:00.0000000', $hora_fim);}

      if(!empty($hora_inicio) || !empty($hora_fim)){

          $atestado_inclusao = $this->Atestado->find('first', array(
            'conditions' => array(
              'data_afastamento_periodo BETWEEN ? and ?' => array($start_date, $end_date),
              'data_retorno_periodo BETWEEN ? and ?' => array($start_date, $end_date),
              'hora_afastamento BETWEEN ? and ?' => array($hora_inicio, $hora_fim),
              'hora_retorno BETWEEN ? and ?' => array($hora_inicio, $hora_fim),
              'codigo_cliente_funcionario' => $codigo_cliente_funcionario,
            )
          ));
        
        }else{

          //query da busca do afastamento do funcionario
          $atestado_inclusao = $this->Atestado->find('first', array(
            'conditions' => array(
              'data_afastamento_periodo BETWEEN ? and ?' => array($start_date, $end_date),
              'data_retorno_periodo BETWEEN ? and ?' => array($start_date, $end_date),
              'codigo_cliente_funcionario' => $codigo_cliente_funcionario,
            )
          ));
        }

      $return = (!empty($atestado_inclusao['Atestado']['codigo_cliente_funcionario']) ? 1 : 0);      
      return json_encode(array('return' => $return));
    }//fim buscar afastamento anterior
    


}//FINAL CLASS AtestadosController