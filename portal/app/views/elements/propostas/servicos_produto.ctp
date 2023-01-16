	<div class="row-fluid inline">
    <?=$this->BForm->input("PropostaProdutoServico.{$codigo_produto}.servicos_selecionados",Array('style'=>'display: none','label'=>'<h6>Valores da Proposta</h6>'))?>
		<table width="100%" class="table table-striped">
			<thead>
			<tr>
				<th width="18">&nbsp;</th>
				<th>Serviço</th>
        <?php if (!$aprovacao_cliente): ?>
				<th class="numeric input-small">Vl. Tabela</th>
				<th class="numeric input-small">% Desconto</th>
				<th class="numeric input-small">Vl. Desconto</th>
        <th class="numeric input-small">Vl. Final</th>
        <?php else: ?>
        <th class="numeric input-small">Valor</th>
        <?php endif; ?>
			</tr>
			</thead>
			<tbody>
			<? foreach ($servicos[$codigo_produto] as $key => $dados_servico): ?>
        <? $codigo_servico = $dados_servico['Servico']['codigo'] ?>
				<tr>
          <?php if (!$aprovacao_cliente): ?>
					<td>
						<?=$this->BForm->input("PropostaProdutoServico.{$codigo_produto}.{$codigo_servico}.selecionado",Array('type'=>'checkbox','value'=>'S','label'=>false,'class'=>'selecionado', 'disabled'=>$readonly))?>
					</td>
					<td>
						<?=$this->BForm->input("PropostaProdutoServico.{$codigo_produto}.{$codigo_servico}.codigo_servico",Array('type'=>'hidden'))?>
						<?=$dados_servico['Servico']['descricao']?>
					</td>
					<td class='numeric'>
						<?=$this->BForm->input("PropostaProdutoServico.{$codigo_produto}.{$codigo_servico}.valor_tabela", Array('class'=>'input-small moeda_com_decimal valor_tabela numeric', 'label'=>false,'div'=>Array('style'=>'float: right; margin:0px;'), 'readonly'=>true))?>
					</td>
					<td class='numeric'>
            <?=$this->BForm->input("PropostaProdutoServico.{$codigo_produto}.{$codigo_servico}.perc_limite_desconto_gerencia",Array('class'=>'perc_limite_ger','style'=>'display: none','label'=>false,'div'=>Array('style'=>'display: none;')))?>
            <?=$this->BForm->input("PropostaProdutoServico.{$codigo_produto}.{$codigo_servico}.perc_limite_desconto",Array('class'=>'perc_limite','style'=>'display: none','label'=>false,'div'=>Array('style'=>'display: none;')))?>
						<?=$this->BForm->input("PropostaProdutoServico.{$codigo_produto}.{$codigo_servico}.perc_desconto", Array('class'=>'input-mini moeda_com_decimal perc_desconto numeric','style'=>((!$readonly) || ($aprovacao_interna) ? 'color: '.$this->data['PropostaProdutoServico'][$codigo_produto][$codigo_servico]['color'].';' : ''), 'label'=>false,'div'=>Array('style'=>'float: right; margin:0px;'), 'readonly'=>($this->data['PropostaProdutoServico'][$codigo_produto][$codigo_servico]['selecionado']!='S'?true:$readonly),'maxLength'=>6))?>
					</td>
					<td class='numeric'>
						<?=$this->BForm->input("PropostaProdutoServico.{$codigo_produto}.{$codigo_servico}.valor_desconto", Array('class'=>'input-small moeda_com_decimal valor_desconto numeric', 'label'=>false,'div'=>Array('style'=>'float: right; margin:0px;'), 'readonly'=>true))?>
					</td>
					<td class='numeric'>
						<?=$this->BForm->input("PropostaProdutoServico.{$codigo_produto}.{$codigo_servico}.valor_final", Array('class'=>'input-small moeda_com_decimal valor_final numeric', 'label'=>false,'div'=>Array('style'=>'float: right; margin:0px;'), 'readonly'=>true))?>
					</td>
          <?php else: ?>
          <td>
            <?=$this->BForm->input("PropostaProdutoServico.{$codigo_produto}.{$codigo_servico}.selecionado",Array('type'=>'checkbox','value'=>'S','label'=>false,'class'=>'selecionado', 'disabled'=>$readonly))?>
          </td>
          <td>
            <?=$this->BForm->input("PropostaProdutoServico.{$codigo_produto}.{$codigo_servico}.codigo_servico",Array('type'=>'hidden'))?>
            <?=$dados_servico['Servico']['descricao']?>
            <?=$this->BForm->hidden("PropostaProdutoServico.{$codigo_produto}.{$codigo_servico}.valor_tabela", Array('class'=>'input-small moeda_com_decimal valor_tabela numeric', 'label'=>false,'div'=>Array('style'=>'float: right; margin:0px;'), 'readonly'=>true))?>
            <?=$this->BForm->hidden("PropostaProdutoServico.{$codigo_produto}.{$codigo_servico}.perc_limite_desconto_gerencia",Array('class'=>'perc_limite_ger','style'=>'display: none','label'=>false,'div'=>Array('style'=>'display: none;')))?>
            <?=$this->BForm->hidden("PropostaProdutoServico.{$codigo_produto}.{$codigo_servico}.perc_limite_desconto",Array('class'=>'perc_limite','style'=>'display: none','label'=>false,'div'=>Array('style'=>'display: none;')))?>
            <?=$this->BForm->hidden("PropostaProdutoServico.{$codigo_produto}.{$codigo_servico}.perc_desconto", Array('class'=>'input-mini moeda_com_decimal perc_desconto numeric','style'=>((!$readonly) || ($aprovacao_interna) ? 'color: '.$this->data['PropostaProdutoServico'][$codigo_produto][$codigo_servico]['color'].';' : ''), 'label'=>false,'div'=>Array('style'=>'float: right; margin:0px;'), 'readonly'=>($this->data['PropostaProdutoServico'][$codigo_produto][$codigo_servico]['selecionado']!='S'?true:$readonly),'maxLength'=>6))?>
            <?=$this->BForm->hidden("PropostaProdutoServico.{$codigo_produto}.{$codigo_servico}.valor_desconto", Array('class'=>'input-small moeda_com_decimal valor_desconto numeric', 'label'=>false,'div'=>Array('style'=>'float: right; margin:0px;'), 'readonly'=>true))?>
          </td>
          <td class='numeric'>
            <?=$this->BForm->input("PropostaProdutoServico.{$codigo_produto}.{$codigo_servico}.valor_final", Array('class'=>'input-small moeda_com_decimal valor_final numeric', 'label'=>false,'div'=>Array('style'=>'float: right; margin:0px;'), 'readonly'=>true))?>
          </td>
          <?php endif; ?>
				</tr>
			<? endforeach;?>
			<tbody>
		</table>
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

  	function calcula_desconto(obj) {

  		var objLine = $(obj).parent().parent().parent();
  		var pct_desconto = asFloat($(obj).val());

  		var vl_tabela = asFloat($(objLine).find(".valor_tabela").val());
  		var obj_vl_desconto = $(objLine).find(".valor_desconto");

  		var vl_desconto = vl_tabela * (pct_desconto/100);
  		obj_vl_desconto.val(moeda2(vl_desconto));

  		var obj_vl_final = $(objLine).find(".valor_final");
      var obj_selecionado = $(objLine).find(".selecionado");

      if (obj_selecionado.is(":checked")) {
        obj_vl_final.val(moeda2(vl_tabela - vl_desconto));  
      } else {
        obj_vl_final.val(moeda2(0));  
      }
      var pct_limite = $(objLine).find(".perc_limite_ger").val();
      if (pct_desconto>pct_limite) {
        $(obj).css("color","red");
      } else {
        $(obj).css("color","#555");
      }


      //calcula_totais_proposta();  		
  	}

    jQuery(document).ready(function(){
       
      $(".selecionado").click(function() {
      	var objPercDesconto = $(this).parent().parent().parent().find(".perc_desconto");
        objPercDesconto.attr("readonly",(!this.checked) );
        calcula_desconto(objPercDesconto);
      });

  	  $(document).on("blur",".perc_desconto",function(){
        valida_desconto(this);
    		calcula_desconto(this);
  	  });

      setup_mascaras();

    });', false); 
  ?>    
