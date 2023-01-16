<?php
class ReguladoresController extends AppController {
    public $name = 'Reguladores';
    var $uses = array(
        'Regulador','VEndereco','ReguladorEndereco','ReguladorContato','Endereco','EnderecoBairro',
        'EnderecoCep','EnderecoCidade','EnderecoEstado','ReguladorRegiao'
    );

    function index() {
        $this->data['Regulador'] = $this->Filtros->controla_sessao($this->data, $this->Regulador->name);                
    }
    
    function carrega_combos_formulario() { 
        $enderecos = (isset($this->data['VEndereco']['endereco_cep']) ? $this->VEndereco->listarParaComboPorCep($this->data['VEndereco']['endereco_cep']) : array());
        $this->set(compact('enderecos'));
    }

    public function listagem($exibe_mapa=FALSE){   
        $filtros = $this->Filtros->controla_sessao($this->data, 'Regulador');         
        $conditions = $this->Regulador->converteFiltroEmCondition( $filtros );
        $query_contato = $this->ReguladorContato->find('sql', array(
            'conditions' => array('codigo_regulador = Regulador.codigo'),
            'fields'     => array('descricao'),
            'limit'      => 1,
        ));
        
        $fields = array(
            'Regulador.codigo',
            'Regulador.nome',
            'Regulador.codigo_documento', 
            'Endereco.descricao', 
            'ReguladorEndereco.numero', 
            'EnderecoBairro.descricao', 
            'EnderecoCidade.descricao', 
            'EnderecoEstado.descricao',
            'EnderecoCep.cep', 
            'ReguladorEndereco.latitude',
            'ReguladorEndereco.longitude',
            "($query_contato) AS contato"
        );

        $this->paginate['Regulador'] = array(
            'limit'  => 50,
            'order'  => 'Regulador.nome',
            'fields' => $fields,
            'joins' => array( 
                array(
                    "table" => "{$this->ReguladorEndereco->databaseTable}.{$this->ReguladorEndereco->tableSchema}.{$this->ReguladorEndereco->useTable}",
                    'alias' => 'ReguladorEndereco',
                    "type"  => "LEFT",
                    "conditions" => array("ReguladorEndereco.codigo_regulador = Regulador.codigo")
                ),
                array(
                    "table" => "{$this->Endereco->databaseTable}.{$this->Endereco->tableSchema}.{$this->Endereco->useTable}",
                    'alias' => 'Endereco',
                    "type"  => "LEFT",
                    "conditions" => array("Endereco.codigo = ReguladorEndereco.codigo_endereco")
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
        $reguladores = $this->paginate('Regulador');
        $this->set(compact('reguladores','reguladores_mapa','destino'));        
    }    

    function incluir() {
        $this->pageTitle = 'Incluir Regulador';
        if($this->RequestHandler->isPost()) {
            if ($this->Regulador->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'editar', $this->Regulador->id));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        $this->carrega_combos_formulario();
    }

    function editar($codigo_regulador) {
        $this->pageTitle = 'Atualizar Regulador';
        if (!empty($this->data)) {
            if ($this->Regulador->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->Regulador->carregarParaEdicao($codigo_regulador);
        }
        $this->carrega_combos_formulario();
    }

    function excluir($codigo_regulador) {
        if ( $codigo_regulador) {
            if ($this->Regulador->delete($codigo_regulador)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
    }

    function mapa_reguladores(){
        $this->pageTitle = 'Mapa de Reguladores';
        $this->data['Regulador'] = $this->Filtros->controla_sessao($this->data, 'Regulador');
        $this->data['Regulador']['raio'] = 10;
    }

    public function mapa_reguladores_listagem( ){   
        $filtros = $this->Filtros->controla_sessao($this->data, 'Regulador');
        $conditions = array();
        $latitude    = '-23.6824124';
        $longitude   = '-46.5952992';
        if(!empty($filtros['latitude']) && !empty($filtros['longitude']) && !empty($filtros['raio'])){
            $filtros['raio'] = !empty($filtros['raio']) ? $filtros['raio'] : 10;
            $filtros['latitude']        = str_replace(',', '.', $filtros['latitude']);
            $filtros['longitude']       = str_replace(',', '.', $filtros['longitude']);
            $filtros['latitude_min']    = $filtros['latitude'] - ($filtros['raio']) / 111.319;
            $filtros['latitude_max']    = $filtros['latitude'] + ($filtros['raio']) / 111.319;
            $filtros['longitude_min']   = $filtros['longitude'] - ($filtros['raio']) / 111.319;
            $filtros['longitude_max']   = $filtros['longitude'] + ($filtros['raio']) / 111.319;
            $conditions['ReguladorRegiao.longitude BETWEEN ? AND ?']    = array($filtros['longitude_min'], $filtros['longitude_max']);            
            $conditions['ReguladorRegiao.latitude BETWEEN ? AND ?']     = array($filtros['latitude_min'], $filtros['latitude_max']);
            $latitude  = $filtros['latitude'];
            $longitude = $filtros['longitude'];        
            $reguladores_mapa   = $this->Regulador->listaReguladoresRegiao( $conditions );
            $query_contato      = $this->ReguladorContato->find('sql', array(
                'conditions' => array('codigo_regulador = Regulador.codigo'),
                'fields'     => array('descricao'),
                'limit'      => 1,
            ));        
            $fields = array(
                'Regulador.codigo',
                'Regulador.nome',
                'Regulador.codigo_documento', 
                'Endereco.descricao', 
                'ReguladorEndereco.numero', 
                'EnderecoBairro.descricao', 
                'EnderecoCidade.descricao', 
                'EnderecoEstado.descricao',
                'EnderecoCep.cep', 
                'ReguladorEndereco.latitude',
                'ReguladorEndereco.longitude',
                'ReguladorRegiao.longitude',
                'ReguladorRegiao.latitude',
                "($query_contato) AS contato"
            );

            $this->paginate['Regulador'] = array(
                'limit'  => 10,
                'order'  => 'Regulador.nome',
                'fields' => $fields,
                'joins' => array( 
                    array(
                        "table" => "{$this->ReguladorEndereco->databaseTable}.{$this->ReguladorEndereco->tableSchema}.{$this->ReguladorEndereco->useTable}",
                        'alias' => 'ReguladorEndereco',
                        "type"  => "LEFT",
                        "conditions" => array("ReguladorEndereco.codigo_regulador = Regulador.codigo")
                    ),
                    array(
                        "table" => "{$this->ReguladorRegiao->databaseTable}.{$this->ReguladorRegiao->tableSchema}.{$this->ReguladorRegiao->useTable}",
                        'alias' => 'ReguladorRegiao',
                        "type"  => "LEFT",
                        "conditions" => array("ReguladorRegiao.codigo_regulador = Regulador.codigo")
                    ),                
                    array(
                        "table" => "{$this->Endereco->databaseTable}.{$this->Endereco->tableSchema}.{$this->Endereco->useTable}",
                        'alias' => 'Endereco',
                        "type"  => "LEFT",
                        "conditions" => array("Endereco.codigo = ReguladorEndereco.codigo_endereco")
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
                    ),
                ),
                'conditions' => $conditions,
            );
            $reguladores = $this->paginate('Regulador');        
        }
        $this->set(compact('reguladores','reguladores_mapa','latitude', 'longitude'));
    }    

}
?>