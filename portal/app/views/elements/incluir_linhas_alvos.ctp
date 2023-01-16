<tr class="tablerow-input contato-item">
    <td>
        <?php  echo $this->Buonny->input_referencia($this, '#TRacsRegraAceiteSmCodigoCliente', $model, 'cvva_refe_codigo', $key); ?>
    </td>
    <td>
        <?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error remove-contato', 'escape' => false)); ?>                
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

