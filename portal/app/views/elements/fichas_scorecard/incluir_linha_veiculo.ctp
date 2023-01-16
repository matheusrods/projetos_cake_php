<div class="veiculo-content" id="veiculo-content-<?=$index?>">
	<h4>Dados <?php echo $veiculo_descricao; ?></h4>
	<div class="row-fluid inline">
		<?php echo $this->BForm->hidden("FichaScorecardVeiculo.{$index}.Veiculo.codigo", array('id'=>'codigo_veiculo')) ?>
 		<?if (empty($codigo_ficha) ):?> 
		<?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.Veiculo.placa", array('label' => 'Placa', 'class' => 'input-small placa-veiculo', 'after' => $html->link('...', "javascript:void(0)", array('id' =>'avancar','class' => 'btn btn-search-ellipsis', 'onclick' => 'buscar_dados_placa(this)', 'title' => 'Buscar dados')) )) ?>
	    <?else:?> 
		<?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.Veiculo.placa", array('label' => 'Placa', 'class' => 'input-small placa-veiculo')) ;?>
	    <?endif;?> 
	    <?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.EnderecoCidade.cidade_emplacamento", array('class' => 'input-large ui-autocomplete-input cidade_nome', 'placeholder' => 'Informe uma Cidade', 'label' => 'Cidade')) ?>
        <?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.Veiculo.codigo_estado", array('class' => 'veiculo-estado','type' => 'hidden', 'id'=>'codigo_estado')) ;?>
        <?php echo $this->BForm->hidden("FichaScorecardVeiculo.{$index}.Veiculo.codigo_pais", array('class' => 'veiculo-pais','type' => 'hidden', 'id'=>'codigo_pais')) ;?>
        <?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.Veiculo.codigo_cidade_emplacamento", array('id' => 'codigo_cidade', 'class' => 'veiculo-cidade','type' => 'hidden')) ;?>	
		<?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.Veiculo.chassi", array('label' => 'Chassi', 'class' => 'input-medium chassi')) ?>
		<?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.Veiculo.renavam", array('label' => 'Renavam', 'class' => 'input-medium renavam just-number', 'size' => 11, 'maxlength' => 11)) ?>		
		<?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.Veiculo.codigo_veiculo_tecnologia", array('label' => 'Tecnologia', 'class' => 'input-medium tecnologia', 'empty' => 'Não Possui', 'options'=>$tecnologias)) ?>
	</div>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.Veiculo.codigo_veiculo_cor", array('label' => 'Cor', 'class' => 'input-small cor', 'empty' => 'Cor', 'options'=>$cores)) ?>
		<?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.Veiculo.ano_fabricacao", array('label' => 'Ano Fabricação', 'class' => 'input-medium ano_fabricacao_modelo just-number ano-fabricacao')) ?>
		<?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.Veiculo.ano", array('label' => 'Ano Modelo', 'class' => 'input-medium ano_fabricacao_modelo just-number ano')) ?>
		<?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.Veiculo.codigo_veiculo_fabricante", array('label' => 'Fabricante','class' => 'fabricante input-large fabricante-'.$tipo, 'empty' => 'Selecione um Fabricante', 'options' => $fabricantes)) ?>
		<?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.Veiculo.codigo_veiculo_modelo", array('label' => 'Modelo','class' => 'modelo input-large modelo-'.$tipo, 'empty' => 'Modelo', 'options' => $modelos)) ?>
	</div>
	<?php echo $this->Javascript->codeBlock("
		jQuery(document).ready(function(){
		setup_mascaras();
		function verifica_ano(){
			fabricacao = $('#FichaScorecardVeiculo{$index}VeiculoAnoFabricacao').val();
			modelo = $('#FichaScorecardVeiculo{$index}VeiculoAno').val();
			if( (fabricacao && modelo) && (fabricacao > modelo)) {
				alert('O ano de fabricação não pode ser maior que o ano do modelo')
				$('#FichaScorecardVeiculo{$index}VeiculoAnoFabricacao').val('');
			}
		}
		$('#FichaScorecardVeiculo{$index}VeiculoAnoFabricacao').change(function() {
			verifica_ano();
		});
		
		$('#FichaScorecardVeiculo{$index}VeiculoAno').change(function() {
			verifica_ano();
		});

		$('.fabricante-{$tipo}').change(function() {
			buscar_modelo(this, '.modelo-{$tipo}');
		});
		})");?>
</div>
