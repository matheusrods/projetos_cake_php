<?php if(!empty($processamentos)) : ?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <th>Codigo</th>
            <th>Usuário</th>
            <th>Tipo Arquivo</th>
            <th>Data</th>
            <th>Status</th>
            <th>Download</th>
        </thead>
        <tbody>
            <?php foreach($processamentos as $processamento) : ?>
            <?php
                $class = '';
                if($processamento[0]['status'] == 'EM PROCESSAMENTO')
                    $class = 'badge badge-empty badge-warning';
                if($processamento[0]['status'] == 'PROCESSADO')
                    $class = 'badge badge-empty badge-success';
                if($processamento[0]['status'] == 'SUSPENSO')
                    $class = 'badge badge-empty badge-important';
                if($processamento[0]['status'] == 'AGUARDANDO')
                    $class = 'badge badge-empty badge-light';
            ?>
            <tr>
                <td><?php echo $processamento[0]['codigo']; ?></td>
                <td><?php echo $processamento[0]['usuario']; ?></td>
                <td><?php echo $processamento[0]['tipo_arquivo']; ?></td>
                <td><?php echo $processamento[0]['data_inclusao']; ?>
                <td><span class="<?php echo $class; ?>" title="<?php echo $processamento[0]['status']; ?>"></span></td>
                <td>
                    <?php if(!empty($processamento[0]['caminho']) && !is_null($processamento[0]['caminho'])) : ?>
                        <a href="<?php echo $processamento[0]['caminho']; ?>" class="icon-download-alt" onclick="return fnc_processamento_conta_download('<?php echo $processamento[0]['codigo']; ?>');" target="_blank"></a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">
                    <strong>Total</strong> <?php echo $this->Paginator->params['paging']['Processamento']['count']; ?>
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

<style type="text/css">
    .processamento_status_em_processamento { color: yellow; font-weight: bold; }
    .processamento_status_processado { color: green; font-weight: bold; }
    .processamento_status_suspenso { color: red; font-weight: bold; }
</style>
<script type="text/javascript">
    function fnc_processamento_conta_download(codigo){
        var return_action = false;

        jQuery.ajax({
            method: "GET",
            url: "/portal/processamentos/contagem",
            async: false,
            data: {codigo: codigo},
            dataType: 'json'
        }).done(function(data){
            if(data.status == 'success')
                return_action = true;
        }).fail(function(data){
            console.log("error!")
            console.log(data);
        });
        return return_action;
    }
</script>