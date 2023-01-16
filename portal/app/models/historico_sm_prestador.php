<?php
class HistoricoSmPrestador extends AppModel {
    var $name          = 'HistoricoSmPrestador';
    var $primaryKey    = 'codigo';
    var $databaseTable = 'dbBuonny';
    var $tableSchema   = 'dbo';
    var $useTable      = 'historico_sm_prestador';    
    var $actsAs        = array('Secure');
    
    const CANCELADO  = 'C';
    const FINALIZADO = 'F';

    public function bind_historico_sm_atendimento_sm(){
        $this->bindModel(
            array('belongsTo' => array(                    
                    'HistoricoSm' => array(
                            'class'      => 'HistoricoSm',
                            'foreignKey' => FALSE,
                            'conditions' => 'HistoricoSm.codigo = HistoricoSmPrestador.codigo_historico_sm',
                            'type'       => 'INNER'
                        ),
                    'AtendimentoSm' => array(
                            'class'      => 'AtendimentoSm',
                            'foreignKey' => FALSE,
                            'type'       => 'INNER',
                            'conditions' => 'AtendimentoSm.codigo = HistoricoSm.codigo_atendimento_sm'
                        )

                ))
            );
    }

    public function prestadores_por_historico_sm($codigo_atendimento){
        $this->bind_historico_sm_atendimento_sm();
        $this->Prestador        = ClassRegistry::init('Prestador');
        $this->PrestadorContato = ClassRegistry::init('PrestadorContato');
        $this->TipoContato      = ClassRegistry::init('TipoContato');

        $query_contato = $this->PrestadorContato->find('sql', array(
            'conditions' => array( 'codigo_prestador=Prestador.codigo'),
            'fields'     => array('descricao'),
            'limit'      => 1,
        ));

        $this->bindModel(array(
            'belongsTo' => array(
                'Prestador' => array(
                    'className' => 'Prestador',
                    'foreignKey' => false,
                    'conditions' => array('Prestador.codigo = HistoricoSmPrestador.codigo_prestador')
                ),
                'PrestadorEndereco' => array(
                    'className' => 'PrestadorEndereco',
                    'foreignKey' => false,
                    'conditions' => array('PrestadorEndereco.codigo_prestador = Prestador.codigo')
                ),
                'Endereco' => array(
                    'className' => 'Endereco',
                    'foreignKey' => false,
                    'conditions' => array('Endereco.codigo = PrestadorEndereco.codigo_endereco')
                ),                
                'EnderecoCidade' => array(
                    'className' => 'EnderecoCidade',
                    'foreignKey' => false,
                    'conditions' => array('EnderecoCidade.codigo = Endereco.codigo_endereco_cidade')
                ),
                'EnderecoEstado' => array(
                    'className' => 'EnderecoEstado',
                    'foreignKey' => false,
                    'conditions' => array('EnderecoEstado.codigo = EnderecoCidade.codigo_endereco_estado')
                ),                
                'EnderecoBairro' => array(
                    'className' => 'EnderecoBairro',
                    'foreignKey' => false,
                    'conditions' => array('EnderecoBairro.codigo = Endereco.codigo_endereco_bairro_inicial')
                ),
                'EnderecoCep' => array(
                    'className' => 'EnderecoCep',
                    'foreignKey' => false,
                    'conditions' => array('EnderecoCep.codigo = Endereco.codigo_endereco_cep')
                ),
        )));

        
        $prestadores = $this->find('all', array(
            'fields' => array('Prestador.codigo','Prestador.nome','Prestador.codigo_documento', 'Endereco.descricao', 'PrestadorEndereco.numero', 'EnderecoBairro.descricao', 
            'EnderecoCidade.descricao', 'EnderecoEstado.descricao','EnderecoCep.cep', 'PrestadorEndereco.latitude','PrestadorEndereco.longitude', '('.$query_contato.') as contato', 'HistoricoSmPrestador.status'), 
            'conditions' => array(
                    '(HistoricoSmPrestador.status <> \''.HistoricoSMPrestador::CANCELADO.'\' OR HistoricoSmPrestador.status IS NULL)',
                    'AtendimentoSm.codigo' => $codigo_atendimento,
                    'PrestadorEndereco.codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL
                ),
            'group' => array('Prestador.codigo',
                'Prestador.nome',
                'Prestador.codigo_documento',
                'Endereco.descricao',
                'PrestadorEndereco.numero',
                'EnderecoBairro.descricao',
                'EnderecoCidade.descricao',
                'EnderecoEstado.descricao',
                'EnderecoCep.cep',
                'PrestadorEndereco.latitude',
                'PrestadorEndereco.longitude',
                'HistoricoSmPrestador.status'
            )
        ));   

        return $prestadores;
    }

