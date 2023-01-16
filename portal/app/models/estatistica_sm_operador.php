<?php
class EstatisticaSmOperador extends AppModel {
    var $name = 'EstatisticaSmOperador';
    var $useTable = false;
    var $tipo = null;
    const STATUS_EM_ABERTO = 1;
    const STATUS_MONITORADAS = 2;
    
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

            $dados = $this->EstatisticaSm->consolidaPorOperador(0, $periodo);
            if (!empty($dados)) {
                foreach ($dados as $dado) {
                    $dado = array($this->name => $dado);
                    $dado[$this->name]['data'] = $ultima_data;
                    unset($dado[$this->name]['operador']);
                    $this->incluir($dado);
                }
                
            }
        }
        
        $this->incluirRegistroInicial($periodo_inicial);
        $this->Behaviors->detach('EstatisticaSmPeriodo');
    }
    
    function _removerPeriodoAnterior($periodo) {
        $mascara_retorno = ($this->tipo == EstatisticaSm::TIPO_HORA ? 'Y-m-d H:00:00' : 'Y-m-d 00:00:00');
        $this->deleteAll(array('data >=' => date($mascara_retorno, strtotime($periodo))));
    }
    
    /**
     * Método para obter lista consolidada de SM por operador por período
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
                    $voltar_periodo = '-0 hour';
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

        $this->bindModel(array('belongsTo' => array(
            'Funcionario' => array('foreignKey' => 'codigo_operador'),
        )));
        
        $conditions = array($this->name.'.data BETWEEN ? AND ?' => array($periodo_inicio, $periodo_fim));
        if (isset($dados[$this->EstatisticaSm->name]['codigo_tipo_operacao']) && !empty($dados[$this->EstatisticaSm->name]['codigo_tipo_operacao']))
            $conditions[$this->name.'.codigo_tipo_operacao'] = $dados[$this->EstatisticaSm->name]['codigo_tipo_operacao'];
        if (isset($dados[$this->EstatisticaSm->name]['status']) && !empty($dados[$this->EstatisticaSm->name]['status'])) {
            if ($dados[$this->EstatisticaSm->name]['status'] == self::STATUS_MONITORADAS)
                $conditions[$this->name.'.em_andamento >'] = '0';
        }


        $group = array('Funcionario.Nome', 'Funcionario.Funcao', 'Funcionario.Logado', $this->name.'.codigo_operador');
        $fields = array(
            'Funcionario.Nome as operador', 
            'Funcionario.Funcao as funcao',
            'Funcionario.Logado as logado',
            $this->name.'.codigo_operador as codigo_operador',
            "count(distinct [{$this->name}].[operacoes]) AS operacoes",
            "sum(isnull({$this->name}.em_aberto, 0)) AS em_aberto",
            "sum(isnull({$this->name}.em_andamento, 0)) AS em_andamento",
            "sum(isnull({$this->name}.ocorrencias, 0)) AS ocorrencias",
        );
        $retorno = $this->find('all', array('conditions' => $conditions, 'fields' => $fields, 'group' => $group));
        foreach ($retorno as $key => $dado)
            $retorno[$key] = $dado[0];
        return $retorno;
    }
       
}