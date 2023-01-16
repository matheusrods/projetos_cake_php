<?php
class Igpm extends AppModel {
    var $name = 'Igpm';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'igpm';
    var $primaryKey = 'codigo';
   
	function ultimoIGPM() {
		return 7.2994;
	}
}
?>
