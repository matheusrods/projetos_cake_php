<?php
class TipoAcao extends AppModel {

	var $name = 'TipoAcao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'tipos_acoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	const CLASSIFICACAO_PPRA = 0;
	const CLASSIFICACAO_PCMSO = 1;

	var $validate = array(
		'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Descrição da ação!',
		),
        'classificacao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a Classificacao da ação!',
        ),
        'status' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Status da ação!',
        ),
	);


	public function submeter(array $data){
	    if(!empty($data['TipoAcao']['codigo'])){
	        return self::atualiza($data);
        }else{
	        return self::inclui($data);
        }
    }

    private function atualiza($data){
        try{
            $return = parent::atualizar($data);

            if(isset($this->validationErrors) && !empty($this->validationErrors))
                throw new Exception(join("; ", $this->validationErrors));

            return array(
                'status' => 'success',
                'message' => 'Dados Atualizados com sucesso!',
                'data' => $return,
            );
        }catch(Exception $ex){
            return array(
                'status' => 'error',
                'message' => $ex->getMessage(),
            );
        }
    }

    private function inclui($data){
        try{
            $return = parent::incluir($data);

            if(isset($this->validationErrors) && !empty($this->validationErrors))
                throw new Exception(join("; ", $this->validationErrors));

            return array(
                'status' => 'success',
                'message' => 'Dados inseridos com sucesso!',
                'data' => $return,
            );
        }catch(Exception $ex){
            return array(
                'status' => 'error',
                'message' => $ex->getMessage(),
            );
        }
    }

    public function converte_filtro_em_conditions_listagem(array $data = array()){
	    $conditions = array();

	    if(isset($data['descricao']) && $data['descricao'] != '')
	        $conditions["TipoAcao.descricao LIKE '%{$data['descricao']}%'"];
        if(isset($data['classificacao']) && $data['classificacao'] != '')
            $conditions['TipoAcao.classificacao'] = $data['classificacao'];
        if(isset($data['status']) && $data['status'] != '')
            $conditions['TipoAcao.status'] = $data['status'];
	    $conditions['TipoAcao.codigo_empresa'] = (empty($data['codigo_empresa']) ? $_SESSION['Auth']['Usuario']['codigo_empresa'] : $data['codigo_empresa']);

	    return $conditions;
    }

    public function get_parametros_para_consulta(array $data = array()){
        $fields = array(
            'TipoAcao.codigo as codigo',
            'TipoAcao.descricao as descricao',
            'CASE TipoAcao.classificacao ' .
                'WHEN \'0\' THEN \'PPRA\' ' .
                'WHEN \'1\' THEN \'PCMSO\' ' .
                'ELSE \'DESCONHECIDO\' ' .
            'END as classificacao',
            'TipoAcao.status as status'
        );
        $conditions = self::converte_filtro_em_conditions_listagem($data);
        $limit = 50;
        $order = 'TipoAcao.descricao ASC';
        return compact('fields', 'conditions', 'limit', 'order');
    }

    public function get_all_ppra_list(){
	    $fields = array('codigo', 'descricao');
	    $conditions = array('status' => 1, 'classificacao' => TipoAcao::CLASSIFICACAO_PPRA);
	    return $this->find('list', compact('fields', 'conditions'));
    }

    public function get_all_pcmso_list(){
        $fields = array('codigo', 'descricao');
        $conditions = array('status' => 1, 'classificacao' => TipoAcao::CLASSIFICACAO_PCMSO);
        return $this->find('list', compact('fields', 'conditions'));
    }

    public function status(array $data){
        try{
            $transformed_data = array(
                'TipoAcao' => array(
                    'codigo' => $data['codigo'],
                    'status' => $data['status'],
                )
            );
            $return = parent::atualizar($transformed_data);

            if(isset($this->validationErrors) && !empty($this->validationErrors))
                throw new Exception(join("; ", $this->validationErrors));

            return array(
                'status' => 'success',
                'message' => 'Status Atualizado com sucesso!',
                'data' => $return,
            );
        }catch(Exception $ex){
            return array(
                'status' => 'error',
                'message' => $ex->getMessage(),
            );
        }
    }

}
