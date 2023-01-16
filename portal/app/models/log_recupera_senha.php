<?php
class LogRecuperaSenha extends AppModel {
    var $name = 'LogRecuperaSenha';
    var $tableSchema = 'portal';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'log_recupera_senha';
    var $primaryKey = 'codigo';    
    var $actsAs = array('Secure');  
    var $belongsTo = array(
        'Usuario' => array(
            'className' => 'Usuario',
            'foreignKey' => false,
            'conditions' => array('Usuario.codigo = LogRecuperaSenha.codigo_usuario')
        )
    ); 
    
    function incluir_log($usuario,$email){
        $ip = $_SERVER['REMOTE_ADDR'];
        $navegador = $_SERVER["HTTP_USER_AGENT"];

        $data['LogRecuperaSenha'] = array(
            'codigo_usuario' => $usuario['Usuario']['codigo'],
            'ip' => $ip,
            'navegador' => $navegador,            
            'email' => $email
        );
        
        return parent::incluir($data);
    }

    function convertFiltrosEmConditions($data){
        $conditions = null;
        if(isset($data['ip']) && !empty($data['ip'])){
            $conditions['ip LIKE'] = '%'.$data['ip'].'%';
        }
        if(isset($data['usuario']) && !empty($data['usuario'])){
            $conditions['Usuario.apelido'] = $data['usuario'];
        }
        if (isset($data['data_inicial']) && !empty($data['data_inicial']) && isset($data['data_final']) && !empty($data['data_final'])){
            $conditions['LogRecuperaSenha.data_inclusao BETWEEN ? AND ?'] = array(
                AppModel::dateToDbDate2($data['data_inicial']).' 00:00:00',
                AppModel::dateToDbDate2($data['data_final']).' 23:59:59'
            );   
            
        } 

        return $conditions;
    }
}
?>