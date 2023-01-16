<?php
class EpiRiscosController extends AppController {
    public $name = 'EpiRiscos';
    var $uses = array(
        'EpiRisco',
        'Epi',
        'Risco',
        'GrupoRisco'
        );
    

    function listagem($codigo_epi) {

        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->EpiRisco->name);
        
        $conditions = $this->EpiRisco->converteFiltroEmCondition($filtros);
        $conditions = array_merge($conditions, array('codigo_epi' => $codigo_epi));

        $fields = array('EpiRisco.codigo', 'EpiRisco.ativo', 'Risco.codigo', 'Risco.nome_agente', 'GrupoRisco.descricao', );
        $order = array('GrupoRisco.descricao', 'Risco.nome_agente');

        $joins  = array(
            array(
              'table' => $this->Epi->databaseTable.'.'.$this->Epi->tableSchema.'.'.$this->Epi->useTable,
              'alias' => 'Epi',
              'type' => 'LEFT',
              'conditions' => 'Epi.codigo = EpiRisco.codigo_epi',
            ),
            array(
              'table' => $this->Risco->databaseTable.'.'.$this->Risco->tableSchema.'.'.$this->Risco->useTable,
              'alias' => 'Risco',
              'type' => 'LEFT',
              'conditions' => 'Risco.codigo = EpiRisco.codigo_risco',
            ),
            array(
              'table' => $this->GrupoRisco->databaseTable.'.'.$this->GrupoRisco->tableSchema.'.'.$this->GrupoRisco->useTable,
              'alias' => 'GrupoRisco',
              'type' => 'LEFT',
              'conditions' => 'GrupoRisco.codigo = Risco.codigo_grupo',
            ),

        );

        $this->paginate['EpiRisco'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'joins' => $joins,  
                'limit' => 50,
                'order' => $order,
        );
       
        $epi_riscos = $this->paginate('EpiRisco');
        $this->set(compact('epi_riscos', 'codigo_epi'));
    }

    function incluir($codigo_epi) {
        $this->layout = 'ajax_placeholder';

        if($this->RequestHandler->isPost()) {
            
            try{  
                $this->EpiRisco->query('begin transaction');
                
                foreach ($this->data['EpiRisco'] as $risco){
                
                    if($risco != 0){
                        $dados = array(
                            'EpiRisco' => array(
                                'codigo_epi' => $codigo_epi,
                                'codigo_risco' =>$risco,
                                'ativo' => 1
                            )
                        );

                        if(!$this->EpiRisco->incluir($dados)){
                            throw new Exception();
                        }
                    }
                }

                $this->EpiRisco->commit();
                echo 1;
            } 
            catch (Exception $ex) {              
                $this->EpiRisco->rollback();
                echo 0;
            }
        }
           
        exit;
    }

    public function excluir($codigo) {
        
        if ($this->EpiRisco->excluir($codigo)) {
            $this->BSession->setFlash('save_success');
            echo 1;
        } else {
            $this->BSession->setFlash('save_error');
            echo 0;
        }

        exit;
    }
}