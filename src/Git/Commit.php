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

/**
 * Class Commit
 * @package DrBock\GitChangeLog\Git
 */
class Commit
{
    public const TYPE_NEW = 'New';
    public const TYPE_CHANGED = 'Changed';
    public const TYPE_FIXED = 'Fixed';
    public const TYPE_REFACTORED= 'Refactored';
    public const TYPE_DEPRECATED = 'Deprecated';
    public const TYPE_REMOVED = 'Removed';
    public const TYPE_FOLLOWUP = 'FollowUp';
    public const TYPE_OTHER = 'Other';

    /**
     * @var string
     */
    protected $hash = '';

    /**
     * @var string
     */
    protected $authorEmail = '';

    /**
     * @var string
     */
    protected $authorName = '';

    /**
     * @var int
     */
    protected $timestamp = 0;

    /**
     * @var string
     */
    protected $originalMessage = '';

    /**
     * @var string
     */
    protected $subject = '';

    /**
     * @var string
     */
    protected $body = '';

    /**
     * @var string
     */
    protected $type = self::TYPE_OTHER;

    /**
     * @var string
     */
    protected $ticketNo = '';

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getShortHash(): string {
        return substr($this->getHash(),0,12);
    }

    /**
     * @return string
     */
    public function getAuthorEmail(): string
    {
        return $this->authorEmail;
    }

    /**
     * @param string $authorEmail
     */
    public function setAuthorEmail(string $authorEmail): void
    {
        $this->authorEmail = $authorEmail;
    }

    /**
     * @return string
     */
    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    /**
     * @param string $authorName
     */
    public function setAuthorName(string $authorName): void
    {
        $this->authorName = $authorName;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     */
    public function setTimestamp(int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getOriginalMessage(): string
    {
        return $this->originalMessage;
    }

    /**
     * @param string $originalMessage
     */
    public function setOriginalMessage(string $originalMessage): void
    {
        $this->originalMessage = $originalMessage;
        $this->setCommitTypeAndMessageFromOriginalMessage();
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTicketNo(): string
    {
        return $this->ticketNo;
    }

    /**
     * @param string $ticketNo
     */
    public function setTicketNo(string $ticketNo): void
    {
        $this->ticketNo = $ticketNo;
    }

    /**
     * parses the commitType from commit message
     */
    protected function setCommitTypeAndMessageFromOriginalMessage(): void
    {
        $messageParts = explode(':', $this->getOriginalMessage(), 2);
        $subject = trim($messageParts[1]);
        switch(strtolower($messageParts[0])) {
            case 'new':
            case 'added':
                $type = self::TYPE_NEW;
                break;
            case 'changed':
            case 'change':
                $type = self::TYPE_CHANGED;
                break;
            case 'fixed':
            case 'fix':
            case 'bugfix':
                $type = self::TYPE_FIXED;
                break;
            case 'refactored':
            case 'refactor':
                $type = self::TYPE_REFACTORED;
                break;
            case 'deprecated':
                $type = self::TYPE_DEPRECATED;
                break;
            case 'removed':
            case 'remove':
            case 'deleted':
            case 'delete':
            case 'cleanup':
            case 'revert':
                $type = self::TYPE_REMOVED;
                break;
            case 'follow up':
                $type = self::TYPE_FOLLOWUP;
                break;
            default:
                $type = self::TYPE_OTHER;
                $subject = trim($this->getOriginalMessage());
                break;
        }
        $this->setType($type);
        $this->setSubject($subject);
    }
}
