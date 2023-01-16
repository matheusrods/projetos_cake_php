<div class='well'>
	<?php echo  $this->BForm->create('ItemPedidoNaoIntegrados',array('url' => array('controller' => 'itens_pedidos','action' =>'pedidos_nao_integrados')));?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('mes_faturamento', array('options' => $mes_nao_integrados, 'class' => 'input-medium', 'label' => false)); ?>
        <?php echo $this->BForm->input('ano_faturamento', array('label' => false, 'placeholder' => 'Ano','class' => 'input-mini numeric just-number', 'title' => 'Ano de Faturamento')) ?>
	</div>
	<div class="row-fluid inline">
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		<?php echo $html->link('Limpar', 'pedidos_nao_integrados', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	</div>
	<?php if(isset($lista_nao_integrados) && $lista_nao_integrados ): ?>
	<div class="row-fluid inline">	
		<span class='pull-right'>
			<?php echo $this->BForm->submit('Integrar', array('div' => false, 'class' => 'btn btn-primary', 'name' => 'data[Submit][type]')); ?>
		</span>
	</div>
	<?endif;?>
</div>
<?php if(isset($lista_nao_integrados) && $lista_nao_integrados ){ ?>
<div class="row-fluid inline">
	<span class='pull-right'>
		<?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("checkboxes")')) ?>
		<?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("checkboxes")')) ?>
	</span>
	</div>
 	<table class="table table-striped table-bordered">
        <thead>
            <tr>
            	<th class="input-mini" ></th>
                <th class="input-xxlarge"> Cliente </th>
                <th class="input-medium numeric"> Valor Total</th>
                <th class="input-small numeric"> Mês</th>
                <th class="input-small numeric"> Ano</th>
            </tr>
        </thead>	        
        <tbody>
            <?php $total = 0;?>
            <?php foreach($lista_nao_integrados as $key => $lista):?>
            	<?php $total++;?>
        	<tr>
        		<td>
        			<div id='checkboxes'>
        				<input type="checkbox" name="codigo_pedido[]" value="<?php echo $lista['ItemPedido']['codigo_pedido']; ?>"/>
        			</div>
        		</td>
                <td width="70%">
                    <?php echo $lista['Cliente']['codigo'] ." - ". $lista['Cliente']['razao_social'] ?>
                    <a href="#void" id="expandir_<?php echo $lista['ItemPedido']['codigo_pedido'];?>" onclick="mostrar_itens(this,<?php echo $lista['ItemPedido']['codigo_pedido'];?>);" ><i id="icone_<?php echo $lista['ItemPedido']['codigo_pedido'];?>" class="icon-plus"></i></a>

                    <div id="icon_carregar_<?php echo $lista['ItemPedido']['codigo_pedido'];?>" class="inline well" style="display:none;"></div>

                    <?php echo $this->BForm->input('carregado_'.$lista['ItemPedido']['codigo_pedido'], array('type' => 'hidden', 'value' => '0')); ?>
                </td>
                <td class="numeric"><?php echo $this->Buonny->moeda($lista[0]['valor_pedido']); ?></td>
                <td class="numeric"><?php echo $this->Buonny->moeda($lista['Pedido']['mes_referencia'], array('nozero' => true, 'places' => 0)); ?></td>
                <td class="numeric"><?php echo $lista['Pedido']['ano_referencia']; ?></td>
			</tr>
	        <?php endforeach; ?>
        <tfoot>
        	<tr >
        		<td colspan="6">
        			<strong>Total <?php echo $total ?></strong>
    			</td>
        	</tr>
        </tfoot>
    </table>
<?} else {
	if( isset($filtrado)):?>
    	<div class="alert">Nenhum registro encontrado</div>
	<?endif;?>
<?}?>

<?echo $this->BForm->end()?>

<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){
        setup_mascaras();

        mostrar_itens = function(elemento,codigo_pedido) {

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

            carregamento = $('#ItemPedidoNaoIntegradosCarregado'+codigo_pedido).val();
            if(carregamento != 1) {

                $('#icon_carregar_'+codigo_pedido).show();

                $.ajax({
                    type: 'GET',
                    url: '/portal/itens_pedidos/get_pedido_produto_servico/' + codigo_pedido,
                    dataType: 'json',
                    beforeSend: function() {
                        $('#icon_carregar_'+codigo_pedido).html('<img src=\"/portal/img/default.gif\">');
                    },
                    success: function(dados) {
                        
                        if(dados) {
                            $('icon_carregar_'+codigo_pedido).html('');
                            $('#ItemPedidoNaoIntegradosCarregado'+codigo_pedido).val('1');

                            var detalhes = '';
                            $.each(dados, function(key, val){
                                $.each(val, function(){

                                    if(key == 0) {
                                        detalhes += '<div class=\"control-group input text required \"><label >Produto:</label>';
                                    } else {
                                        detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                    }
                                    detalhes += '<input name=\"\" value=\"'+this.produto_codigo+'-'+this.produto+'\" class=\"input-large\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                    
                                    if(key == 0) {
                                        detalhes += '<div class=\"control-group input text required\"><label >Serviço:</label>';
                                    } else {
                                        detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                    }
                                    detalhes += '<input name=\"\" value=\"'+this.servico_codigo+'-'+this.servico+'\" class=\"input-large\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                    if(key == 0) {
                                        detalhes += '<div class=\"control-group input text required\"><label >Quantidade:</label>';
                                    } else {
                                        detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                    }
                                    detalhes += '<input name=\"\" value=\"'+this.quantidade+'\" class=\"input-small\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                    if(key == 0) {
                                        detalhes += '<div class=\"control-group input text required\"><label >Valor:</label>';
                                    } else {
                                        detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                    }
                                    detalhes += '<input name=\"\" value=\"'+this.valor+'\" class=\"input-small\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                    detalhes += '<div class=\"clear\"></div>';
                                });
                            });

                            $('#icon_carregar_'+codigo_pedido).html(detalhes);

                        } else {
                            swal({type: 'error', title: 'Houve um erro.', text: 'Houve um erro ao tentar carregar os dados do pedido!'});
                        }
                    },
                    complete: function() {
                        
                    }
                });
            }//fim if

        }//fim mostrar_itens

    });", false);
?>