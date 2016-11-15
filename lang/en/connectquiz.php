<?php
$string['modulenameplural']     = 'Connect Quiz';
$string['modulename']           = 'Connect Quiz';
$string['modulename_help']      = 'A Connect Quiz is an Adobe Presenter or Captivate quiz that has been uploaded to your content library in Adobe Connect and added to the course as a gradable activity. Design interactive and dynamic quizzes with the ability to add sound, video and sophisticated design elements available with the Captivate or Adobe Presenter software. Connect quiz activities can be reported on, used as prerequisites and be the basis for course completion and/or issuing certificates. Use custom or standard icons on the course page with many display options.<br /><br />
<b>Note:</b> be sure your quiz grading is set from within the software to correspond with the grading options in the Connect Quiz activity settings. <br /><br />
 <a target="_blank" href="http://support.refineddata.com/hc/en-us/articles/202654500-Connect-Slideshow-Quiz-">More information about Connect Quizzes</a>.';
$string['pluginname']           = 'Connect Quiz';
$string['pluginadministration'] = 'Connect Quiz administration';
$string['intro']                = 'Introduction';
$string['launch']               = 'Launch Adobe Connect';

$string['gradinghdr'] = 'Grading Options';
$string['prochdr']    = 'Session Grading';

$string['grading']        = 'Grading';
$string['addmoregrading'] = 'Add 1 more threshold';

$string['threshold']    = 'Threshold';
$string['url']          = 'Custom URL';
$string['url_location'] = 'URL Location';
$string['grade']        = 'Grade %%';
$string['slide']        = 'Slide';
$string['summary']      = 'Summary';
$string['start']        = 'Start Date';
$string['duration']     = 'Duration';
$string['browse']       = 'Browse Adobe Connect';
$string['whensaved']    = ' will be added to Connect Central when saved.';
$string['choose']       = 'Choose';

$string['display']               = 'Display tags';
$string['displayoncourse']       = 'Display on course page';
$string['update']                = 'Update on Connect Central';
$string['updatedts']             = 'Update date information';
$string['configupdate']          = 'Whether changes or new connect activities information should be updated on Connect Central server.';
$string['configupdatedts']       = 'If updating Connect Central, also update the date information (off if reusing meetings).';
$string['template']              = 'Meeting template';
$string['configtemplate']        = 'Template to use when meetings are automatically created on Connect Central.  When viewing the meeting details on Connect Central, the sco-id of the meeting will be in the url.';
$string['icondisplay']           = 'Allow Icon Display';
$string['configicondisplay']     = 'Set if Connect Activities can be displayed as icons with the [[connect#url]] tags.';
$string['configdisplayoncourse'] = 'By default, make iconic display appear on the main course page';
$string['autofolder']            = 'Meeting folder';
$string['configautofolder']      = 'Folder id to put automatically created meeting rooms inside of.  When viewing the folder on Connect Central, the sco-id of the folder will be in the url.';
$string['cfgmaxviews']           = 'Maximum views set on activities';
$string['configmaxviews']        = 'When zero or positive number, a maximum number of views can be imposed on users.  The number set here becomes the default.  A negative number turns this feature off and it is not selectable for activities.  Zero in activity turns off for that activity.';
$string['maxviews']              = 'Maximum Views';
$string['view']                  = ' View';
$string['views']                 = ' Views';
$string['disabled']              = 'Disabled';
$string['overmaxviews']          = 'You have exceeded the maximum number of times you can view this activity.';

$string['meetingicon']       = 'Meeting Standard Icon';
$string['meetingicondesc']   = 'When one of the standard icon options is used for a connect meeting, it will display this image.';
$string['quizicon']          = 'Quiz Standard Icon';
$string['quizicondesc']      = 'When one of the standard icon options is used for a connect quiz, it will display this image.';
$string['slideshowicon']     = 'Slideshow Standard Icon';
$string['slideshowicondesc'] = 'When one of the standard icon options is used for a connect slideshow, it will display this image.';

$string['remhdr']    = 'Calendar Reminders';
$string['reminders'] = 'Linked to calendar';
$string['mtgdate']   = 'Event Start Date';
$string['reminder']  = 'Reminder';
$string['before']    = 'Before';
$string['after']     = 'After';
$string['weeks']     = 'weeks';
$string['week']      = 'week';

$string['initdelay'] = 'Expected duration of activity';
$string['loops']     = 'Number of times to check grading after initial duration';
$string['loopdelay'] = 'Delay between rechecks of grading (min)';
$string['surveyid']  = 'Survey ID';

