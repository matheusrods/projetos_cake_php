<?php
class CronogramaGestaoPpra extends AppModel {

	var $name = 'CronogramaGestaoPpra';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cronogramas_gestao_ppra';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	const STATUS_CONCLUIDO = 0;
	const STATUS_CANCELADO = 1;

	var $validate = array(
		'codigo_prevencao_risco_ambiental' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Codigo do Cronograma de Ação!',
		),
	);

	public function concluir(array $data){
	    try{
	        $transformed_data = array(
	            'codigo_prevencao_risco_ambiental' => $data['codigo_prevencao_risco_ambiental'],
                'status' => self::STATUS_CONCLUIDO,
                'data_conclusao' => self::dateToDbDate($data['data_conclusao']),
                'motivo_cancelamento' => null,
            );

	        if(!empty($data['codigo']))
	            $transformed_data['codigo'] = $data['codigo'];

	        $conditions = array('CronogramaGestaoPpra.codigo_prevencao_risco_ambiental' => $data['codigo_prevencao_risco_ambiental']);
	        $row = $this->find('first', compact('conditions'));
	        if(empty($row) || is_null($row) || count($row) <= 0){//cadastrar
                $return = $this->incluir($transformed_data);
            }else{//atualizar
                $return = $this->atualizar($transformed_data);
            }
            return array(
                'status' => 'success',
                'message' => 'Cronograma PPRA concluido com sucesso!',
                'data' => $return,
            );
        }catch(Exception $ex){
            return array(
                'status' => 'error',
                'message' => 'ERROR - Erro ao tentar submeter conclusao do cronograma PPRA: ' . $ex->getMessage(),
            );
        }
    }

    public function cancelar(array $data){
        try{
            $transformed_data = array(
                'codigo_prevencao_risco_ambiental' => $data['codigo_prevencao_risco_ambiental'],
                'status' => self::STATUS_CANCELADO,
                'data_conclusao' => null,
                'motivo_cancelamento' => $data['motivo_cancelamento'],
            );

            if(!empty($data['codigo']))
                $transformed_data['codigo'] = $data['codigo'];

            $conditions = array('CronogramaGestaoPpra.codigo_prevencao_risco_ambiental' => $data['codigo_prevencao_risco_ambiental']);
            $row = $this->find('first', compact('conditions'));
            if(empty($row) || is_null($row) || count($row) <= 0){//cadastrar
                $return = $this->incluir($transformed_data);
            }else{//atualizar
                $return = $this->atualizar($transformed_data);
            }
            return array(
                'status' => 'success',
                'message' => 'Cronograma PPRA cancelado com sucesso!',
                'data' => $return,
            );
        }catch(Exception $ex){
            return array(
                'status' => 'error',
                'message' => 'ERROR - Erro ao tentar submeter cancelamento do cronograma PPRA: ' . $ex->getMessage(),
            );
        }
    }

    public function converte_filtro_em_conditions_listagem(array $data = array()){
	    $conditions = array();

	    if(isset($data['codigo_cliente']) && $data['codigo_cliente'] != '') {
	        $conditions[] = " Gpra.codigo_cliente IN (select codigo_cliente from grupos_economicos_clientes where codigo_grupo_economico = (select codigo_grupo_economico from grupos_economicos_clientes where codigo_cliente = " . $data['codigo_cliente'] . "))";
        }
            
        if(isset($data['codigo_cliente_alocacao']) && $data['codigo_cliente_alocacao'] != '')
            $conditions['Gpra.codigo_cliente'] = $data['codigo_cliente_alocacao'];
        if(isset($data['codigo_setor']) && $data['codigo_setor'] != '')
            $conditions['PrevencaoRiscoAmbiental.codigo_setor'] = $data['codigo_setor'];
        if(isset($data['codigo_tipo_acao']) && $data['codigo_tipo_acao'] != '')
            $conditions['PrevencaoRiscoAmbiental.codigo_tipo_acao'] = $data['codigo_tipo_acao'];
        if(isset($data['status']) && $data['status'] != ''){
            if($data['status'] == 'NULL'){
                $conditions[] = 'CronogramaGestaoPpra.status IS NULL';
            }else{
                $conditions['CronogramaGestaoPpra.status'] = $data['status'];
            }
        }
        if(isset($data['data_inicial']) && $data['data_inicial'] != ''){
            $data_inicial = AppModel::dateToDbDate($data['data_inicial']);
            $conditions['PrevencaoRiscoAmbiental.data_inicial >= '] = $data_inicial;
        }
        if(isset($data['data_final']) && $data['data_final'] != ''){
            $data_final = AppModel::dateToDbDate($data['data_final']);
            $conditions['PrevencaoRiscoAmbiental.data_inicial <= '] = $data_final;
        }
	    $conditions['TipoAcao.codigo_empresa'] = (empty($data['codigo_empresa']) ? $_SESSION['Auth']['Usuario']['codigo_empresa'] : $data['codigo_empresa']);

	    return $conditions;
    }

    public function get_parametros_para_consulta(array $data = array()){
        $fields = array(
            'PrevencaoRiscoAmbiental.codigo as codigo',
            //'PrevencaoRiscoAmbiental.codigo_cliente_matriz as codigo_cliente_matriz',
            'Gpra.codigo_cliente as codigo_cliente_unidade',
            'ClienteUnidade.nome_fantasia as nome_fantasia',
            'Setor.descricao as setor',
            'TipoAcao.descricao as tipo_acao',
            'CASE ' .
                'WHEN CronogramaGestaoPpra.status IS NULL THEN \'PENDENTE\' ' .
                'WHEN CronogramaGestaoPpra.status = 0 THEN \'CONCLUIDO\' ' .
                'WHEN CronogramaGestaoPpra.status = 1 THEN \'CANCELADO\' ' .
            'ELSE \'DESCONHECIDO\' ' .
            'END as status',
            'CronogramaGestaoPpra.data_conclusao as data_conclusao',
            'CronogramaGestaoPpra.motivo_cancelamento as motivo_cancelamento',
            'CronogramaGestaoPpra.data_inclusao as data_inclusao'
        );
        $joins = array(
            /*array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'ClienteMatriz',
                'type' => 'INNER',
                'conditions' => 'CronogramaAcao.codigo_cliente_matriz = ClienteMatriz.codigo',
            ),*/
            array(
                'table' => 'Rhhealth.dbo.grupos_prevencao_riscos_ambientais',
                'alias' => 'Gpra',
                'type' => 'INNER',
                'conditions' => 'PrevencaoRiscoAmbiental.codigo_grupo_prevencao_risco_ambiental = Gpra.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'ClienteUnidade',
                'type' => 'INNER',
                'conditions' => 'Gpra.codigo_cliente = ClienteUnidade.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.setores',
                'alias' => 'Setor',
                'type' => 'INNER',
                'conditions' => 'PrevencaoRiscoAmbiental.codigo_setor = Setor.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.tipos_acoes',
                'alias' => 'TipoAcao',
                'type' => 'LEFT',
                'conditions' => 'PrevencaoRiscoAmbiental.codigo_tipo_acao = TipoAcao.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.cronogramas_gestao_ppra',
                'alias' => 'CronogramaGestaoPpra',
                'type' => 'LEFT',
                'conditions' => 'CronogramaGestaoPpra.codigo_prevencao_risco_ambiental = PrevencaoRiscoAmbiental.codigo',
            ),
        );
        $conditions = self::converte_filtro_em_conditions_listagem($data);
        $limit = 50;
        $order = 'ClienteUnidade.nome_fantasia ASC, Setor.descricao ASC';
        $recursive = -1;

        return compact('fields', 'joins', 'conditions', 'limit', 'order', 'recursive');
    }

}