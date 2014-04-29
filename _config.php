<?php

/* 
Requires XSLT-Processor, i.e.

>> sudo apt-get install php5-xsl
*/

// Add model admin for the metadata entries.
CMSMenu::add_menu_item("metadata", 'Metadata', 'admin/metadata', "MetadataAdmin" );
