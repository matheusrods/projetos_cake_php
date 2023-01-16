<div class="well">
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('nome', array('label' => 'Nome hospital (*)', 'class' => 'input-xxlarge'));?>
	</div>
</div>
<div class="well">
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('cep', array('class' => 'evt-endereco-cep input-small', 'label' => 'CEP (*)')); ?>
	</div>
	<div class="row-fluid inline">
		<div >
	        <?php echo $this->BForm->input('estado',array('class' => 'input-mini evt-endereco-estado', 'label' => 'UF', 'type' => 'select','options' => $estados)); ?>
        </div>
        <div >
	        <?php echo $this->BForm->input('cidade', array('class' => 'input evt-endereco-cidade', 'size' => 60, 'label' => 'Cidade'));?>
        </div>
        <div >
	        <?php echo $this->BForm->input('bairro', array(  'class' => 'input-meduim evt-endereco-bairro', 'size' => 60, 'label' => 'Bairro')); 
	        ?>
        </div>
	</div>
	<div class="row-fluid inline">
        <div >
	        <?php                                                                     
	            echo $this->BForm->input('logradouro', array('class' => 'input-max evt-endereco-lagradouro', 'size' => 60, 'label' => 'Logradouro')); 
	        ?>
        </div>
    </div>
    <div class="row-fluid inline">
            <?php echo $this->BForm->input('numero', array('class' => 'input-mini evt-endereco-numero', 'size' => 6, 'label' => 'Número')); ?>
            <?php echo $this->BForm->input('complemento', array('class' => 'input-medium complemento', 'label' => 'Complemento')); ?>
    </div>
</div>
<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'hospitais_emergencia', 'action' => 'lista_hospitais_emergencia', $codigo_cliente, $codigo_unidade), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock("
	$(function(){
        $('#HospitaisEmergenciaCep').attr('callback','RetornoCep');   
    })
	
	$(document).ready(function(){
		setup_mascaras(); 
		setup_datepicker();
	});

	function Disabled(  ){
        $('#HospitaisEmergenciaEstado').removeAttr('readonly')
        $('#HospitaisEmergenciaCidade').removeAttr('readonly')
        $('#HospitaisEmergenciaBairro').removeAttr('readonly')
        $('#HospitaisEmergenciaLogradouro').removeAttr('readonly')
    }

    function RetornoCep( data ){

	    $('#HospitaisEmergenciaCep').parent().find('.alert').remove()
	    Disabled( )

	    if( data ){
	       Fill( data )
	    } else {

	        $('#HospitaisEmergenciaCep').after('<div class=\'alert\'>CEP Não encontrado</div>');
	        setTimeout(function(){
	            $('#HospitaisEmergenciaCep').parent().find('.alert').remove()
	        }, 3000)
	    }    
	}

	function Fill( data ){
    
	    $('#HospitaisEmergenciaEstado').val( data.VEndereco.endereco_estado_abreviacao );
	    $('#HospitaisEmergenciaCidade').val( data.VEndereco.endereco_cidade );      
	    $('#HospitaisEmergenciaBairro').val( data.VEndereco.endereco_bairro );	    
	    $('#HospitaisEmergenciaLogradouro').val( data.VEndereco.endereco_tipo + ' '+data.VEndereco.endereco_logradouro );      
	}
"); ?>