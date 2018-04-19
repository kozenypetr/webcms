<?php

namespace CmsBundle\Repository;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;


/**
 * DocumentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DocumentRepository extends NestedTreeRepository
{
    public function getNodesHierarchyQueryBuilder($node = null, $direct = false, array $options = array(), $includeNode = false)
    {
        $qb = parent::getNodesHierarchyQueryBuilder($node, $direct, $options, $includeNode);

        $qb->join('node.category', 'c');

        $qb->addSelect('c');

        return $qb;
    }

    public function getChildrenWithSystemWidget($node, $limit)
    {
        $qb = parent::childrenQueryBuilder($node);

        $qb->join('node.widgets', 'w');

        $qb->orderBy('node.lft');

        $qb->setMaxResults($limit);
        // $qb->andWhere('node.categoryId = ' . $category);

        $qb->andWhere('w.isSystem = 1');

        $qb->addSelect('w');

        return $qb->getQuery()->execute();
    }


    public function findSystemWidget($document)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT w FROM CmsBundle:Widget w
                     WHERE w.document = :document AND w.isSystem = 1'
            )->setParameter('document', $document);


        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

}
