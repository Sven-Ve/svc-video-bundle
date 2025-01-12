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


## Version 1.9.4
*Mon, 16 Aug 2021 22:09:17 +0000*
- fixed wrong initial video sorting


## Version 1.10.0
*Sun, 24 Apr 2022 13:22:31 +0000*
- ready for symfony 5.4 and 6


## Version 1.10.1
*Sun, 24 Apr 2022 13:25:35 +0000*
- ready for symfony 5.4 and 6 and newer Svc bundles


## Version 1.10.2
*Wed, 27 Apr 2022 19:53:32 +0000*
- update symfony/ux-chartjs to 2.x


## Version 1.10.3
*Wed, 27 Apr 2022 20:10:12 +0000*
- changed to @hotwired/stimulus in js


## Version 3.0.0
*Tue, 03 May 2022 09:26:34 +0000*
- runs only with symfony 5.4/6 and php 8


## Version 3.0.1
*Tue, 03 May 2022 20:43:28 +0000*
- format code with php-cs-fixer


## Version 3.1.0
*Sat, 07 May 2022 19:25:18 +0000*
- move group operations under /admin/...


## Version 3.1.1
*Fri, 13 May 2022 21:10:12 +0000*
- add php attributes


## Version 3.1.2
*Sun, 15 May 2022 08:21:40 +0000*
- small format changes


## Version 3.1.3
*Mon, 30 May 2022 16:03:43 +0000*
- improved console command


## Version 3.1.4
*Fri, 24 Jun 2022 18:04:58 +0000*
- fixed ux-chart options


## Version 4.0.0
*Tue, 19 Jul 2022 20:02:27 +0000*
- build with Symfony 6.1 bundle features, runs only with symfony 6.1


## Version 4.0.1
*Thu, 21 Jul 2022 18:46:38 +0000*
- licence year update


## Version 4.1.0
*Tue, 16 Aug 2022 20:01:47 +0000*
- added pagination for video admin


## Version 4.2.0
*Tue, 23 Aug 2022 18:21:39 +0000*
- added tagging


## Version 4.2.1
*Sat, 17 Sep 2022 19:45:10 +0000*
- fixes video group error (wrong form type)


## Version 4.3.0
*Sat, 18 Mar 2023 15:57:28 +0000*
- ready to run with symfony 6.2 and phpunit 10


## Version 4.3.1
*Sun, 23 Apr 2023 13:00:22 +0000*
- adopt new vimeo security model


## Version 4.3.2
*Sun, 23 Apr 2023 13:30:55 +0000*
- fix wrong vimeo video call


## Version 5.0.0
*Fri, 22 Dec 2023 21:09:29 +0000*
- ready for symfony 6.4 and 7


## Version 5.0.1
*Fri, 22 Dec 2023 21:54:21 +0000*
- ready for symfony 6.4 and 7 - fix deprecations


## Version 5.1.0
*Mon, 01 Jan 2024 19:51:26 +0000*
- ready for assetmapper


## Version 5.1.1
*Tue, 02 Jan 2024 20:50:48 +0000*
- fix php 8.2 deprecation
add daily statistic to all_stats


## Version 5.2.0
*Sun, 21 Jan 2024 16:22:51 +0000*
- adding autosubmit to video search


## Version 5.3.0
*Sat, 23 Mar 2024 20:15:34 +0000*
- runs with doctrine/orm ^3 too


## Version 5.3.1
*Sat, 15 Jun 2024 19:42:54 +0000*
- update requirements


## Version 5.4.0
*Sun, 07 Jul 2024 18:15:19 +0000*
- more test, check with phpstan level 6, fixed level 6 errors


## Version 5.5.0
*Sun, 28 Jul 2024 19:55:05 +0000*
- switch to svc/log-bundle 6.x, use jbtronics/settings-bundle now


## Version 5.6.0
*Sun, 17 Nov 2024 09:45:53 +0000*
- switch to svc/log-bundle 7.x (new data model)


## Version 5.7.0
*Wed, 20 Nov 2024 18:53:34 +0000*
- switch to svc/log-bundle 7.3 (new data model), upgrade phpstan to 2.x


## Version 5.7.1
*Mon, 23 Dec 2024 21:19:11 +0000*
- add sitemap.xml option to route.yaml


## Version 5.7.2
*Fri, 10 Jan 2025 21:47:58 +0000*
- improve route requirements for password entry


## Version 5.7.3
*Sun, 12 Jan 2025 15:02:08 +0000*
- log hacking attemts during password entry
