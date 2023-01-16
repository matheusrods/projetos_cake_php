<?php
class UsuarioHistorico extends AppModel {
    var $name = 'UsuarioHistorico';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'usuarios_historicos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $validate = array(
        'remote_addr' => array(
            'rule' => 'notEmpty',
            'message' => 'O endereço IP não foi informado',
            'required' => true,
            'allowEmpty' => false
        ),
        'http_user_agent' => array(
            'rule' => 'notEmpty',
            'message' => 'O Agente não foi informado',
            'required' => true,
            'allowEmpty' => false
        ),
    );    
    // var $belongsTo = array(
    //     'Usuario' => array('foreignKey' => 'codigo_usuario')
    // );

    public function FiltroEmConditionUH($data){
        $conditions = array();

        unset($data['codigo_cliente_name']);
        unset($data['codigo_fornecedorCodigo']);

        if(empty($conditions['UsuarioHistorico.codigo_empresa'])){
            //contempla a empresa do cliente mas procura pelos usuarios adms que estao sem codigo empresa
            $conditions[] = "UsuarioHistorico.codigo_empresa = ". $_SESSION['Auth']['Usuario']['codigo_empresa']. " OR UsuarioHistorico.codigo_empresa IS NULL";
        }

        if (!empty($data['codigo_cliente'])){
            $conditions['ClienteP.codigo'] = $data['codigo_cliente'];
        }

        if (!empty($data['razao_social_cliente'])) {
            $conditions['Cliente.razao_social LIKE'] = '%'.$data['razao_social_cliente'].'%';
        }

         if (!empty($data['nome_fantasia_cliente'])) {
            $conditions['Cliente.nome_fantasia LIKE'] = '%'.$data['nome_fantasia_cliente'].'%';
        }

        if (!empty($data['codigo_fornecedor'])){
            $conditions['Fornecedor.codigo'] = $data['codigo_fornecedor'];
        }

        if (!empty($data['razao_social_fornecedor'])) {
            $conditions['Fornecedor.razao_social LIKE'] = '%'.$data['razao_social_fornecedor'].'%';
        }

        if (!empty($data['nome_fantasia_fornecedor'])) {
            $conditions['Fornecedor.nome LIKE'] = '%'.$data['nome_fantasia_fornecedor'].'%';
        }

        if (!empty($data['codigo_uperfil'])){
            $conditions['Uperfil.codigo'] = $data['codigo_uperfil'];
        }

        if (!empty($data['login'])) {
            $conditions['Usuario.apelido LIKE'] = '%'.$data['login'].'%';
        }

        if(!empty($data["data_inicio"])) {
            $data_inicio = AppModel::dateToDbDate($data["data_inicio"].' 00:00:00');
            $data_fim = AppModel::dateToDbDate($data["data_fim"].' 23:59:59');
            $conditions [] = "(UsuarioHistorico.data_inclusao >= '". $data_inicio . "'";
        }//fim if

        if(!empty($data["data_fim"])) {
            $conditions [] = "UsuarioHistorico.data_inclusao <= '" . $data_fim . "')";
        }

        if(!empty($data['tipo_usuario'])){

            $codigo_tipo_perfil_interno = 5;

            switch ($data["tipo_usuario"]) {
                case 'I':
                    $conditions['TipoPerfil.codigo'] = $codigo_tipo_perfil_interno;
                break;
                case 'E'://data de validade
                    $conditions [] = "TipoPerfil.codigo != '". $codigo_tipo_perfil_interno . "'";
                break;
            }//switch
        }

        return $conditions;
    }

    public function getHistoricoUser($conditions=null){

        $this->Usuario = ClassRegistry::init('Usuario');
        //order
        $order = 'UsuarioHistorico.codigo DESC';
        $joins = array(
            array(
                'table' => $this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable,
                'alias' => 'Usuario',
                'type' => 'INNER',
                'conditions' => 'UsuarioHistorico.codigo_usuario = Usuario.codigo'     
            ),
            array(
                'table' => 'RHHealth.dbo.usuario_multi_cliente',
                'alias' => 'UsuarioMultiCliente',
                'type' => 'LEFT',
                'conditions' => 'Usuario.codigo = UsuarioMultiCliente.codigo_usuario'        
            ),
            array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'ClienteP',
                'type' => 'LEFT',
                'conditions' => 'ClienteP.codigo = Usuario.codigo_cliente'      
            ),
            array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'ClienteS',
                'type' => 'LEFT',
                'conditions' => 'ClienteS.codigo = Usuario.codigo_cliente'        
            ),
            array(
                'table' => 'RHHealth.dbo.usuario_multi_fornecedor',
                'alias' => 'UsuarioMultiFornecedor',
                'type' => 'LEFT',
                'conditions' => 'Usuario.codigo = UsuarioMultiFornecedor.codigo_usuario'        
            ),
            array(
                'table' => 'RHHealth.dbo.fornecedores',
                'alias' => 'Fornecedor',
                'type' => 'LEFT',
                'conditions' => 'Fornecedor.codigo = Usuario.codigo_fornecedor'        
            ),
            array(
                'table' => 'RHHealth.dbo.uperfis',
                'alias' => 'Uperfil',
                'type' => 'LEFT',
                'conditions' => 'Uperfil.codigo = Usuario.codigo_uperfil'        
            ),
            array(
                'table' => 'RHHealth.dbo.tipos_perfis',
                'alias' => 'TipoPerfil',
                'type' => 'LEFT',
                'conditions' => 'TipoPerfil.codigo = Uperfil.codigo_tipo_perfil'        
            ),
            array(
                'table' => 'RHHealth.dbo.sistema',
                'alias' => 'Sistema',
                'type' => 'LEFT',
                'conditions' => 'Sistema.codigo = UsuarioHistorico.codigo_sistema'        
            ),
        );

        $fields = array(
            'UsuarioHistorico.codigo',
            'UsuarioHistorico.data_inclusao',
            'UsuarioHistorico.data_logout',
            'UsuarioHistorico.codigo_usuario',
            'UsuarioHistorico.remote_addr',
            'UsuarioHistorico.http_user_agent',
            'UsuarioHistorico.message',
            'UsuarioHistorico.fail',
            'UsuarioHistorico.codigo_empresa',
            'UsuarioHistorico.codigo_sistema',
            'Usuario.codigo',
            'Usuario.nome',
            'Usuario.apelido',
            'TipoPerfil.descricao',
            'TipoPerfil.codigo',
            'Uperfil.descricao',
            'Uperfil.codigo',
            'ClienteP.codigo',
            'ClienteP.razao_social',
            'ClienteP.nome_fantasia',
            'CAST(UsuarioHistorico.data_inclusao AS DATE) AS data_acesso',
            'CAST(UsuarioHistorico.data_inclusao AS time) AS hora_acesso',
            'CONVERT(CHAR(8), DATEADD(ss,DATEDIFF(ss, UsuarioHistorico.data_inclusao,UsuarioHistorico.data_logout),CAST(0 AS DATETIME)), 108) as tempo_logado',
            'CAST(UsuarioHistorico.data_logout AS time) as hora_logout',
            'CASE
                WHEN TipoPerfil.codigo = 5 THEN \'INTERNO\'
                WHEN TipoPerfil.codigo != 5 THEN \'EXTERNO\'
            END as tipo_perfil',
            'Sistema.descricao',
            'Sistema.codigo'
        );

        $group = array(
            'Usuario.codigo',
            'Usuario.nome',
            'Usuario.apelido',
            'TipoPerfil.descricao',
            'TipoPerfil.codigo',
            'Uperfil.descricao',
            'Uperfil.codigo',
            'ClienteP.codigo',
            'ClienteP.razao_social',
            'ClienteP.nome_fantasia',
            'UsuarioHistorico.codigo',
            'UsuarioHistorico.data_inclusao',
            'UsuarioHistorico.data_logout',
            'UsuarioHistorico.codigo_usuario',
            'UsuarioHistorico.remote_addr',
            'UsuarioHistorico.http_user_agent',
            'UsuarioHistorico.message',
            'UsuarioHistorico.fail',
            'UsuarioHistorico.codigo_empresa',
            'UsuarioHistorico.codigo_sistema',
            'CAST(UsuarioHistorico.data_inclusao AS DATE)',
            'Sistema.descricao',
            'Sistema.codigo'
        );

        $dados = array(
            'conditions' => $conditions,
            'joins' => $joins,
            'fields' => $fields,
            'order' => $order,
            'group' => $group
        );

        // debug($dados);exit;
        // pr( $this->find('sql',$dados) );exit;

        return $dados;
    }
}
?>