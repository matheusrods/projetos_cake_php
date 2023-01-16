<?php
class Alerta extends AppModel {

    var $name = 'Alerta';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'alertas';
    var $primaryKey = 'codigo'; 
    var $actsAs = array('Secure');

    function bindAlertaTipo(){
        $this->bindModel(array(
            'belongsTo' => array(
                'AlertaTipo' => array(
                    'foreignKey' => 'codigo_alerta_tipo',
                ),
            ),
        ));
    }         

    function contarAlertasPendentes($codigo_cliente) {
        $usuario = ClassRegistry::init('Usuario');
    	$usuarioAlertaTipo = ClassRegistry::init('UsuarioAlertaTipo');
        $codigo_usuario_logado = $_SESSION['Auth']['Usuario']['codigo'];
        return $this->find('count', array(
            'conditions'=>array(
                'Alerta.codigo_cliente'=>$codigo_cliente,
                'Alerta.data_tratamento'=>null,
                'OR' => array(array('Alerta.codigo_usuario_tratamento' => null), array('Alerta.codigo_usuario_tratamento' => $codigo_usuario_logado)),
                'UsuarioCliente.codigo' => $codigo_usuario_logado
            ),
            'joins' => array(
                array(
                    'table' => "{$usuario->databaseTable}.{$usuario->tableSchema}.{$usuario->useTable}",
                    'alias' => 'UsuarioCliente',
                    'type' => 'INNER',
                    'conditions' => array(
                        'UsuarioCliente.codigo_cliente = Alerta.codigo_cliente'
                    )
                ),
                array(
                    'table' => "{$usuarioAlertaTipo->databaseTable}.{$usuarioAlertaTipo->tableSchema}.{$usuarioAlertaTipo->useTable}",
                    'alias' => 'UsuarioAlertaTipo',
                    'type' => 'INNER',
                    'conditions' => array(
                        'UsuarioAlertaTipo.codigo_alerta_tipo = Alerta.codigo_alerta_tipo',
                        'UsuarioAlertaTipo.codigo_usuario = UsuarioCliente.codigo'
                    )
                ),
            ),
        ));
    }

    function listarAlertasPendentes($codigo_cliente, $page=1) {
        $usuario = ClassRegistry::init('Usuario');
        $usuarioAlertaTipo = ClassRegistry::init('UsuarioAlertaTipo');
    	$count = $this->contarAlertasPendentes($codigo_cliente);
    	$codigo_usuario_logado = $_SESSION['Auth']['Usuario']['codigo'];
    	$top = $page * 10;
    	$limit = 10;
    	if($count < $top)
    		$limit = $count - (($page - 1) * 10);
    	
    	$query = "SELECT * FROM (
    				SELECT TOP {$limit} * FROM (
    					SELECT TOP {$top} 
    						Alerta.codigo, 
    						Alerta.codigo_cliente, 
    						Alerta.descricao, 
    						Alerta.codigo_usuario_tratamento,
    						CONVERT(VARCHAR(20), Alerta.data_inclusao, 20) AS data_inclusao,
    						Usuario.nome AS nome_usuario_tratamento
    					FROM {$this->tableSchema}.alertas AS Alerta 
    					LEFT JOIN {$usuario->tableSchema}.usuario AS Usuario ON Alerta.codigo_usuario_tratamento = Usuario.codigo
                        INNER JOIN {$usuario->tableSchema}.usuario AS UsuarioCliente
                        ON UsuarioCliente.codigo_cliente = Alerta.codigo_cliente
                        INNER JOIN {$usuarioAlertaTipo->tableSchema}.usuarios_alertas_tipos AS UsuarioAlertaTipo ON UsuarioAlertaTipo.codigo_alerta_tipo = Alerta.codigo_alerta_tipo
                        AND UsuarioAlertaTipo.codigo_usuario = UsuarioCliente.codigo
    					WHERE Alerta.codigo_cliente = {$codigo_cliente} AND Alerta.data_tratamento IS NULL AND 
					        (Alerta.codigo_usuario_tratamento IS NULL OR Alerta.codigo_usuario_tratamento = {$codigo_usuario_logado}) AND
                            UsuarioCliente.codigo = {$codigo_usuario_logado}
    					ORDER BY Alerta.data_inclusao ASC
    				) AS Set1 ORDER BY data_inclusao DESC
    			) AS Set2 ORDER BY data_inclusao ASC";
    	
