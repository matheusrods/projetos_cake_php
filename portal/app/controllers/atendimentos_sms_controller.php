<?php
class AtendimentosSmsController extends AppController {

	
	public $name = 'AtendimentosSms';
    public $helpers = array('Highcharts');
    public $uses = array('AtendimentoSm');
    
    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array(
            'verifica_acao',
            'autentica',
            'autenticar_usuario',
            'incluir',
        ));
    }

    function incluir() {
        $Usuario = classRegistry::init('Usuario');
        $this->HistoricoSm = classRegistry::init('HistoricoSm');
        $TEspaEventoSistemaPadrao = classRegistry::init('TEspaEventoSistemaPadrao');
        $Recebsm = classRegistry::init('Recebsm');
        $Rastreador = classRegistry::init('Rastreador');
        $PassoAtendimento = classRegistry::init('PassoAtendimento');
        $this->layout = 'sistema_externo';
        $tipos_eventos = $TEspaEventoSistemaPadrao->find('list', array('conditions' => array('not' => array('espa_descricao' => null, 'espa_tipo_ocorrencia' => 4)), 'order' => array('espa_descricao')));
        $codigo_sm = $this->passedArgs[0];
        $prioridades = array(
            1 => 'Baixa',
            2 => 'Média',
            3 => 'Alta',
        );
        $primeiro_passo_atendimento = $PassoAtendimento->find('first', array('conditions' => array('descricao' => 'Buonny Sat'), 'fields' => 'codigo'));
        $primeiro_passo_atendimento = $primeiro_passo_atendimento['PassoAtendimento']['codigo'];
        $this->set(compact('tipos_eventos', 'prioridades', 'codigo_sm'));
        
        if($this->Session->read('latitude') && $this->Session->read('longitude')) {
            $latitude = str_replace(",", ".", $this->Session->read('latitude'));
            $longitude = str_replace(",", ".", $this->Session->read('longitude'));
            $this->set(compact('latitude', 'longitude'));
        }
        
        if(!empty($this->data)) {
                unset($this->data['AtendimentoSm']['codigo']);
                $this->data['AtendimentoSm']['codigo_sm'] = $codigo_sm;
                if ($this->Session->read('Auth.Usuario.codigo')) {
                    $this->data['AtendimentoSm']['codigo_usuario'] = $this->Session->read('Auth.Usuario.codigo');
                }
                if ($this->Session->read('codigo_usuario_inclusao')) {
                    $this->data['AtendimentoSm']['codigo_usuario_inclusao'] = $this->Session->read('codigo_usuario_inclusao');
                } elseif($this->Session->read('codigo_usuario_inclusao_guardian')) {
                    $this->data['AtendimentoSm']['codigo_usuario_inclusao_guardian'] = $this->Session->read('codigo_usuario_inclusao_guardian');
                }
                $this->data['AtendimentoSm']['data_inicio'] = Date('d/m/Y H:i:s');
                $this->data['AtendimentoSm']['codigo_prioridade'] = $TEspaEventoSistemaPadrao->buscarCodigoPrioridade($this->data['AtendimentoSm']['codigo_tipo_evento']);
                if (empty($this->data['AtendimentoSm']['codigo_prioridade'])) {
                     $this->AtendimentoSm->invalidate('AtendimentoSm.codigo_tipo_evento','informe o evento');
                }

                if($this->AtendimentoSm->incluirEvento($this->data)) {
                    echo 'Dados do Atendimento salvos com sucesso';
                    die;
                } else {
                    $this->BSession->setFlash('save_error');
                }
        } else {
                $dados_sm = $Recebsm->BuscaDados($codigo_sm);
                $local = $Rastreador->buscaUltimaPosicao($dados_sm['Recebsm']['Placa']);
                $this->data['AtendimentoSm']['codigo_sm'] = $codigo_sm;
                $this->data['AtendimentoSm']['placa'] = $dados_sm['Recebsm']['Placa'];
                $this->data['AtendimentoSm']['empresa'] = $dados_sm['ClientEmpresa']['Raz_social'];
                $this->data['AtendimentoSm']['telefone_empresa'] = $dados_sm['ClientEmpresa']['Telefone'];
                $this->data['Equipamento']['Descricao'] = $dados_sm['Equipamento']['Descricao'];
                $this->data['AtendimentoSm']['codigo_tecnologia'] = $dados_sm['Equipamento']['Codigo'];
                $this->data['AtendimentoSm']['motorista'] = $dados_sm['Motorista']['Nome'];
                $this->data['AtendimentoSm']['telefone_motorista'] = $dados_sm['Motorista']['DDDTelefone'] . ' ' . $dados_sm['Motorista']['telefone'];
                $this->data['AtendimentoSm']['celular_motorista'] = $dados_sm['Motorista']['DDDcelular'] . ' ' . $dados_sm['Motorista']['celular'];
                $this->data['AtendimentoSm']['origem'] = $dados_sm['CidadeOrigem']['Descricao'];
                $this->data['AtendimentoSm']['destino'] = $dados_sm['CidadeDestino']['Descricao'];
                $this->data['AtendimentoSm']['local'] = $local;
                $this->data['AtendimentoSm']['codigo_passo_atendimento'] = $primeiro_passo_atendimento;
        }
    }
        
    function verifica_acao() {
        $this->autentica();
        if ($this->Session->read('codigo_usuario_inclusao_guardian') > 0 || $this->Session->read('codigo_usuario_inclusao') > 0) {
            $codigo_sm = $this->Session->read('codigo_sm');
            $this->redirect(array('action' => 'incluir', $codigo_sm));
        }
    }
    
    function autentica() {
        if (($this->Session->read('codigo_usuario_inclusao_guardian') == null && $this->Session->read('codigo_usuario_inclusao') == null) || (in_array($this->action, array('index', 'verifica_acao')) && isset($this->passedArgs[0]))) {
            if (isset($this->passedArgs[0]) && $this->passedArgs[0] == 'vlKbjDf') {
                $hash = $this->passedArgs[0];
                if(isset($this->params['named']['guardian'])) {
                    $codigo_usuario_inclusao_guardian = $this->params['named']['guardian'];
                    $this->Session->write('codigo_usuario_inclusao_guardian', $codigo_usuario_inclusao_guardian);
                    $this->Session->write('codigo_sm', $this->passedArgs[1]);
                } else {
                    $codigo_usuario_inclusao = $this->passedArgs[1];
                    $this->Session->write('codigo_usuario_inclusao', $codigo_usuario_inclusao);
                    $this->Session->write('codigo_sm', $this->passedArgs[2]);
                }
                if(isset($this->params['named']['latitude']) && isset($this->params['named']['longitude'])) {
                    $this->Session->write('latitude', $this->params['named']['latitude']);
                    $this->Session->write('longitude', $this->params['named']['longitude']);
                }
            }
        }
    }
    
    function autenticar_usuario($dados_usuario) {
        $Usuario = classRegistry::init('Usuario');
        $usuario = $this->BAuth->auth($dados_usuario['apelido'], $dados_usuario['senha']);
        if (empty($usuario)) {
            $Usuario->invalidate('apelido', 'login ou senha inválidos');
            $Usuario->invalidate('senha', '');
            return false;
        }
        return $usuario;
    }
    
    function atendimentos() {
        $Equipamento = classRegistry::init('Equipamento');
        $OperacaoMonitora = classRegistry::init('OperacaoMonitora');
        $PassoAtendimento = classRegistry::init('PassoAtendimento');
        $Uperfil = classRegistry::init('Uperfil');
        
        $this->pageTitle = 'Atendimentos';  
        $tecnologias = $Equipamento->find('list', array('order' => 'descricao'));
        //$operacoes = $OperacaoMonitora->listaOperacoes();
        $passos_atendimentos = $PassoAtendimento->find('list');

        $usuario = $this->BAuth->user();
        $admin = '';
        $buonnysat = array_search('Buonny Sat', $passos_atendimentos);
        $pronta_resposta = array_search('Pronta Resposta', $passos_atendimentos);

		if ($this->BAuth->temPermissao($usuario['Usuario']['codigo_uperfil'], 'obj_admin-atendimentos')) {
            $this->set(compact('admin'));
        } elseif ($this->BAuth->temPermissao($usuario['Usuario']['codigo_uperfil'], 'obj_operador-pronta-resposta')) {
			$this->set(compact('pronta_resposta'));
		} elseif ($this->BAuth->temPermissao($usuario['Usuario']['codigo_uperfil'], 'obj_acionamento-buonnysat')) {
			$this->set(compact('buonnysat'));
        }
		if(!empty($usuario['Usuario']['codigo_cliente']))
            $this->data['AtendimentoSm']['codigo_cliente'] = $usuario['Usuario']['codigo_cliente'];

      
        $this->set(compact('tecnologias', 'operacoes', 'passos_atendimentos'));
        $this->data['AtendimentoSm'] = $this->Filtros->controla_sessao($this->data, $this->AtendimentoSm->name);
    }

    function listagem() {
		$this->loadModel('PassoAtendimentoSm');
		$this->loadModel('Recebsm');
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->AtendimentoSm->name);
        unset($filtros['data_inicial']);
        unset($filtros['data_final']);
        if (empty($filtros['status_atendimento'])) {
            $filtros['status_atendimento'][] = 1;
        }
        $conditions = $this->AtendimentoSm->converteFiltroEmCondition($filtros);
        $this->paginate['AtendimentoSm'] = array('conditions' => $conditions);
        $atendimentos = $this->paginate('AtendimentoSm');
		$qtd_buonny_sat = 0;
        $qtd_pronta_resposta = 0;

        if(isset($conditions['PassoAtendimentoSm.codigo_passo_atendimento'])){
            if($conditions['PassoAtendimentoSm.codigo_passo_atendimento'] == 1){
                $qtd_buonny_sat = $this->AtendimentoSm->paginateCount($conditions);
            }elseif($conditions['PassoAtendimentoSm.codigo_passo_atendimento'] == 2){
                $qtd_pronta_resposta = $this->AtendimentoSm->paginateCount($conditions);
            }
        }else{
            $conditions['PassoAtendimentoSm.codigo_passo_atendimento'] = 1;
            $qtd_buonny_sat = $this->AtendimentoSm->paginateCount($conditions);
            $conditions['PassoAtendimentoSm.codigo_passo_atendimento'] = 2;
            $qtd_pronta_resposta = $this->AtendimentoSm->paginateCount($conditions);
        }

        $this->set(compact('qtd_buonny_sat', 'qtd_pronta_resposta'));
        if(!empty($filtros)) {
            $this->set('atendimentos', $atendimentos);
        }
    
    }

    function pre_filtro_atendimentos_consulta() {
        $this->Filtros->limpa_sessao('AtendimentoSm');
        $this->Filtros->controla_sessao($this->data, 'AtendimentoSm');
        $this->redirect('atendimentos_consulta');
    }
    
    function atendimentos_consulta() {
        $Equipamento = classRegistry::init('Equipamento');
        $OperacaoMonitora = classRegistry::init('OperacaoMonitora');
        $PassoAtendimento = classRegistry::init('PassoAtendimento');        
        $this->pageTitle = 'Consulta de Atendimentos';
        $tecnologias = $Equipamento->find('list', array('order' => 'descricao'));
        //$operacoes = $OperacaoMonitora->listaOperacoes();
        $usuario = $this->BAuth->user();
        $passos_atendimentos = $PassoAtendimento->find('list');
        if(!empty($usuario['Usuario']['codigo_cliente']))
            $this->data['AtendimentoSm']['codigo_cliente'] = $usuario['Usuario']['codigo_cliente'];
        
        if(empty($this->data['AtendimentoSm']['data_inicial'])){
            $this->data['AtendimentoSm']['data_inicial'] = date('d/m/Y');
        }
        if(empty($this->data['AtendimentoSm']['data_final'])){
            $this->data['AtendimentoSm']['data_final'] = date('d/m/Y');
        }
        $this->set(compact('tecnologias', 'operacoes', 'passos_atendimentos'));
        $this->data['AtendimentoSm'] = $this->Filtros->controla_sessao($this->data, 'AtendimentoSm');
    }
    
    function listagem_consulta($export = false) {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
		$this->loadModel('PassoAtendimentoSm');
		$this->loadModel('Recebsm');
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'AtendimentoSm');
        $conditions = $this->AtendimentoSm->converteFiltroEmCondition($filtros, true);
        $limit = 30;
        
        if($export){
            $this->paginate['AtendimentoSm'] = array(
                'conditions' => $conditions,
                'order' => NULL, 
                'limit' => 999999, 
                'page' => 1,
                'recursive' => 1, 
                'export' => true,
                'fields' => NULL,
            );
            $query = $this->paginate('AtendimentoSm');
            $this->exportListagemConsulta($query);
        }
        $this->paginate['AtendimentoSm'] = array(
            'conditions' => $conditions,
            'fields' => NULL, 
            'order' => NULL, 
            'limit' => $limit,          
            'page' => 1, 
            'recursive' => 1, 
            'export' => false,
        );

        $atendimentos = $this->paginate('AtendimentoSm');

        $qtd_buonny_sat = 0;
        $qtd_pronta_resposta = 0;

        if(isset($conditions['PassoAtendimentoSm.codigo_passo_atendimento'])){
            if($conditions['PassoAtendimentoSm.codigo_passo_atendimento'] == 1){
                $qtd_buonny_sat = $this->AtendimentoSm->paginateCount($conditions);
            }elseif($conditions['PassoAtendimentoSm.codigo_passo_atendimento'] == 2){
                $qtd_pronta_resposta = $this->AtendimentoSm->paginateCount($conditions);
            }
        }else{
            $conditions['PassoAtendimentoSm.codigo_passo_atendimento'] = 1;
            $qtd_buonny_sat = $this->AtendimentoSm->paginateCount($conditions);
            $conditions['PassoAtendimentoSm.codigo_passo_atendimento'] = 2;
            $qtd_pronta_resposta = $this->AtendimentoSm->paginateCount($conditions);
        }

  		$this->set(compact('qtd_buonny_sat', 'qtd_pronta_resposta'));
        if(!empty($filtros)) {
            $this->set('atendimentos', $atendimentos);
        }
    }

    private function exportListagemConsulta($query) {
        $this->loadModel("PassoAtendimentoSm");
        $dbo = $this->AtendimentoSm->getDataSource();
        $dbo->results = $dbo->_execute($query);
        header('Content-type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment; filename="%s"', basename('consulta_de_atendimentos.csv')));
        header('Pragma: no-cache');
        echo iconv('UTF-8', 'ISO-8859-1', '"SM";"Data Início";"Data Análise";"Data Fim";"Operação";"Empresa";"Tipo Evento";"Status";"Pronta Resposta";')."\n";

        while ($dado = $dbo->fetchRow()) { 
            $status = $dado[0]['status'] = !empty($dado[0]['data_fim']) ? 'Finalizado': (!empty($dado[0]['data_encaminhado']) ? 'Encaminhado':(!empty($dado[0]['data_analise']) ? 'Em analise': 'Iniciado'));
            $pronta_resposta = ($dado[0]['pronta_resposta']) ? 'Sim' : 'Nao';
            $linha = '"'.$dado[0]['codigo_sm'].'";';
            $linha .= '"'.AppModel::dbDatetoDate($dado[0]['data_inicio_atendimento_sm']).'";';
            $linha .= '"'.AppModel::dbDatetoDate($dado[0]['data_analise_atendimento_sm']).'";';
            $linha .= '"'.AppModel::dbDatetoDate($dado[0]['data_fim_atendimento_sm']).'";';
            $linha .= '"'.Comum::trata_nome($dado[0]['descricao']).'";';
            $linha .= '"'.Comum::trata_nome($dado[0]['Raz_social']).'";'; 
            $linha .= '"'.Comum::trata_nome($dado[0]['espa_descricao']).'";'; 
            $linha .= '"'.$status.'";';
            $linha .= '"'.$pronta_resposta.'";';
            $linha .= "\n";
            echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
        }
        die();  
    }

    function status_sla() {
        $this->pageTitle = 'SLA Atendimentos';
        $dados = null;
        if (!empty($this->data)) {
            $dados = $this->carrega_series_sla();
            if (empty($dados))
                $this->BSession->setFlash('no_data');
        }
        $this->set(compact('dados'));
    }

    function carrega_series_sla() {
        $tempo_sla = 30;
        $dados = $this->AtendimentoSm->statusSLAPorSetor($tempo_sla, $this->data['AtendimentoSm']['tipo']);
        if ($dados) {
            $pre_series = array();
            $eixo_x = array("'Tipo'");
            $dado = current($dados);
            $series[] = array('name' => "'Sem Análise Dentro do SLA'", 'values' => $dado[0]['dentro']);
            $series[] = array('name' => "'Sem Análise Fora do SLA'", 'values' => $dado[0]['fora']);
            $dado = next($dados);
            $series[] = array('name' => "'Em Análise Dentro do SLA'", 'values' => $dado[0]['dentro']);
            $series[] = array('name' => "'Em Análise Fora do SLA'", 'values' => $dado[0]['fora']);
            return array('eixo_x' => $eixo_x, 'series' => $series);
        }
    }
    
}