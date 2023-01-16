<?php
class FuncoesController extends AppController {
    public $name = 'Funcoes';
    var $uses = array('Funcao');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    function index() {
        $this->pageTitle = 'Funções';
        $this->data['Funcao'] = $this->Filtros->controla_sessao($this->data, $this->Funcao->name);

    }

    function listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Funcao->name);
        $conditions = $this->Funcao->converteFiltroEmCondition($filtros);
        $fields = array('Funcao.codigo','Funcao.descricao', 'Funcao.ativo');
        $order = 'Funcao.codigo';

        $this->paginate['Funcao'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'recursive' => '-1',
                'limit' => 50,
                'order' => $order
        );

        $funcoes = $this->paginate('Funcao');
        $this->set(compact('funcoes'));    
    }

    function incluir() {
        $this->pageTitle = 'Incluir Função';

        if($this->RequestHandler->isPost()) {
            if ($this->Funcao->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'funcoes'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }
    }

    function editar() {
        $this->pageTitle = 'Editar Função'; 

        if($this->RequestHandler->isPost()) {

            if ($this->Funcao->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'funcoes'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->Funcao->carregar($this->passedArgs[0]);
        }        
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';

        $this->data['Funcao']['codigo'] = $codigo;
        $this->data['Funcao']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->Funcao->atualizar($this->data, false)) {   
            echo 1;
        } else {
            echo 0;
        }
        $this->render(false,false);
    }

    function gerar_aplicacao_exames($codigo) {

        $this->loadModel('AplicacaoExame');


        $fields = array('ClienteSetorCargo.codigo_cliente',
                        'ClienteSetorCargo.codigo_setor',
                        'ClienteSetorCargo.codigo_cargo',
                        'ExameFuncao.codigo_exame',
                        'Exame.periodo_meses',
                        'Exame.periodo_apos_demissao',
                        'Exame.exame_admissional',
                        'Exame.exame_periodico',
                        'Exame.exame_demissional',
                        'Exame.exame_retorno',
                        'Exame.exame_mudanca',
                        'Exame.periodo_idade',
                        'Exame.qtd_periodo_idade',
                        'Exame.exame_excluido_convocacao',
                        'Exame.exame_excluido_ppp',
                        'Exame.exame_excluido_aso',
                        'Exame.exame_excluido_pcmso',
                        'Exame.exame_excluido_anual',
                        'Exame.periodo_idade_2',
                        'Exame.qtd_periodo_idade_2',
                        'Exame.periodo_idade_3',
                        'Exame.qtd_periodo_idade_3',
                        'Exame.periodo_idade_4',
                        'Exame.qtd_periodo_idade_4'
                    );

        $joins = array(
                    array(
                        'alias' => 'ExameFuncao',
                        'table' => 'exames_funcoes',
                        'type' => 'INNER', 
                        'conditions' => array('ExameFuncao.codigo_funcao = Funcao.codigo AND ExameFuncao.ativo = 1')
                        ),
                    array(
                        'alias' => 'Cargo',
                        'table' => 'cargos',
                        'type' => 'INNER', 
                        'conditions' => array('Cargo.codigo_funcao = Funcao.codigo AND Cargo.ativo = 1')
                        ),                    
                    array(
                        'alias' => 'Exame',
                        'table' => 'exames',
                        'type' => 'INNER', 
                        'conditions' => array('Exame.codigo = ExameFuncao.codigo_exame AND Exame.ativo = 1')
                        ),
                    array(
                        'alias' => 'ClienteSetorCargo',
                        'table' => 'clientes_setores_cargos',
                        'type' => 'INNER', 
                        'conditions' => array('ClienteSetorCargo.codigo_cargo = Cargo.codigo')
                        )
                );

        $conditions = array('Funcao.codigo' => $codigo,
                            'Funcao.ativo' => '1',
                            "NOT EXISTS(SELECT
                                TOP(1) codigo 
                                FROM
                                aplicacao_exames 
                                WHERE
                                codigo_cliente = ClienteSetorCargo.codigo_cliente AND
                                codigo_setor = ClienteSetorCargo.codigo_setor AND
                                codigo_cargo = ClienteSetorCargo.codigo_cargo  AND
                                codigo_exame = ExameFuncao.codigo_exame )"
                            );


        $group = array('ClienteSetorCargo.codigo_cliente',
                        'ClienteSetorCargo.codigo_setor',
                        'ClienteSetorCargo.codigo_cargo',
                        'ExameFuncao.codigo_exame',
                        'Exame.periodo_meses',
                        'Exame.periodo_apos_demissao',
                        'Exame.exame_admissional',
                        'Exame.exame_periodico',
                        'Exame.exame_demissional',
                        'Exame.exame_retorno',
                        'Exame.exame_mudanca',
                        'Exame.periodo_idade',
                        'Exame.qtd_periodo_idade',
                        'Exame.exame_excluido_convocacao',
                        'Exame.exame_excluido_ppp',
                        'Exame.exame_excluido_aso',
                        'Exame.exame_excluido_pcmso',
                        'Exame.exame_excluido_anual',
                        'Exame.periodo_idade_2',
                        'Exame.qtd_periodo_idade_2',
                        'Exame.periodo_idade_3',
                        'Exame.qtd_periodo_idade_3',
                        'Exame.periodo_idade_4',
                        'Exame.qtd_periodo_idade_4'
                        );


        $exames = $this->Funcao->find('all', array('contain' => array('Funcao', 'ExameFuncao'),'fields' => $fields, 'joins' => $joins, 'conditions' => $conditions, 'group' => $group, 'recursive' => '-1')); 

        $dados = array();

        if(!empty($exames)){
            $erro = 0;

            $this->AplicacaoExame->query('begin transaction');

            foreach($exames as $exame){

                $dados['AplicacaoExame'] = array_merge($exame['ClienteSetorCargo']);
                $exame['Exame']['codigo_tipo_exame'] = 1;
                $dados['AplicacaoExame'][] = array_merge($exame['ExameFuncao'],$exame['Exame']); 


                if(!$this->AplicacaoExame->incluir($dados)){
                    $erro = 1;
                    break;
                }
            }

            if($erro == 1) {
                $this->AplicacaoExame->rollback();
                $this->BSession->setFlash('save_error');
            } else {
                $this->AplicacaoExame->commit();
               $this->BSession->setFlash('save_success');

            }
        }

        $this->redirect(array('action' => 'index'));

    } 

}
