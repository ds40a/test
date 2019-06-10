<?php

/**  */
namespace Test\lib;

/**
 * Class Translator
 */
class Translator extends ContainerAware
{

    const AVAILABLE_LANGS = [
        'ru',
        'en',
    ];

    /** @var string */
    private $lang;

    /** @var array */
    private $translations;

    /**
     * @param  array $translations
     */
    public function init($translations)
    {
        $this->translations = $translations;
    }

    /**
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @param string $transKey
     *
     * @return string
     */
    public function trans($transKey)
    {
        if (
            $this->lang and
            $this->translations and
            isset($this->translations[$this->lang][$transKey])
        ) {
            return $this->translations[$this->lang][$transKey];
        }
        return $transKey;
    }
}
