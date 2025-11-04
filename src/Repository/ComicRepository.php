<?php

namespace App\Repository;

use App\Entity\Comic;
use App\Model\OrderByDto;
use App\Util\Href;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comic>
 */
class ComicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comic::class);
    }

    public function findByCustom(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $query = $this->createQueryBuilder('c');

        $q1 = false;
        $q1Func = function (bool &$c, QueryBuilder &$q): void {
            if ($c) return;
            $q->leftJoin('c.providers', 'cp2');
            $c = true;
        };
        $q11 = false;
        $q11Func = function (bool &$c, QueryBuilder &$q): void {
            if ($c) return;
            $q->leftJoin('cp2.link', 'cp2l');
            $c = true;
        };
        $q111 = false;
        $q111Func = function (bool &$c, QueryBuilder &$q): void {
            if ($c) return;
            $q->leftJoin('cp2l.website', 'cp2lw');
            $c = true;
        };
        $q12 = false;
        $q12Func = function (bool &$c, QueryBuilder &$q): void {
            if ($c) return;
            $q->leftJoin('cp2.language', 'cp2la');
            $c = true;
        };
        $q2 = false;
        $q2Func = function (bool &$c, QueryBuilder &$q): void {
            if ($c) return;
            $q->leftJoin('c.chapters', 'cc2');
            $c = true;
        };

        $qZ = false;
        $qZFunc = function (bool &$c, QueryBuilder &$q): void {
            if ($c) return;
            $q->addGroupBy('c.id');
            $c = true;
        };

        foreach ($criteria as $key => $val) {
            $val = \array_unique($val);

            switch ($key) {
                case 'providerLinkWebsiteHosts':
                    $c = \count($val);
                    if ($c < 1) break;

                    $q1Func($q1, $query);
                    $q11Func($q11, $query);
                    $q111Func($q111, $query);
                    $qZFunc($qZ, $query);

                    if ($c == 1) {
                        $query->andWhere('cp2lw.host = :providerLinkWebsiteHost');
                        $query->setParameter('providerLinkWebsiteHost', $val[0]);
                        break;
                    }
                    $query->andWhere('cp2lw.host IN (:providerLinkWebsiteHosts)');
                    $query->setParameter('providerLinkWebsiteHosts', $val);
                    break;
                case 'providerLinkRelativeReferences':
                    $c = \count($val);
                    if ($c < 1) break;

                    $q1Func($q1, $query);
                    $q11Func($q11, $query);
                    $qZFunc($qZ, $query);

                    if ($c == 1) {
                        $query->andWhere('cp2l.relativeReference = :providerLinkRelativeReference');
                        $query->setParameter('providerLinkRelativeReference', $val[0]);
                        break;
                    }
                    $query->andWhere('cp2l.relativeReference IN (:providerLinkRelativeReferences)');
                    $query->setParameter('providerLinkRelativeReferences', $val);
                    break;
                case 'providerLinkHREFs':
                    $c = \count($val);
                    if ($c < 1) break;

                    $q1Func($q1, $query);
                    $q11Func($q11, $query);
                    $q111Func($q111, $query);
                    $qZFunc($qZ, $query);

                    $qExOr = $query->expr()->orX();
                    foreach ($val as $k => $v) {
                        $href = new Href($v);

                        $qExOr->add('cp2lw.host = :providerLinkHREFA' . $k . ' AND ' . 'cp2l.relativeReference = :providerLinkHREFB' . $k);
                        $query->setParameter('providerLinkHREFA' . $k, $href->getHost());
                        $query->setParameter('providerLinkHREFB' . $k, $href->getRelativeReference() ?? '');
                    }
                    $query->andWhere($qExOr);
                    break;
                case 'providerLanguageLangs':
                    $c = \count($val);
                    if ($c < 1) break;

                    $q1Func($q1, $query);
                    $q12Func($q12, $query);

                    if ($c == 1) {
                        $query->andWhere('cp2la.lang = :providerLanguageLang');
                        $query->setParameter('providerLanguageLang', $val[0]);
                        break;
                    }
                    $query->andWhere('cp2la.lang IN (:providerLanguageLangs)');
                    $query->setParameter('providerLanguageLangs', $val);
                    break;
            }
        }

        if ($orderBy) {
            foreach ($orderBy as $key => $val) {
                if (!($val instanceof OrderByDto)) continue;

                if ($key > 4) break;

                switch ($val->name) {
                    case 'chapterCreatedAt':
                        $q2Func($q2, $query);
                        $qZFunc($qZ, $query);
                        $val->name = 'cc2.createdAt';
                        break;
                    case 'createdAt':
                    case 'updatedAt':
                    case 'code':
                        $val->name = 'c.' . $val->name;
                        break;
                    default:
                        continue 2;
                }

                switch (\strtolower($val->order ?? '')) {
                    case 'a':
                    case 'asc':
                    case 'ascending':
                        $val->order = 'ASC';
                        break;
                    case 'd':
                    case 'desc':
                    case 'descending':
                        $val->order = 'DESC';
                        break;
                    default:
                        $val->order = null;
                }

                switch (\strtolower($val->nulls ?? '')) {
                    case 'f':
                    case 'first':
                        $val->nulls = 'DESC';
                        break;
                    case 'l':
                    case 'last':
                        $val->nulls = 'ASC';
                        break;
                    default:
                        $val->nulls = null;
                }

                if ($val->nulls) {
                    $vname = \str_replace('.', '', $val->name . $key);
                    $vselc = '(CASE WHEN ' . $val->name . ' IS NULL THEN 1 ELSE 0 END) AS HIDDEN ' . $vname;

                    $query->addSelect($vselc);
                    $query->addOrderBy($vname, $val->nulls);
                }

                $query->addOrderBy($val->name, $val->order);
            }
        } else {
            $query->orderBy('c.code');
        }

        $query->setMaxResults($limit);
        $query->setFirstResult($offset);

        return $query->setCacheable(true)->getQuery()->getResult();
    }

    public function countCustom(array $criteria = []): int
    {
        $query = $this->createQueryBuilder('c')
            ->select('count(c.id)');

        $q1 = false;
        $q1Func = function (bool &$c, QueryBuilder &$q): void {
            if ($c) return;
            $q->leftJoin('c.providers', 'cp2');
            $c = true;
        };
        $q11 = false;
        $q11Func = function (bool &$c, QueryBuilder &$q): void {
            if ($c) return;
            $q->leftJoin('cp2.link', 'cp2l');
            $c = true;
        };
        $q111 = false;
        $q111Func = function (bool &$c, QueryBuilder &$q): void {
            if ($c) return;
            $q->leftJoin('cp2l.website', 'cp2lw');
            $c = true;
        };
        $q12 = false;
        $q12Func = function (bool &$c, QueryBuilder &$q): void {
            if ($c) return;
            $q->leftJoin('cp2.language', 'cp2la');
            $c = true;
        };

        foreach ($criteria as $key => $val) {
            $val = \array_unique($val);

            switch ($key) {

                case 'providerLinkWebsiteHosts':
                    $c = \count($val);
                    if ($c < 1) break;

                    $q1Func($q1, $query);
                    $q11Func($q11, $query);
                    $q111Func($q111, $query);

                    if ($c == 1) {
                        $query->andWhere('cp2lw.host = :providerLinkWebsiteHost');
                        $query->setParameter('providerLinkWebsiteHost', $val[0]);
                        break;
                    }
                    $query->andWhere('cp2lw.host IN (:providerLinkWebsiteHosts)');
                    $query->setParameter('providerLinkWebsiteHosts', $val);
                    break;
                case 'providerLinkRelativeReferences':
                    $c = \count($val);
                    if ($c < 1) break;

                    $q1Func($q1, $query);
                    $q11Func($q11, $query);

                    if ($c == 1) {
                        $query->andWhere('cp2l.relativeReference = :providerLinkRelativeReference');
                        $query->setParameter('providerLinkRelativeReference', $val[0]);
                        break;
                    }
                    $query->andWhere('cp2l.relativeReference IN (:providerLinkRelativeReferences)');
                    $query->setParameter('providerLinkRelativeReferences', $val);
                    break;
                case 'providerLinkHREFs':
                    $c = \count($val);
                    if ($c < 1) break;

                    $q1Func($q1, $query);
                    $q11Func($q11, $query);
                    $q111Func($q111, $query);

                    $qExOr = $query->expr()->orX();
                    foreach ($val as $k => $v) {
                        $href = new Href($v);

                        $qExOr->add('cp2lw.host = :providerLinkHREFA' . $k . ' AND ' . 'cp2l.relativeReference = :providerLinkHREFB' . $k);
                        $query->setParameter('providerLinkHREFA' . $k, $href->getHost());
                        $query->setParameter('providerLinkHREFB' . $k, $href->getRelativeReference() ?? '');
                    }
                    $query->andWhere($qExOr);
                    break;
                case 'providerLanguageLangs':
                    $c = \count($val);
                    if ($c < 1) break;

                    $q1Func($q1, $query);
                    $q12Func($q12, $query);

                    if ($c == 1) {
                        $query->andWhere('cp2la.lang = :providerLanguageLang');
                        $query->setParameter('providerLanguageLang', $val[0]);
                        break;
                    }
                    $query->andWhere('cp2la.lang IN (:providerLanguageLangs)');
                    $query->setParameter('providerLanguageLangs', $val);
                    break;
            }
        }

        return $query->getQuery()->getSingleScalarResult();
    }
}
