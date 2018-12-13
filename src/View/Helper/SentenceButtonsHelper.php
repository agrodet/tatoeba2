<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
namespace App\View\Helper;

use App\Model\CurrentUser;
use App\View\Helper\AppHelper;
use Cake\Core\Configure;


/**
 * Helper to display sentences buttons that are not part of the menu.
 *
 * @category Sentences
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class SentenceButtonsHelper extends AppHelper
{
    public $helpers = array(
        'Html',
        'Languages',
        'Form',
        'Images'
    );

    /**
     * Display info button which links to the sentence page
     *
     * @param int $sentenceId The sentence id.
     *
     * @return void
     */
    public function displayNavigationButton($sentenceId, $type)
    {
        if ($type == 'mainSentence') {
            $image = $this->Images->svgIcon('sentence-number');
        } else {
            $image = $this->Images->svgIcon('translation');
        }

        echo $this->Html->link(
            $image,
            array(
                "controller" => "sentences",
                "action" => "show",
                $sentenceId
            ),
            array(
                "escape" => false,
                "class" => "navigationIcon " . $type,
                "title" => __("Show sentence's details"),
            )
        );
    }

    /**
     * Display unlink button for translations.
     *
     * @param int $sentenceId    Id of the main sentence.
     * @param int $translationId Id of the translation.
     * @param int $langFilter    The language sentences should be filtered in when redisplaying the list.
     *
     * @return void
     */
    public function unlinkButton($sentenceId, $translationId, $langFilter = 'und')
    {
        $this->Html->script('links.add_and_delete.js', array('block' => 'scriptBottom'));

        $elementId = 'link_'.$sentenceId.'_'.$translationId;

        $image = $this->Images->svgIcon(
            'unlink',
            array(
                "alt"=>__('Unlink'),
                "width" => 16,
                "height" => 16
            )
        );
        $langFilter = h(json_encode($langFilter));
        echo $this->Html->link(
            $image,
            array(
                "controller" => "links",
                "action" => "delete",
                $sentenceId,
                $translationId
            ),
            array(
                "escape" => false,
                "class" => "link button",
                "id" => $elementId,
                "title" => __('Unlink this translation.'),
                "onclick" => "translationLink('delete', $sentenceId, $translationId, $langFilter); return false"
            )
        );
    }


    /**
     * Display link button for translations.
     *
     * @param int $sentenceId    Id of the main sentence.
     * @param int $translationId Id of the translation.
     * @param int $langFilter    The language sentences should be filtered in when redisplaying the list.
     *
     * @return void
     */
    public function linkButton($sentenceId, $translationId, $langFilter = 'und')
    {
        $this->Html->script('links.add_and_delete.js', array('block' => 'scriptBottom'));

        $elementId = 'link_'.$sentenceId.'_'.$translationId;

        $image = $this->Images->svgIcon(
            'link',
            array(
                "alt"=>__('Link'),
                "width" => 16,
                "height" => 16
            )
        );
        $langFilter = h(json_encode($langFilter));
        echo $this->Html->link(
            $image,
            array(
                "controller" => "links",
                "action" => "add",
                $sentenceId,
                $translationId
            ),
            array(
                "escape" => false,
                "class" => "link button",
                "id" => $elementId,
                "title" => __('Make into direct translation.'),
                "onclick" => "translationLink('add', $sentenceId, $translationId, $langFilter); return false"
            )
        );
    }


    /**
     * Display audio button.
     *
     * @param int   $sentenceId     Id of the sentence on which this button is
     *                              displayed.
     * @param int   $sentenceLang   Language of the sentence.
     * @param sting $sentenceAudios Array of audio recordings of the sentence.
     *
     * @return void
     */
    public function audioButton($sentenceId, $sentenceLang, $sentenceAudios)
    {
        if (count($sentenceAudios)) {
            $onClick = 'return false';
            $path = Configure::read('Recordings.url')
                .$sentenceLang.'/'.$sentenceId.'.mp3';
            $css = 'audioAvailable';

            $audio = isset($sentenceAudios[0]) ?
                     $sentenceAudios[0] :
                     $sentenceAudios;
            $author = isset($audio['User']['username']) ?
                      $audio['User']['username'] :
                      $audio['external']['username'];
            if (empty($author)) {
                $title = __('Play audio');
            } else {
                $title = __(format(
                    'Play audio recorded by {author}',
                    array('author' => $author)
                ), true);
            }
            $this->Html->script('sentences.play_audio.js', array('block' => 'scriptBottom'));
        } else {
            $onClick = 'return false';
            $css = 'audioUnavailable';
            $path = 'http://en.wiki.tatoeba.org/articles/show/contribute-audio';
            $title = __('No audio for this sentence. Click to learn how to contribute.');
            $onClick = 'window.open(this.href); return false;';
        }

        echo $this->Html->Link(
            null, $path,
            array(
                'title' => $title,
                'class' => "audioButton $css",
                'onclick' => $onClick
            )
        );
    }


    /**
     * Language flag.
     *
     * @param int    $id       Id of the sentence.
     * @param string $lang     Language of the sentence.
     * @param bool   $editable Set to true of flag can be changed.
     *
     * @return void
     */
    public function displayLanguageFlag($id, $lang, $editable = false)
    {
        $class = '';
        if ($editable) {
            $this->Html->script('sentences.change_language.js', array('block' => 'scriptBottom'));
            $class = 'editableFlag';

            // language select
            if (CurrentUser::isAdmin() || CurrentUser::isModerator()) {
                $langArray = $this->Languages->otherLanguagesArray();
            } else {
                $langArray = $this->Languages->profileLanguagesArray(
                    false, true
                );
            }

            $preselectedLang = $lang;
            if (!array_key_exists($lang, $langArray)) {
                $preselectedLang = null;
            }
            ?>

            <span id="<?php echo 'selectLangContainer_'.$id; ?>" class="selectLang">
            <?php
            echo $this->Form->select(
                'selectLang_'.$id,
                $langArray,
                array(
                    'id' => 'selectLang_'.$id,
                    "value" => $preselectedLang,
                    "class"=>"language-selector",
                    "empty" => false
                ),
                false
            );
            ?>
            </span>

            <?php
        }

        echo $this->Languages->icon(
            $lang,
            array(
                "id" => "flag_".$id,
                "class" => "languageFlag ".$class,
                "width" => 30,
                "height" => 20,
                "data-sentence-id" => $id
            )
        );

    }


    /**
     *
     */
    public function displayCopyButton($text)
    {
        $this->Html->script('clipboard.min.js', array('block' => 'scriptBottom'));
        $copyButton = $this->Images->svgIcon('copy');
        echo $this->Html->div('copy-btn', $copyButton,
            array(
                'title' => __('Copy sentence')
            )
        );
    }
}
?>
