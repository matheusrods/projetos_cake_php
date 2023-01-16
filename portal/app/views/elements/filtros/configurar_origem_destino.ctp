<div class='well'>
	<?php echo $bajax->form('Referencia', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Referencia', 'element_name' => 'configurar_origem_destino'), 'divupdate' => '.form-procurar')) ?>
        <div class='row-fluid inline'>
        <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', TRUE, 'Referencia') ?>
        <?php echo $this->Buonny->input_referencia($this, '#ReferenciaCodigoCliente', 'Referencia', 'refe_codigo_origem',FALSE,'Alvo Origem',TRUE); ?>
    </div>
    <div class='row-fluid inline'>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn filtrar')); ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    </div>
	<?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    $(document).ready(function(){
        $("#limpar-filtro").click(function(){
            bloquearDiv($(".form-procurar"));
            $(".form-procurar").load(baseUrl + "/filtros/limpar/model:Referencia/element_name:configurar_origem_destino/" + Math.random())
            carrega_listagem();
        });
        carrega_listagem();  
        function carrega_listagem(){
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "referencias/listagem_configuracao_origem_destino/" + Math.random());            
        }
        setup_mascaras();
    });', false);?>