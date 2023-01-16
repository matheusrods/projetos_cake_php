<div class='well'>
    <strong><?= $cliente['TPjurPessoaJuridica']['pjur_razao_social']?></strong>
</div>
<?php echo $this->BForm->create('TCpatCargasPatio', array('autocomplete' => 'off', 'url' => array('controller' => 'cargas_patio', 'action' => 'editar'))) ?>
<div class="row-fluid inline">
	<?php echo $this->BForm->hidden('cpat_pjur_pess_oras_codigo');?>
  <?php echo $this->BForm->input('cpat_placa_carreta', array('class' => 'input-small placa-veiculo','label' =>'Placa da Carreta')); ?>
  <?php echo $this->BForm->input('cpat_loadplan', array('class' => 'input-medium numeric just-number', 'label' => 'Loadplan')); ?>
	<?php echo $this->BForm->input('cpat_valor', array('class' => 'input-medium numeric just-number', 'label' => 'Valor')); ?>
  <?php echo $this->BForm->input('cpat_data_carregamento', array('class' => 'data input-small', 'label' => 'Data do carregamento', 'type' => 'text')) ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('cpat_nota', array('class' => 'input-medium numeric just-number', 'label' => 'Nota')); ?>
	<?php echo $this->BForm->input('cpat_pedido', array('class' => 'input-medium numeric just-number', 'label' => 'Pedido')); ?>
  <?php echo $this->BForm->input('cpat_refe_codigo_origem', array('label' => 'CD','class' => 'input-large', 'options'=> $cds,'empty' => 'Selecione o CD')); ?>
</div>
<div class="form-actions">
      <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
      <?php echo $html->link('Voltar',array('controller' => 'cargas_patio', 'action' => 'index'), array('class' => 'btn')) ;?>
</div>
<?php echo $this->Javascript->codeBlock('
    $(document).ready(function(){
      setup_mascaras();
      setup_datepicker(); 
      carregar_valor_loadplan();
      $("#TCpatCargasPatioCpatLoadplan").blur(function(){
            carregar_valor_loadplan(true);
      });

      function invalidaLoadPlan() {
        var div1 = "<div id=\"cargas-patio-div\" style=\"color:#b94a48\" class=\"help-block error-message\">Informe um Loadplan válido</div>"; 
        var div2 = document.createElement("div");
        $("#TCpatCargasPatioCpatLoadplan").after(div1, div2); 
      } 

      function carregar_valor_loadplan(automatico){
        codigo = $("#TCpatCargasPatioCpatLoadplan");
          $.ajax({
            url: baseUrl + "cargas_patio/buscar_loadplan/" + codigo.val() + "/" + Math.random(),
            cache: false,
            type: "post",
            dataType: "json",
            beforeSend: function(){
                codigo.addClass("ui-autocomplete-loading");
            },
            success: function(data){
              if(data.sucesso == true){                    
               if(!data.dados === false){ 
                  $("#TCpatCargasPatioCpatValor").val(data.dados.viag_valor_carga);
                  document.getElementById("TCpatCargasPatioCpatValor").disabled = true;
                  $( "#cargas-patio-div" ).remove();
                }else{
                  if($("#TCpatCargasPatioCpatValor").val() > 0){
                    $( "#cargas-patio-div" ).remove();
                  }
                  if(automatico){
                    $("#TCpatCargasPatioCpatValor").val("");
                    document.getElementById("TCpatCargasPatioCpatValor").disabled = false;
                    $( "#cargas-patio-div" ).remove();                      
                  }  
                }
              }else{                          
                if(automatico){
                  $("#TCpatCargasPatioCpatValor").val("");                         
                  if(!$(".error-message").length ){
                    invalidaLoadPlan();                              
                  }
                }  
                document.getElementById("TCpatCargasPatioCpatValor").disabled = false;
              }
            },
            complete: function(){
                codigo.removeClass("ui-autocomplete-loading");
            }
        });  
        return false;
      }
    });', false);
?>