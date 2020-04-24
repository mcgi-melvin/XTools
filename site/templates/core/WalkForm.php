<?php
namespace ProcessWire;

/**
 * Class WalkForm
 * @package ProcessWire
 * The Form Builder and Manager class for WalkCMS - extends Page
 * Uses PW template form-builder (in WalkCMS)
 *
 * @property Page $onsubmitAction Page reference will be either 1315=alert, 1316=pagechange, 1316=none
 * @property string $onsubmitSuccessText Alert message text or URL relative to root for pagechange on success
 * @property string $onsubmitFailText Alert message text or URL relative to root for pagechange on success
 * @property Page $emailTemplateSelect The page that containes the Email Template used for the form (response form). Page reference.
 * @property int $sendNotifyEmail Are we sending a notification email to a company representative when this form is completed?. Checkbox.
 * @property string $contact_form_recipient Comma separated email recipients.
 * @property Page $notify_emailTemplateSelect Page reference which template is used to send to company admin if $sendNotifyEmail=1
 * @property int $sendResponseEmail Send response email to the person who filled out this form. Checkbox.
 * @property Page $responseEmailField Page ref for question that is the email input question. Page reference.
 * @property int $saveToPage Are we creating a Page of answers when this form is completed? Checkbox.
 * @property string $savePageTemplate Template name of the Page we are creating on form submission
 * @property Page $savePageParent The parent page under which a new page should be created. Page reference.
 * @property int $newpage_published Should the created page be set to status=published?
 *
 */
class WalkForm extends Page {

}