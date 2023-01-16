<?php $this->addScript($this->Buonny->link_js('tinymce/tinymce.min.js')); ?>


<div class="row-fluid inline">&nbsp;</div>
<div class="row-fluid inline" style="margin-bottom:5px;">Período de visualização:&nbsp;</div>
<div class="row-fluid inline">          
    <?php echo $this->BForm->hidden('codigo'); ?>
    <?php echo $this->Buonny->input_periodo($this, 'MensagemDeAcesso') ?>       
</div>  

<div>
    <?php echo $this->BForm->input('titulo', array('class' => 'input-xlarge', 'label' => 'Título:', /*'maxlength'=>false*/)); ?>
</div>  
<div class="row-fluid inline">
    <span class="label label-info">Perfis:</span>
    <span class='pull-right'>
        <?= $this->Html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("perfil")')) ?>
        <?= $this->Html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("perfil")')) ?>
    </span>
    <div id='perfil'>
        <?php echo $this->BForm->input('MensagemDeAcessoPerfil.codigo_tipos_perfis', array('label' => false, 'class' => 'checkbox inline input-small', 'options' => $lista_perfis, 'multiple' => 'checkbox')); ?>
    </div>
</div>
<div class="row-fluid inline">
    <span class="label label-info">Módulos:</span>
    <span class='pull-right'>
        <?= $this->Html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("modulo")')) ?>
        <?= $this->Html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("modulo")')) ?>
    </span>
    <div id='modulo'>
        <?php echo $this->BForm->input('MensagemDeAcessoModulo.codigo_modulo', array('label' => false, 'class' => 'checkbox inline input-small', 'options' => $modulos, 'multiple' => 'checkbox')); ?>
    </div>
</div>
<div>    
    <?php echo $this->BForm->input('mensagem', array('class' => 'input-xxlarge', 'type'=>'textarea', 'rows'=>10, 'cols'=>20, 'label' => 'Mensagem:')); ?>
</div>
<!-- <strong>Atenção!</strong><br />
Evite usar as seguintes expressões: <span style="color:red;">&lt;html&gt; &lt;head&gt; &lt;title&gt; &lt;body&gt; &lt;script&gt;</span> -->

<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->BForm->end(); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){ 
        tinyMCE.init({
        mode : "textareas",
        language : "pt_BR",
        plugins: [
                "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "table contextmenu directionality emoticons template textcolor paste textcolor colorpicker textpattern"
        ],

        toolbar1: "code preview | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
        toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image | insertdatetime | forecolor backcolor",
        toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print  | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak",

        menubar: false,
        toolbar_items_size: "small",
        convert_urls: false,
        allow_script_urls: true,
        
        cleanup_on_startup: false,
        trim_span_elements: true,
        verify_html: false,
        cleanup: false,
    
        valid_children : "+body[style]",
        style_formats: [
                {title: "Bold text", inline: "b"},
                {title: "Red text", inline: "span", styles: {color: "#ff0000"}},
                {title: "Red header", block: "h1", styles: {color: "#ff0000"}},
                {title: "Example 1", inline: "span", classes: "example1"},
                {title: "Example 2", inline: "span", classes: "example2"},
                {title: "Table styles"},
                {title: "Table row 1", selector: "tr", classes: "tablerow1"}
        ],

        templates: [
                {title: "Test template 1", content: "Test 1"},
                {title: "Test template 2", content: "Test 2"}
        ]

    });  
        setup_datepicker();
        setup_time();
    });', false);
?>
<style type="text/css">#MensagemDeAcessoMensagem{width:800px;}</style>