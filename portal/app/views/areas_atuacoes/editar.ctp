<?php echo $this->BForm->create('TAatuAreaAtuacao', array('type' => 'post' ,'url' => array('controller' => 'areas_atuacoes','action' => 'editar')));?>
	<?php echo $this->BForm->hidden('aatu_codigo') ?>

    <div class='row-fluid inline parent'>
        <?php echo $this->BForm->input('aatu_descricao',array('class' => 'input-xlarge', 'label' => 'Descrição' )) ?>
    </div>
    
    <div class="form-actions">
          <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
          <?php echo $html->link('Voltar',array('controller' => 'areas_atuacoes', 'action' => 'index'), array('class' => 'btn')) ;?>
    </div>

<?php echo $this->BForm->end() ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		setup_mascaras();
	});
');
?>