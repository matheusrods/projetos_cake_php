<?php

class GestaoDocController extends AppController {
    public $name = 'GestaoDoc';
    public $uses = array(
        'GdModelos',
        'GdVariaveis',
        'GestaoDoc',
        'Cliente',
        'GdEstrutura',
        'GdEstruturaTipo',
        'GdEstruturaTipoDetalhes',
        'GdEstruturaCampos',
        'GdLibCapa',
        'GdLibImagem',
    );
    public $helpers = array('BForm', 'Html', 'Ajax', 'Highcharts');

    /**
     * beforeFilter callback
     * @return void
     */
    public function beforeFilter() 
    {
        parent::beforeFilter();

        $this->BAuth->allow('*');
    }


    public function index() 
    {
        $this->pageTitle = 'Templates';
    }


    public function listagem($export = false) 
    {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Exame->name);
        $conditions = $this->Exame->converteFiltroEmCondition($filtros);
        $fields = array('Exame.codigo', 'Exame.descricao', 'Exame.ativo','Exame.recomendacoes','Exame.codigo_tuss','Exame.ativo','Servico.descricao');
        $order = 'Exame.descricao';

        $this->Exame->bindModel(
            array(
                'belongsTo' => array(
                    'Servico' => array(
                        'foreignKey' => false,
                        'conditions' => array('Servico.codigo = Exame.codigo_servico')
                    ),
                )
            ), false
        );


        $this->paginate['Exame'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => $order,
        );

        if($export){
            $query = $this->Exame->find('sql',array('fields' => $fields, 'conditions' => $conditions, 'order' => $order));
            $this->export($query);
        } else {
            $exames = $this->paginate('Exame');
        }

        $this->set(compact('exames'));
    }

    
    /**
     * [indexCliente index dos templates]
     * @return [type] [description]
     */
    public function indexCliente() 
    {
        $this->pageTitle = 'Templates';
    }//fim indexCliente

    /**
     * [listagemCliente listagem dos templates]
     * @param  boolean $export [description]
     * @return [type]          [description]
     */
    public function listagemCliente() 
    {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Exame->name);
        $conditions = $this->Exame->converteFiltroEmCondition($filtros);
        $fields = array('Exame.codigo', 'Exame.descricao', 'Exame.ativo','Exame.recomendacoes','Exame.codigo_tuss','Exame.ativo','Servico.descricao');
        $order = 'Exame.descricao';

        $this->Exame->bindModel(
            array(
                'belongsTo' => array(
                    'Servico' => array(
                        'foreignKey' => false,
                        'conditions' => array('Servico.codigo = Exame.codigo_servico')
                    ),
                )
            ), false
        );


        $this->paginate['Exame'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => $order,
        );

        if($export){
            $query = $this->Exame->find('sql',array('fields' => $fields, 'conditions' => $conditions, 'order' => $order));
            $this->export($query);
        } else {
            $exames = $this->paginate('Exame');
        }

        $this->set(compact('exames'));
    }


}//GdModelosController
