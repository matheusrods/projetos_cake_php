<?php
class EstatisticaSmOperacao extends AppModel {
    var $name = 'EstatisticaSmOperacao';
    var $useTable = false;
    var $tipo = null;
    
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
            $dados = $this->EstatisticaSm->consolidaOperacao($periodo);
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
        $mascara_retorno = ($this->tipo == EstatisticaSm::TIPO_HORA ? 'Y-m-d H:00:00' : 'Y-m-d 00:00:00');
        $this->deleteAll(array('data >=' => date($mascara_retorno, strtotime($periodo))));
    }
    
    function listaPorPeriodo($periodo) {
        $this->MClienteOperacao = ClassRegistry::init('MClienteOperacao');
        return $this->query("
            select convert(varchar, [matriz].[data],120) as data, [matriz].[cod_operacao] as [codigo_tipo_operacao], [matriz].[descricao] as [descricao_operacao], {$this->name}.[operadores], {$this->name}.[em_aberto], {$this->name}.[em_andamento], {$this->name}.[ocorrencias]  FROM  
            (
            	select distinct {$this->name}.[data], MClienteOperacao.cod_operacao, MClienteOperacao.DESCRICAO 
            	from {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} AS {$this->name} 
            	    cross join {$this->MClienteOperacao->databaseTable}.{$this->MClienteOperacao->tableSchema}.{$this->MClienteOperacao->useTable} [MClienteOperacao]
            	where {$this->name}.[data] between '{$periodo[0]}' and '{$periodo[1]}' 
            	    and MClienteOperacao.COD_OPERACAO in (select distinct codigo_tipo_operacao from {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} where [data] between '{$periodo[0]}' and '{$periodo[1]}')
            ) as matriz 
            left join {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} AS {$this->name}
                on ({$this->name}.codigo_tipo_operacao = matriz.COD_OPERACAO and {$this->name}.data = matriz.data)
            order by [matriz].[data], [matriz].[descricao]
        ");
    }
    
}