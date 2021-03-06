<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Lib\LanguagesLib;

class Translation extends Entity
{
    use LanguageNameTrait;

    protected $_virtual = [
        'lang_name',
        'dir',
        'lang_tag',
    ];

    protected function _getLangName()
    {
        return $this->codeToNameAlone($this->lang);
    }

    protected function _getDir()
    {
        return LanguagesLib::getLanguageDirection($this->lang);
    }

    protected function _getLangTag()
    {
        return LanguagesLib::languageTag($this->lang, $this->script);
    }
}
