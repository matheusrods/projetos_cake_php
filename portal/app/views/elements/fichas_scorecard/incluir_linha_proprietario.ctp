<div class="proprietario-content proprietario-content<?php echo $index;?>">
	<h4>Proprietário</h4>
	<div class="row-fluid inline"  for="<?php echo $index?>">
		<h5>Motorista é o proprietário <?php echo $veiculo_descricao; ?>?</h5>
		<?php echo $this->BForm->input("Motorista.{$index}.proprietario", array(
			'type' => 'radio', 
			'options' => $eh_motorista, 
			'default' => 0, 			
			'legend' => false, 
			'label' => array('class' => 'radio inline input-small profissional-proprietario motorista-proprietario'.$index)));
		?>
	</div>
	<div class="row-fluid inline">		
		<?if (empty($codigo_ficha) ):?> 
		<?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.Proprietario.codigo_documento", array('label' => 'CPF/CNPJ', 'class' => 'input-medium cpf_cnpj', 'maxlength' => 18, 'after' => $html->link('...', "javascript:setup_proprietario(this,'".$index."');", array('id' =>'avancar','class' => 'btn btn-search-ellipsis', 'title' => 'Buscar dados')) )) ?>
		<?else:?> 
		<?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.Proprietario.codigo_documento", array('label' => 'CPF/CNPJ', 'class' => 'input-medium cpf_cnpj'));?>
		<?endif;?> 
		<?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.Proprietario.nome_razao_social", array('label' => 'Nome/Razão Social', 'class' => 'input-large')) ?>
		<?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.Proprietario.rg", array('label' => 'RG/IE', 'class' => 'input-small')) ?>
		<?php echo $this->BForm->input("FichaScorecardVeiculo.{$index}.Proprietario.rntrc", array('label' => 'RNTRC', 'class' => 'input-small just-number', 'size' => 14, 'maxlength' => 8)) ?>
	</div>
	<div class="cep-data" id="cep-proprietario<?=$index;?>">
		<div class="row-fluid inline">
			<h5>Endereço</h5>			
			<?php 
			echo $this->Buonny->input_cep_endereco($this, array(), $proprietario_enderecos, true, false,  "FichaScorecardVeiculo.{$index}.ProprietarioEndereco"); ?>			
		</div>
	</div>	
	<div id="lista-contatos-proprietario<?php echo $index;?>">
		<?php echo $this->element('fichas_scorecard/lista_contatos', array(
		'titulo'=>'Contatos do Proprietário', 
		'listaContatos'=>isset($this->data['FichaScorecardVeiculo'][$index]['ProprietarioContato']) ? $this->data['FichaScorecardVeiculo'][$index]['ProprietarioContato'] : array(), 
		'tipo'=>"proprietario", 
		'model'=>"FichaScorecardVeiculo.{$index}.ProprietarioContato", 
		'tipo_retorno'=>$tipo_retorno_proprietario, 
		'index'=>$index,
		'disabled'=>(isset($disabled) ? $disabled : FALSE),
		));?>
	</div>
</div>

<?php echo $this->Javascript->codeBlock("
$(document).ready(function () {
	$('.profissional-proprietario').bind('click', function (event) {
		index = $(this).parent().parent().attr('for');
		div = $('#lista-contatos-proprietario'+index);
	    $.ajax({
	        type: 'post',
	        url: baseUrl + 'fichas_scorecard/copia_profissional_contatos/'+index +'/'+Math.random(),
	        cache: false,
	        data: $('form#FichaScorecardIncluirForm').serialize(),
	        beforeSend : function(){
	            bloquearDiv(div);
	        },
	        success: function(data){
	            div.html(data);	            
	        }
	    });
	});
});
");?>	

<?php echo $this->Javascript->codeBlock('
jQuery(document).ready(function(){
	setup_copia_dados_profissional('.$index.');
});', false);?>


<?php echo $this->Javascript->codeBlock("
function setup_proprietario(documento,id){
	var propr = documento.codigo_documento;
	if ( parseInt(propr) > 0){
		$.ajax({
			url: baseUrl + 'proprietarios/buscar/' + propr + '/' + Math.random(),
			type: 'post',
			dataType: 'json',
			success: function(data){
				if (data){
					preenche_campos_prop(data,id);							
				}
			}
		}); 
	} else {
		limpar_campos_proprietario(id);
	}
}
function preenche_campos_prop(data,id){		
	if (data){
		$('#FichaScorecardVeiculo'+ id + 'ProprietarioNomeRazaoSocial').val(data.Proprietario.nome_razao_social);
		$('#FichaScorecardVeiculo'+ id + 'ProprietarioRg').val(data.Proprietario.rg);
		$('#FichaScorecardVeiculo'+ id + 'ProprietarioRntrc').val(data.Proprietario.rntrc);
		$('#FichaScorecardVeiculo'+ id + 'ProprietarioEnderecoEnderecoCep').val(data.EnderecoCep.cep);
		$('#FichaScorecardVeiculo'+ id + 'ProprietarioEnderecoNumero').val(data.ProprietarioEndereco.numero);
		$('#FichaScorecardVeiculo'+ id + 'ProprietarioEnderecoComplemento').val(data.ProprietarioEndereco.complemento);
		buscar_cep($('#FichaScorecardVeiculo'+ id + 'ProprietarioEnderecoEnderecoCep'), '#FichaScorecardVeiculo'+ id + 'ProprietarioEnderecoCodigoEndereco',data.ProprietarioEndereco.codigo_endereco);
	}else{
		$('#FichaScorecardVeiculo'+ id + 'ProprietarioEnderecoEnderecoCep').trigger('blur');
	} 
}");?>