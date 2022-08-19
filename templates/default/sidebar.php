<section id="sidebar">
    <div class="one_first">
        <aside id="search-2" class="widget widget_search">
            <div class="search_line">                
                <?php
                    echo SearchForm::draw(array(
                        'placeHolder'       => A::t('appointments', 'enter keywords'),
                        'innerWrapper'      => true,
                        'innerWrapperTag'   => 'p',
                        'buttonHtml'        => '<input class="btn" name="ap0" value="" type="submit">',
                        'idHtml'                => 'keywords_sidebar'
                    ));
                ?>
                </div>
        </aside>
    </div>
    <?= FrontendMenu::draw('right', $this->_activeMenu); ?>
</section>
