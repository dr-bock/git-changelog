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
namespace DrBock\GitChangeLog\Helper;

use DrBock\GitChangeLog\Git\Commit;

/**
 * Class IssueFormatHelper
 * @package DrBock\GitChangeLog\Helper
 */
class IssueFormatHelper
{
    public const FORMAT_JIRA = 'jira';
    public const FORMAT_GITHUB = 'github';

    /**
     * @param $format
     * @return string
     * @throws \Exception
     */
    public static function getLinkPattern($format): string
    {
        switch ($format) {
            case self::FORMAT_JIRA:
                return '/([A-Z]{2,5}-[0-9]{1,9})/';
                break;
            case self::FORMAT_GITHUB:
                return '';
                break;
        }
        throw new \Exception('Issue Helper: Unknown Format "' . $format . '"');
    }

    /**
     * @param string $message
     * @param string $format
     * @return array
     * @throws \Exception
     */
    public static function parseCommitMessage($message, $format): array
    {
        switch ($format) {
            case self::FORMAT_JIRA:
                $pattern = '([[A-Z]{2,5}-[0-9]{1,9}])';
                break;
            case self::FORMAT_GITHUB:
                $pattern = '';
                break;
            default:
                throw new \Exception('Issue Helper: Unknown Format "' . $format . '"');
                break;
        }

        $type = Commit::TYPE_OTHER;
        $issue = '';

        $messageParts = preg_split(
            '/'. $pattern .'/',
            trim($message),
            2,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        $messageParts = array_map('trim', $messageParts ?: []);
        if (preg_match('/^' . $pattern . '/', $messageParts[0]) === 1) {
            // Issue at beginning
            $type = Commit::TYPE_OTHER;
            $issue = $messageParts[0];
            $subject = trim(substr($message, strlen($messageParts[0])));
            $subjectParts = explode(':', trim($subject,' :'), 2);
            if (count($subjectParts) !== 1 && strlen($subjectParts[0]) < 12) {
                $subjectParts = array_map('trim', $subjectParts);
                $type =$subjectParts[0];
                $subject = $subjectParts[1];
            }
        } elseif (preg_match('/^' . $pattern . '/', $messageParts[1]) === 1) {
            // Issue in the middle
            $issue = $messageParts[1];
            $type = rtrim($messageParts[0], ':');
            $typeParts = array_map('trim', explode(':', $type, 2));
            if (count($typeParts) === 1) {
                if (strlen($type) < 12) {
                    $subject = $messageParts[2];
                } else {
                    $subject = $message;
                    $type = Commit::TYPE_OTHER;
                }
            } else {
                // Commit-Type at beginning
                $type = $typeParts[0];
                $subject = substr($message, strlen($type));
            }
        } elseif (count($messageParts) === 1) {
            $parts = explode(':', $messageParts[0], 2);
            $type = count($parts) === 2 ? $parts[0] : Commit::TYPE_OTHER;
            $subject = count($parts) === 2 ? $parts[1] : $parts[0];
        }
        else {
            $subject = $message;
        }

        return [
            'subject' => trim($subject, ' :'),
            'type' => trim($type, ' :'),
            'issue' => trim($issue, ' []#')
        ];
    }
}
