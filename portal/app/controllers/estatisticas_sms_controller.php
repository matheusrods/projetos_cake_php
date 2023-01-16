<?php
class EstatisticasSmsController extends AppController {
    public $name = 'EstatisticasSms';
    public $helpers = array('Highcharts');
    var $uses = array('EstatisticaSm', 'MClienteOperacao');
    
    function por_operador() {
        $this->pageTitle = 'Estatísticas por Operador';
        $lista = null;
        if (!empty($this->data)) {          
            if ($this->data['EstatisticaSm']['data'] == null) {
                $this->EstatisticaSm->invalidate('data', 'É necessário informar uma data');
            } else {
                $lista = $this->EstatisticaSm->carregaPorOperador($this->data);
                if (count($lista) > 0) {
                    $this->set(compact('lista'));
                } else {
                    $this->BSession->setFlash('no_data');
                }
            }
        } else {
            $this->data = array('EstatisticaSm' => array('data' => Date('d/m/Y')));
        }
        $tipos = $this->EstatisticaSm->tipos();
        $operacoes = $this->MClienteOperacao->find('list', array('order' => 'descricao'));
        $this->set(compact('tipos', 'lista', 'operacoes'));
    }
    
    function por_operacao() {
        $this->pageTitle = 'Estatísticas por Operação';
        $this->EstatisticaSm = ClassRegistry::init('EstatisticaSm');
        if (!empty($this->data)) {
            if ($this->data['EstatisticaSm']['data'] == null) {
                $this->EstatisticaSm->invalidate('data', 'É necessário informar uma data');
            } else {
                $data = $this->data;
                $lista = $this->EstatisticaSm->carregaPorOperacao($data);
                $dados = $this->EstatisticaSm->carregaLista($data);
                $eixo_x = $this->_estatistica_sm_operador_eixo_x($dados, $data['EstatisticaSm']['tipo']);
                if ($eixo_x) {
                    $series = $this->_estatistica_sm_operador_series($dados);
                    $this->set(compact('eixo_x', 'series', 'lista'));
                } else {
                    $this->BSession->setFlash('no_data');
                }
            }
        } else {
            $this->data = array('EstatisticaSm' => array('data' => Date('d/m/Y'), 'tipo_grafico' => 0));
        }
        $tipos = $this->EstatisticaSm->tipos();
        $this->set(compact('tipos', 'eixo_x'));
    }
    
    private function _estatistica_sm_operador_eixo_x($lista, $estatistica_sm_tipo) {
        $eixo_x = array();
        foreach ($lista as $tempo => $operacoes) {
            if (count($operacoes)>0)
                $eixo_x[] = "'".substr($tempo,0,5).(in_array($estatistica_sm_tipo, array(EstatisticaSm::TIPO_HORA, EstatisticaSm::TIPO_DIA))  ?  ' - '.substr($tempo,11,2)."h'" : "'");
        }
        return $eixo_x;
    }
    
    function _estatistica_sm_operador_series($lista) {
        $pre_series_em_andamento_por_operador = array();
        $pre_series_em_andamento = array();
        foreach ($lista as $tempo => $operacoes) {
            foreach ($operacoes as $operacao) {
                if ($operacao['codigo_tipo_operacao']) {
                    $pre_series_em_andamento_por_operador[$operacao['descricao_operacao']][] = round($operacao['em_andamento_por_operador'],2);
                    $pre_series_em_andamento[$operacao['descricao_operacao']][] = round($operacao['em_andamento'],2);
                }
            }
        }
        $series_em_andamento_por_operador = array();
        foreach ($pre_series_em_andamento_por_operador as $key => $serie){
            $series_em_andamento_por_operador[] = array('name' => "'".$key."'", 'values' => $serie);
        }
        $series_em_andamento = array();
        foreach ($pre_series_em_andamento as $key => $serie){
            $series_em_andamento[] = array('name' => "'".$key."'", 'values' => $serie);
        }
        return array('em_andamento_por_operador' => $series_em_andamento_por_operador, 'em_andamento' => $series_em_andamento);
    }
    
    function geral() {
        $this->pageTitle = 'Estatísticas Geral';
        $this->EstatisticaSm = ClassRegistry::init('EstatisticaSm');
        if (!empty($this->data)) {
            if ($this->data['EstatisticaSm']['tipo'] != EstatisticaSm::TIPO_HORA && $this->data['EstatisticaSm']['data'] == null) {
                $this->EstatisticaSm->invalidate('data', 'É necessário informar uma data');
            } else {
                $data = $this->data;
                $lista = $this->EstatisticaSm->carregaTotalLista($data);
                if ($lista) {
                    $eixo_x = $this->_estatistica_sm_operador_eixo_x($lista, $data['EstatisticaSm']['tipo']);
                    $series = $this->_estatistica_geral_series($lista);
                    if ($this->data['EstatisticaSm']['tipo'] == EstatisticaSm::TIPO_DIA) {
                        $data['EstatisticaSm']['tipo'] = EstatisticaSm::TIPO_SEMANA;
                        $lista = $this->EstatisticaSm->carregaTotalLista($data);
                    }
                    $this->set(compact('eixo_x', 'series', 'lista'));
                } else {
                    $this->BSession->setFlash('no_data');
                }
            }
        } else {
            $this->data = array('EstatisticaSm' => array('data' => Date('d/m/Y')));
        }
        $tipos = $this->EstatisticaSm->tipos();
        $this->set(compact('tipos', 'eixo_x'));
    }
    
    function _estatistica_geral_series($lista) {
        $pre_series = array();
        foreach ($lista as $tempo => $operacoes) {
            foreach ($operacoes[0] as $key => $operacao) {
                if ($key != 'operacoes')
                    $pre_series[$this->_estatistica_geral_converte_key_nome($key)][] = round($operacao,2);
            }
        }
        $series = array();
        foreach ($pre_series as $key => $serie){
            $series[] = array('name' => "'".$key."'", 'values' => $serie);
        }
        return $series;
    }
    
    function _estatistica_geral_converte_key_nome($key) {
        if ($key == 'em_aberto')
            return 'SMs em aberto';
        elseif ($key == 'em_andamento')
            return 'SMs Monitoradas';
        elseif ($key == 'ocorrencias')
            return 'Ocorrências';
        elseif ($key == 'operadores')
            return 'Operadores';
        elseif ($key == 'em_andamento_por_operador')
            return 'Monitoradas Por Operador';
    }
    
    function por_cliente() {
        $this->pageTitle = 'Estatísticas por Cliente';
        $lista = null;
        if (!empty($this->data)) {
            if ($this->data['EstatisticaSm']['data'] == null) {
                $this->EstatisticaSm->invalidate('data', 'É necessário informar uma data');
            } else {
                set_time_limit(0);
                $lista = $this->EstatisticaSm->carregaPorCliente($this->data);
                if (count($lista) > 0) {
                    $this->set(compact('lista'));
                } else {
                    $this->BSession->setFlash('no_data');
                }
            }
        } else {
            $this->data = array('EstatisticaSm' => array('data' => Date('d/m/Y')));
        }
        $tipos = $this->EstatisticaSm->tipos();
        $this->set(compact('tipos', 'lista'));
    }
}