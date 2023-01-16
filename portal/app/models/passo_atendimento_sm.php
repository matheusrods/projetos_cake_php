<?php
class PassoAtendimentoSm extends AppModel {
    var $name = 'PassoAtendimentoSm';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'passos_atendimentos_sms';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    function incluir($codigo_atendimento_sm, $dados) {
        $PassoAtendimento = ClassRegistry::init('PassoAtendimento');
        $passo_atendimento_sm = array(
            'PassoAtendimentoSm' => array(
                'codigo_atendimento_sm' => $codigo_atendimento_sm,
                'codigo_passo_atendimento' => $dados['AtendimentoSm']['codigo_passo_atendimento'],
                'data_inicio' => date('Y-m-d H:i:s'),
            )
        );
        if(isset($dados['PassoAtendimentoSm'])) {
            $passo_atendimento_sm['PassoAtendimentoSm']['data_analise'] = $dados['PassoAtendimentoSm']['data_analise'];
            $passo_atendimento_sm['PassoAtendimentoSm']['data_encaminhado'] = $dados['PassoAtendimentoSm']['data_encaminhado'];
            $passo_atendimento_sm['PassoAtendimentoSm']['data_fim'] = $dados['PassoAtendimentoSm']['data_fim'];
            $passo_atendimento_sm['PassoAtendimentoSm']['codigo_passo_atendimento_encaminhado'] = $dados['PassoAtendimentoSm']['codigo_passo_atendimento_encaminhado'];
        }
        
        if(isset($dados['AtendimentoSm']['codigo_usuario_inclusao']) && !empty($dados['AtendimentoSm']['codigo_usuario_inclusao'])) {
            $passo_atendimento_sm['PassoAtendimentoSm']['codigo_usuario_inclusao'] = $dados['AtendimentoSm']['codigo_usuario_inclusao'];
        } elseif(isset($dados['AtendimentoSm']['codigo_usuario_inclusao_guardian']) && !empty($dados['AtendimentoSm']['codigo_usuario_inclusao_guardian'])) {
            $passo_atendimento_sm['PassoAtendimentoSm']['codigo_usuario_inclusao_guardian'] = $dados['AtendimentoSm']['codigo_usuario_inclusao_guardian'];
        }
        
        if (parent::incluir($passo_atendimento_sm))
            return $this->read(null, $this->id);
        else
            return false;
    }

    function emAberto($codigo_atendimento_sm, $codigo_passo_atendimento) {
        return $this->find('first', array('conditions' => array('codigo_atendimento_sm' => $codigo_atendimento_sm, 'codigo_passo_atendimento' => $codigo_passo_atendimento, 'data_fim' => null)));
    }
	
	// function houveProntaResposta($codigo_atendimento_sm) {
	// 	$result = $this->find('first', array('conditions' => array('codigo_atendimento_sm' => $codigo_atendimento_sm, 'codigo_passo_atendimento' => 2)));
	// 	return $result?true:false;
	// }
}