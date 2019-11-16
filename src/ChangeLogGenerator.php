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
namespace DrBock\GitChangeLog;

use DrBock\GitChangeLog\Git\Commit;
use DrBock\GitChangeLog\Git\GitWorker;
use DrBock\GitChangeLog\Output\OutputAdapterInterface;
use DrBock\GitChangeLog\Output\OutputFactory;

/**
 * Class ChangeLogGenerator
 * @package DrBock\GitChangeLog
 */
class ChangeLogGenerator
{
    /**
     * @var GeneratorOptions
     */
    protected $generatorOptions;

    /**
     * @var OutputAdapterInterface
     */
    protected $outputAdapter;

    /**
     * ChangeLogGenerator constructor.
     * @param GeneratorOptions $generatorOptions
     */
    public function __construct(GeneratorOptions $generatorOptions)
    {
        $this->setGeneratorOptions($generatorOptions);
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
     * @return bool
     * @throws \Exception
     */
    public function generate(): bool
    {
        $worker = new GitWorker($this->getGeneratorOptions()->getIssueFormat());
        $commits = $worker->getCommits(
            $this->getGeneratorOptions()->getFromTag(),
            $this->getGeneratorOptions()->getFromDate(),
            $this->getGeneratorOptions()->getToDate()
        );

        $this->outputAdapter = OutputFactory::getAdapter($this->getGeneratorOptions()->getOutputFormat());
        $this->outputAdapter->setGroupedCommits($this->groupCommits($commits, $this->getGeneratorOptions()->getSorting()));
        $this->outputAdapter->setGeneratorOptions($this->getGeneratorOptions());

        if ($this->getGeneratorOptions()->isDryRun() === true) {
            echo $this->outputAdapter->getFormattedChangelog();
            return true;
        }

        if ($fileName = $this->writeChangelogFile()) {
            if ($this->getGeneratorOptions()->isAddFileToGit() === true) {
                $worker->addFile($fileName);
            }
            $this->writeIndexFile();
        }

        return true;
    }

    /**
     * @param Commit[] $commits
     * @param string $sorting
     * @return array
     */
    protected function groupCommits($commits, $sorting): array
    {
        $groupedCommits = [
            Commit::TYPE_NEW => [],
            Commit::TYPE_CHANGED => [],
            Commit::TYPE_FIXED => [],
            Commit::TYPE_REFACTORED => [],
            Commit::TYPE_DEPRECATED => [],
            Commit::TYPE_REMOVED => [],
            Commit::TYPE_FOLLOWUP => [],
            Commit::TYPE_OTHER => []
        ];
        if ($sorting === 'ASC') {
            usort($commits, static function (Commit $a, Commit $b) {
                return ($a->getTimestamp() <=> $b->getTimestamp());
            });
        } else {
            usort($commits, static function (Commit $a, Commit $b) {
                return -($a->getTimestamp() <=> $b->getTimestamp());
            });
        }
        foreach ($commits as $commit) {
            $groupedCommits[$commit->getType()][] = $commit;
        }
        return $groupedCommits;
    }

    /**
     * @return bool|string
     */
    protected function writeChangelogFile()
    {
        $formattedChangelog =  $this->outputAdapter->getFormattedChangelog();

        $fileName = $this->getGeneratorOptions()->getReleaseDate()->format('Y-m-d');
        if ($this->getGeneratorOptions()->getReleaseName() !== '') {
            $fileName .= '_' . preg_replace( '/[^a-z0-9]+/i', '-', $this->getGeneratorOptions()->getReleaseName());
        }
        $fileName .= '.' . $this->outputAdapter->getFileExtension();

        $path = rtrim($this->getGeneratorOptions()->getChangelogFolder(), '/') . '/' . $fileName;
        return file_put_contents($path, $formattedChangelog) ? $path : false;
    }

    /**
     * @return bool|int
     */
    protected function writeIndexFile()
    {
        $formattedIndex = $this->outputAdapter->getFormattedIndex();
        return file_put_contents($this->getGeneratorOptions()->getChangelogIndexFile(), $formattedIndex);
    }
}
