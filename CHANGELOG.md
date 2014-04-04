2.0 Release Candidate 3
========================
*  Removed all references to isAnonymous.  Added unit tests for Projects Controller
*  Adds isAnonymous method, removes isAnonymous virtual field
*  Fixes issue #18  - Call startOpCode unconditionally
*  Added operation controller unit tests
*  Fixes bug in Operations controller
*  Added copy operation and project functions -- Fixes isssue #15
*  Added unit tests for operations model
*  Refactors some gCode generating code
*  Fixes issue #8
*  Added more project unit tests
*  Fix bug in user default handling. Add unit tests for project model
*  Added tests for email functions
*  More refactoring of user save methods.  Additional user unit tests
*  Moved email update functions to model
*  Abstracted out new user method
*  Fix for user secret validation.
*  Added more user unit tests
*  Updated travis build to check coding standards
*  Added code coverage badge to readme
*  Added code coverage reporting
*  Updated readme with build status
*  Added copying of default config file to travis build
*  Changed travis config to create schemas
*  Add start of unit tests.
*  Update travis config
*  Added ability to admins to login as another user.
*  Add fields to user index for admins
*  Fixes #14 - Incorrent order field.
*  Added update script
*  Change version on develop

2.0 Release Candidate 2
========================
*  Add user profile page listing public projects.
*  Add project counterCaches Restlye public index.
*  Fix formatting on README.md
*  Changed password update functionality to require current password
*  Added link to user profile on project view page
*  Make containable default behavior for models
*  Moved declaration for virtualfields in project
*  Add ability for users to create defaults for projects
*  Fixes #12 - Shortens text on chip display
*  Fixes #11 - Corrected typo on edit project page
*  Updates to gitignore files for random tmp files being generated
*  Switch to database session handling.
*  Add option to exclude empty projects for admins
*  Update default ordering for projects
*  Updates to how searches are proccessed to fix page not found bug
*  Bug fix to fix E-notice on clearing search by submitting empty form
*  Added ability to identify active users.
*  Fix errorneous hasOne association on user sessions