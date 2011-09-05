<div  class='result_item'>

	<div class="title">
		$MDTitle ($metadataStandardName)
	</div>

	<div class="id">
		<div class="id_header header">ID:</div>
		<div class="id_item item">
			<a href='$Top.URLSegment/dogetrecordbyid/$fileIdentifier'>$fileIdentifier</a>
		</div>
	</div>
	
	<div class="abstract">
		<div class="abstract_header header">Abstract:</div>
		<div class="abstract_item item">
			<% if MDAbstract %>
				$MDAbstract
			<% else %>
				(not available)
			<% end_if %>
			</div>
	</div>		
</div>
