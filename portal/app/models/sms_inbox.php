<?php
class SmsInbox extends AppModel {
    var $name = 'SmsInbox';
	var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'sms_inbox';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    function latest($toNumber, $sinceId = 0) {
		return $this->find('all', array('conditions'=>array(
			'fone_para'=>$toNumber,
			'codigo >='=>$sinceId
		)));
	}
	
}
?>