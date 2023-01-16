<?php
class SensoresTemperaturasController extends appController {
    var $name = 'SensoresTemperaturas';
    var $uses = array('TStemSensoresTemperatura');

    function index() {
        $this->pageTitle = 'Sensores de Temperatura';
        $this->data['TStemSensoresTemperatura']['data_inicial'] = date("d/m/Y");
        $this->data['TStemSensoresTemperatura']['data_final'] = date("d/m/Y");
    }

    function listagem(){        
        $filtros = $this->Filtros->controla_sessao($this->data, 'TStemSensoresTemperatura');
        $authUsuario = $this->BAuth->user();
        $dados_sensores = array();
        if( !empty( $authUsuario['Usuario']['codigo_cliente'] ))
            $filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
        if( !empty($filtros['codigo_cliente']) && !empty($filtros['veic_placa']) && !empty($filtros['data_inicial']) && !empty($filtros['data_final']) ){
            $dados_exec_proc = $this->TStemSensoresTemperatura->temperatura_veiculo_cliente( $filtros['codigo_cliente'], $filtros['veic_placa'], $filtros['data_inicial'], $filtros['data_final'] );
            foreach( $dados_exec_proc as $registro ){
                $dados_sensores[$registro[0]['viag_codigo_sm']]['dados_sm'] = $registro[0];
                $dados_sensores[$registro[0]['viag_codigo_sm']]['dados_sensores'][] = $registro[0];
            }
            $this->set(compact('dados_sensores', 'filtros'));            
        }
    }
}
?>