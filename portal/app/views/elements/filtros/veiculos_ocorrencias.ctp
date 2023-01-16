<div class='well'>
    <div id='filtros'>
      	<?php echo $bajax->form('VeiculoOcorrencia', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'VeiculoOcorrencia', 'element_name' => 'veiculos_ocorrencias'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('placa', 
                array( 'label' =>'Número da Placa', 'placeholder' => 'Placa','class' => 'placa-veiculo input-small', 'value' => (isset($this->data['placa']) ? $this->data['placa'] : NULL) ) ) ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div> 
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){ 
        atualizaListaVeiculosOcorrencias(); 
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });         
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:VeiculoOcorrencia/element_name:veiculos_ocorrencias/" + Math.random())
        });        
    });', false); 
?>
<?=$this->Javascript->codeBlock('jQuery(document).ready(function () {setup_mascaras() })')?>            