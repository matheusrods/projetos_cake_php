<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("
          close_dialog();
          var div = jQuery('#regulador-contatos');
          bloquearDiv(div);
          div.load(baseUrl + 'reguladores_contatos/contatos_por_regulador/' + {$this->passedArgs[0]} + '/' + Math.random() );          
            ");
        exit;
    }
?>
<?php echo $bajax->form('ReguladorContato', array('url' => array('controller' => 'reguladores_contatos','action' => 'incluir', $this->passedArgs[0]))); ?>
<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('codigo') ?>
    <?php echo $this->BForm->hidden('codigo_regulador',array('value'=>$this->data['ReguladorContato']['codigo_regulador'])) ?>
    <?php echo $this->BForm->input('nome', array('label' => 'Representante', 'class' => 'text-medium')) ?>
    <?php echo $this->BForm->input('codigo_tipo_contato', array('label' => 'Tipos de Contato','options' => $tipos_contato, 'multiple' => 'checkbox', 'class' => 'checkbox inline')) ?>
</div>

<div id="retornos">
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo_tipo_retorno', array('label' => false,'options' => $tipos_retorno, 'class' => 'input-medium evt-tipo-contato-codigo', 'empty' => 'Selecione')) ?>
        <?php echo $this->BForm->input('descricao', array('label' => false, 'class' => 'input-xlarge evt-tipo-contato-descricao')) ?>
    </div>
    
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div> 
<?php echo $this->BForm->end(); ?>
<? echo $javascript->codeBlock("setup_datepicker();");?>