<?php 

$url = null;
$files = array();

function linkBuilder($fileName) {
    $str = '';
    $str .= "<a href=\"index.php?file={$fileName}\">" . $fileName . "<a>";
    return $str;
}

function listBuilder($directory) {
    $str = '';
    $str.= "<ul id=\"yamls-menu\">\n";
    foreach (scandir($directory) as $v) {
        if (strpos($v, ".yaml")) {
            $files[] = $directory;
            $str.= "\t" 
              . '<li>' 
              . linkBuilder($v) 
              . "</li>\n";
        } 
    }
    $str.= "</ul>\n";
    return $str;
}

if (isset($_GET['file'])) {
    $url = $_GET['file'];
}


 

#$url = "http://".$_SERVER['HTTP_HOST']."/apidoc/swagger.yaml";
#if(isset($_SERVER['HTTPS'])) {
#    if($_SERVER['HTTPS']) {
#        $url = "https://".$_SERVER['HTTP_HOST']."/apidoc/swagger.yaml";
#    }
#}

$url = "swagger.yaml";

?><!-- HTML for static distribution bundle build -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Doc. API IT Health</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Source+Code+Pro:300,600|Titillium+Web:400,600,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="swagger-ui/ithealth.css" >
    <link rel="stylesheet" type="text/css" href="swagger-ui/swagger-ui.css" >
  </head>
  <body>
    <div class="topo_ithealth"><span class="logo"><img src="swagger-ui/logo-ithealth.png" alt="logo do IT Health" title="logo do IT Health"></span></div>
    <div id="swagger-ui"></div>

    <script src="swagger-ui/swagger-ui-bundle.js"> </script>
    <script src="swagger-ui/swagger-ui-standalone-preset.js"> </script>
    <script>

        //link para abrir os dados no sswagger
        // var url = 'https://' + "<?php echo $_SERVER['HTTP_HOST']?>" + '/apidoc/swagger.yaml';
        var url = "<?php echo $url?>";
        
        window.onload = function() {
            const ui = SwaggerUIBundle({
                url: url,
                dom_id: '#swagger-ui',
                deepLinking: true,
                defaultModelRendering: 'model',                
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout"
            })
            window.ui = ui; 
            document.getElementsByClassName("topbar")[0].remove();
        }
    </script>  


  </body>
</html>