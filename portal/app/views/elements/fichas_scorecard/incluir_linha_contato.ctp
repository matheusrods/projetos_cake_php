<?php $disabled = (isset($disabled) && $disabled == true ? true : false);?>
<tr class="tablerow-input contato-<?php echo $tipo; ?>-item">
    <td>
        <?php echo $this->BForm->input("{$model}.{$key}.nome", array('label' => false,'onblur'=>'tira_erro(this)' , 'class' => 'evt-teste input-medium just-letters', 'readonly' => $disabled )) ?>
    </td>
    	<?php if($tipo == 'retorno'): ?>
    		<?php echo $this->BForm->hidden("{$model}.{$key}.codigo_tipo_contato", array('value' => 2)) ?>
    	<?php else: ?>
		    <td>
        		<?php echo $this->BForm->input("{$model}.{$key}.codigo_tipo_contato", array('label' => false, 'empty' => 'Tipo','class' => 'input-medium', 'options' => $tipo_contato, 'disabled' => $disabled )) ?>
		    </td>
        <?php endif; ?>
    <td>
    	<?php if( !empty($tipo_retorno_fixo) && !empty($tipo_retorno[$tipo_retorno_fixo]) ): ?>
    		<?php echo $this->BForm->hidden("{$model}.{$key}.fixo", array('value' => 1)) ?>
    	    <?php echo $this->BForm->input("{$model}.{$key}.codigo_tipo_retorno", array('label' => false, 'class' => 'input-large tipo_retorno', 'options' => array($tipo_retorno_fixo=>$tipo_retorno[$tipo_retorno_fixo]), 'value'=>$tipo_retorno_fixo, 'readonly'=>true)) ?>
			<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){ selecionaTipoRetorno("#'.$model.$key.'CodigoTipoRetorno"); });');?>
    	<?php else: ?>
	        <?php echo $this->BForm->input("{$model}.{$key}.codigo_tipo_retorno", array('label' => false, 'empty' => 'Tipo de Referência','class' => 'input-medium tipo_retorno', 'options' => $tipo_retorno, 'onChange'=>'selecionaTipoRetorno(this)', 'value'=>(!empty($tipo_retorno_fixo) ? $tipo_retorno_fixo : (isset($contato['codigo_tipo_retorno']) ? $contato['codigo_tipo_retorno'] : '')), 'readonly'=> (!empty($tipo_retorno_fixo) && !empty($tipo_retorno[$tipo_retorno_fixo])), 'disabled' => $disabled ) ) ?>
        <?php endif; ?>
    </td>
    <td>
        <?php echo $this->BForm->input("{$model}.{$key}.descricao", array('label' => false,'onblur'=>'tira_erro(this)','class' => 'input-large descricao '.(isset($contato['codigo_tipo_retorno']) && in_array($contato['codigo_tipo_retorno'], array(1, 5)) ? 'telefone' : ''), 'readonly' => $disabled )) ?>
    </td>
    <td>
    <?php if(!$disabled) : ?>
        <?php if($key > 0 && empty($tipo_retorno_fixo)): ?>
            <?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error remove-contato-'.$tipo, 'escape' => false)); ?>				
        <?php endif; ?>
    <?php endif; ?>
    </td>
</tr>
<?php echo $this->Javascript->codeBlock('
	function selecionaTipoRetorno(element){
		var inputDescricao = $(element).parent().parent().next().find(".descricao");
		inputDescricao.attr("class", "input-large descricao").unmask();
		if($(element).val() == 1 || $(element).val() == 5){
			inputDescricao.addClass("telefone");
			setup_mascaras();
		}
	 }
      function tira_erro(element){
           
           $(element).parent().removeClass("error");
           $(element).parent().find(".error-message").remove();
      }  

');

