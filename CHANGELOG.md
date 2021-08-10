# Changelog

## Version 1.0.0
- First released bundle to satis
- added translation

## Version 1.2.0
- first sv-video deploy to prod
- translations
- extended console command

## Version 1.2.0
- hiding navs
- added copy url to video
- extend copy url in admin page (with and without navigation)

## Version 1.2.4
*Sun, 13 Jun 2021 22:15:43 +0000*
- Improve video group layout, added fields for hide nav/groups in video groups
- you have do update the entities!

## Version 1.2.5
*Mon, 14 Jun 2021 13:25:43 +0000*
- Prepare date for private recipe server


## Version 1.3.0
*Thu, 17 Jun 2021 20:58:49 +0000*
- added password check for videos
- added 'hideOnHomePage' for groups


## Version 1.3.1
*Fri, 18 Jun 2021 19:11:33 +0000*
- added breadcrumb
- expression validation for private video password
- new parameter enablePrivate


## Version 1.3.2
*Sat, 19 Jun 2021 18:48:48 +0000*
- added VideoShortCallTrait to implement standardizied short urls


## Version 1.3.3
*Thu, 24 Jun 2021 15:52:59 +0000*
- added private groups, using shortname for video links


## Version 1.3.4
*Thu, 24 Jun 2021 17:05:36 +0000*
- added hideOnHomePage


## Version 1.3.5
*Sun, 27 Jun 2021 17:25:11 +0000*
- readded _list.html.twig, used in some 3party apps


## Version 1.3.6
*Sun, 27 Jun 2021 17:40:37 +0000*
- hide group on video admin edit, if enableGroups == false


## Version 1.4.0
*Wed, 30 Jun 2021 18:49:03 +0000*
- first public


## Version 1.4.1
*Sat, 03 Jul 2021 21:11:25 +0000*
- added video group share urls and copy link in video group lists


## Version 1.4.2
*Sun, 04 Jul 2021 20:40:37 +0000*
- video groups improved, code cleaned


## Version 1.4.3
*Tue, 06 Jul 2021 20:33:35 +0000*
- added basic video statistics


## Version 1.4.4
*Thu, 08 Jul 2021 12:53:31 +0000*
- changed html status code to 303 for shortname and form redirects


## Version 1.5.0
*Sun, 11 Jul 2021 20:27:10 +0000*
- added sort videos


## Version 1.5.1
*Mon, 12 Jul 2021 19:53:15 +0000*
- small enhancements


## Version 1.6.0
*Sat, 17 Jul 2021 16:52:55 +0000*
- integrate video statistics


## Version 1.6.1
*Sun, 18 Jul 2021 20:48:05 +0000*
- integrate video statistic overview (for all videos)


## Version 1.7.0
*Fri, 23 Jul 2021 20:45:57 +0000*
- added chartjs for country stats, copy js-controller during install


## Version 1.7.1
*Sun, 01 Aug 2021 14:55:47 +0000*
- preparation for LogViewer


## Version 1.7.2
*Sun, 01 Aug 2021 15:06:48 +0000*
- added LogDataProvider as a service


## Version 1.7.3
*Sun, 01 Aug 2021 15:15:16 +0000*
- added svc_video.service.log-data-provider as an alias


## Version 1.8.0
*Sun, 01 Aug 2021 20:19:50 +0000*
- implement LogDataProvider for svc/log Log Viewer


## Version 1.8.1
*Tue, 03 Aug 2021 14:16:50 +0000*
- using phpstan, code improvement


## Version 1.8.2
*Tue, 03 Aug 2021 15:59:23 +0000*
- update svc/log-bundle to version>1


## Version 1.8.2
*Tue, 03 Aug 2021 15:59:56 +0000*
- update svc/log-bundle to version>1


## Version 1.8.3
*Tue, 03 Aug 2021 16:00:25 +0000*
- update svc/log-bundle to version>1


## Version 1.8.4
*Wed, 04 Aug 2021 11:55:25 +0000*
- fixed hide-nav error for direct video links


## Version 1.8.5
*Thu, 05 Aug 2021 07:33:41 +0000*
- add all statistics to video and group overviews, display statistics for all groups


## Version 1.8.6
*Fri, 06 Aug 2021 20:46:12 +0000*
- integrate ajax log viewer in video and video group statistics


## Version 1.8.7
*Sun, 08 Aug 2021 17:18:14 +0000*
- update to new version of like-bundle with new stimulus controller call


## Version 1.9.0
*Sun, 08 Aug 2021 20:25:17 +0000*
- install stimulus controller via ux-webpack-logic


## Version 1.9.1
*Mon, 09 Aug 2021 20:42:24 +0000*
- use stimulus controller from svc/util-bundle (clipboard)


## Version 1.9.3
*Tue, 10 Aug 2021 20:42:31 +0000*
- use stimulus controller from svc/util-bundle (show-password and wysiwyg)
