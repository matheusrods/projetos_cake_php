<?php
class ConfiguracaoFixture extends CakeTestFixture {
  var $name = 'Configuracao';
  var $table = 'configuracao';

  var $fields = array( 
  'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
  'codigo_empresa' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
  'chave' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
  'valor' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 500, ),
  'observacao' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 500, ),
  );

  var $records = array( 
    array(
      'codigo' => 1,
      'codigo_empresa' => 1,
      'chave' => 'FORNECEDOR_DOCUMENTACAO_VENCIDA',
      'valor' => 'credenciamento@rhhealth.com.br',
      'observacao' => 'Envio de e-mail notificando que existe(m) documento(s) em vencidos',
    ), 
    array(
      'codigo' => 2,
      'codigo_empresa' => 1,
      'chave' => 'DIAS_VENCIMENTO_EXEME_NOTIFICACAO',
      'valor' => '30',
      'observacao' => 'Parametro para ser utilizado na rotina para notificação de exames à vencer',
    ), 
    array(
      'codigo' => 3,
      'codigo_empresa' => 1,
      'chave' => 'AUSENCIA_DE_RISCO',
      'valor' => '64',
      'observacao' => 'Código do Risco caracterizado como Ausencia de Risco na tabela riscos',
    ), 
    array(
      'codigo' => 4,
      'codigo_empresa' => 1,
      'chave' => 'INSERE_EXAME_CLINICO',
      'valor' => '52',
      'observacao' => ' Exame Clinico',
    ), 
    array(
      'codigo' => 5,
      'codigo_empresa' => 1,
      'chave' => 'VALIDADE_ASO_GRAU_RISCO_1_e_2',
      'valor' => '135',
      'observacao' => 'Validade do Exame Clínico para Empresas com CNAE com grau de risco 1 e 2',
    ), 
    array(
      'codigo' => 6,
      'codigo_empresa' => 1,
      'chave' => 'VALIDADE_ASO_GRAU_RISCO_3_e_4',
      'valor' => '90',
      'observacao' => 'Validade do Exame Clínico para Empresas com CNAE com grau de risco 3 e 4',
    ), 
    array(
      'codigo' => 7,
      'codigo_empresa' => 1,
      'chave' => 'NOVO_EXAME_PERIODO_12-24',
      'valor' => '90',
      'observacao' => 'data de antecedência para permitir emissão de exames periódicos dentro da vigência.',
    ), 
    array(
      'codigo' => 8,
      'codigo_empresa' => 1,
      'chave' => 'NOVO_EXAME_PERIODO_6',
      'valor' => '60',
      'observacao' => 'data de antecedência para permitir emissão de exames periódicos dentro da vigência.',
    ), 
    array(
      'codigo' => 10,
      'codigo_empresa' => 1,
      'chave' => 'CODIGO_SERVICO_INTEGRACAO_FINANCEIRA',
      'valor' => '3190',
      'observacao' => 'CODIGO DO SERVICO PARA INTEGRACAO DO RHHEALTH COM O SISTEMA FINANCEIRO DA BUONNY',
    ), 
    array(
      'codigo' => 17,
      'codigo_empresa' => 1,
      'chave' => 'INSERE_EXAME_AUDIOMETRICO',
      'valor' => '130',
      'observacao' => 'EXAMES AUDIOMETRIA',
    ), 
  );

}
?>