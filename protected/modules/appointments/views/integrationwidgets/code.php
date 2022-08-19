<?php
    Website::setMetaTags(array('title'=>A::t('appointments', 'Integration Widgets')));

    $this->_activeMenu = 'integrationWidgets/code';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Integration Widgets')),
    );

    $spinnersCount = 0;
?>

<h1><?= A::t('appointments', 'Integration Widgets'); ?></h1>

<div class="bloc" id="blocAlerts">
    <?= $tabs; ?>


    <div class="content mh10 mv10">

		<?= $actionMessage; ?>
		<h4><?= A::t('appointments', 'Appointments Widget Code'); ?>:</h4>
		
		<div class="integration-wrapper">
			<div class="alert alert-info"><?= A::t('appointments', 'Widget integration notice'); ?></div>
			<br>
			<div class="align-left col-1">
				<textarea id="integration-code-center" onclick="this.select()" readonly="readonly"><?php
					echo '<script type="text/javascript">'."\n";
					echo 'var udaHost = "'.A::app()->getRequest()->getBaseUrl().'";'."\n";
					echo 'var udaKey = "'.CHash::create('sha1', CConfig::get('installationKey')).'";'."\n";
					echo 'var udaWidth = "300px";'."\n";
					echo 'var udaHeight = "422px";'."\n";
					echo 'var udaLang = "en";'."\n";
					echo 'document.write(unescape(\'%3Cscript src="\' + udaHost + \'assets/modules/appointments/js/appointment-widget.js" type="text/javascript"%3E%3C/script%3E\'));'."\n";
					echo '</script>';
				?></textarea>
				<br>
					
				<a href="javascript:void(0);" class="export-data align-left" onclick="javascript:copyToClipboard()"><i class="icon-export">&nbsp;</i><span class="lnk-copy"><?= A::t('app', 'Copy'); ?></span></a>
				<a href="javascript:void(0);" class="preview-data align-left ml10" onclick="appPopupWindow('integration_preview.html','integration-code-center',false)"><i class="icon-preview">&nbsp;</i><?= A::t('app', 'Preview'); ?></a>
				
				<div class="clear">
					<label class="lbl-copied hidden"><?= A::t('appointments', 'Text has been copied to the clipboard'); ?></label>
				</div>				
					
			</div>		
			<div class="align-left col-2">
				<img src="assets/modules/appointments/images/widget.png" alt="integration" />
			</div>
			
			<div class="clear"></div>
			<br><br>
			
		</div>

    </div>
</div>

<?php
	A::app()->getClientScript()->registerCss(
		'widget-code',
		'
		.col-1{padding: 5px;float: left;width: 65%;}
		.col-1 textarea{width:96%;height:170px;margin:5px 0;float:left;}
        .col-2{padding: 5px;width: 30%;float: left;}
        .col-2 img{width:300px;max-height:422px;}
        '
	);

	A::app()->getClientScript()->registerScript(
		'widget-code',
		'function copyToClipboard(elementId) {
			jQuery("#integration-code-center").select();
			document.execCommand("copy");
			jQuery("#integration-code-center").blur();				
			jQuery(".lbl-copied").show();
			jQuery(".lnk-copy").html("Copied");				
			setTimeout(function() {
				jQuery(".lbl-copied").fadeOut("fast");
				jQuery(".lnk-copy").html("Copy");
			}, 700);
		}
		function appPopupWindow(template_file, element_id, use_replacement){
			var element_id = (element_id != null) ? element_id : false;
			var use_replacement = (use_replacement != null) ? use_replacement : true;
			var new_window = window.open("html/"+template_file, "PopupWindow", "height=450,width=350,toolbar=0,location=0,menubar=0,scrollbars=yes,screenX=300,screenY=300");
			if(window.focus) new_window.focus();
			if(element_id){
				var el = document.getElementById(element_id);		
				var message = (el.type == undefined) ? el.innerHTML : message = el.value;
				if(use_replacement){
					var reg_x = /\n/gi;
					var replace_string = "<br> \n";
					message = message.replace(reg_x, replace_string);
				}
				new_window.document.open();
				new_window.document.write(message);
				new_window.document.close();
			}
		}', 0
	);

?>