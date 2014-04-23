<?php

/* 
Requires XSLT-Processor, i.e.

>> sudo apt-get install php5-xsl
*/

// Change this field to anything except 'setup' do set the configuration fields 
// to read-only.
CataloguePage::set_site_status('setup');

// Add model admin for the metadata entries.
CMSMenu::add_menu_item("metadata", 'Metadata', 'admin/metadata', "MetadataAdmin" );
