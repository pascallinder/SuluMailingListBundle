# SuluMailingListBundle

**Sulu bundle that enabled to save contacts within mailing list, 
craft and send mails to those registered for a mailing list**

## Installation

This bundle requires PHP 8.2 and Sulu 2.6 and the following packages:
- linderp/sulu-form-save-contact-bundle 
- linderp/sulu-base-bundle

1. Open a command console, enter your project directory and run:

```console
composer require linderp/sulu-mailing-list-bundle
```

If you're **not** using Symfony Flex, you'll also need to add the bundle in your `config/bundles.php` file:

```php
return [
    //...
    Linderp\SuluMailingListBundle\SuluMailingListBundle::class => ['all' => true],
];
```

2. Register the new routes by adding the following to your `routes_admin.yaml`:

```yaml
SuluMailingListBundle:
    resource: "@SuluMailingListBundle/Resources/config/routes_admin.yml"
```

3. If you don't have the mjml api key already, get your key [here](https://mjml.io/api).
4. Add the file `config/packages/sulu_mailing_list.yaml` with the following configuration and replace #your key here with your actual key:
```yaml
sulu_mailing_list:
  mjml:
    app_id: <api-id>
    secret_key: <secret-key>
    caching: true
  no_reply_email: <no-reply-mail>
``` 
For development purposes you can deactivate caching. By default, it is enabled.
5. Reference the frontend code by adding the following to your `assets/admin/package.json`:

```json
"dependencies": {
    "sulu-index-now-bundle": "file:../../vendor/linderp/sulu-mailing-list-bundle/Resources/js"
}
```

5. Import the frontend code by adding the following to your `assets/admin/app.js`:

```javascript
import "sulu-mailing-list-bundle";
```

6. Build the admin UI:

```bash
cd assets/admin
npm run build
```
## Additional Setup
### Fonts
To add a new Font for the admin to use add a class implementing `MailFontInterface`, eg:
```php
class MomoMailFont implements MailFontInterface
{

    public function getConfiguration(): MailFontConfiguration
    {
        return new MailFontConfiguration(
            '<url to momo.css>', //css file place it somewhere in your /public folder
            'Momo Trust Sans',  // display name
            'Momo Trust Sans, sans-serif', //font-family that is applied within mjml files
            true
        );
    }
}
```
Either provide the css yourself or use a service like google fonts.
The css file could look as follows:
```css
@font-face {
    font-family: 'Momo Trust Sans';
    font-style: normal;
    font-weight: 400;
    src: url(momo-latin.woff2) format('woff2'); 
    unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}

```