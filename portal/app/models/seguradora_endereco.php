<?php

class SeguradoraEndereco extends AppModel {

    var $name = 'SeguradoraEndereco';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'seguradora_endereco';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_seguradora_endereco'));
    var $belongsTo = array(
        'TipoContato' => array(
            'className' => 'TipoContato',
            'foreignKey' => 'codigo_tipo_contato'
        ),
    );
    var $validate = array(
        'codigo_seguradora' => array(
            'rule' => 'notEmpty',
            'message' => 'Seguradora não informada',
            'required' => true
        ),
        'numero' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o numero'
        ),
        'codigo_tipo_contato' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o tipo de contato'
            ),
            'tipoContatoEnderecoUnico' => array(
                'rule' => 'tipoContatoEnderecoUnico',
                'message' => 'Tipo já informado'
            )
        ),
        'endereco_cep' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe um CEP'
            ),
        )
    );       
    
    function tipoContatoEnderecoUnico($field = array()) {
        $edit_mode = isset($this->data[$this->name]['codigo']) && !empty($this->data[$this->name]['codigo']);

        if ($edit_mode)
            return true;
        else
            $conditions = array(
                'conditions' => array(
                    'codigo_seguradora' => $this->data[$this->name]['codigo_seguradora'],
                    'codigo_tipo_contato' => $this->data[$this->name]['codigo_tipo_contato']
                )
            );

        $tipoContatoExistente = $this->find('count', $conditions);

        if ($tipoContatoExistente > 0) {
            return false;
        }

        return true;
    }
    
    function getByTipoContato($codigo_seguradora = 0, $codigo_tipo_contato = 0) {
        $this->bindModel(array('belongsTo' => array('VEndereco' => array('className' => 'VEndereco', 'foreignKey' => 'codigo_endereco'))));
        $seguradora_endereco = $this->find('first', array('conditions' => array('SeguradoraEndereco.codigo_seguradora' => $codigo_seguradora, 'SeguradoraEndereco.codigo_tipo_contato' => $codigo_tipo_contato)));
        $this->unbindModel(array('belongsTo' => array('VEndereco')));
        return $seguradora_endereco;
    }
    
    private function listaEnderecos($conditions) {

        if (empty($conditions) || !isset($conditions) || !is_array($conditions))
            return false;

        $join = array( );
        if ($this->useDbConfig == 'test_suite') {
            //$join[0]['table'] = $this->getDataSource()->config['database'].'.dbo.uvw_endereco';
            $join[0]['table'] = 'uvw_endereco';
            $join[0]['tableSchema'] = 'dbo';
            $join[0]['databaseTable'] = $this->getDataSource()->config['database'];
        }
        return $this->find('all', array(
                    'fields' => array(
                        'SeguradoraEndereco.*',
                        'TipoContato.descricao'
                    ),
                    'joins' => $join,
                    'conditions' => $conditions));
    }
    
    function listaEnderecosExcetoTipoContato($codigo_seguradora, $tipo_contato) {
        return $this->listaEnderecos(
                        array(
                            'SeguradoraEndereco.codigo_seguradora' => $codigo_seguradora,
                            'SeguradoraEndereco.codigo_tipo_contato !=' => $tipo_contato
                        )
        );
    }
    
    function enderecoCompleto($codigo) {
        $cliente_endereco = $this->carregar($codigo);
        return $cliente_endereco;
    }
}

?>