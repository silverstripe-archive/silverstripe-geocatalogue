<% if SearchRecord %>
<% else %>
	<div class="typography">
		$Content
	</div>
	<div class="form">
		$GeoNetworkSearchForm
	</div>
<% end_if %>

<div class="typography">
	<!-- Search result -->
	<% if SearchResultItems %>
		<div class='results_searchTerm'>
			<h2>
				Search result for: $searchTerm 
			</h2>
			<div>
                <% if IsFirstResultPage %>Prev<% else %><a href="$Link/dogetrecords/$PrevStart/$searchTerm/">Prev</a><% end_if %> | Page $onpage of $ofpages |
                <% if IsLastResultPage %>Last<% else %><a href="$Link/dogetrecords/$NextStart/$searchTerm/">Next</a><% end_if %>
			</div>
		</div>
	
		<div class='results'>
		<!-- Search result items -->
		<% loop SearchResultItems %>
			<% include RecordSummaryList %>
		<% end_loop %>
	<% end_if %>

	<!-- Record result -->
	<% if SearchRecord %>
		<div class='record'>
		<% loop SearchRecord %>
			<% include RecordFull %>
		<% end_loop %>
	<% end_if %>


	<!-- Messages results -->
	<% if Message %>
	<div class='$MessageType'>$Message</div>
	<% end_if %>
</div>
