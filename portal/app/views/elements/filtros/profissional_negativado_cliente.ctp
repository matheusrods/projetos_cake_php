<?$exibe_log = (isset($exibe_log) && $exibe_log ? TRUE : FALSE);?>
<div class='well'>
    <div id='filtros'>
    	<?php echo $bajax->form('ProfNegativacaoCliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ProfNegativacaoCliente', 'element_name' => 'profissional_negativado_cliente'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente( $this, 'codigo_cliente', 'Cliente', FALSE, 'ProfNegativacaoCliente' ); ?>
            <?php //echo $this->BForm->input("ProfNegativacaoCliente.razao_social", array('label' => false, 'class' => 'input-xxlarge', 'readonly'=>true)) ?>
            <?php echo $this->BForm->input('codigo_documento', array('class'=>'input-medium' ,'label'=>FALSE, 'placeholder'=>'CPF')) ?>
            <?php echo $this->Buonny->input_periodo($this,'ProfNegativacaoCliente') ?>
            <?php echo $this->BForm->input('codigo_negativacao',array('label'=>FALSE, 'empty' => 'Tipo de negativação','options' => $tipo_negativacao,'class'=>'input-large' ));?>
            <br>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div> 
</div>
<?php echo $this->addScript($this->Buonny->link_js( array('fichas_scorecard', 'solicitacoes_monitoramento') )) ?>
<?if( $exibe_log === FALSE ):
    echo $this->Javascript->codeBlock('
    $(document).ready(function(){ 
        setup_codigo_cliente();
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "profissionais_negativados_clientes/listagem/" + Math.random());
        $("#limpar-filtro").click(function(){
            bloquearDiv($(".form-procurar"));
            $(".form-procurar").load(baseUrl + "/filtros/limpar/model:ProfNegativacaoCliente/element_name:profissional_negativado_cliente/" + Math.random())
        });
    });', false);
elseif ( $exibe_log === TRUE ) :
    echo $this->Javascript->codeBlock('
    setup_codigo_cliente();    
    $(document).ready(function(){ 
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "profissionais_negativados_clientes/listagem_log/" + Math.random());
        $("#limpar-filtro").click(function(){
            bloquearDiv($(".form-procurar"));
            $(".form-procurar").load(baseUrl + "/filtros/limpar/model:ProfNegativacaoCliente/element_name:profissional_negativado_cliente/" + Math.random())
        });
    });', false);    
endif;?>
