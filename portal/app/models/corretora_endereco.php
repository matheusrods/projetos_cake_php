<?php

class CorretoraEndereco extends AppModel {

    var $name = 'CorretoraEndereco';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'corretora_endereco';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_corretora_endereco'));
    var $belongsTo = array(
        'TipoContato' => array(
            'className' => 'TipoContato',
            'foreignKey' => 'codigo_tipo_contato'
        ),
    );
    var $validate = array(
        'codigo_corretora' => array(
            'rule' => 'notEmpty',
            'message' => 'Corretora não informada',
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
        'cep' => array(
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
                    'codigo_corretora' => $this->data[$this->name]['codigo_corretora'],
                    'codigo_tipo_contato' => $this->data[$this->name]['codigo_tipo_contato']
                )
            );

        $tipoContatoExistente = $this->find('count', $conditions);

        if ($tipoContatoExistente > 0) {
            return false;
        }

        return true;
    }
    
    function getByTipoContato($codigo_corretora = 0, $codigo_tipo_contato = 0) {
        $this->bindModel(array('belongsTo' => array('VEndereco' => array('className' => 'VEndereco', 'foreignKey' => 'codigo_endereco'))));
        $corretora_endereco = $this->find('first', array('conditions' => array('CorretoraEndereco.codigo_corretora' => $codigo_corretora, 'CorretoraEndereco.codigo_tipo_contato' => $codigo_tipo_contato)));
        $this->unbindModel(array('belongsTo' => array('VEndereco')));
        return $corretora_endereco;
    }
    
    private function listaEnderecos($conditions) {

        if (empty($conditions) || !isset($conditions) || !is_array($conditions))
            return false;

        $join = array();
        if ($this->useDbConfig == 'test_suite') {
            //$join[0]['table'] = $this->getDataSource()->config['database'].'.dbo.uvw_endereco';
            $join[0]['table'] = 'uvw_endereco';
            $join[0]['tableSchema'] = 'dbo';
            $join[0]['databaseTable'] = $this->getDataSource()->config['database'];
        }
        return $this->find('all', array(
                    'fields' => array(
                        'CorretoraEndereco.*',
                        'TipoContato.descricao'
                    ),
                    'joins' => $join,
                    'conditions' => $conditions));
    }
    
    function listaEnderecosExcetoTipoContato($codigo_corretora, $tipo_contato) {
        return $this->listaEnderecos(
                        array(
                            'CorretoraEndereco.codigo_corretora' => $codigo_corretora,
                            'CorretoraEndereco.codigo_tipo_contato !=' => $tipo_contato
                        )
        );
    }
    
    function enderecoCompleto($codigo) {
        $cliente_endereco = $this->carregar($codigo);
        return $cliente_endereco;
    }
}

?>