<?php

class Sinistro extends AppModel {

    var $name 			= 'Sinistro';
    var $tableSchema 	= 'monitora';
    var $databaseTable 	= 'dbMonitora';
    var $useTable 		= 'sinistros';
    var $primaryKey 	= 'codigo';
    var $validate       = array(
        'sm' => array(
            'sm_unica' => array(
                'rule' => 'isUnique',
                'message' => 'Já existe sinistro para esta SM',
            ),
            'sm_existente' => array(
                'rule' => 'existeSm',
                'message' => 'SM inexistente',
            ),            
        ),
        'data_evento' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a data do evento!'
            ),
            'periodo' => array(
                'rule' => 'periodoSm',
                'message' => 'A data deve estar dentro do período da SM',
            ),
        ),
        'codigo_documento_profissional' => array(
            'rule' => 'verificaCpf',
            'message' => 'Documento inválido',
        ),
        'codigo_documento_transportador' => array(
            'rule' => 'verificaCnpj',
            'message' => 'Documento inválido',
        ),
        'codigo_documento_embarcador' => array(
            'rule' => 'verificaCnpj',
            'message' => 'Documento inválido',
        ),
        'codigo_documento_seguradora' => array(
            'rule' => 'verificaCnpj',
            'message' => 'Documento inválido',
        ),
        'codigo_documento_corretora' => array(
            'rule' => 'verificaCnpj',
            'message' => 'Documento inválido',
        ),
        'hora' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a hora do evento!'
            ),
        ),
        'natureza' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Escolha a natureza do evento'
            ),
        ),
        'status_veiculo' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Escolha o status do veículo'
            ),
        ),
        'avalicao_geral' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Escolha a avaliação'
            ),
        ),
        'latitude' => array(
            'rule' => array('decimal'),
            'allowEmpty' => true,
            'message' => 'Informe a latitude no formato 99.999999'
        ),
        'longitude' => array(
            'rule' => array('decimal'),
            'allowEmpty' => true,
            'message' => 'Informe a longitude no formato 99.9999999'
        ),
    );

    const AGRP_EMBARCADOR = 1;
    const AGRP_TRANSPORTADOR = 2;
    const AGRP_SEGURADOR = 3;
    const AGRP_CORRETOR = 4;
    const AGRP_MOTORISTA = 5;
    const AGRP_SINISTRO = 6;
    const AGRP_TECNOLOGIA = 7;

    function verificaCpf($data) {
        return Comum::validarCpf($data['codigo_documento_profissional']);
    }

    function verificaCnpj($data) {
        $campo = key($data);
        return Comum::validarCnpj($data[$campo]);
    }

    function existeSm($data) {
        $sm = ClassRegistry::init('TViagViagem')->carregarPorCodigoSm($data['sm']);
        return !empty($sm);
    }
    // Função não encontrada para uso
    // function smFinalizada($data) {
    //     $sm = ClassRegistry::init('TViagViagem')->carregarPorCodigoSm($data['sm']);
    //     if ($sm) {
    //         return !empty($sm['TViagViagem']['viag_data_fim']);
    //     }
    //     return true;
    // }

    public function bindCompleto($persistente = TRUE) {
         $this->bindModel(array('belongsTo' => array(
            'Embarcador' => array('className' => 'Cliente', 'foreignKey' => 'codigo_embarcador'),
            'Transportador' => array('className' => 'Cliente', 'foreignKey' => 'codigo_transportador'),
            'Seguradora' => array('foreignKey' => 'codigo_seguradora'),
            'Corretora' => array('foreignKey' => 'codigo_corretora'),
            'Veiculo' => array('foreignKey' => 'codigo_veiculo'),
            'Profissional' => array('foreignKey' => 'codigo_profissional'),
            'Recebsm' => array('type' => 'LEFT' , 'foreignKey' => 'SM'),
            'Tecnologia' => array('type' => 'LEFT' , 'foreignKey' => false, 'conditions' => 'Recebsm.codigo_tecnologia = Tecnologia.codigo'),
        )), $persistente);
    }

    function unbindCompleto() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'Embarcador',
                'Transportador',
                'Seguradora',
                'Corretora',
                'Veiculo',
                'Profissional',
                'Recebsm',
                'Tecnologia',
            )
        ));
    }

    function periodoSm($data) {
        if (!empty($this->data[$this->name]['sm'])) {
            $sm = ClassRegistry::init('TViagViagem')->carregarPorCodigoSm($this->data[$this->name]['sm']);
            if ($sm) {
                $data_evento = AppModel::dateToDbDate($this->data[$this->name]['data_evento']);
                $viag_data_cadastro = AppModel::dateToDbDate($sm['TViagViagem']['viag_data_cadastro']);
                $ret = ( ($data_evento >= $viag_data_cadastro)  && (date("Ymd H:i:s") >= $data_evento ) );
                return $ret;
            }
        }
        return true;
    }

    function listAvaliacaoGeral() {
        return array('Ótima','Boa','Regular','Ruim','Péssima');
    }

    function listStatusVeiculo() {
        return array('Em movimento','Nao Monitorado','Parado','Pernoite','Telemonitorado');
    }

    function listNatureza() {
        return array( 0 => 'Recuperado', 
                      1 => 'Roubo Parcial', 
                      2 => 'Furto Parcial', 
                      3 => 'Roubo Total', 
                      4 => 'Furto Total', 
                      5 => 'Tentativa', 
                      6 => 'Saque Parcial',
                      7 => 'Saque Total');
    }

    function tiposAgrupamento() {
        return array(self::AGRP_EMBARCADOR => 'Embarcador', 
                     self::AGRP_TRANSPORTADOR => 'Transportadora',
                     self::AGRP_SEGURADOR  => 'Seguradora', 
                     self::AGRP_CORRETOR      => 'Corretora',
                     self::AGRP_MOTORISTA  => 'Motorista',
                     self::AGRP_SINISTRO  => 'Tipo Sinistro',
                     self::AGRP_TECNOLOGIA  => 'Tecnologia');
    }

    function bindRecebsm(){
    	$this->bindModel(array(
		   'belongsTo' => array(
			   'Recebsm' => array('foreignKey' => 'sm'),
		   )
		));
    }

    public function converterFiltrosEmConditions($filtro){
        $conditions = array();

        if( isset($filtro['data_inicial']) && !empty($filtro['data_inicial'])) {
            $conditions['Sinistro.data_evento >='] = AppModel::dateToDbDate($filtro['data_inicial']).' 00:00:00';
        }
        if( isset($filtro['data_final']) && !empty($filtro['data_final'])) {
            $conditions['Sinistro.data_evento <='] = AppModel::dateToDbDate($filtro['data_final']).' 23:59:59';
        }
        if( isset($filtro['codigo_sinistro']) && !empty($filtro['codigo_sinistro']) )
            $conditions['Sinistro.codigo'] = $filtro['codigo_sinistro'];

        if( isset($filtro['codigo_embarcador']) && !empty($filtro['codigo_embarcador']) )
            $conditions['Sinistro.codigo_embarcador'] = $filtro['codigo_embarcador'];

        if( isset($filtro['codigo_transportador']) && !empty($filtro['codigo_transportador']) )
            $conditions['Sinistro.codigo_transportador'] = $filtro['codigo_transportador'];

        if( isset($filtro['codigo_documento_profissional']) && !empty($filtro['codigo_documento_profissional']) )
            $conditions["Profissional.codigo_documento"] = str_replace(array('.','/','-'), '', $filtro['codigo_documento_profissional']);

        if( isset($filtro['nome_profissional']) && !empty($filtro['nome_profissional']) )
            $conditions["Profissional.nome LIKE"] = $filtro['nome_profissional'].'%';

        if( isset($filtro['codigo_corretora']) && !empty($filtro['codigo_corretora']) )
            $conditions['Corretora.codigo'] = $filtro['codigo_corretora'];

        if( isset($filtro['codigo_seguradora']) && !empty($filtro['codigo_seguradora']) )
            $conditions['Seguradora.codigo'] = $filtro['codigo_seguradora'];

        if( isset($filtro['natureza']) && $filtro['natureza'] != '' )
            $conditions['Sinistro.natureza'] = $filtro['natureza'];

        if( isset($filtro['placa']) && !empty($filtro['placa']) )
            $conditions["Veiculo.placa"] = strtoupper(str_replace('-', '', $filtro['placa']));

        if( isset($filtro['sm']) && !empty($filtro['sm']) )
            $conditions['Sinistro.sm'] = $filtro['sm'];

        if( isset($filtro['codigo']) && !empty($filtro['codigo']) )
            $conditions['Sinistro.codigo'] = $filtro['codigo'];

        return $conditions;
    }

    public function listagem($conditions=array(),$agrupamento=null){
        $this->bindCompleto(FALSE);

        // Agrupamento
        if ( !empty($agrupamento) && isset($agrupamento) ) {
            if ($agrupamento == self::AGRP_EMBARCADOR) {            // 1
                $fields = array(
                    'Embarcador.codigo as codigo',
                    'Embarcador.razao_social as descricao',
                );
                $group = array(
                    'Embarcador.codigo',
                    'Embarcador.razao_social',
                );
            } else if ($agrupamento == self::AGRP_TRANSPORTADOR) {  // 2
                $fields = array(
                    'Transportador.codigo as codigo',
                    'Transportador.razao_social as descricao',
                );
                $group = array(
                    'Transportador.codigo',
                    'Transportador.razao_social',
                );
            } else if ($agrupamento == self::AGRP_SEGURADOR) {      // 3
                $fields = array(
                    'Seguradora.codigo AS codigo',
                    'Seguradora.nome AS descricao',
                );
                $group = array(
                    'Seguradora.codigo',
                    'Seguradora.nome',
                );
            } else if ($agrupamento == self::AGRP_CORRETOR) {       // 4
                $fields = array(
                    'Corretora.codigo as codigo',
                    'Corretora.nome AS descricao',
                );
                $group = array(
                    'Corretora.codigo',
                    'Corretora.nome',
                );
            } else if ($agrupamento == self::AGRP_MOTORISTA) {      // 6
                $fields = array(
                    'Profissional.codigo_documento as codigo',
                    'Profissional.nome  AS descricao',
                );
                $group = array(
                    'Profissional.codigo_documento',
                    'Profissional.nome',
                );
            }else if ($agrupamento == self::AGRP_SINISTRO) {     // 6
                $naturezas = $this->listNatureza();
                $fields = array(
                    'Sinistro.natureza as codigo',
                    "(CASE Sinistro.natureza
                        WHEN 0 THEN '".$naturezas[0]."'
                        WHEN 1 THEN '".$naturezas[1]."'
                        WHEN 2 THEN '".$naturezas[2]."'
                        WHEN 3 THEN '".$naturezas[3]."'
                        WHEN 4 THEN '".$naturezas[4]."'
                        WHEN 5 THEN '".$naturezas[5]."'
                        WHEN 6 THEN '".$naturezas[6]."'
                        WHEN 7 THEN '".$naturezas[7]."'
                     END) AS descricao"
                );
                $group = array(
                    'Sinistro.natureza',
                );
            }else if ($agrupamento == self::AGRP_TECNOLOGIA) {     // 6
                $fields = array(
                    'Tecnologia.codigo as codigo',
                    'Tecnologia.descricao  AS descricao',
                );
                $group = array(
                    'Tecnologia.codigo',
                    'Tecnologia.descricao',
                );
            }
            $fields = array_merge(array("COUNT(*) AS qtd_ocorrencias"), $fields);
            $order = array("COUNT(*) DESC");
            return $this->find('all', compact('fields', 'group', 'conditions', 'order'));
        } else {
            // Sem Agrupamento
            $result = $this->find( 'all', array(
                'fields' => array(
                    'Sinistro.codigo',
                    'Sinistro.sm',
                    'Sinistro.valor_recuperado',
                    'Veiculo.placa',
                    'Sinistro.data_evento',
                    'Embarcador.razao_social',
                    'Embarcador.codigo',
                    'Transportador.razao_social',
                    'Transportador.codigo',
                    'Profissional.nome',
                    'Profissional.codigo_documento',
                    'Seguradora.nome',
                    'Corretora.nome',
                    'Sinistro.natureza',
                ),
                'conditions' => $conditions,
                'order' => 'Sinistro.data_evento asc'
            ));
            return $result;
        }
        $this->unbindCompleto();
    }

    public function historicoPorMotorista($codigo_motorista,$dados){
        $this->bindRecebsm();

        $conditions = array('Recebsm.MotResp' => $codigo_motorista);

        if(!$dados['data_inicio'])
            return FALSE;

        $conditions['Sinistro.data_evento >=']  = date("Y-m-d 00:00:00",Comum::dateToTimestamp($dados['data_inicio']));

        if(!$dados['data_fim'])
            return FALSE;

		$conditions['Sinistro.data_evento <='] 	= date("Y-m-d 23:59:59",Comum::dateToTimestamp($dados['data_fim']));

		$fields	= array(
			'natureza',
			'MAX(CONVERT(VARCHAR(10),data_evento,103)) AS ultima_data',
			'COUNT(1) AS total',
		);

		$group 	= array('natureza');
    	return $this->find('all',compact('conditions','fields','group'));

    }

    function ultimasFichasTLC() {
        $this->bindModel(array('belongsTo' => array(
            'Recebsm' => array('foreignKey' => 'sm'),
            'Motorista' => array('foreignKey' => false, 'conditions' => 'Motorista.codigo = Recebsm.MotResp'),
        )));
        $Ficha = ClassRegistry::init('Ficha');
        $ProfissionalLog = ClassRegistry::init('ProfissionalLog');
        $fields = array(
            "(SELECT MAX(Ficha.codigo)
              FROM {$Ficha->databaseTable}.{$Ficha->tableSchema}.{$Ficha->useTable} AS Ficha
              INNER JOIN {$ProfissionalLog->databaseTable}.{$ProfissionalLog->tableSchema}.{$ProfissionalLog->useTable} AS ProfissionalLog ON ProfissionalLog.codigo = Ficha.codigo_profissional_log
              WHERE ProfissionalLog.codigo_documento = REPLACE(REPLACE(Motorista.CPF,'-',''),'.','') AND Ficha.codigo_status <= 3
            ) AS ultima_ficha"
        );
        $conditions = array('Sinistro.data_cadastro >=' => date('Ymd 00:00:00', strtotime('-3 years')));
        $ultimas_fichas = $this->find('sql', compact('conditions', 'fields'));
        $dbo = $this->getDatasource();
        return $dbo->buildStatement(array(
            'fields' => array('DISTINCT ultima_ficha'),
            'table' => "($ultimas_fichas)",
            'alias' => "UltimasFichas",
            'limit' => null,
            'offset' => null,
            'joins' => array(),
            'conditions' => array('ultima_ficha IS NOT NULL'),
            'order' => null,
            'group' => null,
        ), $this);
    }

    function valorSinistradoPorMes($filtros){
        $data_inicial = $filtros['ano'].'-01-01';
        $data_final = $filtros['ano'].'-12-31';
        $conditions = array('data_evento BETWEEN ? AND ?' => array($data_inicial, $data_final));

        if(!empty($filtros['codigo_cliente'])){
            $conditions['OR'] = array(
                'codigo_transportador' => $filtros['codigo_cliente'],
                'codigo_embarcador' => $filtros['codigo_cliente'],
            );
        }
        if(!empty($filtros['codigo_seguradora'])){
            $conditions['codigo_seguradora'] = $filtros['codigo_seguradora'];
        }
        if(!empty($filtros['codigo_corretora'])){
            $conditions['codigo_corretora'] = $filtros['codigo_corretora'];
        }

        $valores = $this->find('all', array(
            'fields' => array(
                'SUBSTRING(CONVERT(VARCHAR, Sinistro.data_evento, 103), 4, 7) AS ano_mes',
                'SUM(Sinistro.valor_sinistrado) AS valor_sinistrado'
            ),
            'conditions' => $conditions,
            'group' => 'SUBSTRING(CONVERT(VARCHAR, Sinistro.data_evento, 103), 4, 7)'
        ));

        $meses = array();
        for($mes = 1; $mes <= 12; $mes++){
            $meses[] = array(
                'ano' => $filtros['ano'],
                'mes' => $mes,
                'valor_sinistrado' => null,
            );
        }
        foreach($valores as $valor){
            foreach($meses as $key => $mes){
                if($mes['mes'] == substr($valor[0]['ano_mes'], 0, 2)){
                    $meses[$key]['valor_sinistrado'] = $valor[0]['valor_sinistrado'];
                }
            }
        }

        return $meses;
    }

    public function listagem_estados($conditions) {
        $this->bindCompleto(FALSE);
        $fields = array('COUNT(Sinistro.codigo) AS qtd',
                        'Sinistro.estado AS descricao');
        $group = array('Sinistro.estado');
        $resultado = $this->find('all', compact('conditions', 'fields', 'group'));
        $this->unbindCompleto();
        return $resultado;
    }

    public function listagem_semanal($conditions) {
        $this->bindCompleto(FALSE);
        $fields = array('COUNT(*) AS qtd',
                        "(CASE DATEPART(DW,data_evento) 
                            WHEN 1 THEN 'Segunda' 
                            WHEN 2 THEN 'Terça' 
                            WHEN 3 THEN 'Quarta' 
                            WHEN 4 THEN 'Quinta' 
                            WHEN 5 THEN 'Sexta' 
                            WHEN 6 THEN 'Sabado' 
                            WHEN 7 THEN 'Domingo' 
                          END) AS descricao");
        $group = array('DATEPART(DW,data_evento)');
        $order = 'DATEPART(DW, data_evento)';
        $resultado = $this->find('all', compact('conditions', 'fields', 'group', 'order'));
        $this->unbindCompleto();
        return $resultado;
    }
    public function listagem_mensal($conditions) {
        $this->bindCompleto(FALSE);
        $fields = array('COUNT(*) AS qtd',
                        "(CASE MONTH(data_evento) 
                            WHEN 1 THEN 'Janeiro' 
                            WHEN 2 THEN 'Fevereiro' 
                            WHEN 3 THEN 'Março' 
                            WHEN 4 THEN 'Abril' 
                            WHEN 5 THEN 'Maio' 
                            WHEN 6 THEN 'Junho' 
                            WHEN 7 THEN 'Julho' 
                            WHEN 8 THEN 'Agosto' 
                            WHEN 9 THEN 'Setembro' 
                            WHEN 10 THEN 'Outubro' 
                            WHEN 11 THEN 'Novembro' 
                            WHEN 12 THEN 'Dezembro' 
                          END) AS descricao");
        $group = array('MONTH(data_evento)');
        $order = array('MONTH(data_evento)');
        $resultado = $this->find('all', compact('conditions', 'fields', 'group', 'order'));
        $this->unbindCompleto();
        return $resultado;
    }


}