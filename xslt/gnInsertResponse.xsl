<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	
<xsl:template match="response">
	$gnID = trim('<xsl:value-of select="id"/>');
</xsl:template>
</xsl:stylesheet>
