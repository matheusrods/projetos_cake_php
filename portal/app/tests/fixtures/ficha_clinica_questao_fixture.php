<?php

class FichaClinicaQuestaoFixture extends CakeTestFixture {

    var $name = 'FichaClinicaQuestao';
    var $table = 'fichas_clinicas_questoes';
    
    var $fields = array (
    'codigo' => 
      array (
        'type' => 'integer',
        'null' => false,
        'default' => NULL,
        'length' => NULL,
        'key' => 'primary',
      ),
      'codigo_ficha_clinica_grupo_questao' => 
      array (
        'type' => 'integer',
        'null' => false,
        'default' => NULL,
        'length' => NULL,
      ),
      'codigo_ficha_clinica_questao' => 
      array (
        'type' => 'integer',
        'null' => true,
        'default' => NULL,
        'length' => NULL,
      ),
      'tipo' => 
      array (
        'type' => 'string',
        'null' => false,
        'default' => NULL,
        'length' => 255,
      ),
      'campo_livre_descricao' => 
      array (
        'type' => 'string',
        'null' => true,
        'default' => NULL,
        'length' => 255,
      ),
      'campo_livre_label' => 
      array (
        'type' => 'string',
        'null' => true,
        'default' => NULL,
        'length' => 255,
      ),
      'observacao' => 
      array (
        'type' => 'string',
        'null' => true,
        'default' => NULL,
        'length' => 255,
      ),
      'obrigatorio' => 
      array (
        'type' => 'integer',
        'null' => false,
        'default' => NULL,
        'length' => NULL,
      ),
      'ajuda' => 
      array (
        'type' => 'string',
        'null' => true,
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
      'ativo' => 
      array (
        'type' => 'integer',
        'null' => false,
        'default' => NULL,
        'length' => NULL,
      ),
      'span' => 
      array (
        'type' => 'string',
        'null' => true,
        'default' => NULL,
        'length' => 255,
      ),
      'label' => 
      array (
        'type' => 'string',
        'null' => true,
        'default' => NULL,
        'length' => 255,
      ),
      'conteudo' => 
      array (
        'type' => 'text',
        'null' => true,
        'default' => NULL,
        'length' => NULL,
      ),
      'parentesco_ativo' => 
      array (
        'type' => 'integer',
        'null' => true,
        'default' => NULL,
        'length' => NULL,
      ),
      'quebra_linha' => 
      array (
        'type' => 'integer',
        'null' => true,
        'default' => NULL,
        'length' => NULL,
      ),
      'ordenacao' => 
      array (
        'type' => 'integer',
        'null' => true,
        'default' => NULL,
        'length' => NULL,
      ),
      'opcao_selecionada' => 
      array (
        'type' => 'string',
        'null' => true,
        'default' => NULL,
        'length' => 255,
      ),
      'opcao_abre_menu_escondido' => 
      array (
        'type' => 'string',
        'null' => true,
        'default' => NULL,
        'length' => 255,
      ),
      'farmaco_ativo' => 
      array (
        'type' => 'integer',
        'null' => true,
        'default' => NULL,
        'length' => NULL,
      ),
      'opcao_exibe_label' => 
      array (
        'type' => 'string',
        'null' => true,
        'default' => NULL,
        'length' => 255,
      ),
      'multiplas_cids_ativo' => 
      array (
        'type' => 'integer',
        'null' => true,
        'default' => NULL,
        'length' => NULL,
      ),
      'exibir_se_sexo' => 
      array (
        'type' => 'string',
        'null' => true,
        'default' => NULL,
        'length' => 255,
      ),
      'exibir_se_idade_maior_que' => 
      array (
        'type' => 'integer',
        'null' => true,
        'default' => NULL,
        'length' => NULL,
      ),
      'exibir_se_idade_menor_que' => 
      array (
        'type' => 'integer',
        'null' => true,
        'default' => NULL,
        'length' => NULL,
      ),
    );
    
    var $records = array (
  0 => 
  array (
    'codigo' => 299,
    'codigo_ficha_clinica_grupo_questao' => 8,
    'codigo_ficha_clinica_questao' => 195,
    'obrigatorio' => 0,
    'ativo' => 1,
    'parentesco_ativo' => NULL,
    'quebra_linha' => NULL,
    'ordenacao' => NULL,
    'farmaco_ativo' => NULL,
    'multiplas_cids_ativo' => NULL,
    'exibir_se_idade_maior_que' => NULL,
    'exibir_se_idade_menor_que' => NULL,
    'data_inclusao' => '2017-01-29 12:00:00',
    'tipo' => 'VARCHAR',
    'campo_livre_descricao' => NULL,
    'campo_livre_label' => NULL,
    'observacao' => NULL,
    'ajuda' => NULL,
    'span' => '12',
    'label' => 'Outras defici??ncias f??sicas:',
    'conteudo' => ' ',
    'opcao_selecionada' => NULL,
    'opcao_abre_menu_escondido' => NULL,
    'opcao_exibe_label' => NULL,
    'exibir_se_sexo' => NULL,
  ),
  1 => 
  array (
    'codigo' => 298,
    'codigo_ficha_clinica_grupo_questao' => 8,
    'codigo_ficha_clinica_questao' => 195,
    'obrigatorio' => 0,
    'ativo' => 1,
    'parentesco_ativo' => NULL,
    'quebra_linha' => NULL,
    'ordenacao' => NULL,
    'farmaco_ativo' => NULL,
    'multiplas_cids_ativo' => NULL,
    'exibir_se_idade_maior_que' => NULL,
    'exibir_se_idade_menor_que' => NULL,
    'data_inclusao' => '2017-01-29 12:00:00',
    'tipo' => 'CHECKBOX',
    'campo_livre_descricao' => NULL,
    'campo_livre_label' => NULL,
    'observacao' => NULL,
    'ajuda' => NULL,
    'span' => '12',
    'label' => 'IV b- Defici??ncia Mental',
    'conteudo' => '{"Lei 12764/2012 Espectro Autista. (Obs: laudo do especialista)": "Lei 12764/2012 Espectro Autista. (Obs: laudo do especialista)"}',
    'opcao_selecionada' => NULL,
    'opcao_abre_menu_escondido' => NULL,
    'opcao_exibe_label' => NULL,
    'exibir_se_sexo' => NULL,
  ),
  2 => 
  array (
    'codigo' => 297,
    'codigo_ficha_clinica_grupo_questao' => 8,
    'codigo_ficha_clinica_questao' => 195,
    'obrigatorio' => 0,
    'ativo' => 1,
    'parentesco_ativo' => NULL,
    'quebra_linha' => NULL,
    'ordenacao' => NULL,
    'farmaco_ativo' => NULL,
    'multiplas_cids_ativo' => NULL,
    'exibir_se_idade_maior_que' => NULL,
    'exibir_se_idade_menor_que' => NULL,
    'data_inclusao' => '2017-01-29 12:00:00',
    'tipo' => 'CHECKBOX',
    'campo_livre_descricao' => NULL,
    'campo_livre_label' => NULL,
    'observacao' => NULL,
    'ajuda' => NULL,
    'span' => '12',
    'label' => 'IV a- Defici??ncia Mental',
    'conteudo' => '{"Psicossocial ?? conforme Conven????o ONU ?? Esquizofrenia, outros transtornos psic??ticos, outras limita????es psicossociais. Informar se h?? outras doen??as associadas e data de inicio de manifesta????o da doen??a. (Obs: Anexar laudo do especialista)": "Psicossocial ?? conforme Conven????o ONU ?? Esquizofrenia, outros transtornos psic??ticos, outras limita????es psicossociais. Informar se h?? outras doen??as associadas e data de inicio de manifesta????o da doen??a. (Obs: Anexar laudo do especialista)"}',
    'opcao_selecionada' => NULL,
    'opcao_abre_menu_escondido' => NULL,
    'opcao_exibe_label' => NULL,
    'exibir_se_sexo' => NULL,
  ),
  3 => 
  array (
    'codigo' => 296,
    'codigo_ficha_clinica_grupo_questao' => 8,
    'codigo_ficha_clinica_questao' => 195,
    'obrigatorio' => 0,
    'ativo' => 1,
    'parentesco_ativo' => NULL,
    'quebra_linha' => NULL,
    'ordenacao' => NULL,
    'farmaco_ativo' => NULL,
    'multiplas_cids_ativo' => NULL,
    'exibir_se_idade_maior_que' => NULL,
    'exibir_se_idade_menor_que' => NULL,
    'data_inclusao' => '2017-01-29 12:00:00',
    'tipo' => 'CHECKBOX',
    'campo_livre_descricao' => NULL,
    'campo_livre_label' => 'Idade de in??cio',
    'observacao' => NULL,
    'ajuda' => NULL,
    'span' => '12',
    'label' => 'IV- Defici??ncia Intelectual- funcionamento intelectual significativamente inferior ?? m??dia, com manifesta????o antes dos 18 anos e limita????es associadas a duas ou mais habilidades adaptativas, tais como:',
    'conteudo' => '{"Comunica????o":"Comunica????o","Cuidado pessoa": "Cuidado pessoa","Habilidades sociais": " Habilidades sociais","Utiliza????o de recursos da comunidade": "Utiliza????o de recursos da comunidade","Sa??de e seguran??a": "Sa??de e seguran??a","Habilidades acad??micas": "Habilidades acad??micas","Lazer": "Lazer","Trabalho": "Trabalho (especificar idade de in??cio abaixo)"}',
    'opcao_selecionada' => NULL,
    'opcao_abre_menu_escondido' => NULL,
    'opcao_exibe_label' => NULL,
    'exibir_se_sexo' => NULL,
  ),
  4 => 
  array (
    'codigo' => 295,
    'codigo_ficha_clinica_grupo_questao' => 8,
    'codigo_ficha_clinica_questao' => 195,
    'obrigatorio' => 0,
    'ativo' => 1,
    'parentesco_ativo' => NULL,
    'quebra_linha' => NULL,
    'ordenacao' => NULL,
    'farmaco_ativo' => NULL,
    'multiplas_cids_ativo' => NULL,
    'exibir_se_idade_maior_que' => NULL,
    'exibir_se_idade_menor_que' => NULL,
    'data_inclusao' => '2017-01-29 12:00:00',
    'tipo' => 'CHECKBOX',
    'campo_livre_descricao' => NULL,
    'campo_livre_label' => NULL,
    'observacao' => NULL,
    'ajuda' => NULL,
    'span' => '12',
    'label' => 'III- Defici??ncia Visual',
    'conteudo' => '{"Cegueira - acuidade visual = 0,05 (20/400) no melhor olho, com a melhor corre????o ??ptica.":"Cegueira - acuidade visual = 0,05 (20/400) no melhor olho, com a melhor corre????o ??ptica.","Baixa vis??o - acuidade visual entre 0,3 (20/60) e 0,05 (20/400) no melhor olho, com a melhor corre????o ??ptica.":"Baixa vis??o - acuidade visual entre 0,3 (20/60) e 0,05 (20/400) no melhor olho, com a melhor corre????o ??ptica.","Defici??ncia Visual - somat??ria da medida do campo visual em ambos os olhos for igual ou menor que 60??.":"Defici??ncia Visual - somat??ria da medida do campo visual em ambos os olhos for igual ou menor que 60??.","Vis??o Monocular - conforme parecer CONJUR/MTE 444/11: cegueira, na qual a acuidade visual com a melhor corre????o ??ptica ?? igual ou menor que 0,05 (20/400) em um olho (ou cegueira declarada por oftalmologista).":"Vis??o Monocular - conforme parecer CONJUR/MTE 444/11: cegueira, na qual a acuidade visual com a melhor corre????o ??ptica ?? igual ou menor que 0,05 (20/400) em um olho (ou cegueira declarada por oftalmologista)."}',
    'opcao_selecionada' => NULL,
    'opcao_abre_menu_escondido' => NULL,
    'opcao_exibe_label' => NULL,
    'exibir_se_sexo' => NULL,
  ),
  5 => 
  array (
    'codigo' => 294,
    'codigo_ficha_clinica_grupo_questao' => 8,
    'codigo_ficha_clinica_questao' => 195,
    'obrigatorio' => 0,
    'ativo' => 1,
    'parentesco_ativo' => NULL,
    'quebra_linha' => NULL,
    'ordenacao' => NULL,
    'farmaco_ativo' => NULL,
    'multiplas_cids_ativo' => NULL,
    'exibir_se_idade_maior_que' => NULL,
    'exibir_se_idade_menor_que' => NULL,
    'data_inclusao' => '2017-01-29 12:00:00',
    'tipo' => 'CHECKBOX',
    'campo_livre_descricao' => NULL,
    'campo_livre_label' => NULL,
    'observacao' => NULL,
    'ajuda' => NULL,
    'span' => '12',
    'label' => 'II- Defici??ncia Auditiva',
    'conteudo' => '{"II- Defici??ncia Auditiva - perda bilateral, parcial ou total, de 41 decib??is (dB) ou mais, aferida por audiograma nas frequ??ncias de 500HZ, 1.000HZ,2.000Hz e 3.000Hz  OBS: Anexar audiograma.":"Perda bilateral, parcial ou total, de 41 decib??is (dB) ou mais, aferida por audiograma nas frequ??ncias de 500HZ, 1.000HZ,2.000Hz e 3.000Hz  OBS: Anexar audiograma."}',
    'opcao_selecionada' => NULL,
    'opcao_abre_menu_escondido' => NULL,
    'opcao_exibe_label' => NULL,
    'exibir_se_sexo' => NULL,
  ),
  6 => 
  array (
    'codigo' => 293,
    'codigo_ficha_clinica_grupo_questao' => 8,
    'codigo_ficha_clinica_questao' => 195,
    'obrigatorio' => 0,
    'ativo' => 1,
    'parentesco_ativo' => NULL,
    'quebra_linha' => NULL,
    'ordenacao' => NULL,
    'farmaco_ativo' => NULL,
    'multiplas_cids_ativo' => NULL,
    'exibir_se_idade_maior_que' => NULL,
    'exibir_se_idade_menor_que' => NULL,
    'data_inclusao' => '2017-01-29 12:00:00',
    'tipo' => 'CHECKBOX',
    'campo_livre_descricao' => NULL,
    'campo_livre_label' => 'Altura',
    'observacao' => NULL,
    'ajuda' => NULL,
    'span' => '12',
    'label' => 'I- Defici??ncia F??sica',
    'conteudo' => '{"Altera????o completa ou parcial de um ou mais segmentos do corpo humano, acarretando o comprometimento da fun????o f??sica, apresentando-se sob a forma de paraplegia, paraparesia, monoplegia, monoparesia, tetraplegia, tetraparesia, triplegia, triparesia, hemiplegia, hemiparesia, ostomia, amputa????o ou aus??ncia de membro, paralisia cerebral, membros com deformidade cong??nita ou adquirida, nanismo (especificar altura abaixo).":"Altera????o completa ou parcial de um ou mais segmentos do corpo humano, acarretando o comprometimento da fun????o f??sica, apresentando-se sob a forma de paraplegia, paraparesia, monoplegia, monoparesia, tetraplegia, tetraparesia, triplegia, triparesia, hemiplegia, hemiparesia, ostomia, amputa????o ou aus??ncia de membro, paralisia cerebral, membros com deformidade cong??nita ou adquirida, nanismo (especificar altura abaixo)."}',
    'opcao_selecionada' => NULL,
    'opcao_abre_menu_escondido' => NULL,
    'opcao_exibe_label' => NULL,
    'exibir_se_sexo' => NULL,
  ),
  7 => 
  array (
    'codigo' => 291,
    'codigo_ficha_clinica_grupo_questao' => 8,
    'codigo_ficha_clinica_questao' => 195,
    'obrigatorio' => 0,
    'ativo' => 1,
    'parentesco_ativo' => NULL,
    'quebra_linha' => NULL,
    'ordenacao' => NULL,
    'farmaco_ativo' => NULL,
    'multiplas_cids_ativo' => NULL,
    'exibir_se_idade_maior_que' => NULL,
    'exibir_se_idade_menor_que' => NULL,
    'data_inclusao' => '2017-01-29 12:00:00',
    'tipo' => 'VARCHAR',
    'campo_livre_descricao' => NULL,
    'campo_livre_label' => NULL,
    'observacao' => NULL,
    'ajuda' => NULL,
    'span' => '12',
    'label' => 'Descri????o das limita????es funcionais para atividades da vida di??ria e social e dos apoios necess??rios:',
    'conteudo' => NULL,
    'opcao_selecionada' => NULL,
    'opcao_abre_menu_escondido' => NULL,
    'opcao_exibe_label' => NULL,
    'exibir_se_sexo' => NULL,
  ),
  8 => 
  array (
    'codigo' => 290,
    'codigo_ficha_clinica_grupo_questao' => 8,
    'codigo_ficha_clinica_questao' => 195,
    'obrigatorio' => 0,
    'ativo' => 1,
    'parentesco_ativo' => NULL,
    'quebra_linha' => NULL,
    'ordenacao' => NULL,
    'farmaco_ativo' => NULL,
    'multiplas_cids_ativo' => NULL,
    'exibir_se_idade_maior_que' => NULL,
    'exibir_se_idade_menor_que' => NULL,
    'data_inclusao' => '2017-01-29 12:00:00',
    'tipo' => 'VARCHAR',
    'campo_livre_descricao' => NULL,
    'campo_livre_label' => NULL,
    'observacao' => NULL,
    'ajuda' => NULL,
    'span' => '12',
    'label' => 'Descri????o detalhada das altera????es f??sicas (anat??micas e funcionais), sensoriais, intelectuais e mentais:',
    'conteudo' => NULL,
    'opcao_selecionada' => NULL,
    'opcao_abre_menu_escondido' => NULL,
    'opcao_exibe_label' => NULL,
    'exibir_se_sexo' => NULL,
  ),
  9 => 
  array (
    'codigo' => 288,
    'codigo_ficha_clinica_grupo_questao' => 8,
    'codigo_ficha_clinica_questao' => 195,
    'obrigatorio' => 0,
    'ativo' => 1,
    'parentesco_ativo' => NULL,
    'quebra_linha' => NULL,
    'ordenacao' => NULL,
    'farmaco_ativo' => NULL,
    'multiplas_cids_ativo' => NULL,
    'exibir_se_idade_maior_que' => NULL,
    'exibir_se_idade_menor_que' => NULL,
    'data_inclusao' => '2017-01-29 12:00:00',
    'tipo' => 'RADIO',
    'campo_livre_descricao' => NULL,
    'campo_livre_label' => NULL,
    'observacao' => NULL,
    'ajuda' => NULL,
    'span' => '12',
    'label' => 'ORIGEM DA DEFICI??NCIA:',
    'conteudo' => '{"Acidente de trabalho":"Acidente de trabalho", "Cong??nita": "Cong??nita", "Adquirida em p??s operat??rio": "Adquirida em p??s operat??rio", "Acidente comum": "Acidente comum", "Doen??a": "Doen??a"}',
    'opcao_selecionada' => NULL,
    'opcao_abre_menu_escondido' => NULL,
    'opcao_exibe_label' => NULL,
    'exibir_se_sexo' => NULL,
  ),
);
}