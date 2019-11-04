<?php
declare(strict_types = 1);

namespace Gettext;

/**
 * Merge contants.
 */
final class Merge
{
    const TRANSLATIONS_OURS = 1 << 0;
    const TRANSLATIONS_THEIRS = 1 << 1;
    const TRANSLATIONS_OVERRIDE = 1 << 2;

    const HEADERS_OURS = 1 << 3;
    const HEADERS_THEIRS = 1 << 4;
    const HEADERS_OVERRIDE = 1 << 5;

    const COMMENTS_OURS = 1 << 6;
    const COMMENTS_THEIRS = 1 << 7;

    const EXTRACTED_COMMENTS_OURS = 1 << 8;
    const EXTRACTED_COMMENTS_THEIRS = 1 << 9;

    const FLAGS_OURS = 1 << 10;
    const FLAGS_THEIRS = 1 << 11;

    const REFERENCES_OURS = 1 << 12;
    const REFERENCES_THEIRS = 1 << 13;

    //Merge strategies
    const SCAN_AND_LOAD =
          Merge::HEADERS_OVERRIDE
        | Merge::TRANSLATIONS_OURS
        | Merge::TRANSLATIONS_OVERRIDE
        | Merge::EXTRACTED_COMMENTS_OURS
        | Merge::REFERENCES_OURS
        | Merge::FLAGS_THEIRS
        | Merge::COMMENTS_THEIRS;
}
