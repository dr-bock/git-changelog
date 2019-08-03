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
namespace DrBock\GitChangeLog\Composer;

use Composer\Script\Event;
use DrBock\GitChangeLog\ChangeLogGenerator;
use DrBock\GitChangeLog\GeneratorOptions;
use DrBock\GitChangeLog\Output\MarkdownOutputAdapter;

/**
 * Class GeneratorCommand
 * @package DrBock\GitChangeLog\Composer
 */
class GeneratorCommand
{
    /**
     * @param Event $event
     * @throws \Exception
     */
    public static function generate(Event $event): void
    {
        $composerConfig = $event->getComposer()->getConfig()->get('dr-bock/git-changelog');
        $arguments = self::parseArguments($event->getArguments());

        $generatorOptions = new GeneratorOptions();
        $generatorOptions->setChangelogIndexFile($composerConfig['changelogIndexFile'])
            ->setProjectName($event->getComposer()->getPackage()->getName())
            ->setChangelogFolder($composerConfig['changelogFolder'])
            ->setIssueUrl($composerConfig['issueUrl'])
            ->setCommitUrl($composerConfig['issueUrl'])
            ->setIssueFormat($composerConfig['issueFormat'])
            ->setOutputFormat(MarkdownOutputAdapter::FORMAT);

        if (isset($arguments['releaseDate'])) {
            $generatorOptions->setReleaseDate(new \DateTime($arguments['releaseDate']));
        } else {
            throw new \Exception('Please specify a release date');
        }

        if (isset($arguments['fromTag'])) {
            $generatorOptions->setFromTag($arguments['fromTag']);
        }
        if (isset($arguments['fromDate'])) {
            $generatorOptions->setFromDate($arguments['fromDate']);
        }
        if (isset($arguments['toDate'])) {
            $generatorOptions->setToDate($arguments['toDate']);
        }
        if (isset($arguments['releaseName'])) {
            $generatorOptions->setReleaseName($arguments['releaseName']);
        }
        if (isset($arguments['dryRun'])) {
            $generatorOptions->setDryRun(true);
        }

        $generator = new ChangeLogGenerator($generatorOptions);
        $generator->generate();
    }

    /**
     * @param array $arguments
     * @return array
     * @throws \Exception
     */
    protected static function parseArguments($arguments = []): array
    {
        $allowedArguments = ['fromDate', 'toDate', 'fromTag', 'releaseName', 'releaseDate', 'dryRun'];

        $parsedArguments = [];
        foreach ($arguments as $argumentString) {
            $aParts = explode('=', $argumentString, 2);
            $aParts = \array_map('trim', $aParts);
            if (count($aParts) !== 2 || !\in_array($aParts[0], $allowedArguments, true)) {
                throw new \Exception('Invalid Argument: ' . $argumentString);
            }
            $parsedArguments[$aParts[0]] = $aParts[1];
        }
        return $parsedArguments;
    }
}
