# Options

## Pagination

If you enabled pagination in /config/packages/svc_video.yaml, please define the css framework you use.
See pagerfanta documentation (https://www.babdev.com/open-source/packages/pagerfantabundle/docs/3.x/views) 

Example:

```yaml
# /config/packages/babdev_pagerfanta.yaml
babdev_pagerfanta:
  default_view: twig
  default_twig_template: '@BabDevPagerfanta/twitter_bootstrap5.html.twig'
```

## Tagging

If you enabled tagging in /config/packages/svc_video.yaml, you have to install WebPack/Stimulus in your project.

Please add the tagging-CSS to your global (S)CSS file, see [Usage](usage.md#CSS).
