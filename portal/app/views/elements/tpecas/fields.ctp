<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('codigo'); 
    echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente','Tpecas') ?>
    <?php echo $this->BForm->input('data',array('class' => 'input-small data','type'=>'text', 'label' => 'Data')); ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('numero_peca', array('class' => 'input-medium','label' => 'Número da Peça' )); ?> 
    <?php echo $this->BForm->input('local', array('class' => 'input-medium','label' => 'Local' )); ?>    
    <?php echo $this->BForm->input('transportador', array('class' => 'input-medium','label' => 'Transportador' )); ?>    
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('dn', array('class' => 'input-medium','label' => 'DN' )); ?>
    <?php echo $this->BForm->input('tipo_caixa', array('class' => 'input-medium','label' => 'Tipo da Caixa' )); ?>
    <?php echo $this->BForm->input('tipo_caixa_avaria', array('class' => 'input-medium','label' => 'Tipo da Avaria da Caixa' )); ?>    
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('tipo_peca', array('class' => 'input-medium','label' => 'Tipo da Peça' )); ?>
    <?php echo $this->BForm->input('tipo_peca_avaria', array('class' => 'input-medium','label' => 'Tipo da Avaria da Peça' )); ?>    
    <?php echo $this->BForm->input('destino', array('class' => 'input-medium','label' => 'Destino' )); ?>
</div>
<div class="row-fluid inline" id="novo">
  <?php echo $this->BForm->input('filename_pic', array('type'=>'file', 'label' => 'Upload de Foto')); ?>
</div>
<div class="row-fluid inline" id="desfazer">  
  <?php echo $this->data['Tpecas']['filename_pic']; ?>
  <input type="hidden" id="TpecasTemFoto" name="data[Tpecas][tem_foto]">
  <span class="btn desfazer" style="margin-left: 5px" data-tipo="faturamento">Escolher outra foto</span>  
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>    
<?php echo $this->BForm->end(); ?>

<?php 
if(empty($this->data['Tpecas']['filename_pic']) || $this->data['Tpecas']['filename_pic']==" "){ 
 echo $this->Javascript->codeBlock('
    $("#novo").show();
    $("#TpecasTemFoto").val("0");
    $("#desfazer").hide();
   ', false); 
 } else {
  echo $this->Javascript->codeBlock('
    $("#desfazer").show();
    $("#TpecasTemFoto").val("1");
    $("#novo").hide();
   ', false); 
 } ?>
<?php echo $this->Javascript->codeBlock('
   jQuery(document).ready(function(){
       setup_datepicker();
       $("#TpecasFilenamePic").change(function(){
          if($(this).val()=="")
            $("#TpecasTemFoto").val("0");
          else
            $("#TpecasTemFoto").val("1");
       });
       $("#desfazer").click(function(){
          $("#TpecasTemFoto").val("0");
          $("#TpecasFilenamePic").val("");
          $("#novo").show();
          $("#desfazer").hide();
       });
    });', false); 
?>    
