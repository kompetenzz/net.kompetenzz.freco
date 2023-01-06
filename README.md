# net.kompetenzz.freco

This extensions provides API4 and UI to compute frequency distribution across a set of custom fields. 

Given a survey based on activities with custom fields each using an option set as results, this extension generates the occurence of each option value:

```
,-----------------------------------------.
|         Field                   | Count |
|---------------------------------+-------|
| CustomfieldLabel1: Optionlabel1 |  16   |
| CustomfieldLabel1: Optionlabel2 |  23   |
| CustomfieldLabel1: OptionlabelN |   1   |
| CustomfieldLabel2: Optionlabel6 |  16   |
| ...                             |  ...  |
| CustomfieldLabelN: Optionlabel2 |  19   |
| CustomfieldLabelN: Optionlabel7 |  13   |
| CustomfieldLabelN: Optionlabel8 |   6   |
`-----------------------------------------´
```

## Requirements

* PHP v7.3+
* CiviCRM (5.39+)

## Installation (Web UI)

Learn more about installing CiviCRM extensions in the [CiviCRM Sysadmin Guide](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/).

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

## Uaage
### UI
Navigate to /civicrm/a/#/freco/computer and set valzues as needed. The URL query params are being synced to provide a shareable link to the results. 

### API4
Entity "FreCo" is propagated with two endpoint actions both expecting the same parameters(!):   

#### compute: generate data
```
$results = \Civi\Api4\FreCo::info()
  ->setActivityTypeId(110)
  ->setActivityStatusIds(2)
  ->setCustomGroupId(70)
  ->setCustomFieldIds('516,517,518,519,520,521,522,523,524,525,526,527,528,530,531,533,534,535,536,537,538')
  ->execute();
```

#### info: get human readable metadata
```
$results = \Civi\Api4\FreCo::info()
  ->setActivityTypeId(110)
  ->setActivityStatusIds(2)
  ->setCustomGroupId(70)
  ->setCustomFieldIds('516,517,518,519,520,521,522,523,524,525,526,527,528,530,531,533,534,535,536,537,538')
  ->execute();
```
