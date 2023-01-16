<?php
class NotaFiscalServicoLog extends AppModel {
	var $name = 'NotaFiscalServicoLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'nota_fiscal_servico_log';
	var $primaryKey = 'codigo';
	var $displayField = 'nome';
	var $actsAs = array('Secure');


	public function log_nfs($codigo_nfs)
	{

		//fields do log
        $options['fields'] = array(
        	'NotaFiscalServicoLog.codigo_nota_fiscal_servico',
			'Fornecedor.codigo_documento',
			'Fornecedor.nome',
			'Fornecedor.razao_social',
			'NotaFiscalStatus.descricao',
			'NotaFiscalServicoLog.numero_nota_fiscal',
			'NotaFiscalServicoLog.data_emissao',
			'NotaFiscalServicoLog.data_vencimento',
			'NotaFiscalServicoLog.data_pagamento',
			'NotaFiscalServicoLog.valor',
			'NotaFiscalServicoLog.ativo',
			'UsuarioInc.nome',
			'NotaFiscalServicoLog.data_inclusao',
			'UsuarioAlt.nome',
			'NotaFiscalServicoLog.data_alteracao',
			'NotaFiscalServicoLog.acao_sistema',
			'TipoRecebimento.descricao',
			'FormaPagto.descricao',
			'MotivoAcrescimo.descricao',
			'NotaFiscalServicoLog.chave_rastreamento',
			'NotaFiscalServicoLog.quantos_dias',
			'NotaFiscalServicoLog.baixa_boleto_data',
			'NotaFiscalServicoLog.baixa_boleto_descricao',
			'UusuarioAud.nome',
			'TipoServicoNfs.descricao',
			'NotaFiscalServicoLog.liberacao_data',
			'NotaFiscalServicoLog.observacao',
			'MotivoDesconto.descricao',
        );
		
        //relacionamentos
        $options['joins'] = array(
            array(
                'table' => 'Rhhealth.dbo.fornecedores',
                'alias' => 'Fornecedor',
                'type' => 'INNER',
                'conditions' => 'Fornecedor.codigo = NotaFiscalServicoLog.codigo_fornecedor',
            ),
            array(
                'table' => 'Rhhealth.dbo.nota_fiscal_status',
                'alias' => 'NotaFiscalStatus',
                'type' => 'INNER',
                'conditions' => 'NotaFiscalStatus.codigo = NotaFiscalServicoLog.codigo_nota_fiscal_status',
            ),
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'UsuarioInc',
                'type' => 'INNER',
                'conditions' => 'UsuarioInc.codigo = NotaFiscalServicoLog.codigo_usuario_inclusao',
            ),
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'UsuarioAlt',
                'type' => 'LEFT',
                'conditions' => 'UsuarioAlt.codigo = NotaFiscalServicoLog.codigo_usuario_alteracao',
            ),
            array(
                'table' => 'Rhhealth.dbo.tipo_recebimento',
                'alias' => 'TipoRecebimento',
                'type' => 'LEFT',
                'conditions' => 'TipoRecebimento.codigo = NotaFiscalServicoLog.codigo_tipo_recebimento',
            ),
            array(
                'table' => 'Rhhealth.dbo.formas_pagto',
                'alias' => 'FormaPagto',
                'type' => 'LEFT',
                'conditions' => 'FormaPagto.codigo = NotaFiscalServicoLog.codigo_formas_pagto',
            ),
            array(
                'table' => 'Rhhealth.dbo.motivos_acrescimo',
                'alias' => 'MotivoAcrescimo',
                'type' => 'LEFT',
                'conditions' => 'MotivoAcrescimo.codigo = NotaFiscalServicoLog.codigo_motivo_acrescimo',
            ),
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'UusuarioAud',
                'type' => 'LEFT',
                'conditions' => 'UusuarioAud.codigo = NotaFiscalServicoLog.auditoria_codigo_usuario_responsavel',
            ),
            array(
                'table' => 'Rhhealth.dbo.tipo_servicos_nfs',
                'alias' => 'TipoServicoNfs',
                'type' => 'LEFT',
                'conditions' => 'TipoServicoNfs.codigo = NotaFiscalServicoLog.codigo_tipo_servicos_nfs',
            ),
            array(
                'table' => 'Rhhealth.dbo.motivos_desconto',
                'alias' => 'MotivoDesconto',
                'type' => 'LEFT',
                'conditions' => 'MotivoDesconto.codigo = NotaFiscalServicoLog.codigo_motivo_desconto',
            ),
        );

        //where
        $options['conditions'] = array('NotaFiscalServicoLog.codigo_nota_fiscal_servico' => $codigo_nfs);

        $dados = $this->find('all', $options);

        return $dados;

	}//fim get dados do log

}