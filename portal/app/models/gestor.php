<?php
class Gestor extends AppModel {
    var $name = 'Gestor';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'usuario';
    var $primaryKey = 'codigo';


    
    function listar($tipo = 'all', array $options = null) {
        App::import('Model', 'Departamento');
        $options = (array) $options;

        // 9 - Dpto. Comercial;
        $dfOptions = array();
        $dfOptions['conditions']['codigo_departamento'] = Departamento::COMERCIAL;
        $dfOptions['conditions']['codigo_cliente'] = null;
        
        $options = array_merge_recursive($dfOptions, $options);
        
        return $this->find($tipo, $options);
    }
    
    function listarNomesGestoresAtivos() {
    	$usuario = & ClassRegistry::init('Usuario');
        return $usuario->listaUsuariosAtivos($gestor = true);
    }

    function verifica_se_usuairo_gestor($codigo_usuario, $verifica_tipo_gestor = FALSE) {
        App::import('Model', 'Departamento');
        App::import('Model', 'Uperfil');
        $conditions = array('Gestor.codigo' => $codigo_usuario,
                            'OR' => array(array('Gestor.codigo_departamento' => Departamento::COMERCIAL,
                                                'Gestor.codigo_uperfil' => Uperfil::GESTOR_COMERCIAL,
                                                'Gestor.codigo_cliente' => NULL),
                                                'Gestor.codigo_departamento' => Departamento::GESTOR_NPE
                                                )
                                    );
        $retorno = $this->find('first' , compact('conditions'));
        if(count($retorno) > 0) {
            return $retorno;
        }else {
            return FALSE;
        }
    }    

}
