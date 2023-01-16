<?php

class StatusViagem extends AppModel {
    public $name = 'StatusViagem';
    public $useTable = false;
    
    const CANCELADO = 2; 
    const AGENDADO = 3; 
    const EM_TRANSITO = 4; 
    const ENTREGANDO = 5; 
    const LOGISTICO = 6; 
    const ENCERRADA = 7;
    const SEM_VIAGEM = 8; 
    
    private $list = array(
		StatusViagem::CANCELADO => 'Cancelado',    	
		StatusViagem::AGENDADO => 'Agendado',    	
		StatusViagem::EM_TRANSITO => 'Em Trânsito',    	
		StatusViagem::ENTREGANDO => 'Entregando',    	
		StatusViagem::LOGISTICO => 'Logístico',    	
        StatusViagem::ENCERRADA => 'Encerrada',
		StatusViagem::SEM_VIAGEM => 'Sem Viagem'    	
    );
    
    public function find($ignorar = array()){
    	$status_viagens = $this->list;
    	foreach($ignorar as $status)
    		unset($status_viagens[$status]);
    	return $status_viagens;
    }

    public function listarParaLoadplan(){
        return $this->find(array(
                self::CANCELADO,
                self::ENTREGANDO,
                self::LOGISTICO,
                self::SEM_VIAGEM
            ));
    }
}