$string['thresholderrorformat'] = 'Threshold must be numeric.';
$string['thresholderrorrange']  = 'Threshold must be greater than zero.';
$string['thresholderrororder']  = 'Thresholds must be in descending order.';
$string['thresholderrorextras'] = 'Information entered after last threshold.';
$string['gradeerrorformat']     = 'Grade must be numeric.';
$string['gradeerrorrange']      = 'Grade must be greater than zero.';
$string['gradeerrorextras']     = 'Information entered after last threshold.';
$string['nogrades']             = 'No grades available.';

$string['strpopups'] = 'Please ensure popups are enabled.  A popup window should appear shortly.  If not, please click on the icon below.';

$string['type']            = 'Type';
$string['typelist']        = 'Unknown';
$string['typelistslide']   = 'Slideshow';
$string['typelistmeeting'] = 'Meeting';
$string['typelistcquiz']   = 'Connect Quiz';
$string['typelistvideo']   = 'Video (deprecated)';

$string['tg'] = 'Threshold';

$string['tgslide']      = 'Threshold';
$string['tgslide_help'] = 'Slides watched -> Activity Grade';

$string['tgmeeting']      = 'Threshold';
$string['tgmeeting_help'] = 'Minutes in Meeting -> Activity Grade';

$string['tgmeetingvp']      = 'Threshold';
$string['tgmeetingvp_help'] = 'Vantage Point Percentage -> Activity Grade';

$string['tgcquiz']      = 'Threshold';
$string['tgcquiz_help'] = 'Grade on Quiz -> Activity Grade';

$string['tgvideo']      = 'Threshold';
$string['tgvideo_help'] = 'Percent of Video Watched -> Activity Grade';

$string['detailgrading'] = 'Detail Grading';

$string['detailgradingslide']      = 'Grading Options';
$string['detailgradingslide_help'] = '<b>Simple Grading</b>: launching the activity = 100%.<br />
<b>Adobe Grading</b>: pulls the number of slides viewed from Adobe Connect and calculates a grade percentage when thresholds are selected. If no thresholds are selected the number of slides viewed is stored as the grade.';

$string['detailgradingmeeting']      = $string['detailgradingslide'];
$string['detailgradingmeeting_help'] = '<b>Simple Grading</b>: launching the activity = 100%.<br />
<b>Adobe Grading</b>: pulls the number of minutes from Adobe Connect and calculates a grade percentage when thresholds are selected. If no thresholds are selected the number of minutes the user attended the meeting is stored as the grade.
<br /><b>Vantage Point Interaction</b>: pulls the percentage of challenges from Vantage Point and calculates a grade percentage when thresholds are selected. If no thresholds are selected then the Vantage Point percentage is stored as the grade.';

$string['detailgradingcquiz']      = $string['detailgradingslide'];
$string['detailgradingcquiz_help'] = '<b>Simple Grading</b>: launching the activity = 100%.<br />
<b>Adobe Grading</b>: pulls the grade from Adobe Connect and calculates a grade percentage when thresholds are selected. If no thresholds are selected the grade from Adobe is stored as the grade.';

$string['detailgradingvideo']      = $string['detailgradingslide'];
$string['detailgradingvideo_help'] = '<b>Simple Grading</b>: launching the activity = 100%.<br />
<b>Interaction Grading</b>:  pulls the position the user has reached in the video and calculates a  grade percentage when thresholds are selected. If no thresholds are selected the position the user reached is stored as the grade.
<br/>Each video has 5 positions which correspond with a % as follows: Position 1 = 5%, Position 2 = 25%, Position 3 = 50%, Position 4 = 75% and Position 5 = 95%.';

$string['off']                 = 'Simple Grading';
$string['fromadobe']           = 'Adobe Grading';
$string['fasttrack']           = 'FastTrack';
$string['vantagepoint']        = 'Vantage Point Interaction';
$string['interaction_grading'] = 'Interaction Grading';

$string['connect:host']      = 'Connect Host';
$string['connect:presenter'] = 'Connect Presenter';

