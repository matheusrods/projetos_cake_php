<div class='well'>
	<?php echo $bajax->form('MensageriaEsocial', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'MensageriaEsocial', 'element_name' => 'index_eventos'), 'divupdate' => '.form-procurar')) ?>
		

		<?php echo $this->Buonny->input_grupo_economico($this, 'MensageriaEsocial', $unidades, $setores, $cargos); ?>
		<div class="row-fluid inline">

			<?php echo $this->BForm->input('tipos_eventos', array('label' => false, 'class' => 'input-medium','options' => $tipos_eventos, 'empty' => 'Selecione um Evento')); ?>

			<?php echo $this->BForm->input('matricula', array('label' => false,'placeholder' => 'Matrícula', 'class' => 'input-medium ', 'type' => 'text')); ?>
			<?php echo $this->BForm->input('codigo_registro_sistema', array('label' => false,'placeholder' => 'Código Evento', 'class' => 'input-medium ', 'type' => 'text')); ?>
			<div class="span1" style="padding-top: 11px;margin-left: 4px;">
				<span class="label label-success">Periodo:</span>
			</div>
			<div class="span2" style="margin-left: 0%;padding-top: 1px;" >
				<?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
			</div>
			<div class="span1" style="padding-top: 17px;margin-left: -4%">
				até
			</div>
			<div class="span2" style="margin-left: -3%;padding-top: 1px;" >
		    	<?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
		    </div>
		</div>

		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-eventos', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>	
<?php 

echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	
		setup_datepicker(); 

		function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "mensageria_esocial/listagem_eventos/" + Math.random());
        }

        atualizaLista();

		jQuery("#limpar-filtro-eventos").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:MensageriaEsocial/element_name:index_eventos/" + Math.random())            
        });        
        
    });', false);
?>