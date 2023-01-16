<?php
class PosObsParticipantes extends AppModel {

	public $name		   	= 'PosObsParticipantes';

	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pos_obs_participantes';
	var $primaryKey = 'codigo';
    var $recursive = 2;

    // public $hasMany  =  array ( 
    //     'PosObsObservacoes',
    // );

	// var $hasOne  =  array ( 
        
    //     'PosObsParticipaUsuarios' => array(
    //         'className'     => 'PosObsParticipaUsuarios',
    //         'foreignKey'    => 'codigo_usuario',
    //         'fields'    => array('cpf'),
    //         'conditions'    => array('PosObsParticipaUsuarios.codigo_usuario' => 'codigo_usuario'),
    //         'dependent'=> true
    //     ),

    // ); 
    

    // var $belongsTo  =  array ( 

	// 	// tem que estar 
    //     'UsuarioParticipante'  =>  array ( 
    //         'className'     =>  'Usuario' , 
    //         'foreignKey'     =>  'codigo_usuario' 
	// 	),

    // );

    // var $hasOne  =  array ( 
        
    //     'UsuarioParticipante' => array(
    //         'className'     => 'Usuario',
    //         'foreignKey'    => 'codigo_usuario',
    //         'conditions'    => array('Usuario.codigo' => 'PosObsParticipaUsuarios.codigo_usuario'),
    //         'dependent'=> true
    //     ),	
    // );
    //     // 'UsuariosDados' => array(
    //     //     'className'     => 'UsuariosDados',
    //     //     'foreignKey'    => 'codigo_usuario',
    //     //     //'associationForeignKey' => 'codigo_usuario',
    //     //     'joinTable' => 'usuarios_dados',
    //     //     'fields'    => array('codigo_usuario','cpf'),
    //     //     'conditions'    => array('UsuariosDados.codigo_usuario' => 73275),
    //     //     //'conditions'    => array('UsuariosDados.ativo' => 1),
    //     //     'dependent'=> true
    //     // ),	
    
    //     'UsuariosD' => array (
    //             'className' => 'UsuariosDado',
    //             'joinTable' => 'usuarios_dados',
    //             'foreignKey' => 'codigo_usuario',
    //             'associationForeignKey' => 'codigo_usuario',
    //             'conditions' => array (
    //                     'UsuariosD.codigo_usuario' => 73275 
    //             ),
    //             'fields' => array (
    //                     'UsuariosD.cpf' 
    //             ) 
    //     ) 

    // );

    
	function bindUsuario() {
		$this->bindModel(array(
			'belongsTo' => array(
				'Usuario' => array(
					'foreignKey' => 'codigo_usuario'
				)
			)
		));
	}

    public function obterParticipantes($codigo_observacao ){

        $conditions = array();
        $conditions['codigo_pos_obs_observacao'] = $codigo_observacao;
        
        $this->bindUsuario();

		return $this->find('all', array(
			//'fields' => $fields,
			'joins'=> array(),
			'conditions' => $conditions,
			'limit' => 1,
			'recursive' => 2
		));        
    }
}