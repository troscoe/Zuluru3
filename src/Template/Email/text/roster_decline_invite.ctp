<?php
use Cake\Core\Configure;
use Cake\Routing\Router;

/**
 * @type $person \App\Model\Entity\Person
 * @type $team \App\Model\Entity\Team
 * @type $captains string
 */
?>

<?= __('Dear {0},', $captains) ?>


<?= __('Your invitation for {0} to join the roster of the {1} team {2} has been declined.',
	$person->full_name,
	Configure::read('organization.name'),
	$team->name
) ?>


<?= __('The {0} roster may be accessed at', $team->name) ?>

<?= Router::url(['controller' => 'Teams', 'action' => 'view', 'team' => $team->id], true) ?>


<?= __('You need to be logged into the website to update this.') ?>


<?= $this->element('Email/text/footer');
