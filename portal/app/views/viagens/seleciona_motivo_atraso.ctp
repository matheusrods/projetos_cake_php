<div class='row-fluid inline well' style="width: 95%">
	<?php echo $this->BForm->input('motivos_atraso', array('class' => 'input-xlarge', 'label' => 'Selecione o Motivo do Atraso', 'options' => $motivos_atraso)) ?>
</div>
<?php echo $this->Html->link('Confirmar', 'javascript: seleciona_motivo_atraso(); ',array('div' => false, 'class' => 'btn btn-success')) ?>
&nbsp;
<?php echo $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')) ;?>

	

<?php echo $this->Javascript->codeBlock('
    function msg_invalido(objeto, mensagem) {
        var div1 = "<div id=\"msg-desconto-invalido\" style=\"color:#b94a48\" class=\"help-block error-message\">"+mensagem+"</div>"; 
        var div2 = document.createElement("div");
        $(objeto).after(div1, div2); 
    }

	function seleciona_motivo_atraso() {
		if ($("#motivos_atraso").val()=="") {
			msg_invalido(obj,"Deve-se informar o motivo do atraso");
			return false;
		}
		$("#TVcheViagemChecklistVcheCmatCodigo").val($("#motivos_atraso").val());		
		$("#TViagViagemPostForm").submit();
	}

	$(document).ready(function(){
		
	});', false);
?>