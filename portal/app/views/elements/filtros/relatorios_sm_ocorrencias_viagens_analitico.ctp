<div class='well'>
	<h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
	    <?php echo $this->Bajax->form('RelatorioSm', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'RelatorioSm', 'element_name' => 'relatorios_sm_ocorrencias_viagens_analitico'), 'divupdate' => '.form-procurar')) ?>
    	    <div class="row-fluid inline">
    	    	<?php echo $this->Buonny->input_periodo($this) ?>
    			<?php echo $this->Buonny->input_codigo_cliente_base($this) ?>
    		</div>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('quantidade_itens', array('class' => 'input-mini', 'placeholder' => 'Qtd. itens', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'placeholder' => 'Placa', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('placa_carreta', array('class' => 'input-small placa-veiculo', 'placeholder' => 'Placa carreta', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('sm', array('class' => 'input-small', 'placeholder' => 'SM', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('pedido_cliente', array('class' => 'input-small', 'placeholder' => 'Pedido Cliente', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('loadplan', array('class' => 'input-small', 'placeholder' => 'Loadplan', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('nf', array('class' => 'input-small', 'placeholder' => 'NF', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('inicializacao', array('class' => 'input-medium', 'label' => false, 'title' => 'Inicialização', 'empty'=>'Inicialização', 'options'=>array(1=>'Automática', 2=>'Manual'))) ?>
				<?php echo $this->BForm->input('finalizacao', array('class' => 'input-medium', 'label' => false, 'title' => 'Inicialização', 'empty'=>'Finalização', 'options'=>array(1=>'Automática', 2=>'Manual'))) ?>
			</div>
			<div class="row-fluid inline">
				<div id="div-tipo-alvo">
					<?php echo $this->element('/filtros/alvos_bandeiras_regioes', array('model'=>'RelatorioSm')); ?>
				</div>
				<?php echo $this->BForm->input('alvo_critico', array('type'=>'checkbox', 'div' => 'control-group input checkbox input-large', 'label' => 'Alvos Críticos')); ?>
				<?php echo $this->BForm->input('sem_tempo_restante', array('type'=>'checkbox', 'div' => 'control-group input checkbox input-large', 'label' => 'Sem Tempo Restante')); ?>
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
	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){ atualizaListaRelatorioSmOcorrenciaViagensAnalitico(); });', false); ?>
<?php endif; ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	$.placeholder.shim();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:RelatorioSm/element_name:relatorios_sm_ocorrencias_viagens_analitico/" + Math.random())
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