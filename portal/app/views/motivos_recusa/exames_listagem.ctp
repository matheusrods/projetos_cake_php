<?php if(!empty($mrexames)) : ?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>

    <table class="table table-striped">
        <thead>
            <th>Codigo</th>
            <th>Descriçao</th>
            <th>Status</th>
            <th>Ações</th>
        </thead>
        <tbody>
            <?php foreach($mrexames as $exame) : ?>
            <?php
                $class = '';
                if($exame[0]['ativo'] == 1){
                    $status = "ATIVADO";
                    $class = 'badge badge-empty badge-success';
                }
                if($exame[0]['ativo'] == 0){
                    $status = "DESATIVADO";
                    $class = 'badge badge-empty badge-important';
                }
                if(!in_array($exame[0]['ativo'], array(0, 1))){
                    $status = "DESCONHECIDO";
                    $class = 'badge badge-empty badge-light';
                }
            ?>
            <tr>
                <td><?php echo $exame[0]['codigo']; ?></td>
                <td><?php echo $exame[0]['descricao']; ?></td>
                <td><span class="<?php echo $class; ?>" title="<?php echo $status; ?>"></span></td>
                <td><a href="#" title="Mudar Status" class="icon-random troca-status" onclick="return fnc_motivo_recusa_exame_status('<?php echo $exame[0]['codigo']; ?>', '<?php echo $exame[0]['ativo']; ?>');"></a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">
                    <strong>Total</strong> <?php echo $this->Paginator->params['paging']['MotivoRecusaExame']['count']; ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span7'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>

        </div>
    </div>
    <?php echo $this->Js->writeBuffer(); ?>

<?php else : ?>
    <div class="alert">Nenhum dado encontrado!</div>
<?php endif; ?>


<script type="text/javascript">
    function fnc_motivo_recusa_exame_status(codigo, status){
        var status = (status == '0' ? 1 : 0);
        var div_listagem = jQuery("div.lista");

        bloquearDiv(div_listagem);
        jQuery.ajax({
            method: "GET",
            url: "/portal/motivos_recusa/exames_status",
            async: false,
            data: {codigo: codigo, ativo: status},
            dataType: 'json'
        }).done(function(data){
            swal("Atenção", data.message, data.status);
            jQuery("input[type='submit']").trigger('click');
        }).fail(function(data){
            console.log("error!")
            console.log(data);
        });
        desbloquearDiv(div_listagem)
        return false;
    }
</script>