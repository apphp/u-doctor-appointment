<?php
	$this->_activeMenu = 'workingHours/edit';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Clinic Working Hours')),
    );    
?>

<h1><?php echo A::t('appointments', 'Clinic Working Hours')?></h1>	

<div class="bloc">
	<?php echo $tabs; ?>
	
	<div class="sub-title"><?php echo A::t('appointments', 'Clinic Working Hours'); ?></div>
    <div class="content">        
    <?php
        echo $actionMessage;
        
        echo CHtml::openForm('workingHours/edit/clinicId/'.$clinicId, 'post', array('name'=>'frmWorkingHours', 'autoGenerateId'=>true));
    ?>
        <label for="clinic_id_label"><?= A::t('appointments', 'Clinic').':'; ?></label>
        <select id="clinic_id" name="clinic_id" class="chosen-select-filter">
            <?php foreach ($clinics as $clinic): ?>
                <option <?= $clinic['id'] == $clinicId ? 'selected' : ''; ?> value="<?= $clinic['id']; ?>"><?= $clinic['clinic_name']; ?></option>
            <?php endforeach; ?>

        </select>

        <input type="hidden" name="act" value="send">

        <table class="table" cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th><?php echo A::t('appointments', 'Day of week'); ?></th>
                <th width="190px" class="center"><?php echo A::t('appointments', 'Start Time'); ?></th>
                <th width="190px" class="center"><?php echo A::t('appointments', 'End Time'); ?></th>
                <th width="130px" class="center"><?php echo A::t('appointments', 'Is Day Off'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            //        CDebug::d($weekDays,1);
            foreach($weekDays as $weekDayNumber => $weekDay){
                $day = $weekDay['day'];
                $dayName = $weekDay['name'];

                $checked = isset($workingHours[$weekDayNumber]['is_day_off']) ? $workingHours[$weekDayNumber]['is_day_off'] : 0;
                $htmlOption = array('disabled'=>($checked ? true : false), 'class'=>'no-choosen');

                $startTime = isset($workingHours[$weekDayNumber]['start_time']) ? $workingHours[$weekDayNumber]['start_time'] : '00:00';
                $startTimeParts = explode(':', $startTime);
                $startHour = isset($startTimeParts[0]) ? $startTimeParts[0] : '00';
                $startMinute = isset($startTimeParts[1]) ? $startTimeParts[1] : '00';

                $endTime = isset($workingHours[$weekDayNumber]['end_time']) ? $workingHours[$weekDayNumber]['end_time'] : '00:00';
                $endTimeParts = explode(':', $endTime);
                $endHour = isset($endTimeParts[0]) ? $endTimeParts[0] : '00';
                $endMinute = isset($endTimeParts[1]) ? $endTimeParts[1] : '00';

                echo '<tr class="odd" id="row_'.$weekDayNumber.'">';
                echo '<td>'.A::t('appointments', $dayName).'</td>';
                echo '<td class="center">';
                echo CHtml::dropDownList($day.'_hour_from', $startHour, null, $htmlOption, array('type'=>'hours')).' : ';
                echo CHtml::dropDownList($day.'_minute_from', $startMinute, null, $htmlOption, array('type'=>'minutes', 'step'=>'5'));
                echo '</td>';
                echo '<td class="center">';
                echo CHtml::dropDownList($day.'_hour_to', $endHour, null, $htmlOption, array('type'=>'hours')).' : ';
                echo CHtml::dropDownList($day.'_minute_to', $endMinute, null, $htmlOption, array('type'=>'minutes', 'step'=>'5'));
                echo '</td>';
                echo '<td class="center"><div class="slideBox"><input id="fromWorkingHours_wd_'.$day.'" value="1"'.($checked ? ' checked="checked" ' : '').' type="checkbox" name="'.$day.'_dayoff" onclick="javascript:toggleWorkingHours(this);"><label for="fromWorkingHours_wd_'.$day.'"></label></div></td>';
                echo '</tr>';
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="4">
                    <div class="buttons-wrapper"><input value="<?php echo A::t('appointments', 'Save'); ?>" type="submit"></div>
                </td>
            </tr>
            </tfoot>
        </table>
		
        
    <?php echo CHtml::closeForm(); ?>    
    </div>
</div>

<?php
	A::app()->getClientScript()->registerScript(
		'working-hours-times-toggle',
		'function toggleWorkingHours(el){
            if($(el).is(":checked")){
                $(el).closest("TR").find("SELECT").attr("disabled",true);
            }else{
                $(el).closest("TR").find("SELECT").attr("disabled",false);
            }
		};',
		0
	);
    A::app()->getClientScript()->registerScript(
		'deliverySettings',
		'jQuery(document).ready(function(){
            $("#clinic_id").change(function(){
                var clinicId = $("#clinic_id").val();
                $(this).closest("form").find("input[name=act]").val("changeLang");$(this).closest("form").attr("action","workingHours/edit/clinicId/"+clinicId);$(this).closest("form").submit();
            });
        });'
	);
