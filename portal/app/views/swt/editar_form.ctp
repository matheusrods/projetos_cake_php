<?php echo $this->BForm->create('PosSwtForm', array('url' => array('controller' => 'swt','action' => 'editar_form'))); ?>
    
    <div class='well'>	
		<?php
    	echo $this->BForm->hidden('codigo_cliente', array('value' => $this->data['PosSwtForm']['codigo_cliente']));
		echo $this->BForm->hidden('codigo', array('value' => $codigo) );
		?>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('form_tipo', array('label' => 'Tipo (*)', 'class' => 'input', 'options' => $form_tipo,'disabled'=>'disabled')); ?>
			
			<div class="pull-right">
				<a id="cad_titulo" href="javascript:void(0);" class="btn btn-primary" style="color: #fff;" onclick="manipula_modal('modal_titulo', 1);" >Cadastrar Título</a>
				<a id="cad_questao" href="javascript:void(0);" class="btn btn-primary" style="color: #fff;" onclick="manipula_modal('modal_questao', 1);" >Cadastrar Questão</a>
			</div>
		</div>
		<div class="modal fade hide" id="modal_titulo" data-backdrop="static" style="width: 57%; left: 23%; top: 11%; margin: 0 auto;">
			<div class="modal-dialog modal-md" style="position: static;">
				<div class="modal-content">
					<div class="modal-header" style="text-align: center;">
						<h4>Título</h4>
					</div>
					<div class="modal-body" style="min-height: 100px;">
						<div class="well">
                            <?php echo $this->BForm->hidden('PosSwtTitulo.codigo', array('value' => '') ); ?>
                            <?php echo $this->BForm->input('PosSwtTitulo.ordem', array('label' => 'Ordem', 'class' => 'input-small')); ?>
                            <?php echo $this->BForm->input('PosSwtTitulo.titulo', array('label' => 'Título', 'class' => 'input-xxlarge')); ?>
                            <label id="mensagem_motivo_titulo" style="color: red; font-weight: bold; display: none; margin: -15px 0 20px 0;">
                                Para salvar você precisa de indicar um título.;
                            </label>
                        </div>
					</div>
					<div class="modal-footer">
						<a href="javascript:void(0);" onclick="confirma_titulo(this, '<?php echo $codigo; ?>','<?php echo $this->data['PosSwtForm']['codigo_cliente']; ?>');" class="btn btn-success">Confirmar</a>
						<a href="javascript:void(0);" onclick="manipula_modal('modal_titulo', 0);" class="btn btn-default">Cancelar</a>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade hide" id="modal_questao" data-backdrop="static" style="width: 57%; left: 23%; top: 11%; margin: 0 auto;">
			<div class="modal-dialog modal-md" style="position: static;">
				<div class="modal-content">
					<div class="modal-header" style="text-align: center;">
						<h4>Questão</h4>
					</div>
					<div class="modal-body" style="min-height: 150px;">

                        <div class="well">
                            <?php echo $this->BForm->hidden('PosSwtFormQuestao.codigo', array('value' => '') ); ?>
                            <div class='row-fluid inline'>
                                <?php echo $this->BForm->input('PosSwtFormTitulo.titulo', array('label' => 'Título da Questão (*)', 'class' => 'input', 'empty' => 'Selecione', 'options' => $titulo)); ?>
                                <label id="mensagem_motivo_questao_titulo" style="color: red; font-weight: bold; display: none; margin: -15px 0 20px 0;">
                                    Para salvar você precisa de indicar um título.;
                                </label>
                            </div>

                            <div class='row-fluid inline'>
                                <?php echo $this->BForm->input('PosSwtFormQuestao.ordem', array('label' => 'Ordem Questão', 'class' => 'input-small')); ?>
                            </div>

                            <div class='row-fluid inline'>
                                <?php echo $this->BForm->input('PosSwtFormQuestao.questao', array('label' => 'Questão', 'class' => 'input-xxlarge')); ?>
                                <label id="mensagem_motivo_questao" style="color: red; font-weight: bold; display: none; margin: -15px 0 20px 0;">
                                    Para salvar você precisa de indicar um descritivo para a questão.;
                                </label>
                            </div>

                            <div class='row-fluid inline'>
                                <?php echo $this->BForm->input('PosSwtFormQuestao.saiba_mais', array( 'type' => "textarea", 'label' => 'Saiba mais', 'rows' => 3, 'class' => 'input-xxlarge', "style" => "margin: 0px 0px 10px; width: 634px;")); ?>
                            </div>
                        </div>
					</div>
					<div class="modal-footer">
						<a href="javascript:void(0);" onclick="confirma_questao(this, '<?php echo $codigo; ?>','<?php echo $this->data['PosSwtForm']['codigo_cliente']; ?>');" class="btn btn-success">Confirmar</a>
						<a href="javascript:void(0);" onclick="manipula_modal('modal_questao', 0);" class="btn btn-default">Cancelar</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="lista_questoes"></div>

    <div class='form-actions'>
        <?= $html->link('Voltar', array('controller' => 'swt', 'action' => 'index_form'), array('class' => 'btn')); ?>
    </div>

	<script type="text/javascript">
	jQuery(document).ready(function() {

		setup_mascaras(); 
		setup_datepicker(); 
		setup_time();

		confirma_titulo = function(elemento, codigo_form, codigo_cliente) {
			
			var element_origin = $(elemento).html();
			var ordem = $("#PosSwtTituloOrdem").val();
			var titulo = $("#PosSwtTituloTitulo").val();
			var codigo_form_titulo = $("#PosSwtTituloCodigo").val();

			if(titulo != "") {
				$.ajax({
			        type: "POST",
			        url: "/portal/swt/confirma_titulo",
			        dataType: "json",
			        data: "codigo_cliente="+codigo_cliente+"&codigo_form=" + codigo_form + "&titulo=" + titulo + "&ordem=" + ordem+"&codigo_form_titulo="+codigo_form_titulo,
			        beforeSend: function() {
						$(elemento).html("<img src=\"/portal/img/default.gif\">");
					},
			        success: function(data) {
						if(data) {
							
							//monta o combo novamente
							$('#PosSwtFormTituloTitulo').html("");
							var options = '<option value="">Selecione</option>';
							$.each(data, function (key, item) {
						        options += '<option value="' + key + '">' + item + '</option>';
						    });
				            $('#PosSwtFormTituloTitulo').html(options);

				            manipula_modal("modal_titulo", 0);

				            atualizaListaQuestoes();
				            
						} else {
							manipula_modal("modal_titulo", 0);
							swal({type: "error", title: "Houve um erro.", text: "Houve um erro ao tentar salvar um novo título!"});
						}
			        },
			        complete: function() {
						$(elemento).html("Salvar");
					}
			    });				
			} 
			else {
				$("#mensagem_motivo_titulo").show();
			}
			
		}


		confirma_questao = function(elemento, codigo_form, codigo_cliente) {
			
			var element_origin = $(elemento).html();
			var codigo_titulo = $("#PosSwtFormTituloTitulo").val();
			var ordem = $("#PosSwtFormQuestaoOrdem").val();
			var questao = $("#PosSwtFormQuestaoQuestao").val();
            var saiba_mais = $("#PosSwtFormQuestaoSaibaMais").val();
			var aux_erro = false;		

			if(questao == "") {
				$("#mensagem_motivo_questao").show();
				aux_erro = true;
			}

			if(codigo_titulo == "") {
				$("#mensagem_motivo_questao_titulo").show();
				aux_erro = true;
			}			
			
			if(aux_erro) return false;

			var codigo_form_questao = $("#PosSwtFormQuestaoCodigo").val();

			var form = {
				codigo_cliente: codigo_cliente,
				codigo_form: codigo_form,
				codigo_titulo : $("#PosSwtFormTituloTitulo").val(),
				questao : $("#PosSwtFormQuestaoQuestao").val(),
				ordem: $("#PosSwtFormQuestaoOrdem").val(), 
				codigo_form_questao: codigo_form_questao,
				saiba_mais: saiba_mais, 
			};

			$.ajax({
		        type: "POST",
		        url: "/portal/swt/confirma_questao",
		        dataType: "json",
		        data: form, //"codigo_cliente="+codigo_cliente+"&codigo_form=" + codigo_form + "&codigo_titulo=" + codigo_titulo + "&questao=" + questao + "&ordem=" + ordem+"&codigo_form_questao="+codigo_form_questao + "&saiba_mais=" + saiba_mais,
		        beforeSend: function() {
					$(elemento).html("<img src=\"/portal/img/default.gif\">");
				},
		        success: function(data) {
					if(data) {
			            
						manipula_modal("modal_questao", 0);

						atualizaListaQuestoes();

					} else {
						manipula_modal("modal_questao", 0);
						swal({type: "error", title: "Houve um erro.", text: "Houve um erro ao tentar salvar um nova questão!"});
					}
		        },
		        complete: function() {
					$(elemento).html("Salvar");
				}
		    });
			
		}

		atualizaListaQuestoes();

		//edicao dos dados da listagem
		editar_lista_titulo = function(codigo_titulo) {
			// console.log('titulo:' + codigo_titulo);
			
			//pega os campos para popular a modal
			var ordem = $("#PosSwtFormTituloOrdem"+codigo_titulo).val();
			var titulo = $("#PosSwtFormTituloTitulo"+codigo_titulo).val();

			//seta o valor na modal
			$("#PosSwtTituloCodigo").val(codigo_titulo);
			$("#PosSwtTituloOrdem").val(ordem);
			$("#PosSwtTituloTitulo").val(titulo);

			manipula_modal("modal_titulo", 1);

		}

		editar_lista_questao = function(codigo_questao) {
			// console.log('questao: ' + codigo_questao);
			
			//pega os campos para popular a modal
			var codigo_titulo = $("#PosSwtFormQuestaoCodigoFormTitulo"+codigo_questao).val();
			var ordem = $("#PosSwtFormQuestaoOrdem"+codigo_questao).val();
			var questao = $("#PosSwtFormQuestaoQuestao"+codigo_questao).val();
            var saiba_mais = $("#PosSwtFormQuestaoSaibaMais"+codigo_questao).val();

            console.log(saiba_mais);
			//seta o valor na modal
			$("#PosSwtFormQuestaoCodigo").val(codigo_questao);
			$("#PosSwtFormTituloTitulo").val(codigo_titulo);
			$("#PosSwtFormQuestaoOrdem").val(ordem);
			$("#PosSwtFormQuestaoQuestao").val(questao);
            $("#PosSwtFormQuestaoSaibaMais").val(saiba_mais);

			manipula_modal("modal_questao", 1);
		}

	});

	function manipula_modal(id, mostra) {
		if(mostra) {
			$("#" + id).css("z-index", "1050");
			$("#" + id).modal("show");
		} else {

			//limpa modal de titulo
			$("#PosSwtTituloCodigo").val('');
			$("#PosSwtTituloOrdem").val('');
			$("#PosSwtTituloTitulo").val('');
			//limpa modal de questoes
			$("#PosSwtFormQuestaoCodigo").val('');
			$("#PosSwtFormTituloTitulo").val('');
			$("#PosSwtFormQuestaoOrdem").val('');
			$("#PosSwtFormQuestaoQuestao").val('');
            $("#PosSwtFormQuestaoSaibaMais").val('');

			$("#" + id).css("z-index", "-1");
			$("#" + id).modal("hide");
		}
	}

	function atualizaListaQuestoes(){
        var div = jQuery("div.lista_questoes");
        bloquearDiv(div);
        div.load(baseUrl + "swt/listagem_form_questao/" + <?php echo $codigo; ?> + "/"+ Math.random());
    }

	</script>

<?php echo $this->BForm->end(); ?>
