<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:csw="http://www.opengis.net/cat/csw/2.0.2">
	
	<xsl:template match="/">
		<xsl:for-each select="csw:TransactionResponse">
			<xsl:for-each select="csw:TransactionSummary">
				$totalInserted = trim('<xsl:value-of select="csw:totalInserted"/>');
			</xsl:for-each>
			<xsl:for-each select="csw:InsertResult">
				<xsl:for-each select="csw:BriefRecord">
					$uuid = trim('<xsl:value-of select="identifier"/>');
				</xsl:for-each>
			</xsl:for-each>			
		</xsl:for-each>			
	</xsl:template>
</xsl:stylesheet>
