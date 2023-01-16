<?php
class ItinerariosSolicitacoesMonitoramentoController extends AppController {
	
    public $name = 'ItinerariosSolicitacoesMonitoramento';
    var $uses = array('MSmitinerario', 'Cliente');
    var $components = array('Maplink');
    
/*    function por_cliente() {
        $this->pageTitle = 'Acompanhamento SMs';
        $this->loadModel('ClientEmpresa');
        $this->data['MSmitinerario'] = $this->Filtros->controla_sessao($this->data, $this->MSmitinerario->name);
        $label_empty = 'Selecione o cliente';
        $clientes_tipos = array();
        $codigo_cliente = $this->data['MSmitinerario']['codigo_cliente'];
        if (!empty($codigo_cliente)) {
            $cliente = $this->Cliente->carregar($codigo_cliente);
            $tipo_empresa = $this->Cliente->retornarClienteSubTipo($codigo_cliente);
            if ($tipo_empresa == Cliente::SUBTIPO_EMBARCADOR)
                $label_empty = 'Embarcador';
            elseif ($tipo_empresa == Cliente::SUBTIPO_TRANSPORTADOR)
                $label_empty = 'Transportadora';
            $clientes_tipos = $this->ClientEmpresa->porBaseCnpj(substr($cliente['Cliente']['codigo_documento'], 0, 8), $tipo_empresa);
        }
        $status = $this->MSmitinerario->listarStatus();
        $this->set(compact('clientes_tipos','status', 'label_empty'));
    }
    
    function por_cliente_listagem() {
        $this->layout = 'ajax';
        $this->loadModel('TViagViagem');
        $filtros = $this->Filtros->controla_sessao($this->data, $this->MSmitinerario->name);
        if (!empty($filtros['codigo_cliente'])) 
            $cliente = $this->Cliente->carregar($filtros['codigo_cliente']);
        $conditions = array('Recebsm.sm' => -1);
        if (isset($filtros['cliente_embarcador']) || isset($filtros['cliente_transportador']))
			$conditions = $this->MSmitinerario->converteFiltrosEmConditions($filtros);

        $this->paginate['MSmitinerario'] = array('conditions' => $conditions, 'limit' => ($this->passedArgs[0] == 'export' ? 999999 : (isset($filtros['itens_por_pagina']) && $filtros['itens_por_pagina'] >0) ? $filtros['itens_por_pagina'] : 6 ), 'extra' => 'new_paginate');
        $sms = $this->paginate('MSmitinerario');
        $total_sms = $this->MSmitinerario->quantidadeDeSms($conditions);
        $this->Maplink->calcula_tempo_restante_sms($sms);
        $this->set(compact('sms', 'cliente', 'total_sms'));
    }
*/
}