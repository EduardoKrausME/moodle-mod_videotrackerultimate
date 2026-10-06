<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * English strings.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['actual'] = 'Observed value';
$string['addindicator'] = 'Add indicator';
$string['adequatelabel'] = 'Second category label';
$string['adequatemin'] = 'Second category minimum';
$string['attentionlabel'] = 'Third category label';
$string['attentionmin'] = 'Third category minimum';
$string['averagepercent'] = 'Average watched percentage';
$string['averagescore'] = 'Average score';
$string['averagewatchtime'] = 'Average effective watch time';
$string['backtoactivity'] = 'Back to activity';
$string['backtooverview'] = 'Back to overview';
$string['belowthreshold'] = 'Learners below review threshold';
$string['calculationorigin'] = 'Calculation origin: {$a}';
$string['classoverview'] = 'Class overview';
$string['completiondetail:indicators'] = 'Meet all indicators marked as mandatory for completion';
$string['completiondetail:percent'] = 'Watch at least {$a}% of the video';
$string['completiondetail:score'] = 'Engagement Score of at least {$a}';
$string['completionindicators'] = 'Require indicators marked as mandatory for completion';
$string['completionminpercent'] = 'Require minimum watched percentage';
$string['completionminscore'] = 'Require minimum Engagement Score';
$string['editindicator'] = 'Edit indicator';
$string['effectivetime'] = 'Effective playback time';
$string['engagementscore'] = 'Engagement Score';
$string['errorgrademax'] = 'The maximum grade must be greater than zero and no greater than 10000.';
$string['errorpercent'] = 'This value must be between 0 and 100.';
$string['errorstatusorder'] = 'Category thresholds must be ordered from highest to lowest.';
$string['errortotalweight'] = 'Enabled indicators already use {$a} points. The active total cannot exceed 100.';
$string['errorweight'] = 'Weight must be greater than zero and no greater than 100.';
$string['eventscoreupdated'] = 'Engagement Score updated';
$string['evidence'] = 'Evidence';
$string['excellentlabel'] = 'Top category label';
$string['excellentmin'] = 'Top category minimum';
$string['exportcsv'] = 'Export CSV';
$string['frequentfailures'] = 'Most frequently unmet indicators';
$string['gradefeedbackorigin'] = 'Video Tracker Ultimate recalculation origin: {$a}';
$string['grademax'] = 'Maximum grade';
$string['indicator'] = 'Indicator';
$string['indicatordeleted'] = 'Indicator deleted. Existing learner scores were queued for recalculation.';
$string['indicatorenabled'] = 'Enabled';
$string['indicatorname'] = 'Name';
$string['indicators'] = 'Indicators';
$string['indicatorsaved'] = 'Indicator saved. Existing learner scores were queued for recalculation.';
$string['indicatorweight'] = 'Weight';
$string['individualreport'] = 'Playback evidence for {$a}';
$string['insufficientlabel'] = 'Lowest category label';
$string['lastupdated'] = 'Last analytics update: {$a}';
$string['lastupdatedlabel'] = 'Last update';
$string['learners'] = 'Learners';
$string['maxposition'] = 'Maximum position';
$string['maxrate'] = 'Maximum playback rate';
$string['modulename'] = 'Video Tracker Ultimate';
$string['modulename_help'] = 'Creates an explainable Engagement Score from explicitly configured video playback evidence.';
$string['modulenameplural'] = 'Video Tracker Ultimate activities';
$string['noduration'] = 'Video duration is not known yet.';
$string['nofailures'] = 'No unmet indicators are present in the current cached results.';
$string['normalizedscoreexplain'] = 'Configured indicators awarded {$a->raw}/{$a->total} raw points, normalized transparently to {$a->score}/100.';
$string['notcalculated'] = 'Not calculated';
$string['percentwatched'] = 'Watched percentage';
$string['pluginadministration'] = 'Video Tracker Ultimate administration';
$string['pluginname'] = 'Video Tracker Ultimate';
$string['points'] = 'Points';
$string['privacy:metadata:log'] = 'Stores an audit trail of score recalculations.';
$string['privacy:metadata:log:newscore'] = 'New normalized score.';
$string['privacy:metadata:log:oldscore'] = 'Previous normalized score.';
$string['privacy:metadata:log:origin'] = 'Reason for recalculation.';
$string['privacy:metadata:log:timecreated'] = 'When recalculation occurred.';
$string['privacy:metadata:log:triggeredby'] = 'User who manually triggered recalculation, when applicable.';
$string['privacy:metadata:log:userid'] = 'Learner whose score was recalculated.';
$string['privacy:metadata:score'] = 'Stores the cached deterministic score and normalized playback evidence.';
$string['privacy:metadata:score:breakdownjson'] = 'Complete explainable indicator result breakdown.';
$string['privacy:metadata:score:calculatedby'] = 'User who explicitly triggered the calculation, when applicable.';
$string['privacy:metadata:score:gradeorigin'] = 'Calculation origin responsible for the last Gradebook update.';
$string['privacy:metadata:score:gradeupdated'] = 'When the Gradebook value was last updated.';
$string['privacy:metadata:score:metricsjson'] = 'Normalized playback metrics from Video Bridge public APIs.';
$string['privacy:metadata:score:origin'] = 'Server-side reason that triggered the calculation.';
$string['privacy:metadata:score:rawscore'] = 'Raw awarded points before transparent normalization.';
$string['privacy:metadata:score:score'] = 'Normalized 0–100 Engagement Score.';
$string['privacy:metadata:score:statuscode'] = 'Playback behavior category code derived from configured thresholds.';
$string['privacy:metadata:score:timecalculated'] = 'When the cached evidence was calculated.';
$string['privacy:metadata:score:totalweight'] = 'Total active indicator weight used in the calculation.';
$string['privacy:metadata:score:userid'] = 'The learner whose evidence is cached.';
$string['privacy:path'] = 'Video Tracker Ultimate evidence';
$string['rank'] = 'Rank';
$string['rankingenabled'] = 'Enable teacher-only ranking';
$string['rankingenabled_help'] = 'When enabled, users with the dedicated ranking capability may sort learners by score. No learner-facing ranking is created.';
$string['recalculateanalytics'] = 'Recalculate analytics';
$string['recalculationqueued'] = '{$a} server-side recalculation task(s) queued.';
$string['replays'] = 'Replays';
$string['reports'] = 'Reports';
$string['requiredcompletion'] = 'Mandatory for completion';
$string['reviewthreshold'] = 'Review threshold';
$string['rule'] = 'Rule';
$string['ruleevidence'] = 'Observed: {$a->actual}; target/limit: {$a->limit}';
$string['rulelimit'] = 'Limit';
$string['rulemet'] = 'Met';
$string['rulepending'] = 'Pending';
$string['ruletype'] = 'Rule type';
$string['ruletype:maxrate_lte'] = 'Maximum playback rate is at most X';
$string['ruletype:percent_gte'] = 'Watched percentage is at least X';
$string['ruletype:reachedend'] = 'Reached the end of the video';
$string['ruletype:replaycount_gte'] = 'Replay count is at least X';
$string['ruletype:seekcount_lte'] = 'Seek count is at most X';
$string['ruletype:segment_watched'] = 'Configured segment is watched';
$string['ruletype:sessions_gte'] = 'Session count is at least X';
$string['ruletype:sessions_lte'] = 'Session count is at most X';
$string['ruletype:watchtime_gte'] = 'Effective watch time is at least X seconds';
$string['saveindicator'] = 'Save indicator';
$string['scorecomposition'] = 'Score composition';
$string['scoredistribution'] = 'Score distribution';
$string['scoreexplanation'] = 'The score is deterministic: each indicator exposes its metric, threshold, weight, awarded points and pass state. It is evidence about configured playback behavior only.';
$string['scoreheader'] = 'Engagement Score and reports';
$string['scorenotyetcalculated'] = 'No cached Engagement Score exists yet. The server will calculate it after playback evidence is available.';
$string['seeks'] = 'Seeks';
$string['segmentcoverage'] = 'Required segment coverage (%)';
$string['segmentdisplay'] = '{$a->start}s–{$a->end}s, {$a->mincoverage}% required';
$string['segmentend'] = 'Segment end (seconds)';
$string['segmentstart'] = 'Segment start (seconds)';
$string['sessionend'] = 'Session end';
$string['sessions'] = 'Sessions';
$string['sessionstart'] = 'Session start';
$string['state'] = 'State';
$string['status'] = 'Playback category';
$string['statusheader'] = 'Playback behavior categories';
$string['taskreconcile'] = 'Reconcile Video Tracker Ultimate cached analytics';
$string['teacherranking'] = 'Teacher-only ranking';
$string['usegrade'] = 'Use Engagement Score as grade';
$string['usegrade_help'] = 'Disabled by default. When enabled, the deterministic 0–100 Engagement Score is scaled to the configured maximum grade and sent to the Moodle Gradebook.';
$string['usernotavailable'] = 'The requested user is not available in your current activity/group scope.';
$string['videosource'] = 'Video source';
$string['videosourceheader'] = 'Video source';
$string['videotrackerultimate:addinstance'] = 'Add a Video Tracker Ultimate activity';
$string['videotrackerultimate:export'] = 'Export Video Tracker Ultimate reports';
$string['videotrackerultimate:manageindicators'] = 'Manage Engagement Score indicators';
$string['videotrackerultimate:recalculate'] = 'Recalculate video analytics';
$string['videotrackerultimate:view'] = 'View Video Tracker Ultimate';
$string['videotrackerultimate:viewranking'] = 'View teacher-only ranking';
$string['videotrackerultimate:viewreport'] = 'View Video Tracker Ultimate reports';
$string['videotrackerultimatename'] = 'Activity name';
$string['watchedmap'] = 'Watched map';
$string['weightcomplete'] = 'Enabled indicator weights total exactly 100 points.';
$string['weightwarning'] = 'Enabled indicators currently total {$a}/100. Scores remain explainable and are normalized to 0–100 until the configuration reaches 100 points.';
