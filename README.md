# SilverStripe-Default-Members
=======
# A module for maintaining a set of default members in your SilverStripe instance


## Maintainer Contact

Nathan J. Brauer <nathan@marketera.com>

## Requirements

* SilverStripe 3

## Setup

Add your list of default members in the following format to a `_config` yaml file.  You can do this in `mysite` for each individual SilverStripe instance.

If you are managing a large set of SilverStripe instances, you may consider creating a "config-only" SilverStripe module which you pull in through composer across every site.

```
DefaultMembers:
  admins:
    -
        Email: someone@example.com
        FirstName: Someone
        Surname: Awesome
    -
        Email: nathan@marketera.com
        FirstName: Nathan
        Surname: Brauer
    -
        Email: another@example.com
        FirstName: Amazing
        Surname: Example
```

## Composer Installation

  Then run the following in the command line:
  
  `composer require marketo/silverstripe-default-members`

## TODO

- Unit tests
- Customization options (user groups, additional fields, etc)