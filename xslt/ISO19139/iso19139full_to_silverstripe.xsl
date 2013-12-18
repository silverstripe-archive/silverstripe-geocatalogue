<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
	xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:gmd="http://www.isotc211.org/2005/gmd" 
	xmlns:gml="http://www.opengis.net/gml" 
	xmlns:gts="http://www.isotc211.org/2005/gts" 
	xmlns:gco="http://www.isotc211.org/2005/gco" 
	xmlns:geonet="http://www.fao.org/geonetwork">

<xsl:template match="/">
	<xsl:for-each select="csw:GetRecordByIdResponse">
		<xsl:call-template name="MD_Metadata"> 
			<xsl:with-param name="Metadata" select="gmd:MD_Metadata"/>
		</xsl:call-template>
	</xsl:for-each>
</xsl:template>

<xsl:template match="gmd:citation">
</xsl:template>

<xsl:include href="iso19139_MDMetadata.xsl"/>

</xsl:stylesheet>


