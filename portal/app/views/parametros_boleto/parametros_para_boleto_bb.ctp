<div class="formulario">
    <?php echo $this->BForm->create('ParametroBoleto', array('url' => array('controller' => 'parametros_boleto', 'action' => 'parametros_para_boleto_bb'))); ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->hidden('codigo'); ?>
            <?php echo $this->BForm->input('taxa_boleto', array('class' => 'input-mini moeda', 'label' => 'Taxa do Boleto', 'type' => 'text')); ?>
            <?php echo $this->BForm->input('numero_documento', array('class' => 'input-mini', 'label' => 'Número do Documento', 'type' => 'text')); ?>
            <?php echo $this->BForm->input('aceite', array('class' => 'input-mini', 'label' => 'Aceite', 'type' => 'text')); ?>
            <?php echo $this->BForm->input('especie', array('class' => 'input-mini', 'label' => 'Espécie', 'type' => 'text')); ?>
            <?php echo $this->BForm->input('especie_doc', array('class' => 'input-mini', 'label' => 'Espécie Doc', 'type' => 'text')); ?>
            <?php if(Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO):?>
            	<?php echo $this->BForm->input('multa', array('class' => 'input-mini', 'label' => 'Multa(%)', 'type' => 'text'));?>
               	<?php echo $this->BForm->input('juros', array('class' => 'input-mini', 'label' => 'Juros(%)', 'type' => 'text')); ?>
            <?php endif?>	
            <?php echo $this->BForm->input('contrato', array('class' => 'input-mini', 'label' => 'Contrato(BB)', 'type' => 'text')); ?>
        </div>
        <div class="row-fluid">
            <?php echo $this->BForm->input('informacoes_cliente', array('class' => 'input-xxlarge', 'label' => 'Informações para o Cliente', 'type' => 'textarea')); ?>
        </div>
        <div class="row-fluid">
            <?php echo $this->BForm->input('instrucoes_caixa', array('class' => 'input-xxlarge', 'label' => 'Instruções para o Caixa', 'type' => 'textarea')); ?>
        </div>
        <div class="form-actions">
          <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
        </div>    
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->addScript($this->Javascript->codeBlock('jQuery(document).ready(function(){setup_mascaras();});')); ?>