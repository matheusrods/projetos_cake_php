<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('Veiculo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Veiculo', 'element_name' => 'consulta_veiculos_checklist_vencido'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true,'Veiculo') ?>
            <?php echo $this->BForm->input('veic_placa', array('label' => 'Placa','type' => 'text','class' => 'placa-veiculo input-small')) ?>
            <?php echo $this->Buonny->input_referencia($this, '#VeiculoCodigoCliente', 'Veiculo','refe_codigo',FALSE,'Alvo Origem',TRUE) ?>
            <?php echo $this->Buonny->input_periodo($this, 'Veiculo', 'data_inicial', 'data_final', TRUE )?>
            <br>
        </div>
        <div class='row-fluid inline'>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id'=>'filtro')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        </div>
        <?php echo $this->BForm->end() ?>
    </div> 
</div>
<?php echo $this->Javascript->codeBlock('
    $(document).ready(function(){ 
        var div = jQuery("div.lista");
        $("#filtro").click(function(){
            bloquearDiv(div);
            div.load(baseUrl + "veiculos/listagem_checklist_vencido/" + Math.random());
        });
        $("#limpar-filtro").click(function(){
            bloquearDiv($(".form-procurar"));
            $(".form-procurar").load(baseUrl + "/filtros/limpar/model:Veiculo/element_name:consulta_veiculos_checklist_vencido/" + Math.random())
            div.load(baseUrl + "veiculos/listagem_checklist_vencido/" + Math.random());
        });
    });', false);?>