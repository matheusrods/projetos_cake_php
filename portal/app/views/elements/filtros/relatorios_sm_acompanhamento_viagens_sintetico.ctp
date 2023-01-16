<div class='well'>
	<h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
	    <?php echo $this->Bajax->form('RelatorioSm', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'RelatorioSm', 'element_name' => 'relatorios_sm_acompanhamento_viagens_sintetico'), 'divupdate' => '.form-procurar')) ?>
	        <div class="row-fluid inline">
    			<?php echo $this->Buonny->input_periodo($this); ?>
    			<?php echo $this->Buonny->input_codigo_cliente_base($this); ?>
    			<?php echo $this->BForm->input('somente_remonta', array('label'=> 'Somente Remonta', 'type'=>'checkbox', 'class' => 'checkbox inline')); ?>
    		</div>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'placeholder' => 'Placa', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('placa_carreta', array('class' => 'input-small placa-veiculo', 'placeholder' => 'Placa carreta', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('sm', array('class' => 'input-small just-number', 'placeholder' => 'SM', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('pedido_cliente', array('class' => 'input-small', 'placeholder' => 'Pedido Cliente', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('loadplan', array('class' => 'input-small', 'placeholder' => 'Loadplan', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('nf', array('class' => 'input-small', 'placeholder' => 'NF', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('cpf', array('class' => 'input-small', 'placeholder' => 'CPF', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('solicitante', array('class' => 'input-small', 'placeholder' => 'Solicitante', 'label' => false, 'type' => 'text')) ?>
			</div>
			<div class="row-fluid inline">
				<div id="div-tipo-alvo">
					<?php echo $this->element('/filtros/alvos_bandeiras_regioes', array('model'=>'RelatorioSm')); ?>
				</div>
				<?php echo $this->BForm->input('UFOrigem', array('label' => false,'class' => 'input-medium','empty'=>'UF Origem','title'=>'UF Origem', 'options' => $EstadoOrigem)) ?>
				<?php echo $this->BForm->input('UFDestino', array('label' => false,'class' => 'input-medium','empty'=>'UF Destino','title'=>'UF Destino', 'options' => $EstadoOrigem)) ?>
				<?php echo $this->BForm->input('alvo_critico', array('type'=>'checkbox', 'class' => 'input-small', 'label' => 'Alvos Críticos')); ?>
			</div>
			<div class="row-fluid inline" id='qualidade'>
				<?php echo $this->BForm->input('qualidade', array('label' => 'Qualidade da temperatura', 'empty' => 'Nenhum','class' => 'input-small', 'options' => $qualidades)) ?>
				<?php echo $this->BForm->input('qualidade_input', array('placeholder' => '%', 'label' => 'Acima de', 'type' => 'text','class' => 'input-small numeric just-number', 'placeholder' => '%')) ?>

			</div>
			<div class="row-fluid inline">
				<span class="label label-info">Agrupar por:</span>
	            <div id='agrupamento'>
					<?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $agrupamento, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-small'))) ?>
				</div>
			</div>
			<div class="row-fluid inline">
				<span class="label label-info">Status das Viagens</span>
				 <span class='pull-right'>
	                <?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("status_viagem")')) ?>
	                <?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("status_viagem")')) ?>
	            </span>
	            <div id='status_viagem'>
					<?php echo $this->BForm->input('codigo_status_viagem', array('label'=>false, 'options'=>$status_viagens, 'multiple'=>'checkbox', 'class' => 'checkbox inline input-xlarge')); ?>
				</div>
			</div>
			<div class="row-fluid inline">
				<span class="label label-info">Tipos de Veículos</span>
				 <span class='pull-right'>
	                <?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("tipo_veiculo")')) ?>
	                <?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("tipo_veiculo")')) ?>
	            </span>
	            <div id='tipo_veiculo'>
					<?php echo $this->BForm->input('codigo_tipo_veiculo', array('label'=>'', 'options'=>$tipos_veiculos, 'multiple'=>'checkbox', 'class' => 'checkbox inline input-xlarge')); ?>
				</div>
			</div>
	    <div class="row-fluid inline">
	        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
	        <?php echo $this->BForm->end() ?>
	    </div>
	    <?php echo $this->Javascript->codeBlock('
		    jQuery(document).ready(function(){
	    		init_combo_event_tipo_alvo("RelatorioSm", "#div-tipo-alvo", "#RelatorioSmCodigoCliente");
		    });', false);
		?>
	</div>
</div>
<?php if(!empty($filtrado)): ?>
	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){ atualizaListaRelatorioSmAcompanhamentoViagensSintetico(); });', false); ?>
<?php endif; ?>

<?php echo $this->Javascript->codeBlock('
	status_atual = false;


	jQuery("#RelatorioSmQualidade").change(function() {
		var selected = jQuery(this).val();
		var div = jQuery("#RelatorioSmQualidadeInput").parent();
		verifica_selecionado(selected, div);
	});

	function verifica_selecionado(selected, div) {
		var label = div.find("label");
		div.hide();
		if (selected == null || selected == 0) {
			div.hide();
		} else if (selected == 1) {
			label.html("Acima de");
			div.show();
		} else if (selected == 2) {
			label.html("Abaixo de");
			div.show();
		} else {
			div.hide();
		}
	}

    jQuery(document).ready(function(){
    	var selected = jQuery("#RelatorioSmQualidade").val();
		var div2 = jQuery("#RelatorioSmQualidadeInput").parent();
		verifica_selecionado(selected, div2);
    	$.placeholder.shim();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:RelatorioSm/element_name:relatorios_sm_acompanhamento_viagens_sintetico/" + Math.random())
            jQuery(".lista").empty();
        });
		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
        setup_mascaras(); 
    });
    function autoRefresh(){
		if(!status_atual){
			$(".auto-refresh").html("Atualização Automática: Ativado");
			status_atual = true;
			autoRefreshInteval = setInterval(function(){
				atualizaListaRelatorioSmAcompanhamentoViagensSintetico();
			}, 300000);
		}else{
			status_atual = false;
			$(".auto-refresh").html("Atualização Automática: Desativado");
			if(typeof autoRefreshInteval != "undefined"){
				clearInterval(autoRefreshInteval);
			}
		}
	}', false);?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>