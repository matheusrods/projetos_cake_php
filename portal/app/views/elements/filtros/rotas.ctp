<div class='well'>    
    <div id='filtros'>
        
        <?php echo $bajax->form('Rota', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Rota', 'element_name' => 'rotas'), 'divupdate' => '.form-procurar')) ?>       

        <div class="row-fluid inline">            
            <?php echo $this->BForm->input('codigo', array('class' => 'input-small', 'label' => false, 'placeholder' => 'Codigo')); ?>
            <?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'label' => false, 'placeholder' => 'Descrição')); ?>
        </div>

        <div class="row-fluid inline">            
            <?php echo $this->BForm->input('cidade_origem', array('class' => 'input-large', 'label' => false, 'placeholder' => 'Cidade Origem')); ?>
            <?php echo $this->BForm->hidden('origem'); ?>

            <?php echo $this->BForm->input('cidade_destino', array('class' => 'input-large', 'label' => false, 'placeholder' => 'Cidade Destino')); ?>
            <?php echo $this->BForm->hidden('destino'); ?>
        </div>
        
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn btn-filtro')); ?>
        <?php echo $this->BForm->end();?>
    </div>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
        atualizaListaRotas();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Rota/element_name:rotas/" + Math.random())
        }); 

        // inicia função de busca por cidades
        var cidadeOrigem  = $("#RotaCidadeOrigem");
        var cidadeDestino = $("#RotaCidadeDestino");                

        $("#RotaCidadeOrigem").attr("autocomplete", "off");
        $("#RotaCidadeDestino").attr("autocomplete", "off");

        function extractLast(term){
            return split(term).pop();
        }
        
        function split(val){
            return val.split( /,\s*/ );
        }

        cidadeOrigem.autocomplete({
            minLength:  3,
            source: "/portal/solicitacoes_monitoramento/busca_cidades",
            focus: function(){return false;},
            select: function( event, ui ){
                var terms = split( this.value );
                $("#RotaOrigem").val(ui.item.value);
                terms.pop();
                terms.push( ui.item.label );
                terms.push( "" );
                this.value = terms.join("");
                $("#RotaCidadeOrigem").val(ui.item.label);
                return false;
            }
        });  

        cidadeDestino.autocomplete({
            minLength:  3,
            source: "/portal/solicitacoes_monitoramento/busca_cidades",
            focus: function(){return false;},
            select: function( event, ui ){
                var terms = split( this.value );
                $("#RotaDestino").val(ui.item.value);
                terms.pop();
                terms.push( ui.item.label );
                terms.push( "" );
                this.value = terms.join("");
                $("#RotaCidadeDestino").val(ui.item.label);
                return false;
            }
        });  

        $("#RotaCidadeDestino").blur(function(){ 
            if( $(this).val() == "" )
                $("#RotaDestino").val("");
        });

        $("#RotaCidadeOrigem").blur(function(){
            if( $(this).val() == "" )
                $("#RotaOrigem").val("");
        });
                
    });', false);
?>
