<?php

class Ocorrencia extends AppModel {

    var $name = 'Ocorrencia';
    var $tableSchema = 'monitora';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'ocorrencias';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $hasMany = array(
        'OcorrenciaTipo' => array(
            'className' => 'OcorrenciaTipo',
            'foreignKey' => 'codigo_ocorrencia'
        ),
        'OcorrenciaHistorico' => array(
            'className' => 'OcorrenciaHistorico',
            'foreignKey' => 'codigo_ocorrencia'
        ),
    );

    var $belongsTo = array(
        'Funcionario' => array(
            'className' => 'Funcionario',
            'foreignKey' => 'usuario_monitora_inclusao',
            'fields' => 'Apelido'
        ),
        'FuncionarioAlteracao' => array(
            'className' => 'Funcionario',
            'foreignKey' => 'usuario_monitora_alteracao',
            'fields' => 'Apelido'
        ),
        'Equipamento' => array(
            'className' => 'Equipamento',
            'foreignKey' => 'codigo_tecnologia',
            'fields' => 'Descricao'
        ),
        'Recebsm' => array(
            'className' => 'Recebsm',
            'conditions' => 'convert(varchar, Ocorrencia.codigo_sm) = Recebsm.sm',
            'foreignKey' => false,
            'fields' => 'COD_operacao',
        ),
        'ClientEmpresa' => array(
            'className' => 'ClientEmpresa',
            'conditions' => 'Recebsm.cliente = ClientEmpresa.codigo',
            'foreignKey' => false,
            'fields' => 'tipo_operacao',
        ),
        'StatusOcorrencia' => array(
            'className' => 'StatusOcorrencia',
            'foreignKey' => 'codigo_status_ocorrencia'
        ),
        'AssinaturaLiberacao' => array(
            'className' => 'Usuario',
            'foreignKey' => 'codigo_supervisor_buonnysat'
        ),
    );

