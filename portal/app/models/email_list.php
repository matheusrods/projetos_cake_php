<?php
class EmailList extends AppModel {
    
    public $name          = 'EmailList';
    public $useDbConfig   = 'mailing';
    public $databaseTable = 'mailing';
    public $useTable      = 'email_lists';
    public $primaryKey    = 'listid';
    public $tableSchema   = 'dbo';
    
}
