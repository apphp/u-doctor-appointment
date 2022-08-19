<?php
$this->_breadCrumbs = array(
	array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
	array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
	array('label'=>A::t('appointments', 'Membership Plans'), 'url'=>'memberships/membershipPlans'),
	array('label'=> $namePayment),
);
?>

<section id="content" role="main">
	<div class="entry">
		<div class="cmsms_cc">
			<div class="one_first first_column">
				<?= $actionMessage; ?>
				<?= $emailMessage; ?>
			</div>
		</div>
	</div>
</section>
