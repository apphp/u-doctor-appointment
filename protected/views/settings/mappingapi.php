<?php
	Website::setMetaTags(array('title'=>A::t('app', 'Mapping APIs Settings')));

	$this->_activeMenu = 'settings/';
	$this->_breadCrumbs = array(
		array('label'=>A::t('app', 'General'), 'url'=>'backend/'),
		array('label'=>A::t('app', 'Site Settings'), 'url'=>'settings/general'),
		array('label'=>A::t('app', 'Mapping APIs Settings'))
	);
?>

<h1><?= A::t('app', 'Mapping APIs Settings'); ?></h1>

<div class="bloc">

    <?= $tabs; ?>

	<div class="content">
        <?= $actionMessage; ?>

		<div class="left-side">
        <?php
            $mappingTypes = array('google_maps' => 'Google Maps');
            echo CWidget::create('CFormView', array(
                'action'=>'settings/mappingApi',
                'method'=>'post',
                'htmlOptions'=>array(
                    'id'=>'frmMappingApi',
                    'name'=>'frmMappingApi',
                    'autoGenerateId'=>true
                ),
                'requiredFieldsAlert'=>false,
                'fields'=>array(
                    'act'      		   => array('type'=>'hidden', 'value'=>'send'),
                    'mapping_api_type' => array('type'=>'select', 'value'=>$mappingType, 'title'=>A::t('app', 'Mapping API Type'), 'tooltip'=>A::t('app', ''), 'data'=>$mappingTypes, 'mandatoryStar'=>true, 'htmlOptions'=>array('__submit'=>'$(this).closest("form").find("input[name=act]").val("changeTemp");$(this).closest("form").submit();'), 'prependCode'=>'<br><br>'),
                    'mapping_api_key'  => array('type'=>'textbox', 'value'=>$mappingApiKey, 'title'=>A::t('app', 'Mapping API Key'), 'tooltip'=>A::t('app', 'Mapping API Key Usage'), 'mandatoryStar'=>false, 'htmlOptions'=>array('class'=>'middle', 'maxLength'=>'70'), 'prependCode'=>'<br><br>'),
                    'mapping_http_key'  => array('type'=>'textbox', 'value'=>$mappingHttpKey, 'title'=>A::t('app', 'Mapping HTTP Key'), 'tooltip'=>A::t('app', 'Mapping HTTP Key Usage'), 'mandatoryStar'=>false, 'htmlOptions'=>array('class'=>'middle', 'maxLength'=>'70'), 'prependCode'=>'<br><br>'),
                ),
                'buttons'=>Admins::hasPrivilege('site_settings', 'edit') ?
                    array(
                        'submit'=>array('type'=>'submit', 'value'=>A::t('app', 'Update')))
                    : array(),
                'events'=>array(
                    'focus'=>array('field'=>$errorField)
                ),
                'return'=>true,
            ));
        ?>
		</div>

		<div class="central-part">
            <?= A::app()->t('app', 'How to obtain Google Maps API key'); ?>:
            <?= A::app()->t('app', 'Google Maps API Instructions'); ?>
		</div>
		<div class="clear"></div>

	</div>
</div>
<?php
A::app()->getClientScript()->registerCss(
    'mapping-api',
    '#frmMappingApi label{width:auto;}#frmMappingApi label .tooltip-icon{margin-left:5px}'
);
?>
