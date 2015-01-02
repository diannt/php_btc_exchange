<nav id="topBar" class="navbar navbar-inverse navbar-fixed-top">
    <div style="float: left;">
        <div class="navbar-header leftfixed">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">EMONEX</a>
        </div>

    </div>
    <div class="navbar-collapse collapse rightfixed" id="bs-navbar-collapse">
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <span class="flag"><img src="<?php print Core::translateToCurrentLocale('/public/img/flag/eng.png'); ?>" style="margin-top:-2px;"></span>
                    <?php print Core::translateToCurrentLocale('English'); ?>  <b class="caret"></b>
                </a>
                <ul class="dropdown-menu" id="topbar-language-menu">
                    <li><a href="/api/SwitchLanguage?langId=1"><img src="/public/img/flag/eng.png" />  English</a></li>
                    <li><a href="/api/SwitchLanguage?langId=2"><img src="/public/img/flag/rus.png" />  Russian</a></li>
                    <li><a href="/api/SwitchLanguage?langId=3"><img src="/public/img/flag/span.png" />  Spanish</a></li>
                </ul>
            </li>
            <?php if(isset($data['user'])): ?>
                <span class="navbar-text">
                        <img src="/public/img/other/person.png" style="margin-top:-2px;margin-right:6px;">
                    <?php print Core::translateToCurrentLocale('Welcome'); ?>,&nbsp;
                    </span>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle navbar-link" data-toggle="dropdown"><?php print $data['user']->getLogin(); ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu" id="topbar-menu">
                        <li><a href="/usr/mypage"><?php print Core::translateToCurrentLocale('Finances'); ?></a></li>
                        <li><a href="/usr/history"><?php print Core::translateToCurrentLocale('Deals history'); ?></a></li>
                        <?php if(Core::isAdministrator($data['user'])): ?>
                            <li><a href="/admin/localization"><?php print Core::translateToCurrentLocale('Localization'); ?></a></li>
                            <li><a href="/admin/io_fees"><?php print Core::translateToCurrentLocale('Input/Output fees'); ?></a></li>
                        <?php endif; ?>
                        <li><a href="/usr/logout"><?php print Core::translateToCurrentLocale('Logout'); ?></a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="#" id="register"><?php print Core::translateToCurrentLocale('Register'); ?></a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="signin"><?php print Core::translateToCurrentLocale('Sign in'); ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <div id="login-menu" class="container">
                            <div class="row">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"><?php print Core::translateToCurrentLocale('Please, sign in'); ?></h3>
                                    </div>
                                    <div class="panel-body">
                                        <form accept-charset="UTF-8" role="form" method="post" action="/usr/login">
                                            <fieldset>
                                                <div class="form-group">
                                                    <input class="form-control" placeholder="<?php print Core::translateToCurrentLocale('Login'); ?>" name="login" type="text">
                                                </div>
                                                <div class="form-group">
                                                    <input class="form-control" placeholder="<?php print Core::translateToCurrentLocale('Password'); ?>" name="pass" type="password" value="">
                                                </div>
                                                <input class="btn btn-lg btn-success btn-block" type="submit" value="<?php print Core::translateToCurrentLocale('Login'); ?>" style="">
                                            </fieldset>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </ul>
                </li>
            <?php endif; ?>
        </ul>
    </div><!--/.navbar-collapse -->
    <a name="topBar"></a>
</nav>