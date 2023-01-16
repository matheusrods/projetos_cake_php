<?php 
class QuestaoResposta extends AppModel {
	var $name = 'QuestaoResposta';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
	var $useTable = 'questao_resposta';
    var $primaryKey = 'codigo';
    
    var $belongsTo = array(
    	'Resposta' => array(
    		'className' => 'Resposta',
    		'conditions' => 'QuestaoResposta.codigo_resposta  = Resposta.codigo',
    		'foreignKey' => false
    	)
    );
    
    function listarRespostas($questao_codigo){
    	$respostas = $this->find('all', array('conditions'=>array('codigo_questao'=>$questao_codigo)));
    	return Set::combine($respostas, '/QuestaoResposta/codigo', '/Resposta/descricao');
    }
}
?>