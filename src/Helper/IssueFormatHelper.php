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
                return '/([A-Z]{2,4}-[0-9]{1,9})/';
                break;
            case self::FORMAT_GITHUB:
                return '';
                break;
        }
        throw new \Exception('Issue Helper: Unknown Format "' . $format . '"');
    }

    /**
     * @param $format
     * @param $message
     * @return array
     * @throws \Exception
     */
    public static function getTicketNumberAndSubject($format, $message): array
    {
        switch ($format) {
            case self::FORMAT_JIRA:
                $messageParts = preg_split('/^([[A-Z]{2,5}-[0-9]{1,9}])/', trim($message), 2, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                if (count($messageParts) === 1) {
                    $ticketNo = '';
                    $subject = trim($messageParts[0]);
                } else {
                    $ticketNo = substr($messageParts[0], 1, -1);
                    $subject = trim($messageParts[1]);
                }
                break;
            case self::FORMAT_GITHUB:
                $ticketNo = '';
                $subject = '';
                break;
            default:
                throw new \Exception('Issue Helper: Unknown Format "' . $format . '"');
                break;
        }
        return ['ticketNo' => $ticketNo, 'subject' => $subject];
    }
}
