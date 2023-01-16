<?php
class UsuarioIp extends AppModel {
    var $name = 'UsuarioIp';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'usuarios_ips';
    var $primaryKey = 'codigo';    
    var $actsAs = array('Secure');

    var $validate = array(
        'endereco_ip' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'message' => 'Informe o numero IP',
                'on' => 'create'
            ),
            'regExp' => array(
              'rule' => '/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/',
              'message' => 'Informe um numero IP válido',
            ),
        ),
    );    

    function converteFiltroEmCondition( $data ) {
        $conditions = array();        
        if (!empty($data['codigo_usuario']))
            $conditions['UsuarioLog.codigo_usuario'] = $data['codigo_usuario'];
        return $conditions;
    }


    function carregarIp($codigo_usuario, $endereco_ip){
        $conditions = array(
            'codigo_usuario' => $codigo_usuario, 
            'endereco_ip'    => $endereco_ip
        );
        return $this->find('count',compact('conditions'));
    }
}
?>