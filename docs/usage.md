Usage
=====

* adapt the default url prefix in config/routes/svc_profile.yaml and enable translation (if you like it)

```yaml
# /config/routes/svc_profile.yaml
_svc_profile:
    resource: '@SvcProfileBundle/src/Resources/config/routes.xml'
    prefix: /svc-profile/{_locale}
    requirements: {"_locale": "%app.supported_locales%"}
```

Generate a sha256-secret key (you can use https://passwordsgenerator.net/sha256-hash-generator for it) and store the key in .env (or better .env.local)
```sh
###> svc/profile-bundle ###
SVC_PROFILE_HASH_SECRET=D9E143E74FC3E5AE3ED5305043FC67030C43CCDA5060EA2FD464BB8C0CC2D65A
###< svc/profile-bundle ###
```


* integrate the change mail controller via path "svc_profile_change_mail_start"
* integrate the change password controller via path "svc_profile_change_pw_start"

* enable captcha (if installed and configured), default = false

```yaml
# /config/packages/svc_profile.yaml
svc_profile:
    # Enable captcha for change email/password forms?
    enableCaptcha: true
```
