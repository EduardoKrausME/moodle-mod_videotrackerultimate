<?php
/**
 * English strings.
 *
 * @package mod_videotrackerultimate
 */

defined('MOODLE_INTERNAL') || die;

$string['pluginname'] = 'Video Tracker Ultimate';
$string['modulename'] = 'Video Tracker Ultimate';
$string['modulenameplural'] = 'Video Tracker Ultimate activities';
$string['modulename_help'] = 'Creates an explainable Engagement Score from explicitly configured video playback evidence.';
$string['pluginadministration'] = 'Video Tracker Ultimate administration';
$string['videotrackerultimatename'] = 'Activity name';

$string['videotrackerultimate:addinstance'] = 'Add a Video Tracker Ultimate activity';
$string['videotrackerultimate:view'] = 'View Video Tracker Ultimate';
$string['videotrackerultimate:viewreport'] = 'View Video Tracker Ultimate reports';
$string['videotrackerultimate:manageindicators'] = 'Manage Engagement Score indicators';
$string['videotrackerultimate:recalculate'] = 'Recalculate video analytics';
$string['videotrackerultimate:export'] = 'Export Video Tracker Ultimate reports';
$string['videotrackerultimate:viewranking'] = 'View teacher-only ranking';

$string['videosourceheader'] = 'Video source';
$string['videosource'] = 'Video source';
$string['scoreheader'] = 'Engagement Score and reports';
$string['usegrade'] = 'Use Engagement Score as grade';
$string['usegrade_help'] = 'Disabled by default. When enabled, the deterministic 0–100 Engagement Score is scaled to the configured maximum grade and sent to the Moodle Gradebook.';
$string['grademax'] = 'Maximum grade';
$string['reviewthreshold'] = 'Review threshold';
$string['rankingenabled'] = 'Enable teacher-only ranking';
$string['rankingenabled_help'] = 'When enabled, users with the dedicated ranking capability may sort learners by score. No learner-facing ranking is created.';
$string['statusheader'] = 'Playback behavior categories';
$string['excellentlabel'] = 'Top category label';
$string['excellentmin'] = 'Top category minimum';
$string['adequatelabel'] = 'Second category label';
$string['adequatemin'] = 'Second category minimum';
$string['attentionlabel'] = 'Third category label';
$string['attentionmin'] = 'Third category minimum';
$string['insufficientlabel'] = 'Lowest category label';
$string['errorgrademax'] = 'The maximum grade must be greater than zero and no greater than 10000.';
$string['errorpercent'] = 'This value must be between 0 and 100.';
$string['errorstatusorder'] = 'Category thresholds must be ordered from highest to lowest.';

$string['completionminscore'] = 'Require minimum Engagement Score';
$string['completionminpercent'] = 'Require minimum watched percentage';
$string['completionindicators'] = 'Require indicators marked as mandatory for completion';
$string['completiondetail:score'] = 'Engagement Score of at least {$a}';
$string['completiondetail:percent'] = 'Watch at least {$a}% of the video';
$string['completiondetail:indicators'] = 'Meet all indicators marked as mandatory for completion';

$string['engagementscore'] = 'Engagement Score';
$string['scoreexplanation'] = 'The score is deterministic: each indicator exposes its metric, threshold, weight, awarded points and pass state. It is evidence about configured playback behavior only.';
$string['normalizedscoreexplain'] = 'Configured indicators awarded {$a->raw}/{$a->total} raw points, normalized transparently to {$a->score}/100.';
$string['status'] = 'Playback category';
$string['evidence'] = 'Evidence';
$string['points'] = 'Points';
$string['lastupdated'] = 'Last analytics update: {$a}';
$string['lastupdatedlabel'] = 'Last update';
$string['scorenotyetcalculated'] = 'No cached Engagement Score exists yet. The server will calculate it after playback evidence is available.';
$string['notcalculated'] = 'Not calculated';
$string['gradefeedbackorigin'] = 'Video Tracker Ultimate recalculation origin: {$a}';

$string['indicators'] = 'Indicators';
$string['indicator'] = 'Indicator';
$string['indicatorname'] = 'Name';
$string['indicatorweight'] = 'Weight';
$string['indicatorenabled'] = 'Enabled';
$string['requiredcompletion'] = 'Mandatory for completion';
$string['ruletype'] = 'Rule type';
$string['rulelimit'] = 'Limit';
$string['rule'] = 'Rule';
$string['actual'] = 'Observed value';
$string['state'] = 'State';
$string['saveindicator'] = 'Save indicator';
$string['addindicator'] = 'Add indicator';
$string['editindicator'] = 'Edit indicator';
$string['indicatorsaved'] = 'Indicator saved. Existing learner scores were queued for recalculation.';
$string['indicatordeleted'] = 'Indicator deleted. Existing learner scores were queued for recalculation.';
$string['errorweight'] = 'Weight must be greater than zero and no greater than 100.';
$string['errortotalweight'] = 'Enabled indicators already use {$a} points. The active total cannot exceed 100.';
$string['weightwarning'] = 'Enabled indicators currently total {$a}/100. Scores remain explainable and are normalized to 0–100 until the configuration reaches 100 points.';
$string['weightcomplete'] = 'Enabled indicator weights total exactly 100 points.';
$string['segmentstart'] = 'Segment start (seconds)';
$string['segmentend'] = 'Segment end (seconds)';
$string['segmentcoverage'] = 'Required segment coverage (%)';
$string['segmentdisplay'] = '{$a->start}s–{$a->end}s, {$a->mincoverage}% required';
$string['backtoactivity'] = 'Back to activity';
$string['rulemet'] = 'Met';
$string['rulepending'] = 'Pending';
$string['ruleevidence'] = 'Observed: {$a->actual}; target/limit: {$a->limit}';

