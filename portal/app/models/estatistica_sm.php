<?php
class EstatisticaSm extends AppModel {
    var $name = 'EstatisticaSm';
    var $tableSchema = 'monitora';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'estatistica_sm';
    var $primaryKey = 'codigo';
   
    const TIPO_HORA = 1;
    const TIPO_DIA = 2;
    const TIPO_SEMANA = 3;
    const TIPO_MES = 4;
    function tipos() {
        return array(
                1 => 'Hora',
                2 => 'Diário', 
                3 => 'Semanal', 
                4 => 'Mensal'
            );
    }
    
    const TIPO_FILTRO_CONDICAO_E = 0;
    const TIPO_FILTRO_CONDICAO_OU = 1;
    
 
    function carregaLista($dados) {
        if ($dados[$this->name]['tipo'] == EstatisticaSm::TIPO_HORA) {
            return $this->carregaListaPorHora($dados);
        }
        if ($dados[$this->name]['tipo'] == EstatisticaSm::TIPO_DIA) {
            return $this->carregaListaPorDia($dados);
        }
        if ($dados[$this->name]['tipo'] == EstatisticaSm::TIPO_SEMANA) {
            return $this->carregaListaPorSemana($dados);
        }
        if ($dados[$this->name]['tipo'] == EstatisticaSm::TIPO_MES) {
            return $this->carregaListaPorMes($dados);
        }
    }
    
