<?php

class PsicossocialValidadeAnexo extends AppModel {

    public $name = 'PsicossocialValidadeAnexo';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
   	public $useTable = 'ficha_psicossocial_validade_anexo';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure', 'Containable');

	public function getByCodigoFichaPsicossocial($codigo_ficha_psicossocial)
	{
		return $this->find('first', 
			array(
				'fields' => array('*'),
				'conditions' => array(
					'codigo_ficha_psicossocial' => $codigo_ficha_psicossocial
				),
				'order' => array('codigo DESC')
			)
		);
	}
}
