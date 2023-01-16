<div class='well'>
	<h5><?= $this->Html->link('Definir Filtros', 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros' style='display:none'>
		<?php echo $this->BForm->create('RelatorioDre', array('autocomplete' => 'off', 'url' => array('controller' => 'relatorios_dre', 'action' => 'listagem'))) ?>
	    <div class="row-fluid inline">
			<?php echo $this->Buonny->input_codigo_cliente($this) ?>
			<?php echo $this->BForm->input('produto', array('label' => false, 'class' => 'input-xlarge', 'options' => $produtos, 'empty' => 'Todos os Produtos')) ?>
		</div>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('ano', array('label' => false, 'placeholder' => 'Ano', 'class' => 'input-small', 'options' => $anos)) ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		<?php // echo $form->end();?>
		<?php  echo $this->BForm->end() ?>
	</div>
</div>

<div class="well">
    <div class="row-fluid-inline">
        <strong>Empresa:</strong> Buonny Projetos e Serv Risc Securitarios LTDA
    </div>
    <div class="row-fluid-inline">
        Valores em <strong>R$ x 1.000</strong>
    </div>
</div>

<div id='tableDiv_Arrays' class='tableDiv row-fluid' style='overflow-x:auto'>
    <table id='Open_Text_Arrays' class='FixedTables table-dre'>
	    <thead>
	        <tr>
	            <th>DRE Buonny</th>
	            
	            <?php for($mes = 1; $mes < 14; $mes++): ?>
	            	<th><?php echo ($mes < 13 ? substr($this->Buonny->mes_extenso($mes), 0, 3) :'Total'); ?></th>
	            <?php endfor; ?>
	        </tr>
	    </thead>
	    <tbody>
	        <?php $topicos_receita = array('ReceitaOperacionalBruta', 'VendasAnuladas', 'DescontosIncondicionais', 'ISS', 'PIS', 'COFINS', 'ReceitaOperacionalLiquida'); ?>
	        <?php foreach($topicos_receita as $indice=>$topico_receita): ?>
	        	<?php $td_class = ($indice == 0 ? 'topico-branco' : ($indice % 2 ? 'sub-topico-azul' : 'sub-topico-branco')); ?>
	        <tr>
            	<td class="<?php echo $td_class; ?>"><?php echo Inflector::humanize(Inflector::underscore($topico_receita)); ?></td>
		        <?php for($mes = 1; $mes < 13; $mes++): ?>
	            	<td class="<?php echo $td_class; ?> numeric"><?php echo $this->Buonny->moeda(isset($dados_receita[$topico_receita][$mes]) ? round($dados_receita[$topico_receita][$mes] / 1000) : 0, array('nozero' => true, 'places'=>0)); ?></td>
		        <?php endfor; ?>        
		        <td class="<?php echo $td_class; ?> numeric"><?php echo $this->Buonny->moeda(isset($dados_receita[$topico_receita]['Total']) ? round($dados_receita[$topico_receita]['Total'] / 1000) : 0, array('nozero' => true, 'places'=>0)); ?></td>
	        </tr>
	        <?php endforeach; ?>  
	        
	        <?php unset($dados_despesa['Total']); ?>
	        <?php foreach($topicos as $topico): ?>
	        	<?php $eh_topico_pai = substr_count($topico['DreTopico']['numero'], ".") == 1 ?>
	        	<?php if($eh_topico_pai): ?>
		        	<?php $indice = 1; ?>
			        <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	        	<?php endif; ?>
	        
	        	<?php $label = "{$topico['DreTopico']['numero']} {$topico['DreTopico']['descricao']}"; ?>
	        	<?php $td_class = ($eh_topico_pai ? 'topico-azul' : ($indice % 2 ? 'sub-topico-azul' : 'sub-topico-branco')); ?>
	        	<tr>
		        	<td class="<?php echo $td_class; ?>">
		        		<?php echo $label; ?>
		        		<?php if($eh_topico_pai): ?>
	        				<span class="ui-icon ui-icon-plusthick colapse"></span>
	        			<?php endif; ?>
        			</td>
			        <?php for($mes = 1; $mes < 13; $mes++): ?>
		            	<td class="<?php echo $td_class; ?> numeric"><?php echo $this->Buonny->moeda(isset($dados_despesa[$label][$mes]) ? round($dados_despesa[$label][$mes] / 1000) : 0, array('nozero' => true, 'places'=>0)); ?></td>
			        <?php endfor; ?>        
			        <td class="<?php echo $td_class; ?> numeric"><?php echo $this->Buonny->moeda(isset($dados_despesa[$label]['Total']) ? round($dados_despesa[$label]['Total'] / 1000) : 0, array('nozero' => true, 'places'=>0)); ?></td>
        		</tr>
	        	<?php $indice++; ?>
	        <?php endforeach; ?>      
	        <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	    </tbody>
	</table>
</div>
<?php echo $this->Buonny->link_css('dre'); ?>
<?php echo $this->Buonny->link_js('jquery.fixedtable', false); ?>
<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){
		jQuery('a#filtros').click(function(){
            jQuery('div#filtros').slideToggle('slow');
        });
		
        $('.tableDiv').each(function() {      
            var Id = $(this).get(0).id;
            var maintbheight = (window.innerHeight-300);

            $('#' + Id + ' .FixedTables').fixedTable({
                height: maintbheight,
                fixedColumns: 1,
                classHeader: 'fixedHead',
                classFooter: 'fixedFoot',
                classColumn: 'fixedColumn',
                fixedColumnWidth: 250,
                outerId: Id,
                Contentbackcolor: '#FFFFFF',
                Hoverclass: 'hover'  
            });        
        });
		
		$('.colapse').click(function() {
			var row_index = $(this).parent().parent().next()[0].rowIndex;
			
			while(true){
				var next_row_td = $('.fixedColumn .fixedTable table tr:eq('+row_index+')').find('td').first();
				if(next_row_td.hasClass('sub-topico-azul') || next_row_td.hasClass('sub-topico-branco')) {
					$('.fixedColumn .fixedTable table tr:eq('+row_index+')').toggle();
					$('.fixedContainer .fixedTable table tr:eq('+row_index+')').toggle();
					row_index++;
				}else{
					break;
				}
			}
			$(this).toggleClass('ui-icon-minusthick');
			$(this).toggleClass('ui-icon-plusthick');
		});
    });", array('inline'=>false));
?>
<?php echo $this->Js->writeBuffer(); ?>
