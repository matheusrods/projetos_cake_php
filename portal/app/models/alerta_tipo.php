<?php
class AlertaTipo extends AppModel {

	var $name = 'AlertaTipo';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'alertas_tipos';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a descrição',
		),
        'codigo_alerta_agrupamento' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o tipo do alerta',
        ),
	);

    CONST ALERTA_EXAMES_A_VENCER = 1;
    CONST ALERTA_EXAMES_SUGESTAO_PCMSO = 4;
    CONST ATESTADOS_SEM_CID = 5;
        
	function listarTipoAlerta($filtros = array(),$perfil = null,$tipo_usuario = null){
		$conditions = $this->converteFiltrosEmConditions($filtros);
        if(!empty($perfil) && !empty($tipo_usuario)){
            $codigos = $this->lista_alertas_sem_permissao_perfil($perfil,$tipo_usuario);
            if(!empty($codigos)){
                $conditions[] = array(
                    'NOT' => array(
                        'codigo' => $codigos
                    )
                );    
            }
        }
        $order = 'descricao';
		return $this->find('all', compact('conditions','order'));
	}   

    function beforeSave() {
        if (isset($this->data['AlertaTipo']['interno'])) {
            if (empty($this->data['AlertaTipo']['interno'])) {
                $this->data['AlertaTipo']['interno'] = 'N';
            }
        }
        return true;
    }
	
	function converteFiltrosEmConditions($filtros){
    	$conditions = array();

    	if(isset($filtros['AlertaTipo']['descricao']) && $filtros['AlertaTipo']['descricao']){
    		$conditions['descricao LIKE'] = '%'.$filtros['AlertaTipo']['descricao'].'%';    	
        }

        if(isset($filtros['AlertaTipo']['interno']) && $filtros['AlertaTipo']['interno']){
            $conditions['interno'] = $filtros['AlertaTipo']['interno'];        
        }
   

    	return $conditions;
    }	

    function excluir($codigo){
    	$UsuarioAlertaTipo =& ClassRegistry::init("UsuarioAlertaTipo");
    	try{
    		$this->query("BEGIN TRANSACTION");

    		if(!$UsuarioAlertaTipo->excluirPorTipoAlerta($codigo,true))
    			throw new Exception();

    		if(!parent::excluir($codigo))
    			throw new Exception();

    		$this->commit();
    		return true;
    	}catch(Exception $ex){
    		$this->rollback();
    		return false;
    	}
    }

    function lista_alertas_sem_permissao_perfil($perfil,$tipo_usuario = false){
        $this->UperfilTipoAlerta =& ClassRegistry::init("UperfilTipoAlerta");
        $codigos = array();
        if($tipo_usuario == 'S'){
            $condicao = "interno = 'S'";
        }else{
            $condicao = "interno = 'N' OR interno IS NULL";
        }
        
        $alertas =  $this->query("
            SELECT codigo FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable}
            WHERE ($condicao) and codigo IN (
                SELECT codigo_alerta_tipo FROM {$this->UperfilTipoAlerta->databaseTable}.{$this->UperfilTipoAlerta->tableSchema}.{$this->UperfilTipoAlerta->useTable} group by codigo_alerta_tipo
            )AND codigo NOT IN(
                SELECT codigo_alerta_tipo FROM {$this->UperfilTipoAlerta->databaseTable}.{$this->UperfilTipoAlerta->tableSchema}.{$this->UperfilTipoAlerta->useTable} WHERE codigo_uperfil = $perfil
            )
        ");

        foreach ($alertas as $codigo) {
            $codigos[] = $codigo[0]['codigo'];
        }

        return $codigos;
    }

    function lista_alertas_cliente($alertasTiposLista,$listar_tipos_alertas){

    	$alertas_agrupados = array();
        foreach($alertasTiposLista as $value){
           foreach ($listar_tipos_alertas as $key => $tipo) {    
              if($tipo['AlertaAgrupamento']['codigo'] == $value['AlertaTipo']['codigo_alerta_agrupamento']){
                $alertas_agrupados['alerta_tipo_'.$tipo['AlertaAgrupamento']['descricao']][$value['AlertaTipo']['codigo']] =  $value['AlertaTipo']['descricao'];
              }
           }
        }
        return $alertas_agrupados;
    }
}
?>
