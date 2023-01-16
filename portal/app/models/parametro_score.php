<?php

class ParametroScore extends AppModel {

    var $name = 'ParametroScore';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'parametros_score';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $validate = array(
            'nivel'=> array(
                'rule' => 'notEmpty',
                'message' => 'Informe Nivel'
            ),
            'valor' => array(
                'rule' => 'valida_valor_maior_que_zero',
                'message' => 'Informe o valor'
            ),
            'pontos' =>array(
                'notEmpty' =>array(
                    'rule' => array('notEmpty'),
                    'message' => 'Informe os pontos  ',
                ),
                'comparison' =>array(
                    'rule' => array('comparison', '<=', 100),
                    'message' => 'Valor maior que 100',
                ),
            ),
        );
    var $displayField  = 'nivel';
    const OURO         = 2;
    const LATAO        = 6;
    const INSUFICIENTE = 7;
    const DIVERGENTE   = 8;
    

    function valida_valor_maior_que_zero($check){
        return($this->ajustaFormatacao($check['valor'])>0);
    }

    function ajustaFormatacao($valor){
        if (strpos($valor, '.')>0 && strpos($valor, ',')>0) {
            $valor = str_replace('.', '', $valor);
        }
        return str_replace(',', '.', $valor);
    }

    function classificaMotorista($percentual, $valores_inferiores=FALSE) {        
        if( $valores_inferiores===TRUE){
            $condition = array('pontos =' => $percentual);
        } else {
            $condition = array('pontos <' => $percentual);
        }
    	return $this->find('first',array('conditions' => $condition, 'order' => 'pontos DESC'));
    }

    function atualizar($dados) {
        if (!isset($dados[$this->name]['codigo']) || $dados[$this->name]['codigo'] == null){
            return false;
        }
        return $this->save($dados);
    }

    function formataResultadoPorTipoProfissional($codigo_profissional_tipo, $pontos, $nivel) {
        if (in_array($codigo_profissional_tipo, array(ProfissionalTipo::CARRETEIRO, ProfissionalTipo::AGREGADO, ProfissionalTipo::FUNCIONARIO_MOTORISTA)) ) {
            return $nivel;
        } else {
            if ($pontos > 0) {
                return 'Adequado ao Risco';
            } else {
                return $nivel;
            }
        }
    }

    function deparaStatusTeleconsult( $codigo_status_score ){
        if( !in_array($codigo_status_score, array(self::INSUFICIENTE, self::DIVERGENTE )))
            $codigo_status_score = self::OURO;
        $classificacao_tlc = array(
            self::OURO         => 'PERFIL ADEQUADO AO RISCO',
            self::INSUFICIENTE => 'PERFIL INSUFICIENTE',
            self::DIVERGENTE   => 'PERFIL DIVERGENTE'
        );
        return $classificacao_tlc[$codigo_status_score];
    }
    
}
