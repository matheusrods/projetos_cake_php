<div class="row-fluid inline text-right control-group">
	
				<?php echo $this->BForm->input('codigo_glosa', array('type' => 'hidden', 'value' => '')) ?>
				<?php echo $this->BForm->input('fornecedor', array('type' => 'hidden', 'value' => $codigo_fornecedor)) ?>
				<?php echo $this->BForm->input('codigo_nota', array('type' => 'hidden', 'value' => $codigo_nota)) ?>

		<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir glosa', 'onclick' => "glosas('{$codigo_fornecedor}', '{$codigo_nota}')")); ?>
</div>
    <?php if(!empty($dados_glosas)):?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="input-mini">Glosa</th>
                    <th>Pedido Exame</th>
                    <th>Exame</th>
                    <th>Valor</th>
                    <th>Data da Glosa</th>
                    <th>Data de Vencimento</th>
                    <th>Data de Pagamento</th>
                    <th>Status</th>
                    <th>Motivo da Glosa</th>
                    <th class="acoes" style="width:52px">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dados_glosas as $dados): ?>
                    <tr>
                        <td class="input-mini"><?php echo $dados['Glosas']['codigo'] ?></td>
                        <td><?php echo $dados['Glosas']['codigo_pedidos_exames'] ?></td>
                        <td><?php echo $dados['Exame']['descricao'] ?></td>
                        <td class="input-mini"><?php echo $this->Buonny->moeda($dados['Glosas']['valor']) ?></td>
                        <td><?php echo $dados['Glosas']['data_glosa'] ?></td>
                        <td><?php echo $dados['Glosas']['data_vencimento'] ?></td>
                        <td><?php echo $dados['Glosas']['data_pagamento'] ?></td>
                        <td><?php echo $dados['GlosasStatus']['descricao'] ?>
                        </td>
                        <td><?php echo $dados['Glosas']['motivo_glosa'] ?></td>
                        <td style="width:50px;">

                            <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status',' onclick' => "atualizaStatusGlosas('{$dados['Glosas']['codigo']}','{$dados['Glosas']['ativo']}')"));?>
                            <?php if($dados['Glosas']['ativo'] == 0): ?>
                                <span class="badge-empty badge badge-important" title="Desativado"></span>
                            <?php elseif($dados['Glosas']['ativo'] == 1): ?>
                                <span class="badge-empty badge badge-success" title="Ativo"></span>
                            <?php endif; ?> 

                            <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-edit ', 'title' => 'Editar', 'escape' => false,' onclick' => "editar_glosas('{$dados['Glosas']['codigo']}')")); ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan = "15"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Glosas']['count']; ?></td>
                </tr>
            </tfoot>    
        </table>
    <?php else:?>
        <div class="alert">Nenhum dado foi encontrado.</div>
    <?php endif;?>

<div class="modal fade" id="modal_glosas" data-backdrop="static" style="width: 60%; left: 19%; top: 15%; margin: 0 auto;"></div>

<div class='form-actions well'>
    	<?php echo $html->link('Voltar', array('controller' => 'notas_fiscais_servico', 'action' => 'index'), array('class' => 'btn btn-default')); ?>
	</div>

<?php echo $this->Javascript->codeBlock("
	glosas = function(codigo_fornecedor, codigo_nota){
        var div = jQuery('div#modal_glosas');
        bloquearDiv(div);

        div.load(baseUrl + 'notas_fiscais_servico/modal_glosas/' + codigo_fornecedor + '/' + codigo_nota + '/' + Math.random(),);           

        $('#modal_glosas').css('z-index', '1050');
        $('#modal_glosas').modal('show');
    }
    ");
?>

<script type="text/javascript">
$(document).ready(function() {

    atualizaStatusGlosas = function(codigo, status){

        var div = jQuery('#tableGlosas');
        bloquearDiv(div);

        $.ajax({
            type: 'POST',
            url: baseUrl + 'notas_fiscais_servico/atualiza_status_glosas/' + codigo + '/' + status + '/' + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($('modal_data'));  
            },
            success: function(data){
                if(data == 1){
                    atualizaListaModalGlosas();
                    $('#modal_data').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else{
                    atualizaListaModalGlosas();
                    $('#modal_data').unblock();
                    viewMensagem(0,'Não foi possível mudar o status!');
               }

               desbloquearDiv(div);
            },
            error: function(erro){
                $('#modal_data').unblock();
                viewMensagem(0,'Não foi possível mudar o status!');
                desbloquearDiv(div);
                }
        });
    }

    editar_glosas = function(codigo_glosa){

        var div = jQuery('#tableGlosas');
        bloquearDiv(div);

        //envia via ajax os dados glosas
        $.ajax({
            url: baseUrl + 'notas_fiscais_servico/editar_dados_glosas',
            type: 'POST',
            dataType: 'json',
            data: {
                "codigo_glosa": codigo_glosa
            }
        })      
        .done(function(data) {

            // console.log(data);
            // return false;

            if(data) {

                //seta os valores nos campos
                $("#NotaFiscalServicoCodigoGlosa").val(codigo_glosa);
                $("#NotaFiscalServicoCodigoItensPedidosExames").val(data.Glosas.codigo_itens_pedidos_exames);
                $("#NotaFiscalServicoValor").val(data.Glosas.valor);
                $("#NotaFiscalServicoDataGlosa").val(data.Glosas.data_glosa);
                $("#NotaFiscalServicoDataVencimento").val(data.Glosas.data_vencimento);
                $("#NotaFiscalServicoDataPagamento").val(data.Glosas.data_pagamento);
                $("#NotaFiscalServicoCodigoStatusGlosa").val(data.Glosas.codigo_status_glosa);
                $("#NotaFiscalServicoMotivoGlosa").val(data.Glosas.motivo_glosa);

            }

            desbloquearDiv(div);

        });
    }
});
</script>
  