$string['disphdr']      = 'Iconic Display';
$string['vdisphdr']     = 'Video Display';
$string['notfound']     = 'The requested url was not found. ';
$string['nameisfound']  = 'The requested name is found and can not be used';
$string['namenotfound'] = 'The requested name is not found and can be used';
$string['large']        = 'Large';
$string['medium']       = 'Medium';
$string['small']        = 'Small';
$string['block']        = 'Block';
$string['custom']       = 'Custom';
$string['iconsize']     = 'Standard Icons';
$string['forceicon']    = 'Custom Icon File';
$string['center']       = 'Center';
$string['left']         = 'Left';
$string['right']        = 'Right';
$string['iconpos']      = 'Icon Position';
$string['iconsilent']   = 'Suppress all Icon text';
$string['iconphone']    = 'Suppress phone information';
$string['iconguests']   = 'Do not allow guests';
$string['iconmouse']    = 'Suppress mouseovers';
$string['iconnorec']    = 'Do not point to recordings';
$string['extrahtml']    = 'Extra Iconic HTML';
$string['width']        = 'Width of player';
$string['height']       = 'Height of player';
$string['image']        = 'URL of layover image';
$string['textdisp']     = 'Display only activity name';

$string['telephony']   = 'Telephony';
$string['conference']  = 'Conference Number(s)';
$string['moderator']   = 'Moderator Code';
$string['participant'] = 'Participant Code';

$string['grading_help'] = '<h1>Grading</h1>

<p>With Detailed Grading off, simply running the Activity
produces a 100% grade.  This will likely be the most
common setting, and is the default.</p>

<p>The grading fields give you the opportunity to alter
a grade based on the number of slides viewed or the number
of minutes in a meeting or presentation.</p>

<p>The threshold is dependent on the type of activity:<br/>
Slideshow: number of slides<br/>
Meeting: minutes in meeting<br/>
Quiz: grade on quiz<br/>
Video: position reached (1=5%, 2=25%, 3=50%, 4=75%, 5=95%)<p/>

<p>Starting with the highest first, enter the threshold, then 
the grade achieved for reaching that threshold.  Not reaching 
the lowest threshold will result in a 0% grade.  Usually the
first threshold will be what is required for 100%</p>

<p>For example, for a meeting, if you enter:</p>

<p>Threshhold: 30<br />
Grade: 100%<br />
Threshold: 20<br />
Grade: 80%<br />
</p>

<p>Then being in this meeting for 10 minutes will result in 0%.
Being in this meeting for 20 minutes will result in 80%.  Being
in this meeting for 30 minutes will result in 100%.</p>';

$string['initdelay_help'] = '<h1>Initial Delay before Grading</h1>

<p>When grading Connect Activities, a delay is needed
to get the grades from the Connect server.  After a
set period of time, these grades need to be recovered
from that server.

<p>A good delay would be the expected length of the meeting,
or the expected time to complete the slideshow or presentation.</p>';

$string['loopdelay_help'] = '<h1>Delay between rechecks of grading</h1>

<p>When getting grades from the Connect server, on
the first pass, the grades may not yet be available.
This could be because the meeting is still going on, 
or the participant is delayed in watching the slideshow
or presentation.</p>

<p>The delay between rechecks of grading is the amount
of time to wait before rechecking the grades on the
Connect server.</p>';

$string['loops_help'] = '<h1>Number of times to recheck grading</h1>

<p>When getting grades from the Connect server, on
the first pass, the grades may not yet be available.
This could be because the meeting is still going on, 
or the participant is delayed in watching the slideshow
or presentation.</p>

<p>The number of times to recheck grading represents how 
many times to go back to the server to check for grades
before giving up.</p>';

$string['surveyid_help'] = '<h1>Survey ID</h1>

<p>The identifier of the survey to be taken prior to going into the meeting, quiz or show. Not applicable for video.</p>';

$string['email']          = 'Email Attendance report to';
$string['unenrol']        = 'Unenrol after attendance';
$string['unenrol_help']   = 'Setting this to yes will remove users from the course and delete their grades!';
$string['recurring']      = 'Recurring Meetings';
$string['recurringconf']  = 'Are you sure you want to delete this instance? - ';
$string['addinst']        = 'Add instance';
$string['noinst']         = 'There are currently no instances except the activity';
$string['edithdr']        = 'Next Instance';
$string['forcehdr']       = 'Override Activity';
$string['comphdr']        = 'Meeting Completion';
$string['attendancehdr']  = 'Attendance Options';
$string['otherhdr']       = 'Other Meeting Settings';
$string['compdelay']      = 'Completion Delay';
$string['compdelay_help'] = 'Completion Delay is based on the start time of the meeting and is set to trigger completion actions such as emailing of certificates, emailing of attendance report and unenrolling users.';
$string['afterstart']     = 'After Start';
$string['all']            = 'All';
$string['none']           = 'None';
$string['attended']       = 'Attended';
$string['absent']         = 'Absent';
$string['autocert']       = 'Issue certificate to Attendee';

$string['instantgrade']       = 'Pre-requisite Regrade';
$string['configinstantgrade'] = 'Regrade pre-requisites when displaying the course.  Turning this on has performance considerations.';

