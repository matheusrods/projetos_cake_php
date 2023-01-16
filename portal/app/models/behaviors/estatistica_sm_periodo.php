<?php
App::import('Model', 'EstatisticaSm');
class EstatisticaSmPeriodoBehavior extends ModelBehavior {
    
    function setup(&$model, $config) {
        $this->Model = $model;
    }

    function obterUltimoPeriodo(&$model) {
        $ultima_data = $model->find('first',
            array(
        		'fields' => array("CONVERT(VARCHAR, MAX(data), 120) AS MAX_DATA")
            )
        );
        
        $ultima_data = $ultima_data[0]['MAX_DATA'];
        
        if (empty($ultima_data)) {
            return false;
        }

        return strtotime($ultima_data);
    }
    
    function geraPeriodo(&$model, $tipo, $data_limite = null) {
        if (empty($data_limite)) {
            $data_limite = time();
        } else {
            $data_limite = strtotime($data_limite);
        }

        $ultima_data = $this->obterUltimoPeriodo($model);
        if (false === $ultima_data) {
            throw new Exception('Registro inicial não encontrado.');
            return array();
        }
        
        $mascara_data = ($tipo == EstatisticaSm::TIPO_HORA ? 'YmdH' : 'Ymd');
        $mascara_retorno = ($tipo == EstatisticaSm::TIPO_HORA ? 'Y-m-d H:00:00' : 'Y-m-d 00:00:00');
        $incremento = ($tipo == EstatisticaSm::TIPO_HORA ? '+1 hour' : '+1 day');
        $decremento = ($tipo == EstatisticaSm::TIPO_HORA ? '-1 hour' : '-1 day');

        // Volta 1 dia ou 1 hora para obter o ultimo período novamente
        $ultima_data = strtotime($decremento, $ultima_data);
        
        $retorno = array();
        while (date($mascara_data, $ultima_data) < date($mascara_data, $data_limite)) {
            $ultima_data = strtotime($incremento, $ultima_data);
            $retorno[] = date($mascara_retorno, $ultima_data);
        }

        return $retorno;
    }
    
    /**
     * Insere um registro na tabela de estatística, caso ela esteja vazia
     */
    function incluirRegistroInicial(&$model, $periodo = null) {
        if (empty($periodo)) {
            return false;
        }
        
        $dado_inicial = array();
        foreach ($this->Model->schema() as $campo => $data) {
            if ($campo == 'codigo') continue;
            if ($data['type'] == 'integer') {
                $dado_inicial[$campo] = 0;
            } else {
                $dado_inicial[$campo] = '';
            }
        }
        
        if ($this->Model->find('count') == 0) {
            $dado = array (
                $this->Model->name => array_merge($dado_inicial, array('data' => $periodo))
            );

            return $this->Model->incluir($dado);
        }

    }
    
}
