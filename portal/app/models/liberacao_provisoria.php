<?php
class LiberacaoProvisoria extends AppModel {
	var $name = 'LiberacaoProvisoria';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'liberacao_provisoria';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    var $evitarBeforeSave = false;
    
    const TIPO_OPERACAO_INCLUSAO = 124;
    const TIPO_OPERACAO_EXCLUSAO = 128;
    
    var $belongsTo = array(
       'Cliente' => array(
           'className' => 'Cliente',
           'foreignKey' => 'codigo_cliente'
       ),
       'Produto' => array(
           'className' => 'Produto',
           'foreignKey' => 'codigo_produto'
       ),
       'Profissional' => array(
           'className' => 'Profissional',
           'foreignKey' => 'codigo_profissional'
       ),
    );
    
    var $validate = array(
        'codigo_profissional' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'message' => 'Informe o profissional',
            )          
        ),
        'codigo_produto' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'message' => 'Informe o produto',
            )
        ),
        'codigo_profissional_tipo' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o tipo do profissional',
             )
        ),
        'data_liberacao' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe uma data válida'
            ),
            'dataValida' => array(
                'rule' => 'dataValida',
                'message' => 'Informe uma data válida',
             )          
        )
    );

    function dataValida($check) {
        $timestamp = strtotime($this->dateTimeToDbDateTime($check['data_liberacao']));
        return $timestamp > time();
    }

    function clienteValido($check) {
        if (empty($check['codigo_cliente'])) {
            return false;
        }
        
        $cliente = $this->Cliente->carregar($check['codigo_cliente']);
        return $cliente['Cliente']['ativo'] == 1;
    }
    
    function produtoClienteValido($check) {
        $this->ClienteProduto = & ClassRegistry::init('ClienteProduto');
        $this->ClienteProduto->recursive = -1;        
        $options= array(
            'conditions' => array(
                'ClienteProduto.codigo_cliente' => $check['codigo_cliente'],
                'ClienteProduto.codigo_produto' => $check['codigo_produto']
            )
        );        
        $produto = $this->ClienteProduto->find('first', $options);
        return $produto['ClienteProduto']['codigo_motivo_bloqueio'] == 1;
    }
    
    function registroDuplicado() {
        $options = array(
            'conditions' => array(
                $this->name . '.codigo_cliente' =>  $this->data[$this->name]['codigo_cliente'],
                $this->name . '.codigo_produto' =>  $this->data[$this->name]['codigo_produto'],
                $this->name . '.codigo_profissional' =>  $this->data[$this->name]['codigo_profissional'],
                $this->name . '.codigo_profissional_tipo' =>  $this->data[$this->name]['codigo_profissional_tipo'],
                $this->name . '.ativo' => 1,
                $this->name . '.data_liberacao >=' =>  $this->dateToDbDate($this->data[$this->name]['data_liberacao'])
            )
        );
        return $this->find('count', $options) > 0;
    }
    
    function beforeSave() {
        if ($this->evitarBeforeSave) 
            return true;
        $profissional_carreteiro    = $this->data[$this->name]['codigo_profissional_tipo'] == 1;
        $codigo_profissional        = $this->data[$this->name]['codigo_profissional'];
        $codigo_cliente             = !$profissional_carreteiro ? $this->data[$this->name]['codigo_cliente'] : NULL;
        $codigo_produto             = $this->data[$this->name]['codigo_produto'];

        /**
         * Se o profissional for carreteiro, liberação serve para todos os clientes,
         * portanto, não verifica qual é o cliente
         */
        if (!$profissional_carreteiro && !$this->clienteValido($this->data[$this->name])) {
            $this->Profissional->invalidate('codigo_cliente', 'Informe um cliente válido');
            return false;
        }
        /*
        *VAlida CNH
        */
        $this->PesquisaConfiguracao = & ClassRegistry::init('PesquisaConfiguracao');
        $valida_cnh = $this->PesquisaConfiguracao->validaCNHVencida($this->data[$this->name]['codigo_profissional']);
        if (!$valida_cnh) {
            $this->Profissional->invalidate('codigo_documento', 'Validade da CNH expirou');
            return false;
        }

        if (empty($this->data[$this->name]['codigo']) && $this->registroDuplicado()) {
            $this->Profissional->invalidate('codigo_documento', 'Perfil Adequado por Prazo já está cadastrado nesse prazo');
            return false;
        }
        
        if (empty($this->data[$this->name]['codigo_profissional'])) {
            $this->Profissional->invalidate('codigo_documento', 'Informe o profissional');
            return false;
        }        
        /**
         * Verificar cliente x produto quando for carreteiro?
         */
        if (!$profissional_carreteiro && !$this->produtoClienteValido($this->data[$this->name])) {
            $this->invalidate('codigo_produto', 'Produto ' . $this->array_produtos[$this->data[$this->name]['codigo_produto']] . ' inativado para o cliente');
            return false;
        }

        if( $codigo_produto != Produto::SCORECARD ){
            /**
             * Obtém a última ficha do profissional
             */
            $this->Ficha = & ClassRegistry::init('Ficha');
            $ultimaFicha = $this->Ficha->obterUltimaFichaProfissional($codigo_cliente, $codigo_profissional, $codigo_produto);

            /**
             * Profissional já está em analise
             */
            if( !empty($ultimaFicha['Ficha']['codigo']) ){
              $this->FichaPesquisa = & ClassRegistry::init('FichaPesquisa');
              $ultimaFichaPesquisaProfissional = $this->FichaPesquisa->obterUltimaFichaPesquisa($ultimaFicha['Ficha']['codigo']);

              if ($ultimaFichaPesquisaProfissional['FichaPesquisa']['codigo_status_profissional'] == 1 && $ultimaFichaPesquisaProfissional['FichaPesquisa']['codigo_tipo_pesquisa'] == 2) {
                $this->Profissional->invalidate('codigo_documento', 'Profissional já está com perfil adequado');
                return false;
              }
            }
            /**
             * Profissional em pesquisa
             */
            if ($this->Profissional->emPesquisa($codigo_profissional, $codigo_cliente, $codigo_produto)) {
                $this->Profissional->invalidate('codigo_documento', 'Profissional em análise');
                return false;
            }

            /**
             * Vínculo do profissional Carreteiro / Outros
             */
            if (!$this->Profissional->possuiVinculo($codigo_profissional, $codigo_cliente, $codigo_produto, $profissional_carreteiro)) {
                $tipo_profissional = $profissional_carreteiro ? 'Carreteiro' : 'Outros';
                $this->Profissional->invalidate('codigo_documento', 'Profissional não possui vínculo como "'. $tipo_profissional . '", ou Cadastro Expirado.');
                return false;
            }
            
            /**
             * Validade da ficha
             */
            $dataValidadeCadastro = $this->Ficha->obterDataValidadeDaUltimaFichaDoProfissional($this->data[$this->name]['codigo_cliente'], $this->data[$this->name]['codigo_produto'], $this->data[$this->name]['codigo_profissional']);

            if (empty($dataValidadeCadastro)) {
                $this->Profissional->invalidate('codigo_documento', 'Profissional ainda não foi consultado por este cliente e produto ' . $this->array_produtos[$this->data[$this->name]['codigo_produto']]);
                return false;
            }
            
            if (strtotime($this->dateTimeToDbDateTime($dataValidadeCadastro)) < time()) {
                $this->Profissional->invalidate('codigo_documento', 'Validade do cadastro expirou');
                return false;
            }
        } else {            
            $this->FichaScorecardStatus = & ClassRegistry::init('FichaScorecardStatus');
            $this->FichaScorecard = & ClassRegistry::init('FichaScorecard');
            $ultimaFicha = $this->FichaScorecard->carregaFichaAnteriorProfissional($codigo_profissional, $codigo_cliente );

            if( empty($ultimaFicha) ){
                return false;
            }
            if( !$this->FichaScorecard->buscaProfissionalPorCliente( comum::soNumero($this->data['Profissional']['codigo_documento']), $codigo_cliente, TRUE )){
                return false;
            }

            if (empty($ultimaFicha['FichaScorecard']['data_validade'])) {
                $this->Profissional->invalidate('codigo_documento', 'Profissional ainda não foi consultado por este cliente e produto Scorecard');
                return false;
            }            
            if (strtotime($this->dateTimeToDbDateTime($ultimaFicha['FichaScorecard']['data_validade'])) < time()) {
                $this->Profissional->invalidate('codigo_documento', 'Validade do cadastro expirou');
                return false;
            }
            if ($ultimaFicha['FichaScorecard']['codigo_status'] != FichaScorecardStatus::FINALIZADA ) {
                $this->Profissional->invalidate('codigo_documento', 'Profissional em análise');
                return false;
            }
        }
        return true;
    }

    function incluir($data) {
        $this->create();
        $this->query('begin tran');            
        if ($this->save($data)) {
            $this->Ficha = & ClassRegistry::init('Ficha');
            $this->LogAtendimento = & ClassRegistry::init('LogAtendimento');
            $ficha = $this->Ficha->obterUltimaFichaProfissional($data[$this->name]['codigo_cliente'], $data[$this->name]['codigo_profissional'], $data[$this->name]['codigo_produto']);
            if ($this->LogAtendimento->gravaLogAtendimentoLiberacaoProvisoria($ficha, LiberacaoProvisoria::TIPO_OPERACAO_INCLUSAO)) {
                $this->query('commit');
                return true;
            }
        }
        
        $this->query('rollback');
        return false;
    }
    
    function salvarLiberacoesPorProduto( $data ) {
        $this->query('begin tran');
        try{
            if( isset($data['LiberacaoProvisoria']['codigo_produto']) && is_array($data['LiberacaoProvisoria']['codigo_produto']) ) {
                foreach ($data['LiberacaoProvisoria']['codigo_produto'] as $key => $codigo_produto ) {
                    $dados_liberacao = $data;
                    $dados_liberacao[$this->name]['codigo_produto'] = $codigo_produto;
                    if( !parent::incluir($dados_liberacao)) {
                    // debug( $dados_liberacao );die;
                        throw new Exception('Erro ao incluir Liberação');
                    }
                }
            } else {
                $this->validationErrors['codigo_produto'] = 'Informe o produto';
                throw new Exception('Informe o produto');
            }
            $this->query('commit');
            return true;
        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }

    function excluir($codigo) {
        $this->recursive = -1;
        $data = $this->read(null, $codigo);
        
        if ($data[$this->name]['ativo'] == 0) {
            return false;
        }

        if (strtotime($this->dateTimeToDbDateTime($data[$this->name]['data_liberacao'])) < time()) {
            return false;
        }

        $data[$this->name]['ativo'] = '0';
                
        $this->query('begin tran');
        
        try {
            $this->evitarBeforeSave = true;
            $this->save($data, false);
            $this->evitarBeforeSave = false;
            
            $this->Ficha = & ClassRegistry::init('Ficha');
            $this->LogAtendimento = & ClassRegistry::init('LogAtendimento');

            $ficha = $this->Ficha->obterUltimaFichaProfissional($data[$this->name]['codigo_cliente'], $data[$this->name]['codigo_profissional'], $data[$this->name]['codigo_produto']);
            if ($this->LogAtendimento->gravaLogAtendimentoLiberacaoProvisoria($ficha, LiberacaoProvisoria::TIPO_OPERACAO_EXCLUSAO)) {
                $this->query('commit');
                return true;
            }
        } catch (Exception $e) {
            $this->query('rollback');
            return false;
        }
        
        $this->query('rollback');
        return false;
    }
    
    function formataDados($data) {
        if (isset($data[$this->name])) {
            $data[$this->name]['data_liberacao'] = reset(explode(' ', $data[$this->name]['data_liberacao']));
        } elseif (isset($data[$this->Profissional->name])) {
            $data[$this->Profissional->name]['codigo_documento'] = preg_replace('/([d]{3})([d]{3})([d]{3})/', '$1.$2.$3-', $data[$this->Profissional->name]['codigo_documento']);
        } elseif (isset($data[0][$this->name])) {
            foreach ($data as $k => $row) {
                $data[$k][$this->name]['data_liberacao'] = reset(explode(' ', $row[$this->name]['data_liberacao']));
                $data[$k]['Profissional']['codigo_documento'] = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{3})/', '$1.$2.$3-', $data[$k]['Profissional']['codigo_documento']);
            }
        }

        return $data;
    }
    
    
    function converteFiltroEmCondition($data) {
        $conditions = array();
        
        if (!empty($data['codigo_cliente'])) {
            $conditions['Cliente.codigo'] = preg_replace('/\D/', '', $data['codigo_cliente']);
        }
        if (!empty($data['razao_social'])) {
            $conditions['Cliente.razao_social like'] = '%' . $data['razao_social'] . '%';
        }
        if (!empty($data['codigo_documento'])) {
            $conditions['Profissional.codigo_documento'] = preg_replace('/\D/', '', $data['codigo_documento']);
        }
        if (!empty($data['codigo_produto'])) {
            $conditions['Produto.codigo'] = $data['codigo_produto'];
        }
        if (!empty($data['data_inicio'])) {
            $conditions[$this->name.'.data_liberacao >'] = AppModel::dateToDbDate($data['data_inicio']) . ' 00:00:00.0';
        }
        if (!empty($data['data_fim'])) {
            $conditions[$this->name.'.data_liberacao <'] = AppModel::dateToDbDate($data['data_fim']) . ' 23:59:59.997';
        }
        
        return $conditions;
    }

    public function verificaLiberacaoProvisoria( $codigo_profissional, $codigo_produto, $codigo_cliente=NULL ){
        $conditions = array(
            'LiberacaoProvisoria.codigo_profissional' => $codigo_profissional,
            'LiberacaoProvisoria.codigo_produto'      => $codigo_produto,
            'LiberacaoProvisoria.ativo'               => 1,
            'LiberacaoProvisoria.data_liberacao >'    => date('Ymd')
        );        
        $conditions['LiberacaoProvisoria.codigo_profissional_tipo'] = 1;
        $liberacao_carreteiro = $this->find('count', compact('conditions'));
        if( $liberacao_carreteiro )
            return true;        
        if( !empty($codigo_cliente))            
            $conditions['codigo_cliente'] = $codigo_cliente;
        unset($conditions['LiberacaoProvisoria.codigo_profissional_tipo']);
        $conditions['LiberacaoProvisoria.codigo_profissional_tipo'] = 0;
        $liberacao_outros  = $this->find('count', compact('conditions'));
        if( $liberacao_outros )
            return true;
        return false;
    }
    
}
