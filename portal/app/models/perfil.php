<?php
class Perfil extends AppModel {
    var $name = 'Perfil';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'perfil';
    var $primaryKey = 'codigo';

    const GESTOR_BUONNY_CREDIT = 'GESTOR BUONNY CREDIT';
    const SUPERVISOR_BUONNYSAT = 'SUPERVISOR BUONNYSAT';
    const OPERADOR_BUONNYSAT = 'OPERADOR BUONNYSAT';
    const PRONTA_RESPOSTA = 'PRONTA  RESPOSTA';
}