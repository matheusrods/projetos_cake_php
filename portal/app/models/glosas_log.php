<?php

	class GlosasLog extends AppModel {

		public $name = 'GlosasLog';
		public $databaseTable = 'RHHealth';
		public $tableSchema = 'dbo';
		public $useTable = 'glosas_log';
		public $primaryKey = 'codigo';
		public $foreignKeyLog = 'codigo_glosas';
		public $actsAs = array('Secure');
	}
	