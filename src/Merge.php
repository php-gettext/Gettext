<?php

namespace Gettext;

/**
 * Static class with merge contants
 */
class Merge
{
    const ADD = 1;
    const REMOVE = 2;

    const HEADERS_ADD = 4;
    const HEADERS_REMOVE = 8;
    const HEADERS_OVERRIDE = 16;

    const LANGUAGE_OVERRIDE = 32;
    const DOMAIN_OVERRIDE = 64;
    const TRANSLATION_OVERRIDE = 128;

    const COMMENTS_OURS = 256;
    const COMMENTS_THEIRS = 512;

    const EXTRACTED_COMMENTS_OURS = 1024;
    const EXTRACTED_COMMENTS_THEIRS = 2048;

    const FLAGS_OURS = 4096;
    const FLAGS_THEIRS = 8192;

    const REFERENCES_OURS = 16384;
    const REFERENCES_THEIRS = 32768;

    const DEFAULT = 1 + 4;
}
