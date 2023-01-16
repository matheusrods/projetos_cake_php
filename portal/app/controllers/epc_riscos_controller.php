<?php
class EpcRiscosController extends AppController {
    public $name = 'EpcRiscos';
    var $uses = array(
	        'EpcRisco',
	        'Epc',
	        'Risco',
	        'GrupoRisco'
        );
    
       
    function listagem($codigo_epc) {

        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->EpcRisco->name);
        
        $conditions = $this->EpcRisco->converteFiltroEmCondition($filtros);
        $conditions = array_merge($conditions, array('codigo_epc' => $codigo_epc));

        $fields = array('EpcRisco.codigo', 'EpcRisco.ativo', 'Risco.codigo', 'Risco.nome_agente', 'GrupoRisco.descricao', );
        $order = array('GrupoRisco.descricao', 'Risco.nome_agente');

        $joins  = array(
            array(
              'table' => $this->Epc->databaseTable.'.'.$this->Epc->tableSchema.'.'.$this->Epc->useTable,
              'alias' => 'Epc',
              'type' => 'LEFT',
              'conditions' => 'Epc.codigo = EpcRisco.codigo_epc',
            ),
            array(
              'table' => $this->Risco->databaseTable.'.'.$this->Risco->tableSchema.'.'.$this->Risco->useTable,
              'alias' => 'Risco',
              'type' => 'LEFT',
              'conditions' => 'Risco.codigo = EpcRisco.codigo_risco',
            ),
            array(
              'table' => $this->GrupoRisco->databaseTable.'.'.$this->GrupoRisco->tableSchema.'.'.$this->GrupoRisco->useTable,
              'alias' => 'GrupoRisco',
              'type' => 'LEFT',
              'conditions' => 'GrupoRisco.codigo = Risco.codigo_grupo',
            ),

        );

        $this->paginate['EpcRisco'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'joins' => $joins,  
                'limit' => 50,
                'order' => $order,
        );
       
        $epc_riscos = $this->paginate('EpcRisco');
        $this->set(compact('epc_riscos', 'codigo_epc'));
    }

    function incluir($codigo_epc) {
        $this->layout = 'ajax_placeholder';

        if($this->RequestHandler->isPost()) {
            
            try{  
                $this->EpcRisco->query('begin transaction');
                
                foreach ($this->data['EpcRisco'] as $risco){
                
                    if($risco != 0){
                        $dados = array(
                            'EpcRisco' => array(
                                'codigo_epc' => $codigo_epc,
                                'codigo_risco' =>$risco,
                                'ativo' => 1
                            )
                        );

                        if(!$this->EpcRisco->incluir($dados)){
                            throw new Exception();
                        }
                    }
                }

                $this->EpcRisco->commit();
                echo 1;
            } 
            catch (Exception $ex) {              
                $this->EpcRisco->rollback();
                echo 0;
            }
        }
           
        exit;
    }

    public function excluir($codigo) {
        
        if ($this->EpcRisco->excluir($codigo)) {
            $this->BSession->setFlash('save_success');
            echo 1;
        } else {
            $this->BSession->setFlash('save_error');
            echo 0;
        }

        exit;
    }
}