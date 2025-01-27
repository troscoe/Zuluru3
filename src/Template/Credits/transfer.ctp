<?php
/**
 * @type $credit \App\Model\Entity\Credit
 * @type $relatives \App\Model\Entity\Person[]
 * @type $captains \App\Model\Entity\Person[]
 */

$this->Html->addCrumb(__('Credit'));
$this->Html->addCrumb($credit->person->full_name);
$this->Html->addCrumb(__('Transfer'));
?>

<div class="credits form">
	<h2><?= __('Transfer Credit') ?></h2>
	<?= $this->Text->autoParagraph($credit->notes) ?>
	<p><?= __('This credit has {0} remaining to be spent.', $this->Number->currency($credit->balance)) ?></p>
	<p class="warning-message"><?= __('By transferring this credit, you are relinquishing any claim to it. It <strong>may</strong> be possible to reclaim it in the event of a mistaken transfer, but this is not guaranteed.') ?>

<?php
if (!empty($relatives)):
?>
	<h3><?= __('Relatives') ?></h3>
<?php
	$items = [];
	foreach ($relatives as $relative) {
		$items[] = $this->element('People/block', ['person' => $relative]) . ' ' .
			$this->Html->iconLink('move_24.png', ['controller' => 'Credits', 'action' => 'transfer', 'credit' => $credit->id, 'person' => $relative->id],
				['alt' => __('Transfer Credit'), 'title' => __('Transfer')],
				['confirm' => __('Are you sure you want to transfer this credit to this person?')]
			);
	}
	echo $this->Html->nestedList($items);
endif;
?>

<?php
if (!empty($captains)):
?>
	<h3><?= __('Captains') ?></h3>
<?php
	$items = [];
	foreach ($captains as $id => $captain) {
		$items[] = $this->element('People/block', ['person' => $captain]) . ' ' .
			$this->Html->iconLink('move_24.png', ['controller' => 'Credits', 'action' => 'transfer', 'credit' => $credit->id, 'person' => $captain->id],
				['alt' => __('Transfer Credit'), 'title' => __('Transfer')],
				['confirm' => __('Are you sure you want to transfer this credit to this person?')]
			);
	}
	echo $this->Html->nestedList($items);
endif;
?>

	<h3><?= __('Search') ?></h3>
<?= $this->element('People/search_form', ['affiliates' => collection($this->UserCache->read('Affiliates'))->combine('id', function ($entity) { return $entity->translateField('name'); })->toArray()]) ?>

	<div id="SearchResults" class="zuluru_pagination">

<?= $this->element('People/search_results', [
	'extra_url' => [
		$this->Html->iconImg('move_24.png', ['alt' => __('Transfer Credit'), 'title' => __('Transfer')]) => [
			'controller' => 'Credits', 'action' => 'transfer', 'credit' => $credit->id, 'return' => false,
			'link_opts' => ['escape' => false, 'class' => 'icon', 'confirm' => __('Are you sure you want to transfer this credit to this person?')]
		],
	]
])
?>

	</div>
</div>
