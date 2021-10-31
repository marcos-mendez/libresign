<?php

namespace OCA\Libresign\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

/**
 * Class FileElementsMapper
 *
 * @package OCA\Libresign\DB
 *
 * @codeCoverageIgnore
 * @method UserElement insert(UserElement $entity)
 * @method UserElement update(UserElement $entity)
 * @method UserElement insertOrUpdate(UserElement $entity)
 * @method UserElement delete(UserElement $entity)
 */
class UserElementMapper extends QBMapper {
	/** @var UserElement[] */
	private $cache = [];

	/**
	 * @param IDBConnection $db
	 */
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'libresign_user_element');
	}

	/**
	 * @return UserElement[]
	 */
	public function getByUserId($userId): array {
		$qb = $this->db->getQueryBuilder();

		$qb->select('ue.*')
			->from($this->getTableName(), 'ue')
			->where(
				$qb->expr()->eq('ue.user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);

		return $this->findEntities($qb);
	}

	/**
	 * @param integer $elementId
	 * @param string $userId
	 * @return UserElement
	 */
	public function getByElementIdAndUserId(int $elementId, string $userId) {
		if (!isset($this->cache[$elementId])) {
			$qb = $this->db->getQueryBuilder();

			$qb->select('ue.*')
				->from($this->getTableName(), 'ue')
				->where(
					$qb->expr()->eq('ue.user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
				)
				->andWhere(
					$qb->expr()->eq('ue.id', $qb->createNamedParameter($elementId, IQueryBuilder::PARAM_INT))
				);

			$this->cache[$elementId] = $this->findEntity($qb);
		}
		return $this->cache[$elementId];
	}

	public function getById(int $id): UserElement {
		if (!isset($this->cache[$id])) {
			$qb = $this->db->getQueryBuilder();

			$qb->select('ue.*')
				->from($this->getTableName(), 'ue')
				->where(
					$qb->expr()->eq('ue.id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
				);

			$this->cache[$id] = $this->findEntity($qb);
		}
		return $this->cache[$id];
	}
}