<?
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $this->Session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog(); atualizaListaClientes('gerenciar_clientes_produtos');");
        exit;
    }else{
        echo $this->Buonny->flash();
        //$this->Session->delete('Message.flash');
    }
?>

<?php echo $this->Bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'ClientesProdutos', 'action' => 'configuracoes_mopp', $codigo_cliente), 'type'=>'POST',)) ?>
	<div class="well">
		<div class="row-fluid inline">
			<?php echo $this->BForm->hidden('codigo') ?>
			<div class="control-group input text">
				<?php echo $this->BForm->input('utiliza_mopp', array('label' => 'Utiliza MOPP', 'class' => 'data input-small', 'options'=>Array(0=>'Não',1=>'Sim'))) ?>
			</div>
			<div class="control-group input text" id="divTempoMinimo" style="display: <?=(isset($this->data['Cliente']['utiliza_mopp']) && $this->data['Cliente']['utiliza_mopp']==1 ? "" : "none")?>">
				<?php echo $this->BForm->input('tempo_minimo_mopp', array('label' => 'Tempo Mínimo','maxLength'=>3, 'class' => 'just-number input-small', 'after'=>' <span id="spnAfter">meses</span>')) ?>
			</div>
		</div>
	<div class="row-fluid inline">
		<?= $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary', 'id' => 'btnSalvar', 'name' => 'data[Acao][tipo]')); ?>
	</div>
<?php echo $this->BForm->end(); ?>

<?php echo $this->Buonny->link_js('solicitacoes_monitoramento'); ?>
<?php
	echo $this->Javascript->codeBlock("
		function msg_invalido(objeto, mensagem) {
	        var div1 = '<div id=\'msg-invalido\' style=\'color:#b94a48\' class=\'help-block error-message\'>'+mensagem+'</div>'; 
	        var div2 = document.createElement('div');
	        $(objeto).next().after(div1, div2);
	    }

		$(document).ready(function(){
			setup_mascaras();

			$('#ClienteUtilizaMopp').change(function(){
				if($(this).val()==1) {
					$('#divTempoMinimo').show();
					$('#ClienteTempoMinimoMopp').attr('required',true);
				} else {
					$('#divTempoMinimo').hide();
					$('#ClienteTempoMinimoMopp').val('');
					$('#ClienteTempoMinimoMopp').attr('required',false);
				}

			});

			$('#ClienteTempoMinimoMopp').change(function() {
		    	var obj_msg = $(this).parent().find('.error-message');
		    	if (obj_msg) obj_msg.remove();
		    	var tempo_minimo = asFloat($(this).val());
				if (isNaN($(this).val())) {
					msg_invalido(this,'Valor inválido');
					$(this).val('');
				}				
				if (tempo_minimo>255) {
					msg_invalido(this,'Tempo Mínimo deve ser menor ou igual a 255');
					$(this).val('');
				}
			});
		});
	");
?>