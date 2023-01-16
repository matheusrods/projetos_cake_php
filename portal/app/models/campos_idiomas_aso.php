<?php

class CamposIdiomasAso extends AppModel {

    var $name = 'CamposIdiomasAso';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'campos_idiomas_aso';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    function listar($codigo_idioma){
        
        //var_dump($codigo_idioma);

        $aux = array();
        $retorno = array();

        $conditions = array("idioma IN ($codigo_idioma)");
        $return = $this->find('all', array(
            'fields' => array('campo', 'titulo'),
            'conditions' => $conditions
        ));

        //debug($return);exit;//completo

        foreach($return as $keyr => $v){
            $v = $v['CamposIdiomasAso'];
        
            //debug($v);

            $campo = $v['campo'];
            $titulo = "";

            foreach($return as $c){
                $c = $c['CamposIdiomasAso'];

                if($c['campo'] == $campo){
                    //foi pedido que colocasse o espaco para que o titulo traduzido foi bem compreendido no relatorio
                    $titulo .= "/".$c['titulo'];                           
                }
            }
            $v['titulo'] = substr($titulo,1);
            
            $aux[] = $v;
        } 
        
        $aux = array_map("unserialize", array_unique(array_map("serialize", $aux)));
        
        //debug($aux);//exit;
        return ($aux);
        
    }
    
}
?>