<?php

namespace CmsBundle\Repository;

use CmsBundle\Entity\Widget;
use CmsBundle\Entity\Document;

/**
 * WidgetRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class WidgetRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Vytvori objekt widget na zaklade parametru - region, document_id
     * prev - ID predchoziho widgetu v regionu
     * next - ID nasledujiciho widgetu v regionu
     * @param array() $parameters
     * @return \CmsBundle\Entity\Widget
     */
    public function createWidget($parameters)
    {
        $em = $this->getEntityManager();

        // nacteni dokumentu
        $document = $em->getRepository(Document::class)->find((int)$parameters['document_id']);

        // nastavime razeni novemu boxu
        $sort = $this->sortWidgetsInRegion(
                        $parameters['region'],
                        $parameters['document_id'],
                        isset($parameters['prev'])?$parameters['prev']:null,
                        isset($parameters['next'])?$parameters['next']:null
                );

        // vytvoreni widgetu
        $widget = new Widget();
        $widget->setTag('div');
        $widget->setHtml('');
        $widget->setSort($sort);
        $widget->setRegion($parameters['region']);
        $widget->setService($parameters['widget']);

        // pokud se jedna o region spojeny se strankou, tak pridame stranku
        if (preg_match('/^page\./', $parameters['region']))
        {
            $widget->setDocument($document);
            // dump($document);
        }

        return $widget;
    }

    /**
     * Vytvori objekt widget na zaklade parametru - region, document_id
     * prev - ID predchoziho widgetu v regionu
     * next - ID nasledujiciho widgetu v regionu
     * @param array() $parameters
     * @return \CmsBundle\Entity\Widget
     */
    public function copyWidget($parameters, $copyWidget)
    {
        $em = $this->getEntityManager();

        // nacteni dokumentu
        $document = $em->getRepository(Document::class)->find((int)$parameters['document_id']);

        // nastavime razeni novemu boxu
        $sort = $this->sortWidgetsInRegion(
            $parameters['region'],
            $parameters['document_id'],
            isset($parameters['prev'])?$parameters['prev']:null,
            isset($parameters['next'])?$parameters['next']:null
        );

        // vytvoreni widgetu
        $widget = new Widget();
        $widget->setTag($copyWidget->getTag());
        $widget->setHtml('');
        $widget->setSort($sort);
        $widget->setRegion($parameters['region']);
        $widget->setService($copyWidget->getService());
        $widget->setSid($copyWidget->getSid());
        $widget->setSubclass($copyWidget->getSubclass());
        $widget->setParameters($copyWidget->getParameters());

        // pokud se jedna o region spojeny se strankou, tak pridame stranku
        if (preg_match('/^page\./', $parameters['region']))
        {
            $widget->setDocument($document);
        }

        return $widget;
    }

    /**
     * Razeni widgetu v regionu
     * @param string $region
     * @param int $documentId
     * @param int $prevWidgetId
     * @param int $nextWidgetId
     * @return int $sort
     */
    public function sortWidgetsInRegion($region, $documentId, $prevWidgetId, $nextWidgetId)
    {
        // urcite razeni pro vlozeny widget
        $sort = 1;
        if ($prevWidgetId)
        {
            // nacteme objekt predchoziho widgetu a udelame inkrement
            $sort = $this->find($prevWidgetId)->getSort() + 1;
        }

        // posuneme nasledujici widgety
        if ($nextWidgetId)
        {
            // nacteme objekt nasledujiciho widgetu
            $nextWidget = $this->find((int)$nextWidgetId);

            // pokud je potreba inkrement, tak razeni vsech dalsich widgetu dame + 1
            if ($nextWidget && $nextWidget->getSort() == $sort)
            {
                $qb = $this->getEntityManager()->createQueryBuilder();
                // aktualizace razeni
                $qb->update('CmsBundle\Entity\Widget', 'w')
                    ->set('w.sort', 'w.sort + 1')
                    ->where('w.region = ?1')
                    ->andWhere('w.sort >= ?2');

                $qb->setParameter(1, $region)
                   ->setParameter(2, $nextWidget->getSort());
                // pokud se jedna o region,
                if ($documentId && preg_match('/^page\./', $region))
                {
                    $qb->andWhere('w.document = ?3')
                       ->setParameter(3, $documentId);
                }

                $q = $qb->getQuery();

                $q->execute();
            }
        }

        return $sort;
    }
}