$string['helpmeeting'] = '
<p>Create live events, meetings and trainings in Adobe Connect directly from the LMS course page. This includes the ability to add new meetings or reusing existing meeting rooms. Meetings, individual instances or multiple instances (<a target="_blank" href="http://rds.adobeconnect.com/recurringmeetingsts">recurring</a>), can be set up days, weeks and months in advance. Linking the event to the course calendar allows for Refined <a target="_blank" href="http://rds.adobeconnect.com/usingremindersts/">Reminders</a> to be issued with links to the meeting being sent out selected times. Not only can you upload presentations and slides to the meeting in advance, you can record the session for viewing later with access directly from the course page. Enable detailed grading and the LMS will pull grading details from Adobe, you can then get a clear picture of who attended the meeting, for how long, how they answered any polls and even whether they were actively viewing the meeting for the entire time (if using Fast Track). Certificates of attendance can be issued afterwards. Meeting rooms can be re-used and you can specify that those who attended are un-enrolled from the course once complete.</p>
<p>A personalized list of upcoming meetings can be displayed for each user with the <a target="_blank" href="http://rds.adobeconnect.com/refinedtagsts/">My Meetings tag</a> and a personalized list of all recordings that the user has permissions to view via the <a target="_blank" href="http://rds.adobeconnect.com/myrecordingsts/">My Recordings</a> Block feature.</p>
<p>Participants can access the meeting though the reminder links or via the customizable activity icons without having to log in to Adobe separately.</p>
<p><a target="_blank" href="http://rds.adobeconnect.com/connectquiz/">More information about Connect Meetings</a>.</p>
';

$string['helpcquiz'] = '
<p>A Connect Quiz is an Adobe Presenter or Captivate quiz that has been uploaded to the Content Library of Adobe Connect and linked to the course (see <a target="_blank" href="http://rds.adobeconnect.com/captivatequizzes/">settings</a> for Captivate quizzes to ensure your content is set properly to report). These quizzes are interactive and can be designed to provide feedback or multiple attempts. The Browse URLs feature allows you to select the uploaded quiz from a drop-down menu from inside the course.</p>
<p>Detailed grading in the Connect Quiz can be selected to report grades and used to restrict access to subsequent course materials, creating a linear learning pathway. The grades appear in the Grader Report for the course.</p>
<p>Students can access the quiz via the customizable icon without having to log in to Adobe Connect.</p>
<p><a target="_blank" href="http://rds.adobeconnect.com/connectquiz/">More information about Connect Quizzes</a>.</p>
';

$string['helpslide'] = '
<p>A Connect Slideshow is an Adobe Presenter or Captivate presentation that has been uploaded to the Content Library of Adobe Connect and linked to the course. The Browse URLs feature allows you to select the uploaded slideshow via a drop-down menu from inside the course. A meeting recording can also be added as a slideshow by means the Browse URLs feature if the recording has been moved from the meeting to the Adobe Connect Content Library. </p>
<p>Slideshows can be restricted or even hidden until students have completed a meeting or viewed a video.</p>
<p>Detailed grading in the Slideshow can be selected to report grades as the raw number of slides viewed or as percentages and can be used to restrict access to subsequent course materials, creating a linear learning pathway.  Reporting of these grades by Adobe is not immediate therefore, if speed is required, do not select Detailed grading for slideshows and users will receive 100 when accessing the material.</p>
<p>Students can access the slideshow via the customizable icon without having to log in to Adobe Connect.</p>
<p><a target="_blank" href="http://rds.adobeconnect.com/connectslideshow/">More information about Connect Slideshows</a>.</p>
';

$string['helpvideo'] = '
<p>A Connect Video activity can play a video located anywhere on the web, including on Adobe. Unlike other Connect Activities where only the unique URL is needed to locate the content, Video URLs must contain the entire URL, even if the video is uploaded to Adobe. The Video can be added to the course as a link only or as a scalable JWPlayer. Background photos can be displayed in the player, either course by course or can be specified as the background for the entire site. If all videos used on the site are located at the same repository, the Full path to Flash Videos<i><b> </b></i>can be specified.</p>
<p>Wherever they are located, viewing of Videos can be graded as a percentage based on the position reached in the Video. </p>
<p>Students can access the slideshow via the link or the JWPlayer from inside the course; grades will appear in the Grader Report either way.</p>
<p><a target="_blank" href="http://rds.adobeconnect.com/connectvideo/">More information about Connect Videos</a>.</p>';

