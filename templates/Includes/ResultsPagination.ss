	<div id="PageNumbers">
		<p class="buttonLinks">
			<% if IsFirstResultPage %>
				<span class="prevLink">Previous</span>
			<% else %>
				<a href="$Link/dogetrecords/$PrevStart/$searchTerm/" class="prevLink">Previous</a>
			<% end_if %>
			<% if IsLastResultPage %>
				<span class="nextLink">Last</span>
			<% else %>
				<!-- Last page -->
			<% end_if %>	
		</p>
		<p>Page $onpage of $ofpages</p>
	</div>
