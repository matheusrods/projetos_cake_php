<?php
class GrupoEmpresa extends AppModel {
	var $name     = 'GrupoEmpresa';
    var $useTable = false;

 //    const BUONNY = 1;
	// const LIDER = 2;
	// const NATEC = 3;
	// const SOLEN = 4;
    const RHHEALTH = 5;

	var $descricoes    = array(
        // self::BUONNY   => 'Buonny',
        // self::LIDER    => 'Líder',
        // self::NATEC    => 'Natec',
        // self::SOLEN    => 'Solen',
        self::RHHEALTH    => 'RHHealth'
    );

    function descricao($grupo) {
    	if(!is_null($grupo) && !empty($grupo))
        	return ClassRegistry::init('GrupoEmpresa')->descricoes[$grupo];
        return '';
    }
    
    function lista(){
    	return $this->descricoes;
    }

    function getDataBase( $codigo ){
        switch ( $codigo ) {
            // case self::BUONNY:
            //     return 'dbNavegarqNatec';
            //     break;
            // case self::LIDER:
            //     return 'dbNavegarqLider';
            //     break;            
            // case self::NATEC:
            //     return 'dbNavegarqNatec';
            //     break;            

            // case self::SOLEN:
            //     return 'dbNavegarqSolen';
            //     break;
            case self::RHHEALTH:
                return 'dbNavegarqNatec';
                break;
            default:
                return 'dbNavegarqNatec';
                break;
        }
    }
}
?>