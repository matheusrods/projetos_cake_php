<?php
class FichaClinicaFixture extends CakeTestFixture {
	var $name = 'FichaClinica';
    var $table = 'fichas_clinicas';

	public $fields = array(
    'codigo' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, 'key' => 'primary',),
    'data_inclusao' => array('type' => 'datetime', 'null' => false, 'default' => '(getdate())', 'length' => NULL, ),
    'incluido_por' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 255, ),
    'hora_inicio_atendimento' => array('type' => 'time', 'null' => false, 'default' => NULL, 'length' => NULL, ),
    'hora_fim_atendimento' => array('type' => 'time', 'null' => false, 'default' => NULL, 'length' => NULL, ),
    'ativo' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
    'codigo_pedido_exame' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
    'codigo_medico' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
    'pa_sistolica' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
    'pa_diastolica' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
    'pulso' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
    'circunferencia_abdominal' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => NULL, ),
    'peso_kg' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
    'peso_gr' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
    'altura_mt' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
    'altura_cm' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
    'circunferencia_quadril' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => NULL, ),
    'parecer' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
    'parecer_altura' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
    'parecer_espaco_confinado' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
  );

    public $records = array();
}
?> 