    function converteDataParaDataPhp($data) {
        $data_especificada = false;
        if ($data == null)
            $data = Date('d/m/Y H:i:s');
        elseif (strlen(trim($data))<11)
            $data .= Date(' H:i:s');
        else
            $data_especificada = true;
        $data_php = preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})(\w*)/", "$3-$2-$1$4", $data);
        if (!$data_especificada && Date('Ymd', strtotime($data_php)) != Date('Ymd'))
            $data_php = Date('Ymd', strtotime($data_php)).' 23:59:59';
        return $data_php;
    }
    
    function converteListaOperacao($registros, $tipo_data = 'datetime') {
        $lista = array();
        $model_name = '0';
        foreach ($registros as $registro) {
            $chave_data = ($tipo_data == 'datetime' ? $registro[$model_name]['data'] : substr($registro[$model_name]['data'],0,10));
            $chave_data = AppModel::dbDateToDate($chave_data);
            if (!isset($lista[$chave_data]))
                $lista[$chave_data] = array();
            unset($registro[$model_name]['codigo']);
            $registro[$model_name]['em_andamento_por_operador'] = ($registro[$model_name]['operadores'] > 0 ? $registro[$model_name]['em_andamento'] / $registro[$model_name]['operadores'] : 0);
            unset($registro[$model_name]['data']);
            $lista[$chave_data][] = $registro[$model_name];
        }
        return $lista;
    }
    
    function carregaListaPorHora($dados) {
        $this->EstatisticaSmOperacaoHora = ClassRegistry::init('EstatisticaSmOperacaoHora');
        $data_php = $this->converteDataParaDataPhp($dados[$this->name]['data']);
        $hora_final = Date('Ymd H:i:s', strtotime($data_php));
        $data_php = Date('Ymd H:00:00', strtotime('-23 hour', strtotime($data_php)));
        $lista = $this->EstatisticaSmOperacaoHora->listaPorPeriodo(array($data_php, $hora_final));
        return $this->converteListaOperacao($lista);
    }
    
    function carregaListaPorDia($dados) {
        $this->EstatisticaSmOperacaoHora = ClassRegistry::init('EstatisticaSmOperacaoHora');
        $data_php = $this->converteDataParaDataPhp($dados[$this->name]['data']);
        $hora_final = Date('Ymd H:i:s', strtotime($data_php));
        $data_php = Date('Ymd H:00:00', strtotime('-'.Date('H', strtotime($data_php)).' hour', strtotime($data_php)));
        $lista = $this->EstatisticaSmOperacaoHora->listaPorPeriodo(array($data_php, $hora_final));
        return $this->converteListaOperacao($lista);
    }
    
    function carregaListaPorSemana($dados) {
        $this->EstatisticaSmOperacaoDia = ClassRegistry::init('EstatisticaSmOperacaoDia');
        $data_php = $this->converteDataParaDataPhp($dados[$this->name]['data']);
        $data_final = $data_php;
        $data_php = Date('Ymd H:00:00', strtotime('-7 day', strtotime($data_php)));
        $lista = $this->EstatisticaSmOperacaoDia->listaPorPeriodo(array($data_php, $data_final));
        return $this->converteListaOperacao($lista, 'date');
    }
    
    function carregaListaPorMes($dados) {
        $this->EstatisticaSmOperacaoDia = ClassRegistry::init('EstatisticaSmOperacaoDia');
        $data_php = $this->converteDataParaDataPhp($dados[$this->name]['data']);
        $dia_final = date('d', strtotime($data_php));
        $data_final = Date("Ym{$dia_final} 23:59:59", strtotime($data_php));
        $data_php = Date('Ym01 00:00:00', strtotime($data_php));
        $lista = $this->EstatisticaSmOperacaoDia->listaPorPeriodo(array($data_php, $data_final));
        return $this->converteListaOperacao($lista, 'date');
    }
    
    function periodo($data, $tipo){
        $data_php = $this->converteDataParaDataPhp($data);
        if ($tipo == EstatisticaSm::TIPO_HORA) {
            return array(Date('Ymd H:00:00', strtotime($data_php)), Date('Ymd H:59:59', strtotime($data_php)));
        } elseif ($tipo == EstatisticaSm::TIPO_DIA) {
            return array(Date('Ymd 00:00:00', strtotime($data_php)), Date('Ymd 23:59:59', strtotime($data_php)));
        } elseif ($tipo == EstatisticaSm::TIPO_SEMANA) {
            return array(Date('Ymd 00:00:00', strtotime('-1 week', strtotime($data_php))), Date('Ymd 23:59:59', strtotime($data_php)));
        } elseif ($tipo == EstatisticaSm::TIPO_MES) {
            return array(Date('Ymd 00:00:00', strtotime('-1 month', strtotime($data_php))), Date('Ymd 23:59:59', strtotime($data_php)));
        }
    }
    
    function consolidaOperacao($periodo) {
        $ultima_data = $this->ultimaDataDoPeriodo($periodo);
        $group = array('codigo_tipo_operacao', 'MClienteOperacao.descricao');
        $fields = array_merge($group, array(
            'isnull(sum(case andamento when 0 then 1 else 0 end), 0) as em_aberto',
            'isnull(sum(case andamento when 1 then 1 else 0 end), 0) as em_andamento',
            'isnull(sum(ocorrencia), 0) as ocorrencias',
            'isnull(count(distinct case andamento when 1 then codigo_operador else null end), 0) as operadores',            
        ));
        $this->bindModel(array('belongsTo' => array('MClienteOperacao' => array('foreignKey' => 'codigo_tipo_operacao'))));
        if (empty($ultima_data)) {
            $results = array();
        } else {
            $results = $this->find('all', array('fields' => $fields, 'conditions' => array('data_inclusao BETWEEN ? AND ?' => array($ultima_data.'.000', $ultima_data.'.999'), 'not' => array('codigo_tipo_operacao' => null)), 'group' => $group));
        }
        $dados = array();
        foreach ($results as $result) {
            $result[0]['em_andamento_por_operador'] = ($result[0]['operadores'] > 0 ? $result[0]['em_andamento'] / $result[0]['operadores'] : 0);
            $dados[] = array_merge($result['MClienteOperacao'], array_merge($result[$this->name], $result[0]));
        }
        return $dados;
    }
    
    function ultimaDataDoPeriodo($periodo) {
        $ultima_data = $this->find('first', array('fields' => array('convert(varchar, max(data_inclusao), 120) as ultima_data'), 'conditions' => array('data_inclusao between ? and ?' => $periodo)));
        return $ultima_data[0]['ultima_data'];
    }

    function consolidaTotal($periodo) {
        $ultima_data = $this->ultimaDataDoPeriodo($periodo);
        $fields = array(
            'isnull(count(distinct codigo_tipo_operacao), 0) as operacoes', 
            'isnull(sum(case andamento when 0 then 1 else 0 end), 0) as em_aberto',
            'isnull(sum(case andamento when 1 then 1 else 0 end), 0) as em_andamento',
            'isnull(sum(ocorrencia), 0) as ocorrencias',
            'isnull(count(distinct case andamento when 1 then codigo_operador else null end), 0) as operadores',            
        );
        if (empty($ultima_data)) {
            $results = array();
        } else {
            $results = $this->find('all', array('fields' => $fields, 'conditions' => array('data_inclusao BETWEEN ? AND ?' => array($ultima_data.'.000', $ultima_data.'.999'), 'not' => array('codigo_tipo_operacao' => null))));
        }
        foreach ($results as $key => $result)
            $results[$key][0]['em_andamento_por_operador'] = ($result[0]['operadores'] > 0 ? $result[0]['em_andamento'] / $result[0]['operadores'] : 0);
        return $this->limpaArray($results);
    }
    
    function consolidaPorOperador($codigo_tipo_operacao = 0, $periodo) {
        $ultima_data = $this->ultimaDataDoPeriodo($periodo);
        $group = array('codigo_tipo_operacao', 'codigo_operador');
        $fields = array_merge($group, array(
            'isnull(sum(case andamento when 0 then 1 else 0 end), 0) as em_aberto',
            'isnull(sum(case andamento when 1 then 1 else 0 end), 0) as em_andamento',
            'isnull(sum(ocorrencia), 0) as ocorrencias',
            'isnull(count(distinct case andamento when 1 then codigo_operador else null end), 0) as operadores',
            'isnull(count(distinct codigo_tipo_operacao), 0) as operacoes', 
        ));
        $conditions = array('data_inclusao BETWEEN ? AND ?' => array($ultima_data.'.000', $ultima_data.'.999'), 'not' => array('codigo_tipo_operacao' => null));
        if ($codigo_tipo_operacao > 0)
            $conditions['codigo_tipo_operacao'] = $codigo_tipo_operacao;

        if (empty($ultima_data)) {
            $results = array();
        } else {
            $results = $this->find('all', array('fields' => $fields, 'conditions' => $conditions, 'group' => $group));
        }
        $dados = array();
        foreach ($results as $result)
            $dados[] = array_merge($result[$this->name], $result[0]);
        return $dados;
    }
    
    function consolidaPorCliente($periodo) {
        $ultima_data = $this->ultimaDataDoPeriodo($periodo);
        $group = array('cliente');
        $fields = array(
            'cliente as codigo_cliente',
            'isnull(sum(case andamento when 0 then 1 else 0 end), 0) as em_aberto',
            'isnull(sum(case andamento when 1 then 1 else 0 end), 0) as em_andamento',
            'isnull(sum(ocorrencia), 0) as ocorrencias',
            'isnull(count(distinct case andamento when 1 then codigo_operador else null end), 0) as operadores',            
            'isnull(count(distinct codigo_tipo_operacao), 0) as operacoes',         
        );
        if (empty($ultima_data)) {
            $results = array();
        } else {
            $results = $this->find('all', array('fields' => $fields, 'conditions' => array('data_inclusao BETWEEN ? AND ?' => array($ultima_data.'.000', $ultima_data.'.999'), 'not' => array('codigo_tipo_operacao' => null)), 'group' => $group));
        }
        return $this->limpaArray($results);
    }
    
    function limpaArray($results) {
        $lista = array();
        foreach ($results as $result) {
            $lista[] = $result[0];
        }
        return $lista;
    }
    
    function carregaPorOperacao($dados) {
        $periodo = $this->periodo($dados[$this->name]['data'], $dados[$this->name]['tipo']);
        return $this->consolidaOperacao($periodo);
    }
    
    function carregaPorOperador($dados) {
        $tipo  = (isset($dados[$this->name]['tipo']) && !empty($dados[$this->name]['tipo'])) ? $dados[$this->name]['tipo'] : self::TIPO_DIA;
        $class = (isset($dados[$this->name]['tipo']) && $dados[$this->name]['tipo'] != self::TIPO_HORA) ? 'Dia' : 'Hora';
        $class = 'EstatisticaSmOperador' . $class;
        $this->{$class} = ClassRegistry::init($class);
        $lista = $this->{$class}->listaPorPeriodo($dados[$this->name]['data'], $tipo, $dados);
        return $lista;
    }
    
    function carregaPorCliente($dados) {
        $tipo  = (isset($dados[$this->name]['tipo']) && !empty($dados[$this->name]['tipo'])) ? $dados[$this->name]['tipo'] : self::TIPO_DIA;
        $class = (isset($dados[$this->name]['tipo']) && $dados[$this->name]['tipo'] != self::TIPO_HORA) ? 'Dia' : 'Hora';
        $class = 'EstatisticaSmCliente' . $class;
        $this->{$class} = ClassRegistry::init($class);
        $lista = $this->{$class}->listaPorPeriodo($dados[$this->name]['data'], $tipo, $dados);
        return $lista;
    }
    

    function carregaTotalLista($dados) {
        if ($dados[$this->name]['tipo'] == EstatisticaSm::TIPO_HORA) {
            return $this->carregaTotalListaPorHora($dados);
        }
        if ($dados[$this->name]['tipo'] == EstatisticaSm::TIPO_DIA) {
            return $this->carregaTotalListaPorDia($dados);
        }
        if ($dados[$this->name]['tipo'] == EstatisticaSm::TIPO_SEMANA) {
            return $this->carregaTotalListaPorSemana($dados);
        }
        if ($dados[$this->name]['tipo'] == EstatisticaSm::TIPO_MES) {
            return $this->carregaTotalListaPorMes($dados);
        }
    }
    
    function converteListaTotal($model_name, $registros, $tipo_data = 'datetime') {
        $lista = array();
        foreach ($registros as $registro) {
            unset($registro[$model_name]['codigo']);
            $registro[$model_name]['em_andamento_por_operador'] = ($registro[$model_name]['operadores'] > 0 ? $registro[$model_name]['em_andamento'] / $registro[$model_name]['operadores'] : 0);
            $chave = ($tipo_data == 'datetime' ? $registro[$model_name]['data'] : substr($registro[$model_name]['data'],0,10));
            unset($registro[$model_name]['data']);
            $lista[$chave][0] = $registro[$model_name];
        }
        return $lista;
    }
    
    function carregaTotalListaPorHora($dados) {
        $this->EstatisticaSmGeralHora = ClassRegistry::init('EstatisticaSmGeralHora');
        $data_php = $this->converteDataParaDataPhp($dados[$this->name]['data']);
        $hora_final = Date('Ymd H:i:s', strtotime($data_php));
        $data_php = Date('Ymd H:00:00', strtotime('-23 hour', strtotime($data_php)));
        $lista = $this->EstatisticaSmGeralHora->find('all', array('conditions' => array('data between ? and ?' => array($data_php, $hora_final))));
        return $this->converteListaTotal('EstatisticaSmGeralHora', $lista);
    }
    
    function carregaTotalListaPorDia($dados) {
        $this->EstatisticaSmGeralHora = ClassRegistry::init('EstatisticaSmGeralHora');
        $data_php = $this->converteDataParaDataPhp($dados[$this->name]['data']);
        $hora_final = Date('Ymd H:i:s', strtotime($data_php));
        $data_php = Date('Ymd H:00:00', strtotime('-'.Date('H', strtotime($data_php)).' hour', strtotime($data_php)));
        $lista = $this->EstatisticaSmGeralHora->find('all', array('conditions' => array('data between ? and ?' => array($data_php, $hora_final))));
        return $this->converteListaTotal('EstatisticaSmGeralHora', $lista);
    }
    
    function carregaTotalListaPorSemana($dados) {
        $this->EstatisticaSmGeralDia = ClassRegistry::init('EstatisticaSmGeralDia');
        $data_php = $this->converteDataParaDataPhp($dados[$this->name]['data']);
        $data_final = Date('Ymd 23:59:59', strtotime($data_php));
        $data_php = Date('Ymd 00:00:00', strtotime('-6 day', strtotime($data_php)));
        $lista = $this->EstatisticaSmGeralDia->find('all', array('conditions' => array('data between ? and ?' => array($data_php, $data_final))));
        return $this->converteListaTotal('EstatisticaSmGeralDia', $lista, 'date');
    }
    
    function carregaTotalListaPorMes($dados) {
        $this->EstatisticaSmGeralDia = ClassRegistry::init('EstatisticaSmGeralDia');
        $data_php = $this->converteDataParaDataPhp($dados[$this->name]['data']);
        $primeiro_dia_mes_seguinte = date('Ym01 H:00:00', strtotime('+1 month', strtotime($data_php)));
        $dia_final = date('Ymd 23:59:59', strtotime('-1 day', strtotime($primeiro_dia_mes_seguinte)));
        $data_php = Date('Ym01 00:00:00', strtotime($data_php));
        $lista = $this->EstatisticaSmGeralDia->find('all', array('conditions' => array('data between ? and ?' => array($data_php, $dia_final))));
        return $this->converteListaTotal('EstatisticaSmGeralDia', $lista, 'date');
    }   
    
    function converteFiltrosEmConditions( $filtro ) {
        
        $conditions = array();
        $condition_tecnologia = array();
        
        if( isset( $filtro['codequipamento'] ) && !empty( $filtro['codequipamento'] ) )
            $condition_tecnologia['Recebsm.CodEquipamento'] = $filtro['codequipamento'];
        
        if( isset( $filtro['cod_operacao'] ) && !empty( $filtro['cod_operacao'] ) )
            $conditions[ $this->name . '.codigo_tipo_operacao' ] = $filtro['cod_operacao'];       
            
        if( isset( $filtro['cod_operador'] ) && !empty( $filtro['cod_operador'] ) )
            $conditions[ $this->name . '.codigo_operador' ] = $filtro['cod_operador'];
        
        if( isset( $filtro['status'] ) && !empty( $filtro['status'] ) ){         
            
            $status = $filtro['status'][0];
            
            if ( $status != '*' ) 
                $conditions[ $this->name . '.andamento' ] = $filtro['status'];
            else
                $conditions[ $this->name . '.andamento' ] = array( 0, 1 );           
        }
                
        if( isset( $filtro['data'] ) && !empty( $filtro['data'] )  ) 
            $conditions['CONVERT(VARCHAR(20), data_inclusao, 20)'] = AppModel::dateToDbDate($filtro['data']);        
        
        if( isset( $filtro['sm'] ) && !empty( $filtro['sm'] ) )
            $conditions[ $this->name . '.codigo_sm' ] = $filtro['sm'];
        
        if (isset($filtro['codequipamento']) && count($filtro['codequipamento']) > 0 && isset($filtro['tipo_filtro_operacoes']) && $filtro['tipo_filtro_operacoes'] == EstatisticaSm::TIPO_FILTRO_CONDICAO_OU) { 
            $codigo_tipo_operacao = array( $this->name . '.codigo_tipo_operacao' => $conditions[ $this->name . '.codigo_tipo_operacao' ] );
            $condition_tecnologia = array( 'Recebsm.CodEquipamento' => $filtro['codequipamento'] );
            $conditions['OR'] = array($condition_tecnologia,  $codigo_tipo_operacao ); 
            unset( $conditions[ $this->name . '.codigo_tipo_operacao' ] );
        }
        
        if( isset( $filtro['operador'] ) && !empty( $filtro['operador'] ) )
            $conditions[ 'Funcionario.apelido LIKE' ] = '%'.$filtro['operador'].'%';
        
        return $conditions;
     }    
    
    function listarSm( $conditions, $tipo ) {
        $periodo = $this->periodo( $conditions['CONVERT(VARCHAR(20), data_inclusao, 20)'], $tipo ); 
        //$conditions['CONVERT(VARCHAR(20), data_inclusao, 20)'] = $this->ultimaDataDoPeriodo( $periodo );
		$conditions['EstatisticaSm.data_inclusao'] = $this->ultimaDataDoPeriodo( $periodo );
		unset($conditions['CONVERT(VARCHAR(20), data_inclusao, 20)']);
        
        $ClientEmpresa    = ClassRegistry::init('ClientEmpresa');
        $Recebsm          = ClassRegistry::init('Recebsm');
        $Equipamento      = ClassRegistry::init('Equipamento');
        $Funcionario      = ClassRegistry::init('Funcionario');
        $OperacaoMonitora = ClassRegistry::init('OperacaoMonitora');
        
        $joins = array(            
            array(
               'table' => "{$ClientEmpresa->databaseTable}.{$ClientEmpresa->tableSchema}.{$ClientEmpresa->useTable}",
                'alias' => 'ClientEmpresa',
                'conditions' => 'ClientEmpresa.codigo = EstatisticaSm.cliente',
                'type' => 'left',
            ),
            array(
                'table' => "{$Recebsm->databaseTable}.{$Recebsm->tableSchema}.{$Recebsm->useTable}",
                'alias' => 'Recebsm',
                'conditions' => 'EstatisticaSm.codigo_sm = Recebsm.sm',
                'type' => 'left',
            ),
            array(
                'table' => "{$Equipamento->databaseTable}.{$Equipamento->tableSchema}.{$Equipamento->useTable}",
                'alias' => 'Equipamento',
                'conditions' => 'Equipamento.Codigo = Recebsm.CodEquipamento',
                'type' => 'left',
            ),
            array(
                'table' => "{$Funcionario->databaseTable}.{$Funcionario->tableSchema}.{$Funcionario->useTable}",
                'alias' => 'Funcionario',
                'conditions' => 'Funcionario.Codigo = EstatisticaSm.codigo_operador',
                'type' => 'left',
            ),
            array(
                'table' => "{$OperacaoMonitora->databaseTable}.{$OperacaoMonitora->tableSchema}.{$OperacaoMonitora->useTable}",
                'alias' => 'OperacaoMonitora',
                'conditions' => 'OperacaoMonitora.cod_operacao = EstatisticaSm.codigo_tipo_operacao',
                'type' => 'left',
            )
        );
        
        $result = $this->find( 'all', array(            
               'fields' => array(
                   'EstatisticaSm.codigo',
                   'EstatisticaSm.codigo_sm',
                   'EstatisticaSm.cliente',
                   'EstatisticaSm.codigo_operador',
                   'EstatisticaSm.codigo_tipo_operacao',
                   'EstatisticaSm.andamento',
                   'EstatisticaSm.ocorrencia',
                   'EstatisticaSm.data_inclusao',
                   'EstatisticaSm.codigo_usuario_inclusao',
                   'ClientEmpresa.raz_social',
                   'Recebsm.sm',   
                   'Equipamento.descricao',
                   'Funcionario.apelido',
                   'OperacaoMonitora.descricao',
                ),
                'conditions' => $conditions,
              'joins' => $joins
        ) );

        return $result;
    }

    function carregarTabela() {
        $Funcionario = ClassRegistry::init('Funcionario');
        $ClientEmpresa = ClassRegistry::init('ClientEmpresa');
        $Recebsm = ClassRegistry::init('Recebsm');
        $Ocorrencia = ClassRegistry::init('Ocorrencia');
        $StatusOcorrencia = ClassRegistry::init('StatusOcorrencia');

        $AtendimentoSm = ClassRegistry::init('AtendimentoSm');
        $PassoAtendimentoSm = ClassRegistry::init('PassoAtendimentoSm');
        $HistoricoSm = ClassRegistry::init('HistoricoSm');

        $query = "insert into
                {$this->databaseTable}.{$this->tableSchema}.{$this->useTable}
                    (codigo_sm, cliente, codigo_operador, codigo_tipo_operacao, andamento, ocorrencia, data_inclusao, codigo_usuario_inclusao)
                select distinct
                    sm.sm,
                    sm.cliente,
                    operador.codigo,
                    isnull(cliente.tipo_operacao, 14),
                    case
                        when (Select top 1 1  from {$Recebsm->databaseTable}.{$Recebsm->tableSchema}.acomp_viagem where acomp_viagem.sm = sm.sm) = 1
                            then 1
                            else 0
                    end status,
                    (";
        if (false) { // Metodo antigo
            $query .= "Select count(*) from {$Ocorrencia->databaseTable}.{$Ocorrencia->tableSchema}.{$Ocorrencia->useTable} ocorrencia 
                    left join {$StatusOcorrencia->databaseTable}.{$StatusOcorrencia->tableSchema}.{$StatusOcorrencia->useTable} status_ocorrencia on ocorrencia.codigo_status_ocorrencia = status_ocorrencia.codigo
                    where ocorrencia.codigo_sm = sm.sm and ocorrencia.usuario_monitora_inclusao = cast(sm.operador as integer) and status_ocorrencia.fechado != 1";
        } else {
            $query .= "Select count(distinct AtendimentoSm.codigo) from {$HistoricoSm->databaseTable}.{$HistoricoSm->tableSchema}.{$HistoricoSm->useTable} HistoricoSm
                    left join {$PassoAtendimentoSm->databaseTable}.{$PassoAtendimentoSm->tableSchema}.{$PassoAtendimentoSm->useTable} PassoAtendimentoSm on PassoAtendimentoSm.codigo = HistoricoSm.codigo_passo_atendimento_sm
                    left join {$AtendimentoSm->databaseTable}.{$AtendimentoSm->tableSchema}.{$AtendimentoSm->useTable} AtendimentoSm on AtendimentoSm.codigo = PassoAtendimentoSm.codigo_atendimento_sm
                    where HistoricoSm.codigo_sm = sm.sm and HistoricoSm.codigo_usuario_monitora = cast(sm.operador as integer) and AtendimentoSm.data_fim is null";
        }
        $query .= ") ocorrencia,
                    cast(convert(varchar, GETDATE(), 120) as datetime),
                    1
                from
                    {$Recebsm->databaseTable}.{$Recebsm->tableSchema}.{$Recebsm->useTable} sm WITH(NOLOCK)
                left outer join {$Funcionario->databaseTable}.{$Funcionario->tableSchema}.{$Funcionario->useTable} operador WITH(NOLOCK) on(operador.codigo = sm.operador)
                left outer join {$ClientEmpresa->databaseTable}.{$ClientEmpresa->tableSchema}.{$ClientEmpresa->useTable} cliente WITH(NOLOCK) on(cliente.codigo = sm.cliente)
                where
                    sm.encerrada = 'n'
                    and sm.operador is not null";
        return ($this->query($query) !== false);
    }
    
}

?>