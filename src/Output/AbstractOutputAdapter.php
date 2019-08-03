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

use DrBock\GitChangeLog\GeneratorOptions;
use DrBock\GitChangeLog\Git\Commit;

/**
 * Class AbstractOutputAdapter
 * @package DrBock\GitChangeLog\Output
 */
abstract class AbstractOutputAdapter implements OutputAdapterInterface
{
    /**
     * @var array
     */
    protected $groupedCommits = [];

    /**
     * @var GeneratorOptions
     */
    protected $generatorOptions;

    /**
     * @var string
     */
    protected $fileExtension = '';

    /**
     * Titles for commit types
     */
    protected const TITLE_MAPPING = [
        Commit::TYPE_NEW => 'Added',
        Commit::TYPE_CHANGED => 'Changed',
        Commit::TYPE_FIXED => 'Bugfixes',
        Commit::TYPE_REFACTORED => 'Refactorings',
        Commit::TYPE_DEPRECATED => 'Deprecations',
        Commit::TYPE_REMOVED => 'Removed',
        Commit::TYPE_FOLLOWUP => 'FollowUp',
        Commit::TYPE_OTHER => 'Other'
    ];

    /**
     * @return array
     */
    public function getGroupedCommits(): array
    {
        return $this->groupedCommits;
    }

    /**
     * @param array $groupedCommits
     */
    public function setGroupedCommits(array $groupedCommits): void
    {
        $this->groupedCommits = $groupedCommits;
    }

    /**
     * @return GeneratorOptions
     */
    public function getGeneratorOptions(): GeneratorOptions
    {
        return $this->generatorOptions;
    }

    /**
     * @param GeneratorOptions $generatorOptions
     */
    public function setGeneratorOptions(GeneratorOptions $generatorOptions): void
    {
        $this->generatorOptions = $generatorOptions;
    }

    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    /**
     * @return array|false
     */
    public function getFileIndex()
    {
        $pattern = rtrim($this->getGeneratorOptions()->getChangelogFolder(), '/') . '/*.' . $this->getFileExtension();
        return glob($pattern);
    }

    /**
     * calculates the path between two relative paths
     *
     * @param $from
     * @param $to
     * @return string
     */
    public function calculateLink($from, $to): string
    {
        $fromParts = explode( DIRECTORY_SEPARATOR, $from);
        $toParts = explode( DIRECTORY_SEPARATOR, $to);
        $linkParts = $toParts;
        $c = min(count($fromParts), count($toParts));
        for ($i = 0; $i < $c; $i++) {
            if ($fromParts[$i] === $toParts[$i]) {
                unset($linkParts[$i]);
            } else {
                break;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $linkParts);
    }

    /**
     * @return string
     */
    abstract public function getFormattedChangelog(): string;

    /**
     * @return string
     */
    abstract public function getFormattedIndex(): string;

}
