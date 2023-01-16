<?php
class HistoricoSM extends AppModel {
    var $name = 'HistoricoSM';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'historicos_sms';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');
    var $validate = array(
        'texto' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo observação deve ser preenchido',
        ),
        'local' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o local',
        ),
        'longitude' => array(
            'rule' => array('decimal'),
            'allowEmpty' => true,
            'message' => 'Informe a longitude no formato 99.9999999'
        ),
        'latitude' => array(
            'rule' => array('decimal'),
            'allowEmpty' => true,
            'message' => 'Informe a latitude no formato 99.999999'
        ),
        'codigo_tipo_evento' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe um evento'
        )
    );
    
    const TIPO_ACAO_EM_ANALISE = 1;
    const TIPO_ACAO_ENCAMINHADO = 2;
    const TIPO_ACAO_FINALIZADO = 3;
    
    function bindUsuario() {
        $this->bindModel(array(
            'belongsTo' => array(
                'Usuario' => array(
                    'class' => 'Usuario',
                    'foreignKey' => 'codigo_usuario'
                )
            )
        ));
    }
    
    function unbindUsuario() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'Usuario'
            )
        ));
    }
  
    function bindPassoAtendimento() {
        $this->bindModel(array(
            'belongsTo' => array(
                'PassoAtendimento' => array(
                    'class' => 'PassoAtendimento',
                    'foreignKey' => 'codigo_passo_atendimento'
                )
            )
        ));
    }
    
    function unbindPassoAtendimento() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'PassoAtendimento'
            )
        ));
    }
    
    function listarPorSm($codigo_sm) {
        $this->bindUsuario();
        $this->bindPassoAtendimento();
        $historicos = $this->find('all', array('conditions' => array('codigo_sm' => $codigo_sm), 'order' => array('HistoricoSm.data_inclusao' => 'DESC')));
        $this->unbindUsuario();
        $this->unbindPassoAtendimento();
        return $historicos;
    }
    
    function primeiroHistoricoAtendimento($codigo_passo_atendimento_sm) {
        $Funcionario = ClassRegistry::init('Funcionario');
        return $this->find('first', array(
            'conditions' => array('codigo_passo_atendimento_sm' => $codigo_passo_atendimento_sm, 'NOT' => array('local' => null)),
            'order' => array('data_inclusao ASC'),
            'fields' => array('codigo_usuario_autorizacao', 'codigo_tipo_evento', 'local', 'codigo_usuario_monitora', 'Funcionario.Apelido'),
            'joins' => array(array(
                'table' => "{$Funcionario->databaseTable}.{$Funcionario->tableSchema}.{$Funcionario->useTable}",
                'conditions' => "HistoricoSm.codigo_usuario_monitora = Funcionario.{$Funcionario->primaryKey}",
                'alias' => 'Funcionario',
                'type' => 'left'
            ))
        ));
    }
    
    function listarHistoricoAtendimento( $codigo_sm ) {
        $Usuario                  = ClassRegistry::init('Usuario');
        $Funcionario              = ClassRegistry::init('Funcionario');
        $TUsuaUsuario             = ClassRegistry::init('TUsuaUsuario');
        $AtendimentoSm            = classRegistry::init('AtendimentoSm');
        $PassoAtendimento         = classRegistry::init('PassoAtendimento');
        $PassoAtendimentoSm       = classRegistry::init('PassoAtendimentoSm');
        $TEspaEventoSistemaPadrao = classRegistry::init('TEspaEventoSistemaPadrao');        
        $Prestador                = classRegistry::init('Prestador');
        $HistoricoSmPrestador     = classRegistry::init('HistoricoSmPrestador');
        
        $joins = array (
            array(
               'table' => "{$PassoAtendimentoSm->databaseTable}.{$PassoAtendimentoSm->tableSchema}.{$PassoAtendimentoSm->useTable}",
                'alias' => 'PassoAtendimentoSm',
                'conditions' => 'HistoricoSm.codigo_passo_atendimento_sm = PassoAtendimentoSm.codigo',
                'type' => 'left',
            ),
            array(
                'table' => "{$AtendimentoSm->databaseTable}.{$AtendimentoSm->tableSchema}.{$AtendimentoSm->useTable}",
                'alias' => 'AtendimentoSm',
                'conditions' => 'PassoAtendimentoSm.codigo_atendimento_sm = AtendimentoSm.codigo',
                'type' => 'left',
            ),
            array(
                'table' => "{$PassoAtendimento->databaseTable}.{$PassoAtendimento->tableSchema}.{$PassoAtendimento->useTable}",
                'alias' => 'PassoAtendimento',
                'conditions' => 'PassoAtendimentoSm.codigo_passo_atendimento = PassoAtendimento.codigo',
                'type' => 'left',
            ),
            array(
                'table' => "{$Usuario->databaseTable}.{$Usuario->tableSchema}.{$Usuario->useTable}",
                'alias' => 'Usuario',
                'conditions' => 'HistoricoSm.codigo_usuario = Usuario.codigo',
                'type' => 'left',
            ),
            array(
                'table' => "{$Funcionario->databaseTable}.{$Funcionario->tableSchema}.{$Funcionario->useTable}",
                'alias' => 'Funcionario',
                'conditions' => 'HistoricoSm.codigo_usuario_monitora = Funcionario.codigo',
                'type' => 'left',
            ),
            array(
                'table' => "{$HistoricoSmPrestador->databaseTable}.{$HistoricoSmPrestador->tableSchema}.{$HistoricoSmPrestador->useTable}",
                'alias' => 'HistoricoSmPrestador',
                'conditions' => 'HistoricoSmPrestador.codigo_historico_sm = HistoricoSm.codigo',
                'type' => 'left',
            ),
            array(
                'table' => "{$Prestador->databaseTable}.{$Prestador->tableSchema}.{$Prestador->useTable}",
                'alias' => 'Prestador',
                'conditions' => 'HistoricoSmPrestador.codigo_prestador = Prestador.codigo',
                'type' => 'left',
            ),
        );

		$data_inclusao = $this->useDbConfig == 'test_suite' ? 'convert(varchar, HistoricoSm.data_inclusao, 20) AS data_inclusao' : 'HistoricoSm.data_inclusao';

		$fields = array(
				$data_inclusao,
                'PassoAtendimento.descricao AS descricao',
                'HistoricoSm.codigo',
                'HistoricoSm.texto AS texto',
                'HistoricoSm.latitude AS latitude',
                'HistoricoSm.longitude AS longitude',
                'HistoricoSm.codigo_tipo_evento',
                'Usuario.apelido',
                'Funcionario.Apelido',
                'AtendimentoSM.codigo',
                'AtendimentoSM.codigo_usuario_inclusao_guardian',
                'Prestador.nome',
                'HistoricoSmPrestador.status'
		);
		
		$order = $this->useDbConfig == 'test_suite' ? 'data_inclusao DESC': 'HistoricoSm.data_inclusao DESC';

        $resultado = $this->find( 'all', array(
            'fields' => $fields,
            'order' => $order,
            'conditions' => array('AtendimentoSm.codigo_sm' => $codigo_sm),
            'joins' => $joins
            )
        );

        $resultado = $TEspaEventoSistemaPadrao->trataArrayHistoricoSm($resultado);
        foreach($resultado as &$result){
            $usuario_guardian = $TUsuaUsuario->find('first',array(
                'fields' => array(
                    'usua_pfis_pess_oras_codigo',
                    'usua_login'
                ),
                'conditions' => array(
                    'usua_pfis_pess_oras_codigo' => $result['AtendimentoSM']['codigo_usuario_inclusao_guardian'],
                ),
            ));
            $result['TUsuaUsuario'] = $usuario_guardian['TUsuaUsuario'];
           
        }        
        return $resultado;
    }
    
    function incluir($passo_atendimento_sm, $dados) {
        $dados_final = array(
            'HistoricoSm' => array(
                'codigo_sm' => $dados['AtendimentoSm']['codigo_sm'],
                'codigo_tipo_evento' => $dados['AtendimentoSm']['codigo_tipo_evento'],
                'texto' => $dados['AtendimentoSm']['texto'],
                'codigo_passo_atendimento' => $passo_atendimento_sm['PassoAtendimentoSm']['codigo_passo_atendimento'],
                'local' => $dados['AtendimentoSm']['local'],
                'latitude' => $dados['AtendimentoSm']['latitude'],
                'longitude' => $dados['AtendimentoSm']['longitude'],
                'codigo_passo_atendimento_sm' => $passo_atendimento_sm['PassoAtendimentoSm']['codigo'],
                'codigo_atendimento_sm' => $dados['AtendimentoSm']['codigo_atendimento_sm']
            )
        );
        if(isset($dados['AtendimentoSm']['codigo_usuario_inclusao']) && !empty($dados['AtendimentoSm']['codigo_usuario_inclusao'])) {
            $dados_final['HistoricoSm']['codigo_usuario_monitora'] = $dados['AtendimentoSm']['codigo_usuario_inclusao'];
        } elseif(isset($dados['AtendimentoSm']['codigo_usuario_inclusao_guardian']) && !empty($dados['AtendimentoSm']['codigo_usuario_inclusao_guardian'])) {
            $dados_final['HistoricoSm']['codigo_usuario_inclusao_guardian'] = $dados['AtendimentoSm']['codigo_usuario_inclusao_guardian'];
        }elseif(isset($dados['AtendimentoSm']['codigo_usuario']) && !empty($dados['AtendimentoSm']['codigo_usuario'])) {
            $dados_final['HistoricoSm']['codigo_usuario'] = $dados['AtendimentoSm']['codigo_usuario'];
        }

        return (parent::incluir($dados_final)) ? true: false;
    }
    
    function registrarAtendimento($data = null, $gravar_aviso_encaminhamento = false) {
        $PassoAtendimentoSm   = classRegistry::init('PassoAtendimentoSm');
        $AtendimentoSm        = classRegistry::init('AtendimentoSm');
        $PassoAtendimento     = classRegistry::init('PassoAtendimento');
        $Prestador            = classRegistry::init('Prestador');
        
        $data_hora_atual = date('Y-m-d H:i:s');
        
        $passo_atendimento = '';
        if(!isset($data['HistoricoSm']['codigo_passo_atendimento'])) {
            $codigo_passo_atendimento = $PassoAtendimento->find('first', array('conditions' => array('ordem' => 1), 'fields' => 'codigo'));
            $data['HistoricoSm']['codigo_passo_atendimento'] = $codigo_passo_atendimento['PassoAtendimento']['codigo'];
        } else {
            $passo_atendimento = $PassoAtendimento->find('first', array('conditions' => array('codigo' => $data['HistoricoSm']['codigo_passo_atendimento']), 'fields' => 'descricao'));
        }
        $finalizar = false;
        $atendimento_sm = '';
        $historico_sm = '';
        $passo_atendimento_sm = $PassoAtendimentoSm->find('first', array('conditions' => array('codigo' => $data['HistoricoSm']['codigo_passo_atendimento_sm'])));
        $mais_passo_atendimento_sm = '';
        
        if(!empty($passo_atendimento_sm)) {
            $atendimento_sm = $AtendimentoSm->find('first', array('conditions' => array('codigo' => $passo_atendimento_sm['PassoAtendimentoSm']['codigo_atendimento_sm'])));
        }


        $atendimento_sm['AtendimentoSm']['data_analise'] = $data_hora_atual;
        
        $cod_passo_atendimento_sm = $passo_atendimento_sm['PassoAtendimentoSm']['codigo'];
        $cod_atendimento_sm = $passo_atendimento_sm['PassoAtendimentoSm']['codigo_atendimento_sm'];
        
        switch ($data['HistoricoSm']['tipo_acao']) {
            case 1:
                $passo_atendimento_sm['PassoAtendimentoSm']['data_analise'] = $data_hora_atual;
                break;
            case 2:
                if(empty($passo_atendimento_sm['PassoAtendimentoSm']['data_analise'])) {
                    $passo_atendimento_sm['PassoAtendimentoSm']['data_analise'] = $data_hora_atual;
                }
                $passo_atendimento_sm['PassoAtendimentoSm']['data_encaminhado'] = $data_hora_atual;
                break;
            case 3:
                $finalizar = true;
                break;
        }
        
        $latitude = isset($data['HistoricoSm']['latitude']) ? $data['HistoricoSm']['latitude']: null;
        $longitude = isset($data['HistoricoSm']['longitude']) ? $data['HistoricoSm']['longitude']: null;

        if ($gravar_aviso_encaminhamento) {
            $historico_sm = array(
                'HistoricoSm' => array(
                    'codigo_usuario' => $data['HistoricoSm']['codigo_usuario'],
                    'codigo_sm' => $atendimento_sm['AtendimentoSm']['codigo_sm'],
                    'codigo_passo_atendimento' => $data['HistoricoSm']['codigo_passo_atendimento'],
                    'codigo_passo_atendimento_sm' => $data['HistoricoSm']['codigo_passo_atendimento_sm'],
                    'data_inclusao' => date('Y-m-d H:i:s', strtotime('+1 second', strtotime($data_hora_atual))),
                    'texto' => 'ENCAMINHADO PARA O PRONTA RESPOSTA',
                    'codigo_atendimento_sm' => $data['HistoricoSm']['codigo_atendimento_sm'],
                )
            );

            parent::incluir($historico_sm);            
		}
		
        $historico_sm = array(
            'HistoricoSm' => array(
                'codigo_sm' => $atendimento_sm['AtendimentoSm']['codigo_sm'],
                'texto' => $data['HistoricoSm']['texto'],
                'codigo_usuario' => $data['HistoricoSm']['codigo_usuario'],
                'codigo_passo_atendimento' => $data['HistoricoSm']['codigo_passo_atendimento'],
                'codigo_usuario_monitora' => null,
                'codigo_usuario_autorizacao' => null,
                'data_inclusao' => $data_hora_atual,
                'codigo_passo_atendimento_sm' => $data['HistoricoSm']['codigo_passo_atendimento_sm'],
                'latitude' => $latitude,
                'longitude' => $longitude,
                'codigo_atendimento_sm' => $data['HistoricoSm']['codigo_atendimento_sm'],
            )
        );
		
        if($data['HistoricoSm']['tipo_acao'] == 2 && $passo_atendimento['PassoAtendimento']['descricao'] === 'Pronta Resposta') {
            $passo_atendimento_sm['PassoAtendimentoSm']['codigo_passo_atendimento_encaminhado'] = $data['HistoricoSm']['codigo_passo_atendimento'];
            $passo_atendimento_sm['PassoAtendimentoSm']['data_encaminhado'] = $data_hora_atual;
            $mais_passo_atendimento_sm = $passo_atendimento_sm;
            $mais_passo_atendimento_sm['PassoAtendimentoSm']['data_inicio'] = $data_hora_atual;
            $mais_passo_atendimento_sm['PassoAtendimentoSm']['codigo_passo_atendimento'] = $data['HistoricoSm']['codigo_passo_atendimento'];
            
            unset($mais_passo_atendimento_sm['PassoAtendimentoSm']['codigo']);
            unset($mais_passo_atendimento_sm['PassoAtendimentoSm']['data_analise']);
            unset($mais_passo_atendimento_sm['PassoAtendimentoSm']['codigo_passo_atendimento_encaminhado']);
            unset($mais_passo_atendimento_sm['PassoAtendimentoSm']['data_encaminhado']);
            unset($mais_passo_atendimento_sm['PassoAtendimentoSm']['data_fim']);
            
            $PassoAtendimentoSm->create();
            $PassoAtendimentoSm->set($mais_passo_atendimento_sm);
            $PassoAtendimentoSm->save();
        }

        $PassoAtendimentoSm->set($passo_atendimento_sm);
        $PassoAtendimentoSm->save();

        $AtendimentoSm->set($atendimento_sm);
        $AtendimentoSm->save();
        
        if($finalizar) {
            $this->finalizarPassosDepoisAtendimento($cod_atendimento_sm, $cod_passo_atendimento_sm);
        }
        
        return (parent::incluir($historico_sm)) ? true: false;
    }
    
    function finalizarPassosDepoisAtendimento($cod_atendimento_sm, $cod_passo_atendimento_sm) {
        $PassoAtendimentoSm = classRegistry::init('PassoAtendimentoSm');
        $AtendimentoSm = classRegistry::init('AtendimentoSm');

        $data_hora_atual = date('Y-m-d H:i:s');
        $passo_atendimento_sm_atual = $PassoAtendimentoSm->find('first', array('conditions' => array('codigo' => $cod_passo_atendimento_sm)));

        $PassoAtendimentoSm->set($passo_atendimento_sm_atual);
        $passo_atendimento_sm_atual['PassoAtendimentoSm']['data_fim'] = $data_hora_atual;
        $PassoAtendimentoSm->set($passo_atendimento_sm_atual);
        $PassoAtendimentoSm->save();
        
        $passos_em_aberto = $PassoAtendimentoSm->find('count', array('conditions' => array('codigo_atendimento_sm' => $cod_atendimento_sm, 'data_fim' => null)));
        
        if($passos_em_aberto == 0) {
            $atendimento_sm = $AtendimentoSm->find('first', array('conditions' => array('codigo' => $cod_atendimento_sm)));
            $atendimento_sm['AtendimentoSm']['data_fim'] = $data_hora_atual;
            $AtendimentoSm->set($atendimento_sm);
            $AtendimentoSm->save();
        }
    }

    function listaSLA($query_provisoria = array(), $pronta = false) {
        $TEspaEventoSistemaPadrao = classRegistry::init('TEspaEventoSistemaPadrao');
        $PassoAtendimentoSm = classRegistry::init('PassoAtendimentoSm');
        $PassoAtendimento = classRegistry::init('PassoAtendimento');
        $dbo = $this->getDataSource();
        
        $subquery_fields_group = array(
            "HistoricoSm.codigo_atendimento_sm",
            "HistoricoSm.codigo_tipo_evento"
        );
        
        $passo_atendimento = $dbo->buildStatement(
            array(
                'fields'=> array('codigo'),
                'table' => "{$PassoAtendimento->databaseTable}.{$PassoAtendimento->tableSchema}.{$PassoAtendimento->useTable}",
                'alias' => "PassoAtendimento",
                'conditions' => array(
                    "PassoAtendimento.ordem" => 2
                ),
                'limit' => null,
                'offset' => null,
                'order' => null,
                'group' => null,
            ), $this
        );
                
        if($pronta) {

            $data_inicio = $dbo->buildStatement(
                array(
                    'fields'=> array('data_inicio'),
                    'table' => "{$PassoAtendimentoSm->databaseTable}.{$PassoAtendimentoSm->tableSchema}.{$PassoAtendimentoSm->useTable}",
                    'alias' => 'PassoAtendimentoDataInicio',
                    'limit' => 1,
                    'conditions' => array(
                        "PassoAtendimentoDataInicio.codigo_atendimento_sm = HistoricoSm.codigo_atendimento_sm",
                        "codigo_passo_atendimento" => 2
                    ),
                    'offset' => null,
                    'order' => null,
                    'group' => null,
                ), $this
            );

            $data_analise = $dbo->buildStatement(
                array(
                    'fields'=> array('data_analise'),
                    'table' => "{$PassoAtendimentoSm->databaseTable}.{$PassoAtendimentoSm->tableSchema}.{$PassoAtendimentoSm->useTable}",
                    'alias' => 'PassoAtendimentoDataAnalise',
                    'limit' => 1,
                    'conditions' => array(
                        "PassoAtendimentoDataAnalise.codigo_atendimento_sm = HistoricoSm.codigo_atendimento_sm",
                        "codigo_passo_atendimento" => 2
                    ),
                    'offset' => null,
                    'order' => null,        
                    'group' => null,        
                ), $this
            );
                    
            $subquery_fields_pronta = array(
                "isnull(PassoAtendimento.codigo, 2) codigo_passo_atendimento",
                "({$data_inicio}) data_inicio",
                "({$data_analise}) data_analise",
            );
            $subquery_fields_group = array_merge($subquery_fields_group, $subquery_fields_pronta);
        } else {
            array_push($subquery_fields_group, "PassoAtendimentoSM.data_inicio");
            array_push($subquery_fields_group, "PassoAtendimentoSM.data_analise");
        }
        
        $subquery_group = array(
            "HistoricoSm.codigo_atendimento_sm",
            "HistoricoSm.codigo_tipo_evento",
        );
        
        if($pronta) {
            array_push($subquery_group, "PassoAtendimento.codigo");
        } else {
            $subquery_group = $subquery_fields_group;
        }
        
        $subquery_conditions = array(
            "HistoricoSm.codigo_passo_atendimento" => 1,
            "HistoricoSm.codigo_tipo_evento IS NOT NULL",
            "PassoAtendimentoSM.data_fim" => NULL
        );
        
        $subquery = $dbo->buildStatement(
            array(
                'fields' => $subquery_fields_group,
                'table' => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
                'alias' => 'HistoricoSm',
                'limit' => null,
                'offset' => null,
                'joins' => array(
                    array(
                        'table' => "{$PassoAtendimentoSm->databaseTable}.{$PassoAtendimentoSm->tableSchema}.{$PassoAtendimentoSm->useTable}",
                        'alias' => 'PassoAtendimentoSm',
                        'type' => 'LEFT',
                        'conditions' => array('HistoricoSm.codigo_atendimento_sm = PassoAtendimentoSm.codigo_atendimento_sm'),
                    ),
                    array(
                        'table' => "({$passo_atendimento})",
                        'alias' => 'PassoAtendimento',
                        'type' => 'LEFT',
                        'conditions' => array('HistoricoSm.codigo_passo_atendimento = PassoAtendimento.codigo'),
                    )
                ),
                'conditions' => $subquery_conditions,
                'order' => null,
                'group' => $subquery_group,
            ), $this
        );
                        
        $fields = array(
            "SUM(CASE WHEN DATEDIFF(n, HistoricoSm.data_inicio, isnull( HistoricoSm.data_analise, getdate())) <= 30 THEN 1 ELSE 0 END) AS dentro",
            "SUM(CASE WHEN DATEDIFF(n, HistoricoSm.data_inicio, isnull( HistoricoSm.data_analise, getdate())) > 30 THEN 1 ELSE 0 END) AS fora",
            "HistoricoSm.codigo_tipo_evento"
        );
        $group = array(
          "HistoricoSm.codigo_tipo_evento"
        );  
        
        if($pronta) {
            $conditions = array("HistoricoSm.data_inicio IS NOT NULL");
        } else {
            $conditions = null;
        }
        
        $query = $dbo->buildStatement(
            array(
                'fields' => $fields,
                'table' => "({$subquery})",
                'alias' => 'HistoricoSm',
                'limit' => null,
                'offset' => null,
                'conditions' => $conditions,
                'order' => null,
                'group' => $group,
            ), $this
        );
                
        if($pronta) {
            $query_provisoria .= ' UNION ';
            $query_provisoria .= $query;
            $query_final = $dbo->buildStatement(
                array(
                    'fields' => array(
                                "SUM(dentro) AS dentro",
                                "SUM(fora) AS fora",
                                "HistoricoSm.codigo_tipo_evento"
                    ),
                    'table' => "({$query_provisoria})",
                    'alias' => 'HistoricoSm',
                    'limit' => null,
                    'offset' => null,
                    'conditions' => null,
                    'order' => null,
                    'group' => $group,
                ), $this
            );
            $sla = $this->query($query_final);
            if($sla) {
                $sla = $TEspaEventoSistemaPadrao->trataArraySla($sla);
                return $sla;
            } else {
                return false;
            }
        } else {
            return $this->listaSLA($query, true);
        }
    }
    
}