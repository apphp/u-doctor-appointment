<?php
    $this->_activeMenu = 'clinics/manage';
    $breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments')
    );
    if($parentContoller == 'manage'){
        $breadCrumbs[] = array('label'=>A::t('appointments', 'Clinic Management'));
    }else{
        $breadCrumbs[] = array('label'=>A::t('appointments', 'Clinics Management'), 'url'=>'clinics/manage');
        $breadCrumbs[] = array('label'=>A::t('appointments', 'Edit Clinic'));
    }
    $this->_breadCrumbs = $breadCrumbs;

    $formName = 'frmClinicEdit';

    A::app()->getClientScript()->registerScriptFile('assets/modules/appointments/js/appointments.js', 2);
?>

<h1><?= A::t('appointments', $multiClinics ? 'Clinics Management' : 'Clinic Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>

    <div class="sub-title"><?= A::t('appointments', 'Edit Clinic'); ?></div>
    <div class="content">
    <?php
        echo $actionMessage;

        if(!empty($id)){
            echo CWidget::create('CDataForm', array(
                'model'             => 'Modules\Appointments\Models\Clinics',
                'primaryKey'        => $id,
                'operationType'     => 'edit',
                'action'            => $parentContoller == 'manage' ? 'clinics/manage' : 'clinics/edit/id/'.$id,
                'successUrl'        => 'clinics/manage',
                'cancelUrl'         => 'clinics/manage',
                'passParameters'    => false,
                'method'            => 'post',
                'htmlOptions'       => array(
                    'id'                => $formName,
                    'name'              => $formName,
                    //'enctype'         => 'multipart/form-data',
                    'autoGenerateId'    => true
                ),
                'requiredFieldsAlert' => true,
                'fields'            => array(
                    'phone'                     => array('type'=>'textbox', 'title'=>A::t('appointments', 'Phone'), 'default'=>'', 'validation'=>array('required'=> false, 'type'=>'phoneString', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'32', 'autocomplete'=>'off')),
                    'fax'                       => array('type'=>'textbox', 'title'=>A::t('appointments', 'Fax'), 'default'=>'', 'validation'=>array('required'=> false, 'type'=>'phoneString', 'maxLength'=>32, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'32', 'autocomplete'=>'off')),
                    'time_zone'		            => array('type'=>'select', 'title'=>A::t('app', 'Time Zone'), 'tooltip'=>A::t('app', 'Time Zone Tooltip'), 'data'=>$timeZonesList, 'appendCode'=>' &nbsp;&nbsp;'.$utcTime, 'htmlOptions'=>array()),
                    'longitude'                 => array('type'=>'textbox', 'title'=>A::t('appointments', 'Longitude'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'float', 'maxLength'=>12), 'htmlOptions'=>array('class'=>'medium', 'maxLength'=>12)),
                    'latitude'                  => array('type'=>'textbox', 'title'=>A::t('appointments', 'Latitude'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'float', 'maxLength'=>12), 'htmlOptions'=>array('class'=>'medium', 'maxLength'=>12)),
                    'find_longitude_latitude'   => array('type'=>'link', 'title'=>' ', 'tooltip'=>'', 'linkUrl'=>'javascript:void(0);', 'linkText'=>A::t('appointments', 'Find Longitude / Latitude'), 'htmlOptions'=>array('class'=>'find-longitude-latitude', 'id'=>'find_longitude_latitude', 'data-language'=>$language), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
                    'sort_order'                => array('disabled'=>$parentContoller == 'manage' ? true : false, 'type'=>'textbox', 'title'=>A::t('appointments', 'Sort Order'), 'tooltip'=>'', 'default'=>'0', 'validation'=>array('required'=>true, 'type'=>'numeric', 'maxLength'=>3), 'htmlOptions'=>array('maxLength'=>3, 'class'=>'small')),
                    'is_default'                => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Default'),        'default'=>false, 'tooltip'=>'', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom', 'htmlOptions'=>($clinic->is_default ? array('disabled'=>'disabled', 'uncheckValue'=>$clinic->is_default) : array())),
                    'is_active'                 => array('disabled'=>$parentContoller == 'manage' ? true : false, 'type'=>'checkbox', 'title'=>A::t('appointments', 'Active'), 'default'=>true, 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom', 'htmlOptions'=>($clinic->is_default ? array('disabled'=>'disabled', 'uncheckValue'=>$clinic->is_default) : array())),
                ),
				'translationInfo' => array('relation'=>array('id', 'clinic_id'), 'languages'=>Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'))),
				'translationFields' => array(
					'name'  => array('type'=>'textbox', 'title'=>A::t('appointments', 'Clinic Name'), 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>125), 'htmlOptions'=>array('maxLength'=>'125', 'class'=>'large')),
                    'address'    => array('type'=>'textbox', 'title'=>A::t('appointments', 'Address'), 'tooltip'=>A::te('appointments', 'Address Format Tooltip'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>64), 'htmlOptions'=>array('class'=>'large', 'maxLength'=>64)),
					'description'   => array('type'=>'textarea', 'title'=>A::t('appointments', 'Description'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>2048), 'htmlOptions'=>array('maxLength'=>'2048')),
				),
                'buttons' => array(
                    'submitUpdateClose' => array('disabled'=>$parentContoller == 'manage' ? true : false, 'type'=>'submit', 'value'=>A::t('appointments', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                    'submitUpdate'      => array('type'=>'submit', 'value'=>A::t('appointments', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                    'cancel'            => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
                ),
				'messagesSource' 	=> 'core',
				'showAllErrors'     => false,
				'alerts'            => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Clinic Info')),
				'return'            => true,
            ));
        }
    ?>
    </div>
</div>
<?php
    A::app()->getClientScript()->registerScript(
        'findLangitudeLatitude',
        'jQuery(document).ready(function(){
            jQuery(".find-longitude-latitude").click(function(){
                clinic_FindCoordinates("'.$formName.'");
            });
        });'
    );
