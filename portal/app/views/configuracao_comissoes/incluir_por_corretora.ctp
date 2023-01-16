<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("
        	close_dialog();
        	atualizaListaConfiguracaoComissaoCorretora('#lista');
        	$('#ConfiguracaoComissaoCorreCodigoCorretora').val('".$this->data['ConfiguracaoComissaoCorre']['codigo_corretora']."');
        	$('#ConfiguracaoComissaoCorreCodigoCorretoraVisual').val('".$this->data['ConfiguracaoComissaoCorre']['codigo_corretora_visual']."');
        	$('#ConfiguracaoComissaoCorreCodigoProduto').val('".$this->data['ConfiguracaoComissaoCorre']['codigo_produto']."');
        	$.ajax({
				'url': baseUrl + 'produtos_servicos/servicos_por_produto/".$this->data['ConfiguracaoComissaoCorre']['codigo_produto']."/' + Math.random(),
				'success': function(data) {
					$('#ConfiguracaoComissaoCorreCodigoServico').html(data);
					$('#ConfiguracaoComissaoCorreCodigoServico').val('".$this->data['ConfiguracaoComissaoCorre']['codigo_servico']."');
				}
			});
		");
        exit;
    }
?>
<?php echo $this->Bajax->form('ConfiguracaoComissaoCorre', array('url' => array('controller' => 'configuracao_comissoes','action' => 'incluir_por_corretora'))) ?>
	<?php echo $this->element('configuracao_comissoes/fields_por_corretora'); ?>
<?php echo $this->BForm->end(); ?>