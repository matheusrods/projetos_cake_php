<?php
class FontesGeradorasRiscosController extends AppController {
    public $name = 'FontesGeradorasRiscos';
    var $uses = array(
        'FonteGeradoraRisco',
        'FonteGeradora',
        'Risco',
        'GrupoRisco'
        );
    
    
    function listagem($codigo_fonte_geradora) {

        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->FonteGeradoraRisco->name);
        
        $conditions = $this->FonteGeradoraRisco->converteFiltroEmCondition($filtros);
        $conditions = array_merge($conditions, array('codigo_fonte_geradora' => $codigo_fonte_geradora));

        $fields = array('FonteGeradoraRisco.codigo', 'FonteGeradoraRisco.ativo', 'Risco.codigo', 'Risco.nome_agente', 'GrupoRisco.descricao', );
        $order = array('GrupoRisco.descricao', 'Risco.nome_agente');

        $joins  = array(
            array(
              'table' => $this->FonteGeradora->databaseTable.'.'.$this->FonteGeradora->tableSchema.'.'.$this->FonteGeradora->useTable,
              'alias' => 'FonteGeradora',
              'type' => 'LEFT',
              'conditions' => 'FonteGeradora.codigo = FonteGeradoraRisco.codigo_fonte_geradora',
            ),
            array(
              'table' => $this->Risco->databaseTable.'.'.$this->Risco->tableSchema.'.'.$this->Risco->useTable,
              'alias' => 'Risco',
              'type' => 'LEFT',
              'conditions' => 'Risco.codigo = FonteGeradoraRisco.codigo_risco',
            ),
            array(
              'table' => $this->GrupoRisco->databaseTable.'.'.$this->GrupoRisco->tableSchema.'.'.$this->GrupoRisco->useTable,
              'alias' => 'GrupoRisco',
              'type' => 'LEFT',
              'conditions' => 'GrupoRisco.codigo = Risco.codigo_grupo',
            ),

        );

        $this->paginate['FonteGeradoraRisco'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'joins' => $joins,  
                'limit' => 50,
                'order' => $order,
        );
       
        $fontes_geradoras_riscos = $this->paginate('FonteGeradoraRisco');
        $this->set(compact('fontes_geradoras_riscos', 'codigo_fonte_geradora'));
    }

    function incluir($codigo_fonte_geradora) {
        $this->layout = 'ajax_placeholder';

        if($this->RequestHandler->isPost()) {
            
            try{  
                $this->FonteGeradoraRisco->query('begin transaction');
                
                foreach ($this->data['FonteGeradoraRisco'] as $risco){
                
                    if($risco != 0){
                        $dados = array(
                            'FonteGeradoraRisco' => array(
                                'codigo_fonte_geradora' => $codigo_fonte_geradora,
                                'codigo_risco' =>$risco,
                                'ativo' => 1
                            )
                        );

                        if(!$this->FonteGeradoraRisco->incluir($dados)){
                            throw new Exception();
                        }
                    }
                }

                $this->FonteGeradoraRisco->commit();
                echo 1;
            } 
            catch (Exception $ex) {              
                $this->FonteGeradoraRisco->rollback();
                echo 0;
            }
        }
           
        exit;
    }

    public function excluir($codigo) {
        
        if ($this->FonteGeradoraRisco->excluir($codigo)) {
            $this->BSession->setFlash('save_success');
            echo 1;
        } else {
            $this->BSession->setFlash('save_error');
            echo 0;
        }

        exit;
    }
}