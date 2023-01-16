<?php
class HospitaisEmergenciaController extends AppController {
    public $name = 'HospitaisEmergencia';
    public $components = array('Filtros', 'RequestHandler','ExportCsv', 'Upload');
    public $helpers = array('Html', 'Ajax', 'Highcharts','Buonny');

    var $uses = array(
        'HospitaisEmergencia',
        'Corretora',
        'Cliente',
        'Gestor',
        'EnderecoRegiao',
        'MotivoBloqueio',
        'PlanoDeSaude',
        'GrupoEconomico',
        'GrupoEconomicoCliente',
        'ClienteEndereco',
        'Corporacao',
        'TipoContato',
        'VEndereco'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('lista_unidades', 'lista_hospitais_emergencia', 'editar');
    }

    /**
     * [index description]
     * 
     * metodo para montar os filtros
     * 
     * @return [type] [description]
     */
	public function index() {

	    //titulo
        $this->pageTitle = 'Hospitais de Emergência';

        //controle da sessao para alimentar os filtros
        $filtros = $this->Filtros->controla_sessao($this->data, $this->HospitaisEmergencia->name);

        //trazer o codigo_cliente do usuario
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        //setando a model para os filtros
        $this->data['HospitaisEmergencia'] = $filtros;

        //carrega o combo dos estados para a ctp
        $this->carrega_combos_grupo_economico('HospitaisEmergencia');
	}//fim

    //tela da lista das unidades
    public function lista_unidades()
    {
        $this->layout = 'ajax'; 

        //controle da sessao para alimentar os filtros
        $filtros = $this->Filtros->controla_sessao($this->data, $this->HospitaisEmergencia->name);

        // se tem dados na sessao então preencha o codigo cliente e se tem codigo_cliente em $filtros usuario deve estar pesquisando
        if(!empty($this->authUsuario['Usuario']['codigo_cliente']) && empty($filtros['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }
        
        //condicoes vindas do filtro
        $conditions = $this->HospitaisEmergencia->converteFiltroEmCondition($filtros);

        //condicao adicionada para ajudar na busca
        $conditions[] = array('ClienteEndereco.codigo_tipo_contato = 2');

        //fields
        $fields = array(
            'GrupoEconomico.codigo',
            'GrupoEconomico.codigo_cliente',
            'GrupoEconomicoCliente.codigo',
            'GrupoEconomicoCliente.codigo_grupo_economico',
            'GrupoEconomicoCliente.codigo_cliente',
            'Cliente.codigo',
            'Cliente.razao_social',
            'Cliente.nome_fantasia',
            'Unidade.codigo',
            'Unidade.razao_social',
            'Unidade.nome_fantasia',
            'ClienteEndereco.bairro',
            'ClienteEndereco.cidade',
            'ClienteEndereco.estado_abreviacao'
        );

        //joins
        $joins = array(
            array(
                'table' => 'cliente',
                'alias' => 'Unidade',
                'type' => 'INNER',
                'conditions' => 'Unidade.codigo = GrupoEconomicoCliente.codigo_cliente'
            ), 
            array(
                'table' => 'cliente_endereco',
                'alias' => 'ClienteEndereco',
                'type' => 'INNER',
                'conditions' => 'ClienteEndereco.codigo_cliente = Unidade.codigo'
            ),
        );

        $this->paginate['GrupoEconomicoCliente'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'joins' => $joins
        );

        //buscando todas as unidades do cliente
        $lista_clientes_grupo = $this->paginate('GrupoEconomicoCliente');

        // $lista_clientes_grupo = $this->GrupoEconomicoCliente->find('all', array('conditions' => $conditions, 'fields' => $fields, 'joins' => $joins));

        $this->set(compact('lista_clientes_grupo'));

    }//FINAL FUNCTION unidades_hospital_emergencia

    function lista_hospitais_emergencia($codigo_cliente, $codigo_unidade){

        $this->pageTitle = 'Lista Hospitais de Emergência'; 

        //fields
        $fields = array(
            'HospitaisEmergencia.codigo',
            'HospitaisEmergencia.nome',
            'HospitaisEmergencia.cep',
            'HospitaisEmergencia.logradouro',
            'HospitaisEmergencia.numero',
            'HospitaisEmergencia.complemento',
            'HospitaisEmergencia.bairro',
            'HospitaisEmergencia.estado',
            'HospitaisEmergencia.cidade'
        );

        //where
        $conditions = array(
            'HospitaisEmergencia.codigo_cliente_matriz' => $codigo_cliente,
            'HospitaisEmergencia.codigo_cliente_unidade' => $codigo_unidade
        );

        $this->paginate['HospitaisEmergencia'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 10
        );

        //buscando todos os hospitais
        $dados_hospitais = $this->paginate('HospitaisEmergencia');

        $this->set(compact('dados_hospitais', 'codigo_cliente', 'codigo_unidade'));
    }


    //tela de inclusao dos hospitais de emergencia
    function incluir($codigo_cliente, $codigo_unidade) {
    
        //titulo
        $this->pageTitle = 'Incluir Hospital de Emergência';

        //coloca o usuario clica no salvar ele incluir
        if($this->RequestHandler->isPost()) {

            //se ele incluir, senao reportar erro para que o usuario se adeque
            if ($this->HospitaisEmergencia->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'lista_hospitais_emergencia', 'controller' => 'hospitais_emergencia', $codigo_cliente, $codigo_unidade));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        //seta para a ctp o combo dos estados
        $this->carrega_combos_formulario();
        $this->set(compact('codigo_cliente', 'codigo_unidade'));       
    }//final function incluir

    //editar dados do hospital de emergencia
    function editar($codigo, $codigo_cliente, $codigo_unidade) {
        
        $this->pageTitle = 'Editar Hospital de Emergência';

        //quando ele clicar botao save devera carregar as informacoes do hospital
        if($this->RequestHandler->isPost()) {

            if ($this->HospitaisEmergencia->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'hospitais_emergencia', 'action' => 'lista_hospitais_emergencia', $codigo_cliente, $codigo_unidade));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
            
            $this->redirect(array('controller' => 'hospitais_emergencia', 'action' => 'unidades_hospital_emergencia', $codigo_cliente, $codigo_unidade));
        }

        //pega os dados do hospital
       $this->data = $this->HospitaisEmergencia->find('first', array('conditions' => array('codigo' => $codigo)));

        $this->carrega_combos_formulario();
        $this->set(compact('codigo', 'codigo_cliente', 'codigo_unidade'));
    }//fim editar

    //metodo auxilia o carregamento do filtro unidades
    public function carrega_combos_grupo_economico($model) {
        
        //variavel vazia
        $unidades = array();

        //se existir codigo cliente, ele seta, senao vazio
        $codigo_cliente = (isset($this->data[$model]['codigo_cliente'])) ? $this->data[$model]['codigo_cliente'] : array();

        //se codigo cliente nao for vazio ele carrega o combo e alimenta o filtro das unidades
        if(!empty($codigo_cliente)){
            $codigo_cliente = (is_array($codigo_cliente)) ? $codigo_cliente : $codigo_cliente;
            $codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

            $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
        }

        $this->set(compact('unidades'));
    }//fim

    //metodo auxilia no carregamento do combo estados
    function carrega_combos_formulario() {

        $comum = new Comum;
        $estados = $comum->estados();
        array_unshift( $estados , "");
        $this->set(compact('estados'));     
    }
}