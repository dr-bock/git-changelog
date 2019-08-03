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

/**
 * Class OutputFactory
 * @package DrBock\GitChangeLog\Output
 */
class OutputFactory
{
    /**
     * @var array
     */
    private static $map = [
        MarkdownOutputAdapter::FORMAT => MarkdownOutputAdapter::class
    ];

    /**
     * Get output adapter instance
     *
     * @param string $type
     * @return OutputAdapterInterface
     */
    public static function getAdapter($type): OutputAdapterInterface
    {
        $adapter = self::$map[$type];
        if (array_key_exists($type, self::$map)) {
            $adapter = self::$map[$type];
        }
        return new $adapter();
    }
}
