# Video Tracker Ultimate

Video Tracker Ultimate is the evidence-oriented activity in the Video Tracker family. It combines detailed playback analytics from `local_video_bridge` with configurable, deterministic rules and an explainable Engagement Score.

The activity never infers attention, intelligence, learning, motivation, effort, ability or academic mastery. Its score only summarizes the playback facts that a teacher explicitly chose to measure, such as watched coverage, effective playback time, number of sessions, playback rate, seeks, replays, reaching the end and coverage of selected timeline segments.

## Explainable Engagement Score

Teachers build indicators from a fixed catalogue of safe rules. There is no expression language, PHP, JavaScript, SQL, `eval()` or executable teacher input.

Initial rule types are:

- watched percentage >= X;
- effective watch time >= X seconds;
- sessions >= X;
- sessions <= X;
- maximum observed playback rate <= X;
- reached the end;
- timeline segment watched;
- seek count <= X;
- replay count >= X.

Every indicator stores a name, rule type, weight, threshold/configuration, enabled state and optional completion requirement. Minimum-target indicators award proportional points up to their target, while ceiling and boolean rules are pass/fail. Segment rules award proportional points from the actual coverage of the configured segment. The stored score contains the complete breakdown, actual metric, threshold, ratio, awarded points and rule configuration used at calculation time.

If active weights do not total 100, the raw result is still preserved and shown (for example 72/90) and the public Engagement Score is normalized to 0–100. This normalization is displayed in the explanation and never hidden.

## Video Bridge

`local_video_bridge` is a mandatory dependency. Video Tracker Ultimate does not implement players, providers, viewing maps or raw playback tracking.

The activity requests detailed telemetry with:

```php
$manager->get_player_config(
    $activity,
    $context,
    \local_video_bridge\analytics::LEVEL_DETAILED
);
```

Recalculation reads only public Video Bridge APIs:

- `local_video_bridge\progress\manager::get_progress()`;
- `local_video_bridge\analytics::media_hash()`;
- `local_video_bridge\analytics::get_session_metrics()`.

It never queries `local_video_bridge_*` tables directly.

The normalized snapshot cached by Video Tracker Ultimate contains watched percentage, duration, unique watched time, effective playback time, elapsed session time, session count, pauses, seeks, replays, maximum observed rate, end reached, watched ranges, last position and playback regularity. Opening a report never replays raw telemetry.

## Recalculation

Scores can be recalculated through an adhoc task, a scheduled consistency task or the teacher-only **Recalculate analytics** action. Each stored score records its calculation timestamp and origin. Relevant activity updates queue recalculation so rule changes, grading changes and completion changes do not silently leave stale scores.

## Reports

Teachers with the appropriate capabilities can use:

- class overview with average score, score distribution, completion, average watched percentage and effective playback time;
- learners below the configured review threshold;
- indicators most frequently not met;
- indicator report;
- learner list;
- individual evidence page with sessions, viewing map, cached metrics, met/pending rules and full score composition;
- CSV export;
- optional teacher-only ranking.

There is no public learner ranking.

Group access follows Moodle activity group rules and capabilities.

## Gradebook and completion

Using the Engagement Score as a grade is disabled by default. When enabled, the 0–100 score is scaled to the configured maximum grade and sent through the Moodle Gradebook API. The score row stores when and why the grade was last updated.

Completion is configured independently and may require a minimum Engagement Score, minimum watched percentage and/or one or more indicators marked as mandatory for completion.

## Privacy and security

The plugin validates context, capability, group visibility and sesskey on state-changing operations. The External API validates module context and never trusts a client-provided score. Rule evaluation always happens on the server.

The Privacy API exports the learner's cached metrics, score, complete breakdown and the applied rule definitions. Backup and restore include activity configuration and indicators but deliberately exclude learner analytics/scores so restored activities start with evidence belonging to the destination course.

## Important interpretation boundary

**Engagement Score is not a diagnosis and is not evidence of learning.**

A high score only means that the configured playback conditions were satisfied to the configured degree. A low score only means that those playback conditions were not satisfied. Neither result establishes whether a learner understood the content, paid attention, learned efficiently, has a particular ability, or needs pedagogical intervention. Those conclusions require other evidence and human judgment.
