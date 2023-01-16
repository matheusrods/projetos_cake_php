<?php
class CbqTurmaCurso extends AppModel {
	public $name = 'CbqTurmaCurso';
	public $tableSchema = 'dbo';
	public $databaseTable = 'Erp_buonny';
	public $useTable = 'CBQ_Turma_x_Curso';
	public $primaryKey = 'EMP_Codigo';
	public $actsAs = array('Secure');

}
