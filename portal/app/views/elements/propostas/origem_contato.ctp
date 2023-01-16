<h5>Origem do Contato</h5>
<div class='well'>
  <div class="row-fluid inline">
      <?php echo $this->BForm->input('codigo_tipo_origem_contato', array('class' => 'input-large', 'label' => 'Origem', 'options'=>$tipo_origem_contato, 'required'=>true, 'empty'=>'Origem','disabled'=>$readonly)); ?>
      <?php echo $this->BForm->input('codigo_corretora', array('class' => 'input-xlarge', 'label' => 'Corretora', 'options'=>$corretoras, 'required'=>true, 'empty'=>'Corretora','disabled'=>$readonly)); ?>
      <?php echo $this->BForm->input('codigo_seguradora', array('class' => 'input-xlarge', 'label' => 'Seguradora', 'options'=>$seguradoras, 'required'=>true, 'empty'=>'Seguradora','disabled'=>$readonly)); ?>
      <?php echo $this->BForm->input("codigo_usuario_gestor", array('class'=>'input-large','options' => $gestores,'label' => 'Gestor', 'required'=>true,'empty'=>'Gestor','disabled'=>$readonly)) ?>
  </div>
</div>
