<?php

class widgetControl extends MainController
{

    static public function getPageWidgets($widgetPage = null)
    {
        $usr = usr::getCurrentUser(1);
        if(!isset($usr))
        {
            $widgets = self::getDefaultWidgets();
            return self::prepareDefaultWidgets($widgets);
        }

        if ($widgetPage == null) {
            $widgetPage = self::getVar('pageNum');
         }

        $val = new Widget();
        $widgets = $val->findBy(array('UID' => $usr->getId(), 'page' => $widgetPage));

        if (empty($widgets))
        {
            $widgets = self::getDefaultWidgets();

            $widget = new Widget();
            $widget->setUID($usr->getId());
            foreach ($widgets as $value)
            {
                $widget->setPage($value['Page']);
                $widget->setWidgetId($value['WidgetId']);
                $widget->setPriority($value['Priority']);
                $widget->setRate($value['RateId']);
                $widget->insert();
            }

            $widgets = $widget->findBy(array('UID' => $usr->getId()));
        }

        foreach($widgets as $key=>$value)
        {
            $id = $value['rate'];
            $rt = new Rate();
            $rt->setId($id);
            $rt->findById($id);
            $fcId = $rt->getFirstCurrencyId();
            $scId = $rt->getSecondCurrencyId();
            $cur = new Currency();
            $cur->findById($fcId);
            $firstCurr = $cur->getName();
            $cur->findById($scId);
            $secondCurr = $cur->getName();

            $arr = array();
            $arr['firstCurrency'] = $firstCurr;
            $arr['secondCurrency'] = $secondCurr;
            $widgets[$key]['rate'] = $arr;

            $widgets[$key]['widget_info'] = Widget::getWidgetDescription($value['widget_id']);
        }

        return Core::array_sort($widgets, 'priority', SORT_ASC);
    }

    static public function getPageDefaultWidgets()
    {
        $widgets = self::getDefaultWidgets();
        $return = array();
        foreach ($widgets as $value)
        {
            $page = $value['Page'];
            array_push($return, $page);
        }

        return $return;
    }

    static private function getDefaultWidgets()
    {
        $country = Core::identifyCountry();
        //$country = 'CN'; // just for test
        $widgets = DefaultWidget::findByCountry($country);
        if (empty($widgets))
        {
            $widgets = DefaultWidget::findByCountry('US');
        }

        return $widgets;
    }

    static private function prepareDefaultWidgets($widgets)
    {
        $rate = new Rate();
        foreach ($widgets as $key=>$widget)
        {
            $rate->findById($widget['RateId']);
            $cur = new Currency();
            $cur->findById($rate->getFirstCurrencyId());
            $firstCurr = $cur->getName();
            $cur->findById($rate->getSecondCurrencyId());
            $secondCurr = $cur->getName();

            $arr = array();
            $arr['firstCurrency'] = $firstCurr;
            $arr['secondCurrency'] = $secondCurr;

            $widgets[$key]['rate'] = $arr;
            $widgets[$key]['widget_info'] = Widget::getWidgetDescription($widget['WidgetId']);
        }

        return $widgets;
    }

    static public function getWidgetsCategories()
    {
        $widget = new Widget();
        $categories = $widget->getWidgetsCategories();

        return $categories;
    }

    static public function getWidgetsByCategory($category)
    {
        $widget = new Widget();
        $widgets = $widget->getWidgetsByCategory($category);

        return $widgets;
    }

    static public function getAllWidgetsPlease()
    {
        $widgets = new Widget();
        $result = $widgets->getAllWidgets();

        return $result;
    }

    static public function setWidgetPriory()
    {
        $usr = usr::getCurrentUser(1);
        if(!isset($usr))
            return;

        $widgets = self::getVar('Data');

        $dbh = User::getDBConnection();
        foreach($widgets as $value)
        {
            $query = 'UPDATE `user_widgets` SET priority=' . $value['priority'] . ' WHERE id=' . $value['widgetId'] .';';
            $dbh->query($query);
        }
    }

    static public function getPages()
    {
        $usr = usr::getCurrentUser(1);
        if(!isset($usr))
            return;

        $widget = new Widget();
        $widget->setUID($usr->getId());
        $pages = $widget->getAllPages();

        foreach($pages as $key=>$value)
        {
            $id = $value['rate'];
            $rt = new Rate();
            $rt->setId($id);
            $rt->findById($id);
            $fcId = $rt->getFirstCurrencyId();
            $scId = $rt->getSecondCurrencyId();
            $cur = new Currency();
            $cur->findById($fcId);
            $firstCurr = $cur->getName();
            $cur->findById($scId);
            $secondCurr = $cur->getName();

            $arr = array();
            $arr['firstCurrency'] = $firstCurr;
            $arr['secondCurrency'] = $secondCurr;
            $pages[$key]['rate'] = $arr;
        }

        return $pages;
    }

    static public function addWidget()
    {
        $usr = usr::getCurrentUser(1);
        if(!isset($usr))
            return;

        $newWidgetId = Core::validate(self::getVar('widgetId'));
        $rateId = Core::validate(self::getVar('rateId'));

        $widget = new Widget();
        $widget->setUID($usr->getId());

        $newPriority = $widget->getUserWidgetsCount() + 1;

        $widget->setPriority($newPriority);
        $widget->setWidgetId($newWidgetId);
        $widget->setRate($rateId);

        $widget->insert();

        header('Location: / ');
    }

    static public function addPage()
    {
        $usr = usr::getCurrentUser(1);
        if(!isset($usr))
        {
            return;
        }

        $firstCurrency = Core::validate(self::getVar('firstCurrency'));
        $secondCurrency = Core::validate(self::getVar('secondCurrency'));

        $rate = api::getRate($firstCurrency, $secondCurrency);
        if ($rate == null)
            $rate = api::getRate($secondCurrency, $firstCurrency);
        if ($rate == null)
        {
            $return['success'] = 0;
            $return['error'] = "There is no rate like this";
            $return['code'] = 1;
            print json_encode($return);
            return;
        }

        $widget = new Widget();
        $widget->setUID($usr->getId());

        $pages = $widget->getAllPages();
        foreach($pages as $page)
        {
            if ($rate->getId()==$page['rate'])
            {
                $return['success'] = 0;
                $return['error'] = "You already got this page";
                $return['code'] = 2;
                print json_encode($return);
                return;
            }
        }

        $pageNum = $widget->getUserPagesCount();
        $widget->setPage($pageNum);
        $widget->setRate($rate->getId());

        $widget->insertAllWidgetsOnPage();

        $return['success'] = 1;
        print json_encode($return);
    }


    static public function removePage()
    {
        $usr = usr::getCurrentUser(1);
        if(!isset($usr))
            return;

        $widgetPage = Core::validate(self::getVar('page'));

        $widget = new Widget();
        $uid = $usr->getId();
        $result = $widget->findBy(array('UID' => $uid, 'page' => $widgetPage));

        if(count($result) <= 0)
            return;

        $widget->setUID($uid);
        $widget->setPage($widgetPage);
        $widget->deletePage();

        $return['success'] = 1;
        print json_encode($return);
    }

    static public function removeStack()
    {
        $usr = usr::getCurrentUser(1);
        if(!isset($usr))
            return;

        $firstCurrency = Core::validate(self::getVar('first'));
        $secondCurrency = Core::validate(self::getVar('second'));

        if(!isset($firstCurrency) || !isset($secondCurrency))
        {
            header('Location: / ');
            exit;
        }

        $pages = self::getPages();

        foreach($pages as $key => $value)
            if(($value['rate']['firstCurrency'] == $firstCurrency) && ($value['rate']['secondCurrency'] == $secondCurrency))
                $resultId = $value['page'];


        if(!isset($resultId))
        {
            header('Location: / ');
            exit;
        }

        $widget = new Widget();
        $widget->setPage($resultId);
        $widget->setUID($usr->getId());
        $widget->deletePage();

        header('Location: / ');
    }


    static public function removeWidget()
    {
        $usr = usr::getCurrentUser(1);
        if(!isset($usr))
            return;

        $widgetId = Core::validate(self::getVar('id'));

        $widget = new Widget();
        $result = $widget->findBy(array('UID' => $usr->getId(), 'id' => $widgetId));

        if(count($result) <= 0)
            return;

        $widget->setId($widgetId);
        $widget->delete();

        header('Location: / ');
    }


}