# SuluMailingListBundle

Sulu bundle to manage mailing lists, subscribe contacts (including via Sulu Forms), and send MJML-based newsletter mails from the admin UI.

## Features
- Newsletter management with translatable content and double-opt-in templates.
- Newsletter subscriptions with confirm/unsubscribe flow and locale-aware subscriptions.
- Admin UI resources and lists for newsletters, newsletter mails, and subscriptions.
- Newsletter mails can target newsletter subscribers and optionally filter by selected contacts.
- MJML + Twig email templates with configurable wrappers/components and optional caching.
- Optional form integration via `linderp/sulu-form-save-contact-bundle` to auto-subscribe contacts.

## Requirements
- PHP 8.2+
- Sulu 2.6+
- `linderp/sulu-form-save-contact-bundle`
- `linderp/sulu-base-bundle`

## Installation
1. Install the bundle:

```console
composer require linderp/sulu-mailing-list-bundle
```

2. If you are **not** using Symfony Flex, add the bundle to `config/bundles.php`:

```php
return [
    // ...
    Linderp\SuluMailingListBundle\SuluMailingListBundle::class => ['all' => true],
];
```

3. Register routes:

`config/routes_admin.yaml`
```yaml
SuluMailingListBundle:
    resource: "@SuluMailingListBundle/Resources/config/routes_admin.yml"
```

`config/routes_website.yaml`
```yaml
SuluMailingListBundle:
    resource: "@SuluMailingListBundle/Resources/config/routes.yml"
```

4. Configure MJML and sender settings in `config/packages/sulu_mailing_list.yaml`:

```yaml
sulu_mailing_list:
  mjml:
    app_id: <api-id>
    secret_key: <secret-key>
    caching: true
    socials:
      facebook:
      facebookNoShare:
      twitter:
      twitterNoShare:
      x:
      xNoShare:
      google:
      googleNoShare:
      pinterest:
      pinterestNoShare:
      linkedin:
      linkedinNoShare:
      tumblr:
      tumblrNoShare:
      xing:
      xingNoShare:
      github:
      instagram:
      web:
      snapchat:
      youtube:
      vimeo:
      medium:
      soundcloud:
      dribbble:
  no_reply_email: <no-reply-mail>
```

5. Add the admin UI assets dependency (admin app):

`assets/admin/package.json`
```json
"dependencies": {
  "sulu-mailing-list-bundle": "file:../../vendor/linderp/sulu-mailing-list-bundle/Resources/js"
}
```

6. Import the admin UI bundle:

`assets/admin/app.js`
```javascript
import "sulu-mailing-list-bundle";
```

7. Build admin assets:

```bash
cd assets/admin
npm run build
```

## Admin UI
The bundle registers admin resources and lists for:
- `newsletters`
- `newsletters_mails`
- `newsletters_subscriptions`
- `filtered_contacts` (used by the newsletter mail contact selector)

Toolbar actions are provided for:
- Subscribe / Unsubscribe on newsletter subscription lists.
- Send on newsletter mail detail view (disabled if already sent or translations are missing).

## Website routes (unsubscribe / confirm)
The website routes are handled by `NewsletterWebsiteController`:
- `/{locale}/newsletter/unsubscribe/{newsletterId}/{token}`
- `/{locale}/newsletter/confirm/{newsletterId}/{token}`

Both routes redirect to the configured unsubscribe/confirmation pages on the newsletter entity.

## Sending newsletters
- Mails are sent only once (`sent = true` after send).
- Only confirmed and not-unsubscribed subscriptions are included.
- If contacts are selected, subscribers are filtered to those contacts.
- Duplicates are removed per contact.

## Sulu Forms integration
The bundle provides two dynamic field types:
- `newsletter` (checkbox)
- `hidden_newsletter`

When a form submission is saved via `linderp/sulu-form-save-contact-bundle`, selected newsletters are attached to the saved contact and handled by the subscription service.

## Email templates
MJML + Twig templates live in `Resources/views/mails/`:
- `base_email.mjml.twig` (base layout)
- `wrappers/` for layout sections
- `components/` for individual blocks

Templates are rendered through the MJML API and can be cached via `sulu_mailing_list.mjml.caching`.

## Extending mail editor resources
The mail editor is built from four building blocks: `resources`, `wrappers`, `contexts`, and `fields`.
When you add one, you must wire it in the correct places. The exact locations differ per type.