$string['ruletype:percent_gte'] = 'Watched percentage is at least X';
$string['ruletype:watchtime_gte'] = 'Effective watch time is at least X seconds';
$string['ruletype:sessions_gte'] = 'Session count is at least X';
$string['ruletype:sessions_lte'] = 'Session count is at most X';
$string['ruletype:maxrate_lte'] = 'Maximum playback rate is at most X';
$string['ruletype:reachedend'] = 'Reached the end of the video';
$string['ruletype:segment_watched'] = 'Configured segment is watched';
$string['ruletype:seekcount_lte'] = 'Seek count is at most X';
$string['ruletype:replaycount_gte'] = 'Replay count is at least X';

$string['reports'] = 'Reports';
$string['classoverview'] = 'Class overview';
$string['individualreport'] = 'Playback evidence for {$a}';
$string['backtooverview'] = 'Back to overview';
$string['scorecomposition'] = 'Score composition';
$string['recalculateanalytics'] = 'Recalculate analytics';
$string['recalculationqueued'] = '{$a} server-side recalculation task(s) queued.';
$string['calculationorigin'] = 'Calculation origin: {$a}';
$string['exportcsv'] = 'Export CSV';
$string['averagescore'] = 'Average score';
$string['averagepercent'] = 'Average watched percentage';
$string['averagewatchtime'] = 'Average effective watch time';
$string['belowthreshold'] = 'Learners below review threshold';
$string['scoredistribution'] = 'Score distribution';
$string['frequentfailures'] = 'Most frequently unmet indicators';
$string['nofailures'] = 'No unmet indicators are present in the current cached results.';
$string['learners'] = 'Learners';
$string['teacherranking'] = 'Teacher-only ranking';
$string['rank'] = 'Rank';

$string['percentwatched'] = 'Watched percentage';
$string['effectivetime'] = 'Effective playback time';
$string['sessions'] = 'Sessions';
$string['seeks'] = 'Seeks';
$string['replays'] = 'Replays';
$string['maxrate'] = 'Maximum playback rate';
$string['maxposition'] = 'Maximum position';
$string['watchedmap'] = 'Watched map';
$string['noduration'] = 'Video duration is not known yet.';
$string['sessionstart'] = 'Session start';
$string['sessionend'] = 'Session end';
$string['usernotavailable'] = 'The requested user is not available in your current activity/group scope.';

$string['eventscoreupdated'] = 'Engagement Score updated';
$string['taskreconcile'] = 'Reconcile Video Tracker Ultimate cached analytics';

$string['privacy:path'] = 'Video Tracker Ultimate evidence';
$string['privacy:metadata:score'] = 'Stores the cached deterministic score and normalized playback evidence.';
$string['privacy:metadata:score:userid'] = 'The learner whose evidence is cached.';
$string['privacy:metadata:score:rawscore'] = 'Raw awarded points before transparent normalization.';
$string['privacy:metadata:score:totalweight'] = 'Total active indicator weight used in the calculation.';
$string['privacy:metadata:score:score'] = 'Normalized 0–100 Engagement Score.';
$string['privacy:metadata:score:statuscode'] = 'Playback behavior category code derived from configured thresholds.';
$string['privacy:metadata:score:metricsjson'] = 'Normalized playback metrics from Video Bridge public APIs.';
$string['privacy:metadata:score:breakdownjson'] = 'Complete explainable indicator result breakdown.';
$string['privacy:metadata:score:calculatedby'] = 'User who explicitly triggered the calculation, when applicable.';
$string['privacy:metadata:score:origin'] = 'Server-side reason that triggered the calculation.';
$string['privacy:metadata:score:timecalculated'] = 'When the cached evidence was calculated.';
$string['privacy:metadata:score:gradeupdated'] = 'When the Gradebook value was last updated.';
$string['privacy:metadata:score:gradeorigin'] = 'Calculation origin responsible for the last Gradebook update.';
$string['privacy:metadata:log'] = 'Stores an audit trail of score recalculations.';
$string['privacy:metadata:log:userid'] = 'Learner whose score was recalculated.';
$string['privacy:metadata:log:triggeredby'] = 'User who manually triggered recalculation, when applicable.';
$string['privacy:metadata:log:origin'] = 'Reason for recalculation.';
$string['privacy:metadata:log:oldscore'] = 'Previous normalized score.';
$string['privacy:metadata:log:newscore'] = 'New normalized score.';
$string['privacy:metadata:log:timecreated'] = 'When recalculation occurred.';
