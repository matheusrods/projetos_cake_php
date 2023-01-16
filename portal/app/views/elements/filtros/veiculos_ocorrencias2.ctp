<div class='well'>
    <?php echo $bajax->form('TOveiOcorrenciaVeiculo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TOveiOcorrenciaVeiculo', 'element_name' => 'veiculos_ocorrencias2'), 'divupdate' => '.form-ocorrencias_veiculos')) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true,'TOveiOcorrenciaVeiculo') ?>
           	<?php echo $this->BForm->input('svoc_codigo', array('label' => 'Status', 'class' => 'input-small', 'options' => $status,'empty' => 'Selecione')) ?>
            <?php echo $this->BForm->input('tvoc_codigo', array('label' => 'Tipo', 'class' => 'input-large', 'options' => $tipos,'empty' => 'Selecione')) ?>
        </div>

        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	
        setup_mascaras();
       	'.($is_post ? 'atualizaListaVeiculosOcorrencias2();' : '').'

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-ocorrencias_veiculos"));
            jQuery(".form-ocorrencias_veiculos").load(baseUrl + "/filtros/limpar/model:TOveiOcorrenciaVeiculo/element_name:veiculos_ocorrencias2/" + Math.random())
        });
    });', false);

?>
