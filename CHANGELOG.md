# Changelog

This changelog covers only the two most recent published GitHub releases. Each entry describes the changes between that release and the immediately preceding published release. Subsequent changes on `master` are excluded.

## 1.0.5 - 2026-10-23

### Changed

- Documented the required `local_video_bridge` dependency (version `2026100618` or later) and installation order in the README. The documented dependency chain also includes `local_ai_bridge` through Video Bridge; Video Bridge is not bundled with this activity.
- Updated the compiled AMD player adapter bootstrap in `amd/build/player.min.js`. The new output retains the existing adapter loading and initialization paths; the tag comparison does not establish a new player feature.
- Added a Git-reference-based concurrency configuration to the existing GitHub release workflow, without cancelling active runs.
- Updated the plugin release identifier from `1.0.4` to `1.0.5` and the build number from `2026101500` to `2026102300`.

[Compare 1.0.4...1.0.5](https://github.com/EduardoKrausME/moodle-mod_videotrackerultimate/compare/1.0.4...1.0.5)

## 1.0.4 - 2026-10-15

### Changed

- Updated the GitHub Actions CI PostgreSQL service from version 16 to 17.
- Updated the MariaDB CI service from version 10 to 11.4, adjusting its startup environment variables and health-check settings.
- Added the plugin image at `pix/icon.png` and reorganized the SVG logo asset as `pix/monologo.svg`.
- Updated the plugin release identifier from `1.0.3` to `1.0.4` and the build number from `2026101200` to `2026101500`.

No changes to video-tracking functionality were identified between the published `1.0.3` and `1.0.4` tags.

[Compare 1.0.3...1.0.4](https://github.com/EduardoKrausME/moodle-mod_videotrackerultimate/compare/1.0.3...1.0.4)
