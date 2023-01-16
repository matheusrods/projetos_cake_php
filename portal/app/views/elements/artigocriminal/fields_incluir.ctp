
<div class="row-fluid inline parent">
        <h4>Buscar Profissional</h4> 
        <?php echo $this->element('profissionais/fields') ?>
    </div>
<div class="row-fluid inline">
    	<?php 
       echo $this->BForm->input('ProfissionalNegativacao.tipo_negociacao',array('id'=>'tipo_negativacao', 'name' => "data[ProfissionalNegativacao][tipo_negociacao][]",'label'=>'Tipo Negociação',"empty" => "Retorno","default"=> $negativacao,"options" => $tipoNegativacao
    )); ?></td>
    <?php echo $this->BForm->input('ProfissionalNegativacao.observacao',array('class' => 'input-xxlarge data','type'=>'textarea','cols'=>'90', 'label' => 'Observação')); ?>   

</div>

<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>

</div>    
<?php echo $this->BForm->end(); ?>
