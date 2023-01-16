<?php
class OOcorrencias extends AppModel {
    var $name = 'OOcorrencias';
    var $useDbConfig = 'ocomon';
    var $databaseTable = 'ocomon_rc6';
    var $useTable = 'ocorrencias';
    var $primaryKey = 'numero';
    var $displayField = 'descricao';

    public function converteFiltroEmCondition($data)
    {
 		$conditions = array();

		if (!empty($data['data_inicial']) AND !empty($data['data_final']))
        {
			$data_ini_ray = explode("/", $data['data_inicial']);
            $data_inicial = "$data_ini_ray[2]"."-"."$data_ini_ray[1]"."-"."$data_ini_ray[0]";

            $data_fim_ray = explode("/", $data['data_final']);
            $data_final   = "$data_fim_ray[2]"."-"."$data_fim_ray[1]"."-"."$data_fim_ray[0]";

            $conditions['OOcorrencias.data_fechamento BETWEEN ? AND ?'] = array($data_inicial,$data_final);
        }

        if(empty($data['problema']))
        {
        	$conditions['OOcorrencias.problema'] = array(181, 68, 176);
        }
        else
       	{
       		$conditions['OOcorrencias.problema'] = $data['problema'];
       	}



		return $conditions;
    }

    public function buscaOcorrencias($conditions) {

        $this->bindModel(array('hasOne' => array('OProblemas' => array('foreignKey' => false, 
                    'conditions' => 'OProblemas.prob_id = OOcorrencias.problema'))));

        $this->bindModel(array('hasOne' => array('OUsuarios' => array('foreignKey' => false, 
                    'conditions' => 'OUsuarios.user_id = OOcorrencias.operador'))));

        $fields = array('OOcorrencias.numero','OProblemas.problema', 'OUsuarios.nome', 'OOcorrencias.descricao','OOcorrencias.data_fechamento');
        $order = array('OOcorrencias.data_fechamento desc');

        $conditions['OOcorrencias.sistema'] = 56;
        $conditions['OOcorrencias.operador NOT'] = array(415, 1086);
        $conditions['OOcorrencias.status'] = 4;
        if(!isset($conditions['OOcorrencias.data_fechamento BETWEEN ? AND ?'])){ $conditions['OOcorrencias.data_fechamento BETWEEN ? AND ?'] = array(date('d/m/Y'),date('d/m/Y'));}

    	return $this->find('all',compact('conditions', 'fields', 'order'));
    }



    public function buscaFechadasPorDia($conditions, $DiaAtendido) {

        
        $this->bindModel(array('hasOne' => array('OProblemas' => array('foreignKey' => false, 
                    'conditions' => 'OProblemas.prob_id = OOcorrencias.problema'))));

        $this->bindModel(array('hasOne' => array('OUsuarios' => array('foreignKey' => false, 
                    'conditions' => 'OUsuarios.user_id = OOcorrencias.operador'))));

        $fields = array('COUNT(OOcorrencias.numero) AS fechadas_dia, OProblemas.problema, OProblemas.prob_id');
        $group = array('OProblemas.problema');

        $conditions['OOcorrencias.sistema'] = 56;
        $conditions['OOcorrencias.operador NOT'] = array(415, 1086);
        $conditions['OOcorrencias.status'] = 4;

       	$conditions['OOcorrencias.data_fechamento BETWEEN ? AND ?'] = array($DiaAtendido." 00:00:00", $DiaAtendido." 23:59:59");




    	return $this->find('all',compact('conditions', 'fields', 'group'));
    }



	public function buscaBacklogPrimeAbertas($conditions, $prime)
	{
        $fields = array('COUNT(OOcorrencias.numero) AS backlog_abertas');

        $conditions['OOcorrencias.sistema'] = 56;
		$conditions['OOcorrencias.data_abertura <'] = $prime;
        $conditions['OOcorrencias.operador NOT'] = array(415, 1086);

       	unset($conditions['OOcorrencias.data_fechamento BETWEEN ? AND ?']);
       	return $this->find('all',compact('conditions', 'fields'));
	}

	public function buscaBacklogPrimeFechadas($conditions, $prime)
	{
        $fields = array('COUNT(OOcorrencias.numero) AS backlog_fechadas');

        $conditions['OOcorrencias.sistema'] = 56;
		$conditions['OOcorrencias.data_abertura <'] = $prime;
        $conditions['OOcorrencias.operador NOT'] = array(415, 1086);
        $conditions['OOcorrencias.data_fechamento <'] = $prime;

       	unset($conditions['OOcorrencias.data_fechamento BETWEEN ? AND ?']);
       	return $this->find('all',compact('conditions', 'fields'));
	}


	public function buscaFechadasPorTipo($conditions)
	{
        $this->bindModel(array('hasOne' => array('OProblemas' => array('foreignKey' => false, 
                    'conditions' => 'OProblemas.prob_id = OOcorrencias.problema'))));
        $this->bindModel(array('hasOne' => array('OUsuarios' => array('foreignKey' => false, 
                    'conditions' => 'OUsuarios.user_id = OOcorrencias.operador'))));

        $fields = array('COUNT(OOcorrencias.numero) as total','OProblemas.problema');
        $order = array('OOcorrencias.data_fechamento desc');
        $group = array('OProblemas.problema');

        $conditions['OOcorrencias.sistema'] = 56;
        $conditions['OOcorrencias.operador NOT'] = array(415, 1086);
        $conditions['OOcorrencias.status'] = 4;
        if(!isset($conditions['OOcorrencias.data_fechamento BETWEEN ? AND ?'])){ $conditions['OOcorrencias.data_fechamento BETWEEN ? AND ?'] = array(date('d/m/Y'),date('d/m/Y'));}

    	return $this->find('all',compact('conditions', 'fields', 'order', 'group'));
	}


	public function buscaAbertasSemana($conditions)
	{


        $fields = array('COUNT(OOcorrencias.numero) AS total_abertas');

        $conditions['OOcorrencias.sistema'] = 56;
        $conditions['OOcorrencias.status <>'] = 12;
        $conditions['OOcorrencias.operador NOT'] = array(415, 1086);
        if(!isset($conditions['OOcorrencias.data_abertura BETWEEN ? AND ?']))
        { 
        	$conditions['OOcorrencias.data_abertura BETWEEN ? AND ?'] = 
        	array($conditions['OOcorrencias.data_fechamento BETWEEN ? AND ?'][0],
        		$conditions['OOcorrencias.data_fechamento BETWEEN ? AND ?'][1]);
        }

        unset($conditions['OOcorrencias.data_fechamento BETWEEN ? AND ?']);
       	
 		
       	return $this->find('all',compact('conditions', 'fields'));

	}

	public function buscaAnalistaSemana($conditions)
	{
        $this->bindModel(array(
            'hasOne' => array(
                'OUsuarios' => array(
                    'foreignKey' => false, 
                    'conditions' => 'OUsuarios.user_id = OOcorrencias.operador'))));

        $fields = array('OUsuarios.user_id', 'OUsuarios.nome','COUNT(OOcorrencias.numero) AS total_fechadas');
        $order = array('COUNT(OOcorrencias.numero) desc');
        $group = array('OUsuarios.nome');
        $conditions['OOcorrencias.sistema'] = 56;
        $conditions['OOcorrencias.status'] = 4;
        $conditions['OOcorrencias.operador NOT'] = array(415, 1086);

 
        return $this->find('all',compact('conditions', 'fields', 'order', 'group'));
	}

 












}

?>

