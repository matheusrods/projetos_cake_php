<?php echo $this->BForm->create('TCdfvCriterioFaixaValor', array('url' => array('controller' => 'faixas_valores','action' => 'incluir')));?>

    <div class='row-fluid inline parent'>        
        <?php echo $this->BForm->input('cdfv_descricao',array('class' => 'input-xlarge', 'label' => 'Descrição' )) ?>
    </div>
    <div class='row-fluid inline parent'>       
        <?php echo $this->BForm->input('cdfv_valor_minimo',array('class' => 'input-medium moeda', 'label' => 'Valor Mínimo', 'maxlength' => 13 )) ?>
        <?php echo $this->BForm->input('cdfv_valor_maximo',array('class' => 'input-medium moeda', 'label' => 'Valor Maximo', 'maxlength' => 13 )) ?>
    </div>    
    
    <div class="form-actions">
          <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
          <?php echo $html->link('Voltar',array('controller' => 'faixas_valores', 'action' => 'index'), array('class' => 'btn')) ;?>
    </div>

<?php echo $this->BForm->end() ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		setup_mascaras();
	});
');
?>