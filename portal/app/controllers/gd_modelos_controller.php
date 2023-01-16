<?php

class GdModelosController extends AppController {
    public $name = 'GdModelos';
    public $uses = array(
        'GdModelos',
        'GdVariaveis',
    );
    public $helpers = array('BForm', 'Html', 'Ajax', 'Highcharts');

    /**
     * beforeFilter callback
     * @return void
     */
    public function beforeFilter() {
        parent::beforeFilter();

        $this->BAuth->allow('*');
    }

    /**
     * [index index do modelos]
     * @return [type] [description]
     */
    public function index() 
    {
        $this->pageTitle = 'Modelos';
    }

    /**
     * [listagem dos modelos]
     * @param  boolean $export [description]
     * @return [type]          [description]
     */
    public function listagem() 
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
     * [indexVariavel variavel para cadastro]
     * @return [type] [description]
     */
    public function indexVariavel() 
    {
        $this->pageTitle = 'Variáveis';
    }

    /**
     * [listagemVariavel listagem das variaveis]
     * @param  boolean $export [description]
     * @return [type]          [description]
     */
    public function listagemVariavel() 
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
