<?php
class Equipamento extends AppModel {
    var $name = 'Equipamento';
    var $tableSchema = 'dbo';
    var $databaseTable = 'Monitora';
    var $useTable = 'System_Monitora';
    var $primaryKey = 'Codigo';
    var $actsAs = array('Secure');
    var $displayField = 'Descricao';

    const MON_TELEMONITORADO = '000012';

    public function buscaPorCodigoGuardian($tecn_codigo,$fields = array('Codigo','Descricao','codigo_dbbouonny_tecnologia','guadian_tecn_codigo','Modelo')){
    	if(!$tecn_codigo)
            return FALSE;
        
        $conditions = array('guadian_tecn_codigo' => $tecn_codigo);
		return $this->find('first',compact('conditions','fields'));
    }
}
?>