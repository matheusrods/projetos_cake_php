<?php
class Mes extends AppModel {
    var $name = 'Mes';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'meses';
    var $primaryKey = 'codigo';
    
    function comparativoFaturamentoAnual($filtros) {
        $Notaite =& ClassRegistry::init('Notaite');
        $ano2 = $Notaite->baseComparativoAnual($filtros);
        $filtros['ano']--;
        $ano1 = $Notaite->baseComparativoAnual($filtros);
        $fields = array(
            "{$this->name}.codigo",
            "{$this->name}.nome",
            "Ano1.valor",
            "Ano2.valor",
            "CASE WHEN Ano1.valor > 0 THEN (((Ano2.valor/Ano1.valor) -1) * 100) ELSE NULL END AS diferenca",
        );
        $joins = array(
            array(
                'table' => "({$ano1})",
                'alias' => 'Ano1',
                'type' => 'LEFT',
                'conditions' => array("Ano1.mes = {$this->name}.codigo")
            ),
            array(
                'table' => "({$ano2})",
                'alias' => 'Ano2',
                'type' => 'LEFT',
                'conditions' => array("Ano2.mes = {$this->name}.codigo")
            )
        );
        
        $order = array("{$this->name}.codigo");
        return $this->find('all', compact('joins', 'fields', 'order'));
    }
}
?>