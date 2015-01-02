<?php
/**
 * MODEL PART
 *
 * Please, god, forgive me for this piece of shit.
 */

$news = new News();
$newsItem = $news->getLastNewsByLocation();

$newsItem['date'] = new DateTime($newsItem['date']);
$newsItem['date'] = $newsItem['date']->format('y/m/d');

$wordsArray = explode(' ', $newsItem['full']);
if(count($wordsArray) >= 50)
{
    $firstPiece = array_slice($wordsArray, 0,50);
    $secondPiece = array_slice($wordsArray, 50, count($wordsArray));

    $moreLabel = '<span id="widgetNewsMoreLabel" class="widgetNewsMoreLabel">... <a>' . Core::translateToCurrentLocale('Read more') . '</a></span>';
    $result = implode($firstPiece, ' ') . $moreLabel . '<div id="widgetNewsItemFull" class="widgetNewsItemFull">' . implode($secondPiece, ' ') . '</div>';
}
else
    $result = $newsItem['full'];

?>

<div class="widgetNews clearfix">
    <h3 class="widgetName"><?php print Core::translateToCurrentLocale('News'); ?></h3>
    <div class="widgetNewsContainer">
        <div class="widgetNewsItem">
            <div class="widgetNewsItemTop clearfix">
                <strong class="widgetNewsItemDate left"><?php print $newsItem['date']; ?></strong>
                <h4 class="widgetNewsItemTitle left"><?php print $newsItem['title']; ?></h4>
            </div>
            <div class="widgetNewsItemContent">
                <?php print $result; ?>
            </div>
        </div>
    </div>
</div>