<?php
//echo $this->passedArgs[0];
echo $this->BForm->create('Ocorrencia', array('url' => array('action' => 'atualizar', $this->passedArgs[0])));
?>
<fieldset style ="width:565px">
<?php echo $this->BForm->input('codigo', array('type' => 'hidden', 'value' => $ocorrencia['Ocorrencia']['codigo'])); ?>
    <table>
        <tr>
            <td>
                <strong>Placa:</strong>
            </td>
            <td>
<?php echo $ocorrencia['Ocorrencia']['placa']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Codigo Sm:</strong>
            </td>

            <td>
<?php echo $this->Buonny->codigo_sm($ocorrencia['Ocorrencia']['codigo_sm']); ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Ocorrência:</strong>
            </td>

            <td>
                <?php
                $tiposDaOcorrenciaAtual = '';
                foreach ($ocorrencia['OcorrenciaTipo'] as $tipo) {
                    $tiposDaOcorrenciaAtual .= $ocorrencia_tipo[$tipo['codigo_tipo_ocorrencia']] . ', ';
                }
                echo substr($tiposDaOcorrenciaAtual, 0, strlen($tiposDaOcorrenciaAtual) - 2);
                ?>

            </td>
        </tr>
        <?php
            if (!empty ($ocorrencia['Ocorrencia']['descricao_tipo_ocorrencia'])){
        ?>
        <tr>
            <td>
                <strong>Descrição outros:</strong>
            </td>
            <td>
<?php echo $ocorrencia['Ocorrencia']['descricao_tipo_ocorrencia']; ?>
            </td>
        </tr>
            <?php
                }
            ?>
        <tr>
            <td>
                <strong>Empresa:</strong>
            </td>
            <td>
<?php echo $ocorrencia['Ocorrencia']['empresa']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Telefone Empresa:</strong>
            </td>

            <td>
<?php echo $ocorrencia['Ocorrencia']['telefone_empresa']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Tecnologia:</strong>
            </td>

            <td>
<?php echo $ocorrencia['Equipamento']['Descricao']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Motorista:</strong>
            </td>

            <td>
<?php echo $ocorrencia['Ocorrencia']['motorista']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Telefone Motorista:</strong>
            </td>

            <td>
<?php echo $ocorrencia['Ocorrencia']['telefone_motorista']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Celular Motorista:</strong>
            </td>

            <td>
<?php echo $ocorrencia['Ocorrencia']['celular_motorista']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Local:</strong>
            </td>

            <td>
<?php echo $ocorrencia['Ocorrencia']['local']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Rodovia:</strong>
            </td>
            <td>
<?php echo $ocorrencia['Ocorrencia']['rodovia']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Origem:</strong>
            </td>
            <td>
<?php echo $ocorrencia['Ocorrencia']['origem']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Destino</strong>
            </td>
            <td>
<?php echo $ocorrencia['Ocorrencia']['destino']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Observação:</strong>
            </td>
            <td>
<?php echo $ocorrencia['Ocorrencia']['observacao']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Usuário Inclusão:</strong>
            </td>

            <td>
<? echo $ocorrencia['Funcionario']['Apelido']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Data Inclusão:</strong>
            </td>

            <td>
<? echo $ocorrencia['Ocorrencia']['data_inclusao']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Usuário Alteração:</strong>
            </td>

            <td>
<? echo $ocorrencia['FuncionarioAlteracao']['Apelido']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Data Alteração:</strong>
            </td>

            <td>
<? echo $ocorrencia['Ocorrencia']['data_alteracao']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Status Anterior:</strong>
            </td>

            <td>
<? echo $tipoStatusSVizualizacao[$ocorrencia['Ocorrencia']['codigo_status_ocorrencia']]; ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Novo Status:</strong>
            </td>
            <td>
                <?php
                echo $this->BForm->input('codigo_status_ocorrencia', array(
                    'label' => false,
                    'options' => $tipoStatusSupervisor,
                    'class' => 'status',
                    'empty' => 'Escolha um Status'
                        )
                )
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <?php
                echo $this->BForm->submit('Alterar Status', array(
                    'class' => 'fullwide',
                    'onclick' => ''))
                ?>
            </td>
        </tr>
    </table>
</fieldset>

<?php echo $this->BForm->end(); ?>

<script type="text/javascript">
    (function($) {
        var form = $('#OcorrenciaAtualizarForm');
        form.submit(function() {
            $.post(form.attr('action'), form.serialize(), function() {
                form.parents('.ui-dialog-content').dialog('close');
                location.reload();
            }, 'json');
            return false;
        });
    })(jQuery);
</script>
