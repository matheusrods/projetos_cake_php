<?php

class UperfisController extends AppController {
    public $name = 'Uperfis';
    public $uses=array('Uperfil','Usuario','TipoPerfil', 'UperfilLog');
    var $helpers = array('Tree');
    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array(
            'busca_tipo_perfil_json',
            'listagem_log'
        ));
    }

    function bindUsuario() {
        $this->bindModel(
            array(
                'hasOne' => array(
                    'Usuario' => array(
                        'className' => 'Usuario',
                        'foreignKey' => 'codigo'
                    )
                )
            )
        );
    }
    
    function unbindUsuario() {
        $this->unbindModel(
            array(
                'hasOne' => array('Usuario')
            )
        );
    }

    function busca_tipo_perfil_json($codigo_uperfil){
        $tipo_perfil = $this->Uperfil->find('first',array('conditions' => array('codigo' => $codigo_uperfil), 'fields' => array('codigo_tipo_perfil')));
        echo json_encode($tipo_perfil);
        exit;
    }

    function index() {
        $this->pageTitle = 'Perfis';
        $this->set('isAjax', $this->RequestHandler->isAjax());
        $joins = array(
            array(
                'table' => "{$this->Usuario->databaseTable}.{$this->Usuario->tableSchema}.{$this->Usuario->useTable}",
                'alias' => 'Usuario',
                'type' => 'INNER',
                'conditions' => array('Usuario.codigo= Uperfil.codigo_usuario_inclusao')
            ),            
            array(
                'table' => "{$this->TipoPerfil->databaseTable}.{$this->TipoPerfil->tableSchema}.{$this->TipoPerfil->useTable}",
                'alias' => 'TipoPerfil',
                'type' => 'LEFT',
                'conditions' => array('TipoPerfil.codigo= Uperfil.codigo_tipo_perfil')
            ),
        );
                
        $options = array( 
            'recursive' => -1,
            'fields' =>array('Uperfil.codigo','Uperfil.descricao','TipoPerfil.descricao', 'Uperfil.data_inclusao', 'Usuario.nome'), 
            'order'  => array('Uperfil.descricao'), 
            'limit'  => 200, 
            'conditions' => array('Uperfil.codigo_cliente' => null),'joins' => $joins
        );
        if (isset($this->authUsuario['Usuario']['admin']) && $this->authUsuario['Usuario']['admin'] == 1) {
            if(!empty($this->authUsuario['Usuario']['codigo_cliente']))
                $options['conditions'] = array('Uperfil.codigo_cliente' => $this->authUsuario['Usuario']['codigo_cliente']);
        }
        $this->paginate = $options;
        $perfil_interno = $this->Usuario->verifica_usuario_interno($this->authUsuario['Usuario']['codigo']);
        $this->set(compact('perfil_interno'));
        $this->set('uperfis', $this->paginate());
    }

    function incluir() {
        $this->pageTitle     = 'Incluir Perfil';
        $this->loadModel('TipoPerfil');
        $this->loadModel('AlertaTipo');
        $this->loadModel('AlertaAgrupamento');
        $tipos_perfis        = $this->TipoPerfil->listar();
        $authUsuario         = $this->BAuth->user();
        $perfis = $this->Uperfil->carrega_perfis_interno();
        $usuario_tipo_perfil = $this->TipoPerfil->verificaTipoPerfil($authUsuario);

        $interno = $this->Usuario->verifica_usuario_interno($authUsuario['Usuario']['codigo']);
        $perfis = $this->Uperfil->carrega_perfis_interno();
        $listar_tipos_alertas = $this->AlertaAgrupamento->verifica_existencia_agrupamento();

        if (!empty($this->data)) {
            if( isset($this->authUsuario['Usuario']['admin']) && !empty($this->authUsuario['Usuario']['admin']) ){
                $this->data['Uperfil']['codigo_cliente']     = $this->authUsuario['Usuario']['codigo_cliente'];
                $this->data['Uperfil']['codigo_tipo_perfil'] = TipoPerfil::CLIENTE;
            } else {
            	$this->data['Uperfil']['codigo_tipo_perfil'] = TipoPerfil::INTERNO;
            }         

            $this->data['Uperfil']['codigo_alerta_tipo'] = array();
            foreach ($listar_tipos_alertas as $lista_tipo_alerta) {                      
                if(!empty($this->data['Uperfil']['codigo_alerta_tipo_'.$lista_tipo_alerta['AlertaAgrupamento']['descricao']])){
                    if(is_array($this->data['Uperfil']['codigo_alerta_tipo_'.$lista_tipo_alerta['AlertaAgrupamento']['descricao']])){
                        foreach ($this->data['Uperfil']['codigo_alerta_tipo_'.$lista_tipo_alerta['AlertaAgrupamento']['descricao']] as $key => $valor_alerta) {
                            array_push($this->data['Uperfil']['codigo_alerta_tipo'], $valor_alerta);
                        }
                    }else{
                        array_push($this->data['Uperfil']['codigo_alerta_tipo'], $this->data['Uperfil']['codigo_alerta_tipo_'.$lista_tipo_alerta['AlertaAgrupamento']['descricao']]);
                    } 
                }
            }

            if($interno){
            	
                if ($this->Uperfil->incluir_perfil_alertas($this->data)) {
                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('action' => 'permissoes_perfil', $this->Uperfil->id));
                } else {
                    $this->BSession->setFlash('save_error');
                }
            }else{
                if ($this->Uperfil->incluir($this->data)) {
                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('action' => 'permissoes_perfil', $this->Uperfil->id));
                } else {
                    $this->BSession->setFlash('save_error');
                }    
            }

            
        }
        $filtros_alerta = Array('AlertaTipo'=>Array('interno'=> 'S'));        
        $alertasTiposLista = $this->AlertaTipo->listarTipoAlerta($filtros_alerta);
           
        foreach($alertasTiposLista as $value){
           foreach ($listar_tipos_alertas as $key => $tipo) {    
              if($tipo['AlertaAgrupamento']['codigo'] == $value['AlertaTipo']['codigo_alerta_agrupamento']){
                $alertas_agrupados['codigo_alerta_tipo_'.$tipo['AlertaAgrupamento']['descricao']][$value['AlertaTipo']['codigo']] =  $value['AlertaTipo']['descricao'];
              }
           }
        } 
        $this->set(compact('tipos_perfis','usuario_tipo_perfil','perfis','alertas_agrupados'));
    }

    function editar($codigo = null) {
        $this->pageTitle = 'Editar Perfil';
        $this->loadModel('TipoPerfil');
        $this->loadModel('AlertaTipo');
        $this->loadModel('AlertaAgrupamento');
        $this->loadModel('UperfilTipoAlerta');
        $this->loadModel('Usuario');
        $tipos_perfis = $this->TipoPerfil->listar();
        $authUsuario = $this->BAuth->user();
        $usuario_tipo_perfil = $this->TipoPerfil->verificaTipoPerfil($authUsuario);
        
        $interno = $this->Usuario->verifica_usuario_interno($authUsuario['Usuario']['codigo']);
        $perfis = $this->Uperfil->carrega_perfis_interno();
        $listar_tipos_alertas = $this->AlertaAgrupamento->verifica_existencia_agrupamento();
      
        if (!$codigo && empty($this->data)) {
            $this->BSession->setFlash('codigo_invalido');
            $this->redirect(array('action' => 'index'));
        }   
        
        if (!empty($this->data)) {
            $this->data['Uperfil']['codigo_alerta_tipo'] = array();
            foreach ($listar_tipos_alertas as $lista_tipo_alerta) {                      
                if(!empty($this->data['Uperfil']['codigo_alerta_tipo_'.$lista_tipo_alerta['AlertaAgrupamento']['descricao']])){
                    if(is_array($this->data['Uperfil']['codigo_alerta_tipo_'.$lista_tipo_alerta['AlertaAgrupamento']['descricao']])){
                        foreach ($this->data['Uperfil']['codigo_alerta_tipo_'.$lista_tipo_alerta['AlertaAgrupamento']['descricao']] as $key => $valor_alerta) {
                            array_push($this->data['Uperfil']['codigo_alerta_tipo'], $valor_alerta);
                        }
                    }else{
                        array_push($this->data['Uperfil']['codigo_alerta_tipo'], $this->data['Uperfil']['codigo_alerta_tipo_'.$lista_tipo_alerta['AlertaAgrupamento']['descricao']]);
                    } 
                }
            }
            if($interno){
                if ($this->Uperfil->atualizar_perfil_alertas($this->data)) {
                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('action' => 'index'));
                }else{
                    $this->BSession->setFlash('save_error');
                } 
            }else{
                if ($this->Uperfil->atualizar($this->data)) {
                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('action' => 'index'));
                }else{
                    $this->BSession->setFlash('save_error');
                }    
            } 
        } else {
            $this->data = $this->Uperfil->read(null, $codigo);
            $alertas_exclusivos_perfis = $this->UperfilTipoAlerta->listar_tipos_por_perfil($codigo);
            $this->data['Uperfil']['codigo_alerta_tipo'] = $alertas_exclusivos_perfis;    
        }
        $filtros_alerta = Array('AlertaTipo'=>Array('interno'=> 'S'));        
        $alertasTiposLista = $this->AlertaTipo->listarTipoAlerta($filtros_alerta);
       
        foreach ($listar_tipos_alertas as $lista_tipo_alerta){
            $this->data['Uperfil']['codigo_alerta_tipo_'.$lista_tipo_alerta['AlertaAgrupamento']['descricao']] = $this->data['Uperfil']['codigo_alerta_tipo'];
        }   
    
        foreach($alertasTiposLista as $value){
           foreach ($listar_tipos_alertas as $key => $tipo) {    
              if($tipo['AlertaAgrupamento']['codigo'] == $value['AlertaTipo']['codigo_alerta_agrupamento']){
                $alertas_agrupados['codigo_alerta_tipo_'.$tipo['AlertaAgrupamento']['descricao']][$value['AlertaTipo']['codigo']] =  $value['AlertaTipo']['descricao'];
              }
           }
        }
            
        $this->set(compact('tipos_perfis','usuario_tipo_perfil','perfis','alertas_agrupados'));
    }

    function excluir($codigo) {
        $usuarios = $this->Usuario->listaUsuariosPerfil($codigo);
  
        if(!empty($usuarios)){
            $this->BSession->setFlash('save_error');
             $this->BSession->setFlash(array(MSGT_ERROR, 'Não foi possivel excluir,existe um usuário para este perfil.'));
            $this->redirect(array('action' => 'index'));
            return false;
        }

        if($this->Uperfil->excluir($codigo)){
            $this->BSession->setFlash('save_success');
            $this->redirect(array('action' => 'index'));
        }else{
            $this->BSession->setFlash(array(MSGT_ERROR, $this->Uperfil->validationErrors['codigo']));
            $this->redirect(array('action' => 'index'));
        }
    }

    function permissoes_perfil($codigo_uperfil) {
        set_time_limit(0);        
        $this->ObjetoAcl = ClassRegistry::init('ObjetoAcl');
        $this->pageTitle = 'Acessos para o perfil selecionado';
        if($this->RequestHandler->isPost()) {
            if (isset($this->authUsuario['Usuario']['admin']) && $this->authUsuario['Usuario']['admin'] == 1) {
                //Foi ocultado o tela inicial, porem os dados serao salvos.
                $permissaoTelaInicial = array( 
                    'Painel__modulo_comercial'=> 1, 'Painel__modulo_financeiro'=> 1, 'Painel__modulo_saude' => 1, 'Painel__modulo_seguranca' => 1,
                );
                if( !empty($this->data['Permissao']) )
                    $this->data['Permissao'] = array_merge($this->data['Permissao'], $permissaoTelaInicial );
                else
                    $this->data['Permissao'] = $permissaoTelaInicial;
            }

            $antes = $this->Uperfil->listaPermissoes($codigo_uperfil, true);

            $log = array('UperfilLog' => array(
                'codigo_uperfil' => $codigo_uperfil,
                'antes' => ($antes) ? json_encode($antes) : '',
                'depois' => ($this->data) ? json_encode($this->data) : '',
                'data_inclusao' => date("Y-m-d H:i:s"),
                'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo']
            ));

            // debug('log');
            // debug($log);
            // exit;

            // Salvar o log
            if (!$this->UperfilLog->incluir($log)) {
                $this->BSession->setFlash('save_error');
            }

            $this->Uperfil->geraPermissao($codigo_uperfil, $this->data);
            $this->Uperfil->limpa_cache_servidor($codigo_uperfil);
            $this->BSession->setFlash('save_success');
            $this->redirect(array('action' => 'index'));
        }
        $perfil = $this->Uperfil->carregar($codigo_uperfil);
        $codigo_tipo_perfil = $this->Uperfil->codigoTipoPerfil($perfil['Uperfil']['codigo']);
        if (isset($this->authUsuario['Usuario']['admin']) && !empty($this->authUsuario['Usuario']['admin'])) {
       		$permitidos = $this->Uperfil->listaPermissoes($this->authUsuario['Usuario']['codigo_uperfil'], true, true);

            $objetos = $this->ObjetoAcl->listaObjetos($codigo_tipo_perfil, $permitidos, true);
            unset( $objetos[0] ); //Nao exibir Tela Inicial para o usuario Admin
        } else {
            $objetos = $this->ObjetoAcl->listaObjetos($codigo_tipo_perfil, null, true);
        }
        $this->data = $this->Uperfil->listaPermissoes($codigo_uperfil, true);
        $this->set(compact('perfil', 'objetos'));
    }

    function listagem_log($codigo_uperfil) {
        set_time_limit(0);

        $this->pageTitle = 'Log do Perfil';
        
        $this->ObjetoAcl = ClassRegistry::init('ObjetoAcl');
        
        $uperfil = $this->Uperfil->carregar($codigo_uperfil);
        
		$this->paginate['UperfilLog'] = array(
			'conditions' => array('UperfilLog.codigo_uperfil' => $codigo_uperfil),
			'order' => array('UperfilLog.codigo DESC'),
			'joins' => array(
				array(
					'table' => "{$this->Usuario->databaseTable}.{$this->Usuario->tableSchema}.{$this->Usuario->useTable}",
					'alias' => 'Usuario',
					'conditions' => array('Usuario.codigo = UperfilLog.codigo_usuario_inclusao'),
					'type' => 'LEFT',
                )
            ),
			'fields' => array('UperfilLog.codigo', 'UperfilLog.codigo_uperfil', 'UperfilLog.antes', 'UperfilLog.depois', 'UperfilLog.data_inclusao', 'Usuario.codigo', 'Usuario.nome')
        );

		$uperfilLogs = $this->paginate('UperfilLog');
		        
        $codigo_tipo_perfil = $this->Uperfil->codigoTipoPerfil($uperfil['Uperfil']['codigo']);
        if (isset($this->authUsuario['Usuario']['admin']) && !empty($this->authUsuario['Usuario']['admin'])) {
       		$permitidos = $this->Uperfil->listaPermissoes($this->authUsuario['Usuario']['codigo_uperfil'], true, true);

            $objetos = $this->ObjetoAcl->listaObjetos($codigo_tipo_perfil, $permitidos, true);
            unset( $objetos[0] ); //Nao exibir Tela Inicial para o usuario Admin
        } else {
            $objetos = $this->ObjetoAcl->listaObjetos($codigo_tipo_perfil, null, true);
        }

        $this->set(compact('uperfilLogs', 'uperfil', 'objetos'));
    }
}
