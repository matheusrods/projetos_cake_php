<?php
class ClienteEnderecoLog extends AppModel {
    var $name = 'ClienteEnderecoLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'cliente_endereco_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
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

    function unbindUsuarioInclusao() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'Usuario'
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
    
    function listar($conditions = null, $limit = null) {
        if (strpos($this->databaseTable, 'test') > 0) {
            $prefix = 'dbo.';
        } else {
            $prefix = 'RHHealth.dbo.';
        }
        $this->bindUsuarioInclusao();
        $this->bindTipoContato();
        $order = array('ClienteEnderecoLog.data_inclusao desc');

        $fields = array(
            'ClienteEnderecoLog.*',
            'Usuario.*',
            'TipoContato.*'
        );
        $result = $this->find('all', array('fields' => $fields, 'limit' => 10, 'conditions' => $conditions, 'order' => $order, 'joins' => $join));
        
        $this->unbindUsuarioInclusao();
        $this->unbindTipoContato();
        return $result;
    }
    
    function converteFiltroEmCondition($data, $condition_vazia_bloqueada = false) {
        $conditions = array();
        if (isset($data['codigo']) && !empty($data['codigo']))
            $conditions['ClienteEnderecoLog.codigo'] = $data['codigo'];
        if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente']))
            $conditions['ClienteEnderecoLog.codigo_cliente'] = $data['codigo_cliente'];
        if (isset($data['usuario']) && !empty($data['usuario']))
            $conditions['Usuario.apelido like'] = '%' . $data['usuario'] . '%';
        if (!empty($data['data_inicio'])) {
            $conditions[$this->name.'.data_inclusao >'] = AppModel::dateToDbDate($data['data_inicio']) . ' 00:00:00.0';
        }
        if (!empty($data['data_fim'])) {
            $conditions[$this->name.'.data_inclusao <'] = AppModel::dateToDbDate($data['data_fim']) . ' 23:59:59.997';
        }
        
        if (count($conditions) == 0) {
            if ($condition_vazia_bloqueada)
                $conditions = array('ClienteEnderecoLog.codigo' => null);
        }
        
        return $conditions;
    }

    function listarEnderecoLogByCodigoCliente($codigo_cliente,$tipo_contato){
        $this->bindTipoContato();

        $conditions = array(
            'ClienteEnderecoLog.codigo_cliente' => $codigo_cliente,
            'ClienteEnderecoLog.codigo_tipo_contato' => $tipo_contato
        );

        $fields = array(
            'ClienteEnderecoLog.*',
            'TipoContato.descricao'
        );

        $order = 'ClienteEnderecoLog.data_inclusao DESC';

        return $this->find('all', array('conditions' => $conditions, 'fields' => $fields, 'order' => $order,'limit' => 2));
    }
    
}