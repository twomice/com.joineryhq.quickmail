# QuickMail
Create and send immediately-scheduled emails to an admin-configured list of 
contact groups.

## Use case
Created for situations like this:

* C-suite users frequently wish to send emails to one or a few select groups of contacts.
* These users have no need to access CiviCRM directly, and are not interested in being trained.
* This extension provides a simple interface whereby these users can select the groups to send to, enter a subject line and HTML body, and click _Send_.

![Screenshot](https://joineryhq.com/external/civicrm/com.joineryhq.quickmail/quickmail.png "Screen Shot")

## Configuration
The site administrator controls use of this feature as follows:

* Grant the "access QuickMail" permission to users who are trusted to send mail with this feature.
* Navigate to Administer > CiviMail > QuickMail Settings to define these settings:
  * Which groups of type "Mailing List" are available for selection as recipients.
  * Which Header (if any) to prepend automatically to all QuickMail mailings.
  * Which Footer (if any) to append automatically to all QuickMail mailings.

**Note:** the selected header and footer must have, somewhere in the sum of their content, the {domain.address} and {action.optOutUrl} tokens, in order to comply with CiviCRM's requirements for outbound mass mailings.

## Usage

Any user with the "access QuickMail" permission can navigate to the appropriate URL to create and send QuickMail:
* Drupal: https://example.com/civicrm/quickmail/compose?reset=1
* WordPress: https://example.com/wp-admin/admin.php?page=CiviCRM&q=civicrm/quickmail/compose&reset=1
* Joomla (back-end): https://example.com/administrator/index.php?option=com_civicrm&task=civicrm/quickmail/compose&reset=1
* Joomla (front-end): https://example.com/index.php?option=com_civicrm&task=civicrm/quickmail/compose&reset=1

## Functionality
All QuickMail emails are created as Scheduled Mailings, scheduled for immediate sending, and will be sent out on the next execution of the "Send Scheduled Mailings" scheduled job.

## Support
![screenshot](/images/joinery-logo.png)

Joinery provides services for CiviCRM including custom extension development, training, data migrations, and more. We aim to keep this extension in good working order, and will do our best to respond appropriately to issues reported on its [github issue queue](https://github.com/twomice/com.joineryhq.quickmail/issues). In addition, if you require urgent or highly customized improvements to this extension, we may suggest conducting a fee-based project under our standard commercial terms.  In any case, the place to start is the [github issue queue](https://github.com/twomice/com.joineryhq.quickmail/issues) -- let us hear what you need and we'll be glad to help however we can.

And, if you need help with any other aspect of CiviCRM -- from hosting to custom development to strategic consultation and more -- please contact us directly via https://joineryhq.com