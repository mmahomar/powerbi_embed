This project provides a field (type, widget, and formatter) and a global configuration page that enables Drupal to render PowerBI Embed reports.

It's quite simple to use:

1. Enable the module.
2. Go to the PowerBI Embed settings page and configure: Azure Client Application ID, PowerBI Workspace ID, Client ID, Client Secret, Tenant ID, and Scope.
3. Add a PowerBI Embed report field to a content type.
4. Create a node whose type includes the PowerBI Embed report field. Fill in the PowerBI report ID on the field. Save the node.

You should see your report rendered on node view.

A Drupal custom block type may be created containing a PowerBI Embed field, that can be placed using Page Manager, Layout Builder or the traditional Drupal blocks system.
For more information about this repository, visit the source project page at https://www.drupal.org/project/powerbi_embed
