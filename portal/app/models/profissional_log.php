<?php

class ProfissionalLog extends AppModel {

    var $name = 'ProfissionalLog';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'profissional_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    //var $foreignKeyLog = 'codigo_profissional';
    var $belongsTo = array(
        'Profissional' => array(
            'class' => 'Profissional',
            'foreignKey' => 'codigo_profissional'
        )
    );

    function bindLazyFicha() {
        $this->bindModel(array(
            'hasOne' => array(
                'Ficha' => array(
                    'className' => 'Ficha',
                    'foreignKey' => 'codigo_profissional_log'
            ))));
    }

    function unbindFicha() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'Ficha'
            )
        ));
    }

    public function converteFiltroEmCondition( $filtros ){
        $conditions = array();
        if( !empty($filtros['codigo_profissional'])){
            $conditions['ProfissionalLog.codigo_profissional'] = $filtros['codigo_profissional'];
        }
        if( !empty($filtros['nome'])){
            $conditions['ProfissionalLog.nome like'] = $filtros['nome'] . '%';
        }
        if( !empty($filtros['codigo'])){
            $conditions['ProfissionalLog.codigo'] = $filtros['codigo'];
        }        
        if( !empty($filtros['codigo_documento'])){            
            $conditions['ProfissionalLog.codigo_documento'] = str_replace(array('.','-'), '', $filtros['codigo_documento'] );
        }      
        // debug($conditions) ;
        return $conditions;
    }

    function buscaStatusPenultimaFicha($codigo_produto, $codigo_ficha_atual, $codigo_profissional){
        $this->Ficha = ClassRegistry::init('Ficha');
        $ficha_atual = $this->Ficha->findByCodigo($codigo_ficha_atual);
        $joins = array(
            array(
                'table'      => $this->Ficha->databaseTable.'.'.$this->Ficha->tableSchema.'.ficha',
                'alias'      => 'Ficha',
                'type'       => 'LEFT',
                'conditions' => 'ProfissionalLog.codigo = Ficha.codigo_profissional_log',
            )
        );
        
        // Busca a ultima ficha do cliente quando não é pesquisa de carreteiro.
        if($ficha_atual['Ficha']['codigo_profissional_tipo'] <> 1){
            $condicoes['codigo_cliente'] = $ficha_atual['Ficha']['codigo_cliente'];
        }
        
        $condicoes['Ficha.codigo_produto']                  = $codigo_produto;
        $condicoes['Ficha.ativo']                           = array(0,1);
        $condicoes['ProfissionalLog.codigo_profissional']      = $codigo_profissional;
        $condicoes['not']                                   = array('Ficha.codigo' => $codigo_ficha_atual);

        $status = $this->find('first', array(
                'fields' => 'Ficha.codigo_status',
                'conditions' => $condicoes,
                'order' => array('Ficha.data_inclusao DESC'),
                'joins' => $joins,
            )
        );
        return $status['Ficha']['codigo_status'];
    }
    
    function buscaStatusPenultimaFichaPorDocumento($codigo_produto, $codigo_ficha_atual, $documento_profissional){
        $this->Ficha = ClassRegistry::init('Ficha');
        $joins = array(
            array(
                'table'      => $this->Ficha->databaseTable.'.'.$this->Ficha->tableSchema.'.ficha',
                'alias'      => 'Ficha',
                'type'       => 'LEFT',
                'conditions' => 'ProfissionalLog.codigo = Ficha.codigo_profissional_log',
            )
        );
        
        $condicoes['Ficha.codigo_produto']                  = $codigo_produto;
        $condicoes['Ficha.ativo']                           = array(0,1);
        $condicoes['ProfissionalLog.codigo_documento']      = $documento_profissional;
        $condicoes['not']                                   = array('Ficha.codigo' => $codigo_ficha_atual);

        $status = $this->find('first', array(
                'fields' => 'Ficha.codigo_status',
                'conditions' => $condicoes,
                'order' => array('Ficha.data_inclusao DESC'),
                'joins' => $joins,
            )
        );
        return $status['Ficha']['codigo_status'];
    }

    function buscaStatusUltimaFichaPorDocumento($documento_profissional,$completo = false){
        $this->Ficha = ClassRegistry::init('Ficha');
        $this->Status = ClassRegistry::init('Status');
        $joins = array(
            array(
                'table'      => $this->Ficha->databaseTable.'.'.$this->Ficha->tableSchema.'.'.$this->Ficha->useTable,
                'alias'      => 'Ficha',
                'type'       => 'LEFT',
                'conditions' => 'ProfissionalLog.codigo = Ficha.codigo_profissional_log',
            ),

            array(
                'table'      => $this->Status->databaseTable.'.'.$this->Status->tableSchema.'.'.$this->Status->useTable,
                'alias'      => 'Status',
                'type'       => 'INNER',
                'conditions' => 'Ficha.codigo_status = Status.codigo',
            )
        );
        
        $condicoes['ProfissionalLog.codigo_documento']      = $documento_profissional;
        if($completo){
        	$fields = array('ProfissionalLog.nome','Status.codigo','Status.descricao');
        } else {
        	$fields = array('Status.codigo','Status.descricao');
        }

        $status = $this->find('first', array(
                'fields' 		=> $fields,
                'conditions' 	=> $condicoes,
                'order' 		=> array('Ficha.data_inclusao DESC'),
                'joins' 		=> $joins,
            )
        );

        return $status;
    }

    function profissionalEmPesquisa($codigo_documento, $codigo_profissional_tipo, $codigo_produto, $codigo_cliente){
        App::import('Model', 'ProfissionalTipo');
        App::import('Model', 'TipoPesquisa');
        
        $tipos_pesquisa = sprintf(
            '%d, %d, %d, %d',
            TipoPesquisa::PENDENTE,
            TipoPesquisa::AGUARDANDO_APROVACAO,
            TipoPesquisa::REGISTRO_BLOQUEADO_APROVACAO,
            TipoPesquisa::REGISTRO_BLOQUEADO_PESQUISA
        );

        $this->Ficha = ClassRegistry::init('Ficha');
        $this->FichaPesquisa = ClassRegistry::init('FichaPesquisa');

        $condicoes_join_pesquisa = Array(
            'FichaPesquisa.codigo_ficha = Ficha.codigo',
            "FichaPesquisa.codigo_tipo_pesquisa in ($tipos_pesquisa)",
        );

        if ($codigo_profissional_tipo == ProfissionalTipo::CARRETEIRO) {
            $condicoes_join_pesquisa[] = 'Ficha.codigo_profissional_tipo = ' . ProfissionalTipo::CARRETEIRO;
        } else {
            if (!empty($codigo_produto) and !empty($codigo_cliente)) {
                $condicoes_join_pesquisa[] = 'Ficha.codigo_cliente = '.preg_replace('/\D/', '', $codigo_cliente);
                $condicoes_join_pesquisa[] = 'Ficha.codigo_profissional_tipo = '.preg_replace('/\D/', '', $codigo_profissional_tipo);
                $condicoes_join_pesquisa[] = 'Ficha.codigo_produto = '.preg_replace('/\D/', '', $codigo_produto);
            }
        }

        $joins = array(
            array(
                'table'      => $this->Ficha->databaseTable.'.'.$this->Ficha->tableSchema.'.'.$this->Ficha->useTable,
                'alias'      => 'Ficha',
                'type'       => 'INNER',
                'conditions' => 'ProfissionalLog.codigo = Ficha.codigo_profissional_log',
            ),

            array(
                'table'      => $this->FichaPesquisa->databaseTable.'.'.$this->FichaPesquisa->tableSchema.'.'.$this->FichaPesquisa->useTable,
                'alias'      => 'FichaPesquisa',
                'type'       => 'INNER',
                'conditions' => $condicoes_join_pesquisa
            )
        );
        
        $condicoes['ProfissionalLog.codigo_documento'] = $codigo_documento;

        $retorno = $this->find('count', array(
                'conditions'    => $condicoes,
                'joins'         => $joins,
            )
        );
        //return $retorno;
        return ($retorno>0);
    }

    function obterProfissionalPeloCodigoProfissionalLog($codigo_profissional_log) {
        $retorno = $this->findByCodigo($codigo_profissional_log);
        return $retorno;
    }
    
    function incluir($dados){ 
        unset($dados[$this->name]['codigo']);
        unset($dados[$this->name]['data_inclusao']);
        $this->create();
        return $this->save($dados);
    }
    
    function duplicar($codigo) {
        try {
            if(empty($codigo)) {
                throw new Exception();
            }
            
            $model_data = $this->find('first', array(
                'conditions' => array(
                    'ProfissionalLog.codigo' => $codigo
            )));
            
            $result = $this->incluir($model_data);
            
            if($result) {
                return $this->id;
            } else {
                throw new Exception();
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function carregarDadosCadastraisLog( $codigo ){
        if( $codigo ){
            $this->ProfissionalContatoLog  = ClassRegistry::init('ProfissionalContatoLog');
            $this->ProfissionalEnderecoLog = ClassRegistry::init('ProfissionalEnderecoLog');
            $this->VEndereco            = ClassRegistry::init('VEndereco');
            $dados_profissional         = $this->carregar( $codigo );
            $data_inclusao = AppModel::dateTimeToDbDateTime2( $dados_profissional['ProfissionalLog']['data_inclusao']);
            $data_inicio   = date("Ymd H:i:s", strtotime("-2 second", strtotime($data_inclusao)));
            $data_fim      = date("Ymd H:i:s", strtotime("+2 second", strtotime($data_inclusao)));
            $contatos_logs      = $this->ProfissionalContatoLog->find('all', array(
                'conditions'=>array(
                    'ProfissionalContatoLog.codigo_profissional' => $dados_profissional['ProfissionalLog']['codigo_profissional'],
                    'ProfissionalContatoLog.data_inclusao BETWEEN ? AND ?' => array($data_inicio, $data_fim),
                    'ProfissionalContatoLog.codigo_usuario_inclusao' => $dados_profissional['ProfissionalLog']['codigo_usuario_alteracao']
                ), 
                'fields'=> array('nome', 'codigo_tipo_contato', 'codigo_tipo_retorno', 'descricao'),
                'order' => 'ProfissionalContatoLog.codigo ASC' 
            ));
            $endereco_log  = $this->ProfissionalEnderecoLog->find('first', array(
                'conditions'=>array(
                    'ProfissionalEnderecoLog.codigo_profissional' => $dados_profissional['ProfissionalLog']['codigo_profissional'],
                    'ProfissionalEnderecoLog.data_inclusao BETWEEN ? AND ?' => array($data_inicio, $data_fim),
                    'ProfissionalEnderecoLog.codigo_usuario_inclusao' => $dados_profissional['ProfissionalLog']['codigo_usuario_alteracao']
                )
            ));
            $dados_profissional['ProfissionalEnderecoLog'] = $endereco_log['ProfissionalEnderecoLog'];            
            $VEndereco  = $this->VEndereco->carregar( $endereco_log['ProfissionalEnderecoLog']['codigo_endereco'] );
            $dados_profissional['ProfissionalEnderecoLog']['endereco_cep'] = $VEndereco['VEndereco']['endereco_cep'];
            $dados_profissional['ProfissionalContatoLog']  = Set::extract('/ProfissionalContatoLog/.', $contatos_logs);
            return $dados_profissional;
        }
    }    
}
