ProcessWire ProFields: Fieldset Group
=====================================

Fieldtype module for ProcessWire
Part of the ProcessWire ProFields set of modules
Copyright 2017 by Ryan Cramer
https://processwire.com/api/modules/profields/

COMMERCIAL MODULE: DO NOT DISTRIBUTE

Getting Started
---------------

1. Make sure you are running ProcessWire 3.0.74 or newer. 

2. Copy, upload or FTP the contents of the included ZIP file to this directory:
   /site/modules/FieldtypeFieldsetGroup/
   
3. In your ProcessWire admin, go to Modules > Refresh. 

4. Now go to Modules > Site > Fieldtype > Fieldset Group, and click the 
   “Install” button. The module will now be installed. 
   
5. Create a new field (Setup > Fields > Add New), and choose “Fieldset (Group)”   
   as the type. Save. 
   
6. Click the “Details” tab, and start adding fields to the fieldset. Save. 

7. After adding fields to your fieldset (and saving) you can optionally 
   configure contextual details by clicking the field name in the list. 
   
8. Add your new Fieldset Group field to one or more templates where you want
   to use it. You can do this from the “Actions” tab while editing your 
   Fieldset Group field, or you can add it toe templates individually by 
   editing each template where you want to add it. 


About Fieldset Group
--------------------

This module extends the existing Fieldset type in ProcessWire, but with one big
difference: the group of fields that are contained within it are managed by it. 
So when you create a new Fieldset Group field, it then asks you to start adding 
fields to it. Then you can add that Fieldset Group field to any templates, and 
they inherit all the fields in the group. 

You can create a single field and add it to a dozen templates in one shot. No 
more need to add fields one-by-one, then drag each into a fieldset, and then 
repeat for each template. Meaning, this Fieldset Group can save you a lot of 
time. It literally adds the fieldset, and all the fields within the fieldset to 
each template using the fieldset.

Mirroring changes:
The usefulness of Fieldset Group goes beyond the initial creation and 
configuration of fields. Any changes you later make to that Fieldset Group 
(like adding new fields or changing the order of them) automatically get 
applied to to all appearances of the group, across all the templates using it.

Contexts:
Fieldset Group also supports template-field context. Meaning, you can adjust 
properties of each field (like width, visibility, label, etc.) within the 
context of the group. Those context changes automatically migrate to all the 
templates using the Fieldset Group. If a template has also applied context 
changes to a field within the group, then the two sets of context changes are 
merged with one another, so that both apply.

Already familiar:
There's nothing new to know about Fieldset Group fields on the API side. When 
you add a Fieldset Group to a template, the fields within it become fields of 
the page. The Fieldset Group does NOT add another layer that you must access 
them through on the API side. Meaning, if you added a Fieldset Group field to 
a template, and it contained a field named "author", then it would be accessed 
as $page->author. So its behavior is identical to that of Fieldsets that you 
already use in ProcessWire, and the fields within it are literal fields on 
the page.

Note about API:
There is no extra API for Fieldset Group. From the API side it is functionally
identical to regular Fieldsets. The primary benefit of Fieldset Group is on
the interactive/UI admin as a time saving tool. 


Support and Upgrades
--------------------

For support and upgrades, please visit the ProcessWire ProFields board at: 
https://processwire.com/talk/forum/28-profields-support/
(requires login and active ProFields subscription)


Terms and Conditions
--------------------

You may not copy or distribute ProFields, except on site(s) you have registered it
for with Ryan Cramer Design, LLC. It is okay to make copies for use on staging
or development servers specific to the site you registered for.

This service/software includes 1-year of support through the ProcessWire ProFields
Support forum and/or email. Please notify us of your forum name so that we can
upgrade your access to the members-only ProFields Support forum.

In no event shall Ryan Cramer, Ryan Cramer Design, LLC or ProcessWire be liable for 
any special, indirect, consequential, exemplary, or incidental damages whatsoever, 
including, without limitation, damage for loss of business profits, business interruption,
loss of business information, loss of goodwill, or other pecuniary loss whether
based in contract, tort, negligence, strict liability, or otherwise, arising out of
the use or inability to use ProcessWire ProFields, even if Ryan Cramer Design, LLC /
ProcessWire has been advised of the possibility of such damages.

ProFields are provided "as-is" without warranty of any kind, either expressed or
implied, including, but not limited to, the implied warranties of merchantability and
fitness for a particular purpose. The entire risk as to the quality and performance
of the program is with you. Should the program prove defective, you assume the cost
of all necessary servicing, repair or correction. If within 7 days of purchase, you
may request a full refund. Should you run into any trouble with ProFields, please
email for support or visit the ProFields Support forum.

Thanks for using ProcessWire ProFields!


