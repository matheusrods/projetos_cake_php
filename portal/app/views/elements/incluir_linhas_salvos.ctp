<tr class="tablerow-input contato-item">
    <td>
        <?php echo $this->BForm->input("{$model}.{$key}.nome", array('label' => false,'onblur'=>'tira_erro(this)' , 'class' => 'evt-teste input-medium just-letters-without-special', 'readonly'=>$readonly)) ?>
    </td>
    <td>
        <?php if($key > 0 && !($readonly)): ?>
            <?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error remove-contato', 'escape' => false)); ?>				
        <?php endif; ?>
    </td>
</tr>
<?php echo $this->Javascript->codeBlock('
	function selecionaTipoRetorno(element){
		var inputDescricao = $(element).parent().parent().next().find(".descricao");
		inputDescricao.attr("class", "input-large descricao").unmask();
		if($(element).val() == 1 || $(element).val() == 5 || $(element).val() == 7){
			inputDescricao.addClass("telefone");
			setup_mascaras();
		}
	 }

     function tira_erro(element){
           
           $(element).parent().removeClass("error");
           $(element).parent().find(".error-message").remove();
     }  
     jQuery(document).ready(function(){ 
        selecionaTipoRetorno("#'.$model.$key.'CodigoTipoRetorno"); 
        setup_mascaras();
     });
');

