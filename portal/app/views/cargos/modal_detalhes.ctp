<div class="modal fade" id="modal_detalhes_cargos" data-backdrop="static" style="width: 35%; top: 15%; ">
    <div class="modal-dialog modal-sm" style="position: static;">
        <div class="modal-content">
            <div class="modal-header" style="text-align: center;">
                <h3>Cargo:</h3>
            </div>

            <div class="modal-body" >
                <p><b>Descrição:</b> <?php echo $cargo['Cargo']['descricao']; ?></p>

                <p><b>CBO:</b> <?php echo $cargo['Cargo']['codigo_cbo']; ?></p>                
                
                <p><b>Descrição de atividade:</b></p>
                <p><?php echo $cargo['Cargo']['descricao_cargo']; ?></p>
                
                <p><b>Cargo similar:</b> <?php echo $cargos_similares['Cargo']['descricao']; ?></p>

                <p ><b>Atribuições: </b></p>
                <?php if(!empty($atribuicoes_cargos)): ?>
                    <ul>
                    <?php       
                    foreach($atribuicoes_cargos as $id => $atribuicao): 
                        if(isset($cargo_atribuicoes_cargos[$id])){
                            echo "<li>".$atribuicao."</li>";
                        } 
                    endforeach; 
                    ?>
                    <ul>
                <?php endif; ?>
                
            </div>
        </div>
        <div class="modal-footer">
            <div class="right">
                <a href="javascript:void(0);" onclick="mostra_modal(0);" class="btn btn-danger">FECHAR</a>
            </div>
        </div>                                      
    </div>
</div>
<?php echo $this->Javascript->codeBlock("
jQuery(document).ready(function() {
   mostra_modal = function(mostra){
        if(mostra == 1){
            $('#modal_detalhes_cargos').css('z-index', '1050');
            $('#modal_detalhes_cargos').modal('show');
        }
        else{
            $('#modal_detalhes_cargos').css('z-index', '-1');
            $('#modal_detalhes_cargos').modal('hide');
        }
    }

    mostra_modal(1); 

});
"); ?>  