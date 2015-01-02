<?php

$activeMenu = $data['activeMenu'];
$activeClass = 'class="active-list-element"';

?>

<ul class="side-menu">
    <li <?php if($activeMenu == 'Finances') print $activeClass; ?>><a href="/usr/mypage"><?php print Core::translateToCurrentLocale('Finances'); ?></a></li>
    <li <?php if($activeMenu == 'Deals history') print $activeClass; ?>><a href="/usr/history"><?php print Core::translateToCurrentLocale('Deals history'); ?></a></li>
    <li <?php if($activeMenu == 'Feedback') print $activeClass; ?>><a href="/usr/feedback"><?php print Core::translateToCurrentLocale('Feedback'); ?></a></li>
    <?php if(Core::isAdministrator($data['user'])): ?>
        <li <?php if($activeMenu == 'YM transactions') print $activeClass; ?>><a href="/admin/ym"><?php print 'YM transactions' ?></a></li>
        <li <?php if($activeMenu == 'PM wallets') print $activeClass; ?>><a href="/admin/pm"><?php print Core::translateToCurrentLocale('PM wallets'); ?></a></li>
        <li <?php if($activeMenu == 'LTC wallets') print $activeClass; ?>><a href="/admin/ltc"><?php print Core::translateToCurrentLocale('LTC wallets'); ?></a></li>
        <li <?php if($activeMenu == 'BTC wallets') print $activeClass; ?>><a href="/admin/btc"><?php print Core::translateToCurrentLocale('BTC wallets'); ?></a></li>
        <li <?php if($activeMenu == 'OKP wallets') print $activeClass; ?>><a href="/admin/okp"><?php print Core::translateToCurrentLocale('OKP wallets'); ?></a></li>
        <li <?php if($activeMenu == 'EGOP wallets') print $activeClass; ?>><a href="/admin/egop"><?php print Core::translateToCurrentLocale('EGOP wallets'); ?></a></li>
        <li <?php if($activeMenu == 'Localization') print $activeClass; ?>><a href="/admin/localization"><?php print Core::translateToCurrentLocale('Localization'); ?></a></li>
        <li <?php if($activeMenu == 'Input/Output fees') print $activeClass; ?>><a href="/admin/io_fees"><?php print Core::translateToCurrentLocale('Input/Output fees'); ?></a></li>
        <li <?php if($activeMenu == 'Internal fees') print $activeClass; ?>><a href="/admin/internal_fees"><?php print Core::translateToCurrentLocale('Internal fees'); ?></a></li>
        <li <?php if($activeMenu == 'Order settings') print $activeClass; ?>><a href="/admin/order_settings"><?php print Core::translateToCurrentLocale('Order settings'); ?></a></li>
        <li <?php if($activeMenu == 'News') print $activeClass; ?>><a href="/admin/news"><?php print Core::translateToCurrentLocale('News'); ?></a></li>
        <li <?php if($activeMenu == 'Order priority') print $activeClass; ?>><a href="/admin/order_priority"><?php print Core::translateToCurrentLocale('Order priority'); ?></a></li>
    <?php endif; ?>
</ul>