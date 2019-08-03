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

/**
 * Class GeneratorOptions
 * @package DrBock\GitChangeLog
 */
class GeneratorOptions
{
    /**
     * @var string
     */
    private $outputFormat = '';

    /**
     * @var string
     */
    private $projectName = '';

    /**
     * @var string
     */
    private $issueFormat = 'jira';

    /**
     * A valid start date for changelog Y-m-d
     *
     * @var string
     */
    private $fromDate = '';

    /**
     * A valid end date for changelog Y-m-d
     *
     * @var string
     */
    private $toDate = '';

    /**
     * @var string
     */
    private $fromTag = '';

    /**
     * @var string
     */
    private $releaseName = '';

    /**
     * @var \DateTime
     */
    private $releaseDate = '';

    /**
     * @var string
     */
    private $issueUrl = '';

    /**
     * @var string
     */
    private $commitUrl = '';

    /**
     * @var string
     */
    private $changelogIndexFile = '';

    /**
     * @var string
     */
    private $changelogFolder = '';

    /**
     * @var bool
     */
    private $addFileToGit = true;

    /**
     * @var bool
     */
    private $dryRun = false;

    /**
     * @return string
     */
    public function getOutputFormat(): string
    {
        return $this->outputFormat;
    }

    /**
     * @param string $outputFormat
     * @return GeneratorOptions
     */
    public function setOutputFormat(string $outputFormat): self
    {
        $this->outputFormat = $outputFormat;
        return $this;
    }

    /**
     * @return string
     */
    public function getProjectName(): string
    {
        return $this->projectName;
    }

    /**
     * @param string $projectName
     * @return GeneratorOptions
     */
    public function setProjectName(string $projectName): self
    {
        $this->projectName = $projectName;
        return $this;
    }

    /**
     * @return string
     */
    public function getIssueFormat(): string
    {
        return $this->issueFormat;
    }

    /**
     * @param string $issueFormat
     * @return GeneratorOptions
     */
    public function setIssueFormat(string $issueFormat): self
    {
        $this->issueFormat = $issueFormat;
        return $this;
    }

    /**
     * @return string
     */
    public function getFromDate(): string
    {
        return $this->fromDate;
    }

    /**
     * @param string $fromDate
     * @return GeneratorOptions
     */
    public function setFromDate(string $fromDate): self
    {
        $this->fromDate = $fromDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getToDate(): string
    {
        return $this->toDate;
    }

    /**
     * @param string $toDate
     * @return GeneratorOptions
     */
    public function setToDate(string $toDate): self
    {
        $this->toDate = $toDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getFromTag(): string
    {
        return $this->fromTag;
    }

    /**
     * @param string $fromTag
     * @return GeneratorOptions
     */
    public function setFromTag(string $fromTag): self
    {
        $this->fromTag = $fromTag;
        return $this;
    }

    /**
     * @return string
     */
    public function getReleaseName(): string
    {
        return $this->releaseName;
    }

    /**
     * @param string $releaseName
     * @return GeneratorOptions
     */
    public function setReleaseName(string $releaseName): self
    {
        $this->releaseName = $releaseName;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getReleaseDate(): \DateTime
    {
        return $this->releaseDate;
    }

    /**
     * @param \DateTime $releaseDate
     * @return GeneratorOptions
     */
    public function setReleaseDate(\DateTime $releaseDate): self
    {
        $this->releaseDate = $releaseDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getIssueUrl(): string
    {
        return $this->issueUrl;
    }

    /**
     * @param string $issueUrl
     * @return GeneratorOptions
     */
    public function setIssueUrl(string $issueUrl): self
    {
        $this->issueUrl = $issueUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommitUrl(): string
    {
        return $this->commitUrl;
    }

    /**
     * @param string $commitUrl
     * @return GeneratorOptions
     */
    public function setCommitUrl(string $commitUrl): self
    {
        $this->commitUrl = $commitUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getChangelogIndexFile(): string
    {
        return $this->changelogIndexFile;
    }

    /**
     * @param string $changelogIndexFile
     * @return GeneratorOptions
     */
    public function setChangelogIndexFile(string $changelogIndexFile): self
    {
        $this->changelogIndexFile = $changelogIndexFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getChangelogFolder(): string
    {
        return $this->changelogFolder;
    }

    /**
     * @param string $changelogFolder
     * @return GeneratorOptions
     */
    public function setChangelogFolder(string $changelogFolder): self
    {
        $this->changelogFolder = $changelogFolder;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAddFileToGit(): bool
    {
        return $this->addFileToGit;
    }

    /**
     * @param bool $addFileToGit
     * @return GeneratorOptions
     */
    public function setAddFileToGit(bool $addFileToGit): self
    {
        $this->addFileToGit = $addFileToGit;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDryRun(): bool
    {
        return $this->dryRun;
    }

    /**
     * @param bool $dryRun
     * @return GeneratorOptions
     */
    public function setDryRun(bool $dryRun): self
    {
        $this->dryRun = $dryRun;
        return $this;
    }
}
