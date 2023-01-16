<?php 
class FichaScorecardQuestao extends AppModel {
	var $name = 'FichaScorecardQuestao';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'ficha_scorecard_questao';
    var $primaryKey = 'codigo';
    
    var $belongsTo = array(
    		'Questao'=>array('className'=>'Questao', 'conditions'=>'FichaScorecardQuestao.codigo_questao = Questao.codigo', 'foreignKey'=>false)
    	);
    
    function buscarQuestionarioFicha(){
    	$questoes = $this->find('all', array('fields'=>array('Questao.codigo', 'Questao.descricao')));
    	
    	$this->QuestaoResposta = ClassRegistry::init('QuestaoResposta');
    	foreach($questoes as $key=>$questao){
    		$questoes[$key]['Questao']['respostas'] = $this->QuestaoResposta->listarRespostas($questao['Questao']['codigo']);
    	}
    	
    	return $questoes;
    }
}
?>