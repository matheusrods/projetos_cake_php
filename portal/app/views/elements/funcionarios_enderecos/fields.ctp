<?php $edit_mode = isset($edit_mode) ? $edit_mode : NULL; ?>

<div id="funcionario-endereco">
	<div class="span6" style="margin-left: 0px;">
	    <div class="row-fluid inline">
	        <?php echo $this->BForm->hidden('FuncionarioEndereco.codigo'); ?>
	        <?php echo $this->BForm->input('FuncionarioEndereco.cep', array('class' => 'evt-endereco-cep input-small just-number', 'label' => 'CEP', 'readonly' => $edit_mode)); ?>
	            </div>
	    <div class="row-fluid inline">

			<?php echo $this->BForm->input('FuncionarioEndereco.logradouro', array('label' => 'Logradouro', 'class' => 'input-xlarge endereco-logradouro')); ?>

            <?php echo $this->BForm->input('FuncionarioEndereco.numero', array('class' => 'input-mini evt-endereco-numero', 'size' => 4, 'maxlength' => 6, 'label' => 'Número', 'readonly' => $edit_mode)); ?>
            <?php echo $this->BForm->input('FuncionarioEndereco.complemento', array('class' => 'input-medium complemento', 'label' => 'Complemento', 'readonly' => $edit_mode)); ?>
	    </div>
	    <div class="row-fluid inline">
	        <div class="clear">
			<?php echo $this->BForm->input('FuncionarioEndereco.bairro', array('label' => 'Bairro', 'class' => 'input-medium endereco-bairro')); ?>
			<?php echo $this->BForm->input('FuncionarioEndereco.cidade', array('label' => 'Cidade', 'class' => 'input-medium endereco-cidade')); ?>
			<?php echo $this->BForm->input('FuncionarioEndereco.estado_abreviacao', array('label' => 'Estado', 'class' => 'input-mini endereco-estado', 'value' => !empty($this->data['FuncionarioEndereco']['estado_abreviacao']) ? $this->data['FuncionarioEndereco']['estado_abreviacao'] : '' ,'options' => $estados)); ?>
	        </div>
	    </div>
	</div>
</div>

<script type="text/javascript">
	$(function(){
		$(".evt-endereco-cep").attr('callback','RetornoCep');	
	})
	
/*  function Disabled(  ){
        $('#FuncionarioEnderecoCep').parent().find('.alert').remove()
        $('#FuncionarioEnderecoEstadoDescricao').val( '' ).removeAttr('readonly')
        $('#FuncionarioEnderecoCidade').val( '' ).removeAttr('readonly')
        $('#FuncionarioEnderecoBairro').val( '' ).removeAttr('readonly')
        $('#FuncionarioEnderecoLogradouro').val( '' )    
    } */
function RetornoCep( data ){
     // Disabled(  )
    if( data ){
        $('#FuncionarioEnderecoEstadoAbreviacao').val( data.VEndereco.endereco_estado_abreviacao );
        $('#FuncionarioEnderecoCidade').val( data.VEndereco.endereco_cidade );
        $('#FuncionarioEnderecoBairro').val( data.VEndereco.endereco_bairro );
        $('#FuncionarioEnderecoLogradouro').val( data.VEndereco.endereco_tipo+' '+data.VEndereco.endereco_logradouro );    
    } else {
        $('#VEnderecoEnderecoCep').after('<div class=\'alert\'>CEP Não encontrado</div>');
        setTimeout(function(){
            $('#VEnderecoEnderecoCep').parent().find('.alert').remove()
        }, 3000)
    }
    
}

</script>