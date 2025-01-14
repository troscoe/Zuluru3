<?php
namespace App\Model\Table;

use Cake\Database\Query;
use Cake\Validation\Validator;
use App\Core\UserCache;

/**
 * Groups Model
 *
 * @property \Cake\ORM\Association\BelongsToMany $People
 */
class GroupsTable extends AppTable {

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config) {
		parent::initialize($config);

		$this->setTable('groups');
		$this->setDisplayField('name');
		$this->setPrimaryKey('id');

		$this->addBehavior('Trim');
		$this->addBehavior('Translate', ['fields' => ['name', 'description']]);

		$this->belongsToMany('People', [
			'foreignKey' => 'group_id',
			'targetForeignKey' => 'person_id',
			'joinTable' => 'groups_people',
			'saveStrategy' => 'replace',
		]);
	}

	/**
	 * Default validation rules.
	 *
	 * @param \Cake\Validation\Validator $validator Validator instance.
	 * @return \Cake\Validation\Validator
	 */
	public function validationDefault(Validator $validator) {
		$validator
			->numeric('id')
			->allowEmpty('id', 'create')

			->requirePresence('name', 'create')
			->notEmpty('name', __('The name cannot be blank.'))

			->boolean('active')
			->requirePresence('active', 'create')
			->notEmpty('active')

			->numeric('level')
			->requirePresence('level', 'create')
			->notEmpty('level')

			->requirePresence('description', 'create')
			->notEmpty('description')

			;

		return $validator;
	}

	/**
	 * Read the database-based group options.
	 */
	public function findOptions(Query $query, Array $options) {
		$user_cache = UserCache::getInstance();
		$groups = $user_cache->read('Groups');

		$options += ['min_level' => 1, 'require_player' => false];
		if (empty($groups)) {
			$level = $options['min_level'];
		} else {
			$level = max(collection($groups)->max('level')->level, $options['min_level']);
		}

		$query->where(['Groups.level <=' => $level]);
		if (!empty($options['force_players'])) {
			$query->andWhere([
				'OR' => [
					'Groups.id' => GROUP_PLAYER,
					'Groups.active' => true,
				]
			]);
		} else {
			$query->andWhere(['Groups.active' => true]);
		}
		$query->order(['Groups.level', 'Groups.id']);
		return $query->formatResults(function ($results) {
			return $results->combine('id', 'long_name');
		});
	}

	public function mergeList(Array $old, Array $new) {
		// Clear join data from all the new groups
		foreach ($new as $group) {
			unset($group->_joinData);
		}

		// As a special case, deal with anybody who said they are now only a parent, but might have been other things before
		if (count($new) == 1 && $new[0]->id == GROUP_PARENT) {
			foreach ($old as $group) {
				if ($group->id != GROUP_PARENT) {
					unset($group->_joinData);
					$new[] = $group;
				}
			}
		} else {
			// Find any old groups that aren't present in the new list, and copy them over
			foreach ($old as $group) {
				if (!collection($new)->firstMatch(['id' => $group->id])) {
					unset($group->_joinData);
					$new[] = $group;
				}
			}
		}

		return $new;
	}

}
