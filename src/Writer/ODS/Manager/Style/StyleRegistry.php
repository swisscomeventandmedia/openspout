<?php

namespace OpenSpout\Writer\ODS\Manager\Style;

use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\Manager\Style\StyleRegistry as CommonStyleRegistry;

/**
 * Registry for all used styles.
 */
final class StyleRegistry extends CommonStyleRegistry
{
    /** @var array [FONT_NAME] => [] Map whose keys contain all the fonts used */
    protected array $usedFontsSet = [];

    /**
     * Registers the given style as a used style.
     * Duplicate styles won't be registered more than once.
     *
     * @param Style $style The style to be registered
     *
     * @return Style the registered style, updated with an internal ID
     */
    public function registerStyle(Style $style): Style
    {
        if ($style->isRegistered()) {
            return $style;
        }

        $registeredStyle = parent::registerStyle($style);
        $this->usedFontsSet[$style->getFontName()] = true;

        return $registeredStyle;
    }

    /**
     * @return string[] List of used fonts name
     */
    public function getUsedFonts(): array
    {
        return array_keys($this->usedFontsSet);
    }
}