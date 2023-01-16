<?php

class FichaClinicaGrupoQuestaoFixture extends CakeTestFixture {

    var $name = 'FichaClinicaGrupoQuestao';
    var $table = 'fichas_clinicas_grupo_questoes';
    
    var $fields = array (
      'codigo' => 
      array (
        'type' => 'integer',
        'null' => false,
        'default' => NULL,
        'length' => NULL,
        'key' => 'primary',
      ),
      'descricao' => 
      array (
        'type' => 'string',
        'null' => false,
        'default' => NULL,
        'length' => 255,
      ),
      'data_inclusao' => 
      array (
        'type' => 'datetime',
        'null' => false,
        'default' => '(getdate())',
        'length' => NULL,
      ),
      'codigo_usuario_inclusao' => 
      array (
        'type' => 'integer',
        'null' => false,
        'default' => NULL,
        'length' => NULL,
      ),
      'ativo' => 
      array (
        'type' => 'integer',
        'null' => false,
        'default' => NULL,
        'length' => NULL,
      ),
    );
    
    var $records = array (
      0 => 
      array (
        'codigo' => 10,
        'codigo_usuario_inclusao' => 1,
        'ativo' => 1,
        'data_inclusao' => '2016-09-20 12:00:00',
        'descricao' => 'RESULTADO DO EXAME',
      ),
      1 => 
      array (
        'codigo' => 9,
        'codigo_usuario_inclusao' => 1,
        'ativo' => 1,
        'data_inclusao' => '2016-09-20 12:00:00',
        'descricao' => 'EXAME FÍSICO',
      ),
      2 => 
      array (
        'codigo' => 8,
        'codigo_usuario_inclusao' => 1,
        'ativo' => 1,
        'data_inclusao' => '2016-09-20 12:00:00',
        'descricao' => 'AVALIAÇÃO PCD',
      ),
      3 => 
      array (
        'codigo' => 7,
        'codigo_usuario_inclusao' => 1,
        'ativo' => 1,
        'data_inclusao' => '2016-09-20 12:00:00',
        'descricao' => 'HISTÓRICO OCUPACIONAL ',
      ),
      4 => 
      array (
        'codigo' => 6,
        'codigo_usuario_inclusao' => 1,
        'ativo' => 1,
        'data_inclusao' => '2016-09-20 12:00:00',
        'descricao' => 'HÁBITOS DE VIDA',
      ),
      5 => 
      array (
        'codigo' => 5,
        'codigo_usuario_inclusao' => 1,
        'ativo' => 1,
        'data_inclusao' => '2016-09-20 12:00:00',
        'descricao' => 'EXAMES PREVENTIVOS',
      ),
      6 => 
      array (
        'codigo' => 4,
        'codigo_usuario_inclusao' => 1,
        'ativo' => 1,
        'data_inclusao' => '2016-09-20 12:00:00',
        'descricao' => 'HISTÓRICO GESTACIONAL',
      ),
      7 => 
      array (
        'codigo' => 3,
        'codigo_usuario_inclusao' => 1,
        'ativo' => 1,
        'data_inclusao' => '2016-09-20 12:00:00',
        'descricao' => 'HISTÓRICO PESSOAL (HPP)',
      ),
      8 => 
      array (
        'codigo' => 2,
        'codigo_usuario_inclusao' => 1,
        'ativo' => 1,
        'data_inclusao' => '2016-09-20 12:00:00',
        'descricao' => 'HISTÓRICO FAMILIAR',
      ),
    );
}