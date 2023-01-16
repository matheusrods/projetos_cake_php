<?php if(isset($veiculo_sem_cliente) && $veiculo_sem_cliente == TRUE): ?>
    <div class="alert">
        Veículo sem vínculo com Cliente.
    </div>
<?php else: ?>
<?php echo $this->BForm->create('TCveiChecklistVeiculo', array('url' => array('controller' => 'Veiculos','action' => 'incluir_checklist',$this->passedArgs[0],$this->passedArgs[1],0)));?>
<?php echo $this->element('veiculos/checklist_dados',array('voltar' => array('controller' => 'Veiculos','action' => 'checklist_por_placa',$dados_relacionameto[0][0]['veic_placa']))) ?>
<?php echo $this->BForm->hidden('TCveiChecklistVeiculo.cvei_pess_oras_codigo');?>
<?php echo $this->BForm->hidden('TCveiChecklistVeiculo.cvei_veic_oras_codigo') ?>
<?php echo $this->element('veiculos/checklist_perifericos');  ?>
<div class="form-actions">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success', 'id'=>'botao-submit')); ?>
	<?php echo $html->link('Voltar', array('action' => 'checklist',$dados_relacionameto[0][0]['veic_placa']), array('class' => 'btn')) ;?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	setup_mascaras();

        $("#botao-submit").dblclick(function(e){
            e.preventDefault();
        });

		var telefones = ["(11)1111-11111","(22)2222-22222","(33)3333-33333","(44)4444-44444","(55)5555-55555","(66)6666-66666","(77)7777-77777","(88)8888-88888","(99)9999-99999","(00)0000-00000"];
    	$("#TCveiChecklistVeiculoCveiContatoTelefone").blur(function(){
    		$("#TCveiChecklistVeiculoCveiContatoTelefone").removeClass("form-error");
    		$("#TCveiChecklistVeiculoCveiContatoTelefone").parent().find(".help-block").remove();
    		$("#TCveiChecklistVeiculoCveiContatoTelefone").parent().removeClass("error");
    		if($("#TCveiChecklistVeiculoCveiContatoTelefone").val() == ""){
    			$("#TCveiChecklistVeiculoCveiContatoTelefone").addClass("form-error");
    			$("#TCveiChecklistVeiculoCveiContatoTelefone").parent().addClass("error");
    			$("#TCveiChecklistVeiculoCveiContatoTelefone").parent().append("<div class=\"help-block error-message\">Informe um telefone</div>");
    		}else if (telefones.indexOf($("#TCveiChecklistVeiculoCveiContatoTelefone").val()) != -1) {
    			$("#TCveiChecklistVeiculoCveiContatoTelefone").addClass("form-error");
    			$("#TCveiChecklistVeiculoCveiContatoTelefone").parent().addClass("error");
    			$("#TCveiChecklistVeiculoCveiContatoTelefone").parent().append("<div class=\"help-block error-message\">Informe um telefone valido</div>");
    		}
    	})
		$("#TCveiChecklistVeiculoCveiContatoNome").blur(function(){
			$("#TCveiChecklistVeiculoCveiContatoNome").removeClass("form-error");
			$("#TCveiChecklistVeiculoCveiContatoNome").parent().find(".help-block").remove();
    		$("#TCveiChecklistVeiculoCveiContatoNome").parent().removeClass("error");
			if($("#TCveiChecklistVeiculoCveiContatoNome").val() == ""){
				$("#TCveiChecklistVeiculoCveiContatoNome").addClass("form-error");
    			$("#TCveiChecklistVeiculoCveiContatoNome").parent().addClass("error");
				$("#TCveiChecklistVeiculoCveiContatoNome").parent().append("<div class=\"help-block error-message\">Informe um contato</div>");
			}
    	})
    	$("#botao-submit").click(function(){
    		var sucesso = true;
    		$("#TCveiChecklistVeiculoCveiContatoTelefone").removeClass("form-error");
    		$("#TCveiChecklistVeiculoCveiContatoTelefone").parent().find(".help-block").remove();
    		$("#TCveiChecklistVeiculoCveiContatoTelefone").parent().removeClass("error");
    		if($("#TCveiChecklistVeiculoCveiContatoTelefone").val() == ""){
    			$("#TCveiChecklistVeiculoCveiContatoTelefone").addClass("form-error");
    			$("#TCveiChecklistVeiculoCveiContatoTelefone").parent().addClass("error");
    			$("#TCveiChecklistVeiculoCveiContatoTelefone").parent().append("<div class=\"help-block error-message\">Informe um telefone</div>");
    			$("#TCveiChecklistVeiculoCveiContatoTelefone").focus();
    			sucesso = false;
    		}else if (telefones.indexOf($("#TCveiChecklistVeiculoCveiContatoTelefone").val()) != -1) {
    			$("#TCveiChecklistVeiculoCveiContatoTelefone").addClass("form-error");
    			$("#TCveiChecklistVeiculoCveiContatoTelefone").parent().addClass("error");
    			$("#TCveiChecklistVeiculoCveiContatoTelefone").parent().append("<div class=\"help-block error-message\">Informe um telefone valido</div>");
    			$("#TCveiChecklistVeiculoCveiContatoTelefone").focus();
    			sucesso = false;
    		}

			$("#TCveiChecklistVeiculoCveiContatoNome").removeClass("form-error");
			$("#TCveiChecklistVeiculoCveiContatoNome").parent().find(".help-block").remove();
    		$("#TCveiChecklistVeiculoCveiContatoNome").parent().removeClass("error");
			if($("#TCveiChecklistVeiculoCveiContatoNome").val() == ""){
				$("#TCveiChecklistVeiculoCveiContatoNome").addClass("form-error");
    			$("#TCveiChecklistVeiculoCveiContatoNome").parent().addClass("error");
				$("#TCveiChecklistVeiculoCveiContatoNome").parent().append("<div class=\"help-block error-message\">Informe um contato</div>");
				$("#TCveiChecklistVeiculoCveiContatoNome").focus();
				sucesso = false;
			}            
            if( sucesso = true ){
                $("#botao-submit").click(function(e){
                    e.preventDefault();
                    return false;
                });                
            }
			return sucesso;
    	})
    });', false);
?>
<?php endif; ?>