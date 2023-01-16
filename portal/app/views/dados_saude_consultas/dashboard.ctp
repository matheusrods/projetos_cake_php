<div class='form-procurar'> 
	<div class='well'>
		<?php echo $this->BForm->create('DashboardRelatorio', array('autocomplete' => 'off', 'url' => array('controller' => 'dados_saude_consultas', 'action' => 'dashboard', $pagina))) ?>
		<div class="row-fluid inline">
			<div class="row-fluid">
				<div class="span2">
					<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'DashboardRelatorio'); ?>
				</div>
			</div>
				<?php echo $this->Buonny->input_unid_setor_cargo($this, 'DashboardRelatorio', $unidades, $setores, $cargos); ?>
			
			<?php if($this->params['pass'][0] == 'dados_gerais') { ?>
				<div id='tipos' style="float: right;position: static;margin-top: -52px;margin-right: 120px;">
			        <?php echo $this->BForm->input('tipo_sistemas', array('type' => 'radio', 'options' => $tipos_sistemas, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
			    </div>
			<?php } ?>
		</div>
		
		<?php if($this->params['pass'][0] == 'colaboradores_atestados') { ?>
		<div class="row-fluid inline">
			<div class="span5">
				<label>Data de atestados:</label>
				<?php echo $this->Form->input('atestados_de', array('class' => 'data input-small', 'label' => false, 'before' => 'de ')); ?>
				<?php echo $this->Form->input('atestados_ate', array('class' => 'data input-small', 'div' => 'input text margin-left-20', 'label' => false, 'before' => 'até ')); ?>
			</div>
			<div class="span3">
				<label for="">Horas de atestados:</label>
				<?php echo $this->Form->input('horas_afastamento', array('class' => 'input-mini just-number', 'before' => 'a partir de ', 'after' => 'h',  'label' => false)); ?>
			</div>
			<div class="span3">
				<label for="">Quantidade de atestados:</label>
				<?php echo $this->Form->input('qnt_atestados', array('class' => 'input-mini just-number', 'before' => 'a partir de ', 'label' => false)); ?>
			</div>
		</div>
		<div>&nbsp;</div>
		<?php } ?>

		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn btn-submit')); ?>
		<?php echo $this->BForm->end();?>
	</div>
</div>

<div id="gadgets">
	<?php if($pagina != 'colaboradores_atestados') { ?>
		<div class="margin-bottom-40">
			<?php if(!empty($gadgets['dados_gerais']) || !empty($gadgets['perfil_saude'])) { ?>
			<ul class="nav nav-tabs" id="myTab">
				<li class="active"><a href="#dados_gerais" data-toggle="tab">Dados Gerais</a></li>
				<li><a href="#perfil_saude" data-toggle="tab">Perfil de Saúde</a></li>
			</ul>
			<?php } ?>
		</div>

		<div class="tab-content"> 
			<div class="tab-pane active" id="dados_gerais">
				<div class="row-fluid lista margin-top-10"> 
					<?php 
					$count = 0;
					$span = 0;
					if(!empty($gadgets['dados_gerais'])) {
						foreach ($gadgets['dados_gerais'] as $key => $gadget) {
							if($span >= '12') { echo '</div><div class="row-fluid margin-top-50">'; $span = 0; } ?>

							<div class="span<?php echo $gadget['span']?> " id="<?php echo $count ?>">           
								<script type="text/javascript">
									$(document).ready(function() {
										carrega_gadget('Teste', '/dados_saude_consultas/' + '<?php echo $gadget['gadget'] ?>' , null, '<?php echo $count ?>');
									});
								</script>
							</div>
							<?php  $span += $gadget['span'];
							$count++;
						} 
					} ?>
				</div>
			</div>
			<div class="tab-pane" id="perfil_saude">
				<div class="row-fluid lista margin-top-10"> 
					<?php 
					$span = 0;
					if(!empty($gadgets['perfil_saude'])) {
						foreach ($gadgets['perfil_saude'] as $key => $gadget) {
							if($span >= '12') { echo '</div><div class="row-fluid margin-top-50">'; $span = 0; } ?>

							<div class="span<?php echo $gadget['span']?> " id="<?php echo $count ?>">           
								<script type="text/javascript">
									var executar = [];
									executar['<?php echo $key ?>'] = {titulo: 'Teste', url: 'dados_saude_consultas/' + '<?php echo $gadget['gadget'] ?>', hash: null, div_id: '<?php echo $count ?>'};
								</script>
							</div>
							<?php  $span += $gadget['span'];
							$count++;
						} 
					} ?>
				</div>
			</div>
		</div>
	<?php } else { ?>
		<div class="row-fluid lista margin-top-10"> 
			<?php 
			$span = 0;
			if(!empty($gadgets['colaboradores_atestados'])) {
				foreach ($gadgets['colaboradores_atestados'] as $key => $gadget) {
					if($span >= '12') { echo '</div><div class="row-fluid margin-top-50">'; $span = 0; } ?>
					<div class="span<?php echo $gadget['span']?> " id="<?php echo $key  ?>">           
						<script type="text/javascript">
							$(document).ready(function() {
								carrega_gadget('Teste', '/dados_saude_consultas/' + '<?php echo $gadget['gadget'] ?>' , null, '<?php echo $key ?>');
							});
						</script>
					</div>
					<?php  $span += $gadget['span'];
				} 
			} ?>
		</div>
	<?php } ?>
</div>
<style type="text/css">
	.tab-content {
		overflow: inherit;
	}
</style>
<?php echo $this->Javascript->codeBlock("
	setup_mascaras();
	setup_time();
	setup_datepicker();
	$(document).ready(function() {
		var perfil_saude = false;
		$('[href=\"#perfil_saude\"]').click(function() {
			if(!perfil_saude) {	
				$.each(executar, function(index, val) {
					carrega_gadget(val.titulo, val.url , val.hash, val.div_id);
				});
				perfil_saude = true;
			}
		});
		$('#myTab a').click(function (e) {
			e.preventDefault();
			$(this).tab('show');
		})

	});
	
	function carrega_gadget(titulo, url, hash, div_id) {
		var div = jQuery('#'+div_id);
		bloquearDiv(div);
		jQuery.ajax({
			type: 'POST',
			url: baseUrl + url + '/' + Math.random()
			,data: \"data[Seguradora][hash]=\" + hash
			,success: function(data) {
				div.html(data);
			}
			,error: function (jqXHR, textStatus, errorThrow) {
				div.html(errorThrow);
			}
		});
	}
	", false); ?>




<!-- 
		 -->