<?php
class Seguradora extends AppModel {
    var $name = 'Seguradora';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'seguradora';
    var $primaryKey = 'codigo';
    var $displayField = 'nome';
    var $actsAs = array('Secure', 'SincronizarCodigoDocumento', 'Loggable' => array('foreign_key' => 'codigo_seguradora'));
    var $validate = array(
        'codigo_documento' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o documento'
        )
    );

    const PORTO = 2;
    const ROYAL = 26;
    const SULAMERICA = 97;

    function converteFiltroEmCondition($data) {
        $conditions = array();
        if (!empty($data['codigo']))
            $conditions['Seguradora.codigo'] = $data['codigo'];
        if (!empty($data['codigo_documento']))
            $conditions['Seguradora.codigo_documento like'] = $data['codigo_documento'] . '%';
        
        if(!empty($data['status'])){
            if($data['status'] == 1){
                if(!empty($data['nome'])){
                    $conditions[]['AND'] = array( 
                        array('Seguradora.nome like' => '%' . $data['nome'] . '%'),
                        array('Seguradora.nome NOT like' => '%DESATIVAD%'),
                    );    
                }else{
                    $conditions['Seguradora.nome NOT like'] = '%DESATIVAD%';    
                } 
            }else{
                if (!empty($data['nome'])){
                    $conditions[]['AND'] = array( 
                        array('Seguradora.nome like' =>'%DESATIVAD%'),
                        array('Seguradora.nome like' => '%' . $data['nome'] . '%'),
                    );
                }else{
                    $conditions['Seguradora.nome like'] = '%DESATIVAD%';
                }
            }
        }else{
            if (!empty($data['nome']))
                $conditions['Seguradora.nome like'] = '%' . $data['nome'] . '%';
        }

        return $conditions;
    }

    public function carregarParaEdicao($codigo_seguradora) {
        $dados = $this->read(null, $codigo_seguradora);
        $SeguradoraEndereco = ClassRegistry::init('SeguradoraEndereco');
        $endereco_comercial = $SeguradoraEndereco->getByTipoContato($codigo_seguradora, TipoContato::TIPO_CONTATO_COMERCIAL);
        if ($endereco_comercial)
            $dados = array_merge($dados, $endereco_comercial);
        return $dados;
    }

    function incluir($dados) {
        try {
            $this->query('begin transaction');
            if (!parent::incluir($dados))
                throw new Exception();
            $dados['Seguradora']['codigo'] = $this->id;
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

        $SeguradoraEndereco = ClassRegistry::init('SeguradoraEndereco');
        $dados_endereco = array('SeguradoraEndereco' => $dados['SeguradoraEndereco']);
        $dados_endereco['SeguradoraEndereco']['codigo_seguradora'] = $dados['Seguradora']['codigo'];
        $dados_endereco['SeguradoraEndereco']['codigo_tipo_contato'] = TipoContato::TIPO_CONTATO_COMERCIAL;

        if (!isset($dados_endereco['SeguradoraEndereco']['codigo']) || empty($dados_endereco['SeguradoraEndereco']['codigo'])) {
            $result = $SeguradoraEndereco->incluir($dados_endereco);
            return $result;
        } else {
            $dados_antigos = $SeguradoraEndereco->carregar($dados_endereco['SeguradoraEndereco']['codigo']);
            if (($dados_endereco['SeguradoraEndereco']['logradouro'] != $dados_antigos['SeguradoraEndereco']['logradouro']) || 
                ($dados_endereco['SeguradoraEndereco']['numero'] != $dados_antigos['SeguradoraEndereco']['numero']) ||
                ($dados_endereco['SeguradoraEndereco']['complemento'] != $dados_antigos['SeguradoraEndereco']['complemento']) ) {
                $dados_endereco = array('SeguradoraEndereco' => array_merge($dados_antigos['SeguradoraEndereco'], $dados_endereco['SeguradoraEndereco']));
                return $SeguradoraEndereco->atualizar($dados_endereco);
            } else {
                return true;
            }
        }
    }

    function atualizar($dados, $endereco = true) {
        if (!isset($dados['Seguradora']['codigo']) || empty($dados['Seguradora']['codigo']))
            return false;
        try {
            $this->query('begin transaction');

            if (!parent::atualizar($dados))
                throw new Exception('Não atualizou seguradora');

            if ($endereco && !$this->atualizarEnderecoComercial($dados))
                throw new Exception('Não atualizou endereço');
            $this->commit();
            return true;
        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }

    function listaSeguradoraJson( $id = null ) {

        if( is_null( $id ) || empty( $id ) )
            $resultado = $this->find( 'all', array( 'fields' => array( 'codigo', 'nome' ) ) );
        else
            $resultado = $this->find( 'all', array( 'conditions' => array( 'codigo' => $id ), 'fields' => array( 'nome' ) ) );

        return $this->retiraModel( $resultado );
    }

    function listarSeguradorasAtivas() {
        $seguradoras = $this->find('list', array(
            'fields' => array('codigo', 'nome'),
            'conditions' => array(
                    'nome NOT LIKE' => 'DESATIVAD%'
                ),
            'order' => 'nome'
            )
        );

        return $seguradoras;
    }

    function completarTViagViagemCodigosDbBuonny($viag_viagem) {
        if (isset($viag_viagem['Seguradora']['pjur_cnpj'])) {
            $seguradora = $this->find('first', array('conditions' => array('codigo_documento' => $viag_viagem['Seguradora']['pjur_cnpj'])));
            if ($seguradora)
                $viag_viagem['Seguradora']['codigo'] = $seguradora['Seguradora']['codigo'];
        }
        return $viag_viagem;
    }

    function listaClientesPorSeguradoraCorretora($codigo_seguradora = NULL, $codigo_corretora = NULL){
        $this->Cliente =& ClassRegistry::init('Cliente');

        $conditions = array();
        $clientes = array();
        if(!empty($codigo_seguradora)){
            $conditions['codigo_seguradora'] = $codigo_seguradora;
        }
        if(!empty($codigo_corretora)){
            $conditions['codigo_corretora'] = $codigo_corretora;
        }
        if(!empty($conditions)){
            $clientes = $this->Cliente->find('list',array(
                'conditions' => $conditions,
                'fields' => array('codigo')
                )
            );
        }
        return $clientes;
    }

}
?>