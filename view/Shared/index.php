<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Bitmonex</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <?php
        $user = usr::getCurrentUser(1);
        $widgets = widgetControl::getPageWidgets(0);
    ?>

    <style type="text/css"></style>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,300,700' rel='stylesheet' type='text/css'>
    <link href="/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/public/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="/public/css/style.css" rel="stylesheet">
    <link href="/public/css/popups.css" rel="stylesheet">
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="/public/js/Libs/jquery-1.10.1.min.js"><\/script>')</script>
    <script type="text/javascript" src="/public/js/Libs/bootstrap.min.js"></script>
    <!--script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script-->
    <script type="text/javascript" src="/public/js/Libs/jquery.tablesorter.min.js"></script>
    <script type="text/javascript" src="/public/js/Libs/spin.min.js"></script>
    <!--[if IE]>
    <script type="text/javascript" src="/public/js/Libs/flashcanvas.js"></script>
    <![endif]-->
    <script type="text/javascript" src="/public/js/Views/concrete/concreteshared.js"></script>
    <script type="text/javascript" src="/public/js/Libs/flotr2.min.js"></script>
    <script type="text/javascript" src="/public/js/Utils/Ajax.js"></script>
    <script type="text/javascript" src="/public/js/Utils/AjaxLoader.js"></script>
    <script type="text/javascript" src="/public/js/Utils/PageLoader.js"></script>
    <script type="text/javascript" src="/public/js/Utils/PopupLoader.js"></script>
    <script type="text/javascript" src="/public/js/Utils/json2.js"></script>
    <script type="text/javascript" src="/public/js/Views/Models/balance.js"></script>
    <script type="text/javascript" src="/public/js/Views/WidgetsLoad.js"></script>

    <?php foreach($widgets as $key => $value): ?>
            <?php
                api::javascriptLoad("/public/js/Views/Models/" . $value['widget_info']['widget_name'] . ".js");
                api::javascriptLoad("/public/js/Views/concrete/concrete" . $value['widget_info']['widget_name'] . ".js");
            ?>
    <?php endforeach; ?>

    <!--[if lt IE 9]>
    <script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <script type="text/javascript">
        document.write('<span id="EgoPaySeal"></span>');
        (function() {
            var eps = document.createElement('script'); eps.type = 'text/javascript'; eps.async = true;
            //eps.src = 'https://www.egopay.com/verify/seal/seal/QHAIF1V5265G';
            var sp = document.getElementById('EgoPaySeal'); if (sp) {sp.appendChild(eps);}
        })();
    </script>
</head>
<body>
<?php Core::runView('Shared/main_topbar', $data); ?>
<div id="main_middle" class="container-fluid content row clearfix">
    <?php $data = $widgets; Core::runView('Shared/main_middle', $data); ?>
</div>
<!-- div class="footer"></div -->

<div class="modal fade" tabindex="-1" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
</body>
</html>