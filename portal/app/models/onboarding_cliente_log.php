<?php

class OnboardingClienteLog extends AppModel {

	public $name = 'OnboardingClienteLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'onboarding_cliente_log';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');
	public $foreignKeyLog = 'codigo';

}