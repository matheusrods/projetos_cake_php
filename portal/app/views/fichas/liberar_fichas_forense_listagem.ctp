<?php 
    echo $this->Paginator->options(array('update' => '.lista')); 
?>
<?php if( isset($dados) && !empty($dados) ): ?>
    <table class='table table-striped'>
        <thead>
            <th class="input-mini">Código</th>
            <th class="input-medium">Seguradora</th>
            <th class="input-large">Cliente</th>
            <th class="input-medium">Motorista</th>
            <th class="input-medium">CPF</th>
            <th class="numeric input-mini">Ações</th>
        </thead>
        <tbody>

            <?php foreach($dados as $value): ?>

                <tr>
                    <td><?php echo $value['Ficha']['codigo']; ?></td>
                    <td><?php echo $value['Seguradora']['nome']; ?></td>
                    <td><?php echo $value['Cliente']['razao_social']; ?></td>
                    <td><?php echo $value['ProfissionalLog']['nome']; ?></td>                    
                    <td><?php echo $buonny->documento($value['ProfissionalLog']['codigo_documento']); ?></td>
                    <td class="numeric">
                        <?php  
                            echo $this->Html->link('', 'javascript:void(0)', array('codigo-id'=>"{$value['FichaForense']['codigo']}", 'class' => 'icon-wrench btn-flag', 'title' => 'Liberar'));
                        ?>
                    </td>
                </tr>

            <?php endforeach; ?>
            
        </tbody>
    </table>

    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span6'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
        </div>
    </div>
    <?php echo $this->Javascript->codeBlock('
        jQuery(document).ready(function(){       
            $(document).on("click",".btn-flag",function(){
                liberar_ficha($(this).attr("codigo-id"));
                return false;
            });

            function liberar_ficha(codigo){           
                $.ajax({
                    url: "/portal/fichas/liberar_forense/"+codigo+"/"+Math.random(),
                    dataType: "text",
                    success: function(data){
                        if(data)
                            atualizaLiberarFichaForense();
                        else
                            alert("Erro ao liberar ficha.");
                    }
                });            
            }
            
        });', false);
    ?> 
    <?php echo $this->Js->writeBuffer(); ?>      
<?php endif; ?>