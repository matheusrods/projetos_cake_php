<div class='well'>
	<h5><?= $this->Html->link('Dados do Veiculo', 'javascript:void(0)', array('id' => 'filtros')) ?></h5>
	<div id='filtros' style="display: none;">
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('TVeicVeiculo.veic_placa', array('class' => 'input-small placa-veiculo', 'readonly' => true, 'label' => 'Placa', 'type' => 'text')) ?>
			<?php echo $this->BForm->input('TTveiTipoVeiculo.tvei_descricao', array('class' => 'input-small cam-carr', 'readonly' => true, 'label' => 'Tipo', 'type' => 'text', )) ?>
			<?php echo $this->BForm->input('TMveiMarcaVeiculo.mvei_descricao', array('class' => 'input-mini', 'readonly' => true, 'label' => 'Fabricante', 'type' => 'text', )) ?>
			<?php echo $this->BForm->input('TMvecModeloVeiculo.mvec_descricao', array('class' => 'input-large modelo', 'readonly' => true, 'label' => 'Modelo', 'type' => 'text', )) ?>
			<?php echo $this->BForm->input('TVeicVeiculo.veic_cor', array('class' => 'input-medium', 'readonly' => true, 'label' => 'Cor', 'type' => 'text', )) ?>
		</div>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('TVeicVeiculo.veic_ano_fabricacao', array('class' => 'input-medium just-number', 'readonly' => true, 'label' => 'Ano Fabricação', 'type' => 'text', )) ?>
			<?php echo $this->BForm->input('TVeicVeiculo.veic_ano_modelo', array('class' => 'input-medium just-number', 'readonly' => true, 'label' => 'Ano  Modelo', 'type' => 'text', )) ?>
			<?php echo $this->BForm->input('TVeicVeiculo.veic_chassi', array('class' => 'input-small', 'readonly' => true, 'label' => 'Chassi', 'type' => 'text', )) ?>
			<?php echo $this->BForm->input('TVeicVeiculo.veic_renavam', array('class' => 'input-medium', 'readonly' => true, 'label' => 'Renavam', 'type' => 'text', )) ?>
		</div>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('TCidaCidade.cida_descricao', array('class' => 'cidade', 'readonly' => true, 'label' => 'Cidade', 'type' => 'text', )) ?>
			<?php echo $this->BForm->input('TEstaEstado.esta_sigla', array('class' => 'input-small uf', 'readonly' => true, 'label' => 'Estado', 'type' => 'text', )) ?>
			<?php echo $this->BForm->input('TTecnTecnologia.tecn_descricao', array('class' => 'input-medium', 'readonly' => true, 'label' => 'Tecnologia', 'type' => 'text', )) ?>
			<?php echo $this->BForm->input('TVtecVersaoTecnologia.vtec_versao', array('class' => 'input-medium', 'readonly' => true, 'label' => 'Versão Tecnologia', 'type' => 'text', )) ?>
			<?php echo $this->BForm->input('TTermTerminal.term_numero_terminal', array('class' => 'input-medium', 'readonly' => true, 'label' => 'Número Terminal', 'type' => 'text', )) ?>
		</div>
		
	</div>
</div>


<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){

		jQuery("a#filtros").click(function(){
			jQuery("div#filtros").slideToggle("slow");
		});

});', false);
?>
