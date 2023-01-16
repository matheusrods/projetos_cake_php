<?php if( isset($dados) && !empty($dados) ): ?>

    <table class='table table-striped'>
        <thead>
            <th>SM</th>
            <th>Data</th>
            <th>Embarcador</th>
            <th>Transportador</th>
            <th>Motorista</th>
            <th>Seguradora</th>
            <th>Corretora</th>          
            <th>Natureza</th>   
            <th>Ações</th>
        </thead>
        <tbody>
            
            <?php foreach($dados as $value): ?>

                <tr>
                    <td><?php echo $this->Buonny->codigo_sm($value['Sinistro']['sm']); ?></td>
                    <td><?php echo substr($value['Sinistro']['data_evento'],0,10); ?></td>
                    <td><?php echo $value['Embarcador']['razao_social'] ?></td>
                    <td><?php echo $value['Transportador']['razao_social'] ?></td>
                    <td><?php echo $value['Profissional']['nome'] ?></td>
                    <td><?php echo $value['Seguradora']['nome'] ?></td>
                    <td><?php echo $value['Corretora']['nome'] ?></td>
                    <td><?php echo $natureza[$value['Sinistro']['natureza']] ?></td>
                    <td>
                        <?php  
                            echo $html->link('', array('action' => 'editar', $value['Sinistro']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar'));
                            echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => "javascript:excluir_sinistro({$value['Sinistro']['codigo']})"));
                        ?>
                    </td>
                </tr>

            <?php endforeach; ?>
            
        </tbody>
    </table>

    <?php echo $this->Javascript->codeBlock('
        jQuery(document).ready(function(){
           
           jQuery("div#filtros").slideToggle("slow");
            
        });', false);
    ?>

<?php endif; ?>

<?php 
echo $this->Javascript->codeBlock("
function excluir_sinistro(codigo) {
    if (confirm('Deseja excluir este sinistro?'))
        location.href = '/portal/sinistros/excluir/' + codigo;
}
"); ?>