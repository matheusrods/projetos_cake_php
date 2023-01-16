<?php if(!empty($pedidos_emitidos)):?>
<?php echo $paginator->options(array('update' => 'div.lista')); ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class = "input-small">Número Pedido</th>
            <th class = "input-xlarge">Funcionário</th>
            <th class = "input-mini">CPF</th>
            <th class = "input-large">Cliente</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pedidos_emitidos as $pedido): ?>
        <tr>
            <td>
                <?php echo $pedido['PedidoExame']['codigo'] ?>
                <a href="#void" id="expandir_<?php echo $pedido['PedidoExame']['codigo'];?>" onclick="exibe_itens(this,<?php echo $pedido['PedidoExame']['codigo'];?>);" ><i id="icone_<?php echo $pedido['PedidoExame']['codigo'];?>" class="icon-plus"></i></a>    
            </td>
            <td><?php echo $pedido['Funcionario']['nome'] ?>
                <div id="icon_carregar_<?php echo $pedido['PedidoExame']['codigo'];?>" class="inline well" style="display:none;">
                </div>
                <?php echo $this->BForm->input('carregado_'.$pedido['PedidoExame']['codigo'], array('type' => 'hidden', 'value' => '0')); ?>
            </td>
            </td>
            <td><?php echo $this->Buonny->documento($pedido['Funcionario']['cpf']); ?></td>
            <td><?php echo $this->Buonny->leiaMais($pedido['Cliente']['nome_fantasia'],50);?></td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>

<div class='row-fluid'>
	<div class='numbers span6'>
		<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
	  <?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
	</div>
	<div class='counter span6'>
		<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
	</div>
</div>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 
<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock("

    function exibe_itens(elemento,codigo_pedido) {

        //troca o icone
        if($('#icone_'+codigo_pedido).hasClass('icon-plus')) {
            $('#icone_'+codigo_pedido).removeClass('icon-plus');
            $('#icone_'+codigo_pedido).addClass('icon-minus');

            $('#icon_carregar_'+codigo_pedido).show();

        } else {
            $('#icone_'+codigo_pedido).removeClass('icon-minus');
            $('#icone_'+codigo_pedido).addClass('icon-plus');

            $('#icon_carregar_'+codigo_pedido).hide();
        }
    
        item_exibido = $('#carregado_'+codigo_pedido).val();

        //Se o conteúdo for vazio
       if (item_exibido != 1) {
            $('#icon_carregar_'+codigo_pedido).show();

            $.ajax({
                    type: 'GET',
                    url: '/portal/pedidos_exames/get_item_pedido_emitido/' + codigo_pedido,
                    dataType: 'json',
                    beforeSend: function() {
                        $('#icon_carregar_'+codigo_pedido).html('<img src=\"/portal/img/default.gif\">');
                    },
                    success: function(dados) {
                        
                        if(dados) {
                            $('icon_carregar_'+codigo_pedido).html('');
                             $('#carregado_'+codigo_pedido).val('1');
                            var detalhes = '';
                            $.each(dados, function(key, val){
                                    var valor_exame = String(val['ItemPedidoExame']['valor']).replace('.',',');
                                    var codigo_cliente_assinatura = val['ItemPedidoExame']['codigo_cliente_assinatura'];
                                    if(codigo_cliente_assinatura == null){
                                        codigo_cliente_assinatura = '';
                                    }
                                    detalhes += '<div class=\"control-group input text required \"><label >Exame</label>';
                                    detalhes += '<input name=\"\" value=\"'+val['Exame']['descricao']+'\" class=\"input-large\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                    
                                    detalhes += '<div class=\"control-group input text required\"><label >Valor</label>';
                                    detalhes += '<input name=\"\" value=\"'+valor_exame+'\" class=\"input-mini\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                    detalhes += '<div class=\"control-group input text required\"><label>Cliente assinatura</label>';
                                 
                                    detalhes += '<input name=\"\" value=\"'+codigo_cliente_assinatura+'\" class=\"input-small\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                 
                                    detalhes += '<div class=\"clear\"></div>';

                            });

                            $('#icon_carregar_'+codigo_pedido).html(detalhes);

                        } else {
                            swal({type: 'error', title: 'Houve um erro.', text: 'Houve um erro ao tentar carregar os dados do pedido!'});
                        }
                    },
                    complete: function() {
                        
                    }
                });


        }


    }
    
"); ?>