# silverstripe-flags

Feature flag toggling for SilverStripe.

## Usage

Add your flags to your website or module config:

```yml
Flag:
  flags:
    - Name: coolFeature
      Description: "Enable website to use cool feature."
      Enabled: true
    - Name: debugAnotherFeature
      Description: "For developers to debug another feature."
    - Name: experimentalThing
      Description: "Enables experimental thing."
```

Then you can control your flags in admin:

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
