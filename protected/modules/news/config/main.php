<?php
return array(
    // Module classes
    'classes' => array(
        'NewsComponent',
        'News',
        'NewsSubscribers',
    ),
    // Management links
    'managementLinks' => array(
        A::t('news', 'News')       => 'news/manage',
        A::t('news', 'Subscribers') => 'newsSubscribers/manage'
    ),    
);
