<!-- HEADER START -->
<header id="header">
    <div class="header_inner">
        <div class="custom_header">
            <div class="header_html">            
                <?php if($sitePhone): ?>
                    <span class="contact_widget_phone">
                        <?= A::t('appointments', 'Contact us'); ?>:
                        <a href="tel:<?= preg_replace('/[^0-9]/', '', $sitePhone); ?>"><?= CHtml::encode($sitePhone); ?></a>
                    </span>
                <?php endif; ?>
                
                <?php if($siteEmail): ?>
                    <span class="contact_widget_email">
                        <a href="mailto:<?= $siteEmail; ?>"><?= CHtml::encode($siteEmail); ?></a>
                    </span>
                <?php endif; ?>
            
                <div id="language-selector">
                    <?= Languages::drawSelector(array('display' => 'icons')); ?>
                </div>
            </div>
            
            <?php if(!empty($socialNetworks)): ?>
            <div class="wrap_social_icons">
                <ul class="social_icons">
                <?php foreach($socialNetworks as $social): ?>
                    <li>
                        <a target="_blank" rel="noopener noreferrer" href="<?= CHtml::encode($social['link']); ?>" title="<?= CHtml::encode($social['name']); ?>">
                            <img src="images/social_networks/<?= CHtml::encode($social['icon']); ?>" alt="<?= CHtml::encode($social['name']); ?>" />
                        </a>
                    </li>
                <?php endforeach; ?>
                    <li>
                        <a target="_blank" rel="noopener noreferrer"  href="feeds/news_rss.xml" title="RSS">
                            <img src="templates/default/images/img/rss.png" alt="RSS"/>
                        </a>
                    </li>
                </ul>
                <div class="cl"></div>
                <a href="#" class="social_toggle"><span></span></a>
            </div>
            <?php endif; ?>
        </div>
        <a class="logo" href="<?= $this->defaultPage; ?>" title="<?= CHtml::encode($this->siteTitle); ?>">
            <img src="templates/default/images/logo.png" alt="<?= CHtml::encode($this->siteTitle); ?>">
            <span id="slogan"><?= $this->siteSlogan; ?></span>
        </a>
        <a class="responsive_nav" href="javascript:void(0);"><span></span></a>
        
        <!-- NAVIGATION START -->
        <div class="wrap_nav">
            <nav role="navigation">
                <?= FrontendMenu::draw('top', $this->_activeMenu, array('menuId'=>'navigation', 'menuClass'=>'navigation', 'subMenuClass'=>'sub-menu', 'dropdownItemClass'=>'dropdown menu-item menu-item-has-children', 'itemInnerTag'=>'span')); ?>
                <div class="cl"></div>
                <div class="wrap_header_search">
                    <a class="search_but cmsms_icon" href="javascript:void(0);"></a>
                    <div class="search_line">
                        <?= SearchForm::draw(); ?>
                    </div>
                </div>
            </nav>
            <div class="cl"></div>
        </div>
        <div class="cl"></div>
        <!-- NAVIGATION FINISH -->
    </div>
    <div class="cl"></div>
</header>
<!-- HEADER FINISH -->
