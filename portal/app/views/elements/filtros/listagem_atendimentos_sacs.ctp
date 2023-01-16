<div class='well'>
    <?php echo $bajax->form('AtendimentoSac', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AtendimentoSac', 'element_name' => 'listagem_atendimentos_sacs'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
		<?php echo $this->BForm->input('codigo_motivo_atendimento', array('label' => 'Motivo da ligação', 'class' => 'input-large', 'options' => $motivos, 'empty' => 'Selecione um motivo')); ?>
		<?php echo $this->BForm->input('nome_atendente', array('class' => 'input-large', 'label' => 'Atendente')); ?>
        <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_embarcador','Embarcador', 'Embarcador', 'AtendimentoSac', null, false) ?>
        <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_transportador','Transportador', 'Transportador', 'AtendimentoSac', null, false) ?>
	</div>
	<div class="row-fluid inline">
		<?php echo $this->Buonny->input_periodo($this, 'AtendimentoSac', 'data_inicial', 'data_final', true) ?>
		<?php echo $this->BForm->input('hora_inicial', array('label' => 'Hora inicial', 'class' => 'hora input-mini')); ?>
		<?php echo $this->BForm->input('hora_final', array('label' => 'Hora final', 'class' => 'hora input-mini')); ?>
	</div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn' )) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-atendimentos', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "atendimentos_sacs/listagem_registro_chamadas/" + Math.random());
        jQuery("#limpar-filtro-atendimentos").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AtendimentoSac/element_name:listagem_atendimentos_sacs/" + Math.random())
        });
    });', false);?>