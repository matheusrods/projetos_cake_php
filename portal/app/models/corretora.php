<?php
class Corretora extends AppModel {
    var $name = 'Corretora';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'corretora';
    var $primaryKey = 'codigo';
    var $displayField = 'nome';
    var $actsAs = array('Secure', 'SincronizarCodigoDocumento', 'Loggable' => array('foreign_key' => 'codigo_corretora'));
    var $validate = array(
        'codigo_documento' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o documento'
        )
    );



    
    function converteFiltroEmCondition($data) {
        $conditions = array();
        if (!empty($data['codigo']))
            $conditions['Corretora.codigo'] = $data['codigo'];
        if (!empty($data['codigo_documento']))
            $conditions['Corretora.codigo_documento like'] = $data['codigo_documento'] . '%';
       
        if(!empty($data['status'])){
            if($data['status'] == 1){
                if(!empty($data['nome'])){
                    $conditions[]['AND'] = array( 
                        array('Corretora.nome like' => '%' . $data['nome'] . '%'),
                        array('Corretora.nome NOT like' => '%DESATIVAD%'),
                    );    
                }else{
                    $conditions['Corretora.nome NOT like'] = '%DESATIVAD%';    
                } 
            }else{
                if (!empty($data['nome'])){
                    $conditions[]['AND'] = array( 
                        array('Corretora.nome like' =>'%DESATIVAD%'),
                        array('Corretora.nome like' => '%' . $data['nome'] . '%'),
                    );
                }else{
                    $conditions['Corretora.nome like'] = '%DESATIVAD%';
                }
            }
        }else{
            if (!empty($data['nome']))
                $conditions['Corretora.nome like'] = '%' . $data['nome'] . '%';
        }

        return $conditions;
    }
    
    public function carregarParaEdicao($codigo_corretora) {
        $dados = $this->read(null, $codigo_corretora);
        $CorretoraEndereco = ClassRegistry::init('CorretoraEndereco');
        $endereco_comercial = $CorretoraEndereco->getByTipoContato($codigo_corretora, TipoContato::TIPO_CONTATO_COMERCIAL);
        if ($endereco_comercial)
            $dados = array_merge($dados, $endereco_comercial);
        return $dados;
    }
    
    function incluir($dados) {

        try {
            $this->query('begin transaction');
            if (!parent::incluir($dados))
                throw new Exception();
            $dados['Corretora']['codigo'] = $this->id;
            if (!$this->atualizarEnderecoComercial($dados))
                throw new Exception();
            $this->commit();
            return true;
        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }
    
    function atualizarEnderecoComercial($dados) {
        if (!isset($dados['CorretoraEndereco']['cep']) || empty($dados['CorretoraEndereco']['cep'])) {
            $this->invalidate('CorretoraEndereco.cep', 'Informe o CEP');
            return false;
        }
        $CorretoraEndereco = ClassRegistry::init('CorretoraEndereco');
        $dados_endereco = array('CorretoraEndereco' => $dados['CorretoraEndereco']);
        $dados_endereco['CorretoraEndereco']['codigo_corretora'] = $dados['Corretora']['codigo'];
        $dados_endereco['CorretoraEndereco']['codigo_tipo_contato'] = TipoContato::TIPO_CONTATO_COMERCIAL;

        if (!isset($dados_endereco['CorretoraEndereco']['codigo']) || empty($dados_endereco['CorretoraEndereco']['codigo'])) {
            $result = $CorretoraEndereco->incluir($dados_endereco);
            return $result;
        } else {
            $dados_antigos = $CorretoraEndereco->carregar($dados_endereco['CorretoraEndereco']['codigo']);
            if (($dados_endereco['CorretoraEndereco']['numero'] != $dados_antigos['CorretoraEndereco']['numero']) ||
                ($dados_endereco['CorretoraEndereco']['complemento'] != $dados_antigos['CorretoraEndereco']['complemento']) ||
                ($dados_endereco['CorretoraEndereco']['logradouro'] != $dados_antigos['CorretoraEndereco']['logradouro']) ) {
                $dados_endereco = array('CorretoraEndereco' => array_merge($dados_antigos['CorretoraEndereco'], $dados_endereco['CorretoraEndereco']));
                $dados_endereco['CorretoraEndereco']['codigo_endereco'] = NULL;
                return $CorretoraEndereco->atualizar($dados_endereco);
            } else {
                return true;
            }
        }
    }
    
    function atualizar($dados, $endereco = true) { 
        if (!isset($dados['Corretora']['codigo']) || empty($dados['Corretora']['codigo']))
            return false;
        try {
            $this->query('begin transaction');
            if($endereco){
                if (!isset($dados['CorretoraEndereco']['codigo']) || empty($dados['CorretoraEndereco']['codigo'])) {
                    $this->invalidate('CorretoraEndereco.codigo', 'Informe o endereco');
                    throw new Exception();
                }
            }

            if (!parent::atualizar($dados))
                throw new Exception('Não atualizou corretora');

            if ($endereco && !$this->atualizarEnderecoComercial($dados))
                throw new Exception('Não atualizou endereço');
            $this->commit();
            return true;
        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }
    
    function buscaCorretoraJson( $like, $id = null, $limit = null ) {
        if( $like != 'null' && ( empty($id) || is_null( $id ) ) )             
            $resultado = $this->find( 'all', array( 
                'fields' => array( 'codigo',  'nome' ) ,
                'conditions' => array( 'OR' => array('nome LIKE' => '%'.$like.'%','codigo' => (int)preg_replace('/[^\-\d]*(\-?\d*).*/','$1',$like))) ,
                'order' => 'nome',
                'limit' => $limit,
            ) );
        else
            $resultado = $this->find( 'all', array( 'conditions' => array( 'codigo' => $id ), 'fields' => array( 'nome' ) ) );
                
        return $this->retiraModel( $resultado );
    }

    function listarCorretorasAtivas() {
        $corretoras = $this->find('list', array(
            'fields' => array('codigo', 'nome'),
            'conditions' => array(
                    'nome NOT LIKE' => 'DESATIVAD%'
                ),
            'order' => 'nome'
            )
        );

        return $corretoras;
    }

    function carregaCorretoraPjur($codigo_corretora){
        $TPjurPessoaJuridica = ClassRegistry::init("TPjurPessoaJuridica");
        $corretora = $this->carregar($codigo_corretora);
        if($corretora){
            $corretora_pjur = $TPjurPessoaJuridica->find('first',array('conditions' => array('pjur_cnpj' => $corretora['Corretora']['codigo_documento'])));
            if($corretora_pjur)
                return $corretora_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
        }
        return false;
    }

    function carregaCorretoraPorPjurCodigo($pjur_codigo){
        $TPjurPessoaJuridica = ClassRegistry::init("TPjurPessoaJuridica");
        $corretora_pjur = $TPjurPessoaJuridica->carregar($pjur_codigo);
        if($corretora_pjur){
            $corretora = $this->find('first',array('conditions' => array('codigo_documento' => $corretora_pjur['TPjurPessoaJuridica']['pjur_cnpj'])));
            if($corretora){
                return $corretora['Corretora']['codigo'];
            }
        }
        return false;
    }

    function completarTViagViagemCodigosDbBuonny($viag_viagem) {
        if (isset($viag_viagem['Corretora']['pjur_cnpj'])) {
            $corretora = $this->find('first', array('conditions' => array('codigo_documento' => $viag_viagem['Corretora']['pjur_cnpj'])));
            if ($corretora)
                $viag_viagem['Corretora']['codigo'] = $corretora['Corretora']['codigo'];
        }
        return $viag_viagem;
    }
}
?>