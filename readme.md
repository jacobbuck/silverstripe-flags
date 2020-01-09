# silverstripe-flags

Feature flag toggling for SilverStripe.

## Usage

Add your flags to your website or module config:

```yml
JacobBuck\Flags\Flag:
  flags:
    - Name: coolFeature
      Description: "Enable website to use cool feature."
      Enabled: true
    - Name: debugAnotherFeature
      Description: "For developers to debug another feature."
    - Name: experimentalThing
      Description: "Enables experimental thing."
```

You can then write code to be conditional based on if a flag is enabled:

```php
    use JacobBuck\Flags\Flag;
    
    ...
    
    if (Flag::isEnabled('coolFeature')) {
        // Do something cool
    }
```

```html
    <% if FlagEnabled("experimentalThing") %>
        <%-- Experimental thing template --%>
    <% end_if %>
```

Flags can be toggled in the CMS:

![screenshot](docs/images/screenshot1.png)

![screenshot](docs/images/screenshot2.png)

You can also see the history of changes to a flag:

![screenshot](docs/images/screenshot3.png)

## Requirements

- Silverstripe 3+

## Installation

The recommended way to install is through Composer:

```
composer require jacobbuck/silverstripe-flags
```
