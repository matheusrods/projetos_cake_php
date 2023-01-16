<?php
class EstatisticaSmCliente extends AppModel {
    var $name = 'EstatisticaSmCliente';
    var $useTable = false;
    var $tipo = null;
    
    /**
     * Método para consolidar os dados de SM por cliente na tabela
     * estatistica_sm_cliente_dia e estatistica_sm_cliente_hora
     * 
     * @param string $data_limite
     */
    function carregarConsolidado($data_limite = null) {
        $this->Behaviors->attach('EstatisticaSmPeriodo');
        
        $this->EstatisticaSm = ClassRegistry::init('EstatisticaSm');

        if (empty($data_limite)) {
            $data_limite = date('Y-m-d H:i:s');
        }

        $lista_periodo = $this->geraPeriodo($this->tipo, $data_limite);
        $periodo_inicial = isset($lista_periodo[0]) ? $lista_periodo[0] : null;

        $this->_removerPeriodoAnterior($periodo_inicial);
        
        foreach ($lista_periodo as $ultima_data) {
            $dado = array();
            $periodo = $this->EstatisticaSm->periodo($ultima_data, $this->tipo);

            $dados = $this->EstatisticaSm->consolidaPorCliente($periodo);
            if (!empty($dados)) {
                foreach ($dados as $dado) {
                    $dado = array($this->name => $dado);
                    $dado[$this->name]['data'] = $ultima_data;
                    $this->incluir($dado);
                }
            }
        }
        
        $this->incluirRegistroInicial($periodo_inicial);
        $this->Behaviors->detach('EstatisticaSmPeriodo');
    }

    function _removerPeriodoAnterior($periodo) {
        $mascara_retorno = ($this->tipo == EstatisticaSm::TIPO_HORA ? 'Ymd H:00:00' : 'Ymd 00:00:00');
        $data = date($mascara_retorno, strtotime($periodo));
        $this->query("
        	DELETE FROM
        	    {$this->databaseTable}.{$this->tableSchema}.{$this->useTable}
        	WHERE
        		data >= '{$data}'"
        );
    }
    
    /**
     * Método para obter lista consolidada de SM por cliente por período
     * 
     * @param array $dados
     */
    function listaPorPeriodo($periodo, $tipo_retorno = EstatisticaSm::TIPO_DIA, $dados = null) {
        $this->EstatisticaSm = ClassRegistry::init('EstatisticaSm');

        $mascara_retorno = ($tipo_retorno == EstatisticaSm::TIPO_HORA ? 'Ymd H:00:00' : 'Ymd 00:00:00');
        if (isset($dados[$this->EstatisticaSm->name]['data_inicio_fim']) && $dados[$this->EstatisticaSm->name]['data_inicio_fim']) {
            $periodo_inicio = date($mascara_retorno, strtotime($this->EstatisticaSm->converteDataParaDataPhp($periodo)));
            $periodo_fim = date($mascara_retorno, strtotime($this->EstatisticaSm->converteDataParaDataPhp($periodo)));
        } else {
            switch ($tipo_retorno) {
                case EstatisticaSm::TIPO_HORA:
                    $voltar_periodo = '-0 minutes';
                    break;
                case EstatisticaSm::TIPO_DIA:
                case EstatisticaSm::TIPO_MES:
                    $voltar_periodo = '-1 month';
                    break;
                case EstatisticaSm::TIPO_SEMANA:
                    $voltar_periodo = '-1 week';
                    break;
            }
            $periodo_fim = date($mascara_retorno, strtotime($this->EstatisticaSm->converteDataParaDataPhp($periodo)));
            $periodo_inicio = date($mascara_retorno, strtotime($voltar_periodo, strtotime($periodo_fim)));
        }
        
        $monitora_database = ($this->useDbConfig == 'test_suite' ? $this->databaseTable : 'Monitora');
        $monitora_schema = ($this->useDbConfig == 'test_suite' ? $this->tableSchema : 'dbo');

        $lista = $this->query("
                SELECT
                    CONVERT(VARCHAR, [{$this->name}].[data], 120) AS [data],
                    [Cliente].[raz_social] AS [raz_social],
                    CASE WHEN ([{$this->name}].[operadores] > 0 AND [{$this->name}].[em_andamento] > 0) THEN
                        (CONVERT(FLOAT, [{$this->name}].[em_andamento]) / CONVERT(FLOAT, [{$this->name}].[operadores])) 
                    ELSE 0 END as [em_andamento_por_operador],
                    [{$this->name}].[codigo],
                    [{$this->name}].[codigo_cliente],
                    ISNULL([{$this->name}].[operadores], 0) AS [operadores],
                    ISNULL([{$this->name}].[operacoes], 0) AS [operacoes],
                    ISNULL([{$this->name}].[em_aberto], 0) AS [em_aberto],
                    ISNULL([{$this->name}].[em_andamento], 0) AS [em_andamento],
                    ISNULL([{$this->name}].[ocorrencias], 0) AS [ocorrencias]
                FROM
                    [{$this->databaseTable}].[{$this->tableSchema}].[{$this->useTable}] AS [{$this->name}] 
                    LEFT JOIN [{$monitora_database}].[{$monitora_schema}].[Client_Empresas] AS [Cliente] 
                    ON [Cliente].[Codigo] = [{$this->name}].[codigo_cliente]
                WHERE [{$this->name}].[data] BETWEEN '{$periodo_inicio}' AND '{$periodo_fim}'
                ORDER BY
                    1 DESC, 2
        ");
        
        $retorno = & $lista;
        foreach ($retorno as &$dado) {
            $dado = $dado[0];
        }
        return $retorno;
    }
    
    
    
    
}