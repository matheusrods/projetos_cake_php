<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('codigo'); 
    echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente','Tveiculos') ?>
    <?php echo $this->BForm->input('data',array('class' => 'input-small data','type'=>'text', 'label' => 'Data')); ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('chassi', array('class' => 'input-medium','label' => 'Chassi' )); ?> 
    <?php echo $this->BForm->input('local', array('class' => 'input-medium','label' => 'Local' )); ?>    
    <?php echo $this->BForm->input('transportador', array('class' => 'input-medium','label' => 'Transportador' )); ?>    
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('entrada_saida', array('class' => 'input-medium','label' => 'Entrada/Saida' )); ?>
    <?php echo $this->BForm->input('veiculo_tipo', array('class' => 'input-medium','label' => 'Tipo do Veículo' )); ?>
    <?php echo $this->BForm->input('veiculo_cor', array('class' => 'input-medium','label' => 'Cor do Veículo' )); ?>    
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('avaria_tipo', array('class' => 'input-medium','label' => 'Tipo da Avaria' )); ?>    
    <?php echo $this->BForm->input('fronte', array('class' => 'input-medium','label' => 'Fronte' )); ?>
    <?php echo $this->BForm->input('lateral', array('class' => 'input-medium','label' => 'Lateral' )); ?>
</div>
<div class="row-fluid inline" id="novo">
  <?php echo $this->BForm->input('filename_pic', array('type'=>'file', 'label' => 'Upload de Foto')); ?>
</div>
<div class="row-fluid inline" id="desfazer">  
  <?php echo $this->data['Tveiculos']['filename_pic']; ?>
  <input type="hidden" id="TveiculosTemFoto" name="data[Tveiculos][tem_foto]">
  <span class="btn desfazer" style="margin-left: 5px" data-tipo="faturamento">Escolher outra foto</span>  
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>    
<?php echo $this->BForm->end(); ?>

<?php 
if(empty($this->data['Tveiculos']['filename_pic']) || $this->data['Tveiculos']['filename_pic']==" "){ 
 echo $this->Javascript->codeBlock('
    $("#novo").show();
    $("#TveiculosTemFoto").val("0");
    $("#desfazer").hide();
   ', false); 
 } else {
  echo $this->Javascript->codeBlock('
    $("#desfazer").show();
    $("#TveiculosTemFoto").val("1");
    $("#novo").hide();
   ', false); 
 } ?>
<?php echo $this->Javascript->codeBlock('
   jQuery(document).ready(function(){
       setup_datepicker();
       $("#TveiculosFilenamePic").change(function(){
          if($(this).val()=="")
            $("#TveiculosTemFoto").val("0");
          else
            $("#TveiculosTemFoto").val("1");
       });
       $("#desfazer").click(function(){
          $("#TveiculosTemFoto").val("0");
          $("#TveiculosFilenamePic").val("");
          $("#novo").show();
          $("#desfazer").hide();
       });
    });', false); 
?>    
