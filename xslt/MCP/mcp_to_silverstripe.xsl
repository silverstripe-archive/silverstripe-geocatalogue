<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
	xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:gmd="http://www.isotc211.org/2005/gmd" 
	xmlns:gml="http://www.opengis.net/gml" 
	xmlns:gts="http://www.isotc211.org/2005/gts" 
	xmlns:gco="http://www.isotc211.org/2005/gco" 
	xmlns:geonet="http://www.fao.org/geonetwork"
	xmlns:mcp="http://bluenet3.antcrc.utas.edu.au/mcp">
	
<xsl:template match="/">
	<xsl:for-each select="csw:GetRecordsResponse">
		<xsl:for-each select="csw:SearchResults">
			$numberOfRecordsMatched = trim('<xsl:value-of select="@numberOfRecordsMatched"/>');
			$numberOfRecordsReturned = trim('<xsl:value-of select="@numberOfRecordsReturned"/>');
			$nextRecord = trim('<xsl:value-of select="@nextRecord"/>');	
			<xsl:apply-templates match="mcp:MD_Metadata"/> 
			<xsl:apply-templates select="MD_Metadata"/> 
		</xsl:for-each>
	</xsl:for-each>
</xsl:template>

<xsl:template match="mcp:MD_Metadata">
	$mdItem = array();
	$mdItem['fileIdentifier'] = trim('<xsl:value-of select="gmd:fileIdentifier"/>');
	$mdItem['metadataStandardName'] = trim('<xsl:value-of select="gmd:metadataStandardName"/>');
	$mdItem['metadataStandardVersion'] = trim('<xsl:value-of select="gmd:metadataStandardVersion"/>');
	<xsl:for-each select="gmd:identificationInfo">
		<xsl:for-each select="mcp:MD_DataIdentification">
			$mdItem['MDTitle'] = trim('<xsl:value-of select="gmd:citation/gmd:CI_Citation/gmd:title"/>');
			$mdItem['MDAbstract'] = trim('<xsl:value-of select="gmd:abstract"/>');
			$mdItem['MDTopicCategory'] = trim('<xsl:value-of select="gmd:topicCategory"/>');
		</xsl:for-each>
	</xsl:for-each>	
	$mdArray[] = $mdItem;
</xsl:template>
</xsl:stylesheet>



