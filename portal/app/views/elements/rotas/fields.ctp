<div class="row-fluid inline">          
    <?php echo $this->BForm->hidden('Codigo'); ?>    
</div>  

<div class="row-fluid inline">
    <?php echo $this->BForm->input('Descricao', array('class' => 'input-large', 'label' => 'Descrição')); ?>
</div>          

<div class="row-fluid inline">          
   <?php echo $this->BForm->input('cidade_origem', array('class' => 'input-large ui-autocomplete-input', 'label' => 'Cidade de Origem')) ?>
   <?php echo $this->BForm->hidden('Origem'); ?>

   <?php echo $this->BForm->input('cidade_destino', array('class' => 'input-large ui-autocomplete-input', 'label' => 'Cidade de Destino')) ?>
   <?php echo $this->BForm->hidden('Destino'); ?>

   <?php echo $this->BForm->input('KM', array('class' => 'input-small numeric just-number', 'label' => 'KM')); ?>
</div>


<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->BForm->end(); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();        
        setup_time();

        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
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
        

    });', false);
?>