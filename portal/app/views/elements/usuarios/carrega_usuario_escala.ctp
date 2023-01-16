<div class="row-fluid inline">
    <div class='actionbar-right'>      
    <?php echo $this->Html->link('Incluir Escala', array('controller' => 'usuarios_escalas','action' => 'incluir',
                $this->data['Usuario']['codigo'],
                rand()),
                array(
                    'onclick' => 'return open_dialog(this, "Adicionar Escala", 560)',
                    'title' => 'Adicionar Escala',
                    'class' => 'btn btn-success',
                )
            );
        ?>
    </div>
    <div class="row-fluid inline escala" id="escalas"></div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        carrega_escala('.$this->data['Usuario']['codigo'].');
        function carrega_escala(codigo_usuario){
            var div = $("#escalas");
            bloquearDiv(div);
            $.get(baseUrl + "usuarios_escalas/carrega_usuario_escala/"+codigo_usuario+"/"+Math.random(),function(data){
                div.html(data);
                div.unblock();
            });
        }
    });', false);?>