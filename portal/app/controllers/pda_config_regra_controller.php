<?php
class PdaConfigRegraController extends AppController {
    public $name = 'PdaConfigRegra';
    public $components = array('Filtros','RequestHandler','Upload');
    
    var $uses = array(
        'GrupoEconomico',
        'Cliente',
        'ClienteBu',
        'ClienteOpco',
        'AcoesMelhoriasStatus',
        'PosCriticidade',
        'PosFerramenta',
        'OrigemFerramenta',
        'PdaConfigRegra',
        'PdaConfigRegraCondicao',
        'PdaConfigRegraAcao',
        'PdaTema',
        'PdaTemaCondicao',
        'PdaTemaAcoes',
        'PdaTemaPdaTemaAcoes',
        'GrupoEconomicoCliente',
        'ItemPedidoExame',
        'FichaClinica',
        'Configuracao',
        'PosSwtFormTitulo',
        'PosSwtFormQuestao',
        'Setor',
        'CentroResultado'
    );
    
    /**
     * beforeFilter callback
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow(array('*'));
    }
    
    /**
     * [index_form description]
     * @return [type] [description]
     */
    public function index_pda_regra() 
    {
        $this->pageTitle = 'Regras da Ação';
        //pega os filtro do controla sessao
        $filtros = $this->Filtros->controla_sessao($this->data, $this->PdaConfigRegra->name);
        
        $this->data['PdaConfigRegra'] = $filtros;

        $codigo_cliente = (isset($this->data['PdaConfigRegra']['codigo_cliente']) ? $this->data['PdaConfigRegra']['codigo_cliente'] : '');

        if(!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])){
            $cliente = $this->Cliente->find('first',array('conditions' => array('codigo' => $_SESSION['Auth']['Usuario']['codigo_cliente'])));
            $nome_cliente = $cliente['Cliente']['razao_social'];

            $codigo_cliente = $_SESSION['Auth']['Usuario']['codigo_cliente'];
            $this->set(compact('nome_cliente'));
        }

        $pos_ferramenta = $this->PosFerramenta->find('list',array('fields' => array('codigo','descricao')));

        $this->set(compact('codigo_cliente','pos_ferramenta'));


    } // fim metodo 
    
    /**
     * [index_qtd_participantes description]
     * @return [type] [description]
     */
    public function listagem_pda_regra() 
    {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->PdaConfigRegra->name);
        // debug($filtros);

        $this->authUsuario = $this->BAuth->user();
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $codigo_cliente = '';
        $dados_config = array();
        if(!empty($filtros['codigo_cliente'])) {
            
            $codigo_cliente = (is_array($filtros['codigo_cliente'])) ? implode(',',$filtros['codigo_cliente']) : $filtros['codigo_cliente'];
        

            $fields = array('Cliente.codigo','Cliente.razao_social','Cliente.nome_fantasia','PdaConfigRegra.codigo','PdaConfigRegra.descricao','PdaTema.descricao','PosFerramenta.descricao');
            $joins = array(
                array(
                    'table' => 'cliente',
                    'alias' => 'Cliente',
                    'type' => 'INNER',
                    'conditions' => array('PdaConfigRegra.codigo_cliente = Cliente.codigo')
                ),
                array(
                    'table' => 'pda_tema',
                    'alias' => 'PdaTema',
                    'type' => 'INNER',
                    'conditions' => array('PdaConfigRegra.codigo_pda_tema = PdaTema.codigo')
                ),
                array(
                    'table' => 'pos_ferramenta',
                    'alias' => 'PosFerramenta',
                    'type' => 'INNER',
                    'conditions' => array('PdaTema.codigo_pos_ferramenta = PosFerramenta.codigo')
                ),
                array(
                    'table' => 'grupos_economicos',
                    'alias' => 'GrupoEconomico',
                    'type' => 'INNER',
                    'conditions' => array('GrupoEconomico.codigo_cliente = Cliente.codigo')
                ),
            );

            $conditions['Cliente.codigo'] = $filtros['codigo_cliente'];

            if(!empty($filtros['codigo_pos_ferramenta'])) {
                $conditions['PdaTema.codigo_pos_ferramenta'] = $filtros['codigo_pos_ferramenta'];
            }

            $this->paginate['PdaConfigRegra'] = array(
                'fields' => $fields,
                'joins'=> $joins,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => "Cliente.nome_fantasia",
            );

            // debug($this->PdaConfigRegra->find('sql',$this->paginate['PdaConfigRegra']));exit;

            //executa com paginação
            $dados_config = $this->paginate('PdaConfigRegra');
        }

        $this->set(compact('dados_config','codigo_cliente'));

    } // fim metodo 
    
    /**
     * [index_qtd_participantes description]
     * @return [type] [description]
     */
    public function incluir_pda_regra($codigo_cliente) 
    {
        $this->pageTitle = 'Incluir Regras da Ação'; 

        //quando clica para salvar
        if(!empty($this->data)) {

            // debug($this->data);exit;
            
            $this->data['PdaConfigRegra']['ativo'] = 1;
            
            if($this->PdaConfigRegra->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'pda_config_regra', 'action' => 'editar_pda_regra', $this->PdaConfigRegra->id));
            }

            $this->BSession->setFlash('save_error');

        }//fim post

        $this->getDadosForm($codigo_cliente);

        $codigo_pda_tema = null;
        $codigo = null;

        $this->set(compact('codigo','codigo_cliente','codigo_pda_tema'));

    } // fim metodo 

    /**
     * [index_qtd_participantes description]
     * @return [type] [description]
     */
    public function editar_pda_regra($codigo) 
    {
        $this->pageTitle = 'Editar Regras da Ação'; 

        //acao de gravar
        if(!empty($this->data)) {

            // debug($this->data);exit;

            // $pda_config_regra = $this->PdaConfigRegra->find('first',array('conditions' => array('codigo'=>$codigo)));
            // $pda_config_regra['PdaConfigRegra']['assunto'] = $this->data['PdaConfigRegra']['assunto'];
            // $pda_config_regra['PdaConfigRegra']['mensagem'] = $this->data['PdaConfigRegra']['mensagem'];

            if($this->PdaConfigRegra->atualizar($this->data)) {

                //deleta as acoes para incluir novamente
                $deleteAcoes = 'DELETE FROM RHHealth.dbo.pda_config_regra_acao WHERE codigo_pda_config_regra = ' . $codigo;
                $this->PdaConfigRegraAcao->query($deleteAcoes);

                //monta as acoes que deve gravar
                $pda_config_regra_acao = array(
                    'PdaConfigRegraAcao' => array(
                        'codigo_pda_config_regra' => $codigo,
                        'ativo' => 1
                    )
                );
                
                //verfica se tem os temas selecionados
                if(!empty($this->data['codigo_pda_tema_acoes']) && is_array($this->data['codigo_pda_tema_acoes'])) {
                    
                    foreach($this->data['codigo_pda_tema_acoes'] AS $tema_acoes) {
                        
                        $pda_config_regra_acao['PdaConfigRegraAcao']['codigo_pda_tema_acoes'] = $tema_acoes;

                        if(!empty($this->data['tipo_eventos']) && is_array($this->data['tipo_eventos'])) {
                            foreach($this->data['tipo_eventos'] AS $tipo_evento) {
                                $pda_config_regra_acao['PdaConfigRegraAcao']['tipo_acao'] = $tipo_evento;
                                $pda_config_regra_acao['PdaConfigRegraAcao']['email'] = null;

                                if($tipo_evento == 4) {
                                    $pda_config_regra_acao['PdaConfigRegraAcao']['email'] = $this->data['PdaConfigRegra']['email'];
                                }

                                $this->PdaConfigRegraAcao->incluir($pda_config_regra_acao);

                            }//fim tipo eventos
                        }
                        else { //implementado pois no swt tem um tema que quando configurado vai para todos os participantes
                            $this->PdaConfigRegraAcao->incluir($pda_config_regra_acao);
                        }
                    }//fim foreach acoes
                }//fim validacao acoes
                else {
                    $pda_config_regra_acao['PdaConfigRegraAcao']['codigo_pda_tema_acoes'] = $this->data['PdaConfigRegra']['codigo_pda_tema_acoes'];
                    $pda_config_regra_acao['PdaConfigRegraAcao']['tipo_acao'] = '3'; //regra
                    
                    $this->PdaConfigRegraAcao->incluir($pda_config_regra_acao);
                }

                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'pda_config_regra', 'action' => 'editar_pda_regra', $codigo));

            }//fim atualziar regra


        }//fim acao de gravar

        
        $this->data = $this->PdaConfigRegra->find('first',array('conditions' => array('codigo'=>$codigo)));
        $codigo_cliente = $this->data['PdaConfigRegra']['codigo_cliente'];
        // debug($this->data);exit;
        $codigo_pda_tema = $this->data['PdaConfigRegra']['codigo_pda_tema'];

        $this->getDadosForm($codigo_cliente,$codigo);

        $this->set(compact('codigo','codigo_pda_tema'));

    } // fim metodo editar_form


    /**
     * [getDadosForm metodo para pegar os dados relacionados na tela para montar os combos]
     * @return [type] [description]
     */
    public function getDadosForm($codigo_cliente,$codigo = null)
    {

        $temas = array();
        $tipos_envios = array();
        $tipos_envios_completos = array();

        if(!is_null($codigo)) {

            //pega os dados de condiguracao da regra
            $dados_pda_config_regra = $this->PdaConfigRegra->find('first', array('conditions' => array('codigo' => $codigo)));

            //pega os temas da ferramenta escolhida
            $temas = $this->PdaTema->find('list',array('fields' => array('codigo','descricao'),'conditions' => array('codigo_pos_ferramenta' => $dados_pda_config_regra['PdaConfigRegra']['codigo_pos_ferramenta'])));


            switch ($dados_pda_config_regra['PdaConfigRegra']['codigo_pos_ferramenta']) {
                case '1': //plano de acao
                    $tipos_envios = array(
                        '1' => 'Usuário que cadastrou a ação',
                        '2' => 'Usuário responsável da ação',
                        '3' => 'Gestor Direto',
                        '5' => 'Responsável da Área',
                        '4' => 'Email',
                    );

                    $tipos_envios_completos = array(
                        '1' => 'Usuário que cadastrou a ação',
                        '2' => 'Usuário responsável da ação',
                        '3' => 'Gestor Direto',
                        '6' => 'Gestor Direto 1',
                        '7' => 'Gestor Direto 2',
                        '8' => 'Gestor Direto 3',
                        '9' => 'Gestor Direto 4',
                        '10' => 'Gestor Direto 5',
                        '5' => 'Responsável da Área',
                        '4' => 'Email',
                    );

                    
                    $acoes_implantacao = $this->PdaTemaAcoes->getTemaAcoes(3);
                    $acoes_cancelamento = $this->PdaTemaAcoes->getTemaAcoes(6);
                    break;
                case '2': // swt
                
                    $tipos_envios = array(
                        '1' => 'Usuário que cadastrou o Walk Talk',
                        '2' => 'Usuário responsável do Walk Talk',
                        '3' => 'Gestor Direto',
                        '5' => 'Responsável da Área',
                        '4' => 'Email',
                    );

                    $tipos_envios_completos = array(
                        '1' => 'Usuário que cadastrou o Walk Talk',
                        '2' => 'Usuário responsável do Walk Talk',
                        '3' => 'Gestor Direto',
                        '6' => 'Gestor Direto 1',
                        '7' => 'Gestor Direto 2',
                        '8' => 'Gestor Direto 3',
                        '9' => 'Gestor Direto 4',
                        '10' => 'Gestor Direto 5',
                        '5' => 'Responsável da Área',
                        '4' => 'Email',
                    );

                    //pega as acoes 
                    // $acoes_melhoria = $this->PdaTemaAcoes->getTemaAcoes(8);
                    // $acoes_implantacao = $this->PdaTemaAcoes->getTemaAcoes(9);
                    // $acoes_cancelamento = $this->PdaTemaAcoes->getTemaAcoes(10);
                    
                case '3': // obs
                    
                    $tipos_envios = array(
                        '1' => 'Usuário que identificou a observação',
                        '2' => 'Usuário responsável pela análise de qualidade',
                        '3' => 'Gestor Direto',
                        '5' => 'Responsável da Área',
                        '4' => 'Email',
                    );

                    $tipos_envios_completos = array(
                        '1' => 'Usuário que identificou a observação',
                        '2' => 'Usuário responsável pela análise de qualidade',
                        '3' => 'Gestor Direto',
                        '6' => 'Gestor Direto 1',
                        '7' => 'Gestor Direto 2',
                        '8' => 'Gestor Direto 3',
                        '9' => 'Gestor Direto 4',
                        '10' => 'Gestor Direto 5',
                        '5' => 'Responsável da Área',
                        '4' => 'Email',
                    );


                    break;
            }//fim switch

        }//fim codigo de edicao


        //busca os produtos pda/swt/obs que tem contratado
        $produtos_contratados = array();
        if($this->Configuracao->get_produto_assinatura($codigo_cliente,'PLANO_DE_ACAO')) {
            $produtos_contratados[] = 1;
        }
        if($this->Configuracao->get_produto_assinatura($codigo_cliente,'SAFETY_WALK_TALK')) {
            $produtos_contratados[] = 2;
        }
        if($this->Configuracao->get_produto_assinatura($codigo_cliente,'OBSERVADOR_EHS')) {
            $produtos_contratados[] = 3;
        }

        $codigos_ferramentas_contratadas = implode(',',$produtos_contratados);
        //pega as ferramentas
        $pos_ferramenta = array();
        if(!empty($codigos_ferramentas_contratadas)) {
            $pos_ferramenta = $this->PosFerramenta->find('list',array('fields' => array('codigo','descricao'),'conditions' => array("codigo IN ({$codigos_ferramentas_contratadas})")));
        }

        $status = $this->AcoesMelhoriasStatus->find('list',array('fields' => array('codigo','descricao'), 'conditions' => array('ativo' => 1, 'codigo IN (1,3,5,6,8)')));

        //pega as acoes 
        $acoes_melhoria = $this->PdaTemaAcoes->getTemaAcoes(1);
        

        $acoes_checked = array();
        $tipos_checked = array();
        $email_checked = '';
        if(!empty($codigo)) {
            //pega o resultado da acao
            $acoes = $this->PdaConfigRegraAcao->find('all',array('conditions' => array('codigo_pda_config_regra' => $codigo)));
            if(!empty($acoes)) {
                foreach($acoes AS $dAcoes) {
                    $this->data['codigo_pda_tema_acoes'][$dAcoes['PdaConfigRegraAcao']['codigo_pda_tema_acoes']] = $dAcoes['PdaConfigRegraAcao']['codigo_pda_tema_acoes'];
                    $this->data['PdaConfigRegra']['codigo_pda_tema_acoes'] = $dAcoes['PdaConfigRegraAcao']['codigo_pda_tema_acoes'];
                    $this->data['tipo_eventos'][$dAcoes['PdaConfigRegraAcao']['tipo_acao']] = $dAcoes['PdaConfigRegraAcao']['tipo_acao'];
                    $email_checked = $dAcoes['PdaConfigRegraAcao']['email'];
                }
            }
        }

        // debug($this->data);exit;

        $this->set(compact('temas','status','acoes_melhoria','acoes_implantacao','acoes_cancelamento','tipos_envios','acoes_checked','tipos_checked','email_checked','tipos_envios_completos','pos_ferramenta'));

    }//fim getDadosForm

    /**
     * [combo_tema metodo para pegar os temas da ferramenta]
     * @param  [type] $codigo_pos_ferramenta [description]
     * @return [type]                        [description]
     */
    public function combo_tema($codigo_pos_ferramenta)
    {
        $this->layout = 'ajax';

        $html = "<option value=''>Selecione um tema</option>";
        //verfica se o param existe
        if(!empty($codigo_pos_ferramenta)) {

            //pega os dados do tema aplicado
            $temas = $this->PdaTema->find('list',array('fields' => array('codigo','descricao'),'conditions' => array('codigo_pos_ferramenta' => $codigo_pos_ferramenta)));

            foreach ($temas as $key => $value) {
                $html .= "<option value='{$key}'>{$value}</option>";
            }
        }

        echo $html;
        $this->render(false,false);

    }//fim combo_tema

    /**
     * [get_combo_questoes_swt]
     * @param  [type] $codigo_pos_swt_form_titulo [description]
     * @return [type]                        [description]
     */
    public function get_combo_questoes_swt($codigo_pos_swt_form_titulo)
    {

        $this->layout = 'ajax';

        $html = "<option value=''>Selecione uma questão</option>";
        //verfica se o param existe
        if(!empty($codigo_pos_swt_form_titulo)) {

            //pega os dados de questões do titulo
            $questoes = $this->PosSwtFormQuestao->find('list',array('fields' => array('codigo','questao'),'conditions' => array('codigo_form_titulo' => $codigo_pos_swt_form_titulo,'ativo'=> 1 ),'order' => array('ordem ASC')));

            foreach ($questoes as $key => $value) {
                $html .= "<option value='{$key}'>{$value}</option>";
            }
        }

        echo $html;

        $this->render(false,false);

    }//fim get_combo_questoes_swt

    /**
     * [modal_condicoes description]
     * @return [type] [description]
     */
    public function modal_condicoes($codigo,$codigo_cliente,$codigo_tema,$codigo_status,$codigo_pda_config_regra_condicao=null)
    {
        //unidades
        $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);

        //pega a criticidade cadastrada do plano de acao
        $criticidade = $this->PosCriticidade->find('list',array('fields' => array('codigo','descricao'), 'conditions' => array('codigo_cliente' => $codigo_cliente,'ativo' => 1,'codigo_pos_ferramenta' => 1)));

        $status = array(
            '3' => 'Em Andamento',
            '13' => 'A Vencer'
        );

        $status_imp_efi =  array(
            '1' => 'Aguardando análise',
            '2' => 'Análise pendente',
            '3' => 'Em Andamento',
        );

        $origem_ferramentas = $this->OrigemFerramenta->find('list',array('fields' => array('codigo','descricao'), 'conditions' => array('ativo' => 1, 'codigo_cliente' => $codigo_cliente)));

        $cliente_opco = $this->ClienteOpco->find('list',array('fields' => array('codigo','descricao'), 'conditions' => array('ativo' => 1, 'codigo_cliente' => $codigo_cliente)));
        $cliente_bu = $this->ClienteBu->find('list',array('fields' => array('codigo','descricao'), 'conditions' => array('ativo' => 1, 'codigo_cliente' => $codigo_cliente)));

        $condicoes = array(
            '>' => ">",
            '<' => "<",
            '=' => "="
        );

        //pega a edicao caso tenha o codigo_pda_config_regra_condicao
        if(!empty($codigo_pda_config_regra_condicao)) {
            $dados_condicoes = $this->PdaConfigRegraCondicao->find('first',array('conditions' => array('codigo' => $codigo_pda_config_regra_condicao)));
            $this->data = $dados_condicoes['PdaConfigRegraCondicao'];
            // debug($this->data);
        }
        else {
            //joins com pda_config_regra com o tema e o status do tema
            $joins = array(
                array(
                    'table' => 'pda_config_regra',
                    'alias' => 'PdaConfigRegra',
                    'type' => 'INNER',
                    'conditions' => array('PdaConfigRegra.codigo = PdaConfigRegraCondicao.codigo_pda_config_regra AND PdaConfigRegra.codigo_pda_tema = 1 AND PdaConfigRegra.codigo_acoes_melhorias_status <> 3')
                ),
            );

            $dados_condicoes = $this->PdaConfigRegraCondicao->find('all',array('joins' => $joins,'conditions' => array('PdaConfigRegraCondicao.codigo_pda_config_regra' => $codigo, 'PdaConfigRegraCondicao.ativo' => 1)));
            
            if (!empty($dados_condicoes)) {
                foreach($dados_condicoes AS $dCond) {
                    $this->data['codigo_pos_criticidade_'.$dCond['PdaConfigRegraCondicao']['codigo_pos_criticidade']] = $dCond['PdaConfigRegraCondicao']['codigo_pos_criticidade'];
                }
            }
        }
        
        $this->set(compact('codigo','codigo_cliente','codigo_tema','codigo_status','status','status_imp_efi','criticidade','origem_ferramentas','cliente_opco','cliente_bu','condicoes','codigo_pda_config_regra_condicao','unidades'));
    }//fim modal_condicoes

    /**
     * [modal_condicoes_swt moda para realizar o cadastro das condicoes solicitadas para o waltk & talk]
     * @return [type] [description]
     */
    public function modal_condicoes_swt($codigo,$codigo_cliente,$codigo_tema,$codigo_pda_config_regra_condicao=null)
    {       
        $titulo_swt = array();
        $questoes_swt = array();
        $criticidade = array();

        $unidades = array();
        $setores = array();
        $cliente_opco = array();
        $cliente_bu = array();

        //verifica qual é o tema
        if($codigo_tema == 9) { //notificar de acordo com a criticidade

            //pega o titulo e perguntas do formulario de swt
            $dados = $this->PosSwtFormTitulo->find('all',array(
                'fields' => array(
                    'PosSwtFormTitulo.codigo AS codigo',
                    "CONCAT(PosSwtFormTitulo.ordem,' - ',PosSwtFormTitulo.titulo) AS titulo"
                ), 
                'conditions' => array('PosSwtFormTitulo.codigo_form' => 1,'PosSwtFormTitulo.ativo' => 1),
                'order'=>array('PosSwtFormTitulo.ordem ASC','PosSwtFormTitulo.codigo ASC')));

            if(!empty($dados)) {
                foreach ($dados as $value_tit) {
                    $titulo_swt[$value_tit[0]['codigo']] = $value_tit[0]['titulo'];
                }
            }

            //pega a criticidade cadastrada do plano de acao
            $criticidade = $this->PosCriticidade->find('list',array('fields' => array('codigo','descricao'), 'conditions' => array('codigo_cliente' => $codigo_cliente,'ativo' => 1,'codigo_pos_ferramenta' => 2)));

        }
        else if($codigo_tema == 10) { // follow-up

            //unidades
            $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
            $setores = $this->Setor->lista($codigo_cliente);

            $cliente_opco = $this->ClienteOpco->find('list',array('fields' => array('codigo','descricao'), 'conditions' => array('ativo' => 1, 'codigo_cliente' => $codigo_cliente)));
            $cliente_bu = $this->ClienteBu->find('list',array('fields' => array('codigo','descricao'), 'conditions' => array('ativo' => 1, 'codigo_cliente' => $codigo_cliente)));
        }
        
        //pega a edicao caso tenha o codigo_pda_config_regra_condicao
        if (!empty($codigo_pda_config_regra_condicao)) {
            $dados_condicoes = $this->PdaConfigRegraCondicao->find('first',array('conditions' => array('codigo' => $codigo_pda_config_regra_condicao)));

            if ($codigo_tema == 9) { //notificar de acordo com a criticidade
                //pega o codigo do titulo salvo para pegar as questoes
                if(!empty($dados_condicoes)) {
                    $questoes_swt = $this->PosSwtFormQuestao->find('list',array('fields' => array('codigo','questao'),'conditions' => array('codigo_form_titulo' => $dados_condicoes['PdaConfigRegraCondicao']['codigo_pos_swt_form_titulo'],'ativo'=> 1 ),'order' => array('ordem ASC')));
                }
            }

            $this->data = $dados_condicoes['PdaConfigRegraCondicao'];
        }
        
        $this->set(compact('codigo','codigo_cliente','codigo_tema','codigo_status','status','status_imp_efi','criticidade','origem_ferramentas','cliente_opco','cliente_bu','condicoes','codigo_pda_config_regra_condicao','unidades','titulo_swt','questoes_swt','setores'));
    }//fim modal_condicoes_swt


    /**
     * [modal_condicoes_obs moda para realizar o cadastro das condicoes solicitadas para o waltk & talk]
     * @return [type] [description]
     */
    public function modal_condicoes_obs($codigo,$codigo_cliente,$codigo_tema,$codigo_pda_config_regra_condicao=null)
    {
        $criticidade = array();
        $tipo_sla = array();
        $unidades = array();
        $setores = array();
        $cliente_opco = array();
        $cliente_bu = array();

        //verifica qual é o tema
        if ($codigo_tema == 12) { //Notificar de acordo com a criticidade
            //pega a criticidade cadastrada do plano de acao
            $criticidade = $this->PosCriticidade->find('list',array('fields' => array('codigo','descricao'), 'conditions' => array('codigo_cliente' => $codigo_cliente,'ativo' => 1,'codigo_pos_ferramenta' => 3)));
        } else if ($codigo_tema == 13) { // Observações em atraso de tratativa
            $tipo_sla = array(
                '1' => "SLA Padrão",
                '2' => "SLA Personalizado"
            );

            //unidades
            $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
            
            $cliente_opco = $this->ClienteOpco->find('list',array('fields' => array('codigo','descricao'), 'conditions' => array('ativo' => 1, 'codigo_cliente' => $codigo_cliente)));
            $cliente_bu = $this->ClienteBu->find('list',array('fields' => array('codigo','descricao'), 'conditions' => array('ativo' => 1, 'codigo_cliente' => $codigo_cliente)));
        }
        
        //pega a edicao caso tenha o codigo_pda_config_regra_condicao
        // if(!empty($codigo_pda_config_regra_condicao)) {
        //     $dados_condicoes = $this->PdaConfigRegraCondicao->find('first',array('conditions' => array('codigo' => $codigo_pda_config_regra_condicao)));

        //     if($codigo_tema == 9) { //notificar de acordo com a criticidade
        //         //pega o codigo do titulo salvo para pegar as questoes
        //         if(!empty($dados_condicoes)) {
        //             $questoes_swt = $this->PosSwtFormQuestao->find('list',array('fields' => array('codigo','questao'),'conditions' => array('codigo_form_titulo' => $dados_condicoes['PdaConfigRegraCondicao']['codigo_pos_swt_form_titulo'],'ativo'=> 1 ),'order' => array('ordem ASC')));
        //         }
        //     }

        //     $this->data = $dados_condicoes['PdaConfigRegraCondicao'];
        //     // debug($this->data);
        // }
        
        $this->set(compact('codigo','codigo_cliente','codigo_tema','codigo_status','status','status_imp_efi','criticidade','origem_ferramentas','cliente_opco','cliente_bu','condicoes','codigo_pda_config_regra_condicao','unidades','setores','tipo_sla'));
    }//fim modal_condicoes_obs

    /**
     * pega o valor do sla configurado em tela "Observador->Operação Terceiros->Configurações Observador EHS"
     */
    public function get_tipo_sla($codigo_cliente)
    {

        $valor_sla = $this->PdaConfigRegraCondicao->get_pos_configuracoes($codigo_cliente);

        $this->layout = 'ajax';

        
        //verfica se o param existe
        $valor = 0;
        if(!empty($valor_sla)) {
            $valor = $valor_sla[0][0]['valor'];
        }

        echo $valor;

        $this->render(false,false);


    }//fim get_tipo_sla


    public function salvar_condicoes()
    {
         $this->layout = 'ajax';

        // debug($this->params['form']);exit;

        //parametros passados
        $codigo_cliente = $this->params['form']['codigo_cliente'];
        $codigo_pda_config_regra = $this->params['form']['codigo'];

        $codigo_pda_config_regra_condicao = $this->params['form']['codigo_pda_config_regra_condicao'];
        
        $arr_obj_pos_criticidade = (isset($this->params['form']['arr_obj_pos_criticidade'])) ? $this->params['form']['arr_obj_pos_criticidade'] : null;

        $tipo_sla = (isset($this->params['form']['tipo_sla'])) ? $this->params['form']['tipo_sla'] : null;

        $qtd_dias = (isset($this->params['form']['qtd_dias'])) ? $this->params['form']['qtd_dias'] : null;
        $codigo_pos_criticidade = (isset($this->params['form']['codigo_pos_criticidade'])) ? $this->params['form']['codigo_pos_criticidade'] : null;
        $codigo_origem_ferramentas = (isset($this->params['form']['codigo_origem_ferramentas'])) ? $this->params['form']['codigo_origem_ferramentas'] : null;
        $codigo_cliente_unidade = (isset($this->params['form']['codigo_cliente_unidade'])) ? $this->params['form']['codigo_cliente_unidade'] : null;
        $codigo_cliente_opco = (isset($this->params['form']['codigo_cliente_opco'])) ? $this->params['form']['codigo_cliente_opco'] : null;
        $codigo_cliente_bu = (isset($this->params['form']['codigo_cliente_bu'])) ? $this->params['form']['codigo_cliente_bu'] : null;
        $codigo_acoes_melhorias_status = (isset($this->params['form']['codigo_acoes_melhorias_status'])) ? $this->params['form']['codigo_acoes_melhorias_status'] : null;
        $condicao = (isset($this->params['form']['condicao'])) ? $this->params['form']['condicao'] : null;


        $codigo_pos_swt_form_titulo = (isset($this->params['form']['codigo_pos_swt_form_titulo'])) ? $this->params['form']['codigo_pos_swt_form_titulo'] : null;
        $codigo_pos_swt_form_questao = (isset($this->params['form']['codigo_pos_swt_form_questao'])) ? $this->params['form']['codigo_pos_swt_form_questao'] : null;
        $codigo_setor = (isset($this->params['form']['codigo_setor'])) ? $this->params['form']['codigo_setor'] : null;


        $return = 0;
        if(!empty($codigo_pda_config_regra) && !empty($codigo_cliente)) {

            $dados_pda_config_regra_condicao = array(
                'PdaConfigRegraCondicao' => array(
                    'codigo_cliente' => $codigo_cliente,
                    'codigo_pda_config_regra' => $codigo_pda_config_regra,
                    'ativo' => 1,
                )
            );


            //verifica se tem arr_obj_pos_criticidade
            if(!empty($arr_obj_pos_criticidade)) {
                
                //inativa todos os ids
                $query = "UPDATE pda_config_regra_condicao SET ativo = 0 WHERE codigo_pda_config_regra = " . $codigo_pda_config_regra; 
                $this->PdaConfigRegraCondicao->query($query);

                //monta array para trabalhar com os ids checkados
                $arr_criticidade = array();
                // varre os codigos de criticidade
                foreach($arr_obj_pos_criticidade AS $val_criticidade) {
                    // $arr_criticidade[$val_criticidade['id']] = $val_criticidade['id'];

                    //para saber se tem que atualizar ou gravar um novo registro
                    $dados_condicoes = $this->PdaConfigRegraCondicao->find('first', array('conditions' => array('codigo_pda_config_regra' => $codigo_pda_config_regra,'codigo_pos_criticidade' => $val_criticidade['id'])));
                    //verifica se tem dados ja cadastrados
                    if(!empty($dados_condicoes)) {
                        $dados_pda_config_regra_condicao['PdaConfigRegraCondicao']['codigo'] = $dados_condicoes['PdaConfigRegraCondicao']['codigo'];
                        $this->PdaConfigRegraCondicao->atualizar($dados_pda_config_regra_condicao);
                    }//fim dados_condicoes
                    else {
                        $dados_pda_config_regra_condicao['PdaConfigRegraCondicao']['codigo_pos_criticidade'] = $val_criticidade['id'];
                        $this->PdaConfigRegraCondicao->incluir($dados_pda_config_regra_condicao);
                    }

                }//fim foreach objetos criticidade

                $return = 1;

            }//fim if criticidade
            else {

                $dados_pda_config_regra_condicao['PdaConfigRegraCondicao']['codigo_pos_criticidade'] = $codigo_pos_criticidade;
                $dados_pda_config_regra_condicao['PdaConfigRegraCondicao']['codigo_origem_ferramentas'] = $codigo_origem_ferramentas;
                $dados_pda_config_regra_condicao['PdaConfigRegraCondicao']['codigo_cliente_unidade'] = $codigo_cliente_unidade;
                $dados_pda_config_regra_condicao['PdaConfigRegraCondicao']['codigo_cliente_opco'] = $codigo_cliente_opco;
                $dados_pda_config_regra_condicao['PdaConfigRegraCondicao']['codigo_cliente_bu'] = $codigo_cliente_bu;
                $dados_pda_config_regra_condicao['PdaConfigRegraCondicao']['codigo_acoes_melhorias_status'] = $codigo_acoes_melhorias_status;
                $dados_pda_config_regra_condicao['PdaConfigRegraCondicao']['condicao'] = $condicao;
                $dados_pda_config_regra_condicao['PdaConfigRegraCondicao']['qtd_dias'] = $qtd_dias;

                $dados_pda_config_regra_condicao['PdaConfigRegraCondicao']['tipo_sla'] = $tipo_sla;

                $dados_pda_config_regra_condicao['PdaConfigRegraCondicao']['codigo_pos_swt_form_titulo'] =  (!empty($codigo_pos_swt_form_titulo)) ? $codigo_pos_swt_form_titulo : null;
                $dados_pda_config_regra_condicao['PdaConfigRegraCondicao']['codigo_pos_swt_form_questao'] = (!empty($codigo_pos_swt_form_questao)) ? $codigo_pos_swt_form_questao : null;

                $dados_pda_config_regra_condicao['PdaConfigRegraCondicao']['codigo_setor'] = $codigo_setor;

                
                //verifica se tem dados ja cadastrados
                if(!empty($codigo_pda_config_regra_condicao)) {
                    $dados_pda_config_regra_condicao['PdaConfigRegraCondicao']['codigo'] = $codigo_pda_config_regra_condicao;
                    $this->PdaConfigRegraCondicao->atualizar($dados_pda_config_regra_condicao);
                }//fim dados_condicoes
                else {
                    $this->PdaConfigRegraCondicao->incluir($dados_pda_config_regra_condicao);
                }

                $return = 1;

            }//fim else

        }//fim validacao codigo e codigo_cliente

        echo $return;
        exit;

    }//fim salvar_condicoes

    public function listagem_condicoes($codigo, $codigo_tema, $codigo_status = null)
    {
        $this->layout = 'ajax';
        
        $dados_condicoes = array();

        if(!empty($codigo)) {
            $fields = array(
                'PdaConfigRegraCondicao.codigo',
                'PdaConfigRegraCondicao.codigo_pda_config_regra',
                'PdaConfigRegraCondicao.codigo_cliente',
                "PdaConfigRegra.codigo_pda_tema",
                "PdaConfigRegra.codigo_acoes_melhorias_status",
                'PdaConfigRegraCondicao.condicao',
                'PdaConfigRegraCondicao.qtd_dias',
                'PdaConfigRegraCondicao.ativo',
                'AcoesMelhoriasStatus.descricao',
                'PosCriticidade.descricao',
                'OrigemFerramenta.descricao',
                'ClienteOpco.descricao',
                'ClienteBu.descricao',
                'ClienteUnidade.codigo',
                'ClienteUnidade.nome_fantasia',
            );

            $joins = array(
                array(
                    'table' => 'pda_config_regra',
                    'alias' => 'PdaConfigRegra',
                    'type' => 'INNER',
                    'conditions' => array('PdaConfigRegra.codigo = PdaConfigRegraCondicao.codigo_pda_config_regra')
                ),
                array(
                    'table' => 'pos_criticidade',
                    'alias' => 'PosCriticidade',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_pos_criticidade = PosCriticidade.codigo')
                ),
                array(
                    'table' => 'acoes_melhorias_status',
                    'alias' => 'AcoesMelhoriasStatus',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_acoes_melhorias_status = AcoesMelhoriasStatus.codigo')
                ),
                array(
                    'table' => 'origem_ferramentas',
                    'alias' => 'OrigemFerramenta',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_origem_ferramentas = OrigemFerramenta.codigo')
                ),
                array(
                    'table' => 'cliente',
                    'alias' => 'ClienteUnidade',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_cliente_unidade = ClienteUnidade.codigo')
                ),
                array(
                    'table' => 'cliente_opco',
                    'alias' => 'ClienteOpco',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_cliente_opco = ClienteOpco.codigo')
                ),
                array(
                    'table' => 'cliente_bu',
                    'alias' => 'ClienteBu',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_cliente_bu = ClienteBu.codigo')
                )
            );

            $conditions['PdaConfigRegraCondicao.codigo_pda_config_regra'] = $codigo;
            $conditions['PdaConfigRegra.codigo_pda_tema'] = $codigo_tema;

            if(!empty($codigo_status)) {
                $conditions['PdaConfigRegra.codigo_acoes_melhorias_status'] = $codigo_status;
            }

            $order = array('PdaConfigRegraCondicao.codigo ASC');

            //executa com paginação
            $dados_condicoes = $this->PdaConfigRegraCondicao->find('all',array(
                'fields' => $fields,
                'joins' => $joins,
                'conditions' => $conditions,
                'order' => $order
            ));
        }

        $this->set(compact('dados_condicoes','codigo','codigo_tema'));
    }//fim lista_condicoes


    public function listagem_condicoes_swt($codigo, $codigo_tema, $codigo_status = null)
    {
        $this->layout = 'ajax';
        
        $dados_condicoes = array();

        if(!empty($codigo)) {
            $fields = array(
                'PdaConfigRegraCondicao.codigo',
                'PdaConfigRegraCondicao.codigo_pda_config_regra',
                'PdaConfigRegraCondicao.codigo_cliente',
                'PdaConfigRegraCondicao.ativo',
                "PdaConfigRegra.codigo_pda_tema",
            );

            $joins = array(
                array(
                    'table' => 'pda_config_regra',
                    'alias' => 'PdaConfigRegra',
                    'type' => 'INNER',
                    'conditions' => array('PdaConfigRegra.codigo = PdaConfigRegraCondicao.codigo_pda_config_regra')
                )
            );

            if ($codigo_tema == 9) {
                $fields[] = 'PosCriticidade.descricao';
                $fields[] = 'PosSwtFormTitulo.titulo';
                $fields[] = 'PosSwtFormQuestao.questao';

                $joins[] = array(
                    'table' => 'pos_criticidade',
                    'alias' => 'PosCriticidade',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_pos_criticidade = PosCriticidade.codigo')
                );
                $joins[] = array(
                    'table' => 'pos_swt_form_titulo',
                    'alias' => 'PosSwtFormTitulo',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_pos_swt_form_titulo = PosSwtFormTitulo.codigo')
                );
                $joins[] = array(
                    'table' => 'pos_swt_form_questao',
                    'alias' => 'PosSwtFormQuestao',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_pos_swt_form_questao = PosSwtFormQuestao.codigo')
                );
            } else if ($codigo_tema == 10) { //follow-up
                $fields[] = 'PdaConfigRegraCondicao.qtd_dias';
                $fields[] = 'ClienteOpco.descricao';
                $fields[] = 'ClienteBu.descricao';
                $fields[] = 'ClienteUnidade.codigo';
                $fields[] = 'ClienteUnidade.nome_fantasia';
                $fields[] = 'Setor.descricao';

                $joins[] = array(
                    'table' => 'cliente',
                    'alias' => 'ClienteUnidade',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_cliente_unidade = ClienteUnidade.codigo')
                );
                $joins[] = array(
                    'table' => 'cliente_opco',
                    'alias' => 'ClienteOpco',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_cliente_opco = ClienteOpco.codigo')
                );
                $joins[] = array(
                    'table' => 'cliente_bu',
                    'alias' => 'ClienteBu',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_cliente_bu = ClienteBu.codigo')
                );
                $joins[] = array(
                    'table' => 'setores',
                    'alias' => 'Setor',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_setor = Setor.codigo')
                );
            }

            $conditions['PdaConfigRegraCondicao.codigo_pda_config_regra'] = $codigo;
            $conditions['PdaConfigRegra.codigo_pda_tema'] = $codigo_tema;

            if(!empty($codigo_status)) {
                $conditions['PdaConfigRegra.codigo_acoes_melhorias_status'] = $codigo_status;
            }

            $order = array('PdaConfigRegraCondicao.codigo ASC');

            //executa com paginação
            $dados_condicoes = $this->PdaConfigRegraCondicao->find('all',array(
                'fields' => $fields,
                'joins' => $joins,
                'conditions' => $conditions,
                'order' => $order
            ));
        }

        $this->set(compact('dados_condicoes','codigo','codigo_tema'));
    }//fim listagem_condicoes_swt


    public function listagem_condicoes_obs($codigo, $codigo_tema, $codigo_status = null)
    {
        $this->layout = 'ajax';
        
        $dados_condicoes = array();
        $tipo_sla = array(
            '' => "Nenhum SLA selecionado",
            '1' => "SLA Padrão",
            '2' => "SLA Personalizado",
        );

        if (!empty($codigo)) {
            $fields = array(
                'PdaConfigRegraCondicao.codigo',
                'PdaConfigRegraCondicao.codigo_pda_config_regra',
                'PdaConfigRegraCondicao.codigo_cliente',
                'PdaConfigRegraCondicao.ativo',
                "PdaConfigRegra.codigo_pda_tema",
            );

            $joins = array(
                array(
                    'table' => 'pda_config_regra',
                    'alias' => 'PdaConfigRegra',
                    'type' => 'INNER',
                    'conditions' => array('PdaConfigRegra.codigo = PdaConfigRegraCondicao.codigo_pda_config_regra')
                )
            );

            if($codigo_tema == 12) { //criticidade
                $fields[] = 'PosCriticidade.descricao';
                
                $joins[] = array(
                    'table' => 'pos_criticidade',
                    'alias' => 'PosCriticidade',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_pos_criticidade = PosCriticidade.codigo')
                );
            } else if ($codigo_tema == 13) { //em atraso
                $fields[] = 'PdaConfigRegraCondicao.qtd_dias';
                $fields[] = 'PdaConfigRegraCondicao.tipo_sla';
                $fields[] = 'ClienteOpco.descricao';
                $fields[] = 'ClienteBu.descricao';
                $fields[] = 'ClienteUnidade.codigo';
                $fields[] = 'ClienteUnidade.nome_fantasia';
                $fields[] = 'Setor.descricao';

                $joins[] = array(
                    'table' => 'cliente',
                    'alias' => 'ClienteUnidade',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_cliente_unidade = ClienteUnidade.codigo')
                );
                $joins[] = array(
                    'table' => 'cliente_opco',
                    'alias' => 'ClienteOpco',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_cliente_opco = ClienteOpco.codigo')
                );
                $joins[] = array(
                    'table' => 'cliente_bu',
                    'alias' => 'ClienteBu',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_cliente_bu = ClienteBu.codigo')
                );
                $joins[] = array(
                    'table' => 'setores',
                    'alias' => 'Setor',
                    'type' => 'LEFT',
                    'conditions' => array('PdaConfigRegraCondicao.codigo_setor = Setor.codigo')
                );
            }

            $conditions['PdaConfigRegraCondicao.codigo_pda_config_regra'] = $codigo;
            $conditions['PdaConfigRegra.codigo_pda_tema'] = $codigo_tema;

            if(!empty($codigo_status)) {
                $conditions['PdaConfigRegra.codigo_acoes_melhorias_status'] = $codigo_status;
            }

            $order = array('PdaConfigRegraCondicao.codigo ASC');

            //executa com paginação
            $dados_condicoes = $this->PdaConfigRegraCondicao->find('all',array(
                'fields' => $fields,
                'joins' => $joins,
                'conditions' => $conditions,
                'order' => $order
            ));
        }

        $this->set(compact('dados_condicoes','codigo','codigo_tema','tipo_sla'));
    }//fim listagem_condicoes_obs


    /**
     * [atualiza_status do modelo]
     * @param  [type] $codigo [description]
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    public function atualiza_status($codigo, $status)
    {
        $this->layout = 'ajax';
        
        $this->data['PdaConfigRegraCondicao']['codigo'] = $codigo;
        $this->data['PdaConfigRegraCondicao']['ativo'] = ($status == "0") ? 1 : 0;
        
        if ($this->PdaConfigRegraCondicao->save($this->data, false)) {   // 0 -> ERRO | 1 -> SUCESSO  
            print 1;
        } else {
            print 0;
        }

        $this->render(false,false);
              
    }

    public function combo_bu_ajax($codigo_cliente)
    {
        $this->layout = 'ajax';

        $cliente_bu = $this->CentroResultado->getCentroResultadoBu($codigo_cliente);

        echo json_encode($cliente_bu);

        $this->render(false,false);        
    }

    public function combo_opco_ajax($codigo_cliente, $codigo_cliente_bu)
    {
        $this->layout = 'ajax';

        $cliente_opco = $this->CentroResultado->getCentroResultadoOpco($codigo_cliente, $codigo_cliente_bu);

        echo json_encode($cliente_opco);

        $this->render(false,false);        
    }

    // /**
    //  * [config_cabecalho metodo para gravar o titulo do formulario]
    //  * @return [type] [description]
    //  */
    // public function confirma_titulo()
    // {
    //     $this->layout = 'ajax';

    //     // debug($this->params['form']);exit;

    //     //parametros passados
    //     $codigo_cliente = $this->params['form']['codigo_cliente'];
    //     $codigo_form = $this->params['form']['codigo_form'];
    //     $titulo = $this->params['form']['titulo'];
    //     $ordem = $this->params['form']['ordem'];

    //     $codigo_form_titulo = $this->params['form']['codigo_form_titulo'];
        
    //     $retorno = array();

    //     //verifica se tem o titulo e o codigo do template
    //     if(!empty($codigo_form) && !empty($titulo)) {

    //         $dados_titulo = array(
    //             'PosSwtFormTitulo' => array(
    //                 'codigo_form' => $codigo_form,
    //                 'codigo_cliente' => $codigo_cliente,
    //                 'titulo' => $titulo,
    //                 'ordem' => $ordem,
    //                 'ativo' => 1,
    //             )
    //         );

    //         // debug($dados_titulo);exit;

    //         if(empty($codigo_form_titulo)) {
    //             $this->PosSwtFormTitulo->incluir($dados_titulo);
    //         }
    //         else {
    //             $dados_titulo['PosSwtFormTitulo']['codigo'] = $codigo_form_titulo;
    //             $this->PosSwtFormTitulo->atualizar($dados_titulo);
    //         }

    //         $retorno = $this->monta_lista_titulos($codigo_form);

    //         echo json_encode($retorno);
    //     }
    //     else {
    //         echo 0;
    //     }
    //     exit;
    // }

    // public function monta_lista_titulos($codigo_form=null)
    // {

    //     $return = array();

    //     if(!empty($codigo_form)) {
    //         $dados = $this->PosSwtFormTitulo->find('all',array(
    //             'fields' => array(
    //                 'PosSwtFormTitulo.codigo AS codigo',
    //                 "CONCAT(PosSwtFormTitulo.ordem,' - ',PosSwtFormTitulo.titulo) AS titulo"
    //             ), 
    //             'conditions' => array('PosSwtFormTitulo.codigo_form' => $codigo_form),
    //             'order'=>array('PosSwtFormTitulo.ordem ASC','PosSwtFormTitulo.codigo ASC')));

    //         if(!empty($dados)) {
    //             foreach ($dados as $value_tit) {
    //                 $return[$value_tit[0]['codigo']] = $value_tit[0]['titulo'];
    //             }
    //         }
    //     }

    //     return $return;

    // }

    // /**
    //  * [config_cabecalho metodo para gravar o titulo do formulario]
    //  * @return [type] [description]
    //  */
    // public function confirma_questao()
    // {
    //     $this->layout = 'ajax';

    //     // debug($this->params['form']);exit;

    //     //parametros passados
    //     $codigo_cliente = $this->params['form']['codigo_cliente'];
    //     $codigo_form = $this->params['form']['codigo_form'];
    //     $codigo_titulo = $this->params['form']['codigo_titulo'];
    //     $ordem = $this->params['form']['ordem'];
    //     $questao = $this->params['form']['questao'];

    //     $codigo_form_questao = $this->params['form']['codigo_form_questao'];

    //     // debug(array($codigo_cliente,$codigo_form,$codigo_titulo,$ordem,$questao));exit;
        
    //     $retorno = array();

    //     //verifica se tem os codigos para gravar
    //     if(!empty($codigo_form) && !empty($codigo_titulo) && !empty($questao)) {

    //         $dados_questao = array(
    //             'PosSwtFormQuestao' => array(
    //                 'codigo_form' => $codigo_form,
    //                 'codigo_form_titulo' => $codigo_titulo,
    //                 'codigo_cliente' => $codigo_cliente,
    //                 'questao' => $questao,
    //                 'ordem' => $ordem,
    //                 'ativo' => 1,
    //             )
    //         );

    //         // debug($dados_questao);exit;

    //         if(empty($codigo_form_questao)) {
    //             $this->PosSwtFormQuestao->incluir($dados_questao);
    //         }
    //         else {
    //             $dados_questao['PosSwtFormQuestao']['codigo'] = $codigo_form_questao;
    //             $this->PosSwtFormQuestao->atualizar($dados_questao);
    //         }

    //         echo 1;
    //     }
    //     else {
    //         echo 0;
    //     }
    //     exit;
    // }

    // /**
    //  * [index_qtd_participantes description]
    //  * @return [type] [description]
    //  */
    // public function listagem_form_questao($codigo_form) 
    // {
    //     $this->layout = 'ajax';
        
    //     $dados_questoes = array();
    //     if(!empty($codigo_form)) {
    //         $fields = array(
    //             'PosSwtFormTitulo.codigo',
    //             'PosSwtFormTitulo.titulo',
    //             'PosSwtFormTitulo.ordem',
    //             'PosSwtFormTitulo.ativo',
    //             'PosSwtFormQuestao.codigo',
    //             'PosSwtFormQuestao.questao',
    //             'PosSwtFormQuestao.ordem',
    //             'PosSwtFormQuestao.ativo',
    //         );

    //         $joins = array(
    //             array(
    //                 'table' => 'pos_swt_form_questao',
    //                 'alias' => 'PosSwtFormQuestao',
    //                 'type' => 'INNER',
    //                 'conditions' => array('PosSwtFormQuestao.codigo_form_titulo = PosSwtFormTitulo.codigo')
    //             ),
    //         );

    //         $conditions['PosSwtFormQuestao.codigo_form'] = $codigo_form;

    //         $order = array('PosSwtFormTitulo.ordem ASC','PosSwtFormQuestao.ordem ASC');

    //         //executa com paginação
    //         $dados_titulos_questoes = $this->PosSwtFormTitulo->find('all',array(
    //             'fields' => $fields,
    //             'joins' => $joins,
    //             'conditions' => $conditions,
    //             'order' => $order
    //         ));

    //         if(!empty($dados_titulos_questoes)) {
    //             //formata o array para enviar para a ctp
    //             foreach ($dados_titulos_questoes as $tq) {
    //                 $dados_questoes[$tq['PosSwtFormTitulo']['codigo']]['PosSwtFormTitulo'] = $tq['PosSwtFormTitulo'];
    //                 $dados_questoes[$tq['PosSwtFormTitulo']['codigo']]['PosSwtFormQuestao'][$tq['PosSwtFormQuestao']['codigo']] = $tq['PosSwtFormQuestao'];
    //             }
    //         }
    //     }

    //     $this->set(compact('dados_questoes','codigo_form'));


    // } // fim metodo 

    

    // /**
    //  * [atualiza_status do modelo]
    //  * @param  [type] $codigo [description]
    //  * @param  [type] $status [description]
    //  * @return [type]         [description]
    //  */
    // public function atualiza_status_questao($codigo, $status)
    // {
    //     $this->layout = 'ajax';
        
    //     $this->data['PosSwtFormQuestao']['codigo'] = $codigo;
    //     $this->data['PosSwtFormQuestao']['ativo'] = ($status == "0") ? 1 : 0;
        
    //     if ($this->PosSwtFormQuestao->save($this->data, false)) {   // 0 -> ERRO | 1 -> SUCESSO  
    //         print 1;
    //     } else {
    //         print 0;
    //     }

    //     $this->render(false,false);
              
    // }


}