Installing your Connect2Moodle plugin!:
======================================

    1. Download Connect2Moodle plugin package:
    http://www.refineddata.com/connect-2-moodle/ and unzip it into the root Moodle directory.

    2. Run plugins upgrade from Moodle (as administrator) or from the server:
    php admin/cli/upgrade.php

Setting up Refined Services:
============================

Refined Services is an integration service to manage the integration between your LMS and Adobe Connect.  This service provides added security, flexibility and simplifies the process for updates and maintenance by keeping all data centralized.

It is essential that you follow the instructions below on your LMS to ensure all your users and content are updated in Refined Services:

    1. Go to Site admin >> Plugins >> Local plugins >> Refined Services
    Click first on "Create Connect Account" this creates your Adobe Connect account in RS. (credentials to be added later)
    2. Go to Site admin >> Plugins >> Activity modules >> Connect activity
    Manually input your Adobe Connect credentials then click update
    3. Go to Site admin >> Plugins >> Filters >> Connect
    Make one change to the page (suggestion: Mouse-over for students).  Then save and Moodle will then trigger an update to RS for all AC filters (once complete the setting can be changed back and saved again)
    4. Return to Site admin >> Plugins >> Local plugins >> Refined Services
        a. Click "Update Users" and wait for the update to stop running.
        b. Click on "Update Courses" and wait for the update to stop running.
        c. Click on "Update Connect Activities" and wait for the update to stop running.

For more information and a User Manual please visit our support site:
http://support.refineddata.com/hc/en-us/categories/200134280-Refined-Training
