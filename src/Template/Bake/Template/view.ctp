<%
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
use App\Controller\AppController;
use Cake\Utility\Inflector;

$associations += ['BelongsTo' => [], 'HasOne' => [], 'HasMany' => [], 'BelongsToMany' => []];
$immediateAssociations = $associations['BelongsTo'] + $associations['HasOne'];
$associationFields = collection($fields)
	->map(function ($field) use ($immediateAssociations) {
		foreach ($immediateAssociations as $alias => $details) {
			if ($field === $details['foreignKey']) {
				return [$field => $details];
			}
		}
	})
	->filter()
	->reduce(function ($fields, $value) {
		return $fields + $value;
	}, []);

$pk = "\$$singularVar->{$primaryKey[0]}";
%>
<?php
/**
 * @type $<%= $singularVar %> \App\Model\Entity\<%= $singularHumanName %>
 */

$this->Html->addCrumb(__('<%= $singularHumanName %>'));
$this->Html->addCrumb(h($<%= $singularVar %>-><%= $displayField %>));
$this->Html->addCrumb(__('View'));
?>

<div class="<%= $pluralVar %> view">
	<h2><?= h($<%= $singularVar %>-><%= $displayField %>) ?></h2>
	<dl class="dl-horizontal">
<%
foreach ($fields as $field):
	if (in_array($field, $primaryKey) || $field == $displayField) {
		continue;
	}
	$type = $schema->columnType($field);
	if (isset($associationFields[$field])):
		$details = $associationFields[$field];
%>
		<dt><?= __('<%= Inflector::humanize($details['property']) %>') ?></dt>
		<dd><?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', '<%= $details['property'] %>' => $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>]) : '' ?></dd>
<% else : %>
		<dt><?= __('<%= Inflector::humanize($field) %>') ?></dt>
<% if (in_array($type, ['integer', 'biginteger', 'decimal', 'float'])): %>
		<dd><?= $this->Number->format($<%= $singularVar %>-><%= $field %>) ?></dd>
<% elseif ($type == 'date'): %>
		<dd><?= $this->Time->date($<%= $singularVar %>-><%= $field %>) ?></dd>
<% elseif ($type == 'time'): %>
		<dd><?= $this->Time->time($<%= $singularVar %>-><%= $field %>) ?></dd>
<% elseif (in_array($type, ['datetime', 'timestamp'])): %>
		<dd><?= $this->Time->datetime($<%= $singularVar %>-><%= $field %>) ?></dd>
<% elseif ($type == 'boolean'): %>
		<dd><?= $<%= $singularVar %>-><%= $field %> ? __('Yes') : __('No') ?></dd>
<% elseif ($type == 'text'): %>
		<?= $this->Text->autoParagraph(h($<%= $singularVar %>-><%= $field %>)) ?>
<% else : %>
		<dd><?= h($<%= $singularVar %>-><%= $field %>) ?>&nbsp;</dd>
<% endif; %>
<% endif; %>
<% endforeach; %>
	</dl>
</div>

<div class="actions columns">
	<ul class="nav nav-pills">
<?php
echo $this->Html->tag('li', $this->Html->iconLink('view_32.png',
	['action' => 'index'],
	['alt' => __('List'), 'title' => __('List <%= $pluralHumanName %>')]));
if ($this->Authorize->can('edit', $<%= $singularVar %>)) {
	echo $this->Html->tag('li', $this->Html->iconLink('edit_32.png',
		['action' => 'edit', '<%= $singularVar %>' => <%= $pk %>],
		['alt' => __('Edit'), 'title' => __('Edit <%= $singularHumanName %>')]));
}
if ($this->Authorize->can('delete', $<%= $singularVar %>)) {
	echo $this->Html->tag('li', $this->Form->iconPostLink('delete_32.png',
		['action' => 'delete', '<%= $singularVar %>' => <%= $pk %>],
		['alt' => __('Delete'), 'title' => __('Delete <%= $singularHumanName %>')],
		['confirm' => __('Are you sure you want to delete this <%= strtolower($singularHumanName) %>?')]));
}
?>
	</ul>
</div>
<%
$relations = $associations['HasMany'] + $associations['BelongsToMany'];
foreach ($relations as $alias => $details):
	$otherSingularVar = Inflector::variable(Inflector::singularize($alias));
	$otherPluralHumanName = Inflector::humanize($details['controller']);
	%>
<?php
if (!empty($<%= $singularVar %>-><%= $details['property'] %>)):
?>

<div class="related row">
	<div class="column">
		<h4 class="subheader"><?= __('Related <%= $otherPluralHumanName %>') ?></h4>
		<div class="table-responsive">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
<% foreach ($details['fields'] as $field): %>
<% if ($field != $details['primaryKey'][0]): %>
					<th><?= __('<%= Inflector::humanize($field) %>') ?></th>
<% endif; %>
<% endforeach; %>
					<th class="actions"><?= __('Actions') ?></th>
				</tr>
			</thead>
			<tbody>
<?php
	foreach ($<%= $singularVar %>-><%= $details['property'] %> as $<%= $otherSingularVar %>):
?>
				<tr>
				<%- foreach ($details['fields'] as $field): %>
					<%- if ($field != $details['primaryKey'][0]): %>
					<td><?= h($<%= $otherSingularVar %>-><%= $field %>) ?></td>
					<%- endif; %>
				<%- endforeach; %>
				<%- $otherPk = "\${$otherSingularVar}->{$details['primaryKey'][0]}"; %>
					<td class="actions"><?php
					echo $this->Html->iconLink('view_24.png',
						['controller' => '<%= $details['controller'] %>', 'action' => 'view', '<%= $otherSingularVar %>' => <%= $otherPk %>],
						['alt' => __('View'), 'title' => __('View')]);
					if ($this->Authorize->can('edit', $<%= $otherSingularVar %>)) {
						echo $this->Html->iconLink('edit_24.png',
							['controller' => '<%= $details['controller'] %>', 'action' => 'edit', '<%= $otherSingularVar %>' => <%= $otherPk %>, 'return' => AppController::_return()],
							['alt' => __('Edit'), 'title' => __('Edit')]);
					}
					if ($this->Authorize->can('delete', $<%= $otherSingularVar %>)) {
						echo $this->Form->iconPostLink('delete_24.png',
							['controller' => '<%= $details['controller'] %>', 'action' => 'delete', '<%= $otherSingularVar %>' => <%= $otherPk %>, 'return' => AppController::_return()],
							['alt' => __('Delete'), 'title' => __('Delete')],
							['confirm' => __('Are you sure you want to delete this <%= strtolower($otherSingularVar) %>?')]);
					}
					?></td>
				</tr>

<?php
	endforeach;
?>
			</tbody>
		</table>
		</div>
	</div>
</div>
<?php
endif;
?>
<% endforeach; %>
