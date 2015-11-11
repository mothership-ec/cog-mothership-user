# Changelog

## 4.3.1

- Resolve issue in **User Summary** report where it would not display users without addresses
- Added **All** option to address filter on **User Summary** report

## 4.3.0

- Made **User Summary** report more useful and flexible. Now contains the following columns:
    - Email
    - Forename
    - Surname
    - Address line 1
    - Address line 2
    - Address line 3
    - Address line 4
    - Town
    - Postcode
    - State
    - Country
    - Created at
- Added `Report\Filter\AddressTypeFilter` class for filtering the report by address type
- Added `Report\Filter\CountryFilter` class for filtering the report by country
- Added `Report\Filter\CreatedAtFilter` class for filtering the report users which were created within a certain range
- Added `Events` class of constants for report names
- Added `EventListener::applyReportFilters()` event listener for modifying queries from filters when rendering a report
- Added `user.report.filter.address_type` service that returns instance of `Report\Filter\AddressTypeFilter`
- Added `user.report.filter.country` service that returns instance of `Report\Filter\CountryFilter`
- Added `user.report.filter.created_at` service that returns instance of `Report\Filter\CreatedAtFilter`
- Added `user.report.user_summary.filters` service that returns instance of `Message\Mothership\Report\Filter\Collection`
- Renamed `user.user_summary` service to `user.report.user_summary`
- Deprecated `user.user_summary` service
- `Report\UserSummary` implements `Message\Mothership\Report\Report\AppendQuery\FilterableInterface`
- `Report\UserSummary` now takes `Message\Cog\Location\CountryList` as third parameter
- `Report\UserSummary` now takes `Message\Cog\Location\StateList` as forth parameter
- `Report\UserSummary` now takes `Message\Mothership\Report\Filter\Collection` as fifth parameter
- `Report\UserSummary` now takes `Message\Cog\Event\Dispatcher` as sixth parameter
- `Report\UserSummary` fires `Message\Mothership\Report\Event\ReportEvent` when generating a report
- Increase Reports dependency to 2.2

## 4.2.1

- Resolve issue where order overview page would break if the returns module is not installed
- Do not display returns section of order overview if there are no returns

## 4.2.0

- Added `FieldType` namespace for content fields
- Added `FieldType\User` class which extends `Message\Cog\Field\Field` for assigning users and user profiles to instances of `Message\Cog\Field\ContentTypeInterface`
- Added `User\Loader` class to alias `Message\User\Loader`. This class adds a method `getByType()`, and will eventually extend `Message\User\Loader` once it has been refactored to use the query builder
- Added `ms.user.loader` service for getting the `User\Loader` from the service container. **Note:** This will eventually be the same as `user.loader` and will therefore be deprecated, this service is just for the interim
- Remove debug code when wrong password is entered on `Controller\Account\Edit`
- Updated `cog` dependency to 4.9

## 4.1.0

- Ability to set user types against a user (defaults to 'none'). A 'Profile' tab will appear in the user admin screen if the type is not set to 'none'.
- Added `Type\UserTypeInterface` interface which extends `Message\Cog\Field\ContentTypeInterface` to represent a user type
- Added `Type\Collection` class for registering and storing user types in the service container
- Added `Type\NoneType` class as a null-type for users
- Added `Type\TeamMemberType` class as a basic type for staff member profiles
- Added `Type\TypeLoader` class for loading user types from the database
- Added `Type\TypeEdit` class for updating user type information in the database
- Added `Type\Profile` class for holding the content determined by the user's type
- Added `Type\ProfileEdit` class for saving changes to a profile to the database
- Added `Type\ProfileLoader` class for loading profile data from the database and populating instances of `Profile` with it
- Added `Type\ProfileFactory` class for creating new instances of `Profile`, with fields built using the `Message\Cog\Field\Factory` class
- Added `Type\Event` event class to store user and profile data
- Added `Type\Events` class to list event names relating to user types and profiles
- Added `User\Profile` controller for handling the rendering of the profile form and updating of profiles
- Amended `User\DetailsEdit` controller to update the user type
- Amended `Form\UserDetails` to include 'type' field for setting the user type (changing the user type will not delete existing profile data until the profile has been updated, it will simply not appear in the form if it is no longer relevant)
- Amended `Controller\Register` controller so that `User\Event\Event::CREATE` event is fired upon successful registration of user
- Added `setProfileType()` method to `EventListener` to set the user type to 'none'
- Added migration for `user_profile` table
- Added migration for `user_type` table
- Increased Cog dependency to 4.7

## 4.0.3

- `_1383909255_UpdateEmailSubscription` migration no longer does anything as it was obsolete and would break on installations that did not have the Mailing module installed (which is not installed by default)

## 4.0.2

- Fix casing inconsistency on sidebar

## 4.0.1

- Work-around for soft dependency on mailing module (to be properly abstracted out later, see https://github.com/mothership-ec/cog-mothership-user/issues/38)
- Added changelog

## 4.0.0

- Initial open source release