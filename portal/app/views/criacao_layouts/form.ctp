<?php
$data = isset($pageData) ? $pageData : array();
function field($source, $field, $default = null)
{
    return is_null($source) == false && isset($source[$field]) ? $source[$field] : $default;
}
?>
<link href="/portal/css/layouts/listagem.css" rel="stylesheet" />
<div id="full-tables-data" class="hidden">
    <?= json_encode(isset($tables) ? $tables : array()) ?>
</div>
<div id="store-page" class="store">
    <?php echo $this->BForm->create('Layouts', array('type' => $pageForm['method'], 'url' => array('controller' => $this->name, 'action' => $pageForm['action']))); ?>
    <?php if (isset($data['codigo'])) : ?>
        <input type="hidden" name="_codigo" value="<?= $data['codigo'] ?>" />
    <?php endif; ?>
    <div class="store__actions">
        <div class="row">
            <div class="input-field">
                <label>Nome *</label>
                <input placeholder="Digite o nome do layout" type="text" name="layout[nome]" value="<?= field($data, 'nome', '') ?>" />
            </div>
            <div class="input-field">
                <label>Apelido *</label>
                <input placeholder="Digite o apelido do layout" type="text" name="layout[apelido]" value="<?= field($data, 'apelido', '') ?>" />
            </div>

            <div class="input-field">
                <label>Dsname *</label>
                <input placeholder="Digite o dsname (slug) do layout" type="text" name="layout[dsname]" value="<?= field($data, 'dsname', '') ?>" />
                <span class="note">DSNAME_AAAAMMDD_HHMMSS.CSV</span>
            </div>
            <div class="input-field">
                <label>Ignora a primeira linha ?</label>
                <?php
                $checked = '';
                if(isset($data['ignora_primeira_linha'])) {

                    if($data['ignora_primeira_linha'] == 1) {
                        $checked = 'checked=checked';
                    }
                }
                ?>
                <input type="checkbox" <?php echo $checked; ?> name="layout[ignora_primeira_linha]">
            </div>
        </div>
        <div class="row">
            <div class="input-field">
                <label>Código do cliente *</label>
                <input placeholder="Digite o código do Cliente" type="text" name="layout[codigo_cliente]" value="<?= field($data, 'codigo_cliente', '') ?>" />
            </div>
            <div class="input-field">
                <label>Tipo *</label>
                <select class="centered" name="layout[tipo_layout]" value="<?= field($data, 'tipo_layout', 0) ?>">
                    <option value="1">Delimitador</option>
                </select>
            </div>
        </div>
    </div>
    <div class="store__columns">
        <span class="store__columns--title">
            Colunas
        </span>
        <div class="content__columns" id="row-wrapper">
            <?php if (isset($columns)) :  ?>
                <?php foreach ($columns as $column) : ?>
                    <?php
                        $table = $tables[0];
                        foreach ($tables as $t) {
                            if ($t['original'] != $column['tabela']) {
                                continue;
                            }

                            $table = $t;
                            break;
                        };
                    ?>
                    <div class="row content-row">
                        <div class="input-field small">
                            <label>Posição</label>
                            <input min="1" class="centered" type="number" value="<?= $column['posicao'] ?>" name="layout[columns][<?= $column['codigo'] ?>][position]" />
                        </div>
                        <div class="input-field">
                            <label>Tipo layout</label>
                            <select class="centered change-table" name="layout[columns][<?= $column['codigo'] ?>][tabela]">
                                <?php foreach ($tables as $t) : ?>
                                    <option value="<?= $t['original'] ?>" <?= $t['original'] == $column['tabela'] ? 'selected' : '' ?> ><?= $t['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-field">
                            <label>Coluna</label>
                            <select class="centered reflect-table-rows" name="layout[columns][<?= $column['codigo'] ?>][coluna]">
                                <?php foreach ($table['fields'] as $field): ?>
                                <option value="<?= $field ?>" <?= $column['campo_saida'] == $field ? 'selected' : '' ?> >
                                    <?= strtoupper($field) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button class="delete-row">
                            <icon class="icon-trash"></icon>
                        </button>
                    </div>
                    <input type="hidden" name="exist" value="true" />
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="row to-end full clear-padding">
            <a href="#" style="color: #fff;" class="content-button btn-save" id="add-row__trigger">+</a>
        </div>
    </div>
    <div class="form-actions">
        <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
        <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
    </div>
    <?php echo $this->BForm->end(); ?>
</div>
<script src="/portal/js/layouts/form.js"></script>