$string['returntocourse'] = 'Return to the course';

$string['no-access'] = 'You do not have permissions to access this room';

$string['generate'] = 'Generate';

$string['meetingnamefound'] = 'A meeting named {$a} exists already, please provide another name.';

$string['apicall']       = 'Connect API Call Made';
$string['video']         = 'Video';
$string['movie']         = 'Movie';
$string['movieposition'] = 'Movie Position';
$string['quizsubmitted'] = 'Connect Quiz Submitted';

$string['localrefinedservicesnotinstalled'] = 'Local Refined Services Plugin has not been installed.';
$string['connectserviceusernamenotgiven']   = 'Refined Service Username has not been set. <a href="{$a->url}">configure</a>';
$string['connectservicepasswordnotgiven']   = 'Refined Service Password has not been set. <a href="{$a->url}">configure</a>';
$string['connectsettingsrequirement']       = 'Connect Service settings of Local Refined Services Plugin are required to activate the following connect settings.';

$string['connect_protocol']              = 'Protocol';
$string['config_connect_protocol']       = 'http:// or https://';
$string['connect_server']                = 'AC server hostname';
$string['config_connect_server']         = '';
$string['connect_account']               = 'AC account ID';
$string['config_connect_account']        = '';
$string['connect_admin_login']           = 'AC admin login';
$string['config_connect_admin_login']    = '';
$string['connect_admin_password']        = 'AC admin password';
$string['config_connect_admin_password'] = '';
$string['connect_prefix']                = 'Username prefix';
$string['config_connect_prefix']         = '(if using usernames)';
$string['connect_debug']                 = 'API debug';
$string['config_connect_debug']          = '';
$string['connectquizcron']               = 'Mod Connect Quiz Cron';
$string['connectslidecron']              = 'Mod Connect Slideshow Cron';

$string['adobe_addin']       = 'Adobe Add-in Option';
$string['configadobe_addin'] = 'When enabled the ability to force students and teachers to launch the Adobe Connect meeting room in the Add-in or in the browser will be available.';
$string['addinroles']        = 'Force these roles into the browser';
$string['addinroles_help']   = 'When the meeting room opens it will open in the browser, as opposed to the Adobe Connect add-in, for the users with the selected course role.';

$string['connect_not_update'] = ' cannot be added to Connect Central because "' . $string['update'] . '" setting turned off';
$string['connect_details']    = 'Details';
$string['connect_name']       = 'Activity Name';

$string['connect_never'] = 'Never';

$string['rs_expired_message'] = 'Your refined services account has expired and been deactivated.  Please contact your administrator to renew it.';

$string['connect_grades_notyet'] = 'Some of your grades are not yet available, please try again in a few minutes';
$string['connectquiz:addinstance']   = 'Add connect quiz activity';

$string['past_sessions_heading'] = 'View Past Sessions';
$string['past_sessions_title']   = 'Past Sessions';

$string['activity_rs_expired_message'] = 'This activity module requires renewal by the system administrator.  Please contact them for any questions.';
$string['viewlimit']                   = 'Number of viewings: ';
$string['tollfree']                    = 'Toll-Free:';
$string['pphone']                      = 'Passcode:';

$string['launch_meeting'] = 'Click to enter the Meeting Room';
$string['launch_content'] = 'Click to view the Presentation';
$string['launch_archive'] = 'Click to view the Recording';
$string['launch_edit']    = 'Edit this Resource at Adobe Connect Central';

$string['viewpastsessions'] = 'Vantage Point Past Sessions';

$string['update_from_adobe'] = 'Update Activity from Adobe Connect';

$string['browsetitle']           = 'Browse Adobe Connect';
$string['refinedservices_debug'] = 'Enable debug logging in Refined Services';

$string['telephony']      = 'Meeting Dial-in Information';
$string['telephony_hint'] = '<font size=\"1\">Disable this option (No) if you prefer students to not see the dial-in information on the course page.</font>';

$string['mouseovers']      = 'Mouseovers for students';
$string['mouseovers_hint'] = '<font size=\"1\">Disable this option (No) if you prefer students to not have a box to display upon moving the mouse over the icon.</font>';

$string['popup_height']      = 'Popup window height.';
$string['popup_height_hint'] = '<font size=\"1\">Height of the popup window launched for an Adobe meeting/presentation.  Determines the default size of a popup window in pixels.</font>';

$string['popup_width']      = 'Popup window width.';
$string['popup_width_hint'] = '<font size=\"1\">Width of the popup window launched for an Adobe meeting/presentation.  Determines the default size of a popup window in pixels.</font>';
