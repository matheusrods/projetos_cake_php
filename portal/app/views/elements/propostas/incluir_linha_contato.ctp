<tr class="tablerow-input contato-item">
    <td>
        <?php echo $this->BForm->input("{$model}.{$key}.nome", array('label' => false,'onblur'=>'tira_erro(this)' , 'class' => 'evt-teste input-medium just-letters-without-special', 'readonly'=>$readonly)) ?>
    </td>
    <td>
		<?php echo $this->BForm->input("{$model}.{$key}.codigo_tipo_contato", array('label' => false, 'empty' => 'Tipo','class' => 'input-medium', 'options' => $tipo_contato, 'disabled'=>$readonly)) ?>
    </td>
    <td>
        <? if (!empty($codigo_tipo_retorno_fixo)): ?>
        <?php echo $this->BForm->hidden("{$model}.{$key}.codigo_tipo_retorno", array('value'=>$codigo_tipo_retorno_fixo)) ?>
        <?php echo $this->BForm->input("{$model}.{$key}.tipo_retorno", array('label' => false, 'empty' => 'Tipo de Referência','class' => 'input-medium tipo_retorno', 'options' => $tipo_retorno, 'onChange'=>'selecionaTipoRetorno(this)', 'value'=>(!empty($codigo_tipo_retorno_fixo) ? $codigo_tipo_retorno_fixo : (isset($contato['codigo_tipo_retorno']) ? $contato['codigo_tipo_retorno'] : '')), 'disabled'=>(!empty($codigo_tipo_retorno_fixo) ? true : $readonly) )) ?>
        <? else: ?>
        <?php echo $this->BForm->input("{$model}.{$key}.codigo_tipo_retorno", array('label' => false, 'empty' => 'Tipo de Referência','class' => 'input-medium tipo_retorno', 'options' => $tipo_retorno, 'onChange'=>'selecionaTipoRetorno(this)', 'value'=>(!empty($codigo_tipo_retorno_fixo) ? $codigo_tipo_retorno_fixo : (isset($contato['codigo_tipo_retorno']) ? $contato['codigo_tipo_retorno'] : '')), 'disabled'=>(!empty($codigo_tipo_retorno_fixo) ? true : $readonly) )) ?>
        <? endif;?>
    </td>
    <td>
        <?php echo $this->BForm->input("{$model}.{$key}.contato", array('label' => false,'onblur'=>'tira_erro(this)','class' => 'input-large descricao '.(isset($contato['codigo_tipo_retorno']) && in_array($contato['codigo_tipo_retorno'], array(1, 5)) ? 'telefone' : ''), 'readonly'=>$readonly)) ?>
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

