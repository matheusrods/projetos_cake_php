<?php
class HistoricosSmsController extends AppController {
    public $name = 'HistoricosSms';
    public $helpers = array('Highcharts');
    public $uses = array('HistoricoSm');
    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array(
            'calcula_distancia_prestador'
        ));
    }
    function adicionar_historico($codigo_sm, $codigo_passo_atendimento_sm, $codigo_atendimento, $bloquear_campos = false, $new_window = false) {
        if($new_window){
            $this->layout = 'new_window';
        }
        $this->pageTitle = 'Adicionar Histórico à SM';
        $this->loadModel('TViagViagem');
        $Recebsm                  = classRegistry::init('Recebsm');
        $TUsuaUsuario             = classRegistry::init('TUsuaUsuario');
        $AtendimentoSm            = classRegistry::init('AtendimentoSm');
        $PassoAtendimento         = classRegistry::init('PassoAtendimento');
        $PassoAtendimentoSm       = classRegistry::init('PassoAtendimentoSm');
        $Prestador                = classRegistry::init('Prestador');
        $TEspaEventoSistemaPadrao = classRegistry::init('TEspaEventoSistemaPadrao');
        $TUposUltimaPosicao       = ClassRegistry::init('TUposUltimaPosicao');
        $HistoricoSmPrestador     = ClassRegistry::init('HistoricoSmPrestador');
        $Usuario                  = classRegistry::init('Usuario');
        
        $dados_atendimento_sm = $AtendimentoSm->find('first', array(
            'conditions' => array('codigo' => $codigo_atendimento),
            'group' => array('codigo', 'codigo_prioridade', 'data_inicio', 'data_analise', 'data_fim', 'codigo_usuario_inclusao', 'codigo_usuario_inclusao_guardian'),
            'fields' => array(
                    'MAX(codigo) as codigo', 
                    'codigo_prioridade', 
                    'data_inicio', 
                    'data_analise', 
                    'data_fim', 
                    'codigo_usuario_inclusao', 
                    'codigo_usuario_inclusao_guardian'
                )
        ));
        $codigo_do_atendimento = $dados_atendimento_sm[0];
        unset($dados_atendimento_sm[0]);
        
        $dados_sm = $Recebsm->buscaDados($codigo_sm);        
        $this->TViagViagem->bindTTermPrincipal();
        $this->TViagViagem->bindModel(array(
            'hasOne' => array(
                'TVtecVersaoTecnologia' => array('foreignKey' => FALSE, 'conditions' => "term_vtec_codigo = vtec_codigo"),
                'TTecnTecnologia' => array('foreignKey' => FALSE, 'conditions' => 'vtec_tecn_codigo = tecn_codigo'),
                'TUposUltimaPosicao' => array('foreignKey' => FALSE, 'conditions' => 'upos_term_numero_terminal = term_numero_terminal AND upos_vtec_codigo = term_vtec_codigo')
            ),
        ));
        $dados = $this->TViagViagem->carregarPorCodigoSm($codigo_sm);

        $dados_atendimento_sm['AtendimentoSm']['status'] = !empty($dados_atendimento_sm['AtendimentoSm']['data_fim']) ? 'Finalizado': (!empty($dados_atendimento_sm['AtendimentoSm']['data_analise']) ? 'Em Análise': 'Iniciado');
        $dados_atendimento_sm['AtendimentoSm']['prioridade'] = isset($dados_atendimento_sm['AtendimentoSm']['codigo_prioridade']) ? ($dados_atendimento_sm['AtendimentoSm']['codigo_prioridade'] == 3 ? 'Alta': ($dados_atendimento_sm['AtendimentoSm']['codigo_prioridade'] == 2 ? 'Média': 'Baixa')): 'Baixa'; 
        
        $primeiro_passo_atendimento_sm = $PassoAtendimentoSm->find('first', array(
            'conditions' => array('codigo_atendimento_sm' => $codigo_do_atendimento),
            'fields' => array('MIN(codigo) as codigo')
        ));
        
        $dados_historico_sm = $this->HistoricoSm->primeiroHistoricoAtendimento($primeiro_passo_atendimento_sm[0]['codigo']);

        $usuario_operador = '';
        if (!empty($dados_historico_sm['Funcionario']['Apelido'])) {
            $usuario_operador = $dados_historico_sm['Funcionario']['Apelido'];
        } elseif (!empty($dados_atendimento_sm['AtendimentoSm']['codigo_usuario_inclusao_guardian'])) {
            $usuario_operador = $TUsuaUsuario->retorna_usuario_login($dados_atendimento_sm['AtendimentoSm']['codigo_usuario_inclusao_guardian']);
        }
        
        $tipo_evento = $TEspaEventoSistemaPadrao->find('first', array('conditions' => array('espa_codigo' => $dados_historico_sm['HistoricoSm']['codigo_tipo_evento'])));
        
        $usuario_autorizacao = $Usuario->find('first', array(
            'conditions' => array('codigo' => $dados_historico_sm['HistoricoSm']['codigo_usuario_autorizacao']),
            'fields' => array('apelido')
        ));
        
        $dados_passo_atendimento_sm = $PassoAtendimentoSm->find('first', array(
            'conditions' => array('codigo' => $codigo_passo_atendimento_sm)));
        $passos_atendimentos = $PassoAtendimento->find('list');

        $usuario = $this->BAuth->user();
        $admin = 1;
        $buonnysat = array_search('Buonny Sat', $passos_atendimentos);
        $pronta_resposta = array_search('Pronta Resposta', $passos_atendimentos);
        if ($this->BAuth->temPermissao($usuario['Usuario']['codigo_uperfil'], 'obj_admin-atendimentos')) {            
            $this->set(compact('admin'));
        } elseif ($this->BAuth->temPermissao($usuario['Usuario']['codigo_uperfil'], 'obj_operador-pronta-resposta')) {
            $this->set(compact('pronta_resposta'));
        } elseif ($this->BAuth->temPermissao($usuario['Usuario']['codigo_uperfil'], 'obj_acionamento-buonnysat')) {
            $this->set(compact('buonnysat'));
        }

        unset($passos_atendimentos[$dados_passo_atendimento_sm['PassoAtendimentoSm']['codigo_passo_atendimento']]);
        
        $ultimo_passo = $PassoAtendimento->find('first', array('fields' => array('MAX(codigo) as codigo')));
        if ($ultimo_passo[0]['codigo'] == $dados_passo_atendimento_sm['PassoAtendimentoSm']['codigo_passo_atendimento']) {
            $this->set(compact('ultimo_passo'));
        }
        
        $encaminhado = $PassoAtendimentoSm->find('first', array(
            'conditions' => array('codigo' => $codigo_passo_atendimento_sm),
            'fields' => array('data_encaminhado')));
        if(!empty($encaminhado['PassoAtendimentoSm']['data_encaminhado'])) {
            $this->set(compact('encaminhado'));
        }

        $usuario_inclusao = $this->BAuth->user();
        
        if($this->RequestHandler->isPost()) {            
            if(!empty($this->data)) {
                try{
                    $this->HistoricoSm->query("BEGIN TRANSACTION");
                    if(is_array($this->data['HistoricoSm']['tipo_acao']))
                        $this->data['HistoricoSm']['tipo_acao'] = $this->data['HistoricoSm']['tipo_acao'][0];
                    $this->data['HistoricoSm']['codigo_usuario'] = $usuario_inclusao['Usuario']['codigo'];
                    $this->data['HistoricoSm']['codigo_atendimento_sm'] = $codigo_atendimento;

    				$gravar_aviso_encaminhamento = false;
    				if($this->data['HistoricoSm']['tipo_acao'] == 2) {
    					$gravar_aviso_encaminhamento = true;
    				}
                    $codigo_prestador = null;
                    if(!empty($this->data['HistoricoSm']['codigo_prestador'])){
    				    $codigo_prestador = $this->data['HistoricoSm']['codigo_prestador'];                    
                    }
                    if($this->data['HistoricoSm']['tipo_acao'] == 3){

                        $HistoricoSmPrestador->bindModel(array(
                                'belongsTo' => array(
                                    'HistoricoSm' => array(
                                        'foreignKey' => 'codigo_historico_sm'
                                    )
                                )
                            )
                        );
                        $prestadores_nao_finalizado = $HistoricoSmPrestador->find('count', array(
                            'conditions' => array(
                                    'HistoricoSm.codigo_atendimento_sm' => $codigo_atendimento,
                                    'HistoricoSmPrestador.status IS NULL'
                                )
                            )
                        );
                        if($prestadores_nao_finalizado > 0){
                            throw new Exception("Antes de finalizar o atendimento é necessário finalizar todos os prestadores");
                        }
                        
                    }

                    if($this->HistoricoSm->registrarAtendimento($this->data, $gravar_aviso_encaminhamento)) {
                        if(!is_null($codigo_prestador) && $codigo_prestador > 0){
                            $dados_prestador = array(
                                'HistoricoSmPrestador' => array(
                                        'codigo_prestador' => $codigo_prestador,
                                        'codigo_historico_sm' => $this->HistoricoSm->id                                  
                                    )
                            );
                            if(!$HistoricoSmPrestador->incluir($dados_prestador))
                                throw new Exception("Erro ao incluir Histórico do Prestador");
                            
                        }
                    } else
                        throw new Exception("Erro ao registrar Histórico do Atendimento");
                    
                    $this->HistoricoSm->commit();
                    $this->redirect(array('controller' => 'atendimentos_sms', 'action' => 'atendimentos'));
                    $this->BSession->setFlash('save_success');
                }catch(Exception $e){
                    $this->BSession->setFlash(array(MSGT_ERROR, $e->getMessage())); 
                    $this->HistoricoSm->rollback();                    
                }
            }
        }

        if ($bloquear_campos) {
            $this->set(compact('bloquear_campos'));
        }
       
        $this->set(compact('dados_historico_sm','dados_passo_atendimento_sm', 'dados_atendimento_sm', 'dados_sm', 'dados', 'codigo_sm', 'passos_atendimentos', 'usuario_autorizacao', 'codigo_atendimento', 'tipo_evento', 'usuario_operador'));
    }
    
    function listagem($codigo_sm){
        $historicos = $this->HistoricoSm->listarHistoricoAtendimento($codigo_sm);
        $this->set(compact('historicos'));
    }

    function sla_tipos_evento() {
        $this->pageTitle = 'SLA por Tipo de Evento';
        $dados = $this->carrega_series_sla();
        if (empty($dados))
            $this->BSession->setFlash('no_data');
        $this->set(compact('dados'));
    }
    
    function carrega_series_sla() {
        $periodo = array(
            '20120501 00:00:00',
            '20151231 23:59:59'
        );
        $dados = $this->HistoricoSm->listaSLA();
        if ($dados) {
            $pre_series = array();
            $eixo_x = array();
            foreach ($dados as $dado) {
                $eixo_x[] = "'".iconv('ISO-8859-1', 'UTF-8', $dado[0]['descricao'])."'";
                //$eixo_x[] = $dado[0]['codigo_tipo_evento'];
                $pre_series['dentro'][] = $dado[0]['dentro'];
                $pre_series['fora'][] = $dado[0]['fora'];
            }
            $series = array(
                array('name' => "'Dentro do SLA'",
                      'values' => $pre_series['dentro'],
                ),
                array('name' => "'Fora do SLA'",
                      'values' => $pre_series['fora'],
                ),
            );
            return array('eixo_x' => $eixo_x, 'series' => $series);
        }
    }
    public function calcula_distancia_prestador() {
        $this->layout = 'ajax';
        $retorno = array();
        foreach($_POST as $codigo => $latitudes){
            $distancia = Comum::distancia_entre_dois_pontos($latitudes['latitude1'],$latitudes['longitude1'],$latitudes['latitude2'],$latitudes['longitude2']);
            $retorno[$codigo] = number_format($distancia,2,',','.').' KM';
        }
        die(json_encode($retorno));
    }
    
}
