<?php

class ClienteProdutoLog extends AppModel {

    var $name = 'ClienteProdutoLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHhealth';
    var $useTable = 'cliente_produto_log';
    var $primaryKey = 'codigo';
    var $foreignKeyLog = 'codigo_cliente_produto';
    var $displayField = 'nome_fantasia';
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

    function bindCliente() {
        $this->bindModel(array(
            'belongsTo' => array(
                'Cliente' => array(
                    'className' => 'Cliente',
                    'foreignKey' => 'codigo_cliente'
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

    function bindProduto() {
        $this->bindModel(array(
            'belongsTo' => array(
                'Produto' => array(
                    'class' => 'Produto',
                    'foreignKey' => 'codigo_produto'
                )
            )
        ));
    }
    
    function unbindProduto() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'Produto'
            )
        ));
    }
    
    function bindMotivoBloqueio() {
        $this->bindModel(array(
            'belongsTo' => array(
                'MotivoBloqueio' => array(
                    'class' => 'MotivoBloqueio',
                    'foreignKey' => 'codigo_motivo_bloqueio'
                )
            )
        ));
    }
    
    function unbindMotivoBloqueio() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'MotivoBloqueio'
            )
        ));
    }

    function listar($conditions = null, $limit = null) {
        $this->bindUsuarioInclusao();
        $this->bindUsuarioAlteracao();
        $this->bindProduto();
        $this->bindMotivoBloqueio();
        $order = array('ClienteProdutoLog.data_alteracao DESC', 'ClienteProdutoLog.codigo DESC');
        $result = $this->find('all', array('limit' => 10, 'conditions' => $conditions, 'order' => $order));
        $this->unbindMotivoBloqueio();
        $this->unbindProduto();
        $this->unbindUsuarioInclusao();
        return $result;
    }
    
    function converteFiltroEmCondition($data, $condition_vazia_bloqueada = true) {
        $conditions = array();
        if (isset($data['codigo']) && !empty($data['codigo']))
            $conditions['ClienteProdutoLog.codigo'] = $data['codigo'];
        if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente']))
            $conditions['ClienteProdutoLog.codigo_cliente'] = $data['codigo_cliente'];
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
                $conditions = array('ClienteProdutoLog.codigo' => null);
        }
        
        return $conditions;
    }

    function listarParaEnvioEmailJuridico(){
        return $this->find('all',array(
            'conditions' => array(
                'OR' => array('ClienteProdutoLog.enviado_juridico IS NULL','ClienteProdutoLog.enviado_juridico' => false,),
                'ClienteProdutoLog.acao_sistema' => 1,
            ),
            'order' => 'ClienteProdutoLog.data_inclusao DESC',
        ));
    }

    /*Verifica se houve bloqueio de produto por cliente nos ultimos 30 dias*/
    function verificaBloqueioProdutoCliente( $codigo_cliente, $codigo_produto=array(1, 2), $qtde_dias=30 ){
        $MotivoBloqueio = ClassREgistry::init('MotivoBloqueio');
        $this->bindProduto();
        $codigo_produto = !empty($codigo_produto)  ? $codigo_produto : array(1, 2);
        $data_inicio    = date('Y-m-d 00:00:00', strtotime("-$qtde_dias days"));
        $data_fim       = date('Y-m-d 23:59:59');
        return $this->find('count',array(
            'conditions' => array(
                'Produto.codigo' => $codigo_produto,
                'ClienteProdutoLog.codigo_cliente'  => $codigo_cliente,
                'ClienteProdutoLog.acao_sistema <>' => 0,
                'ClienteProdutoLog.codigo_motivo_bloqueio <>' => $MotivoBloqueio::MOTIVO_OK,
                'ClienteProdutoLog.data_inclusao BETWEEN ? AND ?' => array( $data_inicio,  $data_fim )
            )
        ));        
    } 
}
