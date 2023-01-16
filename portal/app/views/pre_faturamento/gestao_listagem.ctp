<?php if(isset($listagem) && !empty($listagem)){ ?>

<?php echo $this->BForm->create('PreFaturamento', array('url' => array('controller' => 'PreFaturamento', 'action' => ''))); ?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Código do Cliente</th>
            <th>Nome do Cliente</th>
            <th>Usuário cliente validou</th>
            <th>Código do Pedido de Exame</th>
            <th>Nome do Exame</th>
            <th>Data da realização do exame</th>
            <th>Data da baixa do exame</th>
            <th>Status do Pré-Faturamento</th> 
            <th>Ação</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($listagem as $key => $v){ ?>
        <tr>
            <td><?php echo $v['codigo_cliente'];?></td>    
            <td><?php echo $v['nome_cliente'];?></td>   
            <td><?php echo $v['nome_usuario'];?></td>
            <td><?php echo $v['codigo_pedido_exame'];?></td>
            <td><?php echo $v['exame'];?></td>
            <td><?php echo $v['data_realizacao_do_exame'];?></td>
            <td><?php echo $v['data_baixa_exame'];?></td>
            <td><?php echo $v['status'];?></td>
            <?php 
            if($v['status'] == "Em Análise"){
                
                if($v['codigo_usuario_alteracao'] == $authUsuario['Usuario']['codigo']){ 
                    ?><td><a class="btn" href="javascript:void(0);" onclick="modal_analise(<?php echo $v['codigo'];?>)">Continuar Análise</a></td><?php
                }else{
                    ?><td><a class="btn disabled" href="javascript:void(0);" onclick="modal_analise(<?php echo $v['codigo'];?>)">Iniciar Análise</a></td><?php
                }
            }else{
                ?><td><a class="btn" href="javascript:void(0);" onclick="modal_analise(<?php echo $v['codigo'];?>)">Iniciar Análise</a></td><?php 
            } 
            ?>
        </tr>
        <?php } ?> 
               
    </tbody>
</table>


<div class="form-actions">
<?php echo $this->BForm->submit('Concluir a Análise', array('div' => false, 'class' => 'btn btn-primary')); ?>
</div>

<?php echo $this->BForm->end(); ?>

<?php }else{ ?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php } ?>

<div class="modal fade" id="modal" data-backdrop="static"></div>

<script>

    function modal_analise(codigo) {        

        var div = jQuery("div#modal");
        bloquearDiv(div);
        div.load(baseUrl + "pre_faturamento/modal_analise/" + codigo + "/" + Math.random());

        $("#modal").css("z-index", "1050");
        $("#modal").modal("show");
			
    }
    
</script>