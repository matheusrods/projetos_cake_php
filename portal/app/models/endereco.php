<?php
class Endereco extends AppModel {

    var $name = 'Endereco';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'endereco';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $validate = array(
        'codigo_endereco_cidade' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a Cidade',
            'required' => true
            ),
        'codigo_endereco_bairro_inicial' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Bairro',
            'required' => true
            ),
        'codigo_endereco_tipo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Tipo',
            'required' => true
            ),
        'codigo_endereco_cep' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o CEP',
            'required' => true
            ),
        'descricao' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o Logradouro',
                )
            )
        );

    function parentNode() {
        return null;
    }

    public function buscarEnderecoPeloCodigo($codigo) {
        $this->bindCEP();
        $this->bindCidade();

        $enderecos = $this->find('first',array(
            'fields' => array(
                'Endereco.codigo',
                'Endereco.codigo_endereco_cep',    
                'EnderecoCidade.codigo_endereco_estado',
                'Endereco.codigo_endereco_cidade',
                'Endereco.codigo_endereco_bairro_inicial',
                'Endereco.codigo_endereco_tipo',
                'Endereco.descricao',
                'Endereco.codigo_correio',
                'Endereco.data_inclusao'
                ),
            'conditions' => array('Endereco.codigo' => $codigo)
            ));

        $this->unbindCEP();
        $this->unbindCidade();

        return $enderecos;
    }

    public function buscarEnderecoPeloCep($cep) {
        $this->bindCEP();
        $this->bindCidade();

        $enderecos = $this->find('first',array(
            'fields' => array(
                'Endereco.codigo',
                'Endereco.codigo_endereco_cep',    
                'EnderecoCidade.codigo_endereco_estado',
                'Endereco.codigo_endereco_cidade',
                'Endereco.codigo_endereco_bairro_inicial',
                'Endereco.codigo_endereco_tipo',
                'Endereco.descricao',
                'Endereco.codigo_correio',
                'Endereco.data_inclusao'
                ),
            'conditions' => array('EnderecoCep.cep' => $cep)
            ));

        $this->unbindCEP();
        $this->unbindCidade();

        return $enderecos;
    }

    public function buscarEnderecoParaImportacao($cep, $endereco) {
        $conditions['EnderecoCep.cep'] = $cep;
        $joins = array(
            array(
                'table' => 'endereco_cep',
                'alias' => 'EnderecoCep',
                'type' => 'INNER',
                'conditions' => array(
                    'EnderecoCep.codigo = Endereco.codigo_endereco_cep'
                    )
                ),
            array(
                'table' => 'endereco_cidade',
                'alias' => 'EnderecoCidade',
                'type' => 'INNER',
                'conditions' => array(
                    'EnderecoCidade.codigo = Endereco.codigo_endereco_cidade'
                    )
                ),
            array(
                'table' => 'endereco_estado',
                'alias' => 'EnderecoEstado',
                'type' => 'INNER',
                'conditions' => array(
                    'EnderecoEstado.codigo = EnderecoCidade.codigo_endereco_estado'
                    )
                ),
            array(
                'table' => 'endereco_tipo',
                'alias' => 'EnderecoTipo',
                'type' => 'INNER',
                'conditions' => array(
                    'EnderecoTipo.codigo = Endereco.codigo_endereco_tipo'
                    )
                ),
            );
        $fields = array(
            'Endereco.codigo',
            'Endereco.codigo_endereco_cep',    
            'EnderecoCidade.codigo_endereco_estado',
            'EnderecoCidade.descricao',
            'EnderecoEstado.codigo',
            'EnderecoEstado.descricao',
            'Endereco.codigo_endereco_cidade',
            'Endereco.codigo_endereco_bairro_inicial',
            'Endereco.codigo_endereco_tipo',
            'Endereco.descricao',
            'Endereco.codigo_correio',
            'Endereco.data_inclusao',
            'EnderecoTipo.descricao'
            );
        $enderecos = $this->find('count',array(
            'joins' => $joins,
            'conditions' => $conditions
            )
        );
        if($enderecos > 1) $conditions[] = 'SOUNDEX(CONCAT(EnderecoTipo.descricao, " ", Endereco.descricao)) LIKE SOUNDEX("'.$endereco.'")';
        $enderecos = $this->find('first',array(
            'joins' => $joins,
            'fields' => $fields,
            'conditions' => $conditions
            )
        );
        return $enderecos;
    }

    // Associação com o CEP
    function bindCEP() {
        $this->bindModel(array(
            'belongsTo' => array(
                'EnderecoCep' => array(
                    'className' => 'EnderecoCep',
                    'foreignKey' => 'codigo_endereco_cep'
                    ))));
    }

    function unbindCEP() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'EnderecoCep'
                )
            ));
    }

    // Associação com a Cidade
    function bindCidade() {
        $this->bindModel(array(
            'belongsTo' => array(
                'EnderecoCidade' => array(
                    'className' => 'EnderecoCidade',
                    'foreignKey' => 'codigo_endereco_cidade'
                    ))));
    }

    function unbindCidade() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'EnderecoCidade'
                )
            ));
    }

    // Associação com o Bairro
    function bindBairro() {
        $this->bindModel(array(
            'belongsTo' => array(
                'EnderecoBairro' => array(
                    'className' => 'EnderecoBairro',
                    'foreignKey' => 'codigo_endereco_bairro_inicial'
                    ))));
    }

    function unbindBairro() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'EnderecoBairro'
                )
            ));
    }

    // Associação com o Tipo de Endereco
    function bindTipo() {
        $this->bindModel(array(
            'belongsTo' => array(
                'EnderecoTipo' => array(
                    'className' => 'EnderecoTipo',
                    'foreignKey' => 'codigo_endereco_tipo'
                    ))));
    }

    function unbindTipo() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'EnderecoTipo'
                )
            ));
    }

    // Associação com o Distrito
    function bindDistrito() {
        $this->bindModel(array(
            'belongsTo' => array(
                'EnderecoDistrito' => array(
                    'className' => 'EnderecoDistrito',
                    'foreignKey' => 'codigo_endereco_distrito'
                    ))));
    }

    function unbindDistrito() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'EnderecoDistrito'
                )
            ));
    }

    function bindEnderecoEstado() {
        $this->bindModel(array(
            'belongsTo' => array(
                'EnderecoEstado' => array(
                    'className' => 'EnderecoEstado',
                    'foreignKey' => false,
                    'conditions' => 'EnderecoCidade.codigo_endereco_estado = EnderecoEstado.codigo'
                    ))));
    }

    function unbindEnderecoEstado() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'EnderecoEstado'
                )
            ));
    }    

    function carregarParaEdicao($codigo_endereco){
        $this->bindCidade();
        $this->bindCEP();

        $enderecos = $this->find('first',array(
            'fields' => array('Endereco.codigo',
                'EnderecoCep.cep',
                'Endereco.codigo_endereco_cep',    
                'EnderecoCidade.codigo_endereco_estado',
                'Endereco.codigo_endereco_cidade',
                'Endereco.codigo_endereco_bairro_inicial',
                'Endereco.codigo_endereco_tipo',
                'Endereco.descricao',
                'Endereco.codigo_correio',
                'Endereco.data_inclusao'
                ),
            'conditions' => array('Endereco.codigo' => $codigo_endereco)
            ));

        $this->unbindCidade();
        $this->unbindCEP();

        return $enderecos;
    }

    function vericaSePodeEditar($codigo_endereco){
        $permite = $this->find('count',array(
            'conditions' => array(
                'codigo_correio' => NULL,
                'codigo'         => $codigo_endereco,
                'convert(varchar,data_inclusao,103)' => date('d/m/Y')
                )
            ));

        return ($permite > 0);
    }

    function carregarEnderecoCompleto($codigo_endereco){
        $this->bindCidade();
        $this->bindCEP();
        $this->bindBairro();
        $this->bindTipo();
        $this->bindEnderecoEstado(); 
        $enderecos = $this->find('first',array('conditions' => array('Endereco.codigo' => $codigo_endereco)));

        $this->unbindCidade();
        $this->unbindCEP();
        $this->unbindBairro();
        $this->unbindTipo();
        $this->unbindEnderecoEstado(); 

        return $enderecos;
    }    
}