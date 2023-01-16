<?php
class RotasController extends AppController {
	var $name = 'Rotas';
	var $uses = array('Rota');

    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('incluir','editar','excluir','mapa','novo_destino','buscar_codigo','listagem_visualizar','autocomplete_rotas'));
    }


	function index() {
		
		$this->pageTitle    = 'Rotas';
		$this->data['Rota'] = $this->Filtros->controla_sessao($this->data, $this->Rota->name);		
	}

	function listagem() {
		$this->layout    = 'ajax';
        $filtros['Rota'] = $this->Filtros->controla_sessao($this->data, $this->Rota->name);     
        $conditions      = $this->Rota->converterFiltrosEmConditions($filtros);        

        $this->paginate['Rota'] = array(
            'conditions' => $conditions, 
            'fields'     => array(
                'Rota.codigo',
                'Rota.descricao',
                'Rota.km',
                'Rota.DataCriacao',
                'CidadeOrigem.descricao',
                'CidadeOrigem.estado',
                'CidadeDestino.descricao', 
                'CidadeDestino.estado', 
            ),
            'order' => 'Rota.codigo DESC',
            'limit' => 50,               
            'extra' => array('joins' => $this->Rota->joinsListagemRotas())
        );

        $dados = $this->paginate('Rota');
        $this->set(compact('dados'));        
	}

	public function incluir(){

        $this->pageTitle = 'Incluir Rota';

        if( isset($this->data) && !empty($this->data) ){            
            
            $codigo = str_pad($this->Rota->nextValRota(),6,'0',STR_PAD_LEFT);
            $this->data['Rota']['Codigo']       = $codigo;            
            $this->data['Rota']['DataCriacao']  = date('Y-m-d H:i:s');
            if($this->Rota->save($this->data)){
                $this->BSession->setFlash('save_success');
                $this->redirect( array( 'action'=>'index' ) );
            }else{
                $this->BSession->setFlash('save_error');                
            }
        }
    }

    public function editar($codigo){

        $this->pageTitle = 'Editar Rota';

        if( isset($this->data) && !empty($this->data) ){
            
            if($this->Rota->atualizar($this->data)){
                $this->BSession->setFlash('save_success');
                $this->redirect( array( 'action'=>'index') );
            }else{
                $this->BSession->setFlash('save_error');                
            }
        } else {
            $this->loadModel('Cidade');
            $result = $this->Rota->find('first',array('conditions'=>'Codigo = '.$codigo));
            $cida_origem  = $this->Cidade->find('first',array('conditions'=>'Codigo = '.$result['Rota']['Origem']));
            $cida_destino = $this->Cidade->find('first',array('conditions'=>'Codigo = '.$result['Rota']['Destino']));
            
            $this->data = $this->Rota->findByCodigo($codigo);
            $this->data['Rota']['cidade_origem']  = $cida_origem['Cidade']['Descricao'];
            $this->data['Rota']['cidade_destino'] = $cida_destino['Cidade']['Descricao'];
        }
    }

    public function excluir($codigo){

        if( isset($codigo) && !is_null($codigo) ){
            if( $this->Rota->excluir($codigo) ){
                $this->BSession->setFlash('delete_success');
                $this->redirect( array( 'action'=>'index' ) );
            }else{
                $this->BSession->setFlash('delete_error');
            }
        }else{
            $this->BSession->setFlash('delete_error');
        }
    }

    public function mapa($refe_codigos = false, $edit = false){
        $this->loadModel('TRotaRota');
        $this->loadModel('TRefeReferencia');
        $this->layout = 'ajax';
        $this->pageTitle = '';
        $this->data = $this->params['url'];
        //debug($this->data);
        if(isset($this->data['rota_codigo']) && !empty($this->data['rota_codigo'])){
            $this->TRotaRota->bindTRponRotaPonto();
            if (isset($this->data['edit']) && $this->data['edit']=='true') $edit = true;

            $rota = $this->TRotaRota->find('first', array('conditions' => array('rota_codigo' => $this->data['rota_codigo'])));
            if($rota){

                $latitudes = array();
                $longitudes = array();
                $desvios = Array();

                $origem = reset(array_filter($rota['TRponRotaPonto'],function($var){ return $var['rpon_sequencia'] == -1; }));
                $destino = reset(array_filter($rota['TRponRotaPonto'],function($var){ return $var['rpon_sequencia'] == -2; }));
                $itinerario = array_filter($rota['TRponRotaPonto'],function($var){ return $var['rpon_sequencia'] != -2 && $var['rpon_sequencia'] != -1; });
                usort($itinerario, function($a,$b){ return $a['rpon_sequencia'] == $b['rpon_sequencia'] ? 0 : $a['rpon_sequencia'] < $b['rpon_sequencia'] ? -1 : 1; });
                $latitudes[] = $origem['rpon_latitude'];
                $longitudes[] = $origem['rpon_longitude'];
                foreach($itinerario as $rota_ponto){
                    if($rota_ponto['rpon_refe_codigo'] != $destino['rpon_refe_codigo']){
                        $latitudes[] = $rota_ponto['rpon_latitude'];
                        $longitudes[] = $rota_ponto['rpon_longitude'];
                    }
                }
                $latitudes[] = $destino['rpon_latitude'];
                $longitudes[] = $destino['rpon_longitude'];

                if (!empty($rota['TRotaRota']['rota_desvios'])) {
                    $arrDesvios = explode(';',$rota['TRotaRota']['rota_desvios']);
                    foreach ($arrDesvios as $key => $desvio) {
                        $arrCoordenadas = explode("|",$desvio);
                        $leg = $arrCoordenadas[0];
                        if ($leg=='') break;

                        $latitude = $arrCoordenadas[1];
                        $longitude = $arrCoordenadas[2];

                        if (!isset($desvios[$leg])) $desvios[$leg] = Array();
                        $desvios[$leg][] = Array(
                            'latitude'=>$latitude,
                            'longitude'=>$longitude
                        );
                    }
                }


                $this->set(compact('latitudes','longitudes','edit','desvios' ));
            }
        }else{
            if($refe_codigos){
                $refe_referencias = array();
                $latitudes = array();
                $longitudes = array();
                $desvios = Array();
                $refe_codigos = explode('|',trim($refe_codigos,'|'));
                foreach($refe_codigos as $refe_codigo){
                    $refe_referencias[] = $this->TRefeReferencia->carregar($refe_codigo);
                }
                if(!empty($refe_referencias)){
                    foreach($refe_referencias as $refe_referencia){
                        $latitudes[] = $refe_referencia['TRefeReferencia']['refe_latitude'];
                        $longitudes[] = $refe_referencia['TRefeReferencia']['refe_longitude'];
                    }
                }
                $this->set(compact('latitudes','longitudes','edit','desvios'));
            }else{
                $this->data['mapa'] = true;
            }
        }
    }

    public function novo_destino($contador){
        $this->loadModel('TTparTipoParada');
        $this->layout   = false;

        $tipo_parada    = $this->TTparTipoParada->listarParaFormulario();
        $this->set(compact('contador','tipo_parada'));
    }

    public function buscar_codigo(){
        if( !empty($this->passedArgs['codigo']))
            $this->data['TRotaRota']['codigo_cliente'] = $this->passedArgs['codigo']; 

        $clientes = Array();
        if ((!empty($this->passedArgs['codigo_embarcador'])) || (!empty($this->passedArgs['codigo_transportador']))) {
            if (!empty($this->passedArgs['codigo_embarcador'])) {
                $clientes[] = $this->passedArgs['codigo_embarcador'];
            }
            if (!empty($this->passedArgs['codigo_transportador'])) {
                $clientes[] = $this->passedArgs['codigo_transportador'];
            }
            $this->data['TRotaRota']['codigo_cliente'] = $clientes;
            $filtro_rota = 'codigo_embarcador:'.$this->passedArgs['codigo_embarcador'].'/'.'codigo_transportador:'.$this->passedArgs['codigo_transportador'];
        } else {
            $filtro_rota = 'codigo:'.$this->passedArgs['codigo'];
        }

        $this->Filtros->controla_sessao( $this->data, 'TRotaRota');
        $this->set(compact('filtro_rota'));
    }

    public function listagem_visualizar(){
        $this->loadModel('TRotaRota');
        $this->loadModel('Cliente');
        $this->loadModel('TPjurPessoaJuridica');
        $filtros = $this->Filtros->controla_sessao($this->data, 'TRotaRota');
        if (is_array($filtros['codigo_cliente'])) {
            $pjur_pess_oras_codigo = Array();
            foreach ($filtros['codigo_cliente'] as $codigo_cliente) {
                $cliente = $this->Cliente->carregar($codigo_cliente);
                $cliente_pjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($cliente['Cliente']['codigo_documento']);
                $pjur_pess_oras_codigo[] = $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];    
            }
        } else {
            $cliente = $this->Cliente->carregar($filtros['codigo_cliente']);
            $cliente_pjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($cliente['Cliente']['codigo_documento']);
            $pjur_pess_oras_codigo = $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
        }
        $listagem = $this->TRotaRota->listarPorCliente($pjur_pess_oras_codigo, $filtros);
        $this->set(compact('listagem'));
    }

    public function autocomplete_rotas(){
        $this->layout       = false;
        $this->loadModel('TRotaRota');
        $this->loadModel('Cliente');
        $this->loadModel('TPjurPessoaJuridica');
        $clientes = Array();

        if( !empty($this->passedArgs['codigo'])) {
            $codigo_cliente = $this->passedArgs['codigo'];
            $clientes[] = $codigo_cliente;
        }
        
        if ((!empty($this->passedArgs['codigo_embarcador'])) || (!empty($this->passedArgs['codigo_transportador']))) {
            if (!empty($this->passedArgs['codigo_embarcador'])) {
                $clientes[] = $this->passedArgs['codigo_embarcador'];
            }
            if (!empty($this->passedArgs['codigo_transportador'])) {
                $clientes[] = $this->passedArgs['codigo_transportador'];
            }
        }

        $retorno = array();
        $pjur_pess_oras_codigo = Array();
        foreach ($clientes as $codigo_cliente) {
            if ($codigo_cliente > 0) {
                $cliente_pjur   = $this->TPjurPessoaJuridica->buscaClienteCentralizador($codigo_cliente);
                if ($cliente_pjur) {
                    $pjur_pess_oras_codigo[] = $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];

                }
            } else {
                $cliente_pjur = false;
            }
        }
        if (empty($pjur_pess_oras_codigo)) $pjur_pess_oras_codigo = false;
        $filtros = Array(
            'descricao' => $_GET['term']
        );
        $rotas = $this->TRotaRota->listarPorCliente($pjur_pess_oras_codigo, $filtros, false);
        if($rotas){
            foreach($rotas as $key => $value){
                $retorno[]  = array('label' => $value['TRotaRota']['rota_descricao'], 'value' => $value['TRotaRota']['rota_codigo']);
            }
        }

        echo json_encode($retorno);
        exit;
    }

    //Rotas
    public function carregar_combos_rotas() {
        $ativo = array(1 => 'Sim', 2 => 'Não');
        $this->set(compact('ativo'));   
    }

    public function rotas() {
        $this->pageTitle = 'Rotas';
        $this->loadModel('TRotaRota');      
        $authUsuario = $this->BAuth->user();        
        $this->data['TRotaRota'] = $this->Filtros->controla_sessao($this->data, "TRotaRota");       
        if(!empty($authUsuario['Usuario']['codigo_cliente'])){
            $this->data['TRotaRota']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
        }       
        $this->carregar_combos_rotas();
    }

    public function rotas_listagem() {
        App::Import('Component',array('DbbuonnyGuardian'));
        $this->pageTitle = 'Rotas';
        $this->loadModel('TRotaRota');
        $this->loadModel('TRponRotaPonto');
        $this->carregar_combos_rotas();
        
        $filtros['TRotaRota'] = $this->Filtros->controla_sessao($this->data, "TRotaRota");      
        $codigo_cliente = !empty($filtros['TRotaRota']['codigo_cliente']) ? $filtros['TRotaRota']['codigo_cliente'] : NULL ; 
        $rotas = array();

        if(!empty($filtros['TRotaRota']['codigo_cliente'])) {

            if(empty($codigo_cliente)) {
                $this->Rota->invalidate('codigo_cliente', 'Por favor informe o cliente');
            }

            $joins = array(
                array(
                    'table' => "{$this->TRponRotaPonto->databaseTable}.{$this->TRponRotaPonto->tableSchema}.{$this->TRponRotaPonto->useTable}",
                    'alias' => "TRponRotaPontoOrigem",
                    'type' => 'INNER',
                    'conditions' => array('TRponRotaPontoOrigem.rpon_rota_codigo = TRotaRota.rota_codigo',
                                          'TRponRotaPontoOrigem.rpon_sequencia' => -1),
                ),
                array(
                    'table' => "{$this->TRponRotaPonto->databaseTable}.{$this->TRponRotaPonto->tableSchema}.{$this->TRponRotaPonto->useTable}",
                    'type' => 'INNER',
                    'alias' => "TRponRotaPontoDestino",
                    'conditions' => array('TRponRotaPontoDestino.rpon_rota_codigo = TRotaRota.rota_codigo', 
                                          'TRponRotaPontoDestino.rpon_sequencia' => -2),
                )
            );
            $conditions = $this->TRotaRota->converteFiltroEmCondition($filtros);
            $fields = array('TRponRotaPontoOrigem.rpon_descricao',
                            'TRponRotaPontoDestino.rpon_descricao',
                            'TRotaRota.rota_codigo',
                            'TRotaRota.rota_codigo_externo',
                            'TRotaRota.rota_descricao',
                            'TRotaRota.rota_ativo',
                            'TRotaRota.rota_data_ultima_atualizacao_custos',
                            'TRotaRota.rota_previsao_valor_pedagio',
                            'TRotaRota.rota_previsao_distancia',
                            'TRotaRota.rota_previsao_litros_combustivel',
                            'TRotaRota.rota_previsao_valor_combustivel',
                            'TRotaRota.rota_observacao');
            $order = array('TRotaRota.rota_codigo');
            $limit = '50';
            $this->paginate['TRotaRota']  = array(
            'fields'        => $fields,
            'conditions'    => $conditions,
            'order'         => $order,
            'limit'         => $limit,
            'joins'         => $joins,
            );
            $rotas = $this->paginate('TRotaRota');
        }
        $this->set(compact('rotas', 'codigo_cliente'));
    }

    public function adicionar_rota($codigo_cliente){
        $this->pageTitle = 'Incluir Rota';
        $this->loadModel('Cliente');
        $this->loadModel('TRotaRota');
        $this->loadModel('TPjurPessoaJuridica');
        $this->loadModel('TTparTipoParada');
        $this->loadModel('TRponRotaPonto');

        if($this->RequestHandler->isPost()){

            $cliente = $this->Cliente->carregar($codigo_cliente);
            $cliente_pjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($cliente['Cliente']['codigo_documento']);
            $this->data['TRotaRota']['rota_pess_oras_codigo_dono'] = $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];

            foreach($this->data['TRotaRota']['Itinerario'] as $key => $destino){
                if(empty($destino['refe_codigo_destino'])){
                    $this->TRotaRota->validationErrors['Itinerario'][$key]['refe_codigo_destino_visual'] = 'Informe o alvo.';
                }
                if(empty($destino['tipo_parada'])){
                    $this->TRotaRota->validationErrors['Itinerario'][$key]['tipo_parada'] = 'Informe o tipo de parada.';
                }
            }

            if(empty($this->data['TRotaRota']['refe_codigo_origem'])){
                $this->TRotaRota->invalidate('refe_codigo_origem_visual','Informe o alvo de origem.');
            }
            if(empty($this->data['TRotaRota']['rota_descricao'])){
                $this->TRotaRota->invalidate('rota_descricao','Informe a descrição.');
            }

            $conditions = array('rota_pess_oras_codigo_dono' => $this->data['TRotaRota']['rota_pess_oras_codigo_dono'],
                                'rota_codigo_externo' => $this->data['TRotaRota']['rota_codigo_externo']);
            $validar_quantia = 0;

            if(!empty($this->data['TRotaRota']['rota_codigo_externo']) && !empty($codigo_cliente)) {
                $validar_quantia = $this->TRotaRota->find('count',array('conditions' => $conditions));
            }
            if($validar_quantia > 0) {
                $this->TRotaRota->invalidate('rota_codigo_externo', 'O codigo já está sendo ultilizado');
            }

            if(empty($this->TRotaRota->validationErrors)){
                $itinerario = array();
                foreach($this->data['TRotaRota']['Itinerario'] as $destino){
                    $itinerario[] = $destino;
                }
                $this->data['TRotaRota']['Itinerario'] = $itinerario;

                if($this->data['TRotaRota']['monitorar_retorno']){
                    $this->data['TRotaRota']['Itinerario'][] = array(
                        'refe_codigo_destino' => $this->data['TRotaRota']['refe_codigo_origem'],
                        'tipo_parada' => 5,
                    );
                }

                App::Import('Component',array('Maplink'));
                $this->Maplink = new MaplinkComponent();
                $dados_de_custo =  $this->Maplink->calcula_tempo_de_varios_alvos($this->data['TRotaRota']);
                $this->data['TRotaRota']['rota_previsao_valor_combustivel'] = !empty($dados_de_custo['total_combustivel']) ? $dados_de_custo['total_combustivel'] : 0;
                $this->data['TRotaRota']['rota_previsao_litros_combustivel'] = !empty($dados_de_custo['quantia_combustivel']) ? $dados_de_custo['quantia_combustivel'] : 0;
                $this->data['TRotaRota']['rota_previsao_valor_pedagio'] = !empty($dados_de_custo['total_pedagio']) ? $dados_de_custo['total_pedagio'] : 0;
                $this->data['TRotaRota']['rota_previsao_distancia'] = !empty($dados_de_custo['total_distancia']) ? $dados_de_custo['total_distancia'] : NULL;              
                $this->data['TRotaRota']['rota_data_ultima_atualizacao_custos'] = !empty($dados_de_custo['data_atualizacao']) ? $dados_de_custo['data_atualizacao'] : NULL;              

                if(!empty($this->data['TRotaRota']['rota_coordenadaspipe'])){
                    if($this->TRotaRota->incluirRotaComPontos($this->data,$this->data['TRotaRota']['monitorar_retorno'])){
                        $this->BSession->setFlash('save_success');
                        if(!$this->RequestHandler->isAjax()){
                            $this->redirect(array('action' => 'rotas'));
                            exit;
                        }
                    } else {
                        $this->BSession->setFlash('save_error');
                    }
                }else {
                    $this->BSession->setFlash(array(MSGT_ERROR, 'Não foi possível gerar a rota'));
                }
            }
            
        } else {
            $cliente = $this->Cliente->carregar($codigo_cliente);
            $cliente_pjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($cliente['Cliente']['codigo_documento']);
            $this->data['TRotaRota']['rota_pess_oras_codigo_dono'] = $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
        }

        $tipo_parada    = $this->TTparTipoParada->listarParaFormulario();

        $this->set(compact('codigo_cliente','tipo_parada'));
    }

    private function getUsuarioAdministrador($apenas_admin_ti = false) {
        $authUsuario = $this->BAuth->user();
        if ( $authUsuario['Usuario']['codigo_uperfil'] == Uperfil::ADMIN || $authUsuario['Usuario']['admin'] === 1) {
            if ($apenas_admin_ti && $authUsuario['Usuario']['codigo_uperfil'] != Uperfil::ADMIN) {
                $usuario_administrador = false;
            } else {
                $usuario_administrador  = true;                
            }
        } else {
            $usuario_administrador = false;
        }
        $this->set(compact('usuario_administrador'));
        return $usuario_administrador;
    }

    public function editar_rota($rota_codigo){
        $this->pageTitle = 'Alterar Rota';
        $this->loadModel('TRotaRota');
        $this->loadModel('TPjurPessoaJuridica');
        $this->loadModel('Cliente');
        $this->loadModel('TRponRotaPonto');
        $this->loadModel('TRefeReferencia');
        $this->loadModel('TTparTipoParada');

        $rota = $this->TRotaRota->carregar($rota_codigo);
        $rota_pontos = $this->TRponRotaPonto->listarPorRota($rota_codigo);

        $cliente_pjur = $this->TPjurPessoaJuridica->carregar($rota['TRotaRota']['rota_pess_oras_codigo_dono']);
        $cliente = $this->Cliente->carregarPorDocumento($cliente_pjur['TPjurPessoaJuridica']['pjur_cnpj']);


        if(isset($usuario['Uperfil']['codigo_tipo_perfil']) && $usuario['Uperfil']['codigo_tipo_perfil']){
            $this->Session->write('Auth.Usuario.codigo_tipo_perfil', $usuario['Uperfil']['codigo_tipo_perfil']);
        }

        $usuario_administrador = $this->getUsuarioAdministrador(true);

        if($this->RequestHandler->isPost()){
            if ($usuario_administrador) {
                $contador = count($this->data['TRotaRota']['Itinerario']) - 1;
                $itinerario_lista = array();
                foreach($this->data['TRotaRota']['Itinerario'] as $key => $destino){
                 if(empty($destino['refe_codigo_destino'])){
                     $this->TRotaRota->validationErrors['Itinerario'][$contador]['refe_codigo_destino_visual'] = 'Informe o alvo.';
                 }
                 if(empty($destino['tipo_parada'])){
                     $this->TRotaRota->validationErrors['Itinerario'][$contador]['tipo_parada'] = 'Informe o tipo de parada.';
                 }
                 $itinerario_lista[$contador] = $destino;
                 $contador--;
                }
                $this->data['TRotaRota']['Itinerario'] = $itinerario_lista;
                if(empty($this->data['TRotaRota']['refe_codigo_origem'])){
                    $this->TRotaRota->invalidate('refe_codigo_origem_visual','Informe o alvo de origem.');
                }
            }
            if(empty($this->data['TRotaRota']['rota_descricao'])){
                $this->TRotaRota->invalidate('rota_descricao','Informe a descrição.');
            }
            $conditions = array('rota_pess_oras_codigo_dono' => !empty($rota['TRotaRota']['rota_pess_oras_codigo_dono']) ? $rota['TRotaRota']['rota_pess_oras_codigo_dono'] : 99999 ,
                                'rota_codigo_externo' => $this->data['TRotaRota']['rota_codigo_externo'],
                                'rota_codigo !=' => $rota_codigo);
            $validar_quantia = 0;

            if(!empty($this->data['TRotaRota']['rota_codigo_externo'])) {
                $validar_quantia = $this->TRotaRota->find('count',array('conditions' => $conditions));
            }

            if($validar_quantia > 0) {
                $this->TRotaRota->invalidate('rota_codigo_externo', 'O codigo já está sendo ultilizado');
            }

            if(empty($this->TRotaRota->validationErrors)){
                $monitorar_retorno = false;
                if ($usuario_administrador) {
                    $itinerario = array();
                    foreach($this->data['TRotaRota']['Itinerario'] as $destino){
                        $itinerario[] = $destino;
                    }
                    $this->data['TRotaRota']['Itinerario'] = $itinerario;
                    //debug($this->data);
                    if($this->data['TRotaRota']['monitorar_retorno']){
                        $this->data['TRotaRota']['Itinerario'][] = array(
                            'refe_codigo_destino' => $this->data['TRotaRota']['refe_codigo_origem'],
                            'tipo_parada' => 5,
                        );
                        $monitorar_retorno = true;
                    }
                }

                App::Import('Component',array('Maplink'));
                $this->Maplink = new MaplinkComponent();
                $dados_de_custo =  $this->Maplink->calcula_tempo_de_varios_alvos($this->data['TRotaRota']);
                $this->data['TRotaRota']['rota_previsao_valor_combustivel'] = !empty($dados_de_custo['total_combustivel']) ? $dados_de_custo['total_combustivel'] : 0;
                $this->data['TRotaRota']['rota_previsao_litros_combustivel'] = !empty($dados_de_custo['quantia_combustivel']) ? $dados_de_custo['quantia_combustivel'] : 0;
                $this->data['TRotaRota']['rota_previsao_valor_pedagio'] = !empty($dados_de_custo['total_pedagio']) ? $dados_de_custo['total_pedagio'] : 0;
                $this->data['TRotaRota']['rota_previsao_distancia'] = !empty($dados_de_custo['total_distancia']) ? $dados_de_custo['total_distancia'] : 0;
                $this->data['TRotaRota']['rota_data_ultima_atualizacao_custos'] = !empty($dados_de_custo['data_atualizacao']) ? $dados_de_custo['data_atualizacao'] : NULL;
                if($this->TRotaRota->atualizarRotaComPontos($this->data,$monitorar_retorno,$usuario_administrador)){
                    $this->BSession->setFlash('save_success');
                    if(!$this->RequestHandler->isAjax()){
                        $this->redirect(array('action' => 'rotas'));
                        exit;
                    }
                } else {
                    $this->BSession->setFlash('save_error');
                }
            }
            
        }else{
            $this->data = $rota;
            $itinerario = array();

            $origem = reset(array_filter($rota_pontos,function($var){ return $var['TRponRotaPonto']['rpon_sequencia'] == -1; }));
            $destino = reset(array_filter($rota_pontos,function($var){ return $var['TRponRotaPonto']['rpon_sequencia'] == -2; }));
            $itinerario = array_filter($rota_pontos,function($var){ return $var['TRponRotaPonto']['rpon_sequencia'] != -2 && $var['TRponRotaPonto']['rpon_sequencia'] != -1; });
            usort($itinerario, function($a,$b){ return $a['TRponRotaPonto']['rpon_sequencia'] == $b['TRponRotaPonto']['rpon_sequencia'] ? 0 : $a['TRponRotaPonto']['rpon_sequencia'] < $b['TRponRotaPonto']['rpon_sequencia'] ? -1 : 1; });

            if($origem['TRponRotaPonto']['rpon_refe_codigo'] == $destino['TRponRotaPonto']['rpon_refe_codigo']){
                $this->data['TRotaRota']['monitorar_retorno'] = true;
            }

            $this->data['TRotaRota']['refe_codigo_origem'] = $origem['TRponRotaPonto']['rpon_refe_codigo'];
            $this->data['TRotaRota']['refe_codigo_origem_visual'] = $origem['TRponRotaPonto']['rpon_descricao'];
            
            $rota_itinerario = array();
            foreach($itinerario as $destino){
                $rota_itinerario[] = array(
                    'refe_codigo_destino' => $destino['TRponRotaPonto']['rpon_refe_codigo'],
                    'refe_codigo_destino_visual' => $destino['TRponRotaPonto']['rpon_descricao'],
                    'tipo_parada' => $destino['TRponRotaPonto']['rpon_tpar_codigo'],
                );
            }
            krsort($rota_itinerario);
            $this->data['TRotaRota']['Itinerario'] = array();
            foreach($rota_itinerario as $destino){
                $this->data['TRotaRota']['Itinerario'][] = $destino;
            }
        }

        $tipo_parada    = $this->TTparTipoParada->listarParaFormulario();
        
        $this->set(compact('cliente','rota','rota_pontos','tipo_parada'));
    }

    public function remover_rota($rota_codigo){
        $this->loadModel('TRotaRota');
        $this->loadModel('TVrotViagemRota');
        $conditions = array('rota_codigo' => $rota_codigo);
        $rota = $this->TRotaRota->find('first',compact('conditions'));

        $retorno = null;
        if($rota){
            $viagens_rotas = $this->TVrotViagemRota->listarPorRota($rota_codigo);
            try{
                $this->TVrotViagemRota->query('BEGIN TRANSACTION');
                $this->TRotaRota->query('BEGIN TRANSACTION');
                foreach($viagens_rotas as $viagem_rota){
                    $this->TVrotViagemRota->delete($viagem_rota['TVrotViagemRota']['vrot_codigo']);
                }
                if($this->TRotaRota->delete($rota_codigo)){
                    $retorno = 'Registro removido com sucesso';
                }else{
                    $retorno = 'Erro ao apagar registro';
                }
                $this->TVrotViagemRota->commit();
                $this->TRotaRota->commit();
            }catch(Exception $e){
                $this->TVrotViagemRota->rollback();
                $this->TRotaRota->rollback();
                $retorno = 'Erro ao apagar registro';
            }
            
        } else {
            $retorno = 'Rota não encontrada.';
        }
        $this->set(compact('retorno'));
        $this->redirect(array('action' => 'rotas'));
    }


}