    var $validate = array(
        'data_ocorrencia' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'message' => 'Informe a data'
            ),
            'dataValida' => array(
                'rule' => 'validaDataOcorrencia',
                'required' => true,
                'message' => 'Informe uma data válida',
             )
         ),
        'placa' => array(
            'rule' => '/^[A-Z]{3}-[0-9]{4}$/',
            'message' => 'Informe a placa no formato AAA-9999'
        ),
        'codigo_sm' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Informe o cÃ³digo SM'
        ),
        'empresa' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Informe o nome da empresa'
        ),
        'codigo_tecnologia' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Informe a tecnologia'
        ),
        'motorista' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Informe o nome do motorista'
        ),
        'local' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Informe o local'
        ),
        'rodovia' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Informe a rodovia'
        ),
        'origem' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Informe a origem'
        ),
        'destino' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Informe o destino'
        ),
        'codigo_status_ocorrencia' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => 'Informe o status'
        ),
        'longitude' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'allowEmpty' => true,
                'message' => 'informe a longitude no formato 99.9999999'
            )
        ),
        'latitude' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'allowEmpty' => true,
                'message' => 'informe a latitude no formato 99.9999999'
            )
        ),
    );

    const TIPO_FILTRO_CONDICAO_E = 0;
    const TIPO_FILTRO_CONDICAO_OU = 1;

    function validaDataOcorrencia($data) {
        return strtotime($this->dateTimeToDbDateTime($data['data_ocorrencia'])) <= strtotime('+15 minute', mktime());
    }

    function statusUltimaOcorrencia($codigo_ocorrencia) {
        $ocorrencia = $this->find('first', array(
                    'conditions' => array('codigo_sm' => $codigo_ocorrencia),
                    'order' => array('Ocorrencia.data_inclusao desc'),
                        )
        );
        if (!empty($ocorrencia))
            return $ocorrencia['Ocorrencia']['codigo_status_ocorrencia'];
        return null;
    }

    function ultimaOcorrencia($codigo_ocorrencia) {
        $ocorrencia = $this->find('first', array(
                    'conditions' => array('Ocorrencia.codigo_sm' => $codigo_ocorrencia),
                    'order' => array('Ocorrencia.data_inclusao desc'),
                        )
        );
        return $ocorrencia;
    }

    function incluir($dados) {
        try {
            $this->create();
            $this->query('begin transaction');

            if(in_array(13, $dados['OcorrenciaTipo'])){
                $dados['Ocorrencia']['descricao_tipo_ocorrencia'] = null;
            }

            if (!$this->save($dados))
                throw new Exception();

            $dados['Ocorrencia']['codigo'] = $this->id;

            if (isset($dados['OcorrenciaTipo'])) {
                if (!$this->OcorrenciaTipo->atualizar($dados, true)) throw new Exception();
            }
            $historico = array('OcorrenciaHistorico' =>
                array(
                    'codigo_ocorrencia' => $this->id,
                    'codigo_status_ocorrencia' => $dados['Ocorrencia']['codigo_status_ocorrencia'],
                    'descricao' => $dados['Ocorrencia']['observacao'],
                    'usuario_monitora_inclusao' => $dados['Ocorrencia']['usuario_monitora_inclusao'],
                )
            );
            if (!$this->OcorrenciaHistorico->incluir($historico)) throw new Exception();

            $this->commit();
            return true;
        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }

    function atualizar($dados) {
        if (!isset($dados['Ocorrencia']['codigo']) || $dados['Ocorrencia']['codigo'] == null)
            return false;
        try {
            $this->query('begin transaction');
            if(!in_array(13, $dados['OcorrenciaTipo'])){
                $dados['Ocorrencia']['descricao_tipo_ocorrencia'] = null;
            }
            unset($dados[$this->name]['descricao_tipo_ocorrencia']);
            if (!$this->save($dados))
                throw new Exception();

            if (isset($dados['OcorrenciaTipo'])) {
                if (!$this->OcorrenciaTipo->atualizar($dados, true)) throw new Exception();
            }
            $historico = array('OcorrenciaHistorico' =>
                array(
                    'codigo_ocorrencia' => $this->id,
                    'codigo_status_ocorrencia' => $dados['Ocorrencia']['codigo_status_ocorrencia'],
                    'descricao' => $dados['Ocorrencia']['observacao'],
                    'usuario_monitora_inclusao' => $dados['Ocorrencia']['usuario_monitora_inclusao'],
                )
            );
            if (!$this->OcorrenciaHistorico->incluir($historico)) throw new Exception();
            $this->commit();
            return true;
        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }

    /**
     * @deprecated
     */
    function listaStatusEncarregado() {
        $status = array(
            '1' => 'EM ANALISE - OPERADOR',
            '3' => 'ENCAMINHADO ->ENCARREGADO',
            '4' => 'EM ANALISE - ENCARREGADO',
            '5' => 'FINALIZADO - ENCARREGADO'
        );
        return $status;
    }

    /**
     * @deprecated
     */
    function listaStatusVizualizacao() {
        $status = array(
            '1' => 'EM ANALISE - OPERADOR',
            '2' => 'FINALIZADO - OPERADOR',
            '3' => 'ENCAMINHADO ->ENCARREGADO',
            '4' => 'EM ANALISE - ENCARREGADO',
            '5' => 'FINALIZADO - ENCARREGADO'
        );
        return $status;
    }

    function listaOcorrencias($filtros, $tipo = 'all') {
        $this->bindLazyUsuarioInclusao();
        $this->bindLazyUsuarioAlteracao();
        $this->bindLazyTecnologia();

        $retorno = $this->find($tipo, $filtros);

        $this->unbindUsuario();
        $this->unbindTecnologia();

        return $retorno;
    }

    function formataDados($dados, $tipo_conversao = 1) {
        if ($tipo_conversao == 1) {
            $ocorrencias_tipos = array();
            if (is_array($dados['OcorrenciaTipoSelecionado']['codigo_tipo_ocorrencia'])) {
                foreach ($dados['OcorrenciaTipoSelecionado']['codigo_tipo_ocorrencia'] as $codigo_tipo_ocorrencia) {
                    $ocorrencias_tipos[] = array(
                        'codigo_tipo_ocorrencia' => $codigo_tipo_ocorrencia,
                        'observacao' => null,
                    );
                }
                $dados['OcorrenciaTipo'] = $ocorrencias_tipos;
            }
        } else {
            $codigos_tipos_ocorrencias = array();
            foreach ($dados['OcorrenciaTipo'] as $tipo_ocorrencia) {
                $codigos_tipos_ocorrencias[] = $tipo_ocorrencia['codigo_tipo_ocorrencia'];
            }
            $dados['OcorrenciaTipoSelecionado']['codigo_tipo_ocorrencia'] = $codigos_tipos_ocorrencias;
            $dados['Ocorrencia']['data_ocorrencia'] = substr($dados['Ocorrencia']['data_ocorrencia'], 0,16);
        }
        return $dados;
    }

    function converteFiltrosEmConditions($filtros, $codigo_objeto = null, $com_sla = false){
        $conditions = array();
        $condition_tecnologia = array();
        if (!empty($filtros['codigo_tecnologia']))
            $condition_tecnologia['Ocorrencia.codigo_tecnologia'] = $filtros['codigo_tecnologia'];

        if (!empty($filtros['codigo_operacao'])) {
            $sem_vinculo = array_search(14, $filtros['codigo_operacao']);
            if ($sem_vinculo !== false) {
                unset($filtros['codigo_operacao'][$sem_vinculo]);
                $filtros['codigo_operacao'] = array_merge($filtros['codigo_operacao'], array(0, 14));
                $condition_tipo_operacao = array('OR' => array(array('ClientEmpresa.tipo_operacao' => null), array('ClientEmpresa.tipo_operacao' => $filtros['codigo_operacao'])));
            } else {
                $condition_tipo_operacao = array('ClientEmpresa.tipo_operacao' => $filtros['codigo_operacao']);
            }
            if (isset($filtros['codigo_tecnologia']) && count($filtros['codigo_tecnologia']) > 0 && isset($filtros['tipo_filtro_operacoes']) && $filtros['tipo_filtro_operacoes'] == Ocorrencia::TIPO_FILTRO_CONDICAO_OU) {
                $conditions['OR'] = array($condition_tecnologia, $condition_tipo_operacao);
            } else {
                $conditions = array_merge($condition_tecnologia, $condition_tipo_operacao);
            }
        } elseif (!empty($filtros['codigo_tecnologia'])) {
            $conditions['Ocorrencia.codigo_tecnologia'] = $filtros['codigo_tecnologia'];
        }

        if ($com_sla && !empty($filtros['tipo_sla'])) {
            if ($filtros['tipo_sla'] == 1) {
                $conditions['DATEDIFF(n, Ocorrencia.data_inclusao, isnull(Ocorrencia.data_alteracao, getdate())) >'] = 30;
            } elseif ($filtros['tipo_sla'] == 2) {
                $conditions['DATEDIFF(n, Ocorrencia.data_inclusao, isnull(Ocorrencia.data_alteracao, getdate())) <='] = 30;
            }
        }

        if (!empty($filtros['placa']))
            $conditions['Ocorrencia.placa like'] = $filtros['placa'] . '%';
        if (!empty($filtros['codigo_status_ocorrencia']))
            $conditions['Ocorrencia.codigo_status_ocorrencia'] = $filtros['codigo_status_ocorrencia'];
        if (!empty($filtros['codigo_prioridade']))
            $conditions['Ocorrencia.codigo_prioridade'] = $filtros['codigo_prioridade'];
        if (!empty($filtros['local']))
            $conditions['Ocorrencia.local like'] = '%' . $filtros['local'] . '%';
        if (!empty($filtros['origem']))
            $conditions['Ocorrencia.origem like'] = '%' . $filtros['origem'] . '%';
        if (!empty($filtros['rodovia']))
            $conditions['Ocorrencia.rodovia like'] = '%' . $filtros['rodovia'] . '%';
        if (!empty($filtros['destino']))
            $conditions['Ocorrencia.destino like'] = '%' . $filtros['destino'] . '%';
        if (!empty($filtros['empresa']))
            $conditions['Ocorrencia.empresa like'] = '%' . $filtros['empresa'] . '%';
        if (!empty($filtros['motorista']))
            $conditions['Ocorrencia.motorista like'] = '%' . $filtros['motorista'] . '%';
        if (!empty($filtros['data_inclusao'])) {
            $data = explode(' ', $filtros['data_inclusao']);
            $data_correta = explode('/', $data[0]);
            $data_final = array(
              $data_correta['2'] . '/' .$data_correta['1'] . '/' . $data_correta['0'] . ' 00:00:00',
              $data_correta['2'] . '/' .$data_correta['1'] . '/' . $data_correta['0'] . ' 23:59:59'
            );
            $conditions['Ocorrencia.data_inclusao between ? and ?'] = $data_final;
        }
        if (!empty($filtros['data_ocorrencia'])) {
            $data = explode(' ', $filtros['data_ocorrencia']);
            $data_correta = explode('/', $data[0]);
            $data_final = array(
              $data_correta['2'] . '/' .$data_correta['1'] . '/' . $data_correta['0'] . ' 00:00:00',
              $data_correta['2'] . '/' .$data_correta['1'] . '/' . $data_correta['0'] . ' 23:59:59'
            );
            $conditions['Ocorrencia.data_ocorrencia between ? and ?'] = $data_final;
        }
        if (!empty($filtros['data_alteracao'])) {
            $data = explode(' ', $filtros['data_alteracao']);
            $data_correta = explode('/', $data[0]);
            $data_final = array(
              $data_correta['2'] . '/' .$data_correta['1'] . '/' . $data_correta['0'] . ' 00:00:00',
              $data_correta['2'] . '/' .$data_correta['1'] . '/' . $data_correta['0'] . ' 23:59:59'
            );
            $conditions['Ocorrencia.data_alteracao between ? and ?'] = $data_final;
        }
        if (!empty($filtros['codigo_sm']))
            $conditions['Ocorrencia.codigo_sm'] = $filtros['codigo_sm'];
        if (!empty($filtros['codigo_tipo_ocorrencia'])) {
            $this->OcorrenciaTipo = & ClassRegistry::init('OcorrenciaTipo');
            $condicoes = array('codigo_tipo_ocorrencia' => $filtros['codigo_tipo_ocorrencia']);
            $tipos_ocorrencia = $this->OcorrenciaTipo->find('all', array('conditions' => $condicoes, 'fields' => 'codigo_ocorrencia'));
            $conditions['Ocorrencia.codigo'] = Set::extract('/OcorrenciaTipo/codigo_ocorrencia', $tipos_ocorrencia);
        }

        if (empty($filtros['codigo_status_ocorrencia']) && !empty($codigo_objeto)) {
            $this->PerfilStatusOcorrencia = & ClassRegistry::init('PerfilStatusOcorrencia');
            $tipoStatusSVizualizacao = $this->PerfilStatusOcorrencia->statusPorObjeto($codigo_objeto);
            $conditions['Ocorrencia.codigo_status_ocorrencia'] = array_keys($tipoStatusSVizualizacao);
        }
        return $conditions;
    }

    function findRecursiveByCodigo($codigo) {
        $result = $this->findByCodigo($codigo);
        foreach ($result['OcorrenciaHistorico'] as $key => $ocorrencia_historico) {
            $ocorrencia_historico_recursivo = $this->OcorrenciaHistorico->findByCodigo($ocorrencia_historico['codigo']);
            $result['OcorrenciaHistorico'][$key] = $ocorrencia_historico_recursivo;
        }
        return $result;
    }


    const SETOR_GERAL = 1;
    const SETOR_BSAT = 2;
    const SETOR_PRONTA_RESPOSTA = 3;
    function statusSLAPorSetor($periodo, $tempo_sla, $setor = 1) {
    	$lista = array();
    	if ($setor == Ocorrencia::SETOR_GERAL) {
    		$lista['sem_analise'] = $this->statusSLA($periodo, $tempo_sla, Ocorrencia::SLA_SEM_ANALISE_GERAL);
    		$lista['em_analise'] = $this->statusSLA($periodo, $tempo_sla, Ocorrencia::SLA_EM_ANALISE_GERAL);
    	} elseif ($setor == Ocorrencia::SETOR_BSAT) {
    		$lista['sem_analise'] = $this->statusSLA($periodo, $tempo_sla, Ocorrencia::SLA_SEM_ANALISE_BSAT);
    		$lista['em_analise'] = $this->statusSLA($periodo, $tempo_sla, Ocorrencia::SLA_EM_ANALISE_BSAT);
    	}
    	return $lista;
    }

    const SLA_SEM_ANALISE_GERAL = 1;
    const SLA_EM_ANALISE_GERAL = 2;
    const SLA_SEM_ANALISE_BSAT = 3;
    const SLA_EM_ANALISE_BSAT = 4;
    const SLA_SEM_ANALISE_PRONTA_RESPOSTA = 5;
    const SLA_EM_ANALISE_PRONTA_RESPOSTA = 6;
    function statusSLA($periodo, $tempo_sla, $tipo) {
        $this->unbindModel(array('hasMany' => array('OcorrenciaTipo', 'OcorrenciaHistorico')));
        $this->unbindModel(array('belongsTo' => array('Funcionario', 'FuncionarioAlteracao', 'Equipamento', 'Recebsm', 'ClientEmpresa', 'StatusOcorrencia')));
        $this->bindModel(array('hasOne' => array('OcorrenciaTipo' => array('className' => 'OcorrenciaTipo', 'foreignKey' => 'codigo_ocorrencia'))));
    	if ($tipo == Ocorrencia::SLA_SEM_ANALISE_GERAL) {
    		$coluna = 'Sem Análise';
    		$codigo_status = array(1,3);
    	} elseif ($tipo == Ocorrencia::SLA_EM_ANALISE_GERAL) {
    		$coluna = 'Em Análise';
    		$codigo_status = array(4,7,11);
    	} elseif ($tipo == Ocorrencia::SLA_SEM_ANALISE_BSAT) {
    		$coluna = 'Sem Análise';
    		$codigo_status = array(1,3);
    	} elseif ($tipo == Ocorrencia::SLA_EM_ANALISE_BSAT) {
    		$coluna = 'Em Análise';
    		$codigo_status = array(4);
    	} elseif ($tipo == Ocorrencia::SLA_SEM_ANALISE_PRONTA_RESPOSTA) {
    		$coluna = 'Sem Análise';
    		$codigo_status = array(6, 9);
    	} elseif ($tipo == Ocorrencia::SLA_EM_ANALISE_PRONTA_RESPOSTA) {
    		$coluna = 'Em Análise';
    		$codigo_status = array(7, 11);
    	}
    	$fields = array(
    		"SUM(
					CASE
						WHEN DATEDIFF(n, {$this->name}.data_inclusao, isnull({$this->name}.data_alteracao, getdate())) <= $tempo_sla
						THEN 1
						ELSE 0
					END) AS dentro",
				"SUM(
					CASE
						WHEN DATEDIFF(n, {$this->name}.data_inclusao, isnull({$this->name}.data_alteracao, getdate())) > $tempo_sla
						THEN 1
						ELSE 0
					END) AS fora",
    	);
    	$conditions = array($this->name.'.codigo_status_ocorrencia' => $codigo_status);
    	return $this->find('first', array('fields' => $fields, 'conditions' => $conditions));
    }

    function exportaAtendimento($codigo_ocorrencia, $in_another_transaction = false) {
        $this->HistoricoSm = ClassRegistry::init('HistoricoSm');
        $this->AtendimentoSm = ClassRegistry::init('AtendimentoSm');
        $this->PassoAtendimento = ClassRegistry::init('PassoAtendimento');
        $this->PassoAtendimentoSm = ClassRegistry::init('PassoAtendimentoSm');
        $this->OcorrenciaHistorico = ClassRegistry::init('OcorrenciaHistorico');
        
        $primeiro_passo_atendimento = $this->PassoAtendimento->find('first', array('conditions' => array('ordem' => 1), 'fields' => array('codigo')));
        $ocorrencia = $this->find('first', array('conditions' => array('Ocorrencia.codigo' => $codigo_ocorrencia)));
        try {
            $this->create();
            if (!$in_another_transaction) $this->query('begin transaction');
                    $atendimento_sm = array(
                        'AtendimentoSm' => array(
                            'codigo_sm' => $ocorrencia['Ocorrencia']['codigo_sm'],
                            'codigo_passo_atendimento' => $primeiro_passo_atendimento['PassoAtendimento']['codigo'],
                            'codigo_prioridade' => $ocorrencia['Ocorrencia']['codigo_prioridade'],
                            'codigo_usuario' => '',
                            'codigo_usuario_monitora' => $ocorrencia['Ocorrencia']['usuario_monitora_inclusao'],
                            'codigo_usuario_autorizacao' => $ocorrencia['Ocorrencia']['codigo_supervisor_buonnysat'],
                            'data_inicio' => $ocorrencia['Ocorrencia']['data_inclusao'],
                            'codigo_ocorrencia' => $codigo_ocorrencia
                        ),
                    );
                    if ($this->AtendimentoSm->incluirEvento($atendimento_sm, true, false)) {
                        if (!$this->gravarDataAnaliseDataFimEmAtendimentoSmPassoAtendimentoSm($codigo_ocorrencia)) { throw new Exception(); }

                        $codigo_passo_atendimento_sm = $this->PassoAtendimentoSm->find('first', array('fields' => array('MAX(codigo) AS codigo')));
                        $historicos = $this->OcorrenciaHistorico->find('all', array( 'conditions' => array( 'codigo_ocorrencia' => $ocorrencia['Ocorrencia']['codigo'] ) ) );
                        
                        $dados = array(
                            'AtendimentoSm' => array(
                                'codigo_sm' => $ocorrencia['Ocorrencia']['codigo_sm'],
                                'codigo_tipo_evento' => $this->buscarDescricaoTipoEvento($ocorrencia['Ocorrencia']['codigo']),
                                'codigo_usuario_autorizacao' => $ocorrencia['Ocorrencia']['codigo_supervisor_buonnysat'],
                                'local' => $ocorrencia['Ocorrencia']['local'],
                                'latitude' => $ocorrencia['Ocorrencia']['latitude'],
                                'longitude' => $ocorrencia['Ocorrencia']['longitude'],
                        ));
                        $passo_atendimento_sm = array(
                            'PassoAtendimentoSm' => array(
                                'codigo' => $codigo_passo_atendimento_sm[0]['codigo'],
                                'codigo_passo_atendimento' => $primeiro_passo_atendimento['PassoAtendimento']['codigo']
                        ));
                            
                        foreach($historicos as $historico) {
                            if (strlen($historico['OcorrenciaHistorico']['descricao']) <= 1) {
                                $historico['OcorrenciaHistorico']['descricao'] = 'descrição não informada';
                            }
                            $dados['AtendimentoSm']['texto'] = $historico['OcorrenciaHistorico']['descricao'];
                            $dados['AtendimentoSm']['codigo_usuario_monitora'] = $historico['OcorrenciaHistorico']['usuario_monitora_inclusao'];
                            
                            $passo_atendimento_sm = array(
                                'PassoAtendimentoSm' => array(
                                    'codigo' => $codigo_passo_atendimento_sm[0]['codigo'],
                                    'codigo_passo_atendimento' => $primeiro_passo_atendimento['PassoAtendimento']['codigo']
                                )
                            );
                            
                            if (!$this->HistoricoSm->incluir($passo_atendimento_sm, $dados)) { throw new Exception(); }
                            
                        }
                        if(count($ocorrencia['OcorrenciaTipo']) > 0) {
                            foreach($ocorrencia['OcorrenciaTipo'] as $ocorrencia_tipo) {
                                if (strlen($ocorrencia_tipo['observacao']) <= 1) {
                                    $dados['AtendimentoSm']['texto'] = 'descrição não informada';
                                } else {
                                    $dados['AtendimentoSm']['texto'] = $ocorrencia_tipo['observacao'];
                                }
                                if (!$this->HistoricoSm->incluir($passo_atendimento_sm, $dados)) { throw new Exception(); }
                            }
                        }
                    } else {
                        throw new Exception();
                    }
                if(!$in_another_transaction) { $this->commit(); }
                return true;
        } catch (Exception $ex) {
            if(!$in_another_transaction) { $this->rollback(); }
            return false;
        }
    }
    
    function buscarDescricaoTipoEvento($codigo_ocorrencia) {
        $this->OcorrenciaTipo = ClassRegistry::init('OcorrenciaTipo');
        $this->TipoOcorrencia = ClassRegistry::init('TipoOcorrencia');
        $this->TipoEvento = ClassRegistry::init('TipoEvento');
        $descricao = $this->OcorrenciaTipo->find('first', array(
            'joins' => array(
                array(
                    'table' => "{$this->TipoOcorrencia->databaseTable}.{$this->TipoOcorrencia->tableSchema}.{$this->TipoOcorrencia->useTable}",
                    'alias' => 'Tipo',
                    'conditions' => 'OcorrenciaTipo.codigo_tipo_ocorrencia = Tipo.codigo',
                    'type' => 'left',
                )
            ),
            'conditions' => array('codigo_ocorrencia' => $codigo_ocorrencia),
            'fields' => 'Tipo.descricao'
        ));
        $tipo_evento = $this->TipoEvento->find('first', array('conditions' => array('descricao' => $descricao['Tipo']['descricao']), 'fields' => 'codigo'));
        return $tipo_evento['TipoEvento']['codigo'];
    }
    
    function gravarDataAnaliseDataFimEmAtendimentoSmPassoAtendimentoSm($codigo_ocorrencia) {
        $this->AtendimentoSm = ClassRegistry::init('AtendimentoSm');
        $this->PassoAtendimentoSm = ClassRegistry::init('PassoAtendimentoSm');
        
        $em_analise = array(1, 3, 4, 6, 7, 9, 11);
        $finalizado = array(2, 5, 8, 10);
        $atendimento_sm_data_analise = '';
        $atendimento_sm_data_fim = '';
        $passo_atendimento_sm_data_analise = '';
        $passo_atendimento_sm_data_fim = '';
        $dados_ocorrencia = $this->find('first', array('conditions' => array('Ocorrencia.codigo' => $codigo_ocorrencia), 'fields' => array('data_inclusao', 'codigo_status_ocorrencia'), 'recursive' => -1));
        
        $atendimento_sm = $this->AtendimentoSm->find('first', array(
            'conditions' => array('codigo_ocorrencia' => $codigo_ocorrencia),
            'fields' => array('codigo', 'codigo_sm', 'codigo_prioridade', 'data_inicio', 'data_analise', 'data_fim'),
        ));
        
        $passo_atendimento_sm = $this->PassoAtendimentoSm->find('first', array(
            'conditions' => array('codigo_atendimento_sm' => $atendimento_sm['AtendimentoSm']['codigo']),
            'fields' => array('codigo', 'codigo_atendimento_sm', 'data_inicio', 'data_analise', 'data_encaminhado', 'data_fim'),
        ));

        if(in_array($dados_ocorrencia['Ocorrencia']['codigo_status_ocorrencia'], $finalizado)) {
            $atendimento_sm['AtendimentoSm']['data_analise'] = $dados_ocorrencia['Ocorrencia']['data_inclusao'];
            $atendimento_sm['AtendimentoSm']['data_fim'] = $dados_ocorrencia['Ocorrencia']['data_inclusao'];
            $passo_atendimento_sm['PassoAtendimentoSm']['data_analise'] = $dados_ocorrencia['Ocorrencia']['data_inclusao'];
            $passo_atendimento_sm['PassoAtendimentoSm']['data_fim'] = $dados_ocorrencia['Ocorrencia']['data_inclusao'];
        } elseif(in_array($dados_ocorrencia['Ocorrencia']['codigo_status_ocorrencia'], $em_analise)) {
            $atendimento_sm['AtendimentoSm']['data_analise'] = $dados_ocorrencia['Ocorrencia']['data_inclusao'];
            $passo_atendimento_sm['PassoAtendimentoSm']['data_analise'] = $dados_ocorrencia['Ocorrencia']['data_inclusao'];
        }
        
        if (!$this->AtendimentoSm->atualizar($atendimento_sm)) {
            return false;
        }
        if (!$this->PassoAtendimentoSm->atualizar($passo_atendimento_sm)) {
            return false;
        }
        return true;
    }
    
}
?>