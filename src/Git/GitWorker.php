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
namespace DrBock\GitChangeLog\Git;

use DrBock\GitChangeLog\Helper\IssueFormatHelper;

/**
 * Class GitWorker
 * @package DrBock\GitChangeLog\Git
 */
class GitWorker
{

    private const FORMAT_COMMIT_HASH = '%H';
    private const FORMAT_AUTHOR_EMAIL = '%ae';
    private const FORMAT_AUTHOR_NAME = '%aN';
    private const FORMAT_COMMIT_TIMESTAMP = '%at';
    private const FORMAT_COMMIT_MESSAGE = '%s';
    private const FORMAT_COMMIT_BODY = '%b';
    private const COMMIT_ELEM_SEPARATOR = '|';
    private const COMMIT_SEPARATOR = '##NEWCOMMIT##';

    /**
     * @var string
     */
    protected $gitBinary = '';

    /**
     * @var string
     */
    protected $issueFormat = '';

    /**
     * GitWorker constructor.
     * @param $issueFormat
     * @throws \Exception
     */
    public function __construct($issueFormat)
    {
        if (!$this->gitBinary = $this->executeCommand('which git')) {
            throw new \Exception("Git binary does not exist.");
        }

        if (!$this->executeCommand('git rev-parse --is-inside-work-tree') === 'true') {
            throw new \Exception('Current directory is not a valid GIT working tree.');
        }

        $this->issueFormat = $issueFormat;
    }

    /**
     * @param string $fileName
     */
    public function addFile(string $fileName): void
    {
        $rawCommand = '%s add %s';
        $command = sprintf($rawCommand, $this->gitBinary, $fileName);
        $this->executeCommand($command);
    }

    /**
     * @param $command
     * @return string
     */
    public function executeCommand($command): ?string
    {
        $escapedCommand = escapeshellcmd((string) trim($command));
        return shell_exec($escapedCommand);
    }

    /**
     * @param $lastReleaseTag
     * @param $after
     * @param $before
     * @return array
     * @throws \Exception
     */
    public function getCommits($lastReleaseTag, $after, $before): ?array
    {
        if (empty($lastReleaseTag) && empty($after) && empty($before)) {
            throw new \Exception('You must specify a releaseTag or start- and enddate');
        }
        $rawCommand = '%s log';
        if ($lastReleaseTag !== '') {
            $rawCommand .= ' ' . $lastReleaseTag . '..HEAD';
        }
        if ($after !== '') {
            $rawCommand .= '  --after="' . $after . '"';
        }
        if ($before !== '') {
            $rawCommand .= '  --before="' . $before . '"';
        }
        $rawCommand .= ' --no-merges --pretty=format:%s';

        $command = sprintf($rawCommand, $this->gitBinary, $this->getHistoryFormat());
        $rawHistory = $this->executeCommand($command);

        if (empty($rawHistory)) {
            return [];
        }

        return $this->parseHistoryToCommits($rawHistory);
    }

    /**
     * @return string
     */
    protected function getHistoryFormat(): string
    {
        $elements = array(
            self::FORMAT_COMMIT_HASH,
            self::FORMAT_AUTHOR_EMAIL,
            self::FORMAT_AUTHOR_NAME,
            self::FORMAT_COMMIT_TIMESTAMP,
            self::FORMAT_COMMIT_MESSAGE,
            self::FORMAT_COMMIT_BODY
        );
        return implode(self::COMMIT_ELEM_SEPARATOR, $elements) . self::COMMIT_SEPARATOR;
    }

    /**
     * @param $history
     * @return array
     * @throws \Exception
     */
    protected function parseHistoryToCommits($history): array
    {
        $parsedHistory = [];
        $commitList = $this->getCommitListFromHistory($history);

        if (count($commitList) === 0) {
            return [];
        }

        foreach ($commitList as $line) {
            if ($line === null || $line === '') {
                continue;
            }

            $components = explode(self::COMMIT_ELEM_SEPARATOR, $line, 6);
            [$hash, $email, $name, $timestamp, $message, $body] = $components;

            $commit = new Commit();
            $commit->setHash(trim($hash));
            $commit->setAuthorEmail(trim($email));
            $commit->setAuthorName(trim($name));
            $commit->setTimestamp((int)trim($timestamp));
            $commit->setOriginalMessage(trim($message));
            $commit->setBody(trim($body));

            $helperResult = IssueFormatHelper::getTicketNumberAndSubject($this->issueFormat, $commit->getSubject());
            $commit->setSubject($helperResult['subject']);
            $commit->setTicketNo($helperResult['ticketNo']);

            $parsedHistory[] = $commit;
        }

        return $parsedHistory;
    }

    /**
     * @param string $history
     * @return array
     */
    public function getCommitListFromHistory($history): array
    {
        return explode(self::COMMIT_SEPARATOR, trim($history));
    }
}
