<?php echo $this->BForm->input('codigo_cliente', array('class' => 'small', 'value' => $codigo_cliente, 'type' => 'hidden')) ?>
<div class = 'form-procurar'>
	<?= $this->element('/filtros/grupos_exames') ?>
</div>
<div class='actionbar-right'>
	<a href="javascript:void(0);" onclick="manipula_modal('modal_exames_assinatura', 1);" class="btn btn-success"><i class="icon-plus icon-white"></i> Incluir Exame no Grupo</a>
</div>
<div class='lista'></div>

<?php 
    echo $this->Javascript->codeBlock(" 
        setup_mascaras();

        function manipula_modal(id, mostra) {
			if(mostra) {
				$('.modal').css('z-index', '-1');

				$('#' + id).css('z-index', '1050');
				$('#' + id).modal('show');
			} else {
				$('#' + id).css('z-index', '-1');
				$('#' + id).modal('hide');
			}
		}

		function adicionaExamesGrupo(codigo_grupo_economico) {

			var exames = '';
			$('.checkbox_exames').each(function(i, element_exames_disponiveis) {
				if(element_exames_disponiveis.checked) {
					exames = exames + $(element_exames_disponiveis).val() + ',';
				}
			});

			if(exames.length) {
				codigos_exames = exames.substring(0,(exames.length - 1));
			}

			$.ajax({
				type: 'POST',
				url: '/portal/grupos_exames/lista_exames_grupo/".$codigo_detalhe_grupo_exame."',
				dataType: 'json',
				data: 'codigo_grupo_economico=".$dados_grupo[0]['DetalheGrupoExame']['codigo_grupo_economico']."&exames=' + codigos_exames,
				beforeSend: function() {
					manipula_modal('modal_exames_assinatura', 0);
					manipula_modal('modal_carregando', 1);
				},
				complete: function() {
					manipula_modal('modal_carregando', 0);
                    var div = jQuery('.lista');
                    bloquearDiv(div);
                    div.load(baseUrl + '/grupos_exames/listagem/".$dados_grupo[0]["DetalheGrupoExame"]["codigo"]."/' + Math.random());
                    $('.checkbox_exames').each(function(i, element_exames_disponiveis) {
                        $(element_exames_disponiveis).prop('checked', false);
                    });
				}
			});
		}

    ");
?>

<div class="modal fade" id="modal_exames_assinatura">
    <div class="modal-dialog modal-lg" style="position: static;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="gridSystemModalLabel">Serviços Disponíveis na Assinatura:</h4>
                <div class="clear"></div>
            </div>
            <div class="modal-body" style="height: 600px; overflow: scroll;">
                <?php if(count($produtos_servicos)) : ?>
                    <table style="width: 100%" class="table-striped">
                        <?php foreach($produtos_servicos as $key_produto => $produto) : ?>
                            <tr>
                                <td style="background: #CCC; text-align: center;" colspan="3">
                                    <b><?php echo $produto['Produto']['descricao']; ?></b>
                                </td>                           
                            </tr>
                            <?php foreach($produto['ClienteProdutoServico2'] as $key_servico => $servico) : ?>
                                <tr>
                                    <td style="width: 110px; text-align: center;">
                                        <?php if(isset($servico['Servico']['cadastrado']) && ($servico['Servico']['cadastrado'] == 'nao')) : ?>
                                            <span style="font-size: 9px; color: red;">(não cadastrado)</span>
                                        <?php else : ?>
                                            <input class="checkbox_exames" type="checkbox" value="<?php echo $servico['codigo_servico']; ?>" name="tabela.<?php echo $key_servico; ?>.exame">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo utf8_encode(strtoupper($servico['Servico']['descricao'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>                
                        <?php endforeach; ?>
                    </table>
                    <div class='actionbar-right'>
						<label><a href="javascript:void(0);" onclick="adicionaExamesGrupo(<?php echo $this->passedArgs[0]; ?>);" class="btn btn-success btn-sm right" title="Incluir">Incluir no Grupo!</a></label>
					</div>        
                <?php else : ?>
                    <div class="alert alert-danger">Este cliente não possui assinatura de serviços.</div>
                    <div class='actionbar-right'>
                        <label><a href="javascript:void(0);" onclick="manipula_modal('modal_exames_assinatura', 0);" class="btn btn-danger btn-sm right" title="Incluir">Fechar</a></label>
                    </div>  
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>