<?php $total_colunas = 0;?>
<?php $total_usuario = 0;?>
<?php $linha         = 0;?>

<style type="text/css">
.label {width: 200px;border-radius: 5px;border: 1px solid #D1D3D4}
/* hide input */
input.radio:empty {display: none;}
/* style label */
input.radio:empty ~ .label-radio {
    position: relative;
    width: 1.2em;
    float: left;
    line-height: 1em;
    margin-top: 0em;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
input.radio:empty ~ label:before {
    position: absolute;
    display: block;
    top: 0;
    bottom: 0;
    left: 0;
    content: '';
    width: 1.2em;
    background: #D1D3D4;
    border-radius: 1px 0 0 3px;
}
/* toggle hover */
input.radio:hover:not(:checked) ~ label:before {content:'\2714';text-indent: .2em;color: #C2C2C2;}
input.radio:hover:not(:checked) ~ label {color: #888;}
/* toggle on */
input.radio:checked ~ label:before {content:'\2714';text-indent: .2em;color: #9CE2AE;background-color: #4DCB6D;}
</style>
<?php if($usuarios_pais ) : ?>
    <?php echo $this->BForm->create('Usuario',array('url' => array('controller' => 'supervisores_equipes','action' => 'remanejar_equipe'), 'type' => 'POST')) ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="input-mini"></th>
    <?php foreach ($usuarios_pais as $key => $dados): ?>
        <?php ++$total_colunas;?>
                <th class="input-medium" id="<?php echo $dados['Usuario']['codigo'];?>">
                    <?php echo $dados['Usuario']['apelido'];?>
                    <?php if(count($lista_filhos) > 1):?>
                        <div>
                            <?php echo $this->Html->link('', array('action' => 'remanejamento_geral', $dados['Usuario']['codigo'], $codigo_uperfil ), 
                            array('escape' => false,'class' => "icon-random",
                            'title' => 'Migrar Equipe',
                            'onclick' => "return open_dialog(this,'Migrar Equipe', 760)")) ;?>
                        </div>
                        <div>
                            <?php echo $this->Html->link('', array('action' => 'remanejamento', $dados['Usuario']['codigo'], $codigo_uperfil ), 
                            array('escape' => false,'class' => "icon-refresh",
                            'title' => 'Atualizar Equipe',
                            'onclick' => "return open_dialog(this,'Atualizar Equipe', 760)")) ;?>
                        </div>
                    <?php endif;?>
                </th>
    <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($lista_filhos as $key_filho => $dados_filho ): ?>
        <?php ++$total_usuario;?>
        <tr>
            <td class="input-medium"><?php echo $dados_filho['Usuario']['apelido'];?></td>
            <?php foreach ($usuarios_pais as $key => $dados): ?>
                <?php ++$linha;?>
            <td class="input-medium">
                <?php  $checked =  ( $dados_filho['Usuario']['codigo_usuario_pai'] == $dados['Usuario']['codigo'] ) ? 'checked' : '';?>                
                    <input type="radio" name="data[Usuario][<?=$dados_filho['Usuario']['codigo']?>][]" id="radio<?php echo $linha;?>" class="radio" value="<?=$dados['Usuario']['codigo']?>" <?php echo $checked;?> />
                    <label class="label-radio" for="radio<?php echo $linha;?>">&nbsp;</label>
            </td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach;?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="<?php echo ($total_colunas+1)?>">
                    Total: <?php echo $total_usuario?>
                </th>
            </tr>
        </tfoot>        
    </table>
    <?php if($lista_filhos): ?>
        <div class="form-actions">
            <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
        </div>
    <?php endif;?>
    <?php echo $this->BForm->end(); ?>
    <?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        jQuery("div#filtros").slideToggle("slow");    
    });', false);?>
<?php endif; ?>