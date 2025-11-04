<?php

namespace App\Repository;

use App\Entity\ComicChapterProvider;
use App\Model\OrderByDto;
use App\Util\Href;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ComicChapterProvider>
 */
class ComicChapterProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ComicChapterProvider::class);
    }

    public function findByCustom(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $query = $this->createQueryBuilder('c')
            ->leftJoin('c.chapter', 'cc')->addSelect('cc')
            ->leftJoin('cc.comic', 'ccc')->addSelect('ccc')
            ->leftJoin('c.link', 'cl')->addSelect('cl')
            ->leftJoin('cl.website', 'clw')->addSelect('clw')
            ->leftJoin('c.language', 'cla')->addSelect('cla');

        foreach ($criteria as $key => $val) {
            $val = \array_unique($val);

            switch ($key) {
                case 'chapterComicCodes':
                    $c = \count($val);
                    if ($c < 1) break;

                    if ($c == 1) {
                        $query->andWhere('ccc.code = :comicCode');
                        $query->setParameter('comicCode', $val[0]);
                        break;
                    }
                    $query->andWhere('ccc.code IN (:comicCodes)');
                    $query->setParameter('comicCodes', $val);
                    break;
                case 'chapterNumbers':
                    $c = \count($val);
                    if ($c < 1) break;

                    if ($c == 1) {
                        $query->andWhere('cc.number = :chapterNumber');
                        $query->setParameter('chapterNumber', $val[0]);
                        break;
                    }
                    $query->andWhere('cc.number IN (:chapterNumbers)');
                    $query->setParameter('chapterNumbers', $val);
                    break;
                case 'chapterVersions':
                    $c = \count($val);
                    if ($c < 1) break;

                    if ($c == 1) {
                        $query->andWhere('cc.version = :chapterVersion');
                        $query->setParameter('chapterVersion', $val[0]);
                        break;
                    }
                    $query->andWhere('cc.version IN (:chapterVersions)');
                    $query->setParameter('chapterVersions', $val);
                    break;
                case 'linkWebsiteHosts':
                    $c = \count($val);
                    if ($c < 1) break;

                    if ($c == 1) {
                        $query->andWhere('clw.host = :linkWebsiteHost');
                        $query->setParameter('linkWebsiteHost', $val[0]);
                        break;
                    }
                    $query->andWhere('clw.host IN (:linkWebsiteHosts)');
                    $query->setParameter('linkWebsiteHosts', $val);
                    break;
                case 'linkRelativeReferences':
                    $c = \count($val);
                    if ($c < 1) break;

                    if ($c == 1) {
                        $query->andWhere('cl.relativeReference = :linkRelativeReference');
                        $query->setParameter('linkRelativeReference', $val[0]);
                        break;
                    }
                    $query->andWhere('cl.relativeReference IN (:linkRelativeReferences)');
                    $query->setParameter('linkRelativeReferences', $val);
                    break;
                case 'linkHREFs':
                    $c = \count($val);
                    if ($c < 1) break;

                    $qExOr = $query->expr()->orX();
                    foreach ($val as $k => $v) {
                        $href = new Href($v);

                        $qExOr->add('clw.host = :linkHREFA' . $k . ' AND ' . 'cl.relativeReference = :linkHREFB' . $k);
                        $query->setParameter('linkHREFA' . $k, $href->getHost());
                        $query->setParameter('linkHREFB' . $k, $href->getRelativeReference() ?? '');
                    }
                    $query->andWhere($qExOr);
                    break;
                case 'languageLangs':
                    $c = \count($val);
                    if ($c < 1) break;

                    if ($c == 1) {
                        $query->andWhere('cla.lang = :languageLang');
                        $query->setParameter('languageLang', $val[0]);
                        break;
                    }
                    $query->andWhere('cla.lang IN (:languageLangs)');
                    $query->setParameter('languageLangs', $val);
                    break;
            }
        }

        if ($orderBy) {
            foreach ($orderBy as $key => $val) {
                if (!($val instanceof OrderByDto)) continue;

                if ($key > 11) break;

                switch ($val->name) {
                    case 'chapterComicCode':
                        $val->name = 'ccc.code';
                        break;
                    case 'chapterNumbers':
                        $val->name = 'cc.number';
                        break;
                    case 'chapterVersions':
                        $val->name = 'cc.version';
                        break;
                    case 'linkWebsiteHost':
                        $val->name = 'clw.host';
                        break;
                    case 'linkWebsiteName':
                        $val->name = 'clw.name';
                        break;
                    case 'linkRelativeReference':
                        $val->name = 'cl.relativeReference';
                        break;
                    case 'languageLang':
                        $val->name = 'cla.lang';
                        break;
                    case 'createdAt':
                    case 'updatedAt':
                    case 'ulid':
                    case 'releasedAt':
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

                switch ($val->name) {
                    case 'cl.lang':
                        $query->addOrderBy($val->name, $val->order);

                        if (isset($val->custom['prefer'])) {
                            if ($val->custom['prefer'] == '') {
                                break;
                            }
                            $vname = 'languageLangPrefer' . $key;
                            $vvals = \explode('+', $val->custom['prefer']);
                            $vselc = '(CASE';
                            foreach ($vvals as $k => $v) {
                                $v = \str_replace(['_', '%'], '', $v);

                                $vselc .= ' WHEN cla.lang LIKE :' . $vname . $k;
                                $vselc .= ' THEN ' . (\count($vvals) - $k);
                                $query->setParameter($vname . $k, $v . '%');
                            }
                            $vselc .= ' ELSE 0 END) AS HIDDEN ' . $vname;

                            $query->addSelect($vselc);
                            $query->addOrderBy($vname, 'DESC');
                            break;
                        }

                        break;
                    default:
                        $query->addOrderBy($val->name, $val->order);
                }
            }
        } else {
            $query->orderBy('c.ulid');
        }

        $query->setMaxResults($limit);
        $query->setFirstResult($offset);

        return $query->setCacheable(true)->getQuery()->getResult();
    }

    public function countCustom(array $criteria = []): int
    {
        $query = $this->createQueryBuilder('c')
            ->select('count(c.id)');

        $q01 = false;
        $q01Func = function (bool &$c, QueryBuilder &$q): void {
            if ($c) return;
            $q->leftJoin('c.chapter', 'cc');
            $c = true;
        };
        $q011 = false;
        $q011Func = function (bool &$c, QueryBuilder &$q): void {
            if ($c) return;
            $q->leftJoin('cc.comic', 'ccc');
            $c = true;
        };
        $q02 = false;
        $q02Func = function (bool &$c, QueryBuilder &$q): void {
            if ($c) return;
            $q->leftJoin('c.link', 'cl');
            $c = true;
        };
        $q03 = false;
        $q03Func = function (bool &$c, QueryBuilder &$q): void {
            if ($c) return;
            $q->leftJoin('cl.website', 'clw');
            $c = true;
        };
        $q04 = false;
        $q04Func = function (bool &$c, QueryBuilder &$q): void {
            if ($c) return;
            $q->leftJoin('c.language', 'cla');
            $c = true;
        };

        foreach ($criteria as $key => $val) {
            $val = \array_unique($val);

            switch ($key) {
                case 'chapterComicCodes':
                    $c = \count($val);
                    if ($c < 1) break;

                    $q01Func($q01, $query);
                    $q011Func($q011, $query);

                    if ($c == 1) {
                        $query->andWhere('ccc.code = :chapterComicCode');
                        $query->setParameter('chapterComicCode', $val[0]);
                        break;
                    }
                    $query->andWhere('ccc.code IN (:chapterComicCodes)');
                    $query->setParameter('chapterComicCodes', $val);
                    break;
                case 'chapterNumbers':
                    $c = \count($val);
                    if ($c < 1) break;

                    $q01Func($q01, $query);

                    if ($c == 1) {
                        $query->andWhere('cc.number = :chapterNumber');
                        $query->setParameter('chapterNumber', $val[0]);
                        break;
                    }
                    $query->andWhere('cc.number IN (:chapterNumbers)');
                    $query->setParameter('chapterNumbers', $val);
                    break;
                case 'chapterVersions':
                    $c = \count($val);
                    if ($c < 1) break;

                    $q01Func($q01, $query);

                    if ($c == 1) {
                        $query->andWhere('cc.version = :chapterVersion');
                        $query->setParameter('chapterVersion', $val[0]);
                        break;
                    }
                    $query->andWhere('cc.version IN (:chapterVersions)');
                    $query->setParameter('chapterVersions', $val);
                    break;
                case 'linkWebsiteHosts':
                    $c = \count($val);
                    if ($c < 1) break;

                    $q02Func($q02, $query);
                    $q03Func($q03, $query);

                    if ($c == 1) {
                        $query->andWhere('clw.host = :linkWebsiteHost');
                        $query->setParameter('linkWebsiteHost', $val[0]);
                        break;
                    }
                    $query->andWhere('clw.host IN (:linkWebsiteHosts)');
                    $query->setParameter('linkWebsiteHosts', $val);
                    break;
                case 'linkRelativeReferences':
                    $c = \count($val);
                    if ($c < 1) break;

                    $q02Func($q02, $query);

                    if ($c == 1) {
                        $query->andWhere('cl.relativeReference = :linkRelativeReference');
                        $query->setParameter('linkRelativeReference', $val[0]);
                        break;
                    }
                    $query->andWhere('cl.relativeReference IN (:linkRelativeReferences)');
                    $query->setParameter('linkRelativeReferences', $val);
                    break;
                case 'linkHREFs':
                    $c = \count($val);
                    if ($c < 1) break;

                    $q02Func($q02, $query);
                    $q03Func($q03, $query);

                    $qExOr = $query->expr()->orX();
                    foreach ($val as $k => $v) {
                        $href = new Href($v);

                        $qExOr->add('clw.host = :linkHREFA' . $k . ' AND ' . 'cl.relativeReference = :linkHREFB' . $k);
                        $query->setParameter('linkHREFA' . $k, $href->getHost());
                        $query->setParameter('linkHREFB' . $k, $href->getRelativeReference() ?? '');
                    }
                    $query->andWhere($qExOr);
                    break;
                case 'languageLangs':
                    $c = \count($val);
                    if ($c < 1) break;

                    $q04Func($q04, $query);

                    if ($c == 1) {
                        $query->andWhere('cla.lang = :languageLang');
                        $query->setParameter('languageLang', $val[0]);
                        break;
                    }
                    $query->andWhere('cla.lang IN (:languageLangs)');
                    $query->setParameter('languageLangs', $val);
                    break;
            }
        }

        return $query->getQuery()->getSingleScalarResult();
    }
}
