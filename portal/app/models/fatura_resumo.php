<?php

class FaturaResumo extends AppModel {

    var $name = 'FaturaResumo';
    var $tableSchema = 'dbo';
    var $databaseTable = 'Monitora';
    var $useTable = 'fatura_resumo';
    var $primaryKey = null;

    const SOMATORIA_VALOR_TOTAL = 1;
    const SOMATORIA_VALOR_UNITARIO = 2;

    const SM = 1;
    const AVULSO = 2;
    const KM = 3;
    const DIA = 4;

    public function converteParametroEmCondition($link) {
        $parametros = explode('|' , $link);
        $codigo_cliente = $parametros[1];
        $ano_mes = $parametros[2];
        $periodo = Comum::periodo($ano_mes);
        $condition = array(
            'MES'   => substr($ano_mes, 4, 2),
            'ANO'     => substr($ano_mes, 0, 4),
            'CODIGO_CLIENTE' => intval($codigo_cliente)
        );
        return $condition;
    }
}

?>