<?php
class FichaScorecardArtCriminal extends AppModel {

    var $name = 'FichaScorecardArtCriminal';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'ficha_scorecard_artigos_criminais';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $validate = array(
        'codigo_ficha_scorecard' => array(
            'rule' => 'notEmpty',
            'message' => 'Código da ficha não informado',
        ),
        'codigo_artigo_criminal' => array(
            'rule' => 'notEmpty',
            'message' => 'Artigo criminal não informado',
        ),
        'codigo_endereco_cidade' => array(
            'rule' => 'notEmpty',
            'message' => 'Cidade não informada',
        ),
        'codigo_prestador' => array(
            'rule' => 'notEmpty',
            'message' => 'Prestador não informado',
        ),
    );
    var $belongsTo = array(
        'FichaScorecard' => array('foreignKey' => 'codigo_ficha_scorecard'),
        'ProfissionalLog' => array('foreignKey' => false, 'conditions' => array('ProfissionalLog.codigo = FichaScorecard.codigo_profissional_log')),
        'ArtigoCriminal' => array('foreignKey' => 'codigo_artigo_criminal'),
        'IPrestador' => array('foreignKey' => 'codigo_prestador'),
        'Instituicao' => array('foreignKey' => 'codigo_instituicao'),
        'SituacaoProcesso' => array('foreignKey' => 'codigo_situacao_processo'),
        'Usuario' => array('foreignKey' => 'codigo_usuario_inclusao'),
        'EnderecoCidade' => array('foreignKey' => 'codigo_endereco_cidade'),
        'EnderecoEstado' => array('foreignKey' => false, 'conditions' => array('EnderecoEstado.codigo = EnderecoCidade.codigo_endereco_estado')),
    );

    function listar($conditions) {
        $fields = array(
            'FichaScorecardArtCriminal.codigo',
            'FichaScorecardArtCriminal.observacao',
            'FichaScorecardArtCriminal.numero_dp',
            'FichaScorecardArtCriminal.codigo_situacao_processo',
            'FichaScorecardArtCriminal.codigo_instituicao',
            'FichaScorecardArtCriminal.codigo_prestador',
            'FichaScorecardArtCriminal.codigo_ficha_scorecard',
            'FichaScorecardArtCriminal.codigo_artigo_criminal',
            'FichaScorecardArtCriminal.codigo_endereco_cidade',
            'FichaScorecardArtCriminal.codigo_usuario_averiguacao',
            'FichaScorecardArtCriminal.codigo_usuario_inclusao',
            'FichaScorecardArtCriminal.data_ocorrencia',
            'FichaScorecardArtCriminal.data_inquerito',
            'FichaScorecardArtCriminal.data_processo',
            'FichaScorecardArtCriminal.data_averiguacao',
            'FichaScorecardArtCriminal.data_inclusao',
            'FichaScorecardArtCriminal.local_ocorrencia',
            'FichaScorecardArtCriminal.inquerito',
            'FichaScorecardArtCriminal.processo',
            'ArtigoCriminal.descricao',
            'ArtigoCriminal.nome',
            'IPrestador.nome',
            'Instituicao.descricao',
            'SituacaoProcesso.descricao',
            'Usuario.apelido',

        );
        return $this->find('all', compact('conditions', 'fields'));
    }
}