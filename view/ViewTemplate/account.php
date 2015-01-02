<!--

Datalist


pageName - Name of page
activeMenu - Name of page for active menu
pagePath - Path to page for Core::runView

/* pageContent - Content of page
scriptContent - JS from page */


-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Bitmonex - <?php print $data['pageName']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href="/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/public/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="/public/css/style.css" rel="stylesheet">
    <link href="/public/css/popups.css" rel="stylesheet">
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="/public/js/Libs/jquery-1.10.1.min.js"><\/script>')</script>
    <script type="text/javascript" src="/public/js/Libs/bootstrap.min.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/public/js/Libs/jquery.tablesorter.min.js"></script>

    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
<?php Core::runView('Shared/main_topbar', $data); ?>
<div id="main_middle" class="container-fluid content row clearfix">
    <div class="col-xs-3">
        <div class="content-block" style="padding-top: 60px; padding-left: 55px;">
            <?php Core::runView('AccountProfile/usr_accountmenu', $data); ?>
        </div>
    </div>
    <div class="dashboard col-xs-8" style="padding-left: 0px; padding-bottom: 200px;">
        <div class="content" style="margin-left: 0; padding-top: 20px; min-width: 70%;">
            <div class="topHead">
                <span class="greenMark"><?php print $data['pageName']; ?></span>
                <div class="line"></div>
            </div>
            <?php Core::runView($data['pagePath'], $data); ?>
        </div>
    </div>
</div>
<script>
    <?php //print $data['scriptContent']; ?>
</script>

</body>
</html>
