<?php
class VMonitoramento extends AppModel {
    var $name = 'VMonitoramento';
    var $useDbConfig = 'dbtrafegus';
    var $tableSchema = 'public';
    var $databaseTable = 'trafegus';
    var $useTable = 'view_monitoramento';
    
    
    function buscarUltimaAlteracao() {
        $VVeiculoComViagem = classRegistry::init('VVeiculoComViagem');
        $atualizacao = $this->find('all', array(
            'fields' => array('VMonitoramento.term_ctec_codigo', 'VMonitoramento.ctec_descricao', 'VMonitoramento.inclusao', 'VMonitoramento.total', 'COUNT(VVeiculoComViagem.tecn_descricao) AS monitorados'),
            'joins' =>  array(
                array(
                    'table' => "{$VVeiculoComViagem->databaseTable}.{$VVeiculoComViagem->tableSchema}.{$VVeiculoComViagem->useTable}",
                    'alias' => 'VVeiculoComViagem',
                    'type' => 'LEFT',
                    'conditions' => array('VVeiculoComViagem.tecn_descricao = VMonitoramento.tecn_descricao')
                )
            ),
            'group' =>  array('VMonitoramento.term_ctec_codigo', 'VMonitoramento.ctec_descricao', 'VMonitoramento.inclusao', 'VMonitoramento.total'),
            'order' =>  array('VMonitoramento.ctec_descricao'),
        ));

        $status = array();
        $retorno = array();
        $status = Set::extract('{n}.VMonitoramento', $atualizacao);
        foreach ($status as $chave => &$valor) {
            $valor['monitorados'] = $atualizacao[$chave][0]['monitorados'];
        }
        for ($i = 0; $i < count($status); $i++) {
            $tecnologia = $status[$i]['ctec_descricao'];
            $ultima_atualizacao = $status[$i]['inclusao'];
            $retorno[$i]['StatusTecnologia']['veiculos_monitorados'] = $status[$i]['monitorados'] != 0 ? $status[$i]['monitorados']: 0;
            $retorno[$i]['StatusTecnologia']['veiculos_total'] = $status[$i]['total'] != 0 ? $status[$i]['total']: 0;
            $retorno[$i]['StatusTecnologia']['ultima_atualizacao'] = $ultima_atualizacao;
            $retorno[$i]['StatusTecnologia']['tecnologia'] = $tecnologia;
            $retorno[$i]['StatusTecnologia']['atualizacoes'] = $this->totalAlteracoes($status[$i]['term_ctec_codigo']);
        }
        return $retorno;
    }
      
    function totalAlteracoes($term_ctec_codigo) {
        $this->setSource('upos_ultima_posicao');
        $this->useTable = 'upos_ultima_posicao';
        $options = array(
            'fields' => array(
                'TTermTerminal.term_ctec_codigo',
                'COUNT(VMonitoramento.upos_data_comp_bordo) AS bordo',
            ),
            'joins' => array(
                array(
                    'table' => 'trafegus.public.term_terminal',
                    'alias' => 'TTermTerminal',
                    'conditions' => 'TTermTerminal.term_numero_terminal = VMonitoramento.upos_term_numero_terminal'
                ),
            ),
            'conditions' => array(
                'TTermTerminal.term_ctec_codigo' => $term_ctec_codigo,
                'VMonitoramento.upos_data_comp_bordo BETWEEN ? AND ?' => array(date('Ymd H:i', strtotime('-5 minutes')),date('Ymd H:i'))
            ),
            'group' => array(
                'TTermTerminal.term_ctec_codigo'
            ),
        );
        $count = $this->find('all', $options);
        return isset($count[0][0]) ? $count = $count[0][0]['bordo']: 0;
    }
    
}