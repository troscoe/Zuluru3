<?php
/**
 * @type $division \App\Model\Entity\Division
 * @type $league \App\Model\Entity\League
 */
?>
<tr>
	<th><?= __('Seed') ?></th>
	<th><?= __('Team Name') ?></th>
	<th title="<?= __('Wins') ?>"><?= __x('standings', 'W') ?></th>
	<th title="<?= __('Losses') ?>"><?= __x('standings', 'L') ?></th>
	<th title="<?= __('Ties') ?>"><?= __x('standings', 'T') ?></th>
	<th title="<?= __('Defaults') ?>"><?= __x('standings', 'D') ?></th>
	<th title="<?= __('Goals For') ?>"><?= __x('standings', 'GF') ?></th>
	<th title="<?= __('Goals Against') ?>"><?= __x('standings', 'GA') ?></th>
	<th title="<?= __('Plus/Minus') ?>"><?= __x('standings', '+/-') ?></th>
	<th><?= __('Streak') ?></th>
<?php
if ($league->hasSpirit()):
?>
	<th><?= __('Spirit') ?></th>
<?php
endif;
?>
</tr>
