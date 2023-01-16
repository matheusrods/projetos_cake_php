<input id="codigo_matriz2" type="hidden" value="<?= $codigo_matriz; ?>" />
<input id="codigo_grupo_economico2" type="hidden" value="<?= $codigo_grupo_economico; ?>" />
<div class="form-procurar">
    <?= $this->element('/filtros/matriz_responsabilidade_unidades') ?>
</div>

<div class='lista-matriz'></div>

<style>
    h3 {
        text-decoration: none;
    }
</style>
