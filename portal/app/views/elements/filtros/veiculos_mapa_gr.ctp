<!--<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.js"></script>-->
<div class='well'>
	<h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
	    <?php echo $this->Bajax->form('RelatorioSmVeiculos', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'RelatorioSmVeiculos', 'element_name' => 'veiculos_mapa_gr'), 'divupdate' => '.form-procurar')) ?>
    	    <div class="row-fluid inline">
    			<?php echo $this->Buonny->input_codigo_cliente_base($this); ?>
				<?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'placeholder' => 'Placa', 'label' => false, 'type' => 'text')) ?>
				<div id="div-tipo-alvo">
					<?= $this->Buonny->input_alvos_bandeiras_regioes($this, array_merge($alvos_bandeiras_regioes, array('div' => '#div-tipo-alvo', 'force_model' => 'RelatorioSmVeiculos', 'input_codigo_cliente' => 'codigo_cliente', 'exibe_label' => false, 'exibe_classes' => false, 'exibe_veiculo' => false, 'exibe_transportador' => false,'exibe_bandeira'=>false,'exibe_regiao'=>false,'exibe_loja'=>false)))?>
				</div>
    		</div>
			<div class="row-fluid inline">
				<?php echo $this->BForm->hidden('status_atualizacao');?>
				<?php echo $this->BForm->input('sm', array('class' => 'input-small just-number', 'placeholder' => 'SM', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('pedido_cliente', array('class' => 'input-small', 'placeholder' => 'Pedido Cliente', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('loadplan', array('class' => 'input-small', 'placeholder' => 'Loadplan', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('nf', array('class' => 'input-small', 'placeholder' => 'NF', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('cpf', array('class' => 'input-small', 'placeholder' => 'CPF', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('solicitante', array('class' => 'input-small', 'placeholder' => 'Solicitante', 'label' => false, 'type' => 'text')) ?>
			</div>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('posicionando', array('label' => false,'class' => 'input-medium', 'options' => array('0' => 'Posicionando','1' => 'Sim', '2' => 'Não'))) ?>				
				<?php echo $this->BForm->input('UFOrigem', array('label' => false,'class' => 'input-medium','empty'=>'UF Origem','title'=>'UF Origem', 'options' => $EstadoOrigem)) ?>
				<?php echo $this->BForm->input('UFDestino', array('label' => false,'class' => 'input-medium','empty'=>'UF Destino','title'=>'UF Destino', 'options' => $EstadoOrigem)) ?>
				<?php echo $this->BForm->input('vrot_codigo', array('label'=>false,'empty' => 'Rota','class' => 'input-medium', 'options' => array(1 => 'Possui rota',2=> 'Não possui rota'))) ?>
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
				<span class="label label-info">Tipos de Transporte</span>
				 <span class='pull-right'>
	                <?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("tipo_transporte")')) ?>
	                <?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("tipo_transporte")')) ?>
	            </span>
	            <div id='tipo_transporte'>
					<?php echo $this->BForm->input('codigo_tipo_transporte', array('label'=>'', 'options'=>$tipos_transportes, 'multiple'=>'checkbox', 'class' => 'checkbox inline input-xlarge')); ?>
				</div>
			</div>
	    <div class="row-fluid inline">
	        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn filtrar')) ?>
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
	<?php echo $this->Javascript->codeBlock('
		jQuery(document).ready(function(){
	        if(typeof autoRefreshInteval != "undefined"){
                clearInterval(autoRefreshInteval);
            }
			atualizaListaRelatorioSmVeiculosMapaGr(); 
		});', false); ?>
<?php endif; ?>

<?php echo $this->Javascript->codeBlock('
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
            //$("#divAtualizacaoAuto").hide();
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:RelatorioSmVeiculos/element_name:veiculos_mapa_gr/" + Math.random())
            jQuery(".lista").empty();
        });  
		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
		' . (isset($is_post) && $is_post ? 'jQuery(".btn.filtrar").submit()' : '') . '
		setup_mascaras(); 
    });', false);
?>
<?php if (!empty($filtrado)): ?>
 	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>