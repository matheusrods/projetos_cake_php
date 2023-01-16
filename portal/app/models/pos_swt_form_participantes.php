<?php

class PosSwtFormParticipantes extends AppModel
{
  var $name = 'PosSwtFormParticipantes';
  var $tableSchema = 'dbo';
  var $databaseTable = 'RHHealth';
  var $useTable = 'pos_swt_form_participantes';
  var $primaryKey = 'codigo';

  public function getByCodigoFormRespondido($codigo_form_respondido)
  {

    return $this->find('all', array(
      'fields' => array(
        'Usuario.codigo',
        'Usuario.nome'
      ),
      'conditions' => array(
        'codigo_form_respondido' => $codigo_form_respondido,
        'PosSwtFormParticipantes.ativo' => 1
      ),
      //'limit' => 5,
      'joins' => array(
        array(
          'table' => 'usuario',
          'alias' => 'Usuario',
          'type' => 'INNER',
          'conditions' => 'Usuario.codigo = PosSwtFormParticipantes.codigo_usuario'
        ),
      )
    ));
  }
}
