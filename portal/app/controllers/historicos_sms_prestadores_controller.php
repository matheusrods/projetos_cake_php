<?php
class HistoricosSmsPrestadoresController extends AppController {
    public $name = 'HistoricosSmsPrestadores';    
    public $uses = array('HistoricoSm', 'HistoricoSmPrestador');
 	public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('*'));
    }
    public function prestador_por_atendimento($codigo_atendimento, $codigo_sm, $codigo_prestador = null){
        $this->HistoricoSmPrestador = ClassRegistry::init('HistoricoSmPrestador');
        $this->layout = 'ajax';        
        $prestadores = $this->HistoricoSmPrestador->prestadores_por_historico_sm($codigo_atendimento);                
        $this->set(compact('prestadores', 'codigo_atendimento', 'codigo_sm', 'codigo_prestador'));
    } 
    public function status_historico_sm_prestador($acao, $codigo_prestador, $codigo_atendimento, $codigo_sm){
    	$this->layout = 'ajax';
		$this->pageTitle  = false;
		
		if(!empty($this->data)){
			if(empty($this->data['HistoricoSmPrestador']['observacao']))
				$this->HistoricoSmPrestador->invalidate('observacao','Informe a observação.');
			else{	
				if($this->HistoricoSmPrestador->alterar_pronta_resposta($codigo_prestador, $codigo_atendimento, $this->data['HistoricoSmPrestador']['observacao'], $acao)){					
					$this->BSession->setFlash('save_success');
				}else{
					$this->BSession->setFlash('save_error');
				}
			}		
		}				
		$this->set(compact('acao', 'codigo_prestador', 'codigo_atendimento', 'codigo_sm'));
    } 
}
