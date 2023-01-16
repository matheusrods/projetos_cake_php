<?php
class Regulador extends AppModel {
    var $name = 'Regulador';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'reguladores';
    var $primaryKey = 'codigo';
    var $displayField = 'nome';
	var $actsAs = array('Secure', 'SincronizarCodigoDocumento');
    var $validate = array(
        'codigo_documento' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o documento',
             ),
            'documentoValido' => array(
                'rule' => 'documentoValido',
                'message' => 'CPF/CNPJ é invalido!'
            )
    ));


    function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = 1, $extra = array()) {  
        if(!empty($extra['joins']))
            $joins = $extra['joins'];        
        if( !empty($extra['extra']['distancia']) &&  $extra['extra']['distancia']){            
            $retorno = $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'joins'));            
            return $retorno;
        }

        return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'joins'));
    }

    function documentoValido() {
        $model_documento  = & ClassRegistry::init('Documento');
        $this->data[$this->name]['codigo_documento'] = comum::soNumero( $this->data[$this->name]['codigo_documento'] );
        if($model_documento->isCPF($this->data[$this->name]['codigo_documento']) == false && $model_documento->isCNPJ($this->data[$this->name]['codigo_documento']) == false)
            return false;
        else
            return true;
    }

    function converteFiltroEmCondition($filtros, $filtro_padrao = TRUE) {
        $this->TipoContato = classRegistry::init('TipoContato');
        $conditions = array();        
        if (!empty($filtros['codigo_documento'])){
            $conditions['Regulador.codigo_documento'] = $filtros['codigo_documento'];        
        }
        if (!empty($filtros['nome'])){
            $conditions['Regulador.nome like'] = '%' . $filtros['nome'] . '%';
        }
        if (!empty($filtros['codigo_regulador'])){
            $conditions['Regulador.codigo'] = $filtros['codigo_regulador'];        
        }
       
        if(!empty($filtro_padrao) && $filtro_padrao) {
            $conditions['ReguladorEndereco.codigo_tipo_contato'] = TipoContato::TIPO_CONTATO_COMERCIAL;
        }

        return $conditions;
    }
  
    public function carregarParaEdicao($codigo_regulador) {
        $dados = $this->read(null, $codigo_regulador);
        $this->ReguladorEndereco = ClassRegistry::init('ReguladorEndereco');
        $endereco_comercial = $this->ReguladorEndereco->getByTipoContato($codigo_regulador, TipoContato::TIPO_CONTATO_COMERCIAL);
        if ($endereco_comercial)
            $dados = array_merge($dados, $endereco_comercial);
        return $dados;
    }

    function incluir($dados) {
        try {
            $this->query('begin transaction');
            if (!parent::incluir($dados)){
                throw new Exception("Erro ao incluir o regulador");
            }
            $dados['Regulador']['codigo'] = $this->id;
            if (!$this->atualizarEnderecoComercial($dados)){
                throw new Exception("Erro ao incluir endereço");
            }
            $this->commit();
            return true;
        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }

    function atualizarEnderecoComercial($dados) {
        if (!isset($dados['ReguladorEndereco']['codigo_endereco']) || empty($dados['ReguladorEndereco']['codigo_endereco'])) {          
            $this->invalidate('ReguladorEndereco.codigo_endereco', 'Informe o endereco');
            return false;
        }
        $this->ReguladorEndereco = ClassRegistry::init('ReguladorEndereco');
        $dados_endereco = array('ReguladorEndereco' => $dados['ReguladorEndereco']);
        $dados_endereco['ReguladorEndereco']['codigo_regulador'] = $dados['Regulador']['codigo'];
        $dados_endereco['ReguladorEndereco']['codigo_tipo_contato'] = TipoContato::TIPO_CONTATO_COMERCIAL;
        
        if (!isset($dados_endereco['ReguladorEndereco']['codigo']) || empty($dados_endereco['ReguladorEndereco']['codigo'])) {
            $result = $this->ReguladorEndereco->incluir( $dados_endereco );
            return $result;
        } else {
            return $this->ReguladorEndereco->atualizar( $dados_endereco );
        }
    }

    function atualizar($dados, $endereco = true) {
        if (!isset($dados['Regulador']['codigo']) || empty($dados['Regulador']['codigo']))
            return false;
        try {
            $this->query('begin transaction');
            if($endereco){
                if (!isset($dados['ReguladorEndereco']['codigo_endereco']) || empty($dados['ReguladorEndereco']['codigo_endereco'])) {
                    $this->invalidate('ReguladorEndereco.codigo_endereco', 'Informe o endereco');
                    throw new Exception();
                }
            }
            if (!parent::atualizar($dados))
                throw new Exception('Não atualizou Regulador');
            if ($endereco && !$this->atualizarEnderecoComercial($dados))
                throw new Exception('Não atualizou endereço');
            $this->commit();
            return true;
        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }
    function delete( $codigo ){
        $ReguladorEndereco = ClassRegistry::init('ReguladorEndereco');
        $ReguladorContato  = ClassRegistry::init('ReguladorContato');
        $this->query('begin transaction');
        try {
            $ReguladorEndereco->deleteAll( array('codigo_regulador' => $codigo) );
            $ReguladorContato->deleteAll( array('codigo_regulador' => $codigo) );
            parent::delete($codigo);
            $this->commit();
            return true;
        } catch(Exception $e){
            $this->rollback();
            return false;
        }
    }

    public function listaReguladoresRegiao( $conditions ){
        $this->bindModel(array(
            'belongsTo' => array(
                'ReguladorRegiao'   => array(
                    'className'  => 'ReguladorRegiao',
                    'conditions' => 'ReguladorRegiao.codigo_regulador = Regulador.codigo',
                    'foreignKey' => FALSE 
                ),
            ),
        ));
        $this->bindModel(array(
            'hasMany' => array(
                'ReguladorContato' => array('foreignKey'=>'codigo_regulador'),                
            )
        ));

        $fields = array(
            'ReguladorRegiao.latitude','ReguladorRegiao.longitude','ReguladorRegiao.codigo','ReguladorRegiao.prioridade',
            'ReguladorRegiao.raio','Regulador.codigo', 'Regulador.nome');
        $regulador = $this->find('all',compact('conditions','fields'));
        return $regulador;
    }
 
}
?>