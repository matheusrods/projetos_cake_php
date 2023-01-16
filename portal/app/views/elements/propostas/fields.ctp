<?php if (!$aprovacao_cliente): ?>
<ul class="nav nav-tabs">
  <li class="active"><a href="#dados_proposta" data-toggle="tab">Dados da Proposta</a></li>
  <?php if (($exibe_historico_status) && (!empty($this->data['Proposta']['codigo']))): ?>  
  <li><a href="#log_status" data-toggle="tab">Log de Status</a></li>
  <?php endif; ?>
</ul>  
<?php endif; ?>
<?php echo $this->BForm->hidden('codigo'); ?>
<? if (!empty($this->data['Proposta']['codigo'])): ?>
<br/>
<div class='well'>
  <div class="row-fluid inline">
    <?php echo $this->BForm->input('numero_proposta', array('class' => 'input-small', 'label' => 'Num. Proposta', 'readonly'=>true)); ?>
    <?php echo $this->BForm->input('versao', array('class' => 'input-small', 'label' => 'Versão', 'readonly'=>true)); ?>
  </div>
</div>
<? else: ?>
<?php echo $this->BForm->hidden('numero_proposta'); ?>
<?php echo $this->BForm->hidden('versao'); ?>
<? endif; ?>
<br/>
<div class="tab-content">
  <div class="tab-pane active" id="dados_proposta">
    <?php echo $this->element('propostas/dados_empresa'); ?>
    <?php echo $this->element('propostas/origem_contato'); ?>
    <div id='divContatos'>
      <?php echo $this->element('propostas/lista_contatos', array(
        'titulo'=>'Contatos', 
        'listaContatos'=>isset($this->data['PropostaContato']) ? $this->data['PropostaContato'] : array(), 
        'index'=>'proposta_contato', 
        'model'=>'PropostaContato'
      ))?>
    </div>
    <?php echo $this->element('propostas/dados_proposta'); ?>
    <?php //echo $this->element('propostas/totais_proposta'); ?>
  </div>
  <?php if (($exibe_historico_status) && (!empty($this->data['Proposta']['codigo']))): ?>  
  <div class="tab-pane" id="log_status">
    <h4>Histórico de Status</h4>
    <?php echo $this->element('propostas/historico_status'); ?>
  </div>
  <?php endif; ?>
  <?php echo $this->Javascript->codeBlock('


    function selecionaTipoCliente(tipo_cliente) {
        var div = $("#divPropostaCPFCNPJ");
        var label = div.find("label");

        if(tipo_cliente=="'.Documento::PESSOA_FISICA.'") {
            div.show();
            label.html("CPF");
            $("#PropostaCpfCnpj").removeClass("format-cnpj");
            $("#PropostaCpfCnpj").removeClass("cnpj");
            $("#PropostaCpfCnpj").addClass("cpf");
            $("#PropostaCpfCnpj").prop("size", 14).removeAttr("maxlength").addClass("format-cpf").mask("999.999.999-99");
            $("#PropostaCpfCnpj").blur(function() {
              if (validarCPF($(this).val())) {
                $(this).removeClass("form-error").parent().removeClass("error").find("#lbl-error").remove();
              } else {
                $(this).removeClass("form-error").parent().removeClass("error").find("#lbl-error").remove();
                $(this).addClass("form-error").parent().addClass("error").append("<div id=\"lbl-error\" class=\"help-block error-message\">CPF inválido</div>");
              }
            });
        } else if(tipo_cliente=="'.Documento::PESSOA_JURIDICA.'") {
            div.show();
            label.html("CNPJ");
            $("#PropostaCpfCnpj").removeClass("cpf");
            $("#PropostaCpfCnpj").removeClass("format-cpf");
            $("#PropostaCpfCnpj").addClass("cnpj");
            $("#PropostaCpfCnpj").prop("size", 18).removeAttr("maxlength").addClass("format-cnpj").mask("99.999.999/9999-99");
            $("#PropostaCpfCnpj").blur(function() {
              if (validarCNPJ($(this).val())) {
                $(this).removeClass("form-error").parent().removeClass("error").find("#lbl-error").remove();
              } else {
                $(this).removeClass("form-error").parent().removeClass("error").find("#lbl-error").remove();                
                $(this).addClass("form-error").parent().addClass("error").append("<div id=\"lbl-error\" class=\"help-block error-message\">CNPJ inválido</div>");
              }
            });
        }

    }

    function carregaDetalhesProduto(codigo_produto) {
      var conteiner = $("#divDadosProdutos");

      var codigo_corretora = $("#PropostaCodigoCorretora").val();
      var codigo_seguradora = $("#PropostaCodigoSeguradora").val();

      if (codigo_corretora=="") codigo_corretora = "0";
      if (codigo_seguradora=="") codigo_seguradora = "0";

      $("#titProdutos").show();

      $.ajax({
        beforeSend : function(){
          bloquearDiv(conteiner);
        },
        url: baseUrl + "propostas/detalhes_produto/"+ codigo_produto +"/"+codigo_corretora +"/"+codigo_seguradora +"/"+ Math.random(),
        dataType: "html",
        success: function(data){
          jQuery(conteiner).unblock();
          if ($("#divDetalhesProduto"+codigo_produto)) $("#divDetalhesProduto"+codigo_produto).remove();
          conteiner.append(data);
        }
      });       
    }

    function habilitaDetalhesProduto(obj_produto) {
        var obj = $(obj_produto).find("input");

        var id = obj.attr("id");
        var codigo_produto = obj.attr("value");
        var checked = obj.prop("checked");

        if (checked) {
          carregaDetalhesProduto(codigo_produto);
        } else {
          $("#divDetalhesProduto"+codigo_produto).remove();
        }      
        //calcula_totais_proposta();      
    }

    function desabilitaDetalhesTodosProdutos() {
      var produtos = $(document).find(".produto");
      $("#titProdutos").hide();
      $.each($(".produto"), function(){
        var obj = $(this).find("input");
        var codigo_produto = obj.attr("value");
        $("#divDetalhesProduto"+codigo_produto).remove();
      });
      //calcula_totais_proposta();      

    }
    /*
    function calcula_totais_proposta() {
      var objTotalServicos = $("#PropostaValorTotalItens");
      var objTotalDesconto = $("#PropostaValorTotalDesconto");
      var objTotalProposta = $("#PropostaValorTotalProposta");
      var objPercDesconto = $("#PropostaPercDescontoProposta");
      
      var total_servicos = 0;
      var total_desconto = 0;
      var total_proposta = 0;

      $.each($(".selecionado"), function(){
        if ($(this).is(":checked")) {
          var objTr = $(this).parent().parent().parent();
          var vl_tabela = asFloat($(objTr).find(".valor_tabela").val());
          var vl_desc = asFloat($(objTr).find(".valor_desconto").val());
          var vl_total = asFloat($(objTr).find(".valor_final").val());

          if (!isNaN(vl_tabela)) total_servicos += vl_tabela;
          if (!isNaN(vl_desc)) total_desconto += vl_desc;
          if (!isNaN(vl_total)) total_proposta += vl_total;

        }
      });

      var perc_desconto = (total_desconto / total_servicos)*100;

      objTotalServicos.val(moeda_decimal2(total_servicos));
      objTotalDesconto.val(moeda_decimal2(total_desconto));
      objTotalProposta.val(moeda_decimal2(total_proposta));
      objPercDesconto.val(moeda_decimal2(perc_desconto));
    }
    */


    $("#PropostaTipoCliente").change(function() {
        selecionaTipoCliente(this.value);
    });

    jQuery(document).ready(function(){
       
      $(".produto").change(function() {
        habilitaDetalhesProduto(this);
      });


      $("#PropostaCodigoCorretora").change(function() {
        desabilitaDetalhesTodosProdutos();
        var produtos = $(document).find(".produto");
        $.each($(".produto"), function(){
            habilitaDetalhesProduto(this);
        });
      });

      $("#PropostaCodigoSeguradora").change(function() {
        desabilitaDetalhesTodosProdutos();
        var produtos = $(document).find(".produto");
        $.each($(".produto"), function(){
            habilitaDetalhesProduto(this);
        });
      });
      selecionaTipoCliente($("#PropostaTipoCliente").val());
      setup_mascaras();
      setup_datepicker(); 
      //calcula_totais_proposta();

    });', false); 
    ?>    