    public function busca_por_prestador_atendimento($codigo_prestador, $codigo_atendimento){
        $this->bind_historico_sm_atendimento_sm();
        $retorno = $this->find('first', 
            array(
                'fields' => array('HistoricoSmPrestador.codigo','HistoricoSmPrestador.status','HistoricoSm.*'),
                'conditions' => array(
                        'HistoricoSmPrestador.codigo_prestador' => $codigo_prestador,
                        'AtendimentoSm.codigo' => $codigo_atendimento,
                        '(HistoricoSmPrestador.status <> \''.HistoricoSMPrestador::CANCELADO.'\' OR HistoricoSmPrestador.status IS NULL)'
                    )
            )
        );
        return $retorno;
    }
    
    public function alterar_pronta_resposta($codigo_prestador, $codigo_atendimento, $descricao, $status){
        $HistoricoSm = null;
        $HistoricoSm = ClassRegistry::init('HistoricoSm');

        $historico_sm = $this->busca_por_prestador_atendimento($codigo_prestador, $codigo_atendimento);        
        if(is_array($historico_sm)){
            $dados_historico_sm_prestador = array('HistoricoSmPrestador' => 
                    array(
                        'codigo' => $historico_sm['HistoricoSmPrestador']['codigo'],
                        'status' => $status
                    )
                );
            if($this->atualizar($dados_historico_sm_prestador)){
                $dados_historico = array(
                        'HistoricoSm' => 
                        array(
                            'codigo' => null,
                            'codigo_sm' => $historico_sm['HistoricoSm']['codigo_sm'],
                            'codigo_usuario' =>  $_SESSION['Auth']['Usuario']['codigo'],
                            'codigo_passo_atendimento' => $historico_sm['HistoricoSm']['codigo_passo_atendimento'],
                            'codigo_usuario_monitora' => $historico_sm['HistoricoSm']['codigo_usuario_monitora'],
                            'codigo_usuario_autorizacao' => $historico_sm['HistoricoSm']['codigo_usuario_autorizacao'],
                            'codigo_passo_atendimento_sm' => $historico_sm['HistoricoSm']['codigo_passo_atendimento_sm'],
                            'codigo_atendimento_sm' => $historico_sm['HistoricoSm']['codigo_atendimento_sm'],
                            'latitude' => $historico_sm['HistoricoSm']['latitude'],
                            'longitude' => $historico_sm['HistoricoSm']['longitude'],                            
                        )
                    );
                if(!empty($historico_sm['HistoricoSm']['local']))
                    $dados_historico['HistoricoSm']['local'] = $historico_sm['HistoricoSm']['local'];
                if(!empty($historico_sm['HistoricoSm']['codigo_tipo_evento']))
                    $dados_historico['HistoricoSm']['codigo_tipo_evento']  = $historico_sm['HistoricoSm']['codigo_tipo_evento'];

                if($status == self::CANCELADO)
                    $dados_historico['HistoricoSm']['texto'] = 'Pronta Resposta '.$historico_sm['HistoricoSm']['codigo'].' Cancelado - '.$descricao;
                else if($status == self::FINALIZADO)
                    $dados_historico['HistoricoSm']['texto'] = 'Pronta Resposta '.$historico_sm['HistoricoSm']['codigo'].' Finalizado - '.$descricao;

                if($HistoricoSm->save($dados_historico)){
                    return true;
                }
            }
        }
        return false;
    }

    public function atualizar($dados) {
        $this->HistoricoSmPrestadorLog = ClassRegistry::init('HistoricoSmPrestadorLog');
        $antigos = $this->carregar($dados['HistoricoSmPrestador']['codigo']);
        $this->Behaviors->attach('Loggable', array('foreign_key' => 'codigo_historico_sm_prestador'));
        return parent::atualizar($dados);
    }
}
?>