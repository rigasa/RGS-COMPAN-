<?php
global $post;
//
$refPrefix = self::getOptionNameMB_fn();
$refDatas = self::getDatasMB_fn( $post->ID );
//
$TD = self::getTD_fn();

$startDate = $refDatas[ 'startDate' ];
$endDate = $refDatas[ 'endDate' ];

?>
<input type="hidden" name="<?php echo self::getNonceName_fn(); ?>" id="<?php echo self::getNonceName_fn(); ?>" value="<?php echo wp_create_nonce( self::getNonceAction_fn() );?>"/>

<div class="row">
	<p class="post-attributes-label-wrapper">
		<label class="post-attributes-label">
			<?php _e('Start Date', $TD); ?>
		</label>
	</p>
	<div class="fields">
		<input type="text" class="rgsDatePicker" name="<?php echo $refPrefix; ?>[startDate]" value="<?php echo $startDate; ?>"/>
		<p class="description">
			<?php _e("Campaign start date", $TD); ?>
		</p>
	</div>
</div>

<div class="row">
	<p class="post-attributes-label-wrapper">
		<label class="post-attributes-label">
			<?php _e('End Date', $TD); ?>
		</label>
	</p>
	<div class="fields">
		<input type="text" class="rgsDatePicker" name="<?php echo $refPrefix; ?>[endDate]" value="<?php echo $endDate; ?>"/>
		<p class="description">
			<?php _e('Campaign end date', $TD); ?>
		</p>
	</div>
</div>