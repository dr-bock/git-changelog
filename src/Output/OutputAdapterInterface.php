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

/**
 * Interface OutputAdapterInterface
 * @package DrBock\GitChangeLog\Output
 */
interface OutputAdapterInterface
{
    /**
     * @return string
     */
    public function getFormattedChangelog(): string;

    /**
     * @return string
     */
    public function getFormattedIndex(): string;

    /**
     * @param array $groupedCommits
     */
    public function setGroupedCommits(array $groupedCommits): void;

    /**
     * @return array
     */
    public function getGroupedCommits(): array;

    /**
     * @param GeneratorOptions $generatorOptions
     */
    public function setGeneratorOptions(GeneratorOptions $generatorOptions): void;

    /**
     * @return string
     */
    public function getFileExtension(): string;

    /**
     * @return mixed
     */
    public function getFileIndex();
}
