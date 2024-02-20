# Usage

## Entities
Create tables (run `bin/console doctrine:schema:update --force`) or create a migration

## CSS

### SCSS
- include the scss file in your global css

```scss
// /assets/styles/app.sccs
...
@import '~@svc/video-bundle/styles/_svc_video.scss';
@import '~@svc/like-bundle/styles/layout/_likes.scss';
@import '@jcubic/tagger/tagger.css'; // for tagging, if you enable it
...
```

### Assetmapper
- include the css file in your global app.js

```js
// /assets/app.js
...
import './svc/video-bundle/styles/_svc_video.css';
import './svc/like-bundle/styles/layout/_likes.css'
import '@jcubic/tagger/tagger.css'; // for tagging, if you enable it
...
```


## Routes
- adapt the default url prefix in config/routes/svc_profile.yaml and enable translation (if you like it)

```yaml
# /config/routes/_svc_video.yaml
_svc_video:
    resource: '@SvcVideoBundle/config/routes.yaml'
    prefix: /_svc_video/{_locale}
    requirements: {"_locale": "%app.supported_locales%"}
```

## Enable/disable feature
```yaml
# /config/packages/svc_video.yaml
svc_video:
    # Enable likes for videos?
    enableLikes:          false

    # Enable short names for videos (for short URLs)?
    enableShortNames:     false

    # Enable videos groups?
    enableGroups:         false

    # Enable private videos?
    enablePrivate:        true

    # Enable video sort combobox in video overview?
    enableVideoSort:      true

    # Enable tagging for videos (needs js library @jcubic/tagger)
    enableTagging:        false

  # Enable pagination in video admin (needs babdev/pagerfanta-bundle)?
    enablePagination:     false

    # Default route, for redirect after errors
    homeRoute:            svc_video_list

```

## Short URLs
you have to call the trait VideoShortCallTrait in your home controller to use the short URLs
```php
<?php

namespace App\Controller;

use Svc\VideoBundle\Controller\VideoShortCallTrait;
...

class HomeController extends AbstractController
{
  use VideoShortCallTrait;
...
```

## Paths
- integrate the video controller via path "svc_video_run"
- integrate the video admin controller via path "svc_video_admin_index"
- integrate the video group controller via path "svc_video_group_index"
- video overview (with some statistics) via path "svc_video_stats"
- all video statistics via path "svc_video_all_stats"
- all video group statistics via path "svc_video_all_stats_group"

## Statistics

for the video statistics you have to run (about once per hour) the aggregation job

```console
$ bin/console svc_log:stat-aggregate
```

Example for crontab:
```
17 * * * * (cd <your_path>; bin/console svc_log:stat-aggregate) >/dev/null
```
