<?php
class PrestadoresController extends AppController {
    public $name = 'Prestadores';
    var $uses = array(
        'Prestador', 'VEndereco', 'PrestadorEndereco','PrestadorContato', 'Endereco',
        'EnderecoBairro', 'EnderecoCep', 'EnderecoCidade', 'EnderecoEstado', 'TipoContato'
    );

    function index() {
        $this->data['Prestador'] = $this->Filtros->controla_sessao($this->data, $this->Prestador->name);                
    }
    
    function carrega_combos_formulario() { 
        $enderecos = (isset($this->data['VEndereco']['endereco_cep']) ? $this->VEndereco->listarParaComboPorCep($this->data['VEndereco']['endereco_cep']) : array());
        $this->set(compact('enderecos'));
    }

    public function listagem( $mapa_prestadores = FALSE, $destino = FALSE ){        
        $filtros = $this->Filtros->controla_sessao($this->data, 'Prestador');
        if(count($filtros) > 0){
            if(!empty($filtros['latitude']) && !empty($filtros['longitude']) && isset($filtros['raio'])){
                $filtros['latitude']        = str_replace(',', '.', $filtros['latitude']);
                $filtros['longitude']       = str_replace(',', '.', $filtros['longitude']);
                $filtros['latitude_min']    = $filtros['latitude'] - ($filtros['raio']) / 111.319;
                $filtros['latitude_max']    = $filtros['latitude'] + ($filtros['raio']) / 111.319;
                $filtros['longitude_min']   = $filtros['longitude'] - ($filtros['raio']) / 111.319;
                $filtros['longitude_max']   = $filtros['longitude'] + ($filtros['raio']) / 111.319;
            }
            
            if( $mapa_prestadores == TRUE ){
                unset($filtros['nome']);
                unset($filtros['codigo_documento']);
                $prestadores_mapa = $this->Prestador->listaPrestadores( $filtros );
            }else{
                $filtros = array(
                        'nome' => $filtros['nome'],
                        'codigo_documento' => $filtros['codigo_documento']
                    );
            }
            $conditions = $this->Prestador->converteFiltroEmCondition( $filtros );            

            $query_contato = " CAST(STUFF(( SELECT '|' + Contatos.descricao
                FROM ".$this->PrestadorContato->databaseTable.".".$this->PrestadorContato->tableSchema.".".$this->PrestadorContato->useTable." AS Contatos 
                WHERE Contatos.codigo_prestador = Prestador.codigo FOR XML PATH('')),1,1,'') AS text)";
            $fields = array(
                'Prestador.codigo',
                'Prestador.nome',
                'Prestador.codigo_documento', 
                'Endereco.descricao as Prestador__endereco', 
                'PrestadorEndereco.numero as Prestador__numero', 
                'EnderecoBairro.descricao as Prestador__bairro', 
                'EnderecoCidade.descricao as Prestador__cidade', 
                'EnderecoEstado.descricao as Prestador__estado',
                'EnderecoCep.cep as Prestador__cep', 
                'PrestadorEndereco.latitude as Prestador__latitude',
                'PrestadorEndereco.longitude as Prestador__longitude',
                "($query_contato) as Prestador__contato"
            );
            if(!empty($filtros['latitude']) && !empty($filtros['longitude'])){
                $fields[] = "publico.distancia_dois_pontos(".$filtros['latitude'].", ".$filtros['longitude'].", PrestadorEndereco.latitude, PrestadorEndereco.longitude) as Prestador__distancia";
            }else{
                $fields[] = '0 as Prestador__distancia';
            }
            $this->Prestador->virtualFields['endereco'] = 'Prestador__endereco';
            $this->Prestador->virtualFields['numero'] = 'Prestador__numero';
            $this->Prestador->virtualFields['bairro'] = 'Prestador__bairro';
            $this->Prestador->virtualFields['cidade'] = 'Prestador__cidade';
            $this->Prestador->virtualFields['estado'] = 'Prestador__estado';
            $this->Prestador->virtualFields['cep'] = 'Prestador__cep';
            $this->Prestador->virtualFields['latitude'] = 'Prestador__latitude';
            $this->Prestador->virtualFields['longitude'] = 'Prestador__longitude';
            $this->Prestador->virtualFields['contato'] = 'Prestador__contato';
            $this->Prestador->virtualFields['distancia'] = 'Prestador__distancia';

            $this->paginate['Prestador'] = array(
                'limit'  => ($destino == 'prestadores_buscar_codigo' ? 10 : 50),
                'order'  => 'Prestador.nome',
                'fields' => $fields,
                'joins' => array( 
                    array(
                        "table" => "{$this->PrestadorEndereco->databaseTable}.{$this->PrestadorEndereco->tableSchema}.{$this->PrestadorEndereco->useTable}",
                        'alias' => 'PrestadorEndereco',
                        "type"  => "LEFT",
                        "conditions" => array("PrestadorEndereco.codigo_prestador = Prestador.codigo")
                    ),
                    array(
                        "table" => "{$this->Endereco->databaseTable}.{$this->Endereco->tableSchema}.{$this->Endereco->useTable}",
                        'alias' => 'Endereco',
                        "type"  => "LEFT",
                        "conditions" => array("Endereco.codigo = PrestadorEndereco.codigo_endereco")
                    ),
                    array(
                        "table" => "{$this->EnderecoBairro->databaseTable}.{$this->EnderecoBairro->tableSchema}.{$this->EnderecoBairro->useTable}",
                        'alias' => 'EnderecoBairro',
                        "type"  => "LEFT",
                        "conditions" => array("EnderecoBairro.codigo = Endereco.codigo_endereco_bairro_inicial")
                    ),
                    array(
                        "table" => "{$this->EnderecoCidade->databaseTable}.{$this->EnderecoCidade->tableSchema}.{$this->EnderecoCidade->useTable}",
                        'alias' => 'EnderecoCidade',
                        "type"  => "LEFT",
                        "conditions" => array("EnderecoCidade.codigo = Endereco.codigo_endereco_cidade")
                    ),                
                    array(
                        "table" => "{$this->EnderecoEstado->databaseTable}.{$this->EnderecoEstado->tableSchema}.{$this->EnderecoEstado->useTable}",
                        'alias' => 'EnderecoEstado',
                        "type"  => "LEFT",
                        "conditions" => array("EnderecoEstado.codigo = EnderecoCidade.codigo_endereco_estado")
                    ),                
                    array(
                        "table" => "{$this->EnderecoCep->databaseTable}.{$this->EnderecoCep->tableSchema}.{$this->EnderecoCep->useTable}",
                        'alias' => 'EnderecoCep',
                        "type"  => "LEFT",
                        "conditions" => array("EnderecoCep.codigo = Endereco.codigo_endereco_cep")
                    )
                ),
                'conditions' => $conditions,
            );
            $prestadores = $this->paginate('Prestador');
            $latitude    = !empty($filtros['latitude'])  ? $filtros['latitude'] : '-23.6824124';
            $longitude   = !empty($filtros['longitude']) ? $filtros['longitude'] : '-46.5952992';
            $this->set(compact('prestadores','prestadores_mapa', 'latitude', 'longitude', 'destino'));
            if (isset($this->passedArgs['searcher']))
                $this->set('input_id', str_replace('-search', '', $this->passedArgs['searcher']));
        }
    }    

    function incluir() {
        $this->pageTitle = 'Incluir Prestador';
        if($this->RequestHandler->isPost()) {
            if ($this->Prestador->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'editar', $this->Prestador->id));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        $this->carrega_combos_formulario();
    }

    function editar($codigo_Prestador) {
        $this->pageTitle = 'Atualizar Prestador';
        if (!empty($this->data)) {
            if ($this->Prestador->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->Prestador->carregarParaEdicao($codigo_Prestador);
        }
        $this->carrega_combos_formulario();
    }

    function mapa_prestadores(){
        if( !empty($this->passedArgs['searcher'])){
            $this->buscar_codigo();
        } else{
            $this->pageTitle = 'Mapa de Prestadores';
            $this->data['Prestador'] = $this->Filtros->controla_sessao($this->data, 'Prestador');
            $input_id =NULL;
            $this->set(compact('input_id'));
        }
    }

    function mapa_prestadores_listagem( $exportar_excel = FALSE, $destino = FALSE){
        $this->listagem(TRUE, $destino);
    }    

    function excluir($codigo_prestador) {
        if ( $codigo_prestador) {
            if ($this->Prestador->delete($codigo_prestador)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    function buscar_codigo() {
        $this->layout = 'ajax_placeholder';
        $input_id = $this->passedArgs['searcher'];
        if($this->passedArgs['latitude'] && $this->passedArgs['longitude']){
            $this->data['Prestador']['raio'] = 100;
            $this->data['Prestador']['latitude'] = $this->passedArgs['latitude'] != 'undefined' ? $this->passedArgs['latitude'] : 0;
            $this->data['Prestador']['longitude'] = $this->passedArgs['longitude'] != 'undefined' ? $this->passedArgs['longitude'] : 0;                        
        }
        $this->data['Prestador'] = $this->Filtros->controla_sessao($this->data, $this->Prestador->name);
        $this->set(compact('input_id'));
    }
    function busca_por_codigo(){
        $this->layout = 'ajax';
        $codigo = $_POST['codigo'];
        $retorno = array();        
        if(is_numeric($codigo)){
            $retorno = $this->Prestador->listaPrestadores(array('codigo_prestador' => $codigo));            
        }else {
            $retorno[0]['Prestador']['nome'] = !empty($retorno[0]['Prestador']['nome']) ? $retorno[0]['Prestador']['nome'] : '';
        }
        die(json_encode($retorno[0]));
    }
    
    public function carregar_lat_lgn_por_endereco( $endereco ){        
        $filtros    = array('endereco' => $endereco );
        App::import('Component','ApiGoogle');
        // if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();
        // }
        // else if(Ambiente::TIPO_MAPA == 2) {
        //     App::import('Component',array('ApiGeoPortal'));
        //     $this->ApiMaps = new ApiGeoPortalComponent();
        // }
        $lat_lgn = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($endereco);
        $retorno = array('latitude' => $lat_lgn[0], 'longitude' => $lat_lgn[1]);
        echo json_encode($retorno);
        exit;
    }


    public function carregar_combos_listagem_prestadores_viagens() {
        $this->loadModel('EmbarcadorTransportador');
        $this->loadModel('Tecnologia');
        $authUsuario = $this->BAuth->user();
        $tecnologia = array();
        $embarcadores = array();
        $transportadores = array();
        $agrupamento = array(1 => 'Transportador',
                             2 => 'Embarcador',
                             3 => 'Tecnologia',
                             4 => 'Prestador');

        $valores = array(1 => 'Sim',
                         2 => 'Não');

        $tecnologia = $this->Tecnologia->lista();
        if(!empty($authUsuario['Usuario']['codigo_cliente'])) {
            $this->data['PrestadoresPostgres']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
            $dados = $this->EmbarcadorTransportador->dadosPorCliente($this->data['PrestadoresPostgres']['codigo_cliente']);
            $embarcadores = $dados['embarcadores'];
            $transportadores = $dados['transportadores'];
        }elseif(!empty($this->data['PrestadoresPostgres']['codigo_cliente'])) {
            $dados = $this->EmbarcadorTransportador->dadosPorCliente($this->data['PrestadoresPostgres']['codigo_cliente']);
            $embarcadores = $dados['embarcadores'];
            $transportadores = $dados['transportadores'];
        }

       if(empty($this->data['PrestadoresPostgres'])) {
            $this->data['PrestadoresPostgres']['data_envio_prestador_inicial'] = date('01/m/Y');
            $this->data['PrestadoresPostgres']['data_envio_prestador_final'] = date('d/m/Y');
        }
        $this->set(compact('tecnologia', 'embarcadores', 'transportadores', 'agrupamento', 'valores'));
    }

    public function analitico($new_window = FALSE, $exibir_valores = FALSE) {
        $this->pageTitle = 'Analítico do Acionamento de Pronta Resposta';
        $filtrado = FALSE; 
        if($new_window){
            $this->layout = 'new_window';
            $filtrado = TRUE; 
        }
        $this->data['PrestadoresPostgres'] = $this->Filtros->controla_sessao($this->data, "PrestadoresPostgres");
        if($exibir_valores) {
            $this->Session->write('exibir_valores', TRUE);
        }else {
            unset($exibir_valores);
            $this->Session->delete('exibir_valores');
        }
        $this->carregar_combos_listagem_prestadores_viagens();
        $this->set(compact('filtrado', 'exibir_valores'));
    }

    public function analitico_listagem($export = false) {
        $this->layout = 'ajax';
        $this->data['PrestadoresPostgres'] = $this->Filtros->controla_sessao($this->data, "PrestadoresPostgres");
        $exibir_valores = $this->Session->read('exibir_valores');
        $prestadores = $this->Prestador->listaPrestadoresRelacionadosASm($this->data['PrestadoresPostgres']);
        $this->paginate['Prestador'] = $prestadores;
        $totais = $this->paginate['Prestador']['totais'];
        $prestadores = $this->paginate('Prestador');
        $this->set(compact('prestadores'));
        if($export){
            $sql =  $prestadores = $this->Prestador->listaPrestadoresRelacionadosASm($this->data['PrestadoresPostgres'], null, 'sql');
            $this->exportar_acionamento_prestadores($sql);
        }
        $this->set(compact('exibir_valores', 'totais'));
       
    }

    public function sintetico() {
        $this->pageTitle = 'Sintético do Acionamento de Pronta Resposta';
        $this->data['PrestadoresPostgres'] = $this->Filtros->controla_sessao($this->data, "PrestadoresPostgres");
        $exibir_valores = $this->Session->read('exibir_valores');
        if(isset($exibir_valores)) {
            unset($exibir_valores);
            $this->Session->delete('exibir_valores');
        }
        $this->carregar_combos_listagem_prestadores_viagens();
        $filtrado = FALSE;      
        $this->set(compact('filtrado'));
    }

    public function sintetico_listagem() {
        $this->data['PrestadoresPostgres'] = $this->Filtros->controla_sessao($this->data, "PrestadoresPostgres");
        $agrupamento = $this->data['PrestadoresPostgres']['agrupamento'];
        $prestadores = $this->Prestador->listaPrestadoresRelacionadosASm($this->data['PrestadoresPostgres'], $agrupamento, 'all');
        $this->set(compact('prestadores','agrupamento'));
    }

    public function exportar_acionamento_prestadores($query) {
        $exibir_valores = $this->Session->read('exibir_valores');
        $campos_custos = null;
        if(isset($exibir_valores) && $exibir_valores) {
            $campos_custos = 'Valor Honorários;Valor Despesas;Quilômetro;';
        }

        $dbo = $this->Prestador->getDataSource();
        header('Content-type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment; filename="%s"', basename('relatorio_pronta_resposta.csv')));
        header('Pragma: no-cache');
        echo iconv('UTF-8', 'ISO-8859-1', 'SM;Embarcador;Transportador;Inicio Real (SM);Fim Real (SM);Placa;Tecnologia;Prestador;Data de Envio Prestador;'.$campos_custos)."\n";

        $dados = $dbo->fetchAll($query);
        foreach ($dados as $dado) {
            $linha = '"'.$dado['HistoricoSm']['codigo_sm'].'";';
            $linha .= '"'.$dado['Embarcador']['razao_social'].'";';
            $linha .= '"'.$dado['Transportador']['razao_social'].'";';
            $linha .= '"'.$dado['Recebsm']['data_inicio'].'";';
            $linha .= '"'.$dado['Recebsm']['data_final'].'";';
            $linha .= '"'.$dado['Recebsm']['Placa'].'";';
            $linha .= '"'.$dado['Tecnologia']['descricao'].'";';
            $linha .= '"'.$dado['Prestador']['nome'].'";';
            $linha .= '"'.$dado['HistoricoSmPrestador']['data_inclusao'].'";';
            if(isset($exibir_valores) && $exibir_valores) {
                $valor_honorarios = !empty($dado['HistoricoSmPrestador']['valor_honorarios']) ? number_format($dado['HistoricoSmPrestador']['valor_honorarios'],2) : NULL;
                $valor_despesas = !empty($dado['HistoricoSmPrestador']['valor_despesas']) ? number_format($dado['HistoricoSmPrestador']['valor_despesas'],2) : NULL;
                $quantia_km = !empty($dado['HistoricoSmPrestador']['quantia_km']) ? number_format($dado['HistoricoSmPrestador']['quantia_km'],2) : NULL;
                $linha .= '"'.(!empty($valor_honorarios) ? $valor_honorarios : NULL).'";';
                $linha .= '"'.(!empty($valor_honorarios) ? $valor_honorarios : NULL).'";';
                $linha .= '"'.(!empty($quantia_km) ? $quantia_km : NULL).'";';
            }   
            $linha .= "\n";
            echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
        }
        die();

    }

    public function alterar_valores($codigo, $pagina = null) {
        $this->loadModel('HistoricoSmPrestador');
        if(!empty($this->data)) { 
            $honorarios = $this->data['HistoricoSmPrestador']['valor_honorarios'];
            $honorarios = str_replace('.', '', $honorarios);
            $honorarios = str_replace(',', '.', $honorarios);

            $despesas = $this->data['HistoricoSmPrestador']['valor_despesas'];
            $despesas = str_replace('.', '', $despesas);
            $despesas = str_replace(',', '.', $despesas);

            $quantia_km = $this->data['HistoricoSmPrestador']['quantia_km'];
            $quantia_km = str_replace('.', '', $quantia_km);
            $quantia_km = str_replace(',', '.', $quantia_km);

            $this->data['HistoricoSmPrestador']['valor_honorarios'] = $honorarios;
            $this->data['HistoricoSmPrestador']['valor_despesas'] = $despesas;

            if($this->HistoricoSmPrestador->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        }else {
            $conditions = array('HistoricoSmPrestador.codigo' => $codigo);
            $this->data = $this->HistoricoSmPrestador->find('first', compact('conditions'));
            if(!empty($this->data['HistoricoSmPrestador']['valor_honorarios']))
            $this->data['HistoricoSmPrestador']['valor_honorarios'] = number_format($this->data['HistoricoSmPrestador']['valor_honorarios'], 2);
            if(!empty($this->data['HistoricoSmPrestador']['valor_honorarios']))
            $this->data['HistoricoSmPrestador']['valor_despesas'] = number_format($this->data['HistoricoSmPrestador']['valor_despesas'], 2);
            if(!empty($this->data['HistoricoSmPrestador']['quantia_km']))
            $this->data['HistoricoSmPrestador']['quantia_km'] = number_format($this->data['HistoricoSmPrestador']['quantia_km'], 2);
        }
        $this->set(compact('codigo', 'pagina'));
    }



}
?>