<?php
class EstatisticaSmGeral extends AppModel {
    var $name = 'EstatisticaSmGeral';
    var $useTable = false;
    var $tipo = null;
    
    function carregarConsolidado($data_limite = null) {
        $this->Behaviors->attach('EstatisticaSmPeriodo');
        $this->EstatisticaSm = ClassRegistry::init('EstatisticaSm');

        if (empty($data_limite)) {
            $data_limite = date('Y-m-d H:i:s');
        }

        $lista_periodo = $this->geraPeriodo($this->tipo, $data_limite);
        $periodo_inicial = isset($lista_periodo[0]) ? $lista_periodo[0] : null;
        
        $this->_removerPeriodoAnterior($periodo_inicial);

        foreach ($lista_periodo as $ultima_data) {
            $dado = array();
            $periodo = $this->EstatisticaSm->periodo($ultima_data, $this->tipo);
            
            $dado = $this->EstatisticaSm->consolidaTotal($periodo);
            if (empty($dado)) {
                $dado = array(
                    array(
                        'operacoes' => 0,
                        'em_aberto' => 0,
                        'em_andamento' => 0,
                        'ocorrencias' => 0,
                        'operadores' => 0,
                        'em_andamento_por_operador' => 0,
                    )
                );
            }
            
            $dado = array($this->name => $dado[0]);
            $dado[$this->name]['data'] = $ultima_data;
            $this->incluir($dado);
        }
        
        $this->incluirRegistroInicial($periodo_inicial);
        $this->Behaviors->detach('EstatisticaSmPeriodo');
    }
    
    function _removerPeriodoAnterior($periodo) {
        $mascara_retorno = ($this->tipo == EstatisticaSm::TIPO_HORA ? 'Y-m-d H:00:00' : 'Y-m-d 00:00:00');
        $this->deleteAll(array('data >=' => date($mascara_retorno, strtotime($periodo))));
    }
    
}