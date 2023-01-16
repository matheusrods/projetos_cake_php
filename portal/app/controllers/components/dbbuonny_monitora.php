<?php
class DbbuonnyMonitoraComponent {
	var $name = 'DbbuonnyMonitora';
	
	function initialize(&$controller, $settings = array()) {        
		// saving the controller reference for later use        
		$this->controller =& $controller;    
	}
        
	function clientesMonitoraPorBaseCnpjETipoClienteBuonny($codigo_cliente, $tipo_empresa = null) {
		$this->ClientEmpresa =& ClassRegistry::init('ClientEmpresa');
		$this->Cliente = ClassRegistry::init('Cliente');
        $cliente = null;
        $clientes_tipos = array();

        if (!empty($codigo_cliente)) {
    		$codigo_sub_tipo_transportadora = array(1,7,13,19);
            $codigo_sub_tipo_embarcador = array(2,8,14,20);

    		$cliente = $this->Cliente->carregar($codigo_cliente);
    		$base_cnpj = substr($cliente['Cliente']['codigo_documento'],0,8);
        }
   		return array('cliente' => $cliente, 'tipo_empresa' => $tipo_empresa);
	}

	function converteClienteBuonnyEmMonitora($filtros, $sentido) {
        $this->ClientEmpresa =& ClassRegistry::init('ClientEmpresa');
        $this->controller->Cliente = ClassRegistry::init('Cliente');
		$clientes = array();
        if ($sentido == ClientEmpresa::SENTIDO_MONITORA_BUONNY) {
            if (isset($filtros['cliente_embarcador']) && !empty($filtros['cliente_embarcador']))
                $clientes['codigo_embarcador'] = $this->ClientEmpresa->converteCodigoClienteBuonnyMonitora($filtros['cliente_embarcador'], ClientEmpresa::SENTIDO_MONITORA_BUONNY);
            if (isset($filtros['cliente_transportador']) && !empty($filtros['cliente_transportador']))
                $clientes['codigo_transportador'] = $this->ClientEmpresa->converteCodigoClienteBuonnyMonitora($filtros['cliente_transportador'], ClientEmpresa::SENTIDO_MONITORA_BUONNY);
        } elseif ($sentido == ClientEmpresa::SENTIDO_BUONNY_MONITORA) {
            $clientes['cliente_embarcador'] = null;
            $clientes['cliente_transportador'] = null;
        	if (empty($filtros['cliente_embarcador'])) {
        		if (!empty($filtros['codigo_embarcador'])) {
                    $clientes_embarcadores = $this->controller->DbbuonnyMonitora->clientesMonitoraPorBaseCnpjETipoClienteBuonny($filtros['codigo_embarcador'], Cliente::SUBTIPO_EMBARCADOR);
	                $clientes['cliente_embarcador'] = array_keys($clientes_embarcadores['clientes_tipos']);
                }
        	}
            if (empty($filtros['cliente_transportador'])) {
            	if (!empty($filtros['codigo_transportador'])) {
                    $clientes_transportadores = $this->controller->DbbuonnyMonitora->clientesMonitoraPorBaseCnpjETipoClienteBuonny($filtros['codigo_transportador'], Cliente::SUBTIPO_TRANSPORTADOR);
                	$clientes['cliente_transportador'] = array_keys($clientes_transportadores['clientes_tipos']);
                }
            }
        }
        return $clientes;
    }

    function transportadorasMonitoraPorClienteBuonny($codigo_cliente, $data_inicial = null, $data_final = null) {
        $this->controller->loadModel('Recebsm');
        $this->controller->loadModel('ClientEmpresa');
        if ($data_inicial == null) {
            $data_inicial = (date('Y')-1).'-01-01 00:00:00';
            $data_final = date('Y-12-31 23:59:59');
        }
        if (!is_array($codigo_cliente))
            $codigo_cliente = array($codigo_cliente);
        $codigos = array();
        foreach ($codigo_cliente as $codigo) {
            $embarcadores = $this->clientesMonitoraPorBaseCnpjETipoClienteBuonny($codigo, ClientEmpresa::TIPO_EMPRESA_EMBARCADOR);
            if ((is_array($embarcadores['clientes_tipos'])) && (count($embarcadores['clientes_tipos'])>0) ) {
                $codigos = array_merge($codigos, $this->controller->Recebsm->transportadorasPorEmbarcadores( array_keys($embarcadores['clientes_tipos']), $data_inicial, $data_final ));
            }
        }
        return $codigos;
    }
}