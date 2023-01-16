<?php

class FichaQuestaoResposta extends AppModel {

    var $name = 'FichaQuestaoResposta';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'ficha_questao_resposta';
    //var $primaryKey = null;
    var $actsAs = array('Secure');

    function duplicar($codigo_ficha_antiga, $codigo_ficha) {
        try {
            $result = $this->query("insert
                        into {$this->tableSchema}.ficha_questao_resposta 
                        (
                            codigo_ficha,
                            codigo_questao_resposta,
                            observacao 
                        )
                        select
                            '{$codigo_ficha}',
                            codigo_questao_resposta,
                            observacao 
                        from
                            {$this->tableSchema}.ficha_questao_resposta
                        where
                            codigo_ficha = '{$codigo_ficha_antiga}'");
                        
            if ($result === false) {
                throw new Exception('Falha ao gravar a ficha_questao_resposta');
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function salvarTodosFicha($data, $codigo_ficha){
        $this->primaryKey = 'codigo_ficha'; //Para funcionar o delete por causa da chave composta da tabela
        $this->delete($codigo_ficha);
        $this->primaryKey = null;
        foreach($data as $key=>$value){
            $data[$key]['codigo_ficha'] = $codigo_ficha;
        }
        return @$this->saveAll($data, array('validate' => false));
    }
}