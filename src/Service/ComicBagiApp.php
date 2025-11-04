<?php

namespace App\Service;

use App\Model\OrderByDto;
use App\Repository\ComicChapterProviderRepository;
use App\Repository\ComicChapterRepository;
use App\Repository\ComicProviderRepository;
use App\Repository\LanguageRepository;
use App\Util\StringUtil;

class ComicBagiApp
{
    public function __construct(
        private readonly LanguageRepository $languageRepository,
        private readonly ComicProviderRepository $comicProviderRepository,
        private readonly ComicChapterRepository $comicChapterRepository,
        private readonly ComicChapterProviderRepository $comicChapterProviderRepository
    ) {}

    public function getLanguages(
        ?int $limit = null
    ): array {
        return $this->languageRepository->findByCustom(
            [],
            [],
            $limit
        );
    }

    public function getComicProviders(
        string $comicCode,
        ?array $langs = [],
        ?int $limit = null,
        ?array $customUnredacts = []
    ): array {
        if (!$langs) {
            $langs = ['en'];
        }

        $result = $this->comicProviderRepository->findByCustom(
            ['comicCodes' => [$comicCode]],
            [
                new OrderByDto('languageLang', custom: [
                    'prefer' => \implode('+', $langs)
                ]),
                new OrderByDto('linkWebsiteName'),
                new OrderByDto('linkWebsiteHost'),
                new OrderByDto('linkRelativeReference')
            ],
            $limit
        );

        foreach ($result as $v) {
            $v1 = $v->getLink()->getWebsite();
            if ($v1->isRedacted() && !\in_array($v1->getHost(), $customUnredacts)) {
                $v1->setHost(StringUtil::redact($v1->getHost(), 2, ['.']));
                $v1->setName(StringUtil::redact($v1->getName(), 2));
            } else {
                $v1->setRedacted(false);
            }
        }

        return $result;
    }

    public function getComicChapters(
        string $comicCode,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        return $this->comicChapterRepository->findByCustom(
            ['comicCodes' => [$comicCode]],
            [
                new OrderByDto('number', 'desc'),
                new OrderByDto('version')
            ],
            $limit,
            $offset
        );
    }

    public function getComicChapterProviders(
        string $comicCode,
        string $chapterNumber,
        ?string $chapterVersion,
        ?array $langs = [],
        ?int $limit = null,
        ?array $customUnredacts = []
    ): array {
        if (!$langs) {
            $langs = ['en'];
        }

        $result = $this->comicChapterProviderRepository->findByCustom(
            [
                'chapterComicCodes' => [$comicCode],
                'chapterNumbers' => [$chapterNumber],
                'chapterVersions' => [$chapterVersion ?? '']
            ],
            [
                new OrderByDto('languageLang', custom: [
                    'prefer' => \implode('+', $langs)
                ]),
                new OrderByDto('linkWebsiteName'),
                new OrderByDto('linkWebsiteHost'),
                new OrderByDto('linkRelativeReference')
            ],
            $limit
        );

        foreach ($result as $v) {
            $v1 = $v->getLink()->getWebsite();
            if ($v1->isRedacted() && !\in_array($v1->getHost(), $customUnredacts)) {
                $v1->setHost(StringUtil::redact($v1->getHost(), 2, ['.']));
                $v1->setName(StringUtil::redact($v1->getName(), 2));
            } else {
                $v1->setRedacted(false);
            }
        }

        return $result;
    }

    public function getRecommendedLangs(
        array $curLangs,
        array $priLangs
    ): array {
        $recLangs = [];

        foreach ($priLangs as $lang) {
            if (!\in_array($lang, $curLangs)) {
                continue;
            }

            \array_push($recLangs, $lang);
        }

        foreach ($priLangs as $lang) {
            if (!\str_contains($lang, '-')) {
                continue;
            }

            $langs = [];

            $langc = '';
            foreach (\explode('-', $lang) as $langPart) {
                if ($langc) {
                    $langc .= '-';
                }

                $langc .= $langPart;

                if (!\in_array($langc, $recLangs) && \in_array($langc, $curLangs)) {
                    \array_push($langs, $langc);
                }
            }

            \array_push($recLangs, ...\array_reverse($langs));
        }

        return $recLangs;
    }

    public function getHREF(
        string $websiteHost,
        ?string $relativeReference = null
    ): string {
        $href = '';

        if ($websiteHost) {
            $href .= '//' . $websiteHost;
        }

        return $href . $relativeReference;
    }
}
