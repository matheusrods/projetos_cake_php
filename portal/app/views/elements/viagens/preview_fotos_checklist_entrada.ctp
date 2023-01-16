<div id='fotos'>
    <?php foreach ($fotos_checklist_entrada as $dado): ?>       
    <?php $ext  = end(explode('.', $dado['TVcefViagChecklistEntrFoto']['vcef_diretorio'])); ?>
        <div class="thumbs-processo">
            <a class="fancybox" href="<?php echo '/portal/app/webroot/files/imagens_checklist/'.$dado['TVcefViagChecklistEntrFoto']['vcef_diretorio'] ?>" title="<?= $dado['TVcefViagChecklistEntrFoto']['vcef_diretorio'] ?>" data-fancybox-group="gallery">
                <div id="thumb" style="background: #FEFEFE;text-align:center">
                    <img src="/portal/app/webroot/files/imagens_checklist/thumbs/<?php echo $dado['TVcefViagChecklistEntrFoto']['vcef_diretorio'] ?>" />
                </div>
            </a>
            <div id="row-fluid-inline">
                <?php if(empty($readonly) || $readonly == false): ?>
                    <?php echo $this->Html->link('<i class="icon-trash icon-black"></i>', 'javascript:void(0)', array('escape' => false, 'class'=> "btn btn-small acoes-dtr", 'title' => 'Excluir', 'onclick' => "excluir(".$dado['TVcefViagChecklistEntrFoto']['vcef_vcen_codigo'].",".$dado['TVcefViagChecklistEntrFoto']['vcef_codigo'].")")); ?>
                <?php endif; ?>
                <?php echo $this->Html->link('<i class="icon-repeat icon-black"></i>', 'javascript:void(0)', array('escape' => false, 'class'=> "btn btn-small acoes-dtr", 'title' => 'Girar', 'onclick'=>"girar_foto(".$dado['TVcefViagChecklistEntrFoto']['vcef_vcen_codigo'].",".$dado['TVcefViagChecklistEntrFoto']['vcef_codigo'].",".'90'.")")); ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div style="width:10px; height:50px; clear:both;"></div>
<img src="/portal/img/loading.gif" style="display: none;" id="img_loading_preview" />
<?php if(empty($readonly) || $readonly == false): ?>
<div class="form-actions">
    <a class="btn btn-danger delete" onclick="excluir(<?= $dado['TVcefViagChecklistEntrFoto']['vcef_vcen_codigo'] ?>, '')" href="#">
        Excluir todas as imagens
    </a>  
</div>
<?php endif; ?>
<?php
    echo $this->Javascript->codeBlock('
        function girar_foto(codigo_checklist, codigo_imagem, rotacao) {
            $("#img_loading_preview").show();
            $.ajax({
              url: "/portal/viagens/girar_foto_checklist_entrada" + "/" + codigo_checklist + "/" + codigo_imagem + "/" + rotacao + "/",
              type: "POST",
            }).done(function( data ) {
                //if (data.indexOf(" ")>=0) data = "";
                //console.log(data);
                close_window = false;
                top.location.href = "/portal/viagens/fotos_checklist_entrada/" + codigo_checklist + "/'.$readonly.'";
            });
        }

        function excluir(codigo_checklist, codigo_imagem) {
            if (confirm("Deseja realmente excluir?")){
                $("#img_loading_preview").show();
                $.ajax({
                  url: "/portal/viagens/excluir_foto_checklist_entrada/" + codigo_checklist + "/" + codigo_imagem + "/",
                  type: "POST",
                }).done(function( data ) {
                    //if (data.indexOf(" ")>=0) data = "";
                    //console.log(data);
                    close_window = false;
                    top.location.href = "/portal/viagens/fotos_checklist_entrada/" + codigo_checklist + "/";                        
                });
            }
        }        

    ');
?>