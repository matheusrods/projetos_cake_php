<?php echo $this->BForm->create('AutotracExcecao', array('url' => array('controller' => 'autotrac_faturamentos', 'action' => 'incluir_excecao')));?>
    <div class="row-fluid inline">

       <?php echo $this->Buonny->input_codigo_cliente_dados($this,array(
        'razao_social' => 'AutotracExcecaoRazaoSocial',),'codigo_cliente', null, true, 'AutotracExcecao') ?>
    <?php echo $this->BForm->input('razao_social', array('label' => 'Razão Social', 'class' => 'input-xxlarge', 'readonly'=>true)) ?>
    </div>
    <div class="form-actions">
          <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
          <?php echo $html->link('Voltar',array('controller' => 'autotrac_faturamentos', 'action' => 'excecao'), array('class' => 'btn')) ;?>
    </div>
<?php echo $this->BForm->end() ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		setup_mascaras();
	});
');
?>