<?php
Website::setMetaTags(array('title'=>$title));
$this->_pageTitle = $title;

$this->_breadCrumbs = array(
    array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label'=> $title)
);
?>
<section id="content" role="main">
    <?= str_ireplace(array('[iframe', '][/iframe]'), array('<iframe', '></iframe>'), $text); ?>
</section>
