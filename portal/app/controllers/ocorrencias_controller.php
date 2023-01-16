<?php
class OcorrenciasController extends AppController {

    public $name = 'Ocorrencias';
    public $layout = 'sistemas_externos';
    public $helpers = array('Highcharts');
    public $uses = array('Ocorrencia', 'StatusOcorrencia', 'Tecnologia', 'Recebsm', 'TipoOcorrencia', 'Equipamento',
     'HierarquiaUsuario', 'Rastreador', 'OperacaoMonitora', 'OcorrenciaHistorico', 'PerfilStatusOcorrencia', 'Filtro');

    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array(
            '_verifica_acao',
            'autentica',
            '_autenticar_usuario',
            '_incluir',
            '_editar',
            'carrega_combos',
        ));
    }

    function _configurarPerfil() {
        $codigo_usuario = $this->Session->read('codigo_usuario');
        $codigo_perfil  = $this->Session->read('codigo_perfil');

        if (empty($codigo_usuario)) {
            $codigo_usuario = $this->Session->read('Auth.Usuario.codigo');
            $this->Session->write('codigo_usuario', $codigo_usuario);
        }

        if (empty($codigo_perfil)) {
            $this->loadModel('Perfil');
            $this->loadModel('Usuario');

            $perfis = $this->Usuario->obtemPerfil($codigo_usuario);

            $codigo_perfil = array_keys($perfis);

            if (in_array(Perfil::SUPERVISOR_BUONNYSAT, $perfis)) {
                $codigo_perfil = array_search(Perfil::SUPERVISOR_BUONNYSAT, $perfis);
            }

            if (in_array(Perfil::PRONTA_RESPOSTA, $perfis)) {
                $codigo_perfil = array_search(Perfil::PRONTA_RESPOSTA, $perfis);
            }

            $this->Session->write('codigo_perfil', $codigo_perfil);
        }

    }

    function error() {

    }

    function success() {
        $this->BSession->setFlash('save_success');
    }

    function verifica_acao() {
        $this->autentica();
        if ($this->Session->read('codigo_usuario') > 0) {
            $codigo_sm = $this->Session->read('codigo_sm');
            $ocorrencia = $this->Ocorrencia->ultimaOcorrencia($codigo_sm);
            $status_sm = $ocorrencia['Ocorrencia']['codigo_status_ocorrencia'];
            $this->redirect(array('action' => 'incluir', $codigo_sm));
        } else {
            $this->redirect(array('action' => 'error'));
        }
    }

    function autenticar_usuario($dados_usuario) {
        $usuario = $this->BAuth->auth($dados_usuario['apelido'], $dados_usuario['senha']);
        if (empty($usuario)) {
            $this->Usuario->invalidate('apelido', 'login ou senha inválidos');
            $this->Usuario->invalidate('senha', '');
            return false;
        }
        return $usuario;
    }

     function incluir() {
         $this->layout = 'sistema_externo';
            $codigo_sm = $this->passedArgs[0];
            $this->set(compact(array('dados', 'codigo_sm')));

            if (!empty($this->data)) {
                $dados = $this->Ocorrencia->formataDados($this->data);
                $this->loadModel('Usuario');
                $usuario_autenticado = $this->autenticar_usuario($this->data['Usuario']);
                unset($this->data['Usuario']['senha']);
                if (!empty($usuario_autenticado)) {
                    $permitido = !empty($usuario_autenticado) ? $this->BAuth->temPermissao($usuario_autenticado['Usuario']['codigo_uperfil'], 'obj_acionamento-buonnysat') : false;
                    $codigo_supervisor_buonnysat = $this->Usuario->find('first', array('conditions' => array('Usuario.apelido' => $dados['Usuario']['apelido']), 'fields' => 'Usuario.codigo'));

                    $this->data['Ocorrencia']['codigo_funcionario'] = 1;
                    $dados['Ocorrencia']['usuario_monitora_inclusao'] = $this->Session->read('codigo_usuario');
                    $dados['Ocorrencia']['codigo_supervisor_buonnysat'] = $codigo_supervisor_buonnysat['Usuario']['codigo'];
                    if ($permitido) {
                        if (!empty($dados['OcorrenciaTipoSelecionado']['codigo_tipo_ocorrencia'])) {
                            if ($this->Ocorrencia->incluir($dados)) {
                                echo 'Dados da Ocorrência salvos com sucesso';
                                die;
                            } else {
                                $this->BSession->setFlash('save_error');
                            }
                        } else {
                            $this->BSession->setFlash('tipo_ocorrencia_nao_selecionado');
                        }
                    } else {
                        $this->BSession->setFlash('nao_eh_supervisor');
                    }
                } else {
                    $this->BSession->setFlash('invalid_login');
                }
            } else {
                $dados = $this->Recebsm->BuscaDados($codigo_sm);
                $local = $this->Rastreador->buscaUltimaPosicao($dados['Recebsm']['Placa']);
                $this->data['Ocorrencia']['codigo_sm'] = $this->Session->read('codigo_sm');
                $this->data['Ocorrencia']['placa'] = $dados['Recebsm']['Placa'];
                $this->data['Ocorrencia']['empresa'] = $dados['ClientEmpresa']['Raz_social'];
                $this->data['Ocorrencia']['telefone_empresa'] = $dados['ClientEmpresa']['Telefone'];
                $this->data['Equipamento']['Descricao'] = $dados['Equipamento']['Descricao'];
                $this->data['Ocorrencia']['codigo_tecnologia'] = $dados['Equipamento']['Codigo'];
                $this->data['Ocorrencia']['motorista'] = $dados['Motorista']['Nome'];
                $this->data['Ocorrencia']['telefone_motorista'] = $dados['Motorista']['DDDTelefone'] . ' ' . $dados['Motorista']['telefone'];
                $this->data['Ocorrencia']['celular_motorista'] = $dados['Motorista']['DDDcelular'] . ' ' . $dados['Motorista']['celular'];
                $this->data['Ocorrencia']['origem'] = $dados['CidadeOrigem']['Descricao'];
                $this->data['Ocorrencia']['destino'] = $dados['CidadeDestino']['Descricao'];
                $this->data['Ocorrencia']['local'] = $local;
                $this->data['Ocorrencia']['data_ocorrencia'] = date('d/m/Y H:i');
            }
            $this->carrega_combos();
    }

    function editar() {
        if (!empty($this->data)) {
            $dados = $this->Ocorrencia->formataDados($this->data);
            $dados['Ocorrencia']['usuario_monitora_alteracao'] = $this->Session->read('codigo_usuario');
            if ($this->Ocorrencia->atualizar($dados)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'success'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $codigo_ocorrencia = $this->passedArgs[0];
            $dados = $this->Ocorrencia->findByCodigo($codigo_ocorrencia);
            $dados = $this->Ocorrencia->formataDados($dados, 2);
            $this->data = $dados;
        }
        $this->carrega_combos();
    }

    function carrega_combos() {
        $status_ocorrencia = $this->PerfilStatusOcorrencia->statusPerfilOperadorBuonnySat();
        $tecnologia = $this->Equipamento->find('list');
        $tipos_ocorrencia = $this->TipoOcorrencia->listaTipoOcorrencia();
        $this->set(compact('tecnologia', 'tipos_ocorrencia', 'status_ocorrencia'));
    }

    function autentica() {
        if ($this->Session->read('codigo_usuario') == null ||
                (in_array($this->action, array('index', 'verifica_acao')) && isset($this->passedArgs[0]))) {
            if (isset($this->passedArgs[0]) && $this->passedArgs[0] == 'vlKbjDf') {
                $hash = $this->passedArgs[0];
                $codigo_usuario = $this->passedArgs[1];
                if (isset($this->passedArgs[2])) {
                    $codigo_sm = $this->passedArgs[2];
                    $this->Session->write('codigo_sm', $codigo_sm);
                }
                $this->Session->write('codigo_usuario', $codigo_usuario);
            }
        }
    }

    function trata_lista_ocorrencias($lista_ocorrencias) {
        $tipos_ocorrencia = $this->TipoOcorrencia->find('list');
        $style_tipos_ocorrencia = $this->TipoOcorrencia->find('list', array('fields' => 'style'));
        foreach ($lista_ocorrencias as $key_ocorrencia => $ocorrencia) {
            $lista_ocorrencias[$key_ocorrencia]['Ocorrencia']['tipos_ocorrencia'] = '';
            $style = '';
            foreach ($ocorrencia['OcorrenciaTipo'] as $ocorrencia_tipo) {
                $descricao = isset($tipos_ocorrencia[$ocorrencia_tipo['codigo_tipo_ocorrencia']]) ? $tipos_ocorrencia[$ocorrencia_tipo['codigo_tipo_ocorrencia']] : '';
                if (empty($style))
                    $style = isset($style_tipos_ocorrencia[$ocorrencia_tipo['codigo_tipo_ocorrencia']]) ? $style_tipos_ocorrencia[$ocorrencia_tipo['codigo_tipo_ocorrencia']] : '';
                if (!isset($lista_ocorrencias[$key_ocorrencia]['Ocorrencia']['tipos_ocorrencia']))
                    $lista_ocorrencias[$key_ocorrencia]['Ocorrencia']['tipos_ocorrencia'] = '';
                $lista_ocorrencias[$key_ocorrencia]['Ocorrencia']['tipos_ocorrencia'] .= ',' . $descricao;
            }
            $lista_ocorrencias[$key_ocorrencia]['Ocorrencia']['tipos_ocorrencia'] = substr($lista_ocorrencias[$key_ocorrencia]['Ocorrencia']['tipos_ocorrencia'], 1);
            $lista_ocorrencias[$key_ocorrencia]['Ocorrencia']['style'] = $style;
        }
        return $lista_ocorrencias;
    }

    function atualizar($codigo) {
        $this->layout = 'ajax';
        $this->_configurarPerfil();
        $codigo_perfil = $this->Session->read('codigo_perfil');

        $ocorrencia = $this->Ocorrencia->findByCodigo($codigo);

        $ocorrencia_tipo = $this->TipoOcorrencia->listaTipoOcorrencia();

        $tipoStatusSVizualizacao = $this->PerfilStatusOcorrencia->statusPorPerfil($codigo_perfil);
        $this->set(compact(array('ocorrencia', 'tipoStatusSupervisor', 'tipoStatusSVizualizacao','ocorrencia_tipo')));

        if (!empty($this->data)) {
            $usuario_alt = $this->Session->read('codigo_usuario');
            $condicao = array('Ocorrencia.codigo' => $codigo);
            $campos = array(
                'Ocorrencia.codigo_status_ocorrencia' => $this->data['Ocorrencia']['codigo_status_ocorrencia'],
                'Ocorrencia.usuario_monitora_alteracao' => $usuario_alt,
                'Ocorrencia.data_alteracao' => '"' . date('Y-m-d H:i:s') . '"'
            );

            App::import('Model', 'Perfil');

            $response = new stdClass;

            /**
             * @todo Mover Regra para Model
             */
            if ($codigo_perfil != Perfil::PRONTA_RESPOSTA
                    && ($this->data['Ocorrencia']['codigo_status_ocorrencia'] == 9
                    || $this->data['Ocorrencia']['codigo_status_ocorrencia'] == 12)) {
                $response->success = false;
            } else {
                $this->Ocorrencia->updateAll($campos, $condicao);
                $response->success = true;
            }

            echo json_encode($response);
            exit(0);
        }
    }

    function lista_ocorrencias() {
        $this->pageTitle = 'Ocorrências';
        $this->carrega_combos_lista('normal');
        $authUsuario = $this->BAuth->user();
        $filtros_salvos = $this->Filtro->listaFiltros('ocorrencias', $authUsuario['Usuario']['codigo']);
        $this->set(compact('filtros_salvos'));
        $this->data['Ocorrencia'] = $this->Filtros->controla_sessao($this->data, $this->Ocorrencia->name);
    }

    function lista_ocorrencias_consulta() {
        $this->pageTitle = 'Consulta Ocorrências';
        $this->carrega_combos_lista('consulta');
        $authUsuario = $this->BAuth->user();
        $filtros_salvos = $this->Filtro->listaFiltros('ocorrencias', $authUsuario['Usuario']['codigo']);
        $this->set(compact('filtros_salvos'));
        $this->data['Ocorrencia'] = $this->Filtros->controla_sessao($this->data, $this->Ocorrencia->name);
    }

    function carrega_combos_lista($tipo){
        if ($tipo == 'normal') {
            $usuario = $this->BAuth->user();
            if ($this->BAuth->temPermissao($usuario['Usuario']['codigo_uperfil'], 'buonny')) {
                $tipoStatusSVizualizacao = $this->StatusOcorrencia->find('list');
            } else {
                $codigo_objeto = $this->codigo_objeto();
                $tipoStatusSVizualizacao = $this->PerfilStatusOcorrencia->statusPorObjeto($codigo_objeto);
            }
        } elseif ($tipo == 'consulta') {
            $tipoStatusSVizualizacao = $this->StatusOcorrencia->find('list');
        }
        $tipos_ocorrencia = $this->TipoOcorrencia->listaTipoOcorrencia();
        $tecnologias = $this->Equipamento->find('list', array('order' => 'descricao'));
        $operacoes = $this->OperacaoMonitora->listaOperacoes();
        $this->set(compact('tipoStatusSVizualizacao', 'tipos_ocorrencia', 'tecnologias', 'operacoes'));
    }

    function codigo_objeto() {
        $codigo_objeto = $this->Session->read('codigo_objeto');
        $usuario = $this->BAuth->user();
        if ($codigo_objeto == null) {
            $constantes = $this->PerfilStatusOcorrencia->constantes();
            foreach ($constantes as $key => $constante) {
                if ($this->BAuth->temPermissao($usuario['Usuario']['codigo_uperfil'], $constante)) {
                    $codigo_objeto = $key;
                    $this->Session->write('codigo_objeto', $codigo_objeto);
                    break;
                }
            }
        }
        return $codigo_objeto;
    }

    function listagem($tipo) {
        $this->layout = 'ajax';
        $filtros    = $this->Filtros->controla_sessao($this->data, $this->Ocorrencia->name);
        $usuario = $this->BAuth->user();
        $codigo_objeto = null;
        if (!$this->BAuth->temPermissao($usuario['Usuario']['codigo_uperfil'], 'buonny')) 
            $codigo_objeto = $this->codigo_objeto();

        $conditions = $this->Ocorrencia->converteFiltrosEmConditions($filtros, $codigo_objeto, ($tipo == 'consulta'));
        $tipoStatusSVizualizacao = $this->PerfilStatusOcorrencia->statusPorObjeto($codigo_objeto);
        $this->paginate['Ocorrencia'] = array(
            'recursive' => 1,
            'conditions' => $conditions,
            'limit' => 200,
            'order' => array('Ocorrencia.data_ocorrencia')
        );
        $operacoes = $this->OperacaoMonitora->listaOperacoes();
        $ocorrencias = $this->paginate('Ocorrencia');
        $ocorrencias = $this->trata_lista_ocorrencias($ocorrencias);

        $this->set(compact('ocorrencias', 'tipoStatusSVizualizacao', 'operacoes'));
        if ($tipo == 'normal')
            $this->render('listagem');
        elseif ($tipo == 'consulta')
            $this->render('listagem_consulta');
    }

    function adicionar_acao($codigo_ocorrencia){
        $this->layout = 'ajax';
        $codigo_objeto = $this->codigo_objeto();

        if (!empty($this->data)) {
            App::import('Model', 'PerfilStatusOcorrencia');

            $dados = $this->Ocorrencia->formataDados($this->data);

            $status_anterior = $this->Ocorrencia->findByCodigo($dados['Ocorrencia']['codigo']);
            $status_anterior = $status_anterior['Ocorrencia']['codigo_status_ocorrencia'];

            if ($this->Ocorrencia->atualizar($dados)) {
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $ocorrencia = $this->Ocorrencia->findByCodigo($codigo_ocorrencia);
            $ocorrencia = $this->Ocorrencia->formataDados($ocorrencia, 2);
            $this->data = $ocorrencia;
        }

        $tipos_ocorrencia = $this->TipoOcorrencia->listaTipoOcorrencia();
        

        $usuario = $this->BAuth->user();
        if ($this->BAuth->temPermissao($usuario['Usuario']['codigo_uperfil'], 'buonny')) {
            $tipos_status = $this->StatusOcorrencia->find('list');
        } else {
            $codigo_objeto = $this->codigo_objeto();
            $tipos_status = $this->PerfilStatusOcorrencia->statusPorObjeto($codigo_objeto);
        }
        $this->set(compact('ocorrencia', 'tipos_status', 'tipos_ocorrencia'));
    }

    function visualiza_ocorrencia($codigo_ocorrencia){
        $ocorrencia = $this->Ocorrencia->findRecursiveByCodigo($codigo_ocorrencia);
        $ocorrencia = $this->Ocorrencia->formataDados($ocorrencia, 2);
        $ocorrencia['OcorrenciaTipo']['codigo_tipo_ocorrencia'] = Set::extract($ocorrencia['OcorrenciaTipo'], '/codigo_tipo_ocorrencia');
        $tipos_ocorrencia = $this->TipoOcorrencia->listaTipoOcorrencia();
        $tipos_status_geral = $this->StatusOcorrencia->find('list');
        $this->data = $ocorrencia;
        $this->set(compact('ocorrencia', 'tipos_ocorrencia', 'tipos_status_geral'));
    }

    function status_sla() {
        $this->pageTitle = 'SLA Ocorrências';
        $dados = null;
        if (!empty($this->data)) {
        	$dados = $this->carrega_series_sla();
        	if (empty($dados))
        	    $this->BSession->setFlash('no_data');
        }
        $this->set(compact('dados'));
    }

    function carrega_series_sla() {
		$periodo = array(
			'20120501 00:00:00',
    	    '20151231 23:59:59'
		);
        $dados = $this->Ocorrencia->statusSLAPorSetor($periodo, 30, $this->data['Ocorrencia']['tipo']);
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

    function pre_filtro_lista_ocorrencias_consulta() {
        $this->Filtros->limpa_sessao('Ocorrencia');
        $this->Filtros->controla_sessao($this->data, 'Ocorrencia');
        $this->redirect('lista_ocorrencias_consulta');
    }


    function viagem_ocorrencias($viag_codigo){
        $this->loadModel('TVocoViagemOcorrencia');
        $dados =& $this->TVocoViagemOcorrencia->carregarPorViag($viag_codigo);
        $this->set(compact('dados','viag_codigo'));
    }

    function incluir_viagem_ocorrencia($viag_codigo){
        $this->loadModel('TVocoViagemOcorrencia');
        $this->layout   = "ajax";

        if ($this->RequestHandler->isPost()) {
            if($this->TVocoViagemOcorrencia->incluir($this->data)){
                $id = $this->TVocoViagemOcorrencia->getLastInsertID();
                $this->TVocoViagemOcorrencia->salvar_alerta($this->data,$id);
                $this->BSession->setFlash('save_success');
            }else{
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data['TVocoViagemOcorrencia']['voco_viag_codigo'] = $viag_codigo;
        }

        $this->set(compact('viag_codigo'));
    }

    function excluir_viagem_ocorrencia($voco_codigo){
        $this->loadModel('TVocoViagemOcorrencia');

        if(!$this->TVocoViagemOcorrencia->excluir($voco_codigo))
            echo "Erro ao excluir a ocorrencia";
        exit;
    }

}