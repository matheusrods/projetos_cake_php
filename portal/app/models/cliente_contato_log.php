<?php

class ClienteContatoLog extends AppModel {

    var $name = 'ClienteContatoLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'cliente_contato_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $validate = array(
        'codigo_cliente_contato' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo cliente contato',
            'required' => true
        )
    );

    function bindUsuarioInclusao() {
        $this->bindModel(array(
            'belongsTo' => array(
                'Usuario' => array(
                    'class' => 'Usuario',
                    'foreignKey' => 'codigo_usuario_inclusao'
                )
            )
        ));
    }
    function bindUsuarioAlteracao() {
        $this->bindModel(array(
            'belongsTo' => array(
                'UsuarioAlteracao' => array(
                    'class' => 'Usuario',
                    'foreignKey' => 'codigo_usuario_alteracao'
                )
            )
        ));
    }

    function unbindUsuarioInclusao() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'Usuario'
            )
        ));
    }
    
    function bindTipoRetorno() {
        $this->bindModel(array(
            'belongsTo' => array(
                'TipoRetorno' => array(
                    'class' => 'TipoRetorno',
                    'foreignKey' => 'codigo_tipo_retorno'
                )
            )
        ));
    }
    
    function unbindTipoRetorno() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'TipoRetorno'
            )
        ));
    }
     
    function bindTipoContato() {
        $this->bindModel(array(
            'belongsTo' => array(
                'TipoContato' => array(
                    'class' => 'TipoContato',
                    'foreignKey' => 'codigo_tipo_contato'
                )
            )
        ));
    }
    
    function unbindTipoContato() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'TipoContato'
            )
        ));
    }

    function incluir($data) {
        unset($data[$this->name]['codigo']);
        $this->create();
        return $this->save($data);
    }

    function excluir($codigo) {
        return $this->delete($codigo);
    }

    function listar($conditions = null) {
        $this->bindUsuarioInclusao();
        $this->bindTipoRetorno();
        $this->bindTipoContato();
        $order = array('ClienteContatoLog.data_inclusao desc');
        $result = $this->find('all', array('conditions' => $conditions, 'limit' => 10, 'order' => $order));
        $this->unbindTipoRetorno();
        $this->unbindTipoContato();
        $this->unbindUsuarioInclusao();
        return $result;
    }
    
    function converteFiltroEmCondition($data, $condition_vazia_bloqueada = false) {
        $conditions = array();
        if (isset($data['codigo']) && !empty($data['codigo']))
            $conditions['ClienteContatoLog.codigo'] = $data['codigo'];
        if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente']))
            $conditions['ClienteContatoLog.codigo_cliente'] = $data['codigo_cliente'];
        if (isset($data['usuario']) && !empty($data['usuario']))
            $conditions['Usuario.apelido like'] = '%' . $data['usuario'] . '%';
        if (isset($data['data_inicio']) && !empty($data['data_inicio'])) {
            $conditions[$this->name.'.data_inclusao >'] = AppModel::dateToDbDate($data['data_inicio']) . ' 00:00:00.0';
        }
        if (isset($data['data_fim']) && !empty($data['data_fim'])) {
            $conditions[$this->name.'.data_inclusao <'] = AppModel::dateToDbDate($data['data_fim']) . ' 23:59:59.997';
        }
        
        if (count($conditions) == 0) {
            if ($condition_vazia_bloqueada)
                $conditions = array('ClienteContatoLog.codigo' => null);
        }
        
        return $conditions;
    }

}