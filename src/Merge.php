<?php
declare(strict_types = 1);

namespace Gettext;

/**
 * Merge contants.
 */
final class Merge
{
    const TRANSLATIONS_OURS = 1;
    const TRANSLATIONS_THEIRS = 2;
    const TRANSLATIONS_OVERRIDE = 4;

    const HEADERS_OURS = 8;
    const HEADERS_THEIRS = 16;
    const HEADERS_OVERRIDE = 32;

    const LANGUAGE_OVERRIDE = 64;
    const DOMAIN_OVERRIDE = 128;

    const COMMENTS_OURS = 256;
    const COMMENTS_THEIRS = 512;

    const EXTRACTED_COMMENTS_OURS = 1024;
    const EXTRACTED_COMMENTS_THEIRS = 2048;

    const FLAGS_OURS = 4096;
    const FLAGS_THEIRS = 8192;

    const REFERENCES_OURS = 16384;
    const REFERENCES_THEIRS = 32768;

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
