<?php
class AtendimentosSacsController extends AppController {

    public $name = 'AtendimentosSacs';
    var $uses = array(
        'AtendimentoSac',
        'MotivoAtendimento',
        'TViagViagem',
        'Equipamento',
    );  
    public $helpers = array('BForm', 'Buonny', 'Ajax','Highcharts');

    function index(){
        $this->loadmodel("AtendimentoSac");
        $this->pageTitle = 'SAC';

        if(isset($_GET['placa'])) {
            //$this->BSession->setFlash('find_error');
            $this->AtendimentoSac->invalidate('placa', 'Por favor digite a placa');
        }
        if(!empty($_GET['placa'])) {
                $placa_sem_traco = strtoupper(str_replace('-', "", $_GET['placa']));
                $this->redirect(array('action' => 'salvar_registro_ligacao', $placa_sem_traco));
            } 
    
    }

    private function exportAtendimentos($query) {
        $dbo = $this->AtendimentoSac->getDataSource();
        header('Content-type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment; filename="%s"', basename('relatorio_chamadas.csv')));
        header('Pragma: no-cache');
        echo iconv('UTF-8', 'ISO-8859-1', '"SM";"Placa";"Motorista";"Transportador";"Observacao";"Motivo";"Atendente";"Tecnologia";"Data Cadastrada";"Ramal";')."\n";
        $dados = $dbo->fetchAll($query);
        foreach ($dados as $dado) {
            $linha = '"'.$dado['0']['sm'].'";';
            $linha .= '"'.$dado['0']['placa'].'";';
            $linha .= '"'.$dado['0']['motorista'].'";';
            $linha .= '"'.$dado['0']['transportador'].'";';
            $linha .= '"'.$dado['0']['observacao'].'";';
            $linha .= '"'.$dado['0']['motivo'].'";';
            $linha .= '"'.$dado['0']['apelido'].'";';
            $linha .= '"'.$dado['0']['tecnologia'].'";';
            $linha .= '"'.$dado['0']['data_cadastrada'].'";';
            $linha .= '"'.$dado['0']['ramal'].'";';
            $linha .= "\n";
            echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
        }
        die();
    }

    function salvar_registro_ligacao($placa = null) {
        $this->Filtros->limpa_sessao("AtendimentoSac");
        if(!empty($placa)) {
            $this->pageTitle = 'SAC';
            $this->loadmodel('Cliente');
            $this->loadmodel('Recebsm');
            $this->loadmodel('Veiculo');
            $this->loadmodel('TViagViagem');
            $this->loadmodel('TVeicVeiculo');       
            $this->loadmodel('TVembVeiculoEmbarcador');       
            $this->loadmodel('TVtraVeiculoTransportador');       
            $this->loadmodel('TPerfPerfil');       
           $this->TVeicVeiculo->bindModel(array('belongsTo' => array(
                        'TVcavVeiculoCavalo' => array('foreignKey' => 'veic_oras_codigo'),
                        'TErasEstacaoRastreamento' => array('foreignKey' => false, 'conditions' => array('eras_codigo = vcav_eras_codigo')),
                        'TErusEstacaoRastreamentoUsu' => array('foreignKey' => false, 'conditions' => array('erus_eras_codigo = eras_codigo', 'erus_monitor_gr2' => 'S')),
                        'TUsuaUsuario' => array('foreignKey' => false, 'conditions' => array('usua_pfis_pess_oras_codigo = erus_usua_pfis_pess_oras_codigo', 'usua_flg_logado' => true, "usua_heart_beat >= NOW() - interval '5 minutes'", "usua_perf_codigo in (".TPerfPerfil::OPERADOR_BUONNY.",".TPerfPerfil::OPERADOR_RASTREAMENTO.")")),

            )));

            $veic_veiculo = $this->TVeicVeiculo->buscaPorPlaca($placa,null,true,true);
            $this->loadmodel('Equipamento');
            $equipamento = $this->Equipamento->buscaPorCodigoGuardian($veic_veiculo['TTecnTecnologia']['tecn_codigo']);

            //Condições para a busca na viagem
            $conditions = array('veic_placa' => $placa, 'viag_data_fim' => NULL);
            $viagens = $this->TViagViagem->listar($conditions, null, 'all', null);
            foreach ($viagens as $key => $viagem) {
                if($viagem['TViagViagem']['viag_data_inicio'] == null) {
                    $viagens[$key]['status_sms'] = 'Agendado'; 
                } else if ($viagem['TViagViagem']['viag_data_inicio'] != null){
                    $viagens[$key]['status_sms'] = 'Em Transito'; 
                } else {
                    $viagens[$key]['status_sms'] = 'Não Localizado'; 
                }
            }
            //Pesquisa do transportador e embarcador
            $fields = array('codigo');
            if($viagens) {
                $transportador_cnpj = (isset($viagens[0]['TransportadorCnpj']['pjur_cnpj']) ? $viagens[0]['TransportadorCnpj']['pjur_cnpj'] : null);
                $embarcador_cnpj = (isset($viagens[0]['EmbarcadorCnpj']['pjur_cnpj']) ? $viagens[0]['EmbarcadorCnpj']['pjur_cnpj'] : null);
                $transportador_find_codigo = $this->Cliente->porCNPJ($transportador_cnpj, 'first', $fields);
                $embarcador_find_codigo = $this->Cliente->porCNPJ($embarcador_cnpj, 'first', $fields);
            } else {
                $cliente_veiculo = $this->Veiculo->clienteRelacionadosAoVeiculoPorPlaca($placa);
                if($cliente_veiculo)
                    $transportador_find_codigo   = $this->Cliente->porCNPJ($cliente_veiculo[0][0]['codigo_documento'], 'first', $fields);
                $embarcador_find_codigo = $this->Veiculo->buscaPorPlaca($placa, 'codigo_cliente_transportador_default');
            }
            //Validações
            $transportador_cod = (!empty($transportador_find_codigo['Cliente']['codigo']) ? $transportador_find_codigo['Cliente']['codigo'] : null);
            $embarcador_cod = (!empty($embarcador_find_codigo['Veiculo']['codigo_cliente_transportador_default']) ? $embarcador_find_codigo['Veiculo']['codigo_cliente_transportador_default'] : null);
            //Pega o valor do vetor '0', pois deve ser pegar a primera SM para gerar a chamada
            $codigo_sm = (isset($viagens[0]['TViagViagem']['viag_codigo_sm']) ? $viagens[0]['TViagViagem']['viag_codigo_sm'] : null);
            $motivos = $this->MotivoAtendimento->find('list');

            $options['conditions'] = array('AtendimentoSac.placa' => $placa);
            $options['limit'] = '3';
            $options['order'] = 'AtendimentoSac.codigo DESC';
            $atendimentos = $this->listar_registros($options);
            $this->set(compact('viagens', 'transportador_cod', 'embarcador_cod', 'motivos', 'codigo_sm', 'placa', 'veic_veiculo', 'equipamento', 'atendimentos'));
            
        } else {
            $this->redirect(array('action' => 'index'));
        }
        if($this->RequestHandler->isPost()) {
            $observacao =  (!empty($this->data['AtendimentoSac']['observacao']) ? $this->data['AtendimentoSac']['observacao'] : null);
            $this->data['AtendimentoSac']['observacao'] = utf8_decode($observacao);
            if(!empty($this->data['AtendimentoSac']['ramal_encaminhado'])) {
                $this->data['AtendimentoSac']['ramal_encaminhado'];
            } else {
                unset($this->data['AtendimentoSac']['ramal_encaminhado']);
            }
            if ($this->AtendimentoSac->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
    }

    function listar_registros($options ,$paginate = false, $tipo_find = 'all') {
        $filtros = $this->Filtros->controla_sessao($this->data, "AtendimentoSac");
        $this->loadmodel('Usuario'); 
        $this->loadmodel('Recebsm'); 
        $this->loadmodel('MotivoAtendimento'); 
        $this->AtendimentoSac->bindModel(
            array(
                'hasOne'=>array(
                    'Usuario' => array(
                        'className'  =>  'Usuario',
                        'foreignKey' => false,
                        'conditions' => array("Usuario.codigo = AtendimentoSac.codigo_usuario_inclusao"),
                    ),
                    'MotivoAtendimento' => array(
                        'className'  =>  'MotivoAtendimento',
                        'foreignKey' => false,
                        'conditions' => array('MotivoAtendimento.codigo = AtendimentoSac.codigo_motivo_atendimento'),
                    ),
                    'Recebsm' => array(
                        'className'  =>  'Recebsm',
                        'foreignKey' => false,
                        'conditions' => array('Recebsm.SM = AtendimentoSac.codigo_sm'),    
                    ),
                    'Equipamento' => array(
                        'className'  =>  'Equipamento',
                        'foreignKey' => false,
                        'conditions' => array('Equipamento.Codigo = AtendimentoSac.codigo_tecnologia'),   
                    ),
                  'Transportador' => array(
                        'className'  =>  'Cliente',
                        'foreignKey' => false,
                        'conditions' => array('AtendimentoSac.codigo_cliente_transportador = Transportador.codigo'),   
                    ),
                    'Embarcador' => array(
                        'className'  =>  'Cliente',
                        'foreignKey' => false,
                        'conditions' => array('AtendimentoSac.codigo_cliente_embarcador = Embarcador.codigo'),   
                    ),
            )), false
        );

        $this->TViagViagem->bindModel(array(
            'hasOne' => array(
                'TVveiViagemVeiculo' => array('foreignKey' => false, 'conditions' => array('viag_codigo = vvei_viag_codigo', 'vvei_precedencia' => '1')),
                'TVeicVeiculo'       => array('foreignKey' => false, 'conditions' => array('veic_oras_codigo = vvei_veic_oras_codigo')),
                'Motorista'          => array('foreignKey' => false, 'className' => 'TPessPessoa', 'conditions' => 'Motorista.pess_oras_codigo = TVveiViagemVeiculo.vvei_moto_pfis_pess_oras_codigo'),
            ),
        ));

        $conditions_pg = array();
        if( !empty($filtros['data_inicial']) && !empty($filtros['data_final']) ){
            $conditions_pg = array(
                'OR' => array(
                    'TViagViagem.viag_data_fim' => NULL,
                    'TViagViagem.viag_data_fim BETWEEN ? AND ? ' => array( 
                    AppModel::dateToDbDate2($filtros['data_inicial'].' 00:00:00' ),
                    AppModel::dateToDbDate2($filtros['data_final'].' 23:59:59' )
                    )
                )
            );
            if( !empty($options['conditions']['AtendimentoSac.placa']))
                $conditions_pg['TVeicVeiculo.veic_placa LIKE'] = strtoupper($options['conditions']['AtendimentoSac.placa']);
        } else {
            $conditions_pg['TVeicVeiculo.veic_placa LIKE'] = strtoupper($options['conditions']['AtendimentoSac.placa']);
        }
        $query_pg = $this->TViagViagem->find('sql', array(
            'fields'        => array('pess_nome AS pess_nome', 'viag_codigo_sm AS viag_codigo_sm'),
            'conditions'    => array($conditions_pg)
        ));

        $query_pg = str_replace('"', '', $query_pg);
        $query_pg = trim(str_replace("'", "''", $query_pg));
        $this->AtendimentoSac->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
        $joins = array(
            array(
                'table' => "(SELECT * FROM openquery(LK_GUARDIAN,'".$query_pg."'))",
                'alias' => 'ViagViagemMotorista',
                'type'  => 'LEFT',
                'conditions' => array('ViagViagemMotorista.viag_codigo_sm = AtendimentoSac.codigo_sm')
            )
        );        

        $options['fields'] = array(
            'AtendimentoSac.codigo              AS codigo',
            'AtendimentoSac.codigo_sm           AS sm',
            'AtendimentoSac.observacao          AS observacao',
            'ViagViagemMotorista.pess_nome      AS motorista',
            'Usuario.apelido                    AS apelido',
            'MotivoAtendimento.descricao        AS motivo',
            'AtendimentoSac.ramal_encaminhado   AS ramal',
            "convert(varchar(10), AtendimentoSac.data_inclusao, 103) + ' ' + convert(varchar(8), AtendimentoSac.data_inclusao, 108) AS data_cadastrada",
            'AtendimentoSac.placa               AS placa',
            'AtendimentoSac.codigo_tecnologia   AS codigo_tecnologia',
            'Equipamento.Descricao              AS tecnologia',
            'Transportador.razao_social         AS transportador',        
            'Embarcador.razao_social            AS embarcador'        
        );

        if($paginate) {
            $this->paginate['AtendimentoSac']  = array(
            'conditions'    => $options['conditions'],
            'limit'         => 50,
            'joins'         => $joins,
            'fields'        => $options['fields'],
            'order'         => 'codigo',
            );
            $atendimentos = $this->paginate('AtendimentoSac');
            $this->set(compact('atendimentos'));
        } else {
            $atendimentos = $this->AtendimentoSac->find($tipo_find, array(
                'fields'     => $options['fields'],
                'conditions' => $options['conditions'],
                'joins'      => $joins,
                'order'      => 'codigo'
            ));
            return $atendimentos;
        }
    }

    function carregar_combos() {
       if(empty($this->data['AtendimentoSac'])) {
            $this->data['AtendimentoSac']['hora_inicial'] = '00:00';
       }
        if(empty($this->data['AtendimentoSac']['hora_final'])) {
            $this->data['AtendimentoSac']['hora_final'] = '23:59';
        }
        if(empty($this->data['AtendimentoSac']['data_inicial'])) {
            $this->data['AtendimentoSac']['data_inicial'] = date('d/m/Y');
        }
        if(empty($this->data['AtendimentoSac']['data_final'])) {
            $this->data['AtendimentoSac']['data_final'] = date('d/m/Y');
        }
        $motivos = $this->MotivoAtendimento->find('list');
        $agrupamento = $this->AtendimentoSac->listarAgrupamentos();
        $tecnologia = $this->Equipamento->find('list');
        $this->set(compact('motivos','tecnologia','agrupamento'));
    }
    
    function analitico($new_window = FALSE) {
        $filtrado = FALSE; 
        if($new_window){
            $this->layout = 'new_window';
            $filtrado = TRUE; 
        }
        $this->pageTitle = 'SAC Analítico';
        $this->data['AtendimentoSac'] = $this->Filtros->controla_sessao($this->data, "AtendimentoSac");
        $this->carregar_combos();        
        $this->set(compact('filtrado'));
    }

 	function analitico_listagem($export = false) {
        $this->layout = 'ajax';
        $this->data['AtendimentoSac'] = $this->Filtros->controla_sessao($this->data, "AtendimentoSac");
        $options['conditions'] = $this->AtendimentoSac->converteFiltroEmCondition($this->data);
        $this->listar_registros($options, true);
        if($export){
            $dados = $this->listar_registros($options, false, 'sql');
            $this->exportAtendimentos($dados);
        }        
    }

    function visualizar($codigo){
        $this->layout = 'new_window';
        $this->loadmodel('Cliente');
        $this->loadmodel('Usuario');
        $this->loadmodel('Equipamento');
        $this->loadmodel('MotivoAtendimento');
        $this->pageTitle = 'Atendimento';
        $this->AtendimentoSac->bindModel(
            array(
                'hasOne'=>array(
                    'Recebsm' => array(
                        'className'  =>  'Recebsm',
                        'foreignKey' => false,
                        'conditions' => array('Recebsm.SM = AtendimentoSac.codigo_sm'),    
                    ),
                     'Motorista' => array(
                        'className'  =>  'Motorista',
                        'foreignKey' => false,
                        'conditions' => array('Motorista.Codigo = Recebsm.MotResp'),   
                    ),
            )), false
        );

        $conditions = array('AtendimentoSac.codigo' => $codigo);
        $fields = array('AtendimentoSac.codigo_usuario_inclusao',
                        'AtendimentoSac.ramal_encaminhado',
                        'AtendimentoSac.observacao',
                        'AtendimentoSac.codigo_sm',
                        'AtendimentoSac.placa',
                        'AtendimentoSac.codigo_motivo_atendimento',
                        'AtendimentoSac.data_inclusao',
                        'AtendimentoSac.codigo_cliente_transportador',
                        'AtendimentoSac.codigo_cliente_embarcador',
                        'AtendimentoSac.codigo_motivo_atendimento',
                        'AtendimentoSac.codigo_tecnologia',
                        'Motorista.Nome',
                        );

        $this->data = $this->AtendimentoSac->find('first', compact('conditions', 'fields'));
        //$this->data = $this->AtendimentoSac->carregar($codigo);
        $fields = array('Cliente.razao_social as nome');
        $nome_cliente_embarcador = $this->Cliente->buscaPorCodigo($this->data['AtendimentoSac']['codigo_cliente_embarcador'], $fields);
        $nome_cliente_transportador = $this->Cliente->buscaPorCodigo($this->data['AtendimentoSac']['codigo_cliente_transportador'], $fields);
        $tecnologia = $this->Equipamento->find('list');
        $motivos = $this->MotivoAtendimento->find('list'); 
        $conditions = array('Usuario.codigo' => $this->data['AtendimentoSac']['codigo_usuario_inclusao']);
        $fields = array('Usuario.nome       AS nome',
                        'Usuario.apelido    AS apelido');
        $nome_usuario_inclusao = $this->Usuario->find('first', compact('conditions', 'fields'));        
        $this->set(compact('motivos', 'nome_cliente_transportador', 'nome_cliente_embarcador', 'nome_usuario_inclusao', 'tecnologia'));
    }

    public function sintetico() {
        $this->pageTitle = 'SAC Sintético';
        $this->data['AtendimentoSac'] = $this->Filtros->controla_sessao($this->data, "AtendimentoSac");
        $filtrado = FALSE;      
        $this->carregar_combos();
        $this->set(compact('filtrado'));
    }

    public function sintetico_listagem() {
        $this->data['AtendimentoSac'] = $this->Filtros->controla_sessao($this->data, "AtendimentoSac");
        $conditions = $this->AtendimentoSac->converteFiltroEmCondition($this->data);
        $agrupamento = $this->data['AtendimentoSac']['agrupamento'];
        $atendimentos = $this->AtendimentoSac->sintetico($conditions,$agrupamento);
        if(!empty($atendimentos)){
            $this->sintetico_grafico($atendimentos,$agrupamento);
        }
        $this->set(compact('atendimentos','agrupamento'));
    }

    function sintetico_grafico($atendimentos,$agrupamento){
        foreach ($atendimentos as $atendimento) {
            $qtd[] = $atendimento[0]['qtd'];
            $descricao[] = "'".$atendimento[0]['descricao']."'";
        }
        $descricao_agrupamento = $this->AtendimentoSac->retornaAgrupamento($agrupamento);
        $dadosGrafico['eixo_x'] = $descricao;
        $dadosGrafico['series'] =  array(
            array(
                'name' => "'$descricao_agrupamento'",
                'values' => $qtd
            )
        );            
        $this->set(compact('dadosGrafico'));
    }
}
