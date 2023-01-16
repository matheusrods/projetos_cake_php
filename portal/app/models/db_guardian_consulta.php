<?php

class DbGuardianConsulta extends AppModel {
    var $name = 'DbGuardianConsulta';
    var $useDbConfig = 'dbGuardianConsulta';
    var $tableSchema = 'public';
    var $databaseTable = 'trafegus';
    var $useTable = false;

    public function status($tempo){        
        $result = $this->query("SELECT 
                    pg_last_xact_replay_timestamp() as data_ultima_atualizacao,
                    CASE WHEN COALESCE(date_part('seconds', NOW()-pg_last_xact_replay_timestamp()),0) > {$tempo} THEN 
                        'fora'
                    ELSE 
                        'dentro'
                    END AS status");        
        return $result[0][0];
    }
}
