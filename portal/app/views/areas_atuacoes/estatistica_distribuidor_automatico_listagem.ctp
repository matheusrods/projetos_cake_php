<?php if( isset($dados) && !empty($dados) ): ?>

    <table class='table table-striped'>
        <thead>
            <th class="input-medium">Área Atuação</th>            
            <th class="input-medium numeric">Qtd. SM</th>
            <th class="input-medium numeric">Qtd. Operadores</th>
        </thead>
        <tbody>            

            <?php foreach($dados as $value): ?>

                <tr>
                    <?php $link = $value[0]['cdis_codigo'].'.'.$value[0]['aatu_descricao']; ?>
                    <td class="input-medium"><?php echo $this->Html->link($link, array('controller' => 'criterios_distribuicao','action' => 'visualizar',$value[0]['cdis_codigo']), array('class' => 'criterio' ,'escape' => false, 'title'=>'Critério Distribuição')); ?></td>                    
                    <td class="input-medium numeric"><?php echo $value[0]['total_sm']; ?></td>
                    <td class="input-medium numeric"><?php echo $value[0]['total_op']; ?></td>                    
                </tr>  

            <?php endforeach; ?>          
            
        </tbody>
    </table>    

<?php endif; ?>
<div id="dialog-criterio" title="Criterio de Distribuição" style="display:none"></div>
<?php echo $this->Javascript->codeBlock('
    $(function(){
        $(".criterio").click(function() {
            var url = $(this).attr("href");

            $("html, body").animate({ scrollTop: 0 });
            $( "#dialog-criterio" ).dialog({
                width: 500,
                open: function(){
                    bloquearDiv($( this ));
                    $(this).load(url);
                }
            });
            
            return false;
        });
        
    });');
?>