    	$dados = $this->query($query);
    	foreach($dados as $key=>$value){
    		$alerta = current($value);
    		$alerta['data_inclusao'] = $this->dbDateToDate($alerta['data_inclusao']);
    		$alerta['atribuido'] = (!empty($alerta['codigo_usuario_tratamento']) && $alerta['codigo_usuario_tratamento'] == $_SESSION['Auth']['Usuario']['codigo']);
    		unset($alerta['codigo_usuario_tratamento']);
    		$dados[$key] = array('Alerta'=>$alerta);
    		
    	}
    	return $dados;
    }
    
    function listaTodosPendentes($limit = null) {
        $this->bindModel(array(
            'belongsTo' => array(
                'AlertaTipo' => array(
                    'foreignKey' => 'codigo_alerta_tipo',
                    'type' => 'INNER',
                ),
            ),
        ));
        return $this->find('all', array(
            'fields'=>array('codigo', 'codigo_cliente', 'descricao','descricao_email', 'email_agendados','sms_agendados','AlertaTipo.descricao','AlertaTipo.codigo','model','foreign_key', 'assunto', 'AlertaTipo.interno'),
            'conditions'=>array(
                'data_tratamento'=>null, 
                'model IS NOT NULL',
                'foreign_key IS NOT NULL',
                'or' => array(
                    array('email_agendados'=>null), 
                    array('email_agendados'=>0),
                    // array('sms_agendados'=>null), 
                    // array('sms_agendados'=>0),
                )
            ),
            'limit'=>$limit,
            'order' => 'Alerta.data_inclusao'
        ));
    }

    function listaTodosPendentesEmail($limit = null) {
        $this->bindModel(array(
            'belongsTo' => array(
                'AlertaTipo' => array(
                    'foreignKey' => 'codigo_alerta_tipo',
                    'type' => 'INNER',
                ),
            ),
        ));
        return $this->find('all', array(
            'fields'=>array('codigo', 'codigo_cliente', 'descricao_email', 'AlertaTipo.descricao','AlertaTipo.codigo','model','foreign_key', 'assunto', 'AlertaTipo.interno'),
            'conditions'=>array('data_tratamento'=>null, 'or' => array(array('email_agendados'=>null), array('email_agendados'=>0))),
            'limit'=>$limit
        ));
    }
    
    function listaTodosPendentesSms($limit = null) {
        $this->bindModel(array(
            'belongsTo' => array(
                'AlertaTipo' => array(
                    'foreignKey' => 'codigo_alerta_tipo',
                    'type' => 'INNER',
                ),
            ),
        ));
        return $this->find('all', array(
            'fields'=>array('codigo', 'codigo_cliente', 'descricao', 'AlertaTipo.descricao','AlertaTipo.codigo','model','foreign_key', 'assunto','AlertaTipo.interno'),
            'conditions'=>array('data_tratamento'=>null, 'or' => array(array('sms_agendados'=>null), array('sms_agendados'=>0))),
            'limit'=>$limit
        ));
    }

    function listaTodosPendentesWsPorCliente( $codigo_cliente, $limit = null) {
        $MRmaEstatistica =& ClassRegistry::init('MRmaEstatistica');
        // $MRmaOcorrencia =& ClassRegistry::init('MRmaOcorrencia');
        // $MGeradorOcorrencia =& ClassRegistry::init('MGeradorOcorrencia');
        $TViagViagem =& ClassRegistry::init('TViagViagem');
        $TUsuaUsuario =& ClassRegistry::init('TUsuaUsuario');

        $alertas = $this->find('all', array(
            'fields' => array(
                'codigo', 
                'codigo_cliente', 
                'descricao', 
                'model', 
                'foreign_key',
                'data_inclusao'
            ),
            'conditions' => array(
                'data_tratamento' => null, 
                'codigo_cliente' => $codigo_cliente,
                'model' => 'TViagViagem',
                'or' => array(
                    'ws_agendados' => null, 
                    'ws_agendados' => 0
                )
            ),
            'limit' => $limit
        ));

        $rmas = array();
        foreach($alertas as $alerta){
            $viagem = $TViagViagem->find('first',array('fields' => array('viag_codigo_sm AS viag_codigo_sm'),'conditions' => array('viag_codigo' => $alerta['Alerta']['foreign_key'])));
            
            $MRmaEstatistica->bindModel(array('belongsTo' => array(
                'MRmaOcorrencia' => array('foreignKey' => false, 'conditions' => array('MRmaOcorrencia.ID_OCORRENCIA = MRmaEstatistica.occorencia_1 OR MRmaOcorrencia.ID_OCORRENCIA = MRmaEstatistica.OCORRENCIA_2
                OR MRmaOcorrencia.ID_OCORRENCIA = MRmaEstatistica.OCORRENCIA_3', 'MRmaOcorrencia.tipo' => 'RMA'), 'type' => 'INNER'),
                'MGeradorOcorrencia' => array('foreignKey' => false, 'conditions' => 'MGeradorOcorrencia.codigo = MRmaOcorrencia.codigo_gerador_ocorrencia'),
            )), false);
            
            $joins = array(
                array(
                    'table' => "(SELECT * FROM openquery(LK_GUARDIAN,'SELECT usua_pfis_pess_oras_codigo, usua_login FROM {$TUsuaUsuario->databaseTable}.{$TUsuaUsuario->tableSchema}.{$TUsuaUsuario->useTable}'))",
                    'alias' => 'TUsuaUsuario',
                    'type'  => 'LEFT',
                    'conditions' => array('TUsuaUsuario.usua_pfis_pess_oras_codigo = MRmaEstatistica.Operador')
                )
            );
            if($this->useDbConfig == 'test_suite'){
                $joins[0]['table'] = "{$TUsuaUsuario->databaseTable}.{$TUsuaUsuario->tableSchema}.{$TUsuaUsuario->useTable}";                
            }else{
                $MRmaEstatistica->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
            }
            
            $fields = array(
                'MRmaEstatistica.codigo_sm AS sm', 'MRmaEstatistica.PLACA AS placa', 'MRmaEstatistica.REFERENCIA AS local', 'MGeradorOcorrencia.descricao AS fato_gerador', 
                'MRmaOcorrencia.OCORRENCIA AS tipo_ocorrencia', 'TUsuaUsuario.usua_login AS operador', 'CONVERT(VARCHAR(20),DTA_CAD,120) as data_hora' 
            );
            $order = 'MRmaEstatistica.DTA_CAD DESC';
            $rma  = $MRmaEstatistica->find('first', array( 
                'conditions' => array('codigo_sm'=>$viagem[0]['viag_codigo_sm'], 'MRmaEstatistica.DTA_CAD <=' => AppModel::dateTimeToDbDateTime2($alerta['Alerta']['data_inclusao'])), 
                'joins'      => $joins, 
                'order'      => $order, 
                'fields'     => $fields 
                ) 
            );
            if($rma){
                $rmas[$alerta['Alerta']['codigo']] = $rma[0];
            }
        }
        return $rmas;
    }
    
    function atribuir($codigo_alerta) {
    	$this->updateAll(array('codigo_usuario_tratamento'=>$_SESSION['Auth']['Usuario']['codigo']), array('codigo'=>$codigo_alerta));
    }
    
    function desatribuir($codigo_alerta) {
        $this->id = $codigo_alerta;
    	$this->saveField('codigo_usuario_tratamento', null);
    }
    
    function tratar($data) {
    	$data['Alerta']['data_tratamento'] = date('Y-m-d H:d:s');
    	return $this->save($data);
    }
}