Where things live:
- Resources: form XML in `Resources/config/forms/*.xml` + type class in `Mail/Resource/Types/`
- Wrappers: XML in `Resources/config/mail/wrappers/*.xml` + MJML in `Resources/views/mails/wrappers/` + type class in `Mail/Wrapper/Types/`
- Contexts: XML in `Resources/config/mail/contexts/*.xml` + type class in `Mail/Context/Types/` (no MJML template)
- Fields: XML in `Resources/config/mail/types/*.xml` + MJML in `Resources/views/mails/components/<type>/<type>.mjml.twig` + type class in `Mail/Field/Types/`

Notes:
- Keep XML names/keys aligned with the PHP type name and Twig template name.
- Update translations in `Resources/translations/` if you add new labels.
- PHP types are auto-registered via `#[AutoconfigureTag(...)]` on the corresponding interface (no manual pool registration needed).

Example: add a new field type `quote`
1. XML: `Resources/config/mail/types/quote.xml`
```xml
<?xml version="1.0" ?>
<properties xmlns="http://schemas.sulu.io/template/template"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://schemas.sulu.io/template/template http://schemas.sulu.io/template/properties-1.0.xsd">
    <property name="text" type="text_line">
        <meta>
            <title>mailingListMail.props.quote.text</title>
        </meta>
    </property>
</properties>
```
2. MJML: `Resources/views/mails/components/quote/quote.mjml.twig`
```twig
<mj-text font-style="italic">{{ item.text }}</mj-text>
```
3. PHP type: `Mail/Field/Types/QuoteMailFieldType.php`
```php
final class QuoteMailFieldType implements MailFieldTypeInterface
{
    public function getConfiguration(): MailFieldTypeConfiguration
    {
        return new MailFieldTypeConfiguration(
            'quote',
            'mailingListMail.types.quote.label',
            __DIR__ . '/../../../Resources/config/mail/types/quote.xml',
            __DIR__ . '/../../../Resources/views/mails/components/quote/quote.mjml.twig'
        );
    }
}
```

## Admin icon PNG generation
The bundle includes a helper script to generate PNGs from Font Awesome solid SVGs, with optional color variants.

Script location:
`vendor/linderp/sulu-mailing-list-bundle/scripts/generate-fontawesome-pngs.js`

Default output folder:
`icons/` (in the project root, when run from the project root)

Default Font Awesome SVG folder:
`assets/admin/node_modules/@fortawesome/fontawesome-free/svgs/solid`

Additional SVG folder included:
`vendor/linderp/sulu-mailing-list-bundle/Resources/social` (e.g. social brand icons)

Examples (run from the project root):

```bash
# Use COLOR_PICKER_COLORS from .env
node vendor/linderp/sulu-mailing-list-bundle/scripts/generate-fontawesome-pngs.js

# Custom output folder
node vendor/linderp/sulu-mailing-list-bundle/scripts/generate-fontawesome-pngs.js --out /path/to/output

# Custom Font Awesome SVG folder
node vendor/linderp/sulu-mailing-list-bundle/scripts/generate-fontawesome-pngs.js --svg-dir /path/to/solid-svgs

# Custom colors (defaults to output in ./icons)
node vendor/linderp/sulu-mailing-list-bundle/scripts/generate-fontawesome-pngs.js "#F7F7F7" "#A6A6A6"

# Custom output + custom colors
node vendor/linderp/sulu-mailing-list-bundle/scripts/generate-fontawesome-pngs.js --out /path/to/output "#F7F7F7" "#A6A6A6"
```

Notes:
- The script reads solid SVGs from `assets/admin/node_modules/@fortawesome/fontawesome-free/svgs/solid` by default, but you can override with `--svg-dir`.
- It also includes SVGs from `Resources/social/` (e.g. `facebook.svg`, `facebook-noshare.svg`).
- It skips PNGs that already exist, so you can rerun it to resume.
- PNGs are named `{svg-name}.png` and `{svg-name}-{color}.png`.

## Additional Setup
### Fonts
To add a font for the admin mail editor, create a class implementing `MailFontInterface`:

```php
class MomoMailFont implements MailFontInterface
{
    public function getConfiguration(): MailFontConfiguration
    {
        return new MailFontConfiguration(
            '<url to momo.css>', // css file in /public
            'Momo Trust Sans',
            'Momo Trust Sans, sans-serif',
            true
        );
    }
}
```

Either provide the CSS yourself or use a service like Google Fonts. Example CSS:

```css
@font-face {
    font-family: 'Momo Trust Sans';
    font-style: normal;
    font-weight: 400;
    src: url(momo-latin.woff2) format('woff2');
    unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
```
