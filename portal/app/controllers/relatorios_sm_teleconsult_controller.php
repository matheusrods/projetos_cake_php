<?php
class RelatoriosSmTeleconsultController extends AppController {

    public $name = 'RelatoriosSmTeleconsult';
    public $components = array('Filtros', 'DbbuonnyGuardian', 'Maplink');
    public $uses = array(
        'Cliente', 'RelatorioSmTeleconsult',);
    public $helpers = array('Highcharts');

    function beforeFilter() {
        parent::beforeFilter();
		//$this->BAuth->allow(array('*'
        //));
    }

    function sms_status_profissional() {
        $this->pageTitle = 'Consulta SMs vs Status Profissional Teleconsult';
        $is_post = $this->RequestHandler->isPost();
        if ($is_post)
        	$this->Filtros->limpa_sessao('RelatorioSmTeleconsult');

        $this->data['RelatorioSmTeleconsult'] = $this->Filtros->controla_sessao($this->data, "RelatorioSmTeleconsult");
        $authUsuario = $this->BAuth->user();
        if (!empty($authUsuario['Usuario']['codigo_cliente']))
            $this->data['RelatorioSmTeleconsult']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];

        if (!$is_post) {
            $this->data['RelatorioSmTeleconsult']['data_previsao_de'] = date('d/m/Y');
            $this->data['RelatorioSmTeleconsult']['data_previsao_ate'] = date('d/m/Y');
        }

		//$this->set(compact('label_empty', 'tipos_veiculos', 'status_viagens', 'agrupamento', 'is_post','tipos_transportes','EstadoOrigem', 'qualidades'));
    }

    function listagem_sms_status_profissional($tipo_view = false) {
        $this->loadModel('RelatorioSmTeleconsult');
        $this->loadModel('Status');
        $this->layout = 'ajax';

        if( isset($this->data) && $this->data )
            $this->Session->write('FiltrosRelatorioSmTeleconsult', $this->data['RelatorioSmTeleconsult'] );
        
        $filtros['RelatorioSmTeleconsult'] = $this->Session->read('FiltrosRelatorioSmTeleconsult');

        $conditions = $this->RelatorioSmTeleconsult->converteFiltrosEmConditions($filtros['RelatorioSmTeleconsult']);

        $status_teleconsult = $this->Status->find('list');

        if($tipo_view == 'export'){
            $this->listagem_sms_status_profissional_export($conditions);
        }

        if(!empty($conditions)){
            $this->paginate['RelatorioSmTeleconsult'] = array(
                'conditions' => $conditions,
                'extra' => array('listagem' => 1)
            );
            $relatorio = $this->paginate('RelatorioSmTeleconsult');
        }

        $this->set(compact('relatorio','filtros','status_teleconsult'));
    }

    function listagem_sms_status_profissional_export($conditions) {
        $this->render = false;
        $status_teleconsult = $this->Status->find('list');        
        $query = $this->RelatorioSmTeleconsult->listagem($conditions, null, null, null, true );        
        $dbo = $this->RelatorioSmTeleconsult->getDataSource();
        $dbo->results = $dbo->_execute($query);
        header('Content-type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment; filename="%s"', basename('sms_status_profissional_tlc.csv')));
        header('Pragma: no-cache');
        echo iconv('UTF-8', 'ISO-8859-1', 'SM;"Embarcador";"Transportador";"Placa";"Tipo do Veículo";"Alvo Origem";"CPF Motorista";"Nome Motorista";"Tipo Profissional TLC";"Último Status Antes SM";"Data Último Status Antes SM";"Último Status Atual";"Data Último Status Atual";');        
        while ($registro = $dbo->fetchRow()) {        
            $registro = $registro[0];
            $codigo_sm          = (!empty($registro['viag_codigo_sm']) ? $registro['viag_codigo_sm'] : '');
            $embarcador         = (!empty($registro['embarcador'])     ? comum::trata_nome($registro['embarcador'])     : '');
            $transportador      = (!empty($registro['transportador'])  ? comum::trata_nome($registro['transportador'])  : '');
            $placa              = (isset($registro['veic_placa'][0])   ? comum::formatarPlaca($registro['veic_placa'])  : '');
            $tipo_veiculo       = (!empty($registro['tvei_descricao']) ? comum::trata_nome($registro['tvei_descricao']) : '');
            $alvo_origem        = (!empty($registro['refe_descricao']) ? iconv('ISO-8859-1', 'UTF-8', $registro['refe_descricao']) : '' );
            $codigo_documento   = (!empty($registro['pfis_cpf'])       ? Comum::formatarDocumento($registro['pfis_cpf']) : '');
            $nome_profissional  = !empty($registro['pess_nome']) ? $registro['pess_nome']  : NULL;
            $nome_profissional  = preg_replace('/[^A-Za-z0-9\-]/', ' ', $nome_profissional );
            $tipo_profissional  = (!empty($registro['tipo_profissional']) ? iconv('ISO-8859-1', 'UTF-8', $registro['tipo_profissional']) : '' );
            $ultimo_status_antes_sm         = (!empty($registro['ultimo_status_antes_sm']) ? $status_teleconsult[$registro['ultimo_status_antes_sm']] : '');
            $data_ultimo_status_antes_sm    = (!empty($registro['data_ultimo_status_antes_sm']) ? $registro['data_ultimo_status_antes_sm'] : '' );
            $codigo_status      = (!empty($registro['codigo_status']) ? $status_teleconsult[$registro['codigo_status']] : '');
            $data_ultimo_status = (!empty($registro['data_ultimo_status']) ? $registro['data_ultimo_status'] : '');

            $linha = "";
            $linha .= '"'. $codigo_sm . '";';
            $linha .= '"'. $embarcador . '";';
            $linha .= '"'. $transportador . '";';
            $linha .= '"'. $placa . '";';
            $linha .= '"'. $tipo_veiculo . '";';
            $linha .= '"'. $alvo_origem . '";';
            $linha .= '"'. $codigo_documento . '";';
            $linha .= '"'. $nome_profissional . '";';
            $linha .= '"'. $tipo_profissional . '";';
            $linha .= '"'. $ultimo_status_antes_sm . '";';
            $linha .= '"'. $data_ultimo_status_antes_sm . '";';
            $linha .= '"'. $codigo_status . '";';
            $linha .= '"'. $data_ultimo_status . '";';     
            echo "\n".iconv('UTF-8', 'ISO-8859-1', $linha);
        }
        exit;
    }



}