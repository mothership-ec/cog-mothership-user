# Changelog

## 4.0.3

- `_1383909255_UpdateEmailSubscription` migration no longer does anything as it was obsolete and would break on installations that did not have the Mailing module installed (which is not installed by default)

## 4.0.2

- Fix casing inconsistency on sidebar

## 4.0.1

- Work-around for soft dependency on mailing module (to be proper abstracted out later, see https://github.com/mothership-ec/cog-mothership-user/issues/38)
- Added changelog

## 4.0.0

- Initial open source release