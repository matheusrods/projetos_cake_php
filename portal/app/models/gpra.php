<?php
class Gpra extends AppModel {

	public $name = 'Gpra';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'grupos_prevencao_riscos_ambientais';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure','Containable', 'Loggable' => array('foreign_key' => 'codigo_grupos_prevencao_riscos_ambientais'));

	public $validate = array(
		'codigo_medico' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Profissional responsável',
			'required' => true
			)
		);

	public $hasMany = array(
        'PrevencaoRiscoAmbiental' => array(
            'className' => 'PrevencaoRiscoAmbiental',
            'foreignKey' => 'codigo_grupo_prevencao_risco_ambiental',
            'dependent' => true,
            'order' => 'PrevencaoRiscoAmbiental.codigo ASC'
        )
    );

	public function afterFind($dados) {
		if(!empty($dados[0][$this->name]['data_inicio_vigencia'])) {

			//verifica se tem barra para nao dar erro na funcao
			if(strpos($dados[0][$this->name]['data_inicio_vigencia'], "/")) {
				$dados[0][$this->name]['data_inicio_vigencia'] = comum::formataData($dados[0][$this->name]['data_inicio_vigencia'], 'dmy', 'dmy');
			}//fim verificacao
		}
		return $dados;
	}

}