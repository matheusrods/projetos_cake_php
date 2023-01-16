<?php

class ClienteLog extends AppModel {

    var $name = 'ClienteLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'cliente_log';
    var $primaryKey = 'codigo';
    var $displayField = 'nome_fantasia';
    var $actsAs = array('Secure');
    var $validate = array(
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
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
                    'className' => 'Usuario',
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

    function bindCorretora() {
        $this->bindModel(array(
            'belongsTo' => array(
                'Corretora' => array(
                    'class' => 'Corretora',
                    'foreignKey' => 'codigo_corretora'
                )
            )
        ));
    }

    function unbindCorretora() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'Corretora'
            )
        ));
    }

    function bindSeguradora() {
        $this->bindModel(array(
            'belongsTo' => array(
                'Seguradora' => array(
                    'class' => 'Seguradora',
                    'foreignKey' => 'codigo_seguradora'
                )
            )
        ));
    }

    function unbindSeguradora() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'Seguradora'
            )
        ));
    }

    function bindEnderecoRegiao() {
        $this->bindModel(array(
            'belongsTo' => array(
                'EnderecoRegiao' => array(
                    'class' => 'EnderecoRegiao',
                    'foreignKey' => 'codigo_endereco_regiao'
                )
            )
        ));
    }

    function unbindEnderecoRegiao() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'EnderecoRegiao'
            )
        ));
    }

    function bindGestor() {
        $this->bindModel(array(
            'belongsTo' => array(
                'Gestor' => array(
                    'class' => 'gestor',
                    'foreignKey' => 'codigo_gestor',
                )
            )
        ));
    }

    function unbindGestor() {
        return;
        $this->unbindModel(array(
            'belongsTo' => array(
                'Gestor'
            )
        ));
    }
    
    function bindSubtipo() {
        $this->bindModel(array(
            'belongsTo' => array(
                'ClienteSubTipo' => array(
                    'class' => 'ClienteSubTipo',
                    'foreignKey' => 'codigo_cliente_sub_tipo'
                )
            )
        ));
    }
    
    function unbindSubtipo() {
        $this->unbindModel(array(
            'belongsTo' => array('ClienteSubTipo')
        ));
    }

    function listar($conditions = null, $order = null) {
        $this->recursive = 1;

        if (!$order) {
            $order = array(
                'ClienteLog.data_inclusao desc'
            );
        }

        $this->bindCorretora();
        $this->bindUsuarioInclusao();
        $this->bindUsuarioAlteracao();
        $this->bindEnderecoRegiao();
        $result = $this->find('all', array('conditions' => $conditions, 'limit' => 10, 'order' => $order));
        $this->unbindCorretora();
        $this->unbindEnderecoRegiao();
        $this->unbindUsuarioInclusao();     
        $this->unbindGestor();
        return $result;
    }

    function converteFiltroEmCondition($data, $condition_vazia_bloqueada = false) {
        $conditions = array();
        if (isset($data['codigo']) && !empty($data['codigo']))
            $conditions['ClienteLog.codigo'] = $data['codigo'];
        if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente']))
            $conditions['ClienteLog.codigo_cliente'] = $data['codigo_cliente'];
        if (isset($data['razao_social']) && !empty($data['razao_social']))
            $conditions['ClienteLog.razao_social like'] = '%' . $data['razao_social'] . '%';
        if (isset($data['codigo_documento']) && !empty($data['codigo_documento']))
            $conditions['ClienteLog.codigo_documento like'] = $data['codigo_documento'] . '%';
        if (isset($data['codigo_corretora']) && !empty($data['codigo_corretora']))
            $conditions['ClienteLog.codigo_corretora'] = $data['codigo_corretora'];
        if (isset($data['codigo_gestor']) && !empty($data['codigo_gestor']))
            $conditions['ClienteLog.codigo_gestor'] = $data['codigo_gestor'];
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
                $conditions = array('ClienteLog.codigo' => null);
        }
        
        return $conditions;
    }
    
    function ultimoLog($codigo_cliente) {
        $maior_data = $this->find('first', array('order' => array('ClienteLog.data_inclusao desc'), 'fields' => array('ClienteLog.data_inclusao'), 'conditions' => array('ClienteLog.codigo_cliente' => $codigo_cliente)));
        if ($maior_data) {
            $this->bindUsuarioInclusao();
            $this->bindUsuarioAlteracao();
            $result = $this->find('first', array('conditions' => array('ClienteLog.codigo_cliente' => $codigo_cliente, 'ClienteLog.data_inclusao' => AppModel::dateToDbDate($maior_data['ClienteLog']['data_inclusao']))));
            $this->unbindUsuarioInclusao();
            return $result;
        } else {
            return false;
        }
    }

    function listarParaEnvioEmailJuridico(){
        return $this->find('all',array(
            'conditions' => array(
                'OR' => array('ClienteLog.enviado_juridico IS NULL','ClienteLog.enviado_juridico' => false,),
            ),
            'order' => 'ClienteLog.data_inclusao DESC',
        ));
    }
        
    /*Verifica se houve bloqueio de produto por cliente nos ultimos 30 dias*/
    function verificaClienteInativoLog( $codigo_cliente, $qtde_dias=30 ){
        $data_inicio = date('Ymd 00:00:00', strtotime("-$qtde_dias days"));
        $data_fim    = date('Ymd 23:59:59');
        return $this->find('count',array(
            'conditions' => array(
                'codigo_cliente'  => $codigo_cliente,
                'ativo' => 0,
                'data_inclusao BETWEEN ? AND ?' => array( $data_inicio,  $data_fim )
            )
        ));        
    }    

}
