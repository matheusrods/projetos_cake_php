<?php

class PosObsLocalLog extends AppModel
{
    var $name          = 'PosObsLocalLog';
    var $tableSchema   = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable      = 'pos_obs_local_log';
    var $primaryKey    = 'codigo';
    var $actsAs        = array('Secure');
}
