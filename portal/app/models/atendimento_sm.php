<?php
class AtendimentoSm extends AppModel {
    var $name = 'AtendimentoSm';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'atendimentos_sms';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $validate = array(
        'data_inicio' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'message' => 'Informe a data'
            )
         ),
    );
    
    const TIPO_FILTRO_CONDICAO_E = 0;
    const TIPO_FILTRO_CONDICAO_OU = 1;
    
    function incluirEvento($dados, $in_another_transaction = false, $gravar_historico = true) {
        $PassoAtendimentoSm = ClassRegistry::init('PassoAtendimentoSm');
        $HistoricoSm = ClassRegistry::init('HistoricoSm');
        try {
            $this->create();
            if (!$in_another_transaction) $this->query('begin transaction');
            $codigo_atendimento_sm = $this->atendimentoEmAberto($dados['AtendimentoSm']['codigo_sm']);
            
            if(!$codigo_atendimento_sm){
                $codigo_atendimento_sm = $this->gerarAtendimento($dados);
            }
            
            if(!$codigo_atendimento_sm) { throw new Exception(); }
            
            $passo_atendimento_sm = $PassoAtendimentoSm->emAberto($codigo_atendimento_sm, $dados['AtendimentoSm']['codigo_passo_atendimento']);

            if (!$passo_atendimento_sm) 
                $passo_atendimento_sm  = $PassoAtendimentoSm->incluir($codigo_atendimento_sm, $dados);
                
            if(!$passo_atendimento_sm) { throw new Exception(); }
            if($gravar_historico) {
                $dados['AtendimentoSm']['codigo_atendimento_sm'] = $codigo_atendimento_sm;
                if(!$HistoricoSm->incluir($passo_atendimento_sm, $dados)) { 
                    $this->validationErrors = array_merge($this->validationErrors, $HistoricoSm->validationErrors);
                    throw new Exception(); 
                }
            }
            if (!$in_another_transaction) $this->commit();
            return true;
        } catch (Exception $ex) {
            if (!$in_another_transaction) $this->rollback();
            return false;
        }
    }

    function atendimentoEmAberto($codigo_sm) {
        $atendimento_sm = $this->find('first', array('fields' => array('codigo'), 'conditions' => array('codigo_sm' => $codigo_sm, 'data_fim' => null)));
        return isset($atendimento_sm['AtendimentoSm']['codigo']) ? $atendimento_sm['AtendimentoSm']['codigo'] : false;
    }

    function gerarAtendimento($dados) {
        if(empty($dados['AtendimentoSm']['data_inicio'])) {
            $dados['AtendimentoSm']['data_inicio'] = date('Y-m-d H:i:s');
        }
        $atendimento = array(
            'AtendimentoSm'  => array(
                'codigo_sm' => $dados['AtendimentoSm']['codigo_sm'],
                'codigo_prioridade' => $dados['AtendimentoSm']['codigo_prioridade'],
                'data_inicio' => $dados['AtendimentoSm']['data_inicio']
            )
        );
        if (isset($dados['AtendimentoSm']['codigo_usuario_inclusao_guardian']) && !empty($dados['AtendimentoSm']['codigo_usuario_inclusao_guardian'])) {
            $atendimento['AtendimentoSm']['codigo_usuario_inclusao_guardian'] = $dados['AtendimentoSm']['codigo_usuario_inclusao_guardian'];
        } elseif (isset($dados['AtendimentoSm']['codigo_usuario_inclusao']) && !empty($dados['AtendimentoSm']['codigo_usuario_inclusao'])) {
            $atendimento['AtendimentoSm']['codigo_usuario_inclusao'] = $dados['AtendimentoSm']['codigo_usuario_inclusao'];
        }
        if(isset($dados['AtendimentoSm']['codigo_ocorrencia'])) { 
          $atendimento['AtendimentoSm']['codigo_ocorrencia'] = $dados['AtendimentoSm']['codigo_ocorrencia'];
        }
        parent::incluir($atendimento);
        return $this->id;
    }
    
    function converteFiltroEmCondition($data, $com_sla = false) {
        $conditions = array();
        $condition_tecnologia = array();

        if (!empty($data['data_inicial'])) {
			$data['data_inicial'] = AppModel::dateToDbDate2($data['data_inicial'].' 00:00:00');
			$data['data_final'] = !empty($data['data_final']) ? AppModel::dateToDbDate2($data['data_final'].' 23:59:59'): date('Y-m-d H:i:s');
            $conditions['AtendimentoSm.data_inicio BETWEEN ? AND ?'] = array($data['data_inicial'], $data['data_final']);
		}
        if (!empty($data['codigo']))
            $conditions['AtendimentoSm.codigo'] = $data['codigo'];
        if (!empty($data['codigo_sm']))
            $conditions['AtendimentoSm.codigo_sm'] = $data['codigo_sm'];
        if (!empty($data['codigo_prioridade']))
            $conditions['AtendimentoSm.codigo_prioridade'] = $data['codigo_prioridade'];
        if (!empty($data['placa']))
            $conditions['Recebsm.placa'] = $data['placa'];
        if (!empty($data['codigo_tecnologia']))
            $condition_tecnologia['Recebsm.CodEquipamento'] = $data['codigo_tecnologia'];
        if (!empty($data['codigo_passo_atendimento']))
            $conditions['PassoAtendimentoSm.codigo_passo_atendimento'] = $data['codigo_passo_atendimento'];
        if (!empty($data['codigo_operacao'])) {
            $sem_vinculo = array_search(14, $data['codigo_operacao']);
            if ($sem_vinculo !== false) {
                unset($data['codigo_operacao'][$sem_vinculo]);
                $data['codigo_operacao'] = array_merge($data['codigo_operacao'], array(0, 14));
                $condition_tipo_operacao = array('OR' => array(array('ClientEmpresa.tipo_operacao' => null), array('ClientEmpresa.tipo_operacao' => $data['codigo_operacao'])));
            } else {
                $condition_tipo_operacao = array('ClientEmpresa.tipo_operacao' => $data['codigo_operacao']);
            }
            if (isset($data['codigo_tecnologia']) && count($data['codigo_tecnologia']) > 0 && isset($data['tipo_filtro_operacoes']) && $data['tipo_filtro_operacoes'] == AtendimentoSm::TIPO_FILTRO_CONDICAO_OU) {
                $conditions[]['OR'] = array($condition_tecnologia, $condition_tipo_operacao);
            } else {
                $conditions = array_merge($conditions, $condition_tecnologia, $condition_tipo_operacao);
            }
        } elseif (!empty($data['codigo_tecnologia'])) {
            $conditions['Recebsm.CodEquipamento'] = $data['codigo_tecnologia'];
        }
        
        if ($com_sla && !empty($data['tipo_sla'])) {
            if(isset($data['sla'])) {
                if ($data['sla'] == 1) {
                    $conditions['DATEDIFF(n, PassoAtendimentoSm.data_inicio, getdate()) <='] = 30;
                    $conditions['PassoAtendimentoSm.data_analise'] = null;
                    unset($data['status_atendimento']);
                } elseif ($data['sla'] == 2) {
                    $conditions['DATEDIFF(n, PassoAtendimentoSm.data_inicio, getdate()) >'] = 30;
                    $conditions['PassoAtendimentoSm.data_analise'] = null;
                    unset($data['status_atendimento']);
                } elseif ($data['sla'] == 3) {
                    $conditions['DATEDIFF(n, PassoAtendimentoSm.data_inicio, PassoAtendimentoSm.data_analise) <='] = 30;
                } elseif ($data['sla'] == 4) {
                    $conditions['DATEDIFF(n, PassoAtendimentoSm.data_inicio, PassoAtendimentoSm.data_analise) >'] = 30;
                }
            } else {
                if ($data['tipo_sla'] == 1) {
                    $conditions['DATEDIFF(n, PassoAtendimentoSm.data_inicio, isnull(PassoAtendimentoSm.data_analise, getdate())) >'] = 30;
                } elseif ($data['tipo_sla'] == 2) {
                    $conditions['DATEDIFF(n, PassoAtendimentoSm.data_inicio, isnull(PassoAtendimentoSm.data_analise, getdate())) <='] = 30;
                }
            }
            $conditions['PassoAtendimentoSm.data_fim'] = null;
        }
        
        if (!empty($data['status_atendimento'])) {
            if(!is_array($data['status_atendimento'])) {
                $valor_status_atendimento = $data['status_atendimento'];
                $data['status_atendimento'] = array(
                    0 => $valor_status_atendimento
                );
            }
            
            if(!(in_array( 1, $data['status_atendimento']) && in_array( 2, $data['status_atendimento']) && in_array( 3, $data['status_atendimento'])) ){
                $index  = sizeof($conditions);
                $conditions[$index]['OR'] = array();
                if (in_array(3, $data['status_atendimento'])) {
                    array_push($conditions[$index]['OR'], array('not' => array('PassoAtendimentoSm.data_fim' => null)));
                }
                if (in_array(2, $data['status_atendimento'])) {
                    array_push($conditions[$index]['OR'], array('not' => array('PassoAtendimentoSm.data_encaminhado' => null),'PassoAtendimentoSm.data_fim' => null));
                }
                if (in_array(1, $data['status_atendimento'])) {
                    array_push($conditions[$index]['OR'], array(
                        'PassoAtendimentoSm.data_encaminhado' => null,
                        'PassoAtendimentoSm.data_fim' => null,
                        'or' => array(
                                    array('not' => array('PassoAtendimentoSm.data_analise' => null)),
                                    array('not' => array('PassoAtendimentoSm.data_inicio' => null))
                                )
                        )
                    );
                }
            }
        }
      
        if (!empty($data['codigo_cliente'])){           
            $conditions['OR'] = array(
               array('Recebsm.codigo_cliente_embarcador' => $data['codigo_cliente']),
               array('Recebsm.codigo_cliente_transportador' => $data['codigo_cliente'])
            );
        }
        return $conditions;
    }
    
    function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = 1, $extra = array()) {
        $PassoAtendimentoSm =& ClassRegistry::init('PassoAtendimentoSm');
        $HistoricoSm =& ClassRegistry::init('HistoricoSm');
        $this->paginateBind();		        
        $group = array(
                'AtendimentoSm.codigo_sm',
                'Recebsm.sm',
                'Recebsm.placa',
                'Recebsm.CodEquipamento',
                'Recebsm.codigo_cliente_embarcador',
                'Recebsm.codigo_cliente_transportador',
                'ClientEmpresa.tipo_operacao',
                'ClientEmpresa.codigo',
                'ClientEmpresa.Raz_social',
                'AtendimentoSm.codigo',
                'AtendimentoSm.codigo_prioridade',
                'AtendimentoSm.data_inicio',
                'AtendimentoSm.data_analise',
                'AtendimentoSm.data_fim',
                'OperacaoMonitora.descricao',
                'PassoAtendimentoSm.codigo_passo_atendimento',
                'PassoAtendimentoSm.data_inicio',
                'PassoAtendimentoSm.data_analise',
                'PassoAtendimentoSm.data_encaminhado',
                'PassoAtendimentoSm.data_fim',                
        );
        $fields = array(
                'AtendimentoSm.codigo_sm AS codigo_sm',
                'AtendimentoSm.codigo AS codigo',
                'Recebsm.sm AS sm',
                'Recebsm.placa AS placa',
                'Recebsm.CodEquipamento AS CodEquipamento',
                'Recebsm.codigo_cliente_embarcador AS codigo_cliente_embarcador',
                'Recebsm.codigo_cliente_transportador AS codigo_cliente_transportador',
                'ClientEmpresa.tipo_operacao AS tipo_operacao',
                'ClientEmpresa.codigo AS codigo_client_empresa',
                'ClientEmpresa.Raz_social AS Raz_social',
                'AtendimentoSm.codigo AS codigo_atendimento_sm',
                'AtendimentoSm.codigo_prioridade AS codigo_prioridade',
                'CONVERT(VARCHAR,AtendimentoSm.data_inicio,20) AS data_inicio_atendimento_sm',
                'CONVERT(VARCHAR,AtendimentoSm.data_analise,20) AS data_analise_atendimento_sm',
                'CONVERT(VARCHAR,AtendimentoSm.data_fim,20) AS data_fim_atendimento_sm',
                'OperacaoMonitora.descricao AS descricao',
                'PassoAtendimentoSm.codigo_passo_atendimento AS codigo_passo_atendimento',
                'CONVERT(VARCHAR,PassoAtendimentoSm.data_inicio,20) AS data_inicio_passo_atendimento',
                'CONVERT(VARCHAR,PassoAtendimentoSm.data_analise,20) AS data_analise_passo_atendimento',
                'CONVERT(VARCHAR,PassoAtendimentoSm.data_encaminhado,20) AS data_encaminhado',
                'CONVERT(VARCHAR,PassoAtendimentoSm.data_fim,20) AS data_fim_passo_atendimento',                
                'MAX(PassoAtendimentoSm.codigo) as codigo_passo_atendimento_sm',
        );
        $query_base = $this->find('sql', compact('conditions', 'fields', 'group'));

        $offset = ( $page <= 1 ? null : ($page - 1) * $limit );
        $dbo = $this->getDataSource();
        $codigo_passo_atendimento_pronta_resposta = 2;
        $order = array('data_inicio_atendimento_sm DESC');
        $query = $dbo->buildStatement(array(
            'fields' => array(
                'codigo',
                'codigo_sm',
                'sm',
                'placa',
                'codigo_cliente_embarcador',
                'codigo_cliente_transportador',
                'CodEquipamento',
                'tipo_operacao',
                'codigo_client_empresa',
                'Raz_social',
                'codigo_atendimento_sm',
                'codigo_prioridade',
                'data_inicio_atendimento_sm',
                'data_analise_atendimento_sm',
                'data_fim_atendimento_sm',
                'descricao',
                'codigo_passo_atendimento',
                'data_inicio_passo_atendimento',
                'data_analise_passo_atendimento',
                'data_encaminhado',
                'data_fim_passo_atendimento',                
                'codigo_passo_atendimento_sm',
                "CASE WHEN EXISTS(SELECT TOP 1 1 FROM {$PassoAtendimentoSm->databaseTable}.{$PassoAtendimentoSm->tableSchema}.{$PassoAtendimentoSm->useTable} AS PassoAtendimentoSm WHERE PassoAtendimentoSm.codigo_atendimento_sm = Base.codigo_atendimento_sm AND PassoAtendimentoSm.codigo_passo_atendimento = {$codigo_passo_atendimento_pronta_resposta}) THEN 1 ELSE 0 END AS pronta_resposta",
                "(SELECT top 1 espa_descricao FROM {$HistoricoSm->databaseTable}.{$HistoricoSm->tableSchema}.{$HistoricoSm->useTable} AS HistoricoSm INNER JOIN openquery(lk_guardian, 'select espa_codigo, espa_descricao from espa_evento_sistema_padrao') ON espa_codigo = codigo_tipo_evento WHERE HistoricoSm.codigo_atendimento_sm = Base.codigo_atendimento_sm ORDER BY HistoricoSm.codigo DESC) AS espa_descricao"
            ),
            'table' => "({$query_base})",
            'alias' => 'Base',
            'limit' => $limit,
            'offset' => $offset,
            'joins' => array(),
            'conditions' => null,
            'order' => $order,
            'group' => null
        ), $this);

        $this->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
        if(isset($extra['export']) && $extra['export'] == true){
            return $query;
        }
        return $this->query($query);
    }

    function paginateCount($conditions = null,$recursive = 0, $extra = array()) {
        $this->paginateBind();
        return $this->find('count', compact('conditions', 'recursive'));
    }

    private function paginateBind() {
        $this->bindModel(array('belongsTo' => array(
            'Recebsm' => array('foreignKey' => false, 'conditions' => "RIGHT('00000000'+CONVERT(VARCHAR, AtendimentoSm.codigo_sm),8) = Recebsm.SM"),
            'ClientEmpresa' => array('foreignKey' => false, 'conditions' => array('Recebsm.cliente = ClientEmpresa.codigo')),
            'OperacaoMonitora' => array('foreignKey' => false, 'conditions' => array('ClientEmpresa.tipo_operacao = OperacaoMonitora.cod_operacao')),
            'PassoAtendimentoSm' => array('foreignKey' => false, 'conditions' => array('AtendimentoSm.codigo = PassoAtendimentoSm.codigo_atendimento_sm')),
            'PassoAtendimento' => array('foreignKey' => false, 'conditions' => array('PassoAtendimento.codigo = PassoAtendimentoSm.codigo_atendimento_sm')),
        )));        
    }
    
    const SETOR_GERAL = 1;
    const SETOR_BSAT = 2;
    const SETOR_PRONTA_RESPOSTA = 3;
	
    function statusSLAPorSetor($tempo_sla, $setor = 1) {
        $lista = array();
        if ($setor == AtendimentoSm::SETOR_GERAL) {
            $lista['sem_analise'] = $this->statusSLA($tempo_sla, AtendimentoSm::SLA_SEM_ANALISE_GERAL);
            $lista['em_analise'] = $this->statusSLA($tempo_sla, AtendimentoSm::SLA_EM_ANALISE_GERAL);
        } elseif ($setor == AtendimentoSm::SETOR_BSAT) {
            $lista['sem_analise'] = $this->statusSLA($tempo_sla, AtendimentoSm::SLA_SEM_ANALISE_BSAT);
            $lista['em_analise'] = $this->statusSLA($tempo_sla, AtendimentoSm::SLA_EM_ANALISE_BSAT);
        } elseif ($setor == AtendimentoSm::SETOR_PRONTA_RESPOSTA) {
            $lista['sem_analise'] = $this->statusSLA($tempo_sla, AtendimentoSm::SLA_SEM_ANALISE_PRONTA_RESPOSTA);
            $lista['em_analise'] = $this->statusSLA($tempo_sla, AtendimentoSm::SLA_EM_ANALISE_PRONTA_RESPOSTA);
        }
        return $lista;
    }
    
    const SLA_SEM_ANALISE_GERAL = 1;
    const SLA_EM_ANALISE_GERAL = 2;
    const SLA_SEM_ANALISE_BSAT = 3;
    const SLA_EM_ANALISE_BSAT = 4;
    const SLA_SEM_ANALISE_PRONTA_RESPOSTA = 5;
    const SLA_EM_ANALISE_PRONTA_RESPOSTA = 6;
    function statusSLA($tempo_sla, $tipo) {
        $PassoAtendimentoSm = classRegistry::init('PassoAtendimentoSm');
        $sem_analise = array(1,3,5);
        $em_analise = array(2,4,6);
        
        $conditions = array('PassoAtendimentoSm.data_fim' => null);
        if(in_array($tipo, $sem_analise)) {
            $data_analise = 'getdate()';
            array_push($conditions, array('PassoAtendimentoSm.data_analise' => null));
        } elseif(in_array($tipo, $em_analise)) {
            $data_analise = "PassoAtendimentoSm.data_analise";
            array_push($conditions, array('not' => array('PassoAtendimentoSm.data_analise' => null)));
        }
        
        if($tipo == AtendimentoSm::SLA_SEM_ANALISE_BSAT || $tipo == AtendimentoSm::SLA_EM_ANALISE_BSAT) {
            array_push($conditions, array('PassoAtendimentoSm.codigo_passo_atendimento' => 1));
        } elseif($tipo == AtendimentoSm::SLA_SEM_ANALISE_PRONTA_RESPOSTA || $tipo == AtendimentoSm::SLA_EM_ANALISE_PRONTA_RESPOSTA) {
            array_push($conditions, array('PassoAtendimentoSm.codigo_passo_atendimento' => 2));
        }
    	
        $fields = array(
            "SUM(
                CASE
                        WHEN DATEDIFF(n, PassoAtendimentoSm.data_inicio, {$data_analise}) <= $tempo_sla
                        THEN 1
                        ELSE 0
                END) AS dentro",
            "SUM(
                CASE
                        WHEN DATEDIFF(n, PassoAtendimentoSm.data_inicio, {$data_analise}) > $tempo_sla
                        THEN 1
                        ELSE 0
                END) AS fora",
    	);
                        
        $joins = array(
            array(
                'table' => "{$PassoAtendimentoSm->databaseTable}.{$PassoAtendimentoSm->tableSchema}.{$PassoAtendimentoSm->useTable}",
                'alias' => 'PassoAtendimentoSm',
                'type' => 'LEFT',
                'conditions' => array("PassoAtendimentoSm.codigo_atendimento_sm = {$this->name}.codigo")
            )
        );
            
        $dados = $this->find('first', array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins));
        if(!$dados[0]['dentro'])
            $dados[0]['dentro'] = 0;
        if(!$dados[0]['fora'])
            $dados[0]['fora'] = 0;
        return $dados;
    }

    function salvar_alerta($data,$id){
        App::import('Component', array('StringView', 'Mailer.Scheduler'));
        App::import('Component','DbbuonnyGuardian');
        $this->StringView       = new StringViewComponent();
        $this->Scheduler        = new SchedulerComponent();
        $this->DbbuonnyGuardian = new DbbuonnyGuardianComponent();
        
        $this->TViagViagem = ClassRegistry::init('TViagViagem');
        $this->Alerta = ClassRegistry::init('Alerta');
        $this->Usuario = ClassRegistry::init('Usuario');

        $viagem = $this->TViagViagem->find('first',array(
            'fields' => array(
                'viag_emba_pjur_pess_oras_codigo',
                'viag_tran_pess_oras_codigo'
            ),
            'conditions' => array('viag_codigo_sm' => $data['AtendimentoSm']['codigo_sm'])
            )
        );
        
        if(!empty($viagem['TViagViagem']['viag_emba_pjur_pess_oras_codigo'])){
            $embarcador = $this->DbbuonnyGuardian->converteClienteGuardianEmBuonny($viagem['TViagViagem']['viag_emba_pjur_pess_oras_codigo']);
        }
        $transportador = $this->DbbuonnyGuardian->converteClienteGuardianEmBuonny($viagem['TViagViagem']['viag_tran_pess_oras_codigo']);
            
        if(isset($embarcador) && ($embarcador != $transportador)){
            $clientes = array($embarcador,$transportador);
        }else{
            $clientes = array($transportador);
        }

        $usuario = $this->Usuario->carregar($data['AtendimentoSm']['codigo_usuario']);
        $data['AtendimentoSm']['usuario'] = $usuario['Usuario']['nome'];

        $this->StringView->set(compact('data'));
        $content = $this->StringView->renderMail('email_alerta_ocorrencias','default_novo');
       
        foreach ($clientes as $key => $codigo) {         
            $alerta = array(
                'Alerta' => array(
                    'codigo_cliente' => $codigo,
                    'descricao' => "Ocorrencia lancada para SM : {$data['AtendimentoSm']['codigo_sm']} na data : {$data['AtendimentoSm']['data_inicio']}",
                    'descricao_email' => $content,
                    'codigo_alerta_tipo' => 50,
                    'model' => 'AtendimentoSm',
                    'foreign_key' => $id,
                ),
            );
           $this->Alerta->incluir($alerta);
        }
        return true;
    }

}