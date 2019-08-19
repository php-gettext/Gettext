<?php

namespace Gettext;

/**
 * Class to manage a translation string: XLIFF-specific extensions.
 */
class XliffTranslation extends Translation
{
    protected $unitId;

    /**
     * Returns the unit id of this translation.
     *
     * @return string
     */
    public function getUnitId()
    {
        return $this->unitId;
    }

    /**
     * Sets the id of this translation.
     *
     * @param $unitId string
     */
    public function setUnitId($unitId)
    {
        $this->unitId = $unitId;
    }
}
