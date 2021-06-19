# Usage

## Entities
Create tables (run `bin/console doctrine:schema:update --force`) or create a migration

## CSS
- include the css file (assets/styles/layout/_svc_video.scss) in your global css

```scss
// /assets/styles/app.sccs
...
@import './layout/_svc_video';
...
```

## Routes
- adapt the default url prefix in config/routes/svc_profile.yaml and enable translation (if you like it)

```yaml
# /config/routes/_svc_video.yaml
_svc_video:
    resource: '@SvcPVideoBundle/src/Resources/config/routes.xml'
    prefix: /_svc_video/{_locale}
    requirements: {"_locale": "%app.supported_locales%"}
```

## Enable/disable feature
```yaml
# /config/packages/_svc_video.yaml
svc_video:
    # Enable likes for videos?
    enableLikes:          false

    # Enable short names for videos (for short URLs)?
    enableShortNames:     false

    # Enable videos groups?
    enableGroups:         false

    # Enable private viceos?
    enablePrivate:        true

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
- integrate the video controller via path "svc_video_group_index"

