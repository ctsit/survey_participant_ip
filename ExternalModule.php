<?php
/**
 * @file
 * Provides ExternalModule class for Survey Participant IP.
 */

namespace SurveyParticipantIP\ExternalModule;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;
use Records;
use System;

/**
 * ExternalModule class for Survey Participant IP.
 */
class ExternalModule extends AbstractExternalModule {

    protected static $ipFields = [];

    /**
     * @inheritdoc
     */
    function redcap_every_page_before_render($project_id) {
        if (!$project_id || !empty($ipFields) || $this->currentPageIsForm()) {
            return;
        }

        global $Proj;
        $fields = empty($_GET['page']) ? $Proj->metadata : $Proj->forms[$_GET['page']]['fields'];

        foreach (array_keys($fields) as $field_name) {
            $misc = $Proj->metadata[$field_name]['misc'];

            if (empty($misc) || strpos(' ' . $misc . ' ', ' @SURVEY-PARTICIPANT-IP ') === false) {
                continue;
            }

            $Proj->metadata[$field_name]['misc'] .= ' @HIDDEN-SURVEY @READONLY-FORM';
            self::$ipFields[] = $field_name;
        }
    }

    /**
     * @inheritdoc
     */
    function redcap_every_page_top($project_id) {
        if ($project_id) {
            $this->includeJs('js/action_tag_helper.js');
        }
    }

    /**
     * @inheritdoc
     */
    function redcap_survey_page_top($project_id, $record = null, $instrument, $event_id, $group_id = null, $survey_hash, $response_id = null, $repeat_instance = 1) {
        if (empty(self::$ipFields) || $this->currentSurveyHasData()) {
            return;
        }

        $this->setJsSettings(['fields' => self::$ipFields]);
        $this->includeJs('js/survey_participant_ip.js');
    }

    /**
     * Checks whether the current page is a survey or a data entry form.
     *
     * @return bool
     */
    function currentPageIsForm() {
        return (!isset($_GET['s']) || PAGE != 'surveys/index.php' || !defined('NOAUTH')) && (PAGE != 'DataEntry/index.php' || empty($_GET['id']));
    }

    /**
     * Checks whether the current survey has data.
     *
     * @return bool
     */
    function currentSurveyHasData() {
        global $double_data_entry, $user_rights, $quesion_by_section, $pageFields;

        $record = $_GET['id'];
        if ($double_data_entry && $user_rights['double_data'] != 0) {
            $record .= '--' . $user_rights['double_data'];
        }

        if ($question_by_section) {
            return Records::fieldsHaveData($record, $pageFields[$_GET['__page__']], $_GET['event_id']);
        }

        return Records::formHasData($record, $_GET['page'], $_GET['event_id'], $_GET['instance']);
    }

    /**
     * Pass settings to JS.
     *
     * @param mixed $settings
     *   The settings to be set.
     */
    protected function setJsSettings($settings) {
        echo '<script>SurveyParticipantIP = ' . json_encode($settings) . ';</script>';
    }

    /**
     * Includes a local JS file.
     *
     * @param string $path
     *   The relative path to the js file.
     */
    protected function includeJs($path) {
        echo '<script src="' . $this->getUrl($path) . '"></script>';
    }
}
