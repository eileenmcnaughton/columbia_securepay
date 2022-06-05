# columbia_securepay

Internal Columbia communication with internal Secure Pay

## Requirements

* PHP v7.2+
* CiviCRM 5.49

## Installation (Web UI)

Learn more about installing CiviCRM extensions in the [CiviCRM Sysadmin Guide](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/).

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl columbia_securepay@https://github.com/FIXME/columbia_securepay/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/FIXME/columbia_securepay.git
cv en columbia_securepay
```

## Getting Started

- Create a user for communications. The user must have these  permissions:
- permission AuthX: Authenticate to services with API key &
- Secure Pay submit (post_securepay)
- The contact associated with the user must have an api_key (in the table database as civicrm_contact.api_key)

![img.png](img.png)

The remove site will need to know
- the url - something like https://{siteName}/civicrm/ajax/api4/Securepay/submit`
- the api key
-

## Known Issues

(* FIXME *)
