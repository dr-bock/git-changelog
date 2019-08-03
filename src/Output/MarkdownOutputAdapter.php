<?php

/***
 * This file is part of the dr-bock/git-changelog package.
 *
 * (c) 2019 Nikolaj Wojtkowiak-Pfänder <nwp@dr-bock.dev>
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 *
 ***/

declare(strict_types = 1);
namespace DrBock\GitChangeLog\Output;

use DrBock\GitChangeLog\Git\Commit;
use DrBock\GitChangeLog\Helper\IssueFormatHelper;

/**
 * Class MarkdownOutputAdapter
 * @package DrBock\GitChangeLog\Output
 */
class MarkdownOutputAdapter extends AbstractOutputAdapter
{
    /**
     * Format, used to set output option
     */
    public const FORMAT = 'markdown';

    /**
     * String, the release title will be prefixed in detail view
     */
    private const RELEASE_TITLE_PREFIX = '## RELEASE ';

    /**
     * @var string
     */
    protected $fileExtension = 'md';

    /**
     * @return string
     * @throws \Exception
     */
    public function getFormattedChangelog(): string
    {
        $finalOutput = self::RELEASE_TITLE_PREFIX . $this->getGeneratorOptions()->getReleaseDate()->format('Y-m-d');
        if ($this->getGeneratorOptions()->getReleaseName() !== '') {
            $finalOutput .= ' : ' . $this->getGeneratorOptions()->getReleaseName();
        }
        $finalOutput .= "\n\n";
        $finalOutput .= $this->renderSection(Commit::TYPE_NEW);
        $finalOutput .= $this->renderSection(Commit::TYPE_CHANGED);
        $finalOutput .= $this->renderSection(Commit::TYPE_FIXED);
        $finalOutput .= $this->renderSection(Commit::TYPE_REMOVED);
        $finalOutput .= $this->renderSection(Commit::TYPE_REFACTORED);
        $finalOutput .= $this->renderSection(Commit::TYPE_DEPRECATED);
        $finalOutput .= $this->renderSection(Commit::TYPE_OTHER);

        return $finalOutput;
    }

    /**
     * @return string
     */
    public function getFormattedIndex(): string
    {
        $latestReleaseName = '';
        $releaseList = '';
        if ($fileIndex = $this->getFileIndex()) {
            foreach (array_reverse($fileIndex) as $fileName) {
                $link = $this->calculateLink($this->getGeneratorOptions()->getChangelogIndexFile(), $fileName);
                $releaseName = str_replace(self::RELEASE_TITLE_PREFIX, '', trim(fgets(fopen($fileName, 'rb'))));
                $releaseList .= '* [' . $releaseName . '](' . $link . ')' . "\n";
                $latestReleaseName = $latestReleaseName ?: $releaseName;
            }
        }
        $finalOutput  = '## Changelog ' . $this->getGeneratorOptions()->getProjectName() . "\n";
        $finalOutput .= '#### Latest Release: ' . $latestReleaseName . "\n";
        $finalOutput .= 'All previous release changelogs:' . "\n\n";
        $finalOutput .= $releaseList;
        return $finalOutput;
    }

    /**
     * @param $type
     * @return string
     * @throws \Exception
     */
    protected function renderSection($type): string
    {
        $output = '';
        if (count($this->groupedCommits[$type]) > 0) {
            $output = '### ' . self::TITLE_MAPPING[$type] . "\n\n";
            /** @var Commit $commit */
            foreach ($this->groupedCommits[$type] as $commit) {

                /* Template */
                $template  = '- %TICKETNO%%SUBJECT% *([%SHORT_HASH%](%COMMIT_URL%))*' . "\n";
                $template .= "\n";
                $template .= '%BODY%';

                /* Template Variables */
                $ticketNo = $commit->getTicketNo() ? '[' . $commit->getTicketNo() . '] ' : '';
                $subject = $commit->getSubject();
                $shortHash = $commit->getShortHash();
                $commitUrl = str_replace(
                    ['%SHORT_HASH%', '%HASH%'],
                    [$commit->getShortHash(), $commit->getHash()],
                    $this->getGeneratorOptions()->getCommitUrl()
                );
                $body = "\n";
                if ($commit->getBody()) {
                    /* Remove Squashed Text */
                    $body = $this->cutStringAtString('Squashed commit', $commit->getBody());
                    /* Remove Conflicts */
                    $body = $this->cutStringAtString('# Conflicts', $body);
                    $body = "\t" . str_replace("\n", "  \n\t", trim($body));
                    $body .= "\n\n";
                }

                /* Rendering */
                $output .= str_replace(
                    ['%TICKETNO%', '%SUBJECT%', '%SHORT_HASH%', '%COMMIT_URL%', '%BODY%'],
                    [$ticketNo, $subject, $shortHash, $commitUrl, $body],
                    $template
                );

            }
            $output .= "\n\n";
        }

        /* Link Issues */
        $searchPattern = IssueFormatHelper::getLinkPattern($this->getGeneratorOptions()->getIssueFormat());
        $replacement = '[$0](' . str_replace('%ISSUE%','$0', $this->getGeneratorOptions()->getIssueUrl()) . ')';
        $this->getGeneratorOptions()->getIssueUrl();
        $output = preg_replace($searchPattern, $replacement, $output);

        return $output;
    }

    /**
     * @param $needle
     * @param $string
     * @return bool|string
     */
    protected function cutStringAtString($needle, $string)
    {
        $pos = strpos($string, $needle);
        if ($pos !== false) {
            return substr($string, 0, $pos);
        }
        return $string;
    }
}
