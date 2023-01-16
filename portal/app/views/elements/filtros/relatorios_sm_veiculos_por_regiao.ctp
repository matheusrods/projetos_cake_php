<div class='well'>
	<h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
	    <?php echo $this->Bajax->form('RelatorioSmVeiculosRegiao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'RelatorioSmVeiculosRegiao', 'element_name' => 'relatorios_sm_veiculos_por_regiao'), 'divupdate' => '.form-procurar')) ?>
			<div class="row-fluid inline">
				<?php echo $this->Buonny->input_codigo_cliente_base($this, 'codigo_cliente', 'Cliente', false, 'RelatorioSmVeiculosRegiao') ?>
			</div>
			<div class="row-fluid inline">
			    <?php echo $this->BForm->input('transportador', array('options' => $transportadores, 'label' => false, 'empty' => 'Transportador', 'class' => 'input-xxlarge')); ?>
			    <?php echo $this->BForm->input('tecn_codigo', array('options' => $veiculos_tecnologia, 'label' => false, 'empty' => 'Tecnologia','class' => 'input-large')) ?>
			</div>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'placeholder' => 'Placa', 'label' => false, 'type' => 'text')) ?>
				<table data-index = "0" >
					<tr>
						<td>
							<?php echo $this->Buonny->input_referencia($this, '#RelatorioSmVeiculosRegiaoCodigoCliente', 'RelatorioSmVeiculosRegiao') ?>
							<?php echo $this->BForm->input('latitude', array('class' => 'input-medium refe-latitude', 'placeholder' => 'Latitude', 'label' => false, 'type' => 'text')) ?>
							<?php echo $this->BForm->input('longitude', array('class' => 'input-medium refe-longitude', 'placeholder' => 'Longitude', 'label' => false, 'type' => 'text')) ?>
							<?php echo $this->BForm->input('raio', array('class' => 'input-small just-number', 'placeholder' => 'Raio (km)', 'label' => false, 'type' => 'text')) ?>
						</td>
					</tr>
				</table>
			</div>
			<div class="row-fluid inline">
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
			    <span class="label label-info">Classe Alvos</span><?= $html->link('', 'javascript:void(0)', array('class' => 'icon-question-sign', 'title' => 'Cruzar posições dos veículos somente com as classes de alvo desejadas')) ?>
                <span class='pull-right'>
	                <?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("classe_referencia")')) ?>
	                <?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("classe_referencia")')) ?>
	            </span>
	            <div id='classe_referencia'>
					<?php echo $this->BForm->input('cref_codigo', array('label'=>false, 'options'=>$classes_referencia, 'multiple'=>'checkbox', 'class' => 'checkbox inline input-xlarge')); ?>
				</div>
			</div>
		    <div class="row-fluid inline">
		        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
		    </div>
	    <?php echo $this->BForm->end() ?>
	</div>
</div>
<?php if(!empty($filtrado)): ?>
	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){ atualizaListaRelatorioSmVeiculosPorRegiao(); });', false); ?>
<?php endif; ?>

<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	$.placeholder.shim();
		jQuery(document).on("change", "#RelatorioSmVeiculosRegiaoRefeCodigo", function(){
			preencheLatitudeLongitudeReferencia(jQuery(this).val());
		});
		
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:RelatorioSmVeiculosRegiao/element_name:relatorios_sm_veiculos_por_regiao/" + Math.random())
            jQuery(".lista").empty();
        });  
		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
        
        setup_mascaras(); 
    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
<?php endif; ?>