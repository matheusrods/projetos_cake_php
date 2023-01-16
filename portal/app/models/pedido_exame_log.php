<?php
class PedidoExameLog extends AppModel
{

    public $name = 'PedidoExameLog';
    public $databaseTable = 'RHHealth';
    public $tableSchema = 'dbo';
    public $useTable = 'pedidos_exames_log';
    public $primaryKey = 'codigo';
    public $foreignKeyLog = 'codigo_pedidos_exames';
    public $actsAs = array('Secure');

    public function log_pedidos_exames($codigo_pedido_exame)
    {
        $PedidoExame = &ClassRegistry::init('PedidoExame');
        //fields do log
        $options['fields'] = array(
            'PedidoExameLog.codigo',
            'PedidoExameLog.codigo_pedidos_exames',
            'PedidoExameLog.data_alteracao',
            'PedidoExameLog.acao_sistema',
            'PedidoExameLog.data_notificacao',
            'PedidoExame.data_inclusao',
            'PedidoExame.exame_admissional',
            'PedidoExame.exame_periodico',
            'PedidoExame.exame_demissional',
            'PedidoExame.exame_retorno',
            'PedidoExame.exame_mudanca',
            'PedidoExame.exame_monitoracao',
            'PedidoExame.pontual',
            'UsuarioEmissao.apelido',
            'UsuarioAlteracao.apelido',
            'StatusPedidoExame.descricao',
            'Funcionario.nome',
            'Uperfil.descricao',
            '(SELECT 
				top 1
				CONVERT(VARCHAR(24),IPEB.data_realizacao_exame,103)
			FROM itens_pedidos_exames_baixa IPEB
				INNER JOIN itens_pedidos_exames IPE ON (IPEB.codigo_itens_pedidos_exames = IPE.codigo)
			WHERE IPE.codigo_pedidos_exames = PedidoExameLog.codigo_pedidos_exames
			) as baixa_ultimo_exame',
            '(SELECT 
				top 1
				u.apelido
			FROM itens_pedidos_exames_baixa IPEB
				INNER JOIN itens_pedidos_exames IPE ON (IPEB.codigo_itens_pedidos_exames = IPE.codigo)
				INNER JOIN usuario u ON (IPEB.codigo_usuario_inclusao = u.codigo)
			WHERE IPE.codigo_pedidos_exames = PedidoExameLog.codigo_pedidos_exames
			) as usuario_baixa',
            '(CASE
			WHEN PedidoExame.exame_admissional > 0 THEN \'Exame admissional\'
			WHEN PedidoExame.exame_periodico > 0 THEN \'Exame períodico\'
			WHEN PedidoExame.exame_demissional > 0 THEN \'Exame demissional\'
			WHEN PedidoExame.exame_retorno > 0 THEN \'Retorno ao trabalho\'
			WHEN PedidoExame.exame_mudanca > 0 THEN \'Mudança de riscos ocupacionais\'
			WHEN PedidoExame.exame_monitoracao > 0 THEN \'Monitoração Pontual\'
			WHEN PedidoExame.pontual > 0 THEN \'Pontual\'
			ELSE \'\'
			END) AS tipo_pedido',
            'case when [PedidoExameLog].[data_alteracao] is null then \'\'
            else PedidoExameNotificacao.cliente_email end as cliente_email',
            'case when [PedidoExameLog].[data_alteracao] is null then \'\'
            else PedidoExameNotificacao.clinica_email end as clinica_email',
            'case when [PedidoExameLog].[data_alteracao] is null then \'\'
            else PedidoExameNotificacao.funcionario_email end as funcionario_email'
            // 'PedidoExameNotificacao.cliente_email',
            // 'PedidoExameNotificacao.clinica_email',
            // 'PedidoExameNotificacao.funcionario_email'
        );
        //where
        $options['conditions'] = array('PedidoExameLog.codigo_pedidos_exames' => $codigo_pedido_exame);
        //relacionamentos
        $options['joins'] = array(
            array(
                'table' => 'Rhhealth.dbo.pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'INNER',
                'conditions' => 'PedidoExame.codigo = PedidoExameLog.codigo_pedidos_exames',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente_funcionario',
                'alias' => 'ClienteFuncionario',
                'type' => 'INNER',
                'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente_matricula',
            ),
            array(
                'table' => 'Rhhealth.dbo.funcionarios',
                'alias' => 'Funcionario',
                'type' => 'INNER',
                'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
            ),
            array(
                'table' => 'Rhhealth.dbo.status_pedidos_exames',
                'alias' => 'StatusPedidoExame',
                'type' => 'INNER',
                'conditions' => 'StatusPedidoExame.codigo = PedidoExameLog.codigo_status_pedidos_exames',
            ),
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'UsuarioEmissao',
                'type' => 'LEFT',
                'conditions' => 'UsuarioEmissao.codigo = PedidoExameLog.codigo_usuario_inclusao',
            ),
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'UsuarioAlteracao',
                'type' => 'LEFT',
                'conditions' => 'UsuarioAlteracao.codigo = PedidoExameLog.codigo_usuario_alteracao',
            ),
            array(
                'table' => 'Rhhealth.dbo.uperfis',
                'alias' => 'Uperfil',
                'type' => 'LEFT',
                'conditions' => 'Uperfil.codigo = UsuarioAlteracao.codigo_uperfil',
            ),
            array(
                'table' => 'Rhhealth.dbo.pedidos_exames_notificacao',
                'alias' => 'PedidoExameNotificacao',
                'type' => 'LEFT',
                'conditions' => 'PedidoExameLog.codigo_pedidos_exames = PedidoExameNotificacao.codigo_pedido_exame AND PedidoExameNotificacao.codigo_pedido_exame_log = PedidoExameLog.codigo AND substring(CONVERT(varchar(20), PedidoExameLog.data_notificacao, 20),0,17) = substring(CONVERT(varchar(20), PedidoExameNotificacao.data_inclusao, 20),0,17)',
            ),
        );
        //ordenacao
        $options['order'] = array('PedidoExameLog.data_alteracao DESC');
        // debug(pr($this->find('sql', $options)));
        return $this->find('all', $options);
    }

    public function trataDados($dados)
    {

        // debug($dados);exit;

        foreach ($dados as $key => $dado) {
            $dados[$key]['PedidoExameLog']['codigo_pedidos_exame'] = $dado['PedidoExameLog']['codigo_pedidos_exames'];
            $dados[$key]['PedidoExameLog']['data_alteracao'] = $dado['PedidoExameLog']['data_alteracao'];
            $dados[$key]['PedidoExameLog']['data_inclusao'] = $dado['PedidoExame']['data_inclusao'];

            $dados[$key]['PedidoExameLog']['usuario_emissao'] = $dado['UsuarioEmissao']['apelido'];
            $dados[$key]['PedidoExameLog']['tipo_perfil'] = $dado['Uperfil']['descricao'];
            $dados[$key]['PedidoExameLog']['usuario_alteracao'] = $dado['UsuarioAlteracao']['apelido'];
            $dados[$key]['PedidoExameLog']['status'] = $dado['StatusPedidoExame']['descricao'];
            $dados[$key]['PedidoExameLog']['nome_funcionario'] = $dado['Funcionario']['nome'];
            $dados[$key]['PedidoExameLog']['data_baixa'] = $dado[0]['baixa_ultimo_exame'];
            $dados[$key]['PedidoExameLog']['tipo_pedido'] = $dado[0]['tipo_pedido'];
            $dados[$key]['PedidoExameLog']['usuario_baixa'] = $dado[0]['usuario_baixa'];

            $dados[$key]['PedidoExameLog']['cliente_email'] = $dado[0]['cliente_email'];
            $dados[$key]['PedidoExameLog']['clinica_email'] = $dado[0]['clinica_email'];
            $dados[$key]['PedidoExameLog']['funcionario_email'] = $dado[0]['funcionario_email'];

            unset($dados[$key]['UsuarioEmissao']);
            unset($dados[$key]['UsuarioAlteracao']);
            unset($dados[$key]['StatusPedidoExame']);
            unset($dados[$key]['Funcionario']);
            unset($dados[$key]['PedidoExame']);
            unset($dados[$key]['Uperfil']);
            unset($dados[$key]['PedidoExameNotificacao']);
            unset($dados[$key][0]);

            switch ($dado['PedidoExameLog']['acao_sistema']) {
                case 0:
                    $dados[$key]['PedidoExameLog']['acao_sistema'] = 'Inclusão';
                    break;
                case 1:
                    $dados[$key]['PedidoExameLog']['acao_sistema'] = 'Atualização';
                    break;
                case 2:
                    $dados[$key]['PedidoExameLog']['acao_sistema'] = 'Exclusão';
                    break;
            }
        } //fim foreach

        // debug($dados);
        // exit;

        foreach ($dados as $key1 => $dadoLog) {
            foreach ($dadoLog['PedidoExameLog'] as $key2 => $value) {
                if (empty($value))
                    $dados[$key1]['PedidoExameLog'][$key2] = '';
            }
        }

        return $dados;
    }
}
