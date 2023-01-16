<tr class="tablerow-input resposta-item">
    <td>
        <?php echo $this->BForm->input("{$model}.{$key}.resposta", array('label' => false,'onblur'=>'tira_erro(this)' , 'class' => 'evt-teste input-xxlarge')) ?>
    </td>
    <td>
        <?php if($key > 0 && empty($tipo_retorno_fixo)): ?>
            <?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error remove-resposta', 'escape' => false)); ?>				
        <?php endif; ?>
    </td>
</tr>
<?php echo $this->Javascript->codeBlock('
      function tira_erro(element){
           $(element).parent().removeClass("error");
           $(element).parent().find(".error-message").remove();
      }  

');

