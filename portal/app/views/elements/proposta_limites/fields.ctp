<div class='well'>
  <div class="row-fluid inline">
      <?php echo $this->BForm->hidden('codigo'); ?>
      <?php echo $this->BForm->input("codigo_produto", array('class'=>'input-large','options' => $produtos,'label' => 'Produto','empty'=>'Produto','required'=>true)) ?>
  </div>
  <div class="row-fluid inline">
      <?php echo $this->BForm->input("codigo_servico", array('class'=>'input-large','options' => $servicos,'label' => 'Serviço','empty'=>'Serviço','required'=>true)) ?>
  </div>
  <div class="row-fluid inline">
      <?php echo $this->BForm->input("perc_limite_desconto_gerencia", Array('class'=>'input-mini moeda_com_decimal perc_desconto numeric', 'label'=>'% Limite Desconto Nível 1','maxLength'=>6,'required'=>true))?>
      <?php echo $this->BForm->input("perc_limite_desconto", Array('class'=>'input-mini moeda_com_decimal perc_desconto numeric', 'label'=>'% Limite Desconto Nível 2','maxLength'=>6,'required'=>true))?>
  </div>
  <?php if(!empty($this->data['PropostaLimiteDesconto']['codigo'])): ?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input("data_inclusao", Array('type'=>'text','class'=>'input-medium', 'label'=>'Dt. Inclusão', 'readonly'=>true))?>
        <?php echo $this->BForm->input("data_alteracao", Array('type'=>'text','class'=>'input-medium', 'label'=>'Dt. Ult. Alteração', 'readonly'=>true))?>
    </div>
  <?php endif; ?>
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>    
    
  <?php echo $this->Javascript->codeBlock('
    function msg_invalido(objeto, mensagem) {
        var div1 = "<div id=\"msg-desconto-invalido\" style=\"color:#b94a48\" class=\"help-block error-message\">"+mensagem+"</div>"; 
        var div2 = document.createElement("div");
        $(objeto).after(div1, div2); 
    }

    function valida_desconto(obj) {

      var obj_msg = $(obj).parent().find(".error-message");
      if (obj_msg) obj_msg.remove();
      var pct_desconto = asFloat($(obj).val());
      
      if (isNaN(pct_desconto)) {
        msg_invalido(obj,"Valor inválido");
      }

      if (pct_desconto>100) {
        msg_invalido(obj,"Desconto não pode ser maior que 100%");
        $(obj).val("0,00");
        return false;
      }

      return true;
    }

    jQuery(document).ready(function(){
       
        jQuery("#PropostaLimiteDescontoCodigoProduto").change(function(){
            var codigo_produto = $(this).val();
            lista_servicos_produto("PropostaLimiteDescontoCodigoServico",codigo_produto);
        }); 

        $(document).on("blur",".perc_desconto",function(){
          valida_desconto(this);
        });

        setup_mascaras();

    });', false); 
    ?